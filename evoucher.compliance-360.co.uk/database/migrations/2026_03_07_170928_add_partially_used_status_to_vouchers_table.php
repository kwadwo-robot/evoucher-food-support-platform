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
        // Modify the status column to add 'partially_used' option
        DB::statement("ALTER TABLE vouchers MODIFY COLUMN status ENUM('active','partially_used','redeemed','expired','cancelled') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original status enum
        DB::statement("ALTER TABLE vouchers MODIFY COLUMN status ENUM('active','redeemed','expired','cancelled') NOT NULL DEFAULT 'active'");
    }
};
