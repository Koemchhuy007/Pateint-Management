<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default values
        DB::table('system_settings')->insert([
            ['key' => 'app_name',       'value' => 'Patient Management', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'app_description','value' => 'Multi-tenant clinic management system', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'default_locale', 'value' => 'km',                  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'app_timezone',   'value' => 'Asia/Phnom_Penh',     'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
