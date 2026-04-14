<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\Type;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food_listings', function (Blueprint $table) {
            $table->enum('status', ['available', 'reserved', 'redeemed', 'expired', 'removed'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('food_listings', function (Blueprint $table) {
            $table->enum('status', ['available', 'reserved', 'redeemed', 'expired'])->change();
        });
    }
};