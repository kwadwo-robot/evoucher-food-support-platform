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
        Schema::create('bank_detail_change_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_user_id');
            $table->unsignedBigInteger('bank_detail_id');
            $table->string('account_holder_name');
            $table->string('bank_name');
            $table->string('sort_code', 8);
            $table->string('account_number', 8);
            $table->string('bank_reference');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            
            $table->foreign('shop_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('bank_detail_id')->references('id')->on('shop_bank_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_detail_change_requests');
    }
};
