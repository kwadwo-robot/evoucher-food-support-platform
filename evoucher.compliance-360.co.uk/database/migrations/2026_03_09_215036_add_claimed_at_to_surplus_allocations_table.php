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
        // Only add the column if it doesn't already exist
        // (the create migration was updated to include it for fresh installs)
        if (Schema::hasTable('surplus_allocations') && !Schema::hasColumn('surplus_allocations', 'claimed_at')) {
            Schema::table('surplus_allocations', function (Blueprint $table) {
                $table->timestamp('claimed_at')->nullable()->after('allocation_sequence');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('surplus_allocations', 'claimed_at')) {
            Schema::table('surplus_allocations', function (Blueprint $table) {
                $table->dropColumn('claimed_at');
            });
        }
    }
};
