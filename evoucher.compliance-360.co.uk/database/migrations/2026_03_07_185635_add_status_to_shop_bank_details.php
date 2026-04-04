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
        Schema::table('shop_bank_details', function (Blueprint $table) {
            $table->enum('status', ['active', 'pending_approval', 'rejected'])->default('active')->after('bank_reference');
            $table->timestamp('approved_at')->nullable()->after('status');
            $table->text('rejection_reason')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_bank_details', function (Blueprint $table) {
            $table->dropColumn(['status', 'approved_at', 'rejection_reason']);
        });
    }
};
