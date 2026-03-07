<?php

namespace App\Http\Controllers;

use App\Models\SurplusAllocation;
use App\Models\FoodListing;
use App\Models\Redemption;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurplusClaimController extends Controller
{
    /**
     * Claim a surplus item (VCFSE member claims an allocated item)
     */
    public function claim(Request $request, $foodListingId)
    {
        $user = Auth::user();

        // Verify user is VCFSE
        if ($user->role !== 'vcfse') {
            return redirect()->back()->with('error', 'Only VCFSE members can claim surplus items');
        }

        // Get the allocation
        $allocation = SurplusAllocation::where('food_listing_id', $foodListingId)
            ->where('vcfse_user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$allocation) {
            return redirect()->back()->with('error', 'No allocation found for this item');
        }

        // Check if allocation has expired
        if ($allocation->isExpired()) {
            $allocation->update(['status' => 'expired']);
            return redirect()->back()->with('error', 'Allocation has expired');
        }

        // Update allocation status to claimed
        $allocation->update(['status' => 'claimed', 'claimed_at' => now()]);

        // Get the food listing
        $foodListing = FoodListing::find($foodListingId);

        // Create redemption record
        $redemption = Redemption::create([
            'food_listing_id' => $foodListingId,
            'user_id' => $user->id,
            'quantity' => 1,
            'redeemed_at' => now(),
            'status' => 'confirmed',
        ]);

        // Update food listing quantity
        $foodListing->decrement('quantity');

        // If quantity reaches 0, mark as redeemed
        if ($foodListing->quantity <= 0) {
            $allocation->update(['status' => 'redeemed']);
            $foodListing->update(['status' => 'collected']);
        }

        // Send notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'surplus_redeemed',
            'title' => 'Surplus Item Redeemed',
            'message' => 'You have successfully redeemed: ' . $foodListing->item_name,
            'icon' => 'fas fa-check-circle',
        ]);

        return redirect()->back()->with('success', 'Item claimed successfully! You can now collect it from the shop.');
    }
}
