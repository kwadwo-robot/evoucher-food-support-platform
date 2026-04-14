<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_fee_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('service_fee_percentage', 5, 2)->default(10.00)->comment('Service fee percentage (e.g., 10.00 for 10%)');
            $table->text('description')->nullable()->comment('Description of the service fee');
            $table->timestamps();
        });

        // Insert default setting
        DB::table('service_fee_settings')->insert([
            'service_fee_percentage' => 10.00,
            'description' => 'Default service fee for shop payouts',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('service_fee_settings');
    }
};
