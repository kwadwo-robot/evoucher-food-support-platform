<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('vouchers', function (Blueprint $table) {
            // Make recipient_user_id nullable to allow manual voucher issuance without a user
            $table->unsignedBigInteger('recipient_user_id')->nullable()->change();
        });
    }

    public function down(): void {
        Schema::table('vouchers', function (Blueprint $table) {
            // Revert the change
            $table->unsignedBigInteger('recipient_user_id')->nullable(false)->change();
        });
    }
};
