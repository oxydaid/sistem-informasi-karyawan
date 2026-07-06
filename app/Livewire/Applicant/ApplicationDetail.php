<?php

namespace App\Livewire\Applicant;

use App\Models\Applicant;
use App\Models\Contract;
use Livewire\Component;
use Livewire\WithFileUploads;

class ApplicationDetail extends Component
{
    use WithFileUploads;

    public $token; // Token is NIK

    public $applicant;

    public $contract;

    // File upload
    public $fileSignedContract;

    public function mount($token)
    {
        $this->token = $token;
        $this->applicant = Applicant::where('nik', $token)->firstOrFail();
        $this->contract = Contract::with('position.department')
            ->where('applicant_id', $this->applicant->id)
            ->first();
    }

    public function uploadSignedContract()
    {
        $this->validate([
            'fileSignedContract' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [], [
            'fileSignedContract' => 'Berkas Kontrak Fisik TTD',
        ]);

        if (! $this->contract) {
            session()->flash('error', 'Kontrak belum di-generate oleh HRD.');

            return;
        }

        try {
            $ext = $this->fileSignedContract->getClientOriginalExtension();
            $filename = 'signed_spk_'.time().'.'.$ext;
            $path = $this->fileSignedContract->storeAs("contracts/{$this->applicant->nik}", $filename, 'public');

            $this->contract->update([
                'signed_contract_path' => $path,
                'status' => 'uploaded',
            ]);

            session()->flash('success', 'Berkas kontrak fisik yang ditandatangani berhasil diunggah!');
            $this->contract->refresh();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengunggah berkas kontrak: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.applicant.application-detail')
            ->layout('layouts.guest');
    }
}
