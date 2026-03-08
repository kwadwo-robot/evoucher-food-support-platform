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
        Schema::table('fund_loads', function (Blueprint $table) {
            $table->string('stripe_transaction_id')->nullable()->after('reference');
            $table->string('payment_method')->default('bank_deposit')->after('stripe_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fund_loads', function (Blueprint $table) {
            $table->dropColumn(['stripe_transaction_id', 'payment_method']);
        });
    }
};
