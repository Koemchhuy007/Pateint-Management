<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_visit_id')->nullable()->constrained('patient_visits')->nullOnDelete();
            $table->string('invoice_number', 20)->unique();
            $table->foreignId('payment_type_id')->constrained()->restrictOnDelete();
            $table->string('ward', 100)->nullable();
            $table->text('remark')->nullable();
            $table->foreignId('cashier_id')->constrained('users')->restrictOnDelete();
            $table->date('invoice_date');
            $table->decimal('money_paid', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
