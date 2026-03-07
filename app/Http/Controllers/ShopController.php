<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FoodListing;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Show shop details and their food listings
     */
    public function show(User $shop, Request $request)
    {
        // Verify this is a shop user
        if ($shop->role !== 'local_shop') {
            abort(404);
        }

        $type = $request->get('type', 'all');
        $search = $request->get('search', '');
        $sortBy = $request->get('sort', 'newest');

        // Build query for shop's food listings
        $query = FoodListing::where('shop_user_id', $shop->id)
            ->where('is_active', true);

        // Filter by type
        if ($type !== 'all') {
            $query->where('listing_type', $type);
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('discounted_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('discounted_price', 'desc');
                break;
            case 'expiring':
                $query->orderBy('expiry_date', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
        }

        // Paginate results (12 items per page)
        $listings = $query->paginate(12);

        // Get shop profile
        $shopProfile = $shop->organisation_profile;

        // Get statistics
        $totalItems = FoodListing::where('shop_user_id', $shop->id)
            ->where('is_active', true)
            ->count();

        $freeItems = FoodListing::where('shop_user_id', $shop->id)
            ->where('listing_type', 'free')
            ->where('is_active', true)
            ->count();

        $discountedItems = FoodListing::where('shop_user_id', $shop->id)
            ->where('listing_type', 'discounted')
            ->where('is_active', true)
            ->count();

        $surplusItems = FoodListing::where('shop_user_id', $shop->id)
            ->where('listing_type', 'surplus')
            ->where('is_active', true)
            ->count();

        return view('shop.detail', compact(
            'shop',
            'shopProfile',
            'listings',
            'type',
            'search',
            'sortBy',
            'totalItems',
            'freeItems',
            'discountedItems',
            'surplusItems'
        ));
    }

    /**
     * Show list of all shops
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $query = User::where('role', 'local_shop')
            ->where('is_approved', true)
            ->with('organisation_profile');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('organisation_profile', function ($q) use ($search) {
                        $q->where('shop_name', 'like', "%{$search}%");
                    });
            });
        }

        $shops = $query->paginate(12);

        return view('shop.index', compact('shops', 'search'));
    }
}
