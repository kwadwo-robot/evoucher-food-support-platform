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
        // Change the status enum to include 'collected'
        Schema::table('redemptions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed', 'collected', 'cancelled'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('redemptions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->change();
        });
    }
};
