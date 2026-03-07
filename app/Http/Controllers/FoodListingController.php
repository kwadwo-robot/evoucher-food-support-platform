<?php
namespace App\Http\Controllers;
use App\Models\FoodListing;
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
        if ($request->search) $query->where('item_name','like','%'.$request->search.'%');
        $listings = $query->latest()->paginate(12);
        return view('food.index', compact('listings'));
    }

    public function show($id)
    {
        $listing = FoodListing::with('shop.shopProfile')->findOrFail($id);
        return view('food.show', compact('listing'));
    }
}
