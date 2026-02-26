<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Tables that need a tenant_id column. */
    private array $tables = [
        'patients',
        'patient_visits',
        'invoices',
        'invoice_items',
        'drugs',
        'drug_types',
        'payment_types',
        'service_groups',
        'role_permissions',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $t) {
                    // Nullable so existing rows keep working without a tenant
                    $t->foreignId('tenant_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('clients')
                      ->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropConstrainedForeignId('tenant_id');
                });
            }
        }
    }
};
