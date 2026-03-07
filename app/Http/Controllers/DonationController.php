<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

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
                return response()->json([
                    'success' => true,
                    'message' => 'Thank you for your donation!',
                    'amount' => $validated['amount'],
                    'email' => $validated['email']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing failed. Please try again.'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
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
