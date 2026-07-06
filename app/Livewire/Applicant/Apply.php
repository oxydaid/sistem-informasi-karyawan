<?php

namespace App\Livewire\Applicant;

use App\Models\Applicant;
use App\Models\Position;
use App\Services\OcrService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class Apply extends Component
{
    use WithFileUploads;

    // Biodata
    public $name = '';

    public $email = '';

    public $phone = '';

    public $nik = '';

    public $position_id = '';

    public $keterangan = '';

    // File Uploads
    public $fileKtp;

    public $fileKk;

    public $fileIjazah;

    public $fileSkck;

    public $filePasFoto;

    public $fileCv;

    public $fileSim;

    public $fileSertifikat;

    public $filePendukung = []; // Array of files

    public $successMessage = false;

    // OCR properties
    public $isScanningKtp = false;

    public $ktpMetadata = [];

    // Tab State
    public $activeTab = 'register'; // register or check_status

    public $checkNik = '';

    public $checkEmail = '';

    public function checkApplicationStatus()
    {
        $this->validate([
            'checkNik' => 'required|string|size:16',
            'checkEmail' => 'required|email',
        ], [
            'checkNik.required' => 'NIK wajib diisi.',
            'checkNik.size' => 'NIK harus berisi 16 digit.',
            'checkEmail.required' => 'Alamat email wajib diisi.',
            'checkEmail.email' => 'Format alamat email tidak valid.',
        ]);

        $applicant = Applicant::where('nik', $this->checkNik)
            ->where('email', $this->checkEmail)
            ->first();

        if ($applicant) {
            return redirect()->route('applicant.onboarding', ['token' => $applicant->nik]);
        }

        session()->flash('error', 'Data pendaftaran tidak ditemukan. Pastikan NIK dan Email yang dimasukkan sudah sesuai.');
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:applicants,email|unique:users,email',
            'phone' => 'required|string|max:20',
            'nik' => 'required|string|size:16|unique:applicants,nik',
            'position_id' => 'required|exists:positions,id',
            'keterangan' => 'required|string|max:500',

            // File validations (max 2MB per file)
            'fileKtp' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'fileKk' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'fileIjazah' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'fileSkck' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'filePasFoto' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'fileCv' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'fileSim' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'fileSertifikat' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'filePendukung.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'name' => 'Nama Lengkap',
            'email' => 'Alamat Email',
            'phone' => 'No. Handphone/WA',
            'nik' => 'NIK (No. KTP)',
            'position_id' => 'Posisi yang Dilamar',
            'keterangan' => 'Keterangan/Kompetensi Tambahan',
            'fileKtp' => 'Berkas KTP',
            'fileKk' => 'Berkas KK',
            'fileIjazah' => 'Ijazah Terakhir',
            'fileSkck' => 'Surat SKCK',
            'filePasFoto' => 'Pas Foto Resmi',
            'fileCv' => 'CV / Resume',
            'fileSim' => 'SIM (Opsional)',
            'fileSertifikat' => 'Sertifikat Keahlian',
            'filePendukung.*' => 'Dokumen Pendukung',
        ];
    }

    /**
     * Hook triggered automatically when the KTP file is uploaded.
     * Uses OcrService to parse details and auto-fill the NIK and Name fields.
     */
    public function updatedFileKtp()
    {
        $this->validate([
            'fileKtp' => 'required|file|image|max:2048', // Validate as image for OCR scanning
        ], [
            'fileKtp.image' => 'Berkas KTP harus berupa gambar (jpg, jpeg, png) untuk dipindai otomatis.',
        ]);

        $this->isScanningKtp = true;

        try {
            $ocrService = new OcrService;
            $parsed = $ocrService->parseKtp($this->fileKtp->getRealPath());

            if (isset($parsed['error'])) {
                $this->dispatch('toast', type: 'error', message: 'Gagal memindai KTP: '.$parsed['error']);
            } else {
                if (! empty($parsed['nik'])) {
                    $this->nik = $parsed['nik'];
                }
                if (! empty($parsed['name'])) {
                    $this->name = $parsed['name'];
                }

                // Store all parsed fields in metadata properties
                $this->ktpMetadata = array_filter([
                    'tempat_lahir' => $parsed['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $parsed['tanggal_lahir'] ?? null,
                    'jenis_kelamin' => $parsed['jenis_kelamin'] ?? null,
                    'alamat' => $parsed['alamat'] ?? null,
                    'agama' => $parsed['agama'] ?? null,
                    'status_kawin' => $parsed['status_kawin'] ?? null,
                    'pekerjaan' => $parsed['pekerjaan'] ?? null,
                    'kewarganegaraan' => $parsed['kewarganegaraan'] ?? null,
                ]);

                $this->dispatch('toast', type: 'success', message: 'KTP berhasil di-scan otomatis! NIK dan Nama Anda telah terisi.');
            }
        } catch (\Exception $e) {
            Log::error('KTP OCR processing exception: '.$e->getMessage());
            $this->dispatch('toast', type: 'error', message: 'Terjadi kesalahan sistem saat memproses KTP.');
        } finally {
            $this->isScanningKtp = false;
        }
    }

    public function submitApplication()
    {
        $this->validate();

        $documents = [];

        // Helper to store file
        $storeFile = function ($file, $key) {
            if ($file) {
                // Ensure name is clean
                $filename = $key.'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs("berkas/{$this->nik}", $filename, 'public');

                return $path;
            }

            return null;
        };

        // Upload mandatory files
        $documents['ktp'] = $storeFile($this->fileKtp, 'ktp');
        $documents['kk'] = $storeFile($this->fileKk, 'kk');
        $documents['ijazah'] = $storeFile($this->fileIjazah, 'ijazah');
        $documents['skck'] = $storeFile($this->fileSkck, 'skck');
        $documents['pas_foto'] = $storeFile($this->filePasFoto, 'pas_foto');
        $documents['cv'] = $storeFile($this->fileCv, 'cv');

        // Upload optional files
        if ($this->fileSim) {
            $documents['sim'] = $storeFile($this->fileSim, 'sim');
        }
        if ($this->fileSertifikat) {
            $documents['sertifikat'] = $storeFile($this->fileSertifikat, 'sertifikat');
        }

        // Upload multiple supporting documents
        if (! empty($this->filePendukung)) {
            $supportingPaths = [];
            foreach ($this->filePendukung as $index => $file) {
                $filename = 'pendukung_'.($index + 1).'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs("berkas/{$this->nik}", $filename, 'public');
                $supportingPaths[] = $path;
            }
            $documents['pendukung'] = $supportingPaths;
        }

        $posisiName = '';
        $pos = Position::find($this->position_id);
        if ($pos) {
            $posisiName = $pos->name;
        }

        // Save to Database
        Applicant::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'nik' => $this->nik,
            'documents' => $documents,
            'status' => 'pending',
            'metadata' => array_merge([
                'applied_at' => now()->toDateTimeString(),
                'ip_address' => request()->ip(),
                'position_id' => $this->position_id,
                'posisi' => $posisiName,
                'keterangan' => $this->keterangan,
            ], $this->ktpMetadata),
        ]);

        $this->successMessage = true;
        $this->reset();
    }

    public function render()
    {
        $positions = Position::with('department')->get();

        return view('livewire.applicant.apply', [
            'positions' => $positions,
        ])->layout('layouts.guest');
    }
}
