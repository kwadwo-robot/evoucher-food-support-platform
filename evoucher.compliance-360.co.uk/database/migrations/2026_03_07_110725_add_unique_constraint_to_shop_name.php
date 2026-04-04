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
        Schema::table('shop_profiles', function (Blueprint $table) {
            // Add unique constraint to shop_name if it doesn't exist
            if (!Schema::hasColumn('shop_profiles', 'shop_name')) {
                $table->string('shop_name')->unique();
            } else {
                // If column exists, add unique index
                try {
                    $table->unique('shop_name');
                } catch (\Exception $e) {
                    // Index might already exist
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_profiles', function (Blueprint $table) {
            // Drop the unique constraint
            try {
                $table->dropUnique(['shop_name']);
            } catch (\Exception $e) {
                // Index might not exist
            }
        });
    }
};
