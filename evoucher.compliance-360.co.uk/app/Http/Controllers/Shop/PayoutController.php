<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopBankDetail;
use App\Models\ShopPayoutRequest;
use App\Models\Redemption;
use App\Models\Notification;
use App\Models\User;
use App\Models\BankDetailChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    /**
     * Show bank details form and payout overview.
     */
    public function index()
    {
        $user = Auth::user();

        $bankDetails = ShopBankDetail::where('shop_user_id', $user->id)->first();

        // Confirmed/Collected redemptions not yet linked to a payout request
        $unpaidRedemptions = Redemption::with('foodListing')
            ->where('shop_user_id', $user->id)
            ->whereIn('status', ['confirmed', 'collected'])
            ->whereNull('payout_request_id')
            ->get();

        $unpaidTotal = $unpaidRedemptions->sum(function ($r) {
            return $r->amount_used > 0 ? $r->amount_used : ($r->foodListing->voucher_value ?? 0);
        });

        // Past payout requests
        $payoutRequests = ShopPayoutRequest::where('shop_user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $totalPaid = $payoutRequests->where('status', 'paid')->sum('total_amount');
        $totalPending = $payoutRequests->whereIn('status', ['pending', 'approved'])->sum('total_amount');

        return view('shop.payouts.index', compact(
            'bankDetails',
            'unpaidRedemptions',
            'unpaidTotal',
            'payoutRequests',
            'totalPaid',
            'totalPending'
        ));
    }

    /**
     * Save or update bank details.
     */
    public function saveBankDetails(Request $request)
    {
        $request->validate([
            'account_holder_name' => 'required|string|max:100',
            'bank_name'           => 'required|string|max:100',
            'sort_code'           => ['required', 'string', 'regex:/^\d{2}-\d{2}-\d{2}$/'],
            'account_number'      => ['required', 'string', 'regex:/^\d{8}$/'],
            'bank_reference'      => 'nullable|string|max:50',
        ], [
            'sort_code.regex'      => 'Sort code must be in the format 12-34-56.',
            'account_number.regex' => 'Account number must be exactly 8 digits.',
        ]);

        $user = Auth::user();
        $existingBankDetail = ShopBankDetail::where('shop_user_id', $user->id)->first();

        // If bank details already exist and are locked, create a change request instead
        if ($existingBankDetail && $existingBankDetail->isLocked()) {
            // Check if there's already a pending change request
            $pendingRequest = BankDetailChangeRequest::where('bank_detail_id', $existingBankDetail->id)
                ->where('status', 'pending')
                ->first();

            if ($pendingRequest) {
                return redirect()->route('shop.payouts.index')
                    ->with('error', 'You already have a pending bank detail change request. Please wait for admin approval.');
            }

            // Create a new change request
            BankDetailChangeRequest::create([
                'shop_user_id' => $user->id,
                'bank_detail_id' => $existingBankDetail->id,
                'account_holder_name' => $request->account_holder_name,
                'bank_name' => $request->bank_name,
                'sort_code' => $request->sort_code,
                'account_number' => $request->account_number,
                'bank_reference' => $request->bank_reference,
                'status' => 'pending',
            ]);

            return redirect()->route('shop.payouts.index')
                ->with('success', 'Bank detail change request submitted. Admin will review and approve your changes.');
        }

        // If no existing bank details, create new ones with active status
        ShopBankDetail::updateOrCreate(
            ['shop_user_id' => $user->id],
            [
                'account_holder_name' => $request->account_holder_name,
                'bank_name'           => $request->bank_name,
                'sort_code'           => $request->sort_code,
                'account_number'      => $request->account_number,
                'bank_reference'      => $request->bank_reference,
                'status'              => 'active',
            ]
        );

        return redirect()->route('shop.payouts.index')
            ->with('success', 'Bank details saved successfully.');
    }

    /**
     * Submit a payout request for all unpaid collected redemptions.
     */
    public function requestPayout(Request $request)
    {
        $user = Auth::user();

        // Must have approved bank details on file
        $bankDetails = ShopBankDetail::where('shop_user_id', $user->id)
            ->where('status', 'active')
            ->first();
        if (!$bankDetails) {
            return redirect()->route('shop.payouts.index')
                ->with('error', 'Please save your bank details before requesting a payout.');
        }

        // Get unpaid confirmed/collected redemptions
        $unpaidRedemptions = Redemption::with('foodListing')
            ->where('shop_user_id', $user->id)
            ->whereIn('status', ['confirmed', 'collected'])
            ->whereNull('payout_request_id')
            ->get();

        if ($unpaidRedemptions->isEmpty()) {
            return redirect()->route('shop.payouts.index')
                ->with('error', 'No collected redemptions available for payout.');
        }

        $totalAmount = $unpaidRedemptions->sum(function ($r) {
            return $r->amount_used > 0 ? $r->amount_used : ($r->foodListing->voucher_value ?? 0);
        });

        if ($totalAmount <= 0) {
            return redirect()->route('shop.payouts.index')
                ->with('error', 'Total payout amount is £0.00. Nothing to request.');
        }

        DB::transaction(function () use ($user, $unpaidRedemptions, $totalAmount) {
            $payout = ShopPayoutRequest::create([
                'shop_user_id'    => $user->id,
                'total_amount'    => $totalAmount,
                'redemption_count' => $unpaidRedemptions->count(),
                'status'          => 'pending',
            ]);

            // Link each redemption to this payout request
            Redemption::whereIn('id', $unpaidRedemptions->pluck('id'))
                ->update(['payout_request_id' => $payout->id]);

            // Create notifications for all admin and superadmin users
            $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'New Payout Request',
                    'message' => $user->name . ' submitted a payout request for £' . number_format($totalAmount, 2),
                    'type' => 'payout_request',
                    'icon' => 'fas fa-money-bill',
                    'read_at' => null,
                ]);
            }
        });

        return redirect()->route('shop.payouts.index')
            ->with('success', 'Payout request submitted successfully. The admin will process your payment.');
    }

    /**
     * Show detail of a single payout request.
     */
    public function show($id)
    {
        $user = Auth::user();
        $payout = ShopPayoutRequest::with(['redemptions.foodListing'])
            ->where('shop_user_id', $user->id)
            ->findOrFail($id);

        return view('shop.payouts.show', compact('payout'));
    }
}
