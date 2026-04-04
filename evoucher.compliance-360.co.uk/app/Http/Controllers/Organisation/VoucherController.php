<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\User;
use App\Models\SystemLog;
use App\Mail\VoucherIssuedMail;
use App\Mail\VoucherIssuedConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    /**
     * Display a listing of vouchers.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get vouchers issued by this user
        $vouchers = Voucher::where('issued_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Calculate statistics
        $allVouchers = Voucher::where('issued_by', $user->id)->get();
        $totalIssued = $allVouchers->count();
        $activeCount = $allVouchers->where('status', 'active')->count();
        $pendingRedemption = $allVouchers->where('status', 'pending')->count();
        $redeemedCount = $allVouchers->where('status', 'redeemed')->count();
        $expiredCount = $allVouchers->where('status', 'expired')->count();
        $cancelledCount = $allVouchers->where('status', 'cancelled')->count();
        $usedCount = $allVouchers->where('status', 'used')->count();
        
        return view('organisation.vouchers.index', [
            'vouchers' => $vouchers,
            'totalIssued' => $totalIssued,
            'activeCount' => $activeCount,
            'pendingRedemption' => $pendingRedemption,
            'redeemedCount' => $redeemedCount,
            'expiredCount' => $expiredCount,
            'cancelledCount' => $cancelledCount,
            'usedCount' => $usedCount,
        ]);
    }

    /**
     * Show the form for creating a new voucher.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get eligible recipients based on user role
        $allRecipients = $this->getEligibleRecipients($user);
        
        // Format recipients by type
        $recipients = [
            'individuals' => $allRecipients->where('role', 'recipient')->values(),
            'schools' => $allRecipients->where('role', 'school_care')->values(),
            'vcfse' => $allRecipients->where('role', 'vcfse')->values(),
        ];
        
        // Get wallet balance for the user's organization
        $walletBalance = 0;
        if ($user->role === 'vcfse') {
            $profile = $user->organisationProfile;
            $walletBalance = $profile ? $profile->wallet_balance : 0;
        } elseif ($user->role === 'school_care') {
            $profile = $user->organisationProfile;
            $walletBalance = $profile ? $profile->wallet_balance : 0;
        }
        
        return view('organisation.vouchers.create', [
            'recipients' => $recipients,
            'userRole' => $user->role,
            'walletBalance' => $walletBalance,
        ]);
    }

    /**
     * Store a newly created voucher in storage.
     */
    public function store(Request $request)
    {
        // Determine which method to use based on request
        if ($request->has('new_recipient_name')) {
            return $this->storeManualVoucher($request);
        }
        
        return $this->storeVoucherForExistingRecipient($request);
    }

    /**
     * Store voucher for existing recipient
     */
    private function storeVoucherForExistingRecipient(Request $request)
    {
        $user = Auth::user();
        $profile = $user->organisationProfile;
        $walletBalance = $profile ? (float)$profile->wallet_balance : 0.0;

        // Validate input
        $validated = $request->validate([
            'recipient_id' => 'required|integer|exists:users,id',
            'value' => 'required|numeric|min:0.01',
            'quantity' => 'nullable|integer|min:1|max:100',
            'expiry_days' => 'required|integer|min:1|max:365',
            'notes' => 'nullable|string|max:500',
        ], [
            'recipient_id.required' => 'Recipient is required',
            'recipient_id.exists' => 'This recipient is not registered in the system',
            'value.required' => 'Voucher value is required',
            'value.numeric' => 'Voucher value must be a number',
            'value.min' => 'Voucher value must be at least £0.01',
            'quantity.integer' => 'Number of vouchers must be a whole number',
            'quantity.min' => 'Number of vouchers must be at least 1',
            'quantity.max' => 'Number of vouchers cannot exceed 100',
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

        // Get quantity (default to 1 if not provided)
        $quantity = $validated['quantity'] ?? 1;
        $totalAmount = $validated['value'];
        // Divide the total amount by quantity to get per-voucher value
        $valuePerVoucher = round($totalAmount / $quantity, 2);
        
        // Check wallet balance
        if ($walletBalance < $totalAmount) {
            return redirect()->back()
                ->withErrors(['value' => 'Insufficient wallet balance. You need £' . number_format($totalAmount, 2) . ' to split into ' . $quantity . ' voucher(s) of £' . number_format($valuePerVoucher, 2) . ' each.'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create multiple vouchers based on quantity
            $createdVouchers = [];
            for ($i = 0; $i < $quantity; $i++) {
                $voucher = Voucher::create([
                    'code' => Voucher::generateCode(),
                    'recipient_user_id' => $recipient->id,
                    'issued_by' => $user->id,
                    'value' => $valuePerVoucher,
                    'remaining_value' => $valuePerVoucher,
                    'status' => 'active',
                    'expiry_date' => now()->addDays($validated['expiry_days']),
                    'notes' => $validated['notes'] ?? null,
                ]);
                $createdVouchers[] = $voucher;
            }

            // Deduct from wallet balance (total amount for all vouchers)
            $newBalance = $walletBalance - $totalAmount;
            $profile->update(['wallet_balance' => $newBalance]);

            // Send email + in-app notification to recipient
            try {
                // Send email for first voucher (or summary for multiple)
                Mail::to($recipient->email)->send(new VoucherIssuedMail($createdVouchers[0]));
            } catch (\Exception $mailError) {
                \Log::warning('Failed to send voucher email: ' . $mailError->getMessage());
            }
            try {
                // Send notification for first voucher
                $recipient->notify(new \App\Notifications\VoucherIssuedNotification($createdVouchers[0]));
            } catch (\Exception $notificationError) {
                \Log::warning('Failed to send voucher notification: ' . $notificationError->getMessage());
            }

            // Send confirmation email to the issuing organisation
            try {
                Mail::to($user->email)->send(new VoucherIssuedConfirmationMail($createdVouchers[0]));
            } catch (\Exception $confirmError) {
                \Log::warning('Failed to send issuer confirmation email: ' . $confirmError->getMessage());
            }

            DB::commit();
            
            // Log voucher issuance
            $voucherCodes = implode(', ', array_map(function($v) { return '#' . $v->code; }, $createdVouchers));
            SystemLog::create([
                'user_id' => $user->id,
                'action' => 'issue',
                'entity_type' => 'Voucher',
                'entity_id' => $createdVouchers[0]->id,
                'description' => 'Issued ' . $quantity . ' voucher(s) ' . $voucherCodes . ' to ' . $recipient->name . ' for £' . number_format($valuePerVoucher, 2) . ' each (Total: £' . number_format($totalAmount, 2) . ')',
                'changes' => [
                    'recipient_id' => $recipient->id,
                    'quantity' => $quantity,
                    'value_per_voucher' => $valuePerVoucher,
                    'total_amount' => $totalAmount,
                    'wallet_balance_after' => $newBalance,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $successMessage = $quantity === 1 
                ? 'Voucher issued successfully! A confirmation has been sent to your email and the recipient has been notified.'
                : $quantity . ' vouchers issued successfully! A confirmation has been sent to your email and the recipient has been notified.';
            
            return redirect()->route($user->role === 'vcfse' ? 'vcfse.dashboard' : 'school.dashboard')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to issue voucher: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Store manual voucher for new recipient
     */
    private function storeManualVoucher(Request $request)
    {
        $user = Auth::user();
        $profile = $user->organisationProfile;
        $walletBalance = $profile ? (float)$profile->wallet_balance : 0.0;

        // Validate input
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'postcode' => 'required|string|max:10',
            'value' => 'required|numeric|min:0.01',
            'quantity' => 'nullable|integer|min:1|max:100',
            'expiry_days' => 'required|integer|min:1|max:365',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check wallet balance
        $quantity = $validated['quantity'] ?? 1;
        $totalAmount = $validated['value'];
        // Divide the total amount by quantity to get per-voucher value
        $valuePerVoucher = round($totalAmount / $quantity, 2);
        
        if ($walletBalance < $totalAmount) {
            return redirect()->back()
                ->withErrors(['value' => 'Insufficient wallet balance. You need £' . number_format($totalAmount, 2) . ' to split into ' . $quantity . ' voucher(s) of £' . number_format($valuePerVoucher, 2) . ' each.'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create new recipient user
            $recipient = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => bcrypt(Str::random(16)),
                'role' => 'recipient',
                'status' => 'active',
            ]);

            // Create multiple vouchers based on quantity
            $createdVouchers = [];
            for ($i = 0; $i < $quantity; $i++) {
                $voucher = Voucher::create([
                    'code' => Voucher::generateCode(),
                    'recipient_user_id' => $recipient->id,
                    'issued_by' => $user->id,
                    'value' => $valuePerVoucher,
                    'remaining_value' => $valuePerVoucher,
                    'status' => 'active',
                    'expiry_date' => now()->addDays($validated['expiry_days']),
                    'notes' => $validated['notes'] ?? null,
                ]);
                $createdVouchers[] = $voucher;
            }

            // Deduct from wallet balance
            $newBalance = $walletBalance - $totalAmount;
            $profile->update(['wallet_balance' => $newBalance]);

            // Send email to recipient with credentials
            try {
                Mail::to($recipient->email)->send(new VoucherIssuedMail($createdVouchers[0]));
            } catch (\Exception $mailError) {
                \Log::warning('Failed to send voucher email: ' . $mailError->getMessage());
            }

            // Send confirmation email to issuing organisation
            try {
                Mail::to($user->email)->send(new VoucherIssuedConfirmationMail($createdVouchers[0]));
            } catch (\Exception $confirmError) {
                \Log::warning('Failed to send issuer confirmation email: ' . $confirmError->getMessage());
            }

            DB::commit();

            // Log voucher issuance
            $voucherCodes = implode(', ', array_map(function($v) { return '#' . $v->code; }, $createdVouchers));
            SystemLog::create([
                'user_id' => $user->id,
                'action' => 'issue',
                'entity_type' => 'Voucher',
                'entity_id' => $createdVouchers[0]->id,
                'description' => 'Issued ' . $quantity . ' voucher(s) ' . $voucherCodes . ' to new recipient ' . $recipient->name . ' for £' . number_format($valuePerVoucher, 2) . ' each (Total: £' . number_format($totalAmount, 2) . ')',
                'changes' => [
                    'new_recipient_id' => $recipient->id,
                    'quantity' => $quantity,
                    'value_per_voucher' => $valuePerVoucher,
                    'total_amount' => $totalAmount,
                    'wallet_balance_after' => $newBalance,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $successMessage = $quantity === 1 
                ? 'Voucher issued successfully to new recipient! A confirmation has been sent to your email.'
                : $quantity . ' vouchers issued successfully to new recipient! A confirmation has been sent to your email.';

            return redirect()->route($user->role === 'vcfse' ? 'vcfse.dashboard' : 'school.dashboard')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to issue voucher: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Get eligible recipients based on user role
     */
    private function getEligibleRecipients($user)
    {
        if ($user->role === 'vcfse') {
            // VCFSE can issue to recipients and schools/care
            $recipients = User::where('role', 'recipient')
                ->where('is_approved', true)
                ->orderBy('name')
                ->get();
            
            $schools = User::where('role', 'school_care')
                ->where('is_approved', true)
                ->orderBy('name')
                ->get();
            
            return $recipients->concat($schools);
        } elseif ($user->role === 'school' || $user->role === 'school_care') {
            // Schools/Care can issue to recipients and VCFSE groups
            $recipients = User::where('role', 'recipient')
                ->where('is_approved', true)
                ->orderBy('name')
                ->get();
            
            $vcfse = User::where('role', 'vcfse')
                ->where('is_approved', true)
                ->orderBy('name')
                ->get();
            
            // Combine both collections
            return $recipients->concat($vcfse);
        }
        
        return collect();
    }

    /**
     * Check if recipient is eligible
     */
    private function isEligibleRecipient($user, $recipient)
    {
        if ($recipient->role !== 'recipient' || !$recipient->is_approved) {
            return false;
        }

        // Add additional eligibility checks if needed
        return true;
    }

    /**
     * Display the specified voucher.
     */
    public function show($id)
    {
        $voucher = Voucher::findOrFail($id);
        $currentUserId = Auth::id();
        
        // Log for debugging
        \Log::info('Viewing voucher', [
            'voucher_id' => $id,
            'current_user_id' => $currentUserId,
            'issued_by' => $voucher->issued_by,
            'recipient_user_id' => $voucher->recipient_user_id,
            'user_role' => Auth::user()->role
        ]);
        
        // Check authorization - allow if user issued it, is recipient, or is admin
        if ((int)$currentUserId !== (int)$voucher->issued_by && 
            (int)$currentUserId !== (int)$voucher->recipient_user_id && 
            Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        return view('organisation.vouchers.show', ['voucher' => $voucher]);
    }

    /**
     * Show the form for editing the specified voucher.
     */
    public function edit($id)
    {
        $voucher = Voucher::findOrFail($id);
        
        // Check authorization
        if (Auth::id() !== $voucher->issued_by) {
            abort(403, 'Unauthorized');
        }

        return view('organisation.vouchers.edit', ['voucher' => $voucher]);
    }

    /**
     * Update the specified voucher in storage.
     */
    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);
        
        // Check authorization
        if (Auth::id() !== $voucher->issued_by) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $voucher->update($validated);

        return redirect()->route('vouchers.show', $voucher->id)
            ->with('success', 'Voucher updated successfully!');
    }

    /**
     * Remove the specified voucher from storage.
     */
    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        
        // Check authorization
        if (Auth::id() !== $voucher->issued_by) {
            abort(403, 'Unauthorized');
        }

        // Only allow deletion if not yet redeemed
        if ($voucher->status === 'redeemed') {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot delete a redeemed voucher']);
        }

        $voucher->delete();

        return redirect()->route('vcfse.dashboard')
            ->with('success', 'Voucher deleted successfully!');
    }
}
