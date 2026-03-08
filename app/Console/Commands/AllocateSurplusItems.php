<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FoodListing;
use App\Models\SurplusAllocation;
use App\Models\Notification;

class AllocateSurplusItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:allocate-surplus-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allocate surplus items to School/Care users with 2-hour countdown';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find all surplus items that don't have an active allocation
        $surplusItems = FoodListing::where('listing_type', 'surplus')
            ->where('status', 'available')
            ->where('quantity', '>', 0)
            ->get();

        foreach ($surplusItems as $item) {
            // Check if item already has an active allocation
            $activeAllocation = SurplusAllocation::where('food_listing_id', $item->id)
                ->where('status', 'pending')
                ->first();

            if (!$activeAllocation) {
                // Get next available VCFSE user
                $vcfseUser = SurplusAllocation::getNextVcfseUser($item->id);
                // Get next available School/Care user
                $schoolCareUser = SurplusAllocation::getNextSchoolCareUser($item->id);

                // Allocate to VCFSE user if available
                if ($vcfseUser) {
                    $vcfseAllocation = SurplusAllocation::create([
                        'food_listing_id' => $item->id,
                        'vcfse_user_id' => $vcfseUser->id,
                        'allocated_at' => now(),
                        'expires_at' => now()->addHours(2),
                        'status' => 'pending',
                        'allocation_sequence' => 0,
                    ]);

                    Notification::create([
                        'user_id' => $vcfseUser->id,
                        'type' => 'surplus_allocated',
                        'title' => 'Surplus Food Available',
                        'message' => 'A surplus item has been allocated to you for 2 hours: ' . $item->item_name,
                        'icon' => 'fas fa-hourglass-end',
                        'read_at' => null,
                    ]);

                    $this->info("Allocated {$item->item_name} to VCFSE user {$vcfseUser->name}");
                }

                // Allocate to School/Care user if available
                if ($schoolCareUser) {
                    $schoolAllocation = SurplusAllocation::create([
                        'food_listing_id' => $item->id,
                        'school_care_user_id' => $schoolCareUser->id,
                        'allocated_at' => now(),
                        'expires_at' => now()->addHours(2),
                        'status' => 'pending',
                        'allocation_sequence' => 0,
                    ]);

                    Notification::create([
                        'user_id' => $schoolCareUser->id,
                        'type' => 'surplus_allocated',
                        'title' => 'Surplus Food Available',
                        'message' => 'A surplus item has been allocated to you for 2 hours: ' . $item->item_name,
                        'icon' => 'fas fa-hourglass-end',
                        'read_at' => null,
                    ]);

                    $this->info("Allocated {$item->item_name} to School/Care user {$schoolCareUser->name}");
                }
            }
        }

        $this->info('Surplus items allocation completed.');
    }
}
