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
        // 1. Add unique index to payrolls (employee_id, month_year)
        Schema::table('payrolls', function (Blueprint $table) {
            $table->unique(['employee_id', 'month_year']);
        });

        // 2. Add unique index to kpi_evaluations (employee_id, month_year)
        Schema::table('kpi_evaluations', function (Blueprint $table) {
            $table->unique(['employee_id', 'month_year']);
        });

        // 3. Add indexes to leave_requests (status, start_date)
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->index(['status', 'start_date']);
        });

        // 4. Add payroll_id relationship and indexes to cash_advances
        Schema::table('cash_advances', function (Blueprint $table) {
            $table->foreignId('payroll_id')
                ->nullable()
                ->after('employee_id')
                ->constrained()
                ->nullOnDelete();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_advances', function (Blueprint $table) {
            $table->dropForeign(['payroll_id']);
            $table->dropColumn('payroll_id');
            $table->dropIndex(['status']);
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropIndex(['status', 'start_date']);
        });

        Schema::table('kpi_evaluations', function (Blueprint $table) {
            $table->dropUnique(['employee_id', 'month_year']);
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropUnique(['employee_id', 'month_year']);
        });
    }
};
