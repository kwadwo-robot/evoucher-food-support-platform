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
        Schema::table('broadcast_deliveries', function (Blueprint $table) {
            if (!Schema::hasColumn('broadcast_deliveries', 'recipient_email')) {
                $table->string('recipient_email')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('broadcast_deliveries', 'recipient_name')) {
                $table->string('recipient_name')->nullable()->after('recipient_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broadcast_deliveries', function (Blueprint $table) {
            if (Schema::hasColumn('broadcast_deliveries', 'recipient_email')) {
                $table->dropColumn('recipient_email');
            }
            if (Schema::hasColumn('broadcast_deliveries', 'recipient_name')) {
                $table->dropColumn('recipient_name');
            }
        });
    }
};
