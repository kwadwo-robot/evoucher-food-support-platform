<?php

namespace App\Http\Controllers\Shop;

use App\Models\FoodListing;
use App\Models\Redemption;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $shopUser = Auth::user();
        $totalListings = FoodListing::where('shop_user_id', $shopUser->id)->count();
        $activeListings = FoodListing::where('shop_user_id', $shopUser->id)
            ->where('status', 'available')
            ->count();
        $totalRedemptions = Redemption::where('shop_user_id', $shopUser->id)->count();
        $totalEarnings = Redemption::where('shop_user_id', $shopUser->id)
            ->sum('amount_owed_at_shop');
        $redeemedListings = FoodListing::where('shop_user_id', $shopUser->id)->where('status', 'redeemed')->count();
        $recentRedemptions = Redemption::where('shop_user_id', $shopUser->id)->with(['voucher', 'foodListing', 'recipient'])->orderBy('created_at', 'desc')->limit(5)->get();
        return view('shop.dashboard', compact('totalListings', 'activeListings', 'redeemedListings', 'totalRedemptions', 'totalEarnings', 'recentRedemptions'));
    }

    public function verifyVoucher(Request $request)
    {
        $voucher          = null;
        $foodListings     = collect();
        $redemptionHistory = collect();
        $error            = null;
        $code             = $request->query('code');

        if ($code) {
            $result = $this->resolveVoucher(strtoupper(trim($code)));
            $voucher             = $result['voucher'];
            $foodListings        = $result['foodListings'];
            $redemptionHistory   = $result['redemptionHistory'];
            $error               = $result['error'];
        }

        return view('shop.verify', compact('voucher', 'foodListings', 'redemptionHistory', 'error', 'code'));
    }

    public function lookupVoucher(Request $request)
    {
        $request->validate(['code' => 'required|string|max:30']);
        $code = strtoupper(trim($request->input('code')));
        return redirect()->route('shop.verify', ['code' => $code]);
    }

    public function acceptVoucher(Request $request)
    {
        $request->validate([
            'code'            => 'required|string|max:30',
            'food_listing_id' => 'required|integer|exists:food_listings,id',
            'payment_method'  => 'nullable|string|in:cash,card,contactless,bank_transfer',
        ]);

        $code          = strtoupper(trim($request->input('code')));
        $listingId     = (int) $request->input('food_listing_id');
        $paymentMethod = $request->input('payment_method');
        $shopUserId    = Auth::id();

        $voucher = Voucher::where('code', $code)->first();
        if (!$voucher) {
            return back()->withErrors(['code' => 'Voucher not found.'])->withInput();
        }

        if (!in_array($voucher->status, ['active', 'partially_used'])) {
            return back()->withErrors(['code' => 'This voucher is not active (status: ' . $voucher->status . ').'])->withInput();
        }

        if ($voucher->remaining_value <= 0) {
            return back()->withErrors(['code' => 'This voucher has no remaining balance.'])->withInput();
        }

        if ($voucher->expiry_date < now()->toDateString()) {
            return back()->withErrors(['code' => 'This voucher has expired.'])->withInput();
        }

        $previousRedemption = Redemption::where('voucher_id', $voucher->id)
            ->where('food_listing_id', $listingId)
            ->first();

        $isContinuationRedemption = $previousRedemption !== null;

        if ($isContinuationRedemption) {
            $foodListing = FoodListing::where('id', $listingId)
                ->where('shop_user_id', $shopUserId)
                ->first();
        } else {
            $foodListing = FoodListing::where('id', $listingId)
                ->where('shop_user_id', $shopUserId)
                ->where('status', 'available')
                ->first();
        }

        if (!$foodListing) {
            return back()->withErrors(['code' => 'Food listing not found or not available at your shop.'])->withInput();
        }

        if ($previousRedemption && in_array($previousRedemption->status, ['pending', 'collected'])) {
            return back()->withErrors(['code' => 'This voucher has already been redeemed for this item.'])->withInput();
        }

        $itemCost          = (float) ($foodListing->voucher_value ?? 0);
        $voucherBalance    = (float) $voucher->remaining_value;
        $amountUsed        = min($voucherBalance, $itemCost);
        $amountOwedAtShop  = max(0, $itemCost - $voucherBalance);
        $newRemainingValue = max(0, $voucherBalance - $itemCost);

        if ($amountOwedAtShop > 0 && empty($paymentMethod)) {
            return back()->withErrors(['payment_method' => 'Please select the payment method used by the customer to pay the outstanding amount of £' . number_format($amountOwedAtShop, 2) . '.'])->withInput();
        }

        $redemption = Redemption::create([
            'voucher_id'          => $voucher->id,
            'food_listing_id'     => $listingId,
            'shop_user_id'        => $shopUserId,
            'recipient_user_id'   => $voucher->recipient_user_id,
            'amount_used'         => $amountUsed,
            'amount_owed_at_shop' => $amountOwedAtShop,
            'payment_collected'   => $amountOwedAtShop > 0 ? 1 : 0,
            'payment_method'      => $amountOwedAtShop > 0 ? $paymentMethod : null,
            'status'              => 'collected',
            'redeemed_at'         => now(),
        ]);

        if ($newRemainingValue <= 0) {
            $voucher->update(['remaining_value' => 0, 'status' => 'redeemed']);
        } else {
            $voucher->update(['remaining_value' => $newRemainingValue, 'status' => 'partially_used']);
        }

        return redirect()->route('shop.verify')->with('success', 'Voucher redeemed successfully! Amount used: £' . number_format($amountUsed, 2) . ($amountOwedAtShop > 0 ? ', Amount collected: £' . number_format($amountOwedAtShop, 2) : '') . '.');
    }

    public function acceptVoucherDirect(Request $request)
    {
        $request->validate([
            'code'                => 'required|string|max:30',
            'redemption_amount'   => 'required|numeric|min:0.01',
            'payment_method'      => 'nullable|string|in:cash,card,contactless,bank_transfer',
            'top_up_amount'       => 'nullable|numeric|min:0',
        ]);

        $code = strtoupper(trim($request->input('code')));
        $redemptionAmount = (float) $request->input('redemption_amount');
        $topUpAmount = (float) ($request->input('top_up_amount') ?? 0);
        $paymentMethod = $request->input('payment_method');
        $shopUserId = Auth::id();

        $voucher = Voucher::where('code', $code)->first();
        if (!$voucher) {
            return back()->withErrors(['code' => 'Voucher not found.'])->withInput();
        }

        if (!in_array($voucher->status, ['active', 'partially_used'])) {
            return back()->withErrors(['code' => 'This voucher is not active.'])->withInput();
        }

        if ($voucher->remaining_value <= 0) {
            return back()->withErrors(['code' => 'This voucher has no remaining balance.'])->withInput();
        }

        if ($voucher->expiry_date < now()->toDateString()) {
            return back()->withErrors(['code' => 'This voucher has expired.'])->withInput();
        }

        if ($redemptionAmount > $voucher->remaining_value) {
            return back()->withErrors(['redemption_amount' => 'Amount cannot exceed voucher balance.'])->withInput();
        }

        if ($topUpAmount > 0 && empty($paymentMethod)) {
            return back()->withErrors(['payment_method' => 'Payment method required for top-up.'])->withInput();
        }

        $redemption = Redemption::create([
            'voucher_id'          => $voucher->id,
            'food_listing_id'     => null,
            'shop_user_id'        => $shopUserId,
            'recipient_user_id'   => $voucher->recipient_user_id,
            'amount_used'         => $redemptionAmount,
            'amount_owed_at_shop' => $topUpAmount,
            'payment_collected'   => $topUpAmount > 0 ? 1 : 0,
            'payment_method'      => $topUpAmount > 0 ? $paymentMethod : null,
            'status'              => 'collected',
            'redeemed_at'         => now(),
        ]);

        $newRemainingValue = $voucher->remaining_value - $redemptionAmount;
        if ($newRemainingValue <= 0) {
            $voucher->update(['remaining_value' => 0, 'status' => 'redeemed']);
        } else {
            $voucher->update(['remaining_value' => $newRemainingValue, 'status' => 'partially_used']);
        }

        return redirect()->route('shop.verify')->with('success', 'Voucher redeemed! Used: £' . number_format($redemptionAmount, 2) . ($topUpAmount > 0 ? ', Top-up: £' . number_format($topUpAmount, 2) : '') . '.');
    }

    private function resolveVoucher(string $code): array
    {
        $voucher             = null;
        $foodListings        = collect();
        $redemptionHistory   = collect();
        $error               = null;

        // Handle pipe-separated QR code format: CODE|AMOUNT|DATE|RECIPIENT_NAME
        // Extract just the voucher code (first part before the pipe)
        if (strpos($code, "|") !== false) {
            $parts = explode("|", $code);
            $code = trim($parts[0]);
        }

        $voucher = Voucher::with(['recipient.recipientProfile', 'redemptions.foodListing'])
            ->where('code', $code)
            ->first();

        if (!$voucher) {
            $error = 'No voucher found with code "' . $code . '". Please check and try again.';
            return compact('voucher', 'foodListings', 'redemptionHistory', 'error');
        }

        // Get redemption history for this voucher
        $redemptionHistory = Redemption::where('voucher_id', $voucher->id)
            ->with(['foodListing', 'recipient.recipientProfile'])
            ->orderBy('redeemed_at', 'desc')
            ->get();

        if ($voucher->status === 'redeemed') {
            $error = 'This voucher has already been fully redeemed.';
        } elseif ($voucher->status === 'cancelled') {
            $error = 'This voucher has been cancelled and cannot be used.';
        } elseif ($voucher->status === 'expired' || $voucher->expiry_date < now()->toDateString()) {
            $error = 'This voucher expired on ' . $voucher->expiry_date->format('d M Y') . '.';
        } elseif ($voucher->remaining_value <= 0) {
            $error = 'This voucher has no remaining balance.';
        }

        // If voucher has been redeemed before, only show the original item
        if ($redemptionHistory->isNotEmpty()) {
            // Get the first (original) item that was redeemed
            $originalItemId = $redemptionHistory->first()->food_listing_id;
            $originalListing = FoodListing::where('id', $originalItemId)
                ->where('shop_user_id', Auth::id())
                ->first();

            if ($originalListing) {
                // Show only the original item for continued redemption
                $foodListings = collect([$originalListing]);
            } else {
                // Original item no longer exists, show empty (cannot redeem further)
                $foodListings = collect();
            }
        } else {
            // First redemption - show all available items at this shop
            $foodListings = FoodListing::where('shop_user_id', Auth::id())
                ->where('status', 'available')
                ->orderBy('expiry_date')
                ->get();
        }

        return compact('voucher', 'foodListings', 'redemptionHistory', 'error');
    }

    public function redemptions()
    {
        $shopUser = Auth::user();
        $redemptions = Redemption::where('shop_user_id', $shopUser->id)
            ->with(['voucher', 'foodListing', 'recipient'])
            ->orderBy('redeemed_at', 'desc')
            ->paginate(20);
        return view('shop.redemptions', compact('redemptions'));
    }
}
