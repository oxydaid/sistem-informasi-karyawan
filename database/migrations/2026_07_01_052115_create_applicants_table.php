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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('nik', 16)->unique();
            $table->json('documents')->nullable(); // Simpan path KTP, KK, Ijazah, dll secara dinamis
            $table->json('metadata')->nullable(); // Data tambahan pelamar datannya diambil dari OCR KTP pada fielnd json('documents'), bisa diisi manual
            $table->enum('status', ['pending', 'reviewed', 'interviewing', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
