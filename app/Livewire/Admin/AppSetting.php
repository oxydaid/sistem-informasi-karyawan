<?php

namespace App\Livewire\Admin;

use App\Models\AppSetting as AppSettingModel;
use App\Services\WhatsappGatewayService;
use Livewire\Component;
use Livewire\WithFileUploads;

class AppSetting extends Component
{
    use WithFileUploads;

    public $appName = '';

    public $appDescription = '';

    public $primaryColor = '';

    public $secondaryColor = '';

    public $facebookUrl = '';

    public $instagramUrl = '';

    public $linkedinUrl = '';

    public $whatsappUrl = '';

    public $ocrSpaceApiKey = '';

    public $whatsappGatewaySecret = '';

    // New fields
    public $companyName = '';

    public $leaveDeductionAmount = 50000;

    // Live WhatsApp status properties
    public $gatewayStatus = 'disconnected';

    public $gatewayQr = null;

    public $gatewayUser = null;

    public $gatewayMessage = '';

    public $gatewayUrl = '';

    // File uploads
    public $fileFavicon;

    public $fileLogo;

    protected function rules()
    {
        return [
            'appName' => 'required|string|max:50',
            'appDescription' => 'nullable|string',
            'primaryColor' => 'required|string|max:10', // supports hex values
            'secondaryColor' => 'required|string|max:10',
            'facebookUrl' => 'nullable|url',
            'instagramUrl' => 'nullable|url',
            'linkedinUrl' => 'nullable|url',
            'whatsappUrl' => 'nullable|url',
            'ocrSpaceApiKey' => 'nullable|string|max:255',
            'whatsappGatewaySecret' => 'nullable|string|max:255',
            'companyName' => 'required|string|max:100',
            'leaveDeductionAmount' => 'required|numeric|min:0',
            'fileFavicon' => 'nullable|image|max:1024',
            'fileLogo' => 'nullable|image|max:2048',
        ];
    }

    public function mount()
    {
        $settings = AppSettingModel::firstOrCreate([], [
            'app_name' => 'ISP HRIS',
            'primary_color' => '#0ea5e9',
            'secondary_color' => '#334155',
        ]);

        $this->appName = $settings->app_name;
        $this->appDescription = $settings->app_description;
        $this->primaryColor = $settings->primary_color;
        $this->secondaryColor = $settings->secondary_color;
        $this->facebookUrl = $settings->facebook_url;
        $this->instagramUrl = $settings->instagram_url;
        $this->linkedinUrl = $settings->linkedin_url;
        $this->whatsappUrl = $settings->whatsapp_url;
        $this->ocrSpaceApiKey = $settings->ocr_space_api_key;
        $this->whatsappGatewaySecret = $settings->whatsapp_gateway_secret;
        $this->companyName = $settings->company_name ?? 'PT SKYNET INDONESIA';
        $this->leaveDeductionAmount = $settings->leave_deduction_amount ?? 50000;
        $this->gatewayUrl = env('WA_GATEWAY_URL', 'http://localhost:6969');

        $this->updateGatewayStatus();
    }

    /**
     * Poll the WhatsApp gateway for status updates
     */
    public function updateGatewayStatus()
    {
        $gateway = new WhatsappGatewayService;
        $status = $gateway->getStatus();

        if ($status['status'] ?? false) {
            $this->gatewayStatus = $status['connection'] ?? 'disconnected';
            $this->gatewayQr = $status['qr'] ?? null;
            $this->gatewayUser = $status['user'] ?? null;
            $this->gatewayMessage = '';
        } else {
            $this->gatewayStatus = 'disconnected';
            $this->gatewayQr = null;
            $this->gatewayUser = null;
            $this->gatewayMessage = $status['message'] ?? 'Gateway server offline';
        }
    }

    public function saveSettings()
    {
        $this->validate();

        $settings = AppSettingModel::first();

        $data = [
            'app_name' => $this->appName,
            'app_description' => $this->appDescription,
            'primary_color' => $this->primaryColor,
            'secondary_color' => $this->secondaryColor,
            'facebook_url' => $this->facebookUrl,
            'instagram_url' => $this->instagramUrl,
            'linkedin_url' => $this->linkedinUrl,
            'whatsapp_url' => $this->whatsappUrl,
            'ocr_space_api_key' => $this->ocrSpaceApiKey,
            'whatsapp_gateway_secret' => $this->whatsappGatewaySecret,
            'company_name' => $this->companyName,
            'leave_deduction_amount' => $this->leaveDeductionAmount,
        ];

        if ($this->fileFavicon) {
            $data['favicon_path'] = $this->fileFavicon->store('branding', 'public');
        }
        if ($this->fileLogo) {
            $data['logo_path'] = $this->fileLogo->store('branding', 'public');
        }

        $settings->update($data);
        cache()->forget('app_settings');
        session()->flash('success', 'Konfigurasi aplikasi berhasil disimpan!');

        return redirect()->route('admin.settings');
    }

    /**
     * Trigger session connection/QR code generation for WhatsApp
     */
    public function connectWhatsapp()
    {
        $gateway = new WhatsappGatewayService;
        $response = $gateway->connect();

        if ($response['status'] ?? false) {
            $this->dispatch('toast', type: 'success', message: 'Inisialisasi pairing WhatsApp berhasil. Silakan tunggu QR Code muncul.');
        } else {
            $this->dispatch('toast', type: 'error', message: 'Gagal inisialisasi: '.($response['message'] ?? 'server offline'));
        }

        $this->updateGatewayStatus();
    }

    /**
     * Terminate/Logout WhatsApp Gateway connection session
     */
    public function disconnectWhatsapp()
    {
        $gateway = new WhatsappGatewayService;
        $response = $gateway->logout();

        if ($response['status'] ?? false) {
            $this->dispatch('toast', type: 'success', message: 'Sesi WhatsApp berhasil diputuskan!');
        } else {
            $this->dispatch('toast', type: 'error', message: $response['message'] ?? 'Gagal memutuskan sesi WhatsApp.');
        }

        $this->updateGatewayStatus();
    }

    public function render()
    {
        return view('livewire.admin.app-setting')
            ->layout('layouts.app');
    }
}
