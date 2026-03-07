<?php
namespace App\Http\Controllers\Recipient;
use App\Http\Controllers\Controller;
use App\Models\FoodListing;
use App\Models\Redemption;
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
        // Recipients see Free and Discounted listings only (not Free Surplus which is VCFSE-only)
        $availableFood      = FoodListing::where('status','available')
            ->where('expiry_date','>=',now()->toDateString())
            ->whereIn('listing_type', ['free', 'discounted'])
            ->latest()->take(6)->get();
        $total_voucher_value= $active_vouchers->sum('remaining_value');
        return view('recipient.dashboard', compact('active_vouchers','recent_redemptions','availableFood','total_voucher_value'));
    }

    public function browse(Request $request)
    {
        // Recipients see Free and Discounted listings only (not Free Surplus which is VCFSE-only)
        $query = FoodListing::where('status','available')
            ->where('expiry_date','>=',now()->toDateString())
            ->whereIn('listing_type', ['free', 'discounted'])
            ->with('shop.shopProfile');
        if ($request->search) $query->where('item_name','like','%'.$request->search.'%');
        if ($request->max_value) $query->where('voucher_value','<=',$request->max_value);
        if ($request->type && in_array($request->type, ['free', 'discounted'])) $query->where('listing_type', $request->type);
        $listings = $query->latest()->paginate(12);
        return view('recipient.food.browse', compact('listings'));
    }

    public function showListing(FoodListing $listing)
    {
        abort_if($listing->status !== 'available', 404, 'This item is no longer available.');
        // Recipients cannot access surplus listings
        abort_if($listing->listing_type === 'surplus', 403, 'This listing is not available for recipients.');
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
