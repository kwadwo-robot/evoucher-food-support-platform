<?php
namespace App\Http\Controllers\Organisation;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class DonationController extends Controller
{
    public function showForm()
    {
        return view('organisation.donate');
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:5|max:10000',
            'notes'  => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $amount = (float) $request->amount;

        Stripe::setApiKey(config('services.stripe.secret'));

        $donation = Donation::create([
            'donor_user_id' => $user->id,
            'donor_name'    => $user->name,
            'donor_email'   => $user->email,
            'org_name'      => $user->organisationProfile->org_name ?? $user->name,
            'amount'        => $amount,
            'currency'      => 'GBP',
            'status'        => 'pending',
            'notes'         => $request->notes,
        ]);

        $role = $user->role;
        $successRoute = $role === 'vcfse' ? route('vcfse.donate.success') : route('school.donate.success');
        $cancelRoute  = $role === 'vcfse' ? route('vcfse.donate.cancel')  : route('school.donate.cancel');

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'gbp',
                    'unit_amount'  => (int)($amount * 100),
                    'product_data' => ['name' => 'eVoucher Food Support Donation'],
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => $successRoute . '?session_id={CHECKOUT_SESSION_ID}&donation_id=' . $donation->id,
            'cancel_url'  => $cancelRoute . '?donation_id=' . $donation->id,
            'metadata'    => ['donation_id' => $donation->id],
        ]);

        $donation->update(['stripe_session_id' => $session->id]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $donation = Donation::find($request->donation_id);
        if ($donation && $donation->status === 'pending') {
            $donation->update(['status' => 'completed']);
        }
        return view('organisation.donate-success', compact('donation'));
    }

    public function cancel(Request $request)
    {
        $donation = Donation::find($request->donation_id);
        if ($donation) $donation->update(['status' => 'failed']);
        return view('organisation.donate-cancel');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            return response('Webhook error', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $donationId = $session->metadata->donation_id ?? null;
            if ($donationId) {
                Donation::where('id', $donationId)->update([
                    'status'             => 'completed',
                    'stripe_payment_id'  => $session->payment_intent,
                    'stripe_session_id'  => $session->id,
                ]);
            }
        }

        return response('OK', 200);
    }
}
