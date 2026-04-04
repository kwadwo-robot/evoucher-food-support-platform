<?php
namespace App\Http\Controllers\Recipient;
use App\Http\Controllers\Controller;
use App\Models\FoodListing;
use App\Models\Redemption;
use App\Models\ShopProfile;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $active_vouchers    = Voucher::where('recipient_user_id', $user->id)
            ->whereIn('status', ['active', 'partially_used'])
            ->where('remaining_value', '>', 0)
            ->where('expiry_date', '>=', now()->toDateString())
            ->get();
        $recent_redemptions = Redemption::where('recipient_user_id', $user->id)->with('foodListing')->latest()->take(3)->get();
        // Recipients see ONLY Discounted listings (NOT Free or Surplus)
        $availableFood      = FoodListing::where('status','available')
            ->where('expiry_date','>=',now()->toDateString())
            ->where('listing_type', 'discounted')
            ->where('quantity', '>', 0)
            ->latest()->take(6)->get();
        $total_voucher_value= $active_vouchers->sum('remaining_value');
        $totalVouchers = Voucher::where('recipient_user_id', $user->id)->count();
        $totalRedemptions = Redemption::where('recipient_user_id', $user->id)->count();
        $availableItems = FoodListing::where('status','available')
            ->where('expiry_date','>=',now()->toDateString())
            ->where('listing_type', 'discounted')
            ->where('quantity', '>', 0)
            ->count();
        return view('recipient.dashboard', compact('active_vouchers','recent_redemptions','availableFood','total_voucher_value','totalVouchers','totalRedemptions','availableItems'));
    }

    public function browse(Request $request)
    {
        // Recipients see ONLY Discounted listings (NOT Free or Surplus)
        $query = FoodListing::where('status','available')
            ->where('expiry_date','>=',now()->toDateString())
            ->where('listing_type', 'discounted')
            ->where('quantity', '>', 0)
            ->with('shop.shopProfile');
        
        // Filter by shop if specified
        if ($request->shop_id) {
            $query->where('shop_user_id', $request->shop_id);
        }

        // Filter by town via shop profile
        if ($request->town) {
            $shopUserIds = ShopProfile::where('town', $request->town)->pluck('user_id');
            $query->whereIn('shop_user_id', $shopUserIds);
        }
        
        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('item_name','like','%'.$request->search.'%')
                    ->orWhere('description','like','%'.$request->search.'%');
            });
        }
        
        if ($request->max_value) $query->where('voucher_value','<=',$request->max_value);
        
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
            ->where('listing_type', 'discounted')
            ->where('quantity', '>', 0)
            ->select('shop_user_id')
            ->distinct()
            ->get()
            ->map(function ($listing) {
                $user = \App\Models\User::find($listing->shop_user_id);
                if (!$user) return null;
                return [
                    'id' => $listing->shop_user_id,
                    'name' => $user->organisation_profile->shop_name ?? $user->name,
                    'count' => FoodListing::where('shop_user_id', $listing->shop_user_id)
                        ->where('status','available')
                        ->where('expiry_date','>=',now()->toDateString())
                        ->where('listing_type', 'discounted')
                        ->where('quantity', '>', 0)
                        ->count()
                ];
            })
            ->filter()
            ->sortBy('name')
            ->values();
        
        return view('recipient.food.browse', compact('listings', 'shops', 'sortBy'));
    }

    public function showListing(FoodListing $listing)
    {
        abort_if($listing->status !== 'available', 404, 'This item is no longer available.');
        // Recipients can only access discounted listings
        abort_if($listing->listing_type !== 'discounted', 403, 'This listing is not available for recipients.');
        $listing->load('shop.shopProfile');
        // Show ALL active/partially_used vouchers with any remaining balance
        // so recipients can use partial vouchers for items they can afford
        $user_vouchers = Voucher::where('recipient_user_id', Auth::id())
            ->whereIn('status', ['active', 'partially_used'])
            ->where('remaining_value', '>', 0)
            ->where('expiry_date', '>=', now()->toDateString())
            ->get();
        return view('recipient.food.show', compact('listing','user_vouchers'));
    }

    public function history()
    {
        $redemptions = Redemption::where('recipient_user_id', Auth::id())
            ->with(['foodListing.shop.shopProfile','voucher'])->latest()->paginate(15);
        return view('recipient.history', compact('redemptions'));
    }

    public function profile()
    {
        $profile = Auth::user()->recipientProfile;
        return view('recipient.profile', compact('profile'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string',
            'postcode'   => 'nullable|string|max:10',
        ]);
        Auth::user()->recipientProfile->update($request->only(['first_name','last_name','phone','address','postcode']));
        return back()->with('success', 'Profile updated.');
    }
}
