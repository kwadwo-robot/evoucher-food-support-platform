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
        Schema::table('surplus_allocations', function (Blueprint $table) {
            $table->unsignedBigInteger('school_care_user_id')->nullable()->after('vcfse_user_id');
            $table->foreign('school_care_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surplus_allocations', function (Blueprint $table) {
            $table->dropForeign(['school_care_user_id']);
            $table->dropColumn('school_care_user_id');
        });
    }
};
