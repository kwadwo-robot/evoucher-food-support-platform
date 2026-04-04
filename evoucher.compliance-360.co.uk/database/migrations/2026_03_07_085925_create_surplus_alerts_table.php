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
        Schema::create('surplus_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_listing_id')->constrained('food_listings')->onDelete('cascade');
            $table->foreignId('organisation_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('alert_sent_at');
            $table->timestamp('expires_at'); // 2 hours from alert_sent_at
            $table->enum('status', ['pending', 'accepted', 'expired', 'collected'])->default('pending');
            $table->integer('sequence_number'); // Order in which VCFSE members are notified
            $table->timestamps();
            $table->unique(['food_listing_id', 'organisation_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surplus_alerts');
    }
};
