<?php

namespace App\Livewire\Admin;

use App\Models\Applicant;
use App\Models\Contract as ContractModel;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use App\Services\ContractService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Contract extends Component
{
    use WithPagination;

    public $search = '';

    public $filterType = '';

    public $filterStatus = ''; // active, ending_soon, expired, unsigned

    // Form modal state
    public $showModal = false;

    public $isEdit = false;

    public $contractId = null;

    // Form inputs
    public $applicantId = '';

    public $positionId = '';

    public $employmentType = '';

    public $startDate = '';

    public $endDate = '';

    public $salary = '';

    public $isSigned = false;

    // Delete confirmation
    public $confirmingDeletion = false;

    public $deletingId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    protected function rules()
    {
        return [
            'applicantId' => $this->isEdit ? 'required|exists:applicants,id' : 'required|exists:applicants,id|unique:contracts,applicant_id',
            'positionId' => 'required|exists:positions,id',
            'employmentType' => 'required|in:magang,pkl,kontrak,tetap,freelance',
            'startDate' => 'required|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
            'salary' => 'required|numeric|min:0',
            'isSigned' => 'boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'applicantId' => 'Nama Pelamar / Karyawan',
            'positionId' => 'Jabatan',
            'employmentType' => 'Tipe Pekerjaan',
            'startDate' => 'Tanggal Mulai',
            'endDate' => 'Tanggal Berakhir',
            'salary' => 'Gaji Pokok',
            'isSigned' => 'Status Tanda Tangan',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedPositionId($value)
    {
        if ($value) {
            $pos = Position::find($value);
            if ($pos) {
                $this->salary = $pos->base_salary;
            }
        }
    }

    public function openCreateModal()
    {
        $this->resetErrorBag();
        $this->reset(['isEdit', 'contractId', 'applicantId', 'positionId', 'employmentType', 'startDate', 'endDate', 'salary', 'isSigned']);
        $this->startDate = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetErrorBag();
        $contract = ContractModel::findOrFail($id);
        $this->contractId = $contract->id;
        $this->applicantId = $contract->applicant_id;
        $this->positionId = $contract->position_id;
        $this->employmentType = $contract->employment_type;
        $this->startDate = $contract->start_date ? $contract->start_date->format('Y-m-d') : '';
        $this->endDate = $contract->end_date ? $contract->end_date->format('Y-m-d') : '';
        $this->salary = $contract->salary;
        $this->isSigned = $contract->is_signed;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function saveContract(ContractService $contractService)
    {
        $this->validate();

        try {
            $applicant = Applicant::findOrFail($this->applicantId);

            // Generate/regenerate contract draft PDF via Service
            $contract = $contractService->generateDraft(
                $applicant,
                $this->employmentType,
                $this->positionId,
                $this->startDate,
                $this->endDate ?: null,
                $this->salary
            );

            // Update signing status
            $contract->update([
                'is_signed' => $this->isSigned,
            ]);

            // If it's signed and linked to employee, update employee's job status too
            $employee = Employee::where('nik', $applicant->nik)->first();
            if ($employee) {
                $contract->update(['employee_id' => $employee->id]);
                $employee->update([
                    'position_id' => $this->positionId,
                    'employment_status' => $this->employmentType,
                    'join_date' => $this->startDate,
                ]);
            }

            $this->dispatch('toast', type: 'success', message: $this->isEdit ? 'Kontrak kerja berhasil diperbarui!' : 'Kontrak kerja berhasil dibuat!');
            $this->showModal = false;
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal menyimpan kontrak: '.$e->getMessage());
        }
    }

    public function toggleSigned($id)
    {
        $contract = ContractModel::findOrFail($id);
        $newStatus = ! $contract->is_signed;

        if ($newStatus) {
            try {
                $this->createEmployeeFromContract($contract);
                $this->dispatch('toast', type: 'success', message: 'Kontrak berhasil ditandatangani manual & akun karyawan telah dibuat!');
            } catch (\Exception $e) {
                $this->dispatch('toast', type: 'error', message: 'Gagal membuat akun karyawan: '.$e->getMessage());
            }
        } else {
            $contract->update(['is_signed' => false]);
            $this->dispatch('toast', type: 'success', message: 'Status tanda tangan kontrak berhasil diubah!');
        }
    }

    public function approveUploadedContract($id)
    {
        $contract = ContractModel::findOrFail($id);
        if ($contract->status !== 'uploaded') {
            $this->dispatch('toast', type: 'error', message: 'Kontrak ini tidak dapat disetujui karena statusnya bukan Uploaded.');

            return;
        }

        try {
            $this->createEmployeeFromContract($contract);
            $this->dispatch('toast', type: 'success', message: 'Kontrak berhasil disetujui! Akun pengguna dan profil karyawan telah dibuat secara otomatis.');
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal menyetujui kontrak: '.$e->getMessage());
        }
    }

    private function createEmployeeFromContract(ContractModel $contract)
    {
        $applicant = $contract->applicant;

        // Check if employee already exists
        $employee = Employee::where('nik', $applicant->nik)->first();
        if ($employee) {
            $contract->update([
                'employee_id' => $employee->id,
                'is_signed' => true,
                'status' => 'approved',
            ]);
            $applicant->update(['status' => 'accepted']);

            return;
        }

        \DB::transaction(function () use ($contract, $applicant) {
            // 1. Create User
            $user = User::create([
                'name' => $applicant->name,
                'email' => $applicant->email,
                'password' => Hash::make('password123'), // default
                'role' => 'employee',
            ]);

            // 2. Generate Employee ID
            $deptCode = $contract->position
                ? strtoupper(substr($contract->position->department->name ?? 'EMP', 0, 3))
                : 'EMP';
            $randomCode = rand(100, 999);
            $employeeIdNumber = "EMP-{$deptCode}-{$randomCode}";

            // 3. Create Employee
            $employee = Employee::create([
                'user_id' => $user->id,
                'position_id' => $contract->position_id,
                'employee_id_number' => $employeeIdNumber,
                'nik' => $applicant->nik,
                'phone' => $applicant->phone,
                'address' => $applicant->metadata['alamat'] ?? null,
                'employment_status' => $contract->employment_type,
                'join_date' => $contract->start_date ?: now()->format('Y-m-d'),
                'leave_quota' => 12,
                'base_salary' => $contract->salary,
                'documents' => $applicant->documents, // Copy all documents
                'metadata' => $applicant->metadata,   // Copy all metadata
            ]);

            // 4. Update Contract
            $contract->update([
                'employee_id' => $employee->id,
                'is_signed' => true,
                'status' => 'approved',
            ]);

            // 5. Update Applicant status to accepted
            $applicant->update(['status' => 'accepted']);
        });
    }

    public function confirmDelete($id)
    {
        $this->deletingId = $id;
        $this->confirmingDeletion = true;
    }

    public function deleteContract()
    {
        if ($this->deletingId) {
            $contract = ContractModel::findOrFail($this->deletingId);

            // Delete file from storage if exists
            if ($contract->contract_file_path && Storage::disk('public')->exists($contract->contract_file_path)) {
                Storage::disk('public')->delete($contract->contract_file_path);
            }

            $contract->delete();
            $this->dispatch('toast', type: 'success', message: 'Kontrak kerja berhasil dihapus!');
        }

        $this->confirmingDeletion = false;
        $this->deletingId = null;
    }

    public function downloadPdf($id)
    {
        $contract = ContractModel::findOrFail($id);
        if ($contract->contract_file_path && Storage::disk('public')->exists($contract->contract_file_path)) {
            return Storage::disk('public')->download($contract->contract_file_path);
        }

        $this->dispatch('toast', type: 'error', message: 'Berkas PDF kontrak tidak ditemukan di penyimpanan.');
    }

    public function render()
    {
        $query = ContractModel::with(['applicant', 'employee', 'position.department']);

        // Search name or NIK
        if ($this->search) {
            $query->whereHas('applicant', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('nik', 'like', '%'.$this->search.'%');
            });
        }

        // Filter Tipe Pekerjaan
        if ($this->filterType) {
            $query->where('employment_type', $this->filterType);
        }

        // Filter Status Kontrak
        if ($this->filterStatus) {
            $today = now()->toDateString();
            $endingSoon = now()->addDays(30)->toDateString();

            if ($this->filterStatus === 'active') {
                $query->where('is_signed', true)
                    ->where(function ($q) use ($today) {
                        $q->whereNull('end_date')
                            ->orWhere('end_date', '>=', $today);
                    });
            } elseif ($this->filterStatus === 'ending_soon') {
                $query->where('is_signed', true)
                    ->whereNotNull('end_date')
                    ->whereBetween('end_date', [$today, $endingSoon]);
            } elseif ($this->filterStatus === 'expired') {
                $query->whereNotNull('end_date')
                    ->where('end_date', '<', $today);
            } elseif ($this->filterStatus === 'unsigned') {
                $query->where('is_signed', false);
            }
        }

        $contracts = $query->latest()->paginate(10);
        $positions = Position::with('department')->get();

        // Get applicants that don't have contracts yet (only for create form)
        // or if in edit mode, include the currently selected applicant
        $availableApplicants = Applicant::where(function ($q) {
            $q->whereDoesntHave('contracts');
            if ($this->isEdit && $this->applicantId) {
                $q->orWhere('id', $this->applicantId);
            }
        })->get();

        return view('livewire.admin.contract', [
            'contracts' => $contracts,
            'positions' => $positions,
            'availableApplicants' => $availableApplicants,
        ])->layout('layouts.app');
    }
}
