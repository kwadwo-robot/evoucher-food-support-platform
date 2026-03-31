<?php

namespace App\Console\Commands;

use App\Models\ShopPayoutRequest;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class ResendPayoutNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payout:resend-notification {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resend a payout notification to the shop';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $payout = ShopPayoutRequest::find($id);

        if (!$payout) {
            $this->error("Payout with ID {$id} not found.");
            return 1;
        }

        try {
            NotificationService::notifyShopPayoutProcessed($payout);
            $this->info("Notification sent successfully for payout ID: {$id}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error sending notification: " . $e->getMessage());
            return 1;
        }
    }
}
