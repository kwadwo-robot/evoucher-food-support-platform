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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->string('email');
            $table->string('payment_intent_id')->nullable();
            $table->string('payment_method_id')->nullable();
            $table->enum('status', ['pending', 'succeeded', 'failed'])->default('pending');
            $table->text('metadata')->nullable();
            $table->timestamps();
            $table->index('email');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
