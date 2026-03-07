<?php
namespace App\Http\Controllers\Organisation;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\FoodListing;
use App\Models\FundLoad;
use App\Models\BankDeposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function vcfseDashboard()
    {
        return $this->orgDashboard('vcfse');
    }

    public function schoolDashboard()
    {
        return $this->orgDashboard('school_care');
    }

    private function orgDashboard(string $type)
    {
        $user          = Auth::user();
        $donations     = Donation::where('donor_user_id', $user->id)->latest()->take(10)->get();
        $totalDonated  = Donation::where('donor_user_id', $user->id)->where('status','completed')->sum('amount');
        $donationCount = Donation::where('donor_user_id', $user->id)->where('status','completed')->count();
        $vouchersFunded= Donation::where('donor_user_id', $user->id)->where('status','completed')->sum('vouchers_allocated');
        $profile       = $user->organisationProfile;
        $walletBalance = $profile ? (float)$profile->wallet_balance : 0.0;
        $peopleHelped  = (int)$vouchersFunded; // 1 voucher = 1 person helped
        $recentDonations = $donations->take(5);
        $view = $type === 'vcfse' ? 'vcfse.dashboard' : 'school.dashboard';
        return view($view, compact('donations','totalDonated','donationCount','vouchersFunded','profile','walletBalance','peopleHelped','recentDonations'));
    }

    public function donations()
    {
        $user          = Auth::user();
        $donations     = Donation::where('donor_user_id', $user->id)->latest()->paginate(15);
        $totalDonated  = Donation::where('donor_user_id', $user->id)->where('status','completed')->sum('amount');
        $donationCount = Donation::where('donor_user_id', $user->id)->where('status','completed')->count();
        $vouchersFunded= Donation::where('donor_user_id', $user->id)->where('status','completed')->sum('vouchers_allocated');
        $view = $user->role === 'vcfse' ? 'vcfse.donations' : 'school.donations';
        return view($view, compact('donations','totalDonated','donationCount','vouchersFunded'));
    }

    public function browseFood(Request $request)
    {
        // VCFSE and School/Care see Free, Discounted, and Surplus listings
        $query = FoodListing::where('status', 'available')
            ->where('expiry_date', '>=', now()->toDateString())
            ->whereIn('listing_type', ['free', 'discounted', 'surplus'])
            ->with('shop.shopProfile');
        
        // Filter by shop if specified
        if ($request->shop_id) {
            $query->where('shop_user_id', $request->shop_id);
        }
        
        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('item_name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->type && in_array($request->type, ['free', 'discounted', 'surplus'])) {
            $query->where('listing_type', $request->type);
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
        $shops = FoodListing::where('status', 'available')
            ->where('expiry_date', '>=', now()->toDateString())
            ->whereIn('listing_type', ['free', 'discounted', 'surplus'])
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
                        ->where('status', 'available')
                        ->where('expiry_date', '>=', now()->toDateString())
                        ->whereIn('listing_type', ['free', 'discounted', 'surplus'])
                        ->count()
                ];
            })
            ->filter()
            ->sortBy('name')
            ->values();
        
        $user = Auth::user();
        $view = $user->role === 'vcfse' ? 'vcfse.food' : 'school.food';
        return view($view, compact('listings', 'shops', 'sortBy'));
    }

    public function profile()
    {
        $profile = Auth::user()->organisationProfile;
        return view('organisation.profile', compact('profile'));
    }

    public function updateProfile(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'org_name'       => 'required|string|max:200',
            'contact_person' => 'nullable|string|max:150',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string',
            'postcode'       => 'nullable|string|max:10',
            'website'        => 'nullable|url|max:300',
        ]);
        Auth::user()->organisationProfile->update($request->only(['org_name','contact_person','phone','address','postcode','website']));
        return back()->with('success', 'Profile updated.');
    }
}
