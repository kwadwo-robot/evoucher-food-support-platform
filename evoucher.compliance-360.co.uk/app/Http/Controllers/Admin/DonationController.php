<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\StripeService;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * Display all donations
     */
    public function index(Request $request)
    {
        $query = Donation::query();

        // Search by email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('donor_email', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $donations = $query->paginate(20);

        // Get statistics
        $stats = [
            'total' => Donation::count(),
            'completed' => Donation::where('status', 'completed')->count(),
            'processing' => Donation::where('status', 'processing')->count(),
            'failed' => Donation::where('status', 'failed')->count(),
            'total_amount' => Donation::where('status', 'completed')->sum('amount'),
        ];

        return view('admin.donations.index', compact('donations', 'stats'));
    }

    /**
     * Show donation details
     */
    public function show(Donation $donation)
    {
        return view('admin.donations.show', compact('donation'));
    }

    /**
     * Sync completed Stripe charges into the donations table.
     */
    public function syncFromStripe(Request $request)
    {
        $stripe = StripeService::client();

        if (!$stripe) {
            return redirect()->route('admin.donations.index')
                ->with('error', 'Stripe is not configured. Please set your Stripe keys in Settings.');
        }

        $synced  = 0;
        $skipped = 0;
        $failed  = 0;

        try {
            $charges = $stripe->charges->all(['limit' => 100]);

            foreach ($charges->data as $charge) {
                try {
                    if ($charge->status !== 'succeeded') {
                        $skipped++;
                        continue;
                    }

                    // Skip if already stored
                    $piId = $charge->payment_intent ?? $charge->id;
                    if (Donation::where('stripe_payment_id', $piId)->exists() ||
                        Donation::where('stripe_payment_id', $charge->id)->exists()) {
                        $skipped++;
                        continue;
                    }

                    $email = $charge->billing_details->email
                        ?? $charge->receipt_email
                        ?? ($charge->metadata['email'] ?? null)
                        ?? 'unknown@stripe.com';

                    $amount = $charge->amount / 100;

                    Donation::create([
                        'amount'            => $amount,
                        'donor_email'       => $email,
                        'stripe_payment_id' => $piId,
                        'status'            => 'completed',
                        'currency'          => strtoupper($charge->currency),
                        'notes'             => json_encode([
                            'source'           => 'stripe_sync',
                            'stripe_charge_id' => $charge->id,
                            'description'      => $charge->description,
                            'created'          => $charge->created,
                        ]),
                    ]);

                    $synced++;
                } catch (\Exception $e) {
                    $failed++;
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.donations.index')
                ->with('error', 'Stripe API error: ' . $e->getMessage());
        }

        $message = "Stripe sync complete. Imported: {$synced}, Already existed: {$skipped}";
        if ($failed > 0) {
            $message .= ", Errors: {$failed}";
        }

        return redirect()->route('admin.donations.index')->with('success', $message);
    }
}
