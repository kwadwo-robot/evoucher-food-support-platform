<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('shop_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('shop_name');
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('town', 100)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->text('opening_hours')->nullable();
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    public function down(): void { Schema::dropIfExists('shop_profiles'); }
};
