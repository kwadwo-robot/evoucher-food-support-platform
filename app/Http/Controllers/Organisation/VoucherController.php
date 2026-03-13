<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Mail\VoucherIssuedConfirmationMail;
use App\Mail\VoucherIssuedMail;
use App\Models\Voucher;
use App\Models\User;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class VoucherController extends Controller
{
    /**
     * Show the form for creating a new voucher
     */
    public function create()
    {
        $user = Auth::user();
        $profile = $user->organisationProfile;
        $walletBalance = $profile ? (float)$profile->wallet_balance : 0.0;
        
        // Load eligible recipients based on issuer role
        $recipients = $this->getEligibleRecipients($user);

        return view('organisation.vouchers.create', compact('walletBalance', 'profile', 'recipients'));
    }
    
    /**
     * Get eligible recipients based on issuer role
     */
    private function getEligibleRecipients($issuer)
    {
        $issuerRole = $issuer->role;
        
        if ($issuerRole === 'vcfse') {
            // VCFSE can issue to Individuals and School/Care organizations
            $individuals = User::where('role', 'recipient')
                ->where('is_active', 1)
                ->select('id', 'email', 'name')
                ->orderBy('name')
                ->get();
            
            $schools = User::where('role', 'school_care')
                ->where('is_active', 1)
                ->select('id', 'email', 'name')
                ->orderBy('name')
                ->get();
            
            return [
                'individuals' => $individuals,
                'schools' => $schools
            ];
        } elseif ($issuerRole === 'school_care') {
            // School/Care can issue to Individuals and VCFSE organizations
            $individuals = User::where('role', 'recipient')
                ->where('is_active', 1)
                ->select('id', 'email', 'name')
                ->orderBy('name')
                ->get();
            
            $vcfse = User::where('role', 'vcfse')
                ->where('is_active', 1)
                ->select('id', 'email', 'name')
                ->orderBy('name')
                ->get();
            
            return [
                'individuals' => $individuals,
                'vcfse' => $vcfse
            ];
        }
        
        return [];
    }

    /**
     * Store a newly created voucher in storage
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $profile = $user->organisationProfile;
        $walletBalance = $profile ? (float)$profile->wallet_balance : 0.0;

        // Validate input
        $validated = $request->validate([
            'recipient_id' => 'required|integer|exists:users,id',
            'value' => 'required|numeric|min:0.01|max:' . $walletBalance,
            'expiry_days' => 'required|integer|min:1|max:365',
            'notes' => 'nullable|string|max:500',
        ], [
            'recipient_id.required' => 'Recipient is required',
            'recipient_id.exists' => 'This recipient is not registered in the system',
            'value.required' => 'Voucher value is required',
            'value.numeric' => 'Voucher value must be a number',
            'value.min' => 'Voucher value must be at least £0.01',
            'value.max' => 'Voucher value cannot exceed your wallet balance (£' . number_format($walletBalance, 2) . ')',
            'expiry_days.required' => 'Expiry period is required',
            'expiry_days.integer' => 'Expiry period must be a whole number',
            'expiry_days.min' => 'Expiry period must be at least 1 day',
            'expiry_days.max' => 'Expiry period cannot exceed 365 days',
        ]);

        // Get recipient
        $recipient = User::find($validated['recipient_id']);
        
        // Verify recipient is eligible
        if (!$this->isEligibleRecipient($user, $recipient)) {
            return redirect()->back()
                ->withErrors(['recipient_id' => 'This user is not an eligible recipient'])
                ->withInput();
        }

        // Check wallet balance
        if ($walletBalance < $validated['value']) {
            return redirect()->back()
                ->withErrors(['value' => 'Insufficient wallet balance'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create voucher
            $voucher = Voucher::create([
                'code' => Voucher::generateCode(),
                'recipient_user_id' => $recipient->id,
                'issued_by' => $user->id,
                'value' => $validated['value'],
                'remaining_value' => $validated['value'],
                'status' => 'active',
                'expiry_date' => now()->addDays($validated['expiry_days']),
                'notes' => $validated['notes'] ?? null,
            ]);

            // Deduct from wallet balance
            $newBalance = $walletBalance - $validated['value'];
            $profile->update(['wallet_balance' => $newBalance]);

            // Send email + in-app notification to recipient
            try {
                Mail::to($recipient->email)->send(new VoucherIssuedMail($voucher));
            } catch (\Exception $mailError) {
                \Log::warning('Failed to send voucher email: ' . $mailError->getMessage());
            }
            try {
                $recipient->notify(new \App\Notifications\VoucherIssuedNotification($voucher));
            } catch (\Exception $notificationError) {
                \Log::warning('Failed to send voucher notification: ' . $notificationError->getMessage());
            }

            // Send confirmation email to the issuing organisation
            try {
                Mail::to($user->email)->send(new VoucherIssuedConfirmationMail($voucher));
            } catch (\Exception $confirmError) {
                \Log::warning('Failed to send issuer confirmation email: ' . $confirmError->getMessage());
            }

            DB::commit();
            
            // Log voucher issuance
            SystemLog::create([
                'user_id' => $user->id,
                'action' => 'issue',
                'entity_type' => 'Voucher',
                'entity_id' => $voucher->id,
                'description' => 'Issued voucher #' . $voucher->code . ' to ' . $recipient->name . ' for ' . number_format($validated['value'], 2),
                'changes' => [
                    'recipient_id' => $recipient->id,
                    'value' => $validated['value'],
                    'wallet_balance_after' => $newBalance,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return redirect()->route($user->role === 'vcfse' ? 'vcfse.dashboard' : 'school.dashboard')
                ->with('success', 'Voucher issued successfully! A confirmation has been sent to your email and the recipient has been notified.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to issue voucher: ' . $e->getMessage()])
                ->withInput();
        }
    }
    
    /**
     * Check if a user is an eligible recipient for the issuer
     */
    private function isEligibleRecipient($issuer, $recipient)
    {
        $issuerRole = $issuer->role;
        $recipientRole = $recipient->role;
        
        if ($issuerRole === 'vcfse') {
            // VCFSE can issue to Individuals and School/Care
            return in_array($recipientRole, ['recipient', 'school_care']);
        } elseif ($issuerRole === 'school_care') {
            // School/Care can issue to Individuals and VCFSE
            return in_array($recipientRole, ['recipient', 'vcfse']);
        }
        
        return false;
    }

    /**
     * Show list of issued vouchers
     */
    public function index()
    {
        $user = Auth::user();
        
        // Calculate statistics
        $totalIssued = Voucher::where('issued_by', $user->id)->count();
        $activeCount = Voucher::where('issued_by', $user->id)->where('status', 'active')->count();
        $pendingRedemption = Voucher::where('issued_by', $user->id)->where('status', 'active')
            ->where('remaining_value', '>', 0)
            ->count();
        $cancelledCount = Voucher::where('issued_by', $user->id)->where('status', 'cancelled')->count();
        
        // Get paginated vouchers
        $vouchers = Voucher::where('issued_by', $user->id)
            ->with('recipient')
            ->latest()
            ->paginate(15);

        return view('organisation.vouchers.index', compact(
            'vouchers', 
            'totalIssued', 
            'activeCount', 
            'pendingRedemption', 
            'cancelledCount'
        ));
    }

    /**
     * Show a specific voucher
     */
    public function show(Voucher $voucher)
    {
        $user = Auth::user();

        // Check if user issued this voucher
        if ($voucher->issued_by !== $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('organisation.vouchers.show', compact('voucher'));
    }

    /**
     * Cancel a voucher
     */
    public function cancel(Voucher $voucher)
    {
        $user = Auth::user();

        // Check if user issued this voucher
        if ($voucher->issued_by !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Check if voucher can be cancelled
        if ($voucher->status !== 'active') {
            return redirect()->back()
                ->withErrors(['error' => 'Only active vouchers can be cancelled']);
        }

        try {
            DB::beginTransaction();

            // Refund to wallet
            $profile = $user->organisationProfile;
            $profile->update([
                'wallet_balance' => (float)$profile->wallet_balance + (float)$voucher->remaining_value
            ]);

            // Cancel voucher
            $voucher->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Voucher cancelled and refunded to wallet');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to cancel voucher: ' . $e->getMessage()]);
        }
    }
}
