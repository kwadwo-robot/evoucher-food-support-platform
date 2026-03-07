<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundLoad;
use App\Models\OrganisationProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FundLoadController extends Controller
{
    public function index(Request $request)
    {
        // Get all VCFSE and School/Care organisations with their profiles
        $organisations = User::whereIn('role', ['vcfse', 'school_care'])
            ->where('is_approved', true)
            ->with('organisationProfile')
            ->get();

        // Recent fund loads
        $fundLoads = FundLoad::with(['organisation.organisationProfile', 'admin'])
            ->latest()
            ->paginate(20);

        // Summary stats
        $totalLoaded = FundLoad::sum('amount');
        $totalWalletBalance = OrganisationProfile::sum('wallet_balance');
        $totalOrgs = $organisations->count();

        return view('admin.fund-loads.index', compact(
            'organisations', 'fundLoads', 'totalLoaded', 'totalWalletBalance', 'totalOrgs'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'organisation_user_id' => 'required|exists:users,id',
            'amount'               => 'required|numeric|min:1|max:10000',
            'notes'                => 'nullable|string|max:500',
        ]);

        $user = User::whereIn('role', ['vcfse', 'school_care'])
            ->findOrFail($request->organisation_user_id);

        $profile = $user->organisationProfile;
        if (!$profile) {
            return back()->with('error', 'Organisation profile not found.');
        }

        // Generate a reference number
        $reference = 'FL-' . strtoupper(Str::random(8));

        // Create the fund load record
        FundLoad::create([
            'organisation_user_id' => $user->id,
            'admin_user_id'        => auth()->id(),
            'amount'               => $request->amount,
            'notes'                => $request->notes,
            'reference'            => $reference,
        ]);

        // Add to organisation wallet balance
        $profile->increment('wallet_balance', $request->amount);

        return back()->with('success', "£{$request->amount} successfully loaded to {$profile->org_name}. Reference: {$reference}");
    }

    public function destroy($id)
    {
        $fundLoad = FundLoad::findOrFail($id);

        // Deduct from wallet (don't go below zero)
        $profile = $fundLoad->organisation->organisationProfile;
        if ($profile) {
            $newBalance = max(0, $profile->wallet_balance - $fundLoad->amount);
            $profile->update(['wallet_balance' => $newBalance]);
        }

        $fundLoad->delete();

        return back()->with('success', 'Fund load reversed and wallet balance adjusted.');
    }
}
