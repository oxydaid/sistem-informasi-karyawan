<?php

namespace App\Livewire\Admin;

use App\Mail\ApplicantAcceptedMail;
use App\Models\Applicant;
use App\Models\Position;
use App\Services\ContractService;
use App\Services\WhatsappGatewayService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ManageContract extends Component
{
    public $applicantId;

    public $selectedApplicant;

    // Form inputs
    public $positionId = '';

    public $employmentType = '';

    public $startDate = '';

    public $endDate = '';

    public $salary = '';

    public $isGenerating = false;

    protected function rules()
    {
        return [
            'positionId' => 'required|exists:positions,id',
            'employmentType' => 'required|in:magang,pkl,kontrak,tetap,freelance',
            'startDate' => 'required|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
            'salary' => 'required|numeric|min:0',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'positionId' => 'Jabatan',
            'employmentType' => 'Tipe Pekerjaan',
            'startDate' => 'Tanggal Mulai',
            'endDate' => 'Tanggal Berakhir',
            'salary' => 'Gaji Pokok (Rp)',
        ];
    }

    public function mount($applicantId)
    {
        $this->applicantId = $applicantId;
        $this->selectedApplicant = Applicant::findOrFail($applicantId);
        $this->startDate = now()->format('Y-m-d');

        // Auto-select position and default salary if available in metadata
        $metadata = $this->selectedApplicant->metadata ?? [];
        if (! empty($metadata['position_id'])) {
            $this->positionId = $metadata['position_id'];
            $position = Position::find($this->positionId);
            if ($position) {
                $this->salary = $position->base_salary;
            }
        }
    }

    public function updatedPositionId($value)
    {
        if ($value) {
            $position = Position::find($value);
            if ($position) {
                $this->salary = $position->base_salary;
            }
        } else {
            $this->salary = '';
        }
    }

    public function generateContract(ContractService $contractService)
    {
        $this->validate();
        $this->isGenerating = true;

        try {
            // 1. Generate contract draft PDF
            $contract = $contractService->generateDraft(
                $this->selectedApplicant,
                $this->employmentType,
                $this->positionId,
                $this->startDate,
                $this->endDate ?: null,
                $this->salary
            );

            // 2. Update applicant status to accepted
            $this->selectedApplicant->update(['status' => 'accepted']);

            // 3. Send email & WhatsApp notification
            $onboardingUrl = route('applicant.onboarding', ['token' => $this->selectedApplicant->nik]);

            // Email
            try {
                Mail::to($this->selectedApplicant->email)
                    ->send(new ApplicantAcceptedMail($this->selectedApplicant, $onboardingUrl));
            } catch (\Exception $e) {
                Log::error('Failed to send acceptance email: '.$e->getMessage());
            }

            // WhatsApp
            try {
                $waService = new WhatsappGatewayService;
                $message = 'Halo *'.$this->selectedApplicant->name."*,\n\n"
                    ."Selamat! Anda dinyatakan lolos seleksi dan diterima bergabung dengan perusahaan kami.\n\n"
                    ."Silakan lengkapi proses onboarding, periksa draf kontrak kerja (SPK), dan lakukan tanda tangan digital melalui tautan resmi berikut ini:\n"
                    .$onboardingUrl."\n\n"
                    ."Terima kasih,\n"
                    .'ISP HRIS Team';
                $waService->sendMessage($this->selectedApplicant->phone, $message);
            } catch (\Exception $e) {
                Log::error('Failed to send acceptance WhatsApp: '.$e->getMessage());
            }

            session()->flash('success', 'Draf SPK Kontrak Kerja berhasil di-generate secara otomatis! Email & WhatsApp pemberitahuan telah dikirim ke pelamar.');

            return redirect()->route('admin.applicants');

        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal membuat kontrak: '.$e->getMessage());
        } finally {
            $this->isGenerating = false;
        }
    }

    public function render()
    {
        $positions = Position::with('department')->get();

        return view('livewire.admin.manage-contract', [
            'positions' => $positions,
        ])->layout('layouts.app');
    }
}
