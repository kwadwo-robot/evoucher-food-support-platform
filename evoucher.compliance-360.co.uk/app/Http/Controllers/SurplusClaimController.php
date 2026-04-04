<?php

namespace App\Http\Controllers;

use App\Models\SurplusAllocation;
use App\Models\FoodListing;
use App\Models\Redemption;
use App\Models\Notification;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurplusClaimController extends Controller
{
    /**
     * Claim a food item (VCFSE member or School/Care claims a surplus, free, or discounted item)
     */
    public function claim(Request $request, $foodListingId)
    {
        $user = Auth::user();

        // Verify user is VCFSE or School/Care
        if ($user->role !== 'vcfse' && $user->role !== 'school_care') {
            return redirect()->back()->with('error', 'Only VCFSE and School/Care members can claim items');
        }

        // Get the food listing
        $foodListing = FoodListing::find($foodListingId);

        if (!$foodListing) {
            return redirect()->back()->with('error', 'Food item not found');
        }

        // Check if item has quantity available
        if ($foodListing->quantity <= 0) {
            return redirect()->back()->with('error', 'Item is no longer available');
        }

        // For discounted items, check if voucher is provided
        if ($foodListing->listing_type === 'discounted') {
            $voucherId = $request->input('voucher_id');
            
            if (!$voucherId) {
                return redirect()->back()->with('error', 'Please select a voucher to redeem this discounted item');
            }

            // Get the voucher
            $voucher = Voucher::find($voucherId);

            if (!$voucher) {
                return redirect()->back()->with('error', 'Voucher not found');
            }

            // Check if voucher belongs to the user
            if ($voucher->recipient_user_id !== $user->id) {
                return redirect()->back()->with('error', 'This voucher does not belong to you');
            }

            // Check if voucher has sufficient balance
            if ($voucher->remaining_value < $foodListing->discounted_price) {
                return redirect()->back()->with('error', 'Insufficient voucher balance. Required: £' . $foodListing->discounted_price . ', Available: £' . $voucher->remaining_value);
            }

            // Check if voucher is active
            if ($voucher->status !== 'active') {
                return redirect()->back()->with('error', 'This voucher is no longer active');
            }

            // Deduct from voucher balance
            $voucher->decrement('remaining_value', $foodListing->discounted_price);
        }

        // For surplus items, check allocation
        $allocation = null;
        if ($foodListing->listing_type === 'surplus') {
            // Check allocation based on user role
            if ($user->role === 'vcfse') {
                $allocation = SurplusAllocation::where('food_listing_id', $foodListingId)
                    ->where('vcfse_user_id', $user->id)
                    ->where('status', 'pending')
                    ->first();
            } else if ($user->role === 'school_care') {
                $allocation = SurplusAllocation::where('food_listing_id', $foodListingId)
                    ->where('school_care_user_id', $user->id)
                    ->where('status', 'pending')
                    ->first();
            }

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
        }

        // Create redemption record with PENDING status (shop owner needs to confirm)
        $redemptionData = [
            'food_listing_id' => $foodListingId,
            'shop_user_id' => $foodListing->shop_user_id, // Set the shop owner
            'recipient_user_id' => $user->id,
            'redeemed_at' => now(),
            'status' => 'pending', // Changed from 'confirmed' to 'pending'
            'amount_used' => 0, // Default amount for free/surplus items
        ];
        
        // For discounted items, add voucher_id and amount_used
        if ($foodListing->listing_type === 'discounted') {
            $redemptionData['voucher_id'] = $request->input('voucher_id');
            $redemptionData['amount_used'] = $foodListing->discounted_price;
        } else {
            // For free and surplus items, voucher_id is null
            $redemptionData['voucher_id'] = null;
            $redemptionData['amount_used'] = 0;
        }
        
        $redemption = Redemption::create($redemptionData);

        // Update food listing quantity
        $foodListing->decrement('quantity');

        // If quantity reaches 0, mark as redeemed
        if ($foodListing->quantity <= 0) {
            if ($allocation) {
                $allocation->update(['status' => 'redeemed']);
            }
            $foodListing->update(['status' => 'redeemed']);
        }

        // Send notification to the claiming user (School/VCFSE)
        $itemType = $foodListing->listing_type === 'surplus' ? 'Surplus Item' : ($foodListing->listing_type === 'discounted' ? 'Discounted Item' : 'Free Item');
        Notification::create([
            'user_id' => $user->id,
            'type' => 'item_claimed',
            'title' => $itemType . ' Claimed',
            'message' => 'You have successfully claimed: ' . $foodListing->item_name . '. Please collect it from the shop.',
            'icon' => 'fas fa-check-circle',
        ]);

        // Send notification to shop owner about the claim
        Notification::create([
            'user_id' => $foodListing->shop_user_id,
            'type' => 'item_claimed',
            'title' => 'Food Item Claimed',
            'message' => $foodListing->item_name . ' has been claimed by ' . $user->name . ' (' . ucfirst(str_replace('_', ' ', $user->role)) . '). Please confirm when collected.',
            'icon' => 'fas fa-bell',
        ]);

        return redirect()->back()->with('success', 'Item claimed successfully! The shop owner has been notified. Please collect it from the shop.');
    }
}
