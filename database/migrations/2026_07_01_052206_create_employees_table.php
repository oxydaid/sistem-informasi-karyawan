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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->restrictOnDelete();
            $table->string('employee_id_number')->unique(); // NIK Karyawan Internal
            $table->string('nik', 16)->unique(); // NIK KTP
            $table->string('phone');
            $table->text('address')->nullable();
            $table->enum('employment_status', ['magang', 'pkl', 'kontrak', 'tetap', 'freelance']);
            $table->date('join_date');
            $table->integer('leave_quota')->default(12); // Kuota cuti tahunan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
