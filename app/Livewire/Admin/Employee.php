<?php

namespace App\Livewire\Admin;

use App\Models\Employee as EmployeeModel;
use App\Models\Position;
use App\Models\User;
use App\Services\OcrService;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Employee extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $filterPosition = '';

    // Modal & CRUD State
    public $showEmployeeModal = false;

    public $showDeleteModal = false;

    public $isEditing = false;

    public $selectedEmployeeId = null;

    public $deleteEmployeeId = null;

    // Form Fields
    public $name = '';

    public $email = '';

    public $password = '';

    public $position_id = '';

    public $employee_id_number = '';

    public $nik = '';

    public $phone = '';

    public $address = '';

    public $employment_status = 'tetap';

    public $join_date = '';

    public $leave_quota = 12;

    public $base_salary = '';

    public $is_active = true;

    // OCR KTP / Metadata Fields
    public $tempat_lahir = '';

    public $tanggal_lahir = '';

    public $jenis_kelamin = '';

    public $agama = '';

    public $status_kawin = '';

    public $pekerjaan = '';

    public $kewarganegaraan = 'WNI';

    // Documents list
    public $documents = [];

    public $activeDocUrl = null;

    public $signedContractPath = null;

    // File Upload properties
    public $fileKtp;

    public $fileKk;

    public $fileIjazah;

    public $fileSkck;

    public $filePasFoto;

    public $fileCv;

    public $fileSim;

    public $fileSertifikat;

    public $filePendukung = [];

    public $isScanningKtp = false;

    protected $queryString = ['search', 'filterStatus', 'filterPosition'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterPosition()
    {
        $this->resetPage();
    }

    public function updatedPositionId($value)
    {
        if ($value) {
            $position = Position::find($value);
            if ($position) {
                $this->base_salary = $position->base_salary;
            }
        } else {
            $this->base_salary = '';
        }
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'email', 'password', 'position_id', 'employee_id_number', 'nik', 'phone', 'address', 'employment_status', 'join_date', 'leave_quota', 'is_active', 'base_salary', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'status_kawin', 'pekerjaan', 'kewarganegaraan', 'documents', 'activeDocUrl', 'signedContractPath', 'fileKtp', 'fileKk', 'fileIjazah', 'fileSkck', 'filePasFoto', 'fileCv', 'fileSim', 'fileSertifikat', 'filePendukung']);
        $this->isEditing = false;
        $this->join_date = date('Y-m-d');
        $this->leave_quota = 12;
        $this->is_active = true;
        $this->kewarganegaraan = 'WNI';
        $this->showEmployeeModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $this->reset(['fileKtp', 'fileKk', 'fileIjazah', 'fileSkck', 'filePasFoto', 'fileCv', 'fileSim', 'fileSertifikat', 'filePendukung']);
        $this->isEditing = true;
        $this->selectedEmployeeId = $id;

        $employee = EmployeeModel::with('contracts')->findOrFail($id);
        $this->name = $employee->user->name;
        $this->email = $employee->user->email;
        $this->password = ''; // empty by default when editing
        $this->position_id = $employee->position_id;
        $this->employee_id_number = $employee->employee_id_number;
        $this->nik = $employee->nik;
        $this->phone = $employee->phone;
        $this->address = $employee->address ?: ($employee->metadata['alamat'] ?? '');
        $this->employment_status = $employee->employment_status;
        $this->join_date = $employee->join_date ? $employee->join_date->format('Y-m-d') : '';
        $this->leave_quota = $employee->leave_quota;
        $this->is_active = (bool) $employee->is_active;
        $this->base_salary = $employee->base_salary;

        // Load KTP Metadata & Documents
        $this->tempat_lahir = $employee->metadata['tempat_lahir'] ?? '';
        $this->tanggal_lahir = $employee->metadata['tanggal_lahir'] ?? '';
        $this->jenis_kelamin = $employee->metadata['jenis_kelamin'] ?? '';
        $this->agama = $employee->metadata['agama'] ?? '';
        $this->status_kawin = $employee->metadata['status_kawin'] ?? '';
        $this->pekerjaan = $employee->metadata['pekerjaan'] ?? '';
        $this->kewarganegaraan = $employee->metadata['kewarganegaraan'] ?? 'WNI';
        $this->documents = $employee->documents ?? [];
        $this->activeDocUrl = null;

        // Load latest signed contract
        $latestContract = $employee->contracts()
            ->whereNotNull('signed_contract_path')
            ->latest()
            ->first();
        $this->signedContractPath = $latestContract ? $latestContract->signed_contract_path : null;

        $this->showEmployeeModal = true;
    }

    public function saveEmployee()
    {
        $userId = $this->isEditing ? EmployeeModel::findOrFail($this->selectedEmployeeId)->user_id : null;

        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$userId,
            'password' => $this->isEditing ? 'nullable|min:8' : 'required|min:8',
            'position_id' => 'required|exists:positions,id',
            'employee_id_number' => 'nullable|unique:employees,employee_id_number,'.$this->selectedEmployeeId,
            'nik' => 'required|string|size:16|unique:employees,nik,'.$this->selectedEmployeeId,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'employment_status' => 'required|in:magang,pkl,kontrak,tetap,freelance',
            'join_date' => 'required|date',
            'leave_quota' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
            'base_salary' => 'nullable|numeric|min:0',

            // File upload rules
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
            'name' => 'Nama Lengkap',
            'email' => 'Email',
            'password' => 'Password',
            'position_id' => 'Posisi Jabatan',
            'employee_id_number' => 'NIK Karyawan',
            'nik' => 'NIK KTP',
            'phone' => 'No. Handphone/WA',
            'leave_quota' => 'Kuota Cuti',
            'is_active' => 'Status Keaktifan',
            'base_salary' => 'Gaji Pokok',
        ]);

        try {
            // Auto generate employee id number if empty
            if (empty($this->employee_id_number)) {
                $year = date('Y', strtotime($this->join_date ?: now()));
                $prefix = "EMP-{$year}-";
                $lastEmployee = EmployeeModel::where('employee_id_number', 'like', $prefix.'%')
                    ->orderBy('employee_id_number', 'desc')
                    ->first();

                $nextNumber = 1;
                if ($lastEmployee) {
                    $lastNumber = intval(substr($lastEmployee->employee_id_number, strlen($prefix)));
                    $nextNumber = $lastNumber + 1;
                }

                $this->employee_id_number = $prefix.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }

            $metadata = $this->isEditing ? (EmployeeModel::findOrFail($this->selectedEmployeeId)->metadata ?? []) : [];
            $metadata['tempat_lahir'] = $this->tempat_lahir;
            $metadata['tanggal_lahir'] = $this->tanggal_lahir;
            $metadata['jenis_kelamin'] = $this->jenis_kelamin;
            $metadata['agama'] = $this->agama;
            $metadata['status_kawin'] = $this->status_kawin;
            $metadata['pekerjaan'] = $this->pekerjaan;
            $metadata['kewarganegaraan'] = $this->kewarganegaraan;
            $metadata['alamat'] = $this->address;

            // Handle file storage
            $docs = $this->isEditing ? ($employee->documents ?? []) : [];

            $storeFile = function ($file, $key) {
                if ($file) {
                    $filename = $key.'.'.$file->getClientOriginalExtension();
                    $path = $file->storeAs("berkas/{$this->nik}", $filename, 'public');

                    return $path;
                }

                return null;
            };

            if ($this->fileKtp) {
                $docs['ktp'] = $storeFile($this->fileKtp, 'ktp');
            }
            if ($this->fileKk) {
                $docs['kk'] = $storeFile($this->fileKk, 'kk');
            }
            if ($this->fileIjazah) {
                $docs['ijazah'] = $storeFile($this->fileIjazah, 'ijazah');
            }
            if ($this->fileSkck) {
                $docs['skck'] = $storeFile($this->fileSkck, 'skck');
            }
            if ($this->filePasFoto) {
                $docs['pas_foto'] = $storeFile($this->filePasFoto, 'pas_foto');
            }
            if ($this->fileCv) {
                $docs['cv'] = $storeFile($this->fileCv, 'cv');
            }
            if ($this->fileSim) {
                $docs['sim'] = $storeFile($this->fileSim, 'sim');
            }
            if ($this->fileSertifikat) {
                $docs['sertifikat'] = $storeFile($this->fileSertifikat, 'sertifikat');
            }

            if (! empty($this->filePendukung)) {
                $supportingPaths = $docs['pendukung'] ?? [];
                foreach ($this->filePendukung as $index => $file) {
                    $filename = 'pendukung_'.(count($supportingPaths) + 1).'.'.$file->getClientOriginalExtension();
                    $path = $file->storeAs("berkas/{$this->nik}", $filename, 'public');
                    $supportingPaths[] = $path;
                }
                $docs['pendukung'] = $supportingPaths;
            }

            if ($this->isEditing) {
                // Update User details
                $employee = EmployeeModel::findOrFail($this->selectedEmployeeId);
                $user = $employee->user;

                $userData = [
                    'name' => $this->name,
                    'email' => $this->email,
                ];
                if (! empty($this->password)) {
                    $userData['password'] = Hash::make($this->password);
                }
                $user->update($userData);

                // Update Employee details
                $employee->update([
                    'position_id' => $this->position_id,
                    'employee_id_number' => $this->employee_id_number,
                    'nik' => $this->nik,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'employment_status' => $this->employment_status,
                    'join_date' => $this->join_date,
                    'leave_quota' => $this->leave_quota,
                    'is_active' => $this->is_active,
                    'base_salary' => $this->base_salary ?: null,
                    'documents' => $docs,
                    'metadata' => $metadata,
                ]);

                $this->reset(['fileKtp', 'fileKk', 'fileIjazah', 'fileSkck', 'filePasFoto', 'fileCv', 'fileSim', 'fileSertifikat', 'filePendukung']);
                $this->dispatch('toast', type: 'success', message: 'Data karyawan '.$this->name.' berhasil diperbarui!');
            } else {
                // Create User
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'role' => 'employee',
                ]);

                // Create Employee
                EmployeeModel::create([
                    'user_id' => $user->id,
                    'position_id' => $this->position_id,
                    'employee_id_number' => $this->employee_id_number,
                    'nik' => $this->nik,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'employment_status' => $this->employment_status,
                    'join_date' => $this->join_date,
                    'leave_quota' => $this->leave_quota,
                    'is_active' => $this->is_active,
                    'base_salary' => $this->base_salary ?: null,
                    'documents' => $docs,
                    'metadata' => $metadata,
                ]);

                $this->reset(['fileKtp', 'fileKk', 'fileIjazah', 'fileSkck', 'filePasFoto', 'fileCv', 'fileSim', 'fileSertifikat', 'filePendukung']);
                $this->dispatch('toast', type: 'success', message: 'Karyawan baru '.$this->name.' berhasil ditambahkan!');
            }

            $this->showEmployeeModal = false;
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal memproses data karyawan: '.$e->getMessage());
        }
    }

    public function setPreviewDoc($path)
    {
        $this->activeDocUrl = $path;
    }

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
                    $this->nik = $parsed['nik'];
                }
                if (! empty($parsed['tempat_lahir'])) {
                    $this->tempat_lahir = $parsed['tempat_lahir'];
                }
                if (! empty($parsed['tanggal_lahir'])) {
                    $this->tanggal_lahir = $parsed['tanggal_lahir'];
                }
                if (! empty($parsed['jenis_kelamin'])) {
                    $this->jenis_kelamin = $parsed['jenis_kelamin'];
                }
                if (! empty($parsed['alamat'])) {
                    $this->address = $parsed['alamat'];
                }
                if (! empty($parsed['agama'])) {
                    $this->agama = $parsed['agama'];
                }
                if (! empty($parsed['status_kawin'])) {
                    $this->status_kawin = $parsed['status_kawin'];
                }
                if (! empty($parsed['pekerjaan'])) {
                    $this->pekerjaan = $parsed['pekerjaan'];
                }
                if (! empty($parsed['kewarganegaraan'])) {
                    $this->kewarganegaraan = $parsed['kewarganegaraan'];
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
        if (! $this->selectedEmployeeId) {
            return;
        }

        $employee = EmployeeModel::findOrFail($this->selectedEmployeeId);
        $ktpPath = $employee->documents['ktp'] ?? null;
        if (! $ktpPath) {
            $this->dispatch('toast', type: 'error', message: 'Berkas KTP tidak ditemukan untuk karyawan ini.');

            return;
        }

        $absolutePath = \Illuminate\Support\Facades\Storage::disk('public')->path($ktpPath);
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
                $this->nik = $parsed['nik'];
            }
            if (! empty($parsed['tempat_lahir'])) {
                $this->tempat_lahir = $parsed['tempat_lahir'];
            }
            if (! empty($parsed['tanggal_lahir'])) {
                $this->tanggal_lahir = $parsed['tanggal_lahir'];
            }
            if (! empty($parsed['jenis_kelamin'])) {
                $this->jenis_kelamin = $parsed['jenis_kelamin'];
            }
            if (! empty($parsed['alamat'])) {
                $this->address = $parsed['alamat'];
            }
            if (! empty($parsed['agama'])) {
                $this->agama = $parsed['agama'];
            }
            if (! empty($parsed['status_kawin'])) {
                $this->status_kawin = $parsed['status_kawin'];
            }
            if (! empty($parsed['pekerjaan'])) {
                $this->pekerjaan = $parsed['pekerjaan'];
            }
            if (! empty($parsed['kewarganegaraan'])) {
                $this->kewarganegaraan = $parsed['kewarganegaraan'];
            }

            $this->dispatch('toast', type: 'success', message: 'KTP berhasil di-scan ulang! Silakan periksa kolom input.');
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Terjadi kesalahan saat memproses OCR.');
        } finally {
            $this->isScanningKtp = false;
        }
    }

    public function downloadZip($nik)
    {
        $employee = EmployeeModel::where('nik', $nik)->firstOrFail();

        $zipFileName = "berkas_{$nik}.zip";
        $tempDir = storage_path('app/public/temp');
        if (! file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zipPath = "{$tempDir}/{$zipFileName}";

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $folderPath = "berkas/{$nik}";
            $files = \Storage::disk('public')->files($folderPath);

            if (empty($files)) {
                $zip->close();
                $this->dispatch('toast', type: 'error', message: "Tidak ada berkas yang ditemukan untuk NIK {$nik}.");

                return;
            }

            foreach ($files as $file) {
                $absolutePath = \Storage::disk('public')->path($file);
                $zip->addFile($absolutePath, basename($file));
            }

            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } else {
            $this->dispatch('toast', type: 'error', message: 'Gagal membuat file ZIP.');
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteEmployeeId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteEmployee()
    {
        try {
            $employee = EmployeeModel::findOrFail($this->deleteEmployeeId);
            $user = $employee->user;

            // Hapus user (akan menghapus employee juga secara cascade cascadeOnDelete)
            if ($user) {
                $user->delete();
            } else {
                $employee->delete();
            }

            $this->dispatch('toast', type: 'success', message: 'Karyawan berhasil dihapus dari sistem!');
            $this->showDeleteModal = false;
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal menghapus karyawan: '.$e->getMessage());
        }
    }

    public function render()
    {
        $employees = EmployeeModel::with(['user', 'position.department'])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                })->orWhere('employee_id_number', 'like', '%'.$this->search.'%')
                    ->orWhere('nik', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('employment_status', $this->filterStatus);
            })
            ->when($this->filterPosition, function ($query) {
                $query->where('position_id', $this->filterPosition);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $positions = Position::with('department')->get();

        return view('livewire.admin.employee', [
            'employees' => $employees,
            'positions' => $positions,
        ])->layout('layouts.app');
    }
}
