<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Models\Redemption;
use Illuminate\Http\Request;

class FoodBreakdownController extends Controller
{
    /**
     * Show detailed redemptions breakdown for Admin/Super Admin
     * Shows ALL redemptions in the system broken down by food type
     */
    public function adminBreakdown()
    {
        $user = auth()->user();
        
        // Get all completed redemptions with their food listings
        $allRedemptions = Redemption::where('status', 'completed')
            ->with(['foodListing', 'recipient', 'shop', 'voucher'])
            ->orderByDesc('redeemed_at')
            ->get();

        // Separate redemptions by food type
        $freeRedemptions = $allRedemptions->filter(function ($redemption) {
            return $redemption->foodListing && $redemption->foodListing->listing_type === 'free';
        })->values();

        $discountedRedemptions = $allRedemptions->filter(function ($redemption) {
            return $redemption->foodListing && $redemption->foodListing->listing_type === 'discounted';
        })->values();

        $surplusRedemptions = $allRedemptions->filter(function ($redemption) {
            return $redemption->foodListing && $redemption->foodListing->listing_type === 'surplus';
        })->values();

        // Calculate statistics for each type
        $freeStats = $this->calculateRedemptionStats($freeRedemptions);
        $discountedStats = $this->calculateRedemptionStats($discountedRedemptions);
        $surplusStats = $this->calculateRedemptionStats($surplusRedemptions);

        // Overall stats
        $overallStats = $this->calculateRedemptionStats($allRedemptions);

        return view('admin.food-breakdown', [
            'freeRedeemed' => $freeRedemptions,
            'discountedRedeemed' => $discountedRedemptions,
            'surplusRedeemed' => $surplusRedemptions,
            'freeStats' => $freeStats,
            'discountedStats' => $discountedStats,
            'surplusStats' => $surplusStats,
            'overallStats' => $overallStats,
        ]);
    }

    /**
     * Show detailed redemptions breakdown for School/Care
     * Shows all redemptions in the system
     */
    public function schoolBreakdown()
    {
        $user = auth()->user();
        
        // Get all completed redemptions with their food listings
        $allRedemptions = Redemption::where('status', 'completed')
            ->with(['foodListing', 'recipient', 'shop', 'voucher'])
            ->orderByDesc('redeemed_at')
            ->get();

        // Separate redemptions by food type
        $freeRedemptions = $allRedemptions->filter(function ($redemption) {
            return $redemption->foodListing && $redemption->foodListing->listing_type === 'free';
        })->values();

        $discountedRedemptions = $allRedemptions->filter(function ($redemption) {
            return $redemption->foodListing && $redemption->foodListing->listing_type === 'discounted';
        })->values();

        $surplusRedemptions = $allRedemptions->filter(function ($redemption) {
            return $redemption->foodListing && $redemption->foodListing->listing_type === 'surplus';
        })->values();

        // Calculate statistics for each type
        $freeStats = $this->calculateRedemptionStats($freeRedemptions);
        $discountedStats = $this->calculateRedemptionStats($discountedRedemptions);
        $surplusStats = $this->calculateRedemptionStats($surplusRedemptions);

        // Overall stats
        $overallStats = $this->calculateRedemptionStats($allRedemptions);

        return view('school.food-breakdown', [
            'freeRedeemed' => $freeRedemptions,
            'discountedRedeemed' => $discountedRedemptions,
            'surplusRedeemed' => $surplusRedemptions,
            'freeStats' => $freeStats,
            'discountedStats' => $discountedStats,
            'surplusStats' => $surplusStats,
            'overallStats' => $overallStats,
        ]);
    }

    /**
     * Show detailed redemptions breakdown for VCFSE
     * Shows all redemptions in the system
     */
    public function vcfseBreakdown()
    {
        $user = auth()->user();
        
        // Get all completed redemptions with their food listings
        $allRedemptions = Redemption::where('status', 'completed')
            ->with(['foodListing', 'recipient', 'shop', 'voucher'])
            ->orderByDesc('redeemed_at')
            ->get();

        // Separate redemptions by food type
        $freeRedemptions = $allRedemptions->filter(function ($redemption) {
            return $redemption->foodListing && $redemption->foodListing->listing_type === 'free';
        })->values();

        $discountedRedemptions = $allRedemptions->filter(function ($redemption) {
            return $redemption->foodListing && $redemption->foodListing->listing_type === 'discounted';
        })->values();

        $surplusRedemptions = $allRedemptions->filter(function ($redemption) {
            return $redemption->foodListing && $redemption->foodListing->listing_type === 'surplus';
        })->values();

        // Calculate statistics for each type
        $freeStats = $this->calculateRedemptionStats($freeRedemptions);
        $discountedStats = $this->calculateRedemptionStats($discountedRedemptions);
        $surplusStats = $this->calculateRedemptionStats($surplusRedemptions);

        // Overall stats
        $overallStats = $this->calculateRedemptionStats($allRedemptions);

        return view('vcfse.food-breakdown', [
            'freeRedeemed' => $freeRedemptions,
            'discountedRedeemed' => $discountedRedemptions,
            'surplusRedeemed' => $surplusRedemptions,
            'freeStats' => $freeStats,
            'discountedStats' => $discountedStats,
            'surplusStats' => $surplusStats,
            'overallStats' => $overallStats,
        ]);
    }

    /**
     * Calculate statistics for redemptions
     */
    private function calculateRedemptionStats($redemptions)
    {
        $totalRedemptions = $redemptions->count();
        $totalValue = $redemptions->sum('amount_used');
        $totalOwed = $redemptions->sum('amount_owed_at_shop');
        $totalCollected = $redemptions->filter(function ($r) {
            return $r->payment_collected;
        })->count();

        return [
            'total_redemptions' => $totalRedemptions,
            'total_value' => $totalValue,
            'total_owed' => $totalOwed,
            'total_collected' => $totalCollected,
            'average_value' => $totalRedemptions > 0 ? $totalValue / $totalRedemptions : 0,
        ];
    }
}
