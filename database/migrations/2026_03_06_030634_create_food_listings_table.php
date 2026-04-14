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
        Schema::create('food_listings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_user_id');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->integer('quantity')->default(1);
            $table->date('expiry_date');
            $table->decimal('voucher_value', 8, 2)->default(0);
            $table->string('image_url')->nullable();
            $table->enum('status', ['available', 'reserved', 'redeemed', 'expired', 'removed'])->default('available');
            $table->string('collection_address')->nullable();
            $table->string('collection_time')->nullable();
            $table->text('collection_instructions')->nullable();
            $table->timestamps();
            $table->foreign('shop_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['status', 'expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_listings');
    }
};
