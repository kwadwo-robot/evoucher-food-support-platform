<?php

namespace App\Console\Commands;

use App\Models\Donation;
use App\Services\StripeService;
use Illuminate\Console\Command;

class SyncStripeDonations extends Command
{
    protected $signature = 'stripe:sync-donations {--limit=100 : Max number of charges to fetch from Stripe}';
    protected $description = 'Sync completed Stripe charges/PaymentIntents to the donations table';

    public function handle(): int
    {
        $stripe = StripeService::client();

        if (!$stripe) {
            $this->error('Stripe is not configured. Please set your Stripe keys in Admin → Settings.');
            return self::FAILURE;
        }

        $this->info('Fetching charges from Stripe...');

        $limit   = min((int) $this->option('limit'), 100);
        $synced  = 0;
        $skipped = 0;
        $failed  = 0;

        try {
            $charges = $stripe->charges->all(['limit' => $limit]);

            foreach ($charges->data as $charge) {
                try {
                    // Only import succeeded charges
                    if ($charge->status !== 'succeeded') {
                        $skipped++;
                        continue;
                    }

                    // Skip if already in the database (by charge id or payment intent id)
                    if (
                        Donation::where('stripe_payment_id', $charge->id)->exists() ||
                        ($charge->payment_intent && Donation::where('stripe_payment_id', $charge->payment_intent)->exists())
                    ) {
                        $skipped++;
                        continue;
                    }

                    // Resolve donor email
                    $email = $charge->billing_details->email
                        ?? $charge->receipt_email
                        ?? ($charge->metadata['email'] ?? null)
                        ?? 'unknown@stripe.com';

                    $amount = $charge->amount / 100; // pence → pounds

                    Donation::create([
                        'amount'            => $amount,
                        'donor_email'       => $email,
                        'stripe_payment_id' => $charge->payment_intent ?? $charge->id,
                        'status'            => 'completed',
                        'currency'          => strtoupper($charge->currency),
                        'notes'             => json_encode([
                            'source'           => 'stripe_sync',
                            'stripe_charge_id' => $charge->id,
                            'stripe_pi_id'     => $charge->payment_intent,
                            'description'      => $charge->description,
                            'created'          => $charge->created,
                        ]),
                    ]);

                    $synced++;
                    $this->line("  ✓ Imported: {$charge->id} — £{$amount} from {$email}");

                } catch (\Exception $e) {
                    $failed++;
                    $this->error("  ✗ Failed to sync {$charge->id}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->error('Stripe API error: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info("Done. Synced: {$synced}, Skipped: {$skipped}, Failed: {$failed}");
        return self::SUCCESS;
    }
}
