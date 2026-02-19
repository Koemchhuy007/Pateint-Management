<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_visits', function (Blueprint $table) {
            $table->enum('visit_type', ['OPD', 'IPD'])->default('OPD')->after('visit_date');
        });
    }

    public function down(): void
    {
        Schema::table('patient_visits', function (Blueprint $table) {
            $table->dropColumn('visit_type');
        });
    }
};
