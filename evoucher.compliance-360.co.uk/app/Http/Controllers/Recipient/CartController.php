<?php
namespace App\Http\Controllers\Recipient;

use App\Http\Controllers\Controller;
use App\Models\FoodListing;
use App\Models\Redemption;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Return the current cart from the session as a collection of FoodListing IDs.
     */
    private function getCart(): array
    {
        return session()->get('recipient_cart', []);
    }

    /**
     * Display the cart page.
     */
    public function index()
    {
        $cartIds  = $this->getCart();
        $listings = FoodListing::whereIn('id', $cartIds)
            ->where('status', 'available')
            ->where('listing_type', 'discounted')
            ->where('quantity', '>', 0)
            ->where('expiry_date', '>=', now()->toDateString())
            ->with('shop.shopProfile')
            ->get();

        // Remove any items that are no longer available from the session
        $validIds = $listings->pluck('id')->toArray();
        $removed  = count($cartIds) - count($validIds);
        if ($removed > 0) {
            session()->put('recipient_cart', $validIds);
        }

        $totalVoucherCost = $listings->sum('voucher_value');

        $user_vouchers = Voucher::where('recipient_user_id', Auth::id())
            ->whereIn('status', ['active', 'partially_used'])
            ->where('remaining_value', '>', 0)
            ->where('expiry_date', '>=', now()->toDateString())
            ->get();

        $totalVoucherBalance = $user_vouchers->sum('remaining_value');

        return view('recipient.cart', compact(
            'listings',
            'totalVoucherCost',
            'user_vouchers',
            'totalVoucherBalance',
            'removed'
        ));
    }

    /**
     * Add a food listing to the cart.
     */
    public function add(Request $request, FoodListing $listing)
    {
        // Validate the listing is available and discounted
        if ($listing->status !== 'available' || $listing->listing_type !== 'discounted' || $listing->quantity <= 0) {
            return back()->with('error', 'This item is no longer available.');
        }

        $cart = $this->getCart();

        if (in_array($listing->id, $cart)) {
            return back()->with('info', 'This item is already in your cart.');
        }

        $cart[] = $listing->id;
        session()->put('recipient_cart', $cart);

        return back()->with('success', '"' . $listing->item_name . '" added to your cart.');
    }

    /**
     * Remove a food listing from the cart.
     */
    public function remove(FoodListing $listing)
    {
        $cart = array_filter($this->getCart(), fn($id) => $id !== $listing->id);
        session()->put('recipient_cart', array_values($cart));

        return back()->with('success', '"' . $listing->item_name . '" removed from your cart.');
    }

    /**
     * Clear the entire cart.
     */
    public function clear()
    {
        session()->forget('recipient_cart');
        return back()->with('success', 'Cart cleared.');
    }

    /**
     * Checkout: redeem all cart items using the selected voucher.
     */
    public function checkout(Request $request)
    {
        $request->validate(['voucher_id' => 'required|exists:vouchers,id']);

        $user = Auth::user();

        $voucher = Voucher::where('id', $request->voucher_id)
            ->where('recipient_user_id', $user->id)
            ->whereIn('status', ['active', 'partially_used'])
            ->where('remaining_value', '>', 0)
            ->where('expiry_date', '>=', now()->toDateString())
            ->firstOrFail();

        $cartIds  = $this->getCart();
        if (empty($cartIds)) {
            return redirect()->route('recipient.cart')->with('error', 'Your cart is empty.');
        }

        $listings = FoodListing::whereIn('id', $cartIds)
            ->where('status', 'available')
            ->where('listing_type', 'discounted')
            ->where('quantity', '>', 0)
            ->where('expiry_date', '>=', now()->toDateString())
            ->get();

        if ($listings->isEmpty()) {
            session()->forget('recipient_cart');
            return redirect()->route('recipient.food.browse')->with('error', 'None of the items in your cart are still available.');
        }

        $totalOwedAtShop = 0;
        $redeemedCount   = 0;

        DB::transaction(function () use ($voucher, $listings, $user, &$totalOwedAtShop, &$redeemedCount) {
            foreach ($listings as $listing) {
                $itemCost         = $listing->voucher_value ?? 0;
                $voucherUsed      = min($voucher->remaining_value, $itemCost);
                $amountOwedAtShop = max(0, $itemCost - $voucherUsed);

                Redemption::create([
                    'voucher_id'          => $voucher->id,
                    'food_listing_id'     => $listing->id,
                    'recipient_user_id'   => $user->id,
                    'shop_user_id'        => $listing->shop_user_id,
                    'amount_used'         => $voucherUsed,
                    'amount_owed_at_shop' => $amountOwedAtShop,
                    'status'              => 'pending',
                    'redeemed_at'         => now(),
                ]);

                $newRemaining = $voucher->remaining_value - $voucherUsed;
                $voucher->update([
                    'remaining_value' => $newRemaining,
                    'status'          => $newRemaining <= 0 ? 'redeemed' : 'partially_used',
                ]);

                $listing->update(['status' => 'reserved']);

                $totalOwedAtShop += $amountOwedAtShop;
                $redeemedCount++;

                // Stop if voucher is fully used
                if ($voucher->remaining_value <= 0) {
                    break;
                }
            }
        });

        // Clear the cart
        session()->forget('recipient_cart');

        $msg = $redeemedCount . ' item' . ($redeemedCount > 1 ? 's' : '') . ' redeemed successfully! Please collect your items from the shops.';
        if ($totalOwedAtShop > 0) {
            $msg .= ' You will need to pay a total of £' . number_format($totalOwedAtShop, 2) . ' at the shop(s).';
        }

        return redirect()->route('recipient.history')->with('success', $msg);
    }
}
