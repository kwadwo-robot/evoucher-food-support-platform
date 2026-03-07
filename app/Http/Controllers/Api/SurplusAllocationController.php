<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SurplusAllocation;
use App\Models\FoodListing;
use App\Models\Redemption;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurplusAllocationController extends Controller
{
    /**
     * Get current allocation for a surplus item
     */
    public function getCurrentAllocation($foodListingId)
    {
        $allocation = SurplusAllocation::where('food_listing_id', $foodListingId)
            ->where('status', 'pending')
            ->first();

        if (!$allocation) {
            return response()->json(['allocation' => null]);
        }

        return response()->json([
            'allocation' => [
                'id' => $allocation->id,
                'vcfse_user_id' => $allocation->vcfse_user_id,
                'allocated_at' => $allocation->allocated_at,
                'expires_at' => $allocation->expires_at,
                'time_remaining_minutes' => $allocation->getTimeRemainingMinutes(),
                'is_expired' => $allocation->isExpired(),
            ]
        ]);
    }

    /**
     * Claim a surplus item (VCFSE member claims an allocated item)
     */
    public function claim(Request $request, $foodListingId)
    {
        $user = Auth::user();

        // Verify user is VCFSE
        if ($user->role !== 'vcfse') {
            return response()->json(['error' => 'Only VCFSE members can claim surplus items'], 403);
        }

        // Get the allocation
        $allocation = SurplusAllocation::where('food_listing_id', $foodListingId)
            ->where('vcfse_user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$allocation) {
            return response()->json(['error' => 'No allocation found for this item'], 404);
        }

        // Check if allocation has expired
        if ($allocation->isExpired()) {
            $allocation->update(['status' => 'expired']);
            return response()->json(['error' => 'Allocation has expired'], 410);
        }

        // Update allocation status to claimed
        $allocation->update(['status' => 'claimed']);

        // Get the food listing
        $foodListing = FoodListing::find($foodListingId);

        // Create redemption record
        $redemption = Redemption::create([
            'food_listing_id' => $foodListingId,
            'vcfse_user_id' => $user->id,
            'quantity_redeemed' => 1,
            'redemption_date' => now(),
            'status' => 'completed',
        ]);

        // Update food listing quantity
        $foodListing->decrement('quantity');

        // If quantity reaches 0, mark as redeemed
        if ($foodListing->quantity <= 0) {
            $allocation->update(['status' => 'redeemed']);
            $foodListing->update(['status' => 'collected']);
        }

        // Send notification
        NotificationService::create([
            'user_id' => $user->id,
            'type' => 'surplus_redeemed',
            'title' => 'Surplus Item Redeemed',
            'message' => 'You have successfully redeemed: ' . $foodListing->item_name,
            'icon' => 'fas fa-check-circle',
            'related_id' => $foodListingId,
            'related_type' => 'FoodListing',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item claimed successfully',
            'redemption' => $redemption
        ]);
    }

    /**
     * Get surplus items allocated to current user
     */
    public function myAllocations()
    {
        $user = Auth::user();

        $allocations = SurplusAllocation::where('vcfse_user_id', $user->id)
            ->where('status', 'pending')
            ->with('foodListing')
            ->get()
            ->map(function ($allocation) {
                return [
                    'id' => $allocation->id,
                    'food_listing_id' => $allocation->food_listing_id,
                    'item_name' => $allocation->foodListing->item_name,
                    'allocated_at' => $allocation->allocated_at,
                    'expires_at' => $allocation->expires_at,
                    'time_remaining_minutes' => $allocation->getTimeRemainingMinutes(),
                    'is_expired' => $allocation->isExpired(),
                ];
            });

        return response()->json(['allocations' => $allocations]);
    }
}
