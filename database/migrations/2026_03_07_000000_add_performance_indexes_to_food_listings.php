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
        $driver = DB::getDriverName();
        $indexNames = [];

        if ($driver === 'mysql') {
            $indexes = DB::select("SHOW INDEX FROM food_listings");
            $indexNames = array_column($indexes, 'Key_name');
        }

        Schema::table('food_listings', function (Blueprint $table) use ($indexNames) {
            if (!in_array('food_listings_expiry_date_index', $indexNames)) {
                try { $table->index('expiry_date'); } catch (\Exception $e) {}
            }
            if (!in_array('food_listings_created_at_index', $indexNames)) {
                try { $table->index('created_at'); } catch (\Exception $e) {}
            }
        });

        if (!in_array('food_listings_shop_user_id_listing_type_index', $indexNames)) {
            if ($driver === 'mysql') {
                try { DB::statement('ALTER TABLE food_listings ADD INDEX food_listings_shop_user_id_listing_type_index (shop_user_id, listing_type)'); } catch (\Exception $e) {}
            } else {
                try { DB::statement('CREATE INDEX IF NOT EXISTS food_listings_shop_user_id_listing_type_index ON food_listings (shop_user_id, listing_type)'); } catch (\Exception $e) {}
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_listings', function (Blueprint $table) {
            try { $table->dropIndex(['expiry_date']); } catch (\Exception $e) {}
            try { $table->dropIndex(['created_at']); } catch (\Exception $e) {}
        });
        
        // Drop composite index if exists
        try {
            DB::statement('ALTER TABLE food_listings DROP INDEX IF EXISTS food_listings_shop_user_id_listing_type_index');
        } catch (\Exception $e) {
            // Ignore if index doesn't exist
        }
    }
};
