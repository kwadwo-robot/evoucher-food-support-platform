<?php
namespace App\Http\Controllers\Shop;
use App\Http\Controllers\Controller;
use App\Models\FoodListing;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FoodListingController extends Controller
{
    public function index()
    {
        $listings = FoodListing::where('shop_user_id', Auth::id())->latest()->paginate(15);
        return view('shop.listings.index', compact('listings'));
    }

    public function create()
    {
        return view('shop.listings.create');
    }

    public function store(Request $request)
    {
        $listingType = $request->input('listing_type', 'free');

        $rules = [
            'item_name'               => 'required|string|max:200',
            'description'             => 'nullable|string',
            'quantity'                => 'required|integer|min:1',
            'expiry_date'             => 'required|date|after_or_equal:today',
            'image'                   => 'nullable|image|max:2048',
            'collection_address'      => 'nullable|string',
            'collection_instructions' => 'nullable|string',
            'collection_time'         => 'nullable|string|max:100',
            'listing_type'            => 'required|in:free,discounted,surplus',
            'voucher_value'           => 'nullable|numeric|min:0',
        ];
        if ($listingType === 'discounted') {
            $rules['original_price']   = 'required|numeric|min:0.01';
            $rules['discounted_price'] = 'required|numeric|min:0.01';
        }
        $request->validate($rules);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path     = $request->file('image')->store('food-images', 'public');
            $imageUrl = Storage::url($path);
        }

        $listing = FoodListing::create([
            'shop_user_id'            => Auth::id(),
            'item_name'               => $request->item_name,
            'description'             => $request->description,
            'quantity'                => $request->quantity,
            'expiry_date'             => $request->expiry_date,
            'image_url'               => $imageUrl,
            'collection_address'      => $request->collection_address ?? (Auth::user()->shopProfile->address ?? ''),
            'collection_instructions' => $request->collection_instructions,
            'collection_time'         => $request->collection_time,
            'voucher_value'           => $listingType === 'discounted' ? $request->discounted_price : ($listingType === 'surplus' ? 0 : ($request->voucher_value ?? 0)),
            'listing_type'            => $listingType,
            'original_price'          => $listingType === 'discounted' ? $request->original_price : null,
            'discounted_price'        => $listingType === 'discounted' ? $request->discounted_price : null,
            'status'                  => 'available',
        ]);

        // Notify VCFSE/School about surplus food
        if ($listingType === 'surplus') {
            NotificationService::notifySurplusFoodAlert($listing);
        }

        return redirect()->route('shop.listings.index')->with('success', 'Food listing created successfully.');
    }

    public function edit(FoodListing $listing)
    {
        abort_if($listing->shop_user_id !== Auth::id(), 403);
        return view('shop.listings.edit', compact('listing'));
    }

    public function update(Request $request, FoodListing $listing)
    {
        abort_if($listing->shop_user_id !== Auth::id(), 403);
        $listingType = $request->input('listing_type', $listing->listing_type ?? 'free');
        $rules = [
            'item_name'               => 'required|string|max:200',
            'quantity'                => 'required|integer|min:0',
            'expiry_date'             => 'required|date',
            'listing_type'            => 'required|in:free,discounted,surplus',
            'voucher_value'           => 'nullable|numeric|min:0',
        ];
        if ($listingType === 'discounted') {
            $rules['original_price']   = 'required|numeric|min:0.01';
            $rules['discounted_price'] = 'required|numeric|min:0.01';
        }
        $request->validate($rules);
        $data = $request->only(['item_name','description','quantity','expiry_date','collection_address','collection_instructions','collection_time','listing_type']);
        $data['voucher_value']    = $listingType === 'discounted' ? $request->discounted_price : ($listingType === 'surplus' ? 0 : ($request->voucher_value ?? 0));
        $data['original_price']   = $listingType === 'discounted' ? $request->original_price : null;
        $data['discounted_price'] = $listingType === 'discounted' ? $request->discounted_price : null;
        if ($request->hasFile('image')) {
            $path              = $request->file('image')->store('food-images', 'public');
            $data['image_url'] = Storage::url($path);
        }
        $listing->update($data);
        return redirect()->route('shop.listings.index')->with('success', 'Listing updated.');
    }

    public function destroy(FoodListing $listing)
    {
        abort_if($listing->shop_user_id !== Auth::id(), 403);
        $listing->update(['status' => 'removed']);
        return back()->with('success', 'Listing removed.');
    }

    public function markCollected(FoodListing $listing)
    {
        abort_if($listing->shop_user_id !== Auth::id(), 403);
        $listing->update(['status' => 'redeemed']);
        return back()->with('success', 'Marked as collected.');
    }
}
