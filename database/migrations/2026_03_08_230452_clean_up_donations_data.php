<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete all incomplete donations (missing email or amount)
        try {
            DB::table('donations')
                ->whereNull('email')
                ->orWhereNull('amount')
                ->orWhere('amount', 0)
                ->delete();
        } catch (\Exception $e) {
            // Column may not exist yet
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse this destructive operation
    }
};
