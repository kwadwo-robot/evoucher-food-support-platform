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
        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations', 'amount')) {
                $table->decimal('amount', 10, 2)->after('id');
            }
            if (!Schema::hasColumn('donations', 'email')) {
                $table->string('email')->after('amount');
            }
            if (!Schema::hasColumn('donations', 'payment_intent_id')) {
                $table->string('payment_intent_id')->nullable()->after('email');
            }
            if (!Schema::hasColumn('donations', 'payment_method_id')) {
                $table->string('payment_method_id')->nullable()->after('payment_intent_id');
            }
            if (!Schema::hasColumn('donations', 'status')) {
                $table->enum('status', ['pending', 'succeeded', 'failed'])->default('pending')->after('payment_method_id');
            }
            if (!Schema::hasColumn('donations', 'metadata')) {
                $table->text('metadata')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumnIfExists(['amount', 'email', 'payment_intent_id', 'payment_method_id', 'status', 'metadata']);
        });
    }
};
