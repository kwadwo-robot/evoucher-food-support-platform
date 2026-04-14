<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_user_id')->comment('Shop user who paid the fee');
            $table->unsignedBigInteger('payout_request_id')->nullable()->comment('Associated payout request');
            $table->decimal('payout_amount', 10, 2)->comment('Original payout amount before fee');
            $table->decimal('service_fee_percentage', 5, 2)->comment('Service fee percentage applied');
            $table->decimal('service_fee_amount', 10, 2)->comment('Calculated service fee amount');
            $table->decimal('amount_after_fee', 10, 2)->comment('Amount paid to shop after fee deduction');
            $table->enum('status', ['pending', 'collected', 'refunded'])->default('collected')->comment('Fee status');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('shop_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('shop_user_id');
            $table->index('payout_request_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_fees');
    }
};
