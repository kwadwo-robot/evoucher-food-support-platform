<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankDeposit;
use App\Models\Notification;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankDepositController extends Controller
{
    public function index(Request $request)
    {
        $query = BankDeposit::with('organisation', 'verifiedBy');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('reference', 'like', '%' . $request->search . '%')
                ->orWhereHas('organisation', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
        }

        $deposits = $query->latest()->paginate(15);
        $statuses = ['pending', 'verified', 'rejected'];

        return view('admin.bank-deposits.index', compact('deposits', 'statuses'));
    }

    public function show(BankDeposit $deposit)
    {
        $deposit->load('organisation', 'verifiedBy');
        return view('admin.bank-deposits.show', compact('deposit'));
    }

    public function verify(Request $request, BankDeposit $deposit)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        if ($deposit->status !== 'pending') {
            return back()->with('error', 'This deposit has already been processed.');
        }

        $deposit->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'notes' => $request->notes,
        ]);

        // Get the organisation user and their profile
        $organisationUser = $deposit->organisation;
        $organisationProfile = $organisationUser->organisationProfile;

        if ($organisationProfile) {
            // Create FundLoad record
            \App\Models\FundLoad::create([
                'organisation_user_id' => $organisationUser->id,
                'amount' => $deposit->amount,
                'reference' => $deposit->reference,
                'notes' => "Bank deposit from {$organisationProfile->name}",
            ]);

            // Update organisation profile wallet balance
            $organisationProfile->wallet_balance = ($organisationProfile->wallet_balance ?? 0) + $deposit->amount;
            $organisationProfile->save();

            // Create notification for the organisation user
            Notification::create([
                'user_id' => $organisationUser->id,
                'type' => 'fund_loaded',
                'title' => 'Funds Loaded Successfully',
                'message' => 'Your bank deposit of £' . number_format($deposit->amount, 2) . ' has been verified and funds have been loaded to your wallet.',
                'data' => json_encode([
                    'bank_deposit_id' => $deposit->id,
                    'amount' => $deposit->amount,
                    'reference' => $deposit->reference,
                    'new_balance' => $organisationProfile->wallet_balance,
                ]),
                'is_read' => false,
            ]);

            SystemLog::log('bank_deposit_verified', 'bank_deposit', $deposit->id, "Bank deposit of £{$deposit->amount} from {$organisationProfile->name} verified and funds loaded");
            return back()->with('success', 'Bank deposit verified and funds auto-loaded successfully.');
        }

        return back()->with('error', 'Organisation profile not found.');
    }

    public function reject(Request $request, BankDeposit $deposit)
    {
        $request->validate([
            'notes' => 'required|string',
        ]);

        if ($deposit->status !== 'pending') {
            return back()->with('error', 'This deposit has already been processed.');
        }

        $deposit->update([
            'status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'notes' => $request->notes,
        ]);

        SystemLog::log('bank_deposit_rejected', 'bank_deposit', $deposit->id, "Bank deposit of £{$deposit->amount} from {$deposit->organisation->name} rejected: {$request->notes}");

        return back()->with('success', 'Bank deposit rejected.');
    }
}
