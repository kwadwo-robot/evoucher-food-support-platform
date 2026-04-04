<?php
namespace App\Http\Controllers\Recipient;
use App\Http\Controllers\Controller;
use App\Models\FoodListing;
use App\Models\Redemption;
use App\Models\Voucher;
use App\Models\SystemLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::where('recipient_user_id', Auth::id())->with('redemptions')->latest()->paginate(10);
        return view('recipient.vouchers.index', compact('vouchers'));
    }

    public function show(Voucher $voucher)
    {
        abort_if($voucher->recipient_user_id !== Auth::id(), 403);
        $voucher->load(['redemptions.foodListing.shop.shopProfile','issuedBy']);
        
        // Log voucher view
        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'view',
            'entity_type' => 'Voucher',
            'entity_id' => $voucher->id,
            'description' => 'Viewed voucher #' . $voucher->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        
        return view('recipient.vouchers.show', compact('voucher'));
    }

    public function redeem(Request $request, FoodListing $listing)
    {
        $request->validate(['voucher_id' => 'required|exists:vouchers,id']);

        $user = Auth::user();

        // Allow active OR partially_used vouchers
        $voucher = Voucher::where('id', $request->voucher_id)
            ->where('recipient_user_id', $user->id)
            ->whereIn('status', ['active', 'partially_used'])
            ->where('remaining_value', '>', 0)
            ->where('expiry_date', '>=', now()->toDateString())
            ->firstOrFail();

        if ($listing->status !== 'available') {
            return back()->with('error', 'This item is no longer available.');
        }

        if ($voucher->remaining_value <= 0) {
            return back()->with('error', 'Your voucher has no remaining balance.');
        }

        // Calculate how much of the voucher is used vs how much recipient pays at shop
        $itemCost       = $listing->voucher_value ?? 0;
        $voucherUsed    = min($voucher->remaining_value, $itemCost);
        $amountOwedAtShop = max(0, $itemCost - $voucherUsed);

        DB::transaction(function() use ($voucher, $listing, $user, $voucherUsed, $amountOwedAtShop) {
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
            
            // Log voucher redemption
            SystemLog::create([
                'user_id' => $user->id,
                'action' => 'redeem',
                'entity_type' => 'Voucher',
                'entity_id' => $voucher->id,
                'description' => 'Redeemed voucher #' . $voucher->id . ' for food item. Amount used: £' . number_format($voucherUsed, 2),
                'changes' => [
                    'voucher_remaining' => $newRemaining,
                    'amount_used' => $voucherUsed,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        // Get the created redemption to send notification
        $redemption = Redemption::where('voucher_id', $voucher->id)
            ->where('food_listing_id', $listing->id)
            ->where('recipient_user_id', $user->id)
            ->latest()
            ->first();
        
        if ($redemption) {
            NotificationService::notifyRecipientRedemptionConfirmed($redemption);
        }

        $msg = 'Voucher redeemed! Please collect your item from the shop.';
        if ($amountOwedAtShop > 0) {
            $msg .= ' You will need to pay £' . number_format($amountOwedAtShop, 2) . ' at the shop for this item.';
        }

        return redirect()->route('recipient.history')->with('success', $msg);
    }
}
