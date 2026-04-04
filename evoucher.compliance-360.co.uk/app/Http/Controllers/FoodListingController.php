<?php
namespace App\Http\Controllers;
use App\Models\FoodListing;
use App\Models\User;
use Illuminate\Http\Request;

class FoodListingController extends Controller
{
    public function index(Request $request)
    {
        // Public browse shows Free and Discounted listings only
        // Surplus food is only visible to logged-in VCFSE/School users
        $query = FoodListing::where('status','available')
            ->where('expiry_date','>=',now()->toDateString())
            ->whereIn('listing_type', ['free', 'discounted'])
            ->with('shop.shopProfile');
        
        // Filter by shop if specified
        if ($request->shop_id) {
            $query->where('shop_user_id', $request->shop_id);
        }
        
        // Filter by type
        if ($request->type && $request->type !== 'all') {
            $query->where('listing_type', $request->type);
        }
        
        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('item_name','like','%'.$request->search.'%')
                    ->orWhere('description','like','%'.$request->search.'%');
            });
        }
        
        // Sort
        $sortBy = $request->sort ?? 'newest';
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
                $query->latest();
        }
        
        $listings = $query->paginate(12);
        
        // Get list of shops with available items for filtering
        $shops = FoodListing::where('status','available')
            ->where('expiry_date','>=',now()->toDateString())
            ->whereIn('listing_type', ['free', 'discounted'])
            ->select('shop_user_id')
            ->distinct()
            ->get()
            ->map(function ($listing) {
                $user = User::find($listing->shop_user_id);
                if (!$user) return null;
                return [
                    'id' => $listing->shop_user_id,
                    'name' => $user->organisation_profile->shop_name ?? $user->name,
                    'count' => FoodListing::where('shop_user_id', $listing->shop_user_id)
                        ->where('status','available')
                        ->where('expiry_date','>=',now()->toDateString())
                        ->whereIn('listing_type', ['free', 'discounted'])
                        ->count()
                ];
            })
            ->filter()
            ->sortBy('name')
            ->values();
        
        return view('food.index', compact('listings', 'shops', 'sortBy'));
    }

    public function show($id)
    {
        $listing = FoodListing::with('shop.shopProfile')->findOrFail($id);
        return view('food.show', compact('listing'));
    }
}
