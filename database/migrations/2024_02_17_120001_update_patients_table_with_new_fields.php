<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('surname')->nullable()->after('patient_id');
            $table->string('given_name')->nullable()->after('surname');
            $table->string('sex', 20)->nullable()->after('given_name');
            $table->string('personal_status', 30)->nullable()->after('sex');
            $table->foreignId('province_id')->nullable()->after('address')->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->after('province_id')->constrained()->nullOnDelete();
            $table->foreignId('community_id')->nullable()->after('district_id')->constrained()->nullOnDelete();
            $table->foreignId('village_id')->nullable()->after('community_id')->constrained()->nullOnDelete();
        });

        if (Schema::hasColumn('patients', 'first_name')) {
            DB::table('patients')->update([
                'surname' => DB::raw('last_name'),
                'given_name' => DB::raw('first_name'),
                'sex' => DB::raw('gender'),
            ]);
        }

        if (Schema::hasColumn('patients', 'first_name')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropColumn(['first_name', 'last_name', 'gender']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['community_id']);
            $table->dropForeign(['village_id']);
            $table->dropColumn([
                'surname', 'given_name', 'sex', 'personal_status',
                'province_id', 'district_id', 'community_id', 'village_id'
            ]);
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
        });
    }
};
