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
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('donations', 'email')) {
                $table->string('email')->nullable()->after('donor_email');
            }
            if (!Schema::hasColumn('donations', 'payment_intent_id')) {
                $table->string('payment_intent_id')->nullable()->after('stripe_session_id');
            }
            if (!Schema::hasColumn('donations', 'payment_method_id')) {
                $table->string('payment_method_id')->nullable()->after('payment_intent_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumnIfExists(['email', 'payment_intent_id', 'payment_method_id']);
        });
    }
};
