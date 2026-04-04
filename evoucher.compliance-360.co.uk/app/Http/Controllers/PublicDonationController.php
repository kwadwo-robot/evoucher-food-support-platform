<?php

namespace App\Http\Controllers;

use App\Mail\AdminNewDonationMail;
use App\Mail\DonationReceiptMail;
use App\Models\Donation;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PublicDonationController extends Controller
{
    /**
     * Create a payment intent for donation
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
                'email'  => 'required|email'
            ]);

            $stripe = StripeService::client();
            if (!$stripe) {
                return response()->json([
                    'error' => 'Payment processing is not configured. Please contact the administrator.'
                ], 503);
            }

            $amountInCents = intval($validated['amount'] * 100);

            $paymentIntent = $stripe->paymentIntents->create([
                'amount'   => $amountInCents,
                'currency' => 'gbp',
                'automatic_payment_methods' => [
                    'enabled'         => true,
                    'allow_redirects' => 'never'
                ],
                'metadata' => [
                    'email' => $validated['email'],
                    'type'  => 'donation'
                ]
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'amount'       => $validated['amount']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Confirm and store donation, then send emails.
     */
    public function confirm(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount'            => 'required|numeric|min:1',
                'email'             => 'required|email',
                'payment_intent_id' => 'required|string'
            ]);

            $stripe = StripeService::client();
            if (!$stripe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing is not configured. Please contact the administrator.'
                ], 503);
            }

            $paymentIntent = $stripe->paymentIntents->retrieve($validated['payment_intent_id']);

            \Log::info('Payment Intent Retrieved', [
                'id'             => $paymentIntent->id,
                'status'         => $paymentIntent->status,
                'payment_method' => $paymentIntent->payment_method,
            ]);

            if (in_array($paymentIntent->status, ['succeeded', 'processing'])) {
                $donation = Donation::create([
                    'amount'            => $validated['amount'],
                    'donor_email'       => $validated['email'],
                    'stripe_payment_id' => $paymentIntent->id,
                    'status'            => $paymentIntent->status === 'succeeded' ? 'completed' : 'pending',
                    'currency'          => 'GBP',
                    'notes'             => json_encode([
                        'email'          => $validated['email'],
                        'type'           => 'donation',
                        'stripe_status'  => $paymentIntent->status,
                        'payment_method' => $paymentIntent->payment_method
                    ])
                ]);

                // In-app notifications for admins
                $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'title'   => 'New Donation Received',
                        'message' => "Donation of £{$validated['amount']} from {$validated['email']}",
                        'type'    => 'donation',
                        'icon'    => 'gift',
                        'link'    => '/admin/donations',
                    ]);
                }

                try {
                    NotificationService::notifyNewDonation($validated['amount'], $validated['email']);
                } catch (\Exception $notifyError) {
                    // Log but don't fail the donation
                }

                // Send receipt email to donor
                $this->sendDonationEmails($donation);

                return response()->json([
                    'success'     => true,
                    'message'     => 'Thank you for your donation of £' . $validated['amount'] . '! A receipt has been sent to ' . $validated['email'] . '.',
                    'amount'      => $validated['amount'],
                    'email'       => $validated['email'],
                    'donation_id' => $donation->id,
                    'status'      => $paymentIntent->status
                ]);

            } elseif ($paymentIntent->status === 'requires_payment_method') {
                if ($paymentIntent->charges && count($paymentIntent->charges->data) > 0) {
                    $charge = $paymentIntent->charges->data[0];
                    if ($charge->status === 'succeeded') {
                        $donation = Donation::create([
                            'amount'            => $validated['amount'],
                            'donor_email'       => $validated['email'],
                            'stripe_payment_id' => $paymentIntent->id,
                            'status'            => 'completed',
                            'currency'          => 'GBP',
                            'notes'             => json_encode([
                                'email'         => $validated['email'],
                                'type'          => 'donation',
                                'stripe_status' => $paymentIntent->status,
                                'charge_id'     => $charge->id
                            ])
                        ]);

                        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
                        foreach ($admins as $admin) {
                            Notification::create([
                                'user_id' => $admin->id,
                                'title'   => 'New Donation Received',
                                'message' => "Donation of £{$validated['amount']} from {$validated['email']}",
                                'type'    => 'donation',
                                'icon'    => 'gift',
                                'link'    => '/admin/donations',
                            ]);
                        }

                        $this->sendDonationEmails($donation);

                        return response()->json([
                            'success'     => true,
                            'message'     => 'Thank you for your donation of £' . $validated['amount'] . '! A receipt has been sent to ' . $validated['email'] . '.',
                            'amount'      => $validated['amount'],
                            'email'       => $validated['email'],
                            'donation_id' => $donation->id,
                            'status'      => 'completed'
                        ]);
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Payment method not attached. Please try again.'
                ], 400);

            } else {
                $errorMessage = 'Payment not succeeded';
                if ($paymentIntent->last_payment_error) {
                    $errorMessage = $paymentIntent->last_payment_error->message;
                }

                Donation::create([
                    'amount'            => $validated['amount'],
                    'donor_email'       => $validated['email'],
                    'stripe_payment_id' => $paymentIntent->id,
                    'status'            => 'failed',
                    'currency'          => 'GBP',
                    'notes'             => json_encode([
                        'stripe_status' => $paymentIntent->status,
                        'error'         => $errorMessage
                    ])
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing failed. Status: ' . $paymentIntent->status
                ], 400);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', array_merge(...array_values($e->errors())))
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving donation: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Send donation receipt to donor and alert to admin email.
     */
    private function sendDonationEmails(Donation $donation): void
    {
        // Receipt to donor
        try {
            Mail::to($donation->donor_email)->send(new DonationReceiptMail($donation));
        } catch (\Exception $e) {
            \Log::warning('Donation receipt email failed: ' . $e->getMessage());
        }

        // Alert to admin email address
        try {
            $adminEmail = Setting::get('admin_email', config('mail.from.address', 'admin@evoucher.org'));
            Mail::to($adminEmail)->send(new AdminNewDonationMail($donation));
        } catch (\Exception $e) {
            \Log::warning('Admin donation notification email failed: ' . $e->getMessage());
        }
    }
}
