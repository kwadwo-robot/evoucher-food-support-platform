<?php

namespace App\Console\Commands;

use App\Models\Donation;
use Illuminate\Console\Command;
use Stripe\StripeClient;

class SyncStripeDonations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-stripe-donations {--limit=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all donations from Stripe to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Stripe donations sync...');

        try {
            // Initialize Stripe client
            $stripe = new StripeClient(config('services.stripe.secret'));
            
            $limit = $this->option('limit');
            $synced = 0;
            $skipped = 0;
            $failed = 0;

            // Fetch all charges from Stripe
            $charges = $stripe->charges->all([
                'limit' => min($limit, 100),
            ]);

            foreach ($charges->data as $charge) {
                try {
                    // Skip if charge is not successful
                    if ($charge->status !== 'succeeded') {
                        $skipped++;
                        continue;
                    }

                    // Check if donation already exists
                    $existingDonation = Donation::where('stripe_payment_id', $charge->id)
                        ->orWhere('payment_intent_id', $charge->payment_intent)
                        ->first();

                    if ($existingDonation) {
                        $skipped++;
                        continue;
                    }

                    // Get customer email
                    $email = $charge->receipt_email ?? 'unknown@example.com';
                    if ($charge->billing_details && $charge->billing_details->email) {
                        $email = $charge->billing_details->email;
                    }

                    // Create donation record
                    $donation = Donation::create([
                        'donor_email' => $email,
                        'email' => $email,
                        'amount' => $charge->amount / 100, // Convert from cents to pounds
                        'currency' => strtoupper($charge->currency),
                        'stripe_payment_id' => $charge->id,
                        'payment_intent_id' => $charge->payment_intent,
                        'payment_method_id' => $charge->payment_method,
                        'status' => 'completed',
                        'notes' => json_encode([
                            'stripe_charge_id' => $charge->id,
                            'stripe_payment_intent_id' => $charge->payment_intent,
                            'created_at' => $charge->created,
                            'description' => $charge->description,
                        ]),
                    ]);

                    $synced++;
                    $this->line("✓ Synced donation #{$donation->id}: {$email} - £{$donation->amount}");

                } catch (\Exception $e) {
                    $failed++;
                    $this->error("✗ Failed to sync donation: {$e->getMessage()}");
                }
            }

            $this->info("\n=== Sync Complete ===");
            $this->info("Synced: {$synced}");
            $this->info("Skipped: {$skipped}");
            $this->info("Failed: {$failed}");

        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
