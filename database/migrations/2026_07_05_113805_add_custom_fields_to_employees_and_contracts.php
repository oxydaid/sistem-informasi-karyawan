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
        Schema::table('employees', function (Blueprint $table) {
            $table->json('documents')->nullable()->after('address');
            $table->json('metadata')->nullable()->after('documents');
            $table->decimal('base_salary', 15, 2)->nullable()->after('metadata');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->string('signed_contract_path')->nullable()->after('contract_file_path');
            $table->string('status')->default('draft')->after('signed_contract_path'); // 'draft', 'uploaded', 'approved'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['documents', 'metadata', 'base_salary']);
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['signed_contract_path', 'status']);
        });
    }
};
