<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_payout_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_user_id');
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->integer('redemption_count')->default(0);
            $table->enum('status', ['pending', 'approved', 'paid', 'rejected'])->default('pending');
            $table->string('payment_reference')->nullable(); // bank transfer reference from admin
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable(); // admin user id
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('shop_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_payout_requests');
    }
};
