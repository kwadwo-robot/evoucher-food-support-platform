<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_payout_requests', function (Blueprint $table) {
            $table->decimal('service_fee_percentage', 5, 2)->default(10.00)->after('total_amount')->comment('Service fee percentage (e.g., 10.00 for 10%)');
            $table->decimal('service_fee_amount', 10, 2)->default(0.00)->after('service_fee_percentage')->comment('Calculated service fee amount');
            $table->decimal('amount_after_fee', 10, 2)->default(0.00)->after('service_fee_amount')->comment('Amount paid to shop after service fee deduction');
        });
    }

    public function down(): void
    {
        Schema::table('shop_payout_requests', function (Blueprint $table) {
            $table->dropColumn(['service_fee_percentage', 'service_fee_amount', 'amount_after_fee']);
        });
    }
};
