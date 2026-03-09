<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: make admin_user_id nullable without requiring doctrine/dbal
        // SQLite: already allows NULL on all columns — no action needed
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE fund_loads MODIFY admin_user_id BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE fund_loads MODIFY admin_user_id BIGINT UNSIGNED NOT NULL');
        }
    }
};
