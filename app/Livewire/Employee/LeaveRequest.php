<?php

namespace App\Livewire\Employee;

use App\Models\Employee;
use App\Models\LeaveRequest as LeaveModel;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;

class LeaveRequest extends Component
{
    use WithFileUploads;

    public $employee;

    // Form inputs
    public $startDate = '';

    public $endDate = '';

    public $reason = '';

    public $fileProof;

    public $showCreateForm = false;

    protected function rules()
    {
        return [
            'startDate' => 'required|date|after_or_equal:today',
            'endDate' => 'required|date|after_or_equal:startDate',
            'reason' => 'required|string|min:5',
            'fileProof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'startDate' => 'Tanggal Mulai',
            'endDate' => 'Tanggal Berakhir',
            'reason' => 'Alasan Cuti',
            'fileProof' => 'Dokumen Bukti',
        ];
    }

    public function mount()
    {
        $this->employee = Employee::where('user_id', auth()->id())->firstOrFail();
    }

    public function submitRequest()
    {
        $this->validate();

        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $days = $start->diffInDays($end) + 1;

        $proofPath = null;
        if ($this->fileProof) {
            $proofPath = $this->fileProof->store('proof_files', 'public');
        }

        LeaveModel::create([
            'employee_id' => $this->employee->id,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'days_requested' => $days,
            'reason' => $this->reason,
            'proof_file_path' => $proofPath,
            'status' => 'pending',
        ]);

        $this->reset(['startDate', 'endDate', 'reason', 'fileProof', 'showCreateForm']);
        session()->flash('success', 'Pengajuan cuti berhasil dikirim! Menunggu persetujuan Manager.');
    }

    public function render()
    {
        $pastRequests = LeaveModel::where('employee_id', $this->employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.employee.leave-request', [
            'pastRequests' => $pastRequests,
        ])->layout('layouts.app');
    }
}
