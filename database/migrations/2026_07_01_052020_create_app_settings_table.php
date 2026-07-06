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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            // SEO & Branding
            $table->string('app_name')->default('ISP HRIS');
            $table->text('app_description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('default_share_image')->nullable();
            $table->string('ga4_measurement_id')->nullable();

            // Theme Colors
            $table->string('primary_color')->default('#0ea5e9'); // Tailwind Sky 500
            $table->string('secondary_color')->default('#334155'); // Tailwind Slate 700

            // Social Media & Contacts
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('whatsapp_url')->nullable();

            // Integrasi
            $table->string('ocr_space_api_key')->nullable();
            $table->string('whatsapp_gateway_secret')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
