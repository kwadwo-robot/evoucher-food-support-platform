<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncStripeFundLoads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:stripe-fund-loads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Stripe fund load transactions from system logs to fund_loads table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logs = DB::table('system_logs')
            ->where('action', 'fund_load_completed')
            ->get();

        $synced = 0;
        $skipped = 0;

        foreach ($logs as $log) {
            // Extract amount from description
            if (preg_match('/£([\d.]+)/', $log->description, $matches)) {
                $amount = (float)$matches[1];

                // Check if FundLoad already exists for this log
                $existing = DB::table('fund_loads')
                    ->where('reference', 'STRIPE-' . $log->id)
                    ->first();

                if (!$existing) {
                    // Create FundLoad record
                    DB::table('fund_loads')->insert([
                        'organisation_user_id' => $log->user_id,
                        'admin_user_id' => null,
                        'amount' => $amount,
                        'reference' => 'STRIPE-' . $log->id,
                        'notes' => 'Stripe payment - ' . $log->description,
                        'payment_method' => 'stripe',
                        'created_at' => $log->created_at,
                        'updated_at' => $log->created_at,
                    ]);
                    $synced++;
                    $this->info("Created FundLoad for user {$log->user_id}, amount: £{$amount}");
                } else {
                    $skipped++;
                }
            }
        }

        $this->info("Synced: {$synced}, Skipped: {$skipped}");
    }
}
