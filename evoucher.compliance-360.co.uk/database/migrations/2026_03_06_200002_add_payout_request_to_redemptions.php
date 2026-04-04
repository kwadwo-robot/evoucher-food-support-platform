<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('redemptions', function (Blueprint $table) {
            // Link each redemption to a payout request once submitted
            $table->unsignedBigInteger('payout_request_id')->nullable()->after('notes');
            $table->foreign('payout_request_id')->references('id')->on('shop_payout_requests')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('redemptions', function (Blueprint $table) {
            $table->dropForeign(['payout_request_id']);
            $table->dropColumn('payout_request_id');
        });
    }
};
