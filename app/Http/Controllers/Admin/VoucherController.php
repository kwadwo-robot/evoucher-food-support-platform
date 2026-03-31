<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\VoucherIssuedConfirmationMail;
use App\Mail\VoucherIssuedMail;
use App\Models\User;
use App\Models\Voucher;
use App\Models\SystemLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::with(['recipient', 'issuedBy']);
        if ($request->status) $query->where('status', $request->status);
        if ($request->search) $query->where('code', 'like', '%' . $request->search . '%');
        $vouchers = $query->latest()->paginate(20);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        // Fetch all approved recipients (individual, school_care, and vcfse)
        $recipients = User::whereIn('role', ['recipient', 'school_care', 'vcfse'])
                          ->where('is_approved', true)
                          ->orderBy('name')
                          ->get();
        return view('admin.vouchers.create', compact('recipients'));
    }

    public function store(Request $request)
    {
        // Check if this is a manual voucher creation
        if ($request->input('issue_type') === 'manual') {
            return $this->storeManualVoucher($request);
        }

        // Otherwise, it's for a registered recipient
        return $this->storeRegisteredRecipientVoucher($request);
    }

    private function storeRegisteredRecipientVoucher(Request $request)
    {
        $request->validate([
            'recipient_id'  => 'required|exists:users,id',
            'value'         => 'required|numeric|min:0.01|max:500',
            'quantity'      => 'nullable|integer|min:1|max:100',
            'expiry_date'   => 'nullable|date|after:today',
            'notes'         => 'nullable|string|max:500',
        ]);

        // Get quantity (default to 1 if not provided)
        $quantity = $request->quantity ?? 1;
        $totalAmount = $request->value;
        // Divide the total amount by quantity to get per-voucher value
        $valuePerVoucher = round($totalAmount / $quantity, 2);

        try {
            DB::beginTransaction();

            // Create multiple vouchers based on quantity
            $createdVouchers = [];
            for ($i = 0; $i < $quantity; $i++) {
                $voucher = Voucher::create([
                    'code'              => Voucher::generateCode(),
                    'recipient_user_id' => $request->recipient_id,
                    'issued_by'         => Auth::id(),
                    'value'             => $valuePerVoucher,
                    'remaining_value'   => $valuePerVoucher,
                    'status'            => 'active',
                    'expiry_date'       => $request->expiry_date ?? now()->addDays(30)->format('Y-m-d'),
                    'notes'             => $request->notes,
                ]);
                $createdVouchers[] = $voucher;
            }

            // In-app notification and email notification for each voucher
            foreach ($createdVouchers as $voucher) {
                // In-app notification
                try {
                    NotificationService::notifyRecipientNewVoucher($voucher);
                } catch (\Exception $e) {
                    \Log::warning('Voucher in-app notification failed: ' . $e->getMessage());
                }

                // Email notification to recipient
                try {
                    $recipient = $voucher->recipient;
                    if ($recipient && $recipient->email) {
                        Mail::to($recipient->email)->send(new VoucherIssuedMail($voucher));
                    }
                } catch (\Exception $e) {
                    \Log::warning('Voucher email failed: ' . $e->getMessage());
                }
            }

            // Confirmation email to the admin who issued the voucher
            try {
                $admin = Auth::user();
                if ($admin && $admin->email) {
                    Mail::to($admin->email)->send(new VoucherIssuedConfirmationMail($createdVouchers[0]));
                }
            } catch (\Exception $e) {
                \Log::warning('Admin voucher confirmation email failed: ' . $e->getMessage());
            }

            DB::commit();

            // Log voucher issuance
            $voucherCodes = implode(', ', array_map(function($v) { return '#' . $v->code; }, $createdVouchers));
            SystemLog::create([
                'user_id' => Auth::id(),
                'action' => 'issue',
                'entity_type' => 'Voucher',
                'entity_id' => $createdVouchers[0]->id,
                'description' => 'Issued ' . $quantity . ' voucher(s) ' . $voucherCodes . ' to ' . $createdVouchers[0]->recipient->name . ' for £' . number_format($valuePerVoucher, 2) . ' each (Total: £' . number_format($totalAmount, 2) . ')',
                'changes' => [
                    'recipient_id' => $request->recipient_id,
                    'quantity' => $quantity,
                    'value_per_voucher' => $valuePerVoucher,
                    'total_amount' => $totalAmount,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $successMessage = $quantity === 1 
                ? 'Voucher issued successfully. The recipient has been notified and a confirmation has been sent to your email.'
                : $quantity . ' vouchers issued successfully. The recipient has been notified and a confirmation has been sent to your email.';

            return redirect()->route('admin.vouchers.index')->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to issue voucher: ' . $e->getMessage()])
                ->withInput();
        }
    }

    private function storeManualVoucher(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'nullable|string|max:20',
            'county'        => 'required|string|max:255',
            'post_code'     => 'required|string|max:10',
            'category'      => 'required|string|max:255',
            'manual_amount' => 'required|numeric|min:0.01|max:500',
            'manual_quantity' => 'nullable|integer|min:1|max:100',
            'description'   => 'nullable|string|max:500',
            'expiry_date'   => 'nullable|date|after:today',
            'organization'  => 'required|string|max:255',
            'conditions'    => 'nullable|string|max:500',
            'internal_notes' => 'nullable|string|max:500',
        ]);

        // Get quantity (default to 1 if not provided)
        $quantity = $request->manual_quantity ?? 1;
        $totalAmount = $request->manual_amount;
        // Divide the total amount by quantity to get per-voucher value
        $valuePerVoucher = round($totalAmount / $quantity, 2);

        try {
            DB::beginTransaction();

            // Create a new user account for the recipient
            $tempPassword = Str::random(12);
            $user = User::create([
                'name'         => $request->first_name . ' ' . $request->last_name,
                'email'        => $request->email,
                'role'         => 'recipient',
                'is_active'    => true,
                'is_approved'  => true,
                'password'     => bcrypt($tempPassword),
            ]);

            // Store additional recipient information
            try {
                $user->recipientProfile()->create([
                    'phone'      => $request->phone,
                    'postcode'   => $request->post_code,
                ]);
            } catch (\Exception $e) {
                \Log::warning('Failed to create recipient profile: ' . $e->getMessage());
            }

            // Create multiple vouchers based on quantity
            $createdVouchers = [];
            for ($i = 0; $i < $quantity; $i++) {
                $voucher = Voucher::create([
                    'code'              => Voucher::generateCode(),
                    'recipient_user_id' => $user->id,
                    'issued_by'         => Auth::id(),
                    'value'             => $valuePerVoucher,
                    'remaining_value'   => $valuePerVoucher,
                    'status'            => 'active',
                    'expiry_date'       => $request->expiry_date ?? now()->addDays(30)->format('Y-m-d'),
                    'notes'             => $request->internal_notes,
                    'description'       => $request->description,
                    'conditions'        => $request->conditions,
                    'organization'      => $request->organization,
                ]);
                $createdVouchers[] = $voucher;
            }

            // Send email with registration link and voucher details for each voucher
            foreach ($createdVouchers as $voucher) {
                try {
                    Mail::to($user->email)->send(new VoucherIssuedMail($voucher, $tempPassword));
                } catch (\Exception $e) {
                    \Log::warning('Manual voucher email failed: ' . $e->getMessage());
                }
            }

            // Confirmation email to the admin
            try {
                $admin = Auth::user();
                if ($admin && $admin->email) {
                    Mail::to($admin->email)->send(new VoucherIssuedConfirmationMail($createdVouchers[0]));
                }
            } catch (\Exception $e) {
                \Log::warning('Admin voucher confirmation email failed: ' . $e->getMessage());
            }

            DB::commit();

            // Log voucher issuance
            $voucherCodes = implode(', ', array_map(function($v) { return '#' . $v->code; }, $createdVouchers));
            SystemLog::create([
                'user_id' => Auth::id(),
                'action' => 'issue',
                'entity_type' => 'Voucher',
                'entity_id' => $createdVouchers[0]->id,
                'description' => 'Issued ' . $quantity . ' voucher(s) ' . $voucherCodes . ' to new recipient ' . $user->name . ' for £' . number_format($valuePerVoucher, 2) . ' each (Total: £' . number_format($totalAmount, 2) . ')',
                'changes' => [
                    'recipient_id' => $user->id,
                    'quantity' => $quantity,
                    'value_per_voucher' => $valuePerVoucher,
                    'total_amount' => $totalAmount,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $successMessage = $quantity === 1 
                ? 'Voucher issued to ' . $user->email . '. Email sent with registration link and voucher details.'
                : $quantity . ' vouchers issued to ' . $user->email . '. Email sent with registration link and all voucher details.';

            return redirect()->route('admin.vouchers.index')->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to issue voucher: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Voucher $voucher)
    {
        $voucher->load(['recipient', 'issuedBy', 'redemptions.foodListing']);
        return view('admin.vouchers.show', compact('voucher'));
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'status' => 'required|in:active,redeemed,cancelled,expired',
        ]);

        $voucher->update(['status' => $request->status]);

        return redirect()->route('admin.vouchers.show', $voucher)->with('success', 'Voucher updated successfully.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher deleted successfully.');
    }
}
