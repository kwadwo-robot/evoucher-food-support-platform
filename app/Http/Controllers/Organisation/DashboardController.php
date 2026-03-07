<?php
namespace App\Http\Controllers\Organisation;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\FoodListing;
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
        // VCFSE and School/Care see Free listings AND Free Surplus listings
        // They do NOT see Discounted/Food-to-Go listings (those are Recipients only)
        $query = FoodListing::where('status', 'available')
            ->where('expiry_date', '>=', now()->toDateString())
            ->whereIn('listing_type', ['free', 'surplus'])
            ->with('shop.shopProfile');
        if ($request->search) {
            $query->where('item_name', 'like', '%' . $request->search . '%');
        }
        if ($request->type && in_array($request->type, ['free', 'surplus'])) {
            $query->where('listing_type', $request->type);
        }
        $listings = $query->latest()->paginate(12);
        $user     = Auth::user();
        $view     = $user->role === 'vcfse' ? 'vcfse.food' : 'school.food';
        return view($view, compact('listings'));
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
