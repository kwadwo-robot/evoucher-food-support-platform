<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\FoodListing;
use App\Models\Redemption;
use App\Models\Setting;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'          => User::count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'pending_approvals'    => User::where('is_approved', false)->whereNotIn('role', ['recipient','admin','super_admin'])->count(),
            'total_vouchers'       => Voucher::count(),
            'active_vouchers'      => Voucher::where('status', 'active')->count(),
            'total_food_listings'  => FoodListing::where('status','available')->count(),
            'total_donated'        => Donation::where('status', 'completed')->sum('amount'),
            'total_redemptions'    => Redemption::count(),
            'total_shops'          => User::where('role','local_shop')->where('is_approved',true)->count(),
            'total_donors'         => User::whereIn('role',['vcfse','school_care'])->where('is_approved',true)->count(),
        ];
        $pendingUsers    = User::where('is_approved', false)->whereNotIn('role', ['recipient','admin','super_admin'])->latest()->take(5)->get();
        $recentListings  = FoodListing::with('shop')->latest()->take(5)->get();
        $recentDonations = Donation::with('donor')->latest()->take(5)->get();
        $recentVouchers  = Voucher::with('recipient')->latest()->take(5)->get();
        return view('admin.dashboard', compact('stats','pendingUsers','recentListings','recentDonations','recentVouchers'));
    }

    public function listings()
    {
        $listings = FoodListing::with('shop.shopProfile')->latest()->paginate(20);
        return view('admin.listings.index', compact('listings'));
    }

    public function updateListingStatus(Request $request, $id)
    {
        $listing = FoodListing::findOrFail($id);
        $listing->update(['status' => $request->status]);
        return back()->with('success', 'Listing status updated.');
    }

    public function destroyListing(FoodListing $listing)
    {
        $listing->update(['status' => 'removed']);
        return back()->with('success', 'Listing removed.');
    }

    public function donations()
    {
        $payments        = Donation::with('donor')->latest()->paginate(20);
        $total_received  = Donation::where('status','completed')->sum('amount');
        $completed_count = Donation::where('status','completed')->count();
        $pending_count   = Donation::where('status','pending')->count();
        return view('admin.payments.index', compact('payments','total_received','completed_count','pending_count'));
    }

    public function settings()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::set($key, $value);
        }
        return back()->with('success', 'Settings saved successfully.');
    }
}
