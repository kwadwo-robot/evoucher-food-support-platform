<?php

namespace App\Console\Commands;

use App\Models\FoodListing;
use Illuminate\Console\Command;

class UpdateTestCheeseCommand extends Command
{
    protected $signature = 'update:test-cheese';
    protected $description = 'Update Test Discounted Cheese item';

    public function handle()
    {
        $item = FoodListing::where('item_name', 'Test Discounted Cheese')->first();
        
        if ($item) {
            $item->listing_type = 'discounted';
            $item->discounted_price = 2.50;
            $item->save();
            $this->info('Updated: ' . $item->item_name);
        } else {
            $this->error('Item not found');
        }
    }
}
