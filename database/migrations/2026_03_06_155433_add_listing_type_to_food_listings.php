<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('food_listings', function (Blueprint $table) {
            // listing_type: free | discounted | surplus
            // free       = free item, visible to recipients AND vcfse
            // discounted = Food to Go at a discount, visible to recipients ONLY
            // surplus    = Free surplus food for VCFSE collection ONLY
            $table->enum('listing_type', ['free', 'discounted', 'surplus'])
                  ->default('free')
                  ->after('voucher_value');

            // Original price before discount (for discounted items)
            $table->decimal('original_price', 8, 2)->nullable()->after('listing_type');

            // Discounted selling price (for discounted items)
            $table->decimal('discounted_price', 8, 2)->nullable()->after('original_price');
        });
    }

    public function down(): void
    {
        Schema::table('food_listings', function (Blueprint $table) {
            $table->dropColumn(['listing_type', 'original_price', 'discounted_price']);
        });
    }
};
