<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\FoodListing;
use App\Models\Redemption;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Show all reports for the shop
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get listing statistics
        $totalListings = FoodListing::where('shop_user_id', $user->id)->count();
        $availableListings = FoodListing::where('shop_user_id', $user->id)
            ->where('status', 'available')
            ->count();
        $redeemedListings = FoodListing::where('shop_user_id', $user->id)
            ->where('status', 'redeemed')
            ->count();
        $expiredListings = FoodListing::where('shop_user_id', $user->id)
            ->where('status', 'expired')
            ->count();
        
        // Get redemption statistics
        $totalRedemptions = Redemption::whereHas('foodListing', function ($q) use ($user) {
            $q->where('shop_user_id', $user->id);
        })->count();
        
        $totalAmountRedeemed = Redemption::whereHas('foodListing', function ($q) use ($user) {
            $q->where('shop_user_id', $user->id);
        })->sum('amount_used');
        
        // Get recent redemptions
        $recentRedemptions = Redemption::whereHas('foodListing', function ($q) use ($user) {
            $q->where('shop_user_id', $user->id);
        })->with(['foodListing', 'voucher'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('shop.reports.index', compact(
            'totalListings',
            'availableListings',
            'redeemedListings',
            'expiredListings',
            'totalRedemptions',
            'totalAmountRedeemed',
            'recentRedemptions'
        ));
    }
}
