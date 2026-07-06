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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('month_year', 7); // Format: MM-YYYY
            $table->decimal('base_salary', 15, 2);
            $table->decimal('kpi_bonus', 15, 2)->default(0); // Tarikan otomatis dari kpi_evaluations
            $table->decimal('kpi_deduction', 15, 2)->default(0); // Tarikan otomatis dari kpi_evaluations
            $table->decimal('leave_deduction', 15, 2)->default(0); // Pemotongan jika melebihi kuota
            $table->decimal('net_salary', 15, 2);
            $table->string('payslip_file_path')->nullable(); // Path slip PDF hasil generate
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
