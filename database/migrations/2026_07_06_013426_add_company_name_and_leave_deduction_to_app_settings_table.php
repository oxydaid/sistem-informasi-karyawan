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
        Schema::table('app_settings', function (Blueprint $table) {
            $table->string('company_name')->default('PT SKYNET INDONESIA')->after('app_name');
            $table->decimal('leave_deduction_amount', 15, 2)->default(50000)->after('whatsapp_gateway_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'leave_deduction_amount']);
        });
    }
};
