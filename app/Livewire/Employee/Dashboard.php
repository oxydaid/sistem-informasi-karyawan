<?php

namespace App\Livewire\Employee;

use App\Models\Contract;
use App\Models\Employee;
use App\Models\KpiEvaluation;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use Livewire\Component;
use Livewire\WithFileUploads;

class Dashboard extends Component
{
    use WithFileUploads;

    public $filePasFoto;

    public function updatedFilePasFoto()
    {
        $this->validate([
            'filePasFoto' => 'required|image|max:2048',
        ], [
            'filePasFoto.image' => 'Pas foto harus berupa gambar (jpg, jpeg, png).',
            'filePasFoto.max' => 'Ukuran pas foto maksimal 2MB.',
        ]);

        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        // Store new pas foto
        $filename = 'pas_foto.'.$this->filePasFoto->getClientOriginalExtension();
        $path = $this->filePasFoto->storeAs("berkas/{$employee->nik}", $filename, 'public');

        // Update documents array
        $docs = $employee->documents ?? [];
        $docs['pas_foto'] = $path;
        $employee->update(['documents' => $docs]);

        $this->dispatch('toast', type: 'success', message: 'Pas foto profil Anda berhasil diperbarui!');
    }

    public function render()
    {
        $user = auth()->user();
        $employee = Employee::with(['position.department'])->where('user_id', $user->id)->firstOrFail();

        $contract = Contract::where('employee_id', $employee->id)->latest()->first();

        // Calculate average KPI score
        $avgKpi = KpiEvaluation::where('employee_id', $employee->id)->avg('score') ?: 0;

        // Count approved leave days
        $leavesCount = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved_hrd')
            ->sum('days_requested');

        $recentPayrolls = Payroll::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('livewire.employee.dashboard', [
            'employee' => $employee,
            'contract' => $contract,
            'avgKpi' => $avgKpi,
            'leavesCount' => $leavesCount,
            'recentPayrolls' => $recentPayrolls,
        ])->layout('layouts.app');
    }
}
