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
            $table->decimal('amount_owed_at_shop', 8, 2)->nullable()->default(0);
            $table->boolean('payment_collected')->nullable()->default(false);
            $table->string('payment_method')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('redemptions', function (Blueprint $table) {
            $table->dropColumn(['amount_owed_at_shop', 'payment_collected', 'payment_method']);
        });
    }
};
