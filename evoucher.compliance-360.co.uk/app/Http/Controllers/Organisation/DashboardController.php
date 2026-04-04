<?php
namespace App\Http\Controllers\Organisation;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\FoodListing;
use App\Models\FundLoad;
use App\Models\BankDeposit;
use App\Models\Redemption;
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
        $profile       = $user->organisationProfile;
        $walletBalance = $profile ? (float)$profile->wallet_balance : 0.0;
        
        // For VCFSE: Use Donation model
        // For School/Care: Use Voucher model
        if ($type === 'vcfse') {
            $donations     = Donation::where('donor_user_id', $user->id)->latest()->take(10)->get();
            $totalDonated  = Donation::where('donor_user_id', $user->id)->where('status','completed')->sum('amount');
            $donationCount = Donation::where('donor_user_id', $user->id)->where('status','completed')->count();
            $vouchersFunded= Donation::where('donor_user_id', $user->id)->where('status','completed')->sum('vouchers_allocated');
            $peopleHelped  = (int)$vouchersFunded; // 1 voucher = 1 person helped
            $recentDonations = $donations->take(5);
        } else {
            // School/Care: Get voucher issuance data
            $vouchers = \App\Models\Voucher::where('issued_by', $user->id)->get();
            $totalDonated  = $vouchers->sum('value');
            $donationCount = $vouchers->count();
            $vouchersFunded= $vouchers->count();
            $peopleHelped  = $vouchers->pluck('recipient_user_id')->unique()->count();
            $recentDonations = $vouchers->sortByDesc('created_at')->take(5);
            $donations = $vouchers->sortByDesc('created_at')->take(10);
        }
        
        // Fund Loads Data
        $totalLoaded = FundLoad::where('organisation_user_id', $user->id)
            ->sum('amount');
        
        $fundLoadCount = FundLoad::where('organisation_user_id', $user->id)
            ->count();
        
        $recentTransactions = FundLoad::where('organisation_user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        // Food Claims Data (for VCFSE only)
        $foodClaimsCounted = 0;
        $foodClaimsRedeemed = 0;
        $foodClaimsPaid = 0.0;
        $recentFoodClaims = [];
        
        if ($type === 'vcfse') {
            // Count food claims from vouchers issued by this VCFSE
            $voucherIds = \App\Models\Voucher::where('issued_by', $user->id)->pluck('id');
            
            $foodClaimsCounted = Redemption::whereIn('voucher_id', $voucherIds)
                ->where('status', '!=', 'pending')
                ->count();
            
            $foodClaimsRedeemed = Redemption::whereIn('voucher_id', $voucherIds)
                ->where('status', 'confirmed')
                ->whereNotNull('redeemed_at')
                ->count();
            
            // Get total amount paid through vouchers
            $foodClaimsPaid = (float)Redemption::whereIn('voucher_id', $voucherIds)
                ->where('status', '!=', 'pending')
                ->sum('amount_used');
            
            // Get recent food claims
            $recentFoodClaims = Redemption::whereIn('voucher_id', $voucherIds)
                ->with('foodListing')
                ->latest()
                ->take(5)
                ->get();
        } else {
            // School/Care: Get food claims (both voucher-based and free/surplus items)
            // Count claims for vouchers issued by this school
            $voucherBasedClaims = Redemption::whereIn('voucher_id', 
                \App\Models\Voucher::where('issued_by', $user->id)->pluck('id')
            )->where('status', '!=', 'pending')->count();
            
            // Count free/surplus food items claimed by this school directly
            $freeSuplusClaims = Redemption::where('recipient_user_id', $user->id)
            ->where('status', '!=', 'pending')
            ->count();
            
            $foodClaimsCounted = $voucherBasedClaims + $freeSuplusClaims;
            
            $foodClaimsRedeemed = Redemption::where(function($q) use ($user) {
                $q->whereIn('voucher_id', \App\Models\Voucher::where('issued_by', $user->id)->pluck('id'))
                  ->orWhereIn('food_listing_id', FoodListing::where('shop_user_id', '!=', $user->id)->pluck('id'))
                  ->where('recipient_user_id', $user->id);
            })->where('status', 'confirmed')->whereNotNull('redeemed_at')->count();
            
            $foodClaimsPaid = (float)Redemption::whereIn('voucher_id',
                \App\Models\Voucher::where('issued_by', $user->id)->pluck('id')
            )->where('status', '!=', 'pending')->sum('amount_used');
            
            $recentFoodClaims = Redemption::where(function($q) use ($user) {
                $q->whereIn('voucher_id', \App\Models\Voucher::where('issued_by', $user->id)->pluck('id'))
                  ->orWhere('recipient_user_id', $user->id);
            })->with('foodListing')->latest()->take(5)->get();
        }
        
        $view = $type === 'vcfse' ? 'vcfse.dashboard' : 'school.dashboard';
        return view($view, compact('donations','totalDonated','donationCount','vouchersFunded','profile','walletBalance','peopleHelped','recentDonations','foodClaimsCounted','foodClaimsRedeemed','foodClaimsPaid','recentFoodClaims','totalLoaded','fundLoadCount','recentTransactions'));
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
        $user = Auth::user();
        
        // Apply visibility scopes based on user role
        $query = FoodListing::where('status', 'available')
            ->where('expiry_date', '>=', now()->toDateString())
            ->where('quantity', '>', 0) // Hide out-of-stock items
            ->with('shop.shopProfile');
        
        // Apply role-based visibility scopes
        if ($user->role === 'school_care') {
            $query->visibleToSchoolCare();
        } elseif ($user->role === 'vcfse') {
            $query->visibleToVcfse();
        } else {
            // Default to discounted only for other roles
            $query->where('listing_type', 'discounted');
        }
        
        // For surplus items, only show if user has a valid (non-expired) allocation
        $query->where(function ($q) use ($user) {
            // Show all non-surplus items
            $q->where('listing_type', '!=', 'surplus')
              // For surplus items, only show if user has a valid pending allocation
              ->orWhere(function ($sq) use ($user) {
                  $sq->where('listing_type', 'surplus')
                     ->whereHas('allocations', function ($aq) use ($user) {
                         $aq->where(function ($a) use ($user) {
                             if ($user->role === 'vcfse') {
                                 $a->where('vcfse_user_id', $user->id);
                             } else if ($user->role === 'school_care') {
                                 $a->where('school_care_user_id', $user->id);
                             }
                         })
                         ->where('status', 'pending')
                         ->where('expires_at', '>', now()); // Only valid allocations
                     });
              });
        });
        
        // Filter by shop if specified
        if ($request->shop_id) {
            $query->where('shop_user_id', $request->shop_id);
        }
        
        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('item_name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->description . '%');
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
        $shopsQuery = FoodListing::where('status', 'available')
            ->where('expiry_date', '>=', now()->toDateString())
            ->where('quantity', '>', 0) // Hide out-of-stock items
            ->whereIn('listing_type', ['free', 'discounted', 'surplus']);
        
        // Apply same surplus allocation filter for shops list
        $shopsQuery->where(function ($q) use ($user) {
            $q->where('listing_type', '!=', 'surplus')
              ->orWhere(function ($sq) use ($user) {
                  $sq->where('listing_type', 'surplus')
                     ->whereHas('allocations', function ($aq) use ($user) {
                         $aq->where(function ($a) use ($user) {
                             if ($user->role === 'vcfse') {
                                 $a->where('vcfse_user_id', $user->id);
                             } else if ($user->role === 'school_care') {
                                 $a->where('school_care_user_id', $user->id);
                             }
                         })
                         ->where('status', 'pending')
                         ->where('expires_at', '>', now());
                     });
              });
        });
        
        $shops = $shopsQuery->select('shop_user_id')
            ->distinct()
            ->with('shop.shopProfile')
            ->get()
            ->pluck('shop');
        
        $view = Auth::user()->role === 'vcfse' ? 'vcfse.food' : 'school.food';
        return view($view, compact('listings', 'shops'));
    }

    public function reports()
    {
        $user = Auth::user();
        $view = $user->role === 'vcfse' ? 'vcfse.reports' : 'school.reports';
        return view($view);
    }

    public function profile()
    {
        $profile = Auth::user()->organisationProfile;
        return view('organisation.profile', compact('profile'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'org_name'       => 'required|string|max:200',
            'contact_person' => 'nullable|string|max:150',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string',
            'postcode'       => 'nullable|string|max:10',
            'website'        => 'nullable|url|max:255',
        ]);

        Auth::user()->organisationProfile->update($request->only([
            'org_name', 'contact_person', 'phone', 'address', 'postcode', 'website',
        ]));

        return back()->with('success', 'Profile updated successfully.');
    }
}
