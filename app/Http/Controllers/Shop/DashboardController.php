<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\FoodListing;
use App\Models\Redemption;
use App\Models\ShopPayoutRequest;
use App\Models\Voucher;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $totalListings     = FoodListing::where('shop_user_id', $user->id)->count();
        $availableListings = FoodListing::where('shop_user_id', $user->id)->where('status', 'available')->count();
        $redeemedCount     = Redemption::where('shop_user_id', $user->id)->count();
        $expiringSoon      = FoodListing::where('shop_user_id', $user->id)
                              ->where('status', 'available')
                              ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(3)->toDateString()])
                              ->count();
        $listings          = FoodListing::where('shop_user_id', $user->id)->latest()->take(5)->get();
        $recentRedemptions = Redemption::where('shop_user_id', $user->id)
                              ->with(['recipient.recipientProfile', 'foodListing', 'voucher', 'payoutRequest'])
                              ->latest()->take(5)->get();
        
        // Pending redemptions count for notification badge
        $pendingRedemptionsCount = Redemption::where('shop_user_id', $user->id)
                              ->where('status', 'pending')->count();

        // Payout summary
        $unpaidAmount = Redemption::where('shop_user_id', $user->id)
                          ->where('status', 'collected')
                          ->whereNull('payout_request_id')
                          ->with('foodListing')
                          ->get()
                          ->sum(fn($r) => $r->amount_used > 0 ? $r->amount_used : ($r->foodListing->voucher_value ?? 0));
        $pendingPayouts = ShopPayoutRequest::where('shop_user_id', $user->id)
                          ->whereIn('status', ['pending', 'approved'])->count();
        $totalPaidOut   = ShopPayoutRequest::where('shop_user_id', $user->id)
                          ->where('status', 'paid')->sum('total_amount');

        return view('shop.dashboard', compact(
            'totalListings', 'availableListings', 'redeemedCount',
            'expiringSoon', 'listings', 'recentRedemptions',
            'unpaidAmount', 'pendingPayouts', 'totalPaidOut', 'pendingRedemptionsCount'
        ));
    }

    public function redemptions()
    {
        $redemptions = Redemption::where('shop_user_id', Auth::id())
            ->with(['recipient.recipientProfile', 'foodListing', 'voucher', 'payoutRequest'])
            ->latest()->paginate(20);

        return view('shop.redemptions', compact('redemptions'));
    }

    public function confirmRedemption(Request $request, $id)
    {
        $redemption = Redemption::where('id', $id)
            ->where('shop_user_id', Auth::id())
            ->firstOrFail();

        $updateData = ['status' => 'collected'];

        // If this is a payment confirmation from the modal (priced item)
        if ($request->input('payment_confirmed') == '1') {
            $request->validate([
                'payment_method' => 'required|in:cash,card,contactless,bank_transfer',
            ]);
            $updateData['payment_collected'] = true;
            $updateData['payment_method']    = $request->input('payment_method');
        }

        $redemption->update($updateData);

        $msg = 'Redemption marked as collected.';
        if (!empty($updateData['payment_method'])) {
            $msg .= ' Payment received by ' . ucfirst(str_replace('_', ' ', $updateData['payment_method'])) . '.';
        }
        return back()->with('success', $msg);
    }

    // -------------------------------------------------------
    // Voucher Verification Flow
    // -------------------------------------------------------

    /**
     * Show the voucher verification page (QR scan + manual entry).
     */
    public function verifyVoucher(Request $request)
    {
        $voucher      = null;
        $foodListings = collect();
        $error        = null;
        $code         = $request->query('code');

        if ($code) {
            $result = $this->resolveVoucher(strtoupper(trim($code)));
            $voucher      = $result['voucher'];
            $foodListings = $result['foodListings'];
            $error        = $result['error'];
        }

        return view('shop.verify', compact('voucher', 'foodListings', 'error', 'code'));
    }

    /**
     * POST: Look up a voucher code and redirect to verify page with code in query string.
     */
    public function lookupVoucher(Request $request)
    {
        $request->validate(['code' => 'required|string|max:30']);
        $code = strtoupper(trim($request->input('code')));
        return redirect()->route('shop.verify', ['code' => $code]);
    }

    /**
     * POST: Accept/confirm a voucher — create redemption and mark as collected.
     * Handles partial vouchers and payment collection for priced items.
     */
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

        $foodListing = FoodListing::where('id', $listingId)
            ->where('shop_user_id', $shopUserId)
            ->where('status', 'available')
            ->first();

        if (!$foodListing) {
            return back()->withErrors(['code' => 'Food listing not found or not available at your shop.'])->withInput();
        }

        // Prevent double redemption of the same voucher on the same item
        $alreadyRedeemed = Redemption::where('voucher_id', $voucher->id)
            ->where('food_listing_id', $listingId)
            ->whereIn('status', ['pending', 'collected'])
            ->exists();

        if ($alreadyRedeemed) {
            return back()->withErrors(['code' => 'This voucher has already been redeemed for this item.'])->withInput();
        }

        // Calculate amounts
        $itemCost          = (float) ($foodListing->voucher_value ?? 0);
        $voucherBalance    = (float) $voucher->remaining_value;
        $amountUsed        = min($voucherBalance, $itemCost);
        $amountOwedAtShop  = max(0, $itemCost - $voucherBalance);
        $newRemainingValue = max(0, $voucherBalance - $itemCost);

        // If customer owes money at shop, payment_method is required
        if ($amountOwedAtShop > 0 && empty($paymentMethod)) {
            return back()->withErrors(['payment_method' => 'Please select the payment method used by the customer to pay the outstanding amount of £' . number_format($amountOwedAtShop, 2) . '.'])->withInput();
        }

        // Create redemption record
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

        // Update voucher remaining value and status
        if ($newRemainingValue <= 0) {
            $voucher->update(['remaining_value' => 0, 'status' => 'redeemed']);
        } else {
            $voucher->update(['remaining_value' => $newRemainingValue, 'status' => 'partially_used']);
        }

        // Mark food listing as redeemed (quantity management could be added later)
        $foodListing->update(['status' => 'redeemed']);

        // Notify shop about the voucher redemption
        NotificationService::notifyShopVoucherRedemption($redemption);

        $msg = 'Voucher accepted! Food item marked as collected.';
        if ($amountOwedAtShop > 0) {
            $msg .= ' Customer paid £' . number_format($amountOwedAtShop, 2) . ' by ' . ucfirst(str_replace('_', ' ', $paymentMethod)) . '.';
        }
        if ($newRemainingValue > 0) {
            $msg .= ' Voucher has £' . number_format($newRemainingValue, 2) . ' remaining balance.';
        }

        return redirect()->route('shop.verify')
            ->with('success', $msg);
    }

    /**
     * POST: Reject a voucher (no state change — just inform the shop).
     */
    public function rejectVoucher(Request $request)
    {
        $request->validate(['code' => 'required|string|max:30']);
        $code = strtoupper(trim($request->input('code')));

        return redirect()->route('shop.verify')
            ->with('info', 'Voucher ' . $code . ' was rejected. No changes have been made.');
    }

    public function profile()
    {
        $profile = Auth::user()->shopProfile;
        return view('shop.profile', compact('profile'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'shop_name'     => 'required|string|max:200',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'required|string',
            'town'          => 'nullable|string|max:100',
            'postcode'      => 'nullable|string|max:10',
            'opening_hours' => 'nullable|string',
            'description'   => 'nullable|string',
        ]);

        Auth::user()->shopProfile->update($request->only([
            'shop_name', 'phone', 'address', 'town', 'postcode', 'opening_hours', 'description'
        ]));

        return back()->with('success', 'Profile updated.');
    }

    // -------------------------------------------------------
    // Private helper
    // -------------------------------------------------------

    private function resolveVoucher(string $code): array
    {
        $voucher      = null;
        $foodListings = collect();
        $error        = null;

        $voucher = Voucher::with(['recipient.recipientProfile', 'redemptions.foodListing'])
            ->where('code', $code)
            ->first();

        if (!$voucher) {
            $error = 'No voucher found with code "' . $code . '". Please check and try again.';
            return compact('voucher', 'foodListings', 'error');
        }

        if ($voucher->status === 'redeemed') {
            $error = 'This voucher has already been fully redeemed.';
        } elseif ($voucher->status === 'cancelled') {
            $error = 'This voucher has been cancelled and cannot be used.';
        } elseif ($voucher->status === 'expired' || $voucher->expiry_date < now()->toDateString()) {
            $error = 'This voucher expired on ' . $voucher->expiry_date->format('d M Y') . '.';
        } elseif ($voucher->remaining_value <= 0) {
            $error = 'This voucher has no remaining balance.';
        }

        // Get available food listings at this shop
        $foodListings = FoodListing::where('shop_user_id', Auth::id())
            ->where('status', 'available')
            ->orderBy('expiry_date')
            ->get();

        return compact('voucher', 'foodListings', 'error');
    }
}
