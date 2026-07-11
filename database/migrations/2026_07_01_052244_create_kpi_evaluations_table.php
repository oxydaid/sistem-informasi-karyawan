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
            $table->integer('score')->comment('Skor Rerata (skala 20-100)');

            $table->integer('kehadiran')->default(0);
            $table->text('kehadiran_notes')->nullable();

            $table->integer('keahlian')->default(3);
            $table->text('keahlian_notes')->nullable();

            $table->integer('keaktifan')->default(0);
            $table->text('keaktifan_notes')->nullable();

            $table->integer('kedisiplinan')->default(0);
            $table->text('kedisiplinan_notes')->nullable();

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
