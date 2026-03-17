<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\BankDeposit;
use App\Models\FundLoad;
use App\Models\Notification;
use App\Models\OrganisationProfile;
use App\Models\SystemLog;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        // Get recent fund loads for this organisation
        $fundLoads = FundLoad::where('organisation_user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('organisation.fund-load', compact('walletBalance', 'bankDeposits', 'fundLoads', 'user'));
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
            // Use the static StripeService::client() — NOT $this->stripe
            $stripe = StripeService::client();

            if (!$stripe) {
                return response()->json([
                    'error' => 'Payment processing is not configured. Please contact the administrator.',
                ], 503);
            }

            $paymentIntent = $stripe->paymentIntents->retrieve($request->payment_intent_id);

            if ($paymentIntent->status !== 'succeeded') {
                return response()->json(['error' => 'Payment has not succeeded yet. Status: ' . $paymentIntent->status], 400);
            }

            DB::beginTransaction();

            // 1. Update (or create) the organisation wallet balance
            $profile = $user->organisationProfile;
            if ($profile) {
                $profile->increment('wallet_balance', $request->amount);
            } else {
                OrganisationProfile::create([
                    'user_id'        => $user->id,
                    'wallet_balance' => $request->amount,
                ]);
            }

            // 2. Save a FundLoad record so the transaction appears in the admin dashboard.
            // admin_user_id has a NOT NULL constraint on some DB versions, so we use the
            // first available admin account as the "system admin" reference.
            $adminUser = User::whereIn('role', ['admin', 'super_admin'])->first();
            FundLoad::create([
                'organisation_user_id'  => $user->id,
                'admin_user_id'         => $adminUser ? $adminUser->id : $user->id,
                'amount'                => $request->amount,
                'reference'             => 'STRIPE-' . strtoupper(Str::random(8)),
                'stripe_transaction_id' => $paymentIntent->id,
                'payment_method'        => 'stripe',
                'notes'                 => 'Stripe self-load by ' . $user->name . ' (' . $user->email . ')',
            ]);

            // 3. System log
            SystemLog::create([
                'user_id'     => $user->id,
                'action'      => 'fund_load_completed',
                'entity_type' => 'fund_load',
                'entity_id'   => null,
                'description' => "Fund load of £{$request->amount} completed by {$user->name} via Stripe ({$paymentIntent->id})",
                'ip_address'  => $request->ip(),
            ]);

            // 4. Notify all admins
            $admins = User::where('role', 'admin')->orWhere('role', 'super_admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title'   => 'New Fund Load',
                    'message' => "{$user->name} loaded £{$request->amount} via Stripe",
                    'type'    => 'fund_load',
                    'icon'    => 'fas fa-wallet',
                    'link'    => route('admin.load-funds'),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Funds loaded successfully! Your wallet balance has been updated.',
                'amount'  => $request->amount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('FundLoad confirmPayment error: ' . $e->getMessage());
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
