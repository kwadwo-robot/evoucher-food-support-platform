<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('donor_user_id')->nullable();
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->string('org_name')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('GBP');
            $table->string('stripe_payment_id')->nullable();
            $table->string('stripe_session_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->decimal('vouchers_allocated', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('donor_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }
    public function down(): void { Schema::dropIfExists('donations'); }
};
