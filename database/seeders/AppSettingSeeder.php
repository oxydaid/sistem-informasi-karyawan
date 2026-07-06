<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppSetting::firstOrCreate([], [
            'app_name' => 'Skynet',
            'company_name' => 'PT SKYNET',
            'app_description' => 'Sistem Informasi Manajemen Karyawan Skynet',
            'primary_color' => '#0ea5e9',
            'secondary_color' => '#334155',
            'facebook_url' => 'https://facebook.com',
            'instagram_url' => 'https://instagram.com',
            'linkedin_url' => 'https://linkedin.com',
            'whatsapp_url' => 'https://wa.me/628123456789',
            'ocr_space_api_key' => '', // default ocr space free key
            'whatsapp_gateway_secret' => 'wa-gateway',
            'leave_deduction_amount' => 50000,
        ]);
    }
}
