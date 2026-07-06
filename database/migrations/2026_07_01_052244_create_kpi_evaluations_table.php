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
        Schema::create('kpi_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users'); // Atasan yang menilai
            $table->string('month_year', 7); // Format: MM-YYYY
            $table->integer('score')->comment('Skala 1-100');
            $table->decimal('bonus_adjustment', 15, 2)->default(0); // Nominal bonus
            $table->decimal('deduction_adjustment', 15, 2)->default(0); // Nominal pemotongan jika performa buruk
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_evaluations');
    }
};
