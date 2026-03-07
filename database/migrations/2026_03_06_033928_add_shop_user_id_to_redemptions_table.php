<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('redemptions', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_user_id')->nullable()->after('food_listing_id');
            $table->index('shop_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('redemptions', function (Blueprint $table) {
            $table->dropIndex(['shop_user_id']);
            $table->dropColumn('shop_user_id');
        });
    }
};
