<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_fee_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payout_request_id');
            $table->unsignedBigInteger('shop_user_id');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('service_fee_percentage', 5, 2);
            $table->decimal('service_fee_amount', 10, 2);
            $table->decimal('amount_after_fee', 10, 2);
            $table->enum('status', ['pending', 'collected', 'refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('payout_request_id')->references('id')->on('shop_payout_requests')->onDelete('cascade');
            $table->foreign('shop_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_fee_transactions');
    }
};
