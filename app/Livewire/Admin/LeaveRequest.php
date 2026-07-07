<?php

namespace App\Livewire\Admin;

use App\Models\Employee;
use App\Models\LeaveRequest as LeaveModel;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class LeaveRequest extends Component
{
    use WithFileUploads, WithPagination;

    public $filterStatus = '';

    // Modal state
    public $showCreateModal = false;

    public $showEditModal = false;

    public $showDeleteModal = false;

    // Form fields
    public $selectedLeaveId = null;

    public $deletingLeaveId = null;

    public $employeeId = '';

    public $searchEmployee = '';

    public $startDate = '';

    public $endDate = '';

    public $reason = '';

    public $status = 'pending';

    public $fileProof;

    public $unpaidDays = 0;

    protected $queryString = ['filterStatus'];

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['employeeId', 'searchEmployee', 'startDate', 'endDate', 'reason', 'status', 'fileProof', 'unpaidDays', 'selectedLeaveId']);
        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->showCreateModal = true;
    }

    public function selectEmployeeForForm($id, $name)
    {
        $this->employeeId = $id;
        $this->searchEmployee = $name;
    }

    public function clearEmployeeSelection()
    {
        $this->employeeId = '';
        $this->searchEmployee = '';
    }

    public function createLeaveRequest()
    {
        $this->validate([
            'employeeId' => 'required|exists:employees,id',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'reason' => 'required|string|min:5',
            'fileProof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [], [
            'employeeId' => 'Karyawan',
            'startDate' => 'Tanggal Mulai',
            'endDate' => 'Tanggal Selesai',
            'reason' => 'Alasan',
            'fileProof' => 'Bukti Berkas',
        ]);

        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $days = $start->diffInDays($end) + 1;

        $proofPath = null;
        if ($this->fileProof) {
            $proofPath = $this->fileProof->store('proof_files', 'public');
        }

        $req = LeaveModel::create([
            'employee_id' => $this->employeeId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'days_requested' => $days,
            'reason' => $this->reason,
            'proof_file_path' => $proofPath,
            'status' => $this->status,
        ]);

        if ($this->status === 'approved_hrd') {
            $this->deductLeaveQuota($req);
        }

        $this->showCreateModal = false;
        $this->dispatch('toast', type: 'success', message: 'Pengajuan cuti berhasil dibuat!');
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $req = LeaveModel::with('employee.user')->findOrFail($id);
        $this->selectedLeaveId = $req->id;
        $this->employeeId = $req->employee_id;
        $this->searchEmployee = $req->employee->user->name ?? '';
        $this->startDate = $req->start_date->format('Y-m-d');
        $this->endDate = $req->end_date->format('Y-m-d');
        $this->reason = $req->reason;
        $this->status = $req->status;
        $this->unpaidDays = $req->unpaid_days;
        $this->fileProof = null;
        $this->showEditModal = true;
    }

    public function updateLeaveRequest()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'reason' => 'required|string|min:5',
            'fileProof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [], [
            'startDate' => 'Tanggal Mulai',
            'endDate' => 'Tanggal Selesai',
            'reason' => 'Alasan',
            'fileProof' => 'Bukti Berkas',
        ]);

        $req = LeaveModel::findOrFail($this->selectedLeaveId);
        $oldStatus = $req->status;
        $newStatus = $this->status;

        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $days = $start->diffInDays($end) + 1;

        $updateData = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'days_requested' => $days,
            'reason' => $this->reason,
            'status' => $newStatus,
        ];

        if ($this->fileProof) {
            $updateData['proof_file_path'] = $this->fileProof->store('proof_files', 'public');
        }

        if ($oldStatus === 'approved_hrd' && $newStatus !== 'approved_hrd') {
            $this->restoreLeaveQuota($req);
        }

        $req->update($updateData);

        if ($oldStatus !== 'approved_hrd' && $newStatus === 'approved_hrd') {
            $this->deductLeaveQuota($req);
        }

        $this->showEditModal = false;
        $this->dispatch('toast', type: 'success', message: 'Pengajuan cuti berhasil diperbarui!');
    }

    public function confirmDelete($id)
    {
        $this->deletingLeaveId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteLeaveRequest()
    {
        if ($this->deletingLeaveId) {
            $req = LeaveModel::findOrFail($this->deletingLeaveId);
            if ($req->status === 'approved_hrd') {
                $this->restoreLeaveQuota($req);
            }
            $req->delete();
            $this->dispatch('toast', type: 'success', message: 'Pengajuan cuti berhasil dihapus!');
        }
        $this->showDeleteModal = false;
        $this->deletingLeaveId = null;
    }

    private function deductLeaveQuota(LeaveModel $req)
    {
        $employee = $req->employee;
        $days = $req->days_requested;
        $remainingQuota = $employee->leave_quota;

        $unpaidDays = 0;
        if ($days > $remainingQuota) {
            $unpaidDays = $days - $remainingQuota;
            $newQuota = 0;
        } else {
            $newQuota = $remainingQuota - $days;
        }

        $employee->update(['leave_quota' => $newQuota]);
        $req->update(['unpaid_days' => $unpaidDays]);
    }

    private function restoreLeaveQuota(LeaveModel $req)
    {
        $employee = $req->employee;
        $employee->increment('leave_quota', $req->days_requested - $req->unpaid_days);
        $req->update(['unpaid_days' => 0]);
    }

    public function approve($id)
    {
        $req = LeaveModel::findOrFail($id);
        $user = auth()->user();

        if ($user->role === 'manager') {
            $req->update(['status' => 'approved_manager']);
            $this->dispatch('toast', type: 'success', message: 'Pengajuan cuti disetujui oleh Manager! Menunggu persetujuan akhir HRD.');
        } elseif (in_array($user->role, ['hrd', 'super_admin'])) {
            $this->deductLeaveQuota($req);
            $req->update(['status' => 'approved_hrd']);
            $this->dispatch('toast', type: 'success', message: 'Pengajuan cuti berhasil disetujui akhir oleh HRD! Kuota cuti karyawan telah dipotong.');
        }
    }

    public function reject($id)
    {
        $req = LeaveModel::findOrFail($id);
        $req->update(['status' => 'rejected']);
        $this->dispatch('toast', type: 'success', message: 'Pengajuan cuti telah ditolak.');
    }

    public function render()
    {
        $user = auth()->user();

        $query = LeaveModel::with('employee.user');

        // If manager, only show leave requests of employees in the same department as the manager
        if ($user->role === 'manager') {
            $managerEmp = Employee::where('user_id', $user->id)->first();
            if ($managerEmp) {
                $deptId = $managerEmp->position->department_id;
                $query->whereHas('employee.position', function ($q) use ($deptId) {
                    $q->where('department_id', $deptId);
                });
            }
        }

        $requests = $query->when($this->filterStatus, function ($q) {
            $q->where('status', $this->filterStatus);
        })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Optimized Searchable Select (limits results to 5 matches or preloads first 5)
        $searchEmployees = [];
        if ($this->searchEmployee !== '') {
            $searchEmployees = Employee::with('user')
                ->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%'.$this->searchEmployee.'%');
                })
                ->limit(5)
                ->get();
        } else {
            $searchEmployees = Employee::with('user')
                ->limit(5)
                ->get();
        }

        return view('livewire.admin.leave-request', [
            'requests' => $requests,
            'searchEmployees' => $searchEmployees,
        ])->layout('layouts.app');
    }
}
