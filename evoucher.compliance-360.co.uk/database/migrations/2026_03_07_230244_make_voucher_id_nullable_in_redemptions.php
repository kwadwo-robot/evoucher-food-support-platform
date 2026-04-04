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
        Schema::table('redemptions', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['voucher_id']);
            // Make voucher_id nullable
            $table->unsignedBigInteger('voucher_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('redemptions', function (Blueprint $table) {
            // Make voucher_id not nullable again
            $table->unsignedBigInteger('voucher_id')->nullable(false)->change();
            // Re-add the foreign key constraint
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('cascade');
        });
    }
};
