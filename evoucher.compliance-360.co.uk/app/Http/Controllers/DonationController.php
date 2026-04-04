<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Donation;
use App\Services\NotificationService;

class DonationController extends Controller
{
    protected $except = ['store', 'createPaymentIntent'];

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
                'email' => 'required|email',
                'payment_method_id' => 'required|string'
            ]);

            // Convert amount to cents
            $amountInCents = intval($validated['amount'] * 100);

            // Create payment intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'gbp',
                'payment_method' => $validated['payment_method_id'],
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
                'metadata' => [
                    'email' => $validated['email'],
                    'type' => 'donation'
                ]
            ]);

            if ($paymentIntent->status === 'succeeded') {
                // Save donation to database
                Donation::create([
                    'amount' => $validated['amount'],
                    'donor_email' => $validated['email'],
                    'email' => $validated['email'],
                    'stripe_payment_id' => $paymentIntent->id,
                    'status' => 'completed',
                    'currency' => 'GBP',
                    'notes' => json_encode($paymentIntent->metadata)
                ]);

                // Notify admins about new donation
                NotificationService::notifyNewDonation($validated['amount'], $validated['email']);

                return response()->json([
                    'success' => true,
                    'message' => 'Thank you for your donation of £' . $validated['amount'] . '!',
                    'amount' => $validated['amount'],
                    'email' => $validated['email']
                ]);
            } else {
                // Save failed donation
                Donation::create([
                    'amount' => $validated['amount'],
                    'donor_email' => $validated['email'],
                    'email' => $validated['email'],
                    'stripe_payment_id' => $paymentIntent->id,
                    'status' => 'failed',
                    'currency' => 'GBP',
                    'notes' => json_encode($paymentIntent->metadata)
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing failed. Please try again.'
                ], 400);
            }
        } catch (\Exception $e) {
            // Save error donation
            try {
                Donation::create([
                    'amount' => $validated['amount'] ?? 0,
                    'donor_email' => $validated['email'] ?? 'unknown',
                    'email' => $validated['email'] ?? 'unknown@example.com',
                    'status' => 'failed',
                    'currency' => 'GBP',
                    'notes' => json_encode(['error' => $e->getMessage()])
                ]);
            } catch (\Exception $dbError) {
                // Log if database save fails
            }

            return response()->json([
                'success' => false,
                'message' => 'Error saving donation: ' . $e->getMessage()
            ], 400);
        }
    }

    public function createPaymentIntent(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
                'email' => 'required|email'
            ]);

            $amountInCents = intval($validated['amount'] * 100);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'gbp',
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
                'metadata' => [
                    'email' => $validated['email'],
                    'type' => 'donation'
                ]
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'amount' => $validated['amount']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
