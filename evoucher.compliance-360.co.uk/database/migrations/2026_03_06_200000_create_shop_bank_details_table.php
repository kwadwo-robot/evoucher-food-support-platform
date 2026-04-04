<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_bank_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_user_id')->unique();
            $table->string('account_holder_name');
            $table->string('bank_name');
            $table->string('sort_code', 8); // e.g. 12-34-56
            $table->string('account_number', 8);
            $table->string('bank_reference')->nullable(); // optional reference for payments
            $table->timestamps();

            $table->foreign('shop_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_bank_details');
    }
};
