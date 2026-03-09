<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\BankDeposit;
use App\Models\SystemLog;
use App\Models\Notification;
use App\Models\OrganisationProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\StripeService;

class FundLoadController extends Controller
{
    public function showLoadForm()
    {
        $user = Auth::user();
        $profile = $user->organisationProfile ?? new OrganisationProfile();
        $walletBalance = (float)($profile->wallet_balance ?? 0);
        
        // Check if organisation has verified bank deposits
        $bankDeposits = BankDeposit::where('organisation_user_id', $user->id)
            ->where('status', 'verified')
            ->latest()
            ->get();

        return view('organisation.fund-load', compact('walletBalance', 'bankDeposits', 'user'));
    }

    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
        ]);

        $user = Auth::user();
        $amountInCents = (int)($request->amount * 100);

        try {
            $stripe = StripeService::client();
            if (!$stripe) {
                return response()->json(['error' => 'Payment processing is not configured. Please contact the administrator.'], 503);
            }
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $amountInCents,
                'currency' => 'gbp',
                'metadata' => [
                    'user_id' => $user->id,
                    'organisation_name' => $user->name,
                    'role' => $user->role,
                ],
            ]);

            SystemLog::create([
                'user_id' => $user->id,
                'action' => 'fund_load_initiated',
                'entity_type' => 'fund_load',
                'entity_id' => null,
                'description' => "Fund load of £{$request->amount} initiated by {$user->name}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'amount' => $request->amount,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();

        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($request->payment_intent_id);

            if ($paymentIntent->status === 'succeeded') {
                // Update wallet balance
                $profile = $user->organisationProfile;
                if ($profile) {
                    $profile->increment('wallet_balance', $request->amount);
                } else {
                    OrganisationProfile::create([
                        'user_id' => $user->id,
                        'wallet_balance' => $request->amount,
                    ]);
                }

                SystemLog::create([
                    'user_id' => $user->id,
                    'action' => 'fund_load_completed',
                    'entity_type' => 'fund_load',
                    'entity_id' => null,
                    'description' => "Fund load of £{$request->amount} completed by {$user->name}",
                    'ip_address' => $request->ip(),
                ]);

                // Create notification for admin
                $admin = User::where('role', 'admin')->first();
                if ($admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'title' => 'New Fund Load',
                        'message' => "{$user->name} loaded £{$request->amount} via Stripe",
                        'type' => 'fund_load',
                        'icon' => 'wallet',
                        'link' => '/admin/load-funds',
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Funds loaded successfully!',
                    'amount' => $request->amount,
                ]);
            }

            return response()->json(['error' => 'Payment failed'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function loadHistory()
    {
        $user = Auth::user();
        $logs = SystemLog::where('user_id', $user->id)
            ->where('action', 'fund_load_completed')
            ->latest()
            ->paginate(15);

        return view('organisation.fund-load-history', compact('logs'));
    }
}
