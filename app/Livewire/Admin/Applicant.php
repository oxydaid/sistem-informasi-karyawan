<?php

namespace App\Livewire\Admin;

use App\Mail\ApplicantAcceptedMail;
use App\Models\Applicant as ApplicantModel;
use App\Models\Position;
use App\Services\OcrService;
use App\Services\WhatsappGatewayService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use ZipArchive;

class Applicant extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    // Modal Preview State
    public $showPreviewModal = false;

    public $showCreateModal = false;

    public $showDeleteModal = false;

    public $selectedApplicant = null;

    public $deletingApplicantId = null;

    public $activeDocUrl = null;

    // Editable Form Fields
    public $editName = '';

    public $editNik = '';

    public $editEmail = '';

    public $editPhone = '';

    public $editPositionId = '';

    public $editKeterangan = '';

    // OCR KTP Fields (Data KTP)
    public $editTempatLahir = '';

    public $editTanggalLahir = '';

    public $editJenisKelamin = '';

    public $editAgama = '';

    public $editStatusKawin = '';

    public $editPekerjaan = '';

    public $editKewarganegaraan = 'WNI';

    public $editAlamat = '';

    // Interview Scheduling Modal State
    public $showInterviewModal = false;

    public $interviewApplicantId = null;

    public $interviewDate = '';

    public $interviewTime = '';

    public $interviewLocation = '';

    public $interviewNotes = '';

    // File Upload Fields (for Edit/Create in Admin)
    public $fileKtp;

    public $fileKk;

    public $fileIjazah;

    public $fileSkck;

    public $filePasFoto;

    public $fileCv;

    public $fileSim;

    public $fileSertifikat;

    public $filePendukung = [];

    protected $queryString = ['search', 'filterStatus'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function selectApplicant($id)
    {
        $this->reset(['fileKtp', 'fileKk', 'fileIjazah', 'fileSkck', 'filePasFoto', 'fileCv', 'fileSim', 'fileSertifikat', 'filePendukung']);
        $this->selectedApplicant = ApplicantModel::findOrFail($id);
        $this->showPreviewModal = true;

        // Fill form fields
        $this->editName = $this->selectedApplicant->name;
        $this->editNik = $this->selectedApplicant->nik;
        $this->editEmail = $this->selectedApplicant->email;
        $this->editPhone = $this->selectedApplicant->phone;

        $metadata = $this->selectedApplicant->metadata ?? [];
        $this->editPositionId = $metadata['position_id'] ?? '';
        $this->editKeterangan = $metadata['keterangan'] ?? '';
        $this->editTempatLahir = $metadata['tempat_lahir'] ?? '';
        $this->editTanggalLahir = $metadata['tanggal_lahir'] ?? '';
        $this->editJenisKelamin = $metadata['jenis_kelamin'] ?? '';
        $this->editAgama = $metadata['agama'] ?? '';
        $this->editStatusKawin = $metadata['status_kawin'] ?? '';
        $this->editPekerjaan = $metadata['pekerjaan'] ?? '';
        $this->editKewarganegaraan = $metadata['kewarganegaraan'] ?? 'WNI';
        $this->editAlamat = $metadata['alamat'] ?? '';

        // Set default preview to KTP or first available document
        if (is_array($this->selectedApplicant->documents) && ! empty($this->selectedApplicant->documents)) {
            $this->activeDocUrl = $this->selectedApplicant->documents['ktp']
                ?? reset($this->selectedApplicant->documents);

            // If it's the supporting documents array, get the first one
            if (is_array($this->activeDocUrl)) {
                $this->activeDocUrl = reset($this->activeDocUrl);
            }
        } else {
            $this->activeDocUrl = null;
        }
    }

    public function previewFile($path)
    {
        $this->activeDocUrl = $path;
    }

    public function toggleDocVerification($docKey)
    {
        if (! $this->selectedApplicant) {
            return;
        }

        $metadata = $this->selectedApplicant->metadata ?? [];
        $verifiedDocs = $metadata['verified_docs'] ?? [];

        $verifiedDocs[$docKey] = ! ($verifiedDocs[$docKey] ?? false);
        $metadata['verified_docs'] = $verifiedDocs;

        $this->selectedApplicant->update(['metadata' => $metadata]);
        $this->selectedApplicant->refresh();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->selectedApplicant = null;
        $this->activeDocUrl = null;
        $this->reset(['editName', 'editNik', 'editEmail', 'editPhone', 'editPositionId', 'editKeterangan', 'editTempatLahir', 'editTanggalLahir', 'editJenisKelamin', 'editAgama', 'editStatusKawin', 'editPekerjaan', 'editAlamat', 'fileKtp', 'fileKk', 'fileIjazah', 'fileSkck', 'filePasFoto', 'fileCv', 'fileSim', 'fileSertifikat', 'filePendukung']);
        $this->editKewarganegaraan = 'WNI';
        $this->showCreateModal = true;
    }

    public function saveApplicantData()
    {
        $id = $this->selectedApplicant ? $this->selectedApplicant->id : null;

        $rules = [
            'editName' => 'required|string|max:100',
            'editNik' => 'required|string|size:16|unique:applicants,nik,'.$id,
            'editEmail' => 'required|email|unique:applicants,email,'.$id,
            'editPhone' => 'required|string|max:20',
            'editPositionId' => 'required|exists:positions,id',
            'editKeterangan' => 'nullable|string',
            'editTempatLahir' => 'nullable|string',
            'editTanggalLahir' => 'nullable|date',
            'editJenisKelamin' => 'nullable|string',
            'editAgama' => 'nullable|string',
            'editStatusKawin' => 'nullable|string',
            'editPekerjaan' => 'nullable|string',
            'editKewarganegaraan' => 'nullable|string',
            'editAlamat' => 'nullable|string',

            // File uploads validation (2MB max)
            'fileKtp' => 'nullable|file|image|max:2048',
            'fileKk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'fileIjazah' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'fileSkck' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'filePasFoto' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'fileCv' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'fileSim' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'fileSertifikat' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'filePendukung.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        $this->validate($rules, [], [
            'editName' => 'Nama Lengkap',
            'editNik' => 'NIK',
            'editEmail' => 'Email',
            'editPhone' => 'No. WhatsApp',
            'editPositionId' => 'Posisi Dilamar',
            'fileKtp' => 'Berkas KTP',
            'fileKk' => 'Berkas KK',
            'fileIjazah' => 'Berkas Ijazah',
            'fileSkck' => 'Berkas SKCK',
            'filePasFoto' => 'Pas Foto',
            'fileCv' => 'Berkas CV',
        ]);

        try {
            $metadata = $this->selectedApplicant ? ($this->selectedApplicant->metadata ?? []) : [];
            $metadata['position_id'] = $this->editPositionId;
            $metadata['keterangan'] = $this->editKeterangan;
            $metadata['tempat_lahir'] = $this->editTempatLahir;
            $metadata['tanggal_lahir'] = $this->editTanggalLahir;
            $metadata['jenis_kelamin'] = $this->editJenisKelamin;
            $metadata['agama'] = $this->editAgama;
            $metadata['status_kawin'] = $this->editStatusKawin;
            $metadata['pekerjaan'] = $this->editPekerjaan;
            $metadata['kewarganegaraan'] = $this->editKewarganegaraan;
            $metadata['alamat'] = $this->editAlamat;

            if ($this->editPositionId) {
                $pos = Position::find($this->editPositionId);
                if ($pos) {
                    $metadata['posisi'] = $pos->name;
                }
            } else {
                unset($metadata['posisi']);
            }

            $documents = $this->selectedApplicant ? ($this->selectedApplicant->documents ?? []) : [];

            $storeFile = function ($file, $key) {
                if ($file) {
                    $filename = $key.'.'.$file->getClientOriginalExtension();
                    $path = $file->storeAs("berkas/{$this->editNik}", $filename, 'public');

                    return $path;
                }

                return null;
            };

            if ($this->fileKtp) {
                $documents['ktp'] = $storeFile($this->fileKtp, 'ktp');
            }
            if ($this->fileKk) {
                $documents['kk'] = $storeFile($this->fileKk, 'kk');
            }
            if ($this->fileIjazah) {
                $documents['ijazah'] = $storeFile($this->fileIjazah, 'ijazah');
            }
            if ($this->fileSkck) {
                $documents['skck'] = $storeFile($this->fileSkck, 'skck');
            }
            if ($this->filePasFoto) {
                $documents['pas_foto'] = $storeFile($this->filePasFoto, 'pas_foto');
            }
            if ($this->fileCv) {
                $documents['cv'] = $storeFile($this->fileCv, 'cv');
            }
            if ($this->fileSim) {
                $documents['sim'] = $storeFile($this->fileSim, 'sim');
            }
            if ($this->fileSertifikat) {
                $documents['sertifikat'] = $storeFile($this->fileSertifikat, 'sertifikat');
            }

            if (! empty($this->filePendukung)) {
                $supportingPaths = $documents['pendukung'] ?? [];
                foreach ($this->filePendukung as $index => $file) {
                    $filename = 'pendukung_'.(count($supportingPaths) + 1).'.'.$file->getClientOriginalExtension();
                    $path = $file->storeAs("berkas/{$this->editNik}", $filename, 'public');
                    $supportingPaths[] = $path;
                }
                $documents['pendukung'] = $supportingPaths;
            }

            if ($this->selectedApplicant) {
                // Update
                $this->selectedApplicant->update([
                    'name' => $this->editName,
                    'nik' => $this->editNik,
                    'email' => $this->editEmail,
                    'phone' => $this->editPhone,
                    'documents' => $documents,
                    'metadata' => $metadata,
                ]);
                $this->selectedApplicant->refresh();
                $this->showPreviewModal = false;
                $this->reset(['fileKtp', 'fileKk', 'fileIjazah', 'fileSkck', 'filePasFoto', 'fileCv', 'fileSim', 'fileSertifikat', 'filePendukung']);
                $this->dispatch('toast', type: 'success', message: 'Data pelamar berhasil diperbarui!');
            } else {
                // Create
                $metadata['applied_at'] = now()->toDateTimeString();
                ApplicantModel::create([
                    'name' => $this->editName,
                    'nik' => $this->editNik,
                    'email' => $this->editEmail,
                    'phone' => $this->editPhone,
                    'status' => 'pending',
                    'documents' => $documents,
                    'metadata' => $metadata,
                ]);
                $this->showCreateModal = false;
                $this->reset(['fileKtp', 'fileKk', 'fileIjazah', 'fileSkck', 'filePasFoto', 'fileCv', 'fileSim', 'fileSertifikat', 'filePendukung']);
                $this->dispatch('toast', type: 'success', message: 'Pelamar baru berhasil ditambahkan secara manual!');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal memproses data pelamar: '.$e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->deletingApplicantId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteApplicant()
    {
        if ($this->deletingApplicantId) {
            $applicant = ApplicantModel::findOrFail($this->deletingApplicantId);

            $folderPath = "berkas/{$applicant->nik}";
            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }

            $applicant->contracts()->delete();
            $applicant->delete();

            $this->dispatch('toast', type: 'success', message: 'Data pelamar berhasil dihapus secara permanen!');
        }
        $this->showDeleteModal = false;
        $this->deletingApplicantId = null;
    }

    public function closePreviewModal()
    {
        $this->showPreviewModal = false;
        $this->selectedApplicant = null;
        $this->activeDocUrl = null;
        $this->reset(['editName', 'editNik', 'editEmail', 'editPhone', 'editPositionId', 'editKeterangan', 'editTempatLahir', 'editTanggalLahir', 'editJenisKelamin', 'editAgama', 'editStatusKawin', 'editPekerjaan', 'editKewarganegaraan', 'editAlamat']);
    }

    public function updateStatus($id, $newStatus)
    {
        $applicant = ApplicantModel::findOrFail($id);

        // If status is accepted, redirect to manage contract page
        if ($newStatus === 'accepted') {
            return redirect()->route('admin.manage-contract', ['applicantId' => $id]);
        }

        $applicant->update(['status' => $newStatus]);
        $this->dispatch('toast', type: 'success', message: "Status pelamar {$applicant->name} berhasil diubah menjadi: {$newStatus}");

        if ($this->selectedApplicant && $this->selectedApplicant->id === $id) {
            $this->selectedApplicant = $applicant;
        }
    }

    public function downloadZip($nik)
    {
        $applicant = ApplicantModel::where('nik', $nik)->firstOrFail();

        $zipFileName = "berkas_{$nik}.zip";
        $tempDir = storage_path('app/public/temp');
        if (! file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zipPath = "{$tempDir}/{$zipFileName}";

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $folderPath = "berkas/{$nik}";
            $files = Storage::disk('public')->files($folderPath);

            if (empty($files)) {
                $zip->close();
                $this->dispatch('toast', type: 'error', message: "Tidak ada berkas yang ditemukan untuk NIK {$nik}.");

                return;
            }

            foreach ($files as $file) {
                $absolutePath = Storage::disk('public')->path($file);
                $zip->addFile($absolutePath, basename($file));
            }

            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } else {
            $this->dispatch('toast', type: 'error', message: 'Gagal membuat file ZIP.');
        }
    }

    public function sendAcceptanceNotification($id)
    {
        $applicant = ApplicantModel::findOrFail($id);

        if ($applicant->status !== 'accepted') {
            $this->dispatch('toast', type: 'error', message: 'Hanya pelamar dengan status diterima yang dapat dikirimkan notifikasi.');

            return;
        }

        $onboardingUrl = route('applicant.onboarding', ['token' => $applicant->nik]);

        // Email
        try {
            Mail::to($applicant->email)
                ->send(new ApplicantAcceptedMail($applicant, $onboardingUrl));
        } catch (\Exception $e) {
            Log::error('Failed to send acceptance email: '.$e->getMessage());
        }

        // WhatsApp
        try {
            $waService = new WhatsappGatewayService;
            $message = 'Halo *'.$applicant->name."*,\n\n"
                ."Selamat! Anda dinyatakan lolos seleksi dan diterima bergabung dengan perusahaan kami.\n\n"
                ."Silakan lengkapi proses onboarding, periksa draf kontrak kerja (SPK), dan lakukan tanda tangan digital melalui tautan resmi berikut ini:\n"
                .$onboardingUrl."\n\n"
                ."Terima kasih,\n"
                .'ISP HRIS Team';
            $waResponse = $waService->sendMessage($applicant->phone, $message);

            if ($waResponse['status'] ?? false) {
                $this->dispatch('toast', type: 'success', message: 'Notifikasi kelulusan via Email dan WhatsApp berhasil dikirim ke '.$applicant->name.'!');
            } else {
                $this->dispatch('toast', type: 'error', message: 'Notifikasi Email terkirim, tetapi WhatsApp gagal: '.($waResponse['message'] ?? 'Gateway Offline'));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send acceptance WhatsApp: '.$e->getMessage());
            $this->dispatch('toast', type: 'error', message: 'Notifikasi Email terkirim, tetapi WhatsApp mengalami kesalahan.');
        }
    }

    public function openInterviewModal($id)
    {
        $applicant = ApplicantModel::findOrFail($id);
        $this->interviewApplicantId = $id;
        $this->interviewDate = date('Y-m-d', strtotime('+1 day'));
        $this->interviewTime = '10:00';
        $this->interviewLocation = 'Kantor Pusat PT SKYNET';
        $this->interviewNotes = 'Mohon hadir 15 menit sebelum jadwal dimulai dan mengenakan pakaian rapi.';
        $this->showInterviewModal = true;
    }

    public function sendInterviewInvitation()
    {
        $this->validate([
            'interviewDate' => 'required|date',
            'interviewTime' => 'required',
            'interviewLocation' => 'required|string|max:255',
            'interviewNotes' => 'nullable|string|max:500',
        ], [], [
            'interviewDate' => 'Tanggal Interview',
            'interviewTime' => 'Jam Interview',
            'interviewLocation' => 'Lokasi/Tautan Interview',
            'interviewNotes' => 'Catatan Tambahan',
        ]);

        $applicant = ApplicantModel::findOrFail($this->interviewApplicantId);

        // Update status to interviewing
        $applicant->update(['status' => 'interviewing']);
        if ($this->selectedApplicant && $this->selectedApplicant->id === $applicant->id) {
            $this->selectedApplicant = $applicant;
        }

        // Send WhatsApp
        try {
            $waService = new WhatsappGatewayService;

            $formattedDate = Carbon::parse($this->interviewDate)->translatedFormat('l, d F Y');

            $message = "Halo *{$applicant->name}*,\n\n"
                ."Terima kasih telah melamar di perusahaan kami. Berkas lamaran Anda telah ditinjau dan Anda dinyatakan lolos untuk mengikuti tahap wawancara (interview).\n\n"
                ."Berikut detail jadwal wawancara Anda:\n"
                ."• Hari/Tanggal: {$formattedDate}\n"
                ."• Waktu: {$this->interviewTime} WIB\n"
                ."• Tempat/Link: {$this->interviewLocation}\n";

            if (! empty($this->interviewNotes)) {
                $message .= "• Catatan: {$this->interviewNotes}\n";
            }

            $message .= "\nMohon konfirmasi kehadiran Anda dengan membalas pesan ini.\n\n"
                ."Terima kasih,\n"
                .'Tim HRD PT SKYNET';

            $waResponse = $waService->sendMessage($applicant->phone, $message);

            if ($waResponse['status'] ?? false) {
                $this->dispatch('toast', type: 'success', message: 'Undangan interview berhasil dikirim via WhatsApp ke '.$applicant->name.'!');
            } else {
                $this->dispatch('toast', type: 'warning', message: 'Status diubah ke Interview, namun WhatsApp gagal dikirim: '.($waResponse['message'] ?? 'Gateway Offline'));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send interview WhatsApp: '.$e->getMessage());
            $this->dispatch('toast', type: 'error', message: 'Terjadi kesalahan sistem saat mengirim undangan WhatsApp.');
        }

        $this->showInterviewModal = false;
    }

    public $isScanningKtp = false;

    public function updatedFileKtp()
    {
        $this->validate([
            'fileKtp' => 'required|file|image|max:2048',
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
                    $this->editNik = $parsed['nik'];
                }
                if (! empty($parsed['name'])) {
                    $this->editName = $parsed['name'];
                }
                if (! empty($parsed['tempat_lahir'])) {
                    $this->editTempatLahir = $parsed['tempat_lahir'];
                }
                if (! empty($parsed['tanggal_lahir'])) {
                    $this->editTanggalLahir = $parsed['tanggal_lahir'];
                }
                if (! empty($parsed['jenis_kelamin'])) {
                    $this->editJenisKelamin = $parsed['jenis_kelamin'];
                }
                if (! empty($parsed['alamat'])) {
                    $this->editAlamat = $parsed['alamat'];
                }
                if (! empty($parsed['agama'])) {
                    $this->editAgama = $parsed['agama'];
                }
                if (! empty($parsed['status_kawin'])) {
                    $this->editStatusKawin = $parsed['status_kawin'];
                }
                if (! empty($parsed['pekerjaan'])) {
                    $this->editPekerjaan = $parsed['pekerjaan'];
                }
                if (! empty($parsed['kewarganegaraan'])) {
                    $this->editKewarganegaraan = $parsed['kewarganegaraan'];
                }

                $this->dispatch('toast', type: 'success', message: 'KTP berhasil di-scan! Nilai kolom input telah diperbarui.');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Terjadi kesalahan saat memproses pemindaian KTP.');
        } finally {
            $this->isScanningKtp = false;
        }
    }

    public function scanOcrManual()
    {
        if (! $this->selectedApplicant) {
            return;
        }

        $ktpPath = $this->selectedApplicant->documents['ktp'] ?? null;
        if (! $ktpPath) {
            $this->dispatch('toast', type: 'error', message: 'Berkas KTP tidak ditemukan untuk pelamar ini.');

            return;
        }

        $absolutePath = Storage::disk('public')->path($ktpPath);
        if (! file_exists($absolutePath)) {
            $this->dispatch('toast', type: 'error', message: 'Berkas fisik KTP tidak ditemukan di server.');

            return;
        }

        $this->isScanningKtp = true;

        try {
            $ocrService = new OcrService;
            $parsed = $ocrService->parseKtp($absolutePath);

            if (isset($parsed['error'])) {
                $this->dispatch('toast', type: 'error', message: 'Gagal memindai KTP: '.$parsed['error']);

                return;
            }

            if (! empty($parsed['nik'])) {
                $this->editNik = $parsed['nik'];
            }
            if (! empty($parsed['name'])) {
                $this->editName = $parsed['name'];
            }
            if (! empty($parsed['tempat_lahir'])) {
                $this->editTempatLahir = $parsed['tempat_lahir'];
            }
            if (! empty($parsed['tanggal_lahir'])) {
                $this->editTanggalLahir = $parsed['tanggal_lahir'];
            }
            if (! empty($parsed['jenis_kelamin'])) {
                $this->editJenisKelamin = $parsed['jenis_kelamin'];
            }
            if (! empty($parsed['alamat'])) {
                $this->editAlamat = $parsed['alamat'];
            }
            if (! empty($parsed['agama'])) {
                $this->editAgama = $parsed['agama'];
            }
            if (! empty($parsed['status_kawin'])) {
                $this->editStatusKawin = $parsed['status_kawin'];
            }
            if (! empty($parsed['pekerjaan'])) {
                $this->editPekerjaan = $parsed['pekerjaan'];
            }
            if (! empty($parsed['kewarganegaraan'])) {
                $this->editKewarganegaraan = $parsed['kewarganegaraan'];
            }

            $this->dispatch('toast', type: 'success', message: 'KTP berhasil di-scan ulang! Silakan periksa kolom input.');
        } catch (\Exception $e) {
            Log::error('Admin KTP manual OCR scan exception: '.$e->getMessage());
            $this->dispatch('toast', type: 'error', message: 'Terjadi kesalahan sistem saat memproses OCR.');
        } finally {
            $this->isScanningKtp = false;
        }
    }

    public function render()
    {
        $applicants = ApplicantModel::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('nik', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $positions = Position::with('department')->get();

        return view('livewire.admin.applicant', [
            'applicants' => $applicants,
            'positions' => $positions,
        ])->layout('layouts.app');
    }
}
