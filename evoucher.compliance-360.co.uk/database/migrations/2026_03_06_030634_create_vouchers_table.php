<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->unsignedBigInteger('recipient_user_id');
            $table->unsignedBigInteger('issued_by')->nullable();
            $table->decimal('value', 8, 2);
            $table->decimal('remaining_value', 8, 2);
            $table->enum('status', ['active', 'redeemed', 'expired', 'cancelled'])->default('active');
            $table->date('expiry_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('recipient_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('issued_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['status', 'expiry_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('vouchers'); }
};
