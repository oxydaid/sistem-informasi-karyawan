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

    public $fileReplacements = [];

    public function updatedFileReplacements($value, $key)
    {
        // Extract array index
        $parts = explode('.', $key);
        $docKey = end($parts);

        $this->validate([
            "fileReplacements.{$docKey}" => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [], [
            "fileReplacements.{$docKey}" => 'Berkas Perbaikan',
        ]);

        try {
            $file = $this->fileReplacements[$docKey];

            // Clean/rename replacement file
            $filename = $docKey.'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs("berkas/{$this->applicant->nik}", $filename, 'public');

            // Update documents array
            $docs = $this->applicant->documents ?? [];
            $docs[$docKey] = $path;

            // Reset verification status
            $metadata = $this->applicant->metadata ?? [];
            $verifiedDocs = $metadata['verified_docs'] ?? [];
            $verifiedDocs[$docKey] = false;
            $metadata['verified_docs'] = $verifiedDocs;

            $this->applicant->update([
                'documents' => $docs,
                'metadata' => $metadata,
            ]);

            $this->applicant->refresh();

            $this->dispatch('toast', type: 'success', message: 'Berkas perbaikan berhasil diunggah! Menunggu verifikasi ulang oleh HRD.');
            $this->reset('fileReplacements');
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal mengunggah berkas perbaikan: '.$e->getMessage());
        }
    }

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
