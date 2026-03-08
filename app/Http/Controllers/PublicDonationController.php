<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Donation;
use App\Services\NotificationService;

class PublicDonationController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a payment intent for donation
     */
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

    /**
     * Process and store donation
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Donation store request:', $request->all());
            
            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
                'email' => 'required|email',
                'payment_intent_id' => 'required|string'
            ]);
            
            \Log::info('Donation validated:', $validated);

            // Retrieve the payment intent from Stripe
            $paymentIntent = PaymentIntent::retrieve($validated['payment_intent_id']);

            // Check if payment succeeded or is processing
            if ($paymentIntent->status === 'succeeded' || $paymentIntent->status === 'processing') {
                // Save donation to database
                $donation = Donation::create([
                    'amount' => $validated['amount'],
                    'donor_email' => $validated['email'],
                    'stripe_payment_id' => $paymentIntent->id,
                    'payment_intent_id' => $validated['payment_intent_id'],
                    'status' => $paymentIntent->status === 'succeeded' ? 'completed' : 'processing',
                    'currency' => 'GBP',
                    'notes' => json_encode([
                        'email' => $validated['email'],
                        'type' => 'donation',
                        'stripe_status' => $paymentIntent->status,
                        'payment_method' => $paymentIntent->payment_method
                    ])
                ]);

                // Notify admins about new donation
                try {
                    NotificationService::notifyNewDonation($validated['amount'], $validated['email']);
                } catch (\Exception $notifyError) {
                    \Log::error('Failed to send donation notification: ' . $notifyError->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Thank you for your donation of GBP' . $validated['amount'] . '!',
                    'amount' => $validated['amount'],
                    'email' => $validated['email'],
                    'donation_id' => $donation->id,
                    'status' => $paymentIntent->status
                ]);
            } else {
                // Save failed donation
                $errorMessage = 'Payment not succeeded';
                if ($paymentIntent->last_payment_error) {
                    $errorMessage = $paymentIntent->last_payment_error->message;
                }

                Donation::create([
                    'amount' => $validated['amount'],
                    'donor_email' => $validated['email'],
                    'stripe_payment_id' => $paymentIntent->id,
                    'payment_intent_id' => $validated['payment_intent_id'],
                    'status' => 'failed',
                    'currency' => 'GBP',
                    'notes' => json_encode([
                        'stripe_status' => $paymentIntent->status,
                        'error' => $errorMessage
                    ])
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing failed. Status: ' . $paymentIntent->status
                ], 400);
            }
        } catch (\Exception $e) {
            // Save error donation
            try {
                Donation::create([
                    'amount' => $validated['amount'] ?? 0,
                    'donor_email' => $validated['email'] ?? 'unknown',
                    'status' => 'failed',
                    'currency' => 'GBP',
                    'notes' => json_encode(['error' => $e->getMessage()])
                ]);
            } catch (\Exception $dbError) {
                \Log::error('Failed to save donation error: ' . $dbError->getMessage());
            }

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }
}
