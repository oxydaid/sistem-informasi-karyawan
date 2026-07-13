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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('sender_name');
            $table->date('date');
            $table->enum('status', ['hadir', 'izin', 'cuti', 'alpha', 'libur'])->default('hadir');
            $table->string('message_id')->nullable()->unique();
            $table->string('photo_url')->nullable();
            $table->text('caption')->nullable();
            $table->timestamps();

            // Seseorang hanya punya 1 record absensi per hari
            $table->unique(['sender_name', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
