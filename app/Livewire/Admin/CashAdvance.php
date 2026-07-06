<?php

namespace App\Livewire\Admin;

use App\Models\CashAdvance as CashAdvanceModel;
use App\Models\Employee;
use Livewire\Component;
use Livewire\WithPagination;

class CashAdvance extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $filterMonthOnly = '';

    public $filterYearOnly = '';

    // Modal state
    public $showCreateModal = false;

    public $showEditModal = false;

    public $showDeleteModal = false;

    // Form fields
    public $selectedId = null;

    public $deletingId = null;

    public $employeeId = '';

    public $searchEmployee = '';

    public $amount = '';

    public $date = '';

    public $reason = '';

    public $status = 'pending';

    protected $queryString = ['search', 'filterStatus', 'filterMonthOnly', 'filterYearOnly'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterMonthOnly()
    {
        $this->resetPage();
    }

    public function updatingFilterYearOnly()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['selectedId', 'employeeId', 'searchEmployee', 'amount', 'date', 'reason', 'status']);
        $this->date = now()->format('Y-m-d');
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

    public function createCashAdvance()
    {
        $this->validate([
            'employeeId' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1000',
            'date' => 'required|date',
            'reason' => 'required|string|min:5',
            'status' => 'required|in:pending,approved,rejected,settled',
        ], [], [
            'employeeId' => 'Karyawan',
            'amount' => 'Jumlah Pinjaman',
            'date' => 'Tanggal',
            'reason' => 'Alasan/Keterangan',
        ]);

        CashAdvanceModel::create([
            'employee_id' => $this->employeeId,
            'amount' => $this->amount,
            'date' => $this->date,
            'reason' => $this->reason,
            'status' => $this->status,
        ]);

        $this->showCreateModal = false;
        session()->flash('success', 'Kasbon berhasil ditambahkan!');
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $cash = CashAdvanceModel::with('employee.user')->findOrFail($id);
        $this->selectedId = $cash->id;
        $this->employeeId = $cash->employee_id;
        $this->searchEmployee = $cash->employee->user->name ?? '';
        $this->amount = $cash->amount;
        $this->date = $cash->date ? $cash->date->format('Y-m-d') : '';
        $this->reason = $cash->reason;
        $this->status = $cash->status;
        $this->showEditModal = true;
    }

    public function updateCashAdvance()
    {
        $this->validate([
            'amount' => 'required|numeric|min:1000',
            'date' => 'required|date',
            'reason' => 'required|string|min:5',
            'status' => 'required|in:pending,approved,rejected,settled',
        ], [], [
            'amount' => 'Jumlah Pinjaman',
            'date' => 'Tanggal',
            'reason' => 'Alasan/Keterangan',
        ]);

        $cash = CashAdvanceModel::findOrFail($this->selectedId);
        $cash->update([
            'amount' => $this->amount,
            'date' => $this->date,
            'reason' => $this->reason,
            'status' => $this->status,
        ]);

        $this->showEditModal = false;
        session()->flash('success', 'Kasbon berhasil diperbarui!');
    }

    public function confirmDelete($id)
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteCashAdvance()
    {
        if ($this->deletingId) {
            $cash = CashAdvanceModel::findOrFail($this->deletingId);
            $cash->delete();
            session()->flash('success', 'Kasbon berhasil dihapus!');
        }
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function updateStatus($id, $newStatus)
    {
        $cash = CashAdvanceModel::findOrFail($id);
        $cash->update(['status' => $newStatus]);
        session()->flash('success', 'Status kasbon berhasil diperbarui!');
    }

    public function render()
    {
        $query = CashAdvanceModel::with(['employee.user', 'employee.position.department']);

        if ($this->search) {
            $query->whereHas('employee.user', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%');
            })->orWhere('reason', 'like', '%'.$this->search.'%');
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterMonthOnly) {
            $query->whereMonth('date', $this->filterMonthOnly);
        }

        if ($this->filterYearOnly) {
            $query->whereYear('date', $this->filterYearOnly);
        }

        $cashAdvances = $query->orderBy('date', 'desc')->paginate(10);

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

        return view('livewire.admin.cash-advance', [
            'cashAdvances' => $cashAdvances,
            'searchEmployees' => $searchEmployees,
        ])->layout('layouts.app');
    }
}
