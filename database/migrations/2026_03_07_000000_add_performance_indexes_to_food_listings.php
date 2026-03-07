<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get existing indexes
        $indexes = DB::select("SHOW INDEX FROM food_listings");
        $indexNames = array_column($indexes, 'Key_name');
        
        Schema::table('food_listings', function (Blueprint $table) use ($indexNames) {
            // Index for expiry date sorting (if not exists)
            if (!in_array('food_listings_expiry_date_index', $indexNames)) {
                $table->index('expiry_date');
            }
            
            // Index for creation date sorting (if not exists)
            if (!in_array('food_listings_created_at_index', $indexNames)) {
                $table->index('created_at');
            }
        });
        
        // Add composite index if not exists
        if (!in_array('food_listings_shop_user_id_listing_type_index', $indexNames)) {
            DB::statement('ALTER TABLE food_listings ADD INDEX food_listings_shop_user_id_listing_type_index (shop_user_id, listing_type)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_listings', function (Blueprint $table) {
            $table->dropIndexIfExists(['expiry_date']);
            $table->dropIndexIfExists(['created_at']);
        });
        
        // Drop composite index if exists
        try {
            DB::statement('ALTER TABLE food_listings DROP INDEX IF EXISTS food_listings_shop_user_id_listing_type_index');
        } catch (\Exception $e) {
            // Ignore if index doesn't exist
        }
    }
};
