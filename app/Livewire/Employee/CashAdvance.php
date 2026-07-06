<?php

namespace App\Livewire\Employee;

use App\Models\CashAdvance as CashAdvanceModel;
use Livewire\Component;
use Livewire\WithPagination;

class CashAdvance extends Component
{
    use WithPagination;

    public $showRequestModal = false;

    public $filterMonthOnly = '';

    public $filterYearOnly = '';

    protected $queryString = ['filterMonthOnly', 'filterYearOnly'];

    public function updatingFilterMonthOnly()
    {
        $this->resetPage();
    }

    public function updatingFilterYearOnly()
    {
        $this->resetPage();
    }

    // Form fields
    public $amount = '';

    public $date = '';

    public $reason = '';

    public function openRequestModal()
    {
        $this->resetValidation();
        $this->reset(['amount', 'date', 'reason']);
        $this->date = now()->format('Y-m-d');
        $this->showRequestModal = true;
    }

    public function requestCashAdvance()
    {
        $employee = auth()->user()->employee;
        if (! $employee) {
            session()->flash('error', 'Profil karyawan tidak ditemukan.');

            return;
        }

        $this->validate([
            'amount' => 'required|numeric|min:1000',
            'date' => 'required|date',
            'reason' => 'required|string|min:5',
        ], [], [
            'amount' => 'Jumlah Pinjaman',
            'date' => 'Tanggal',
            'reason' => 'Alasan/Keterangan',
        ]);

        CashAdvanceModel::create([
            'employee_id' => $employee->id,
            'amount' => $this->amount,
            'date' => $this->date,
            'reason' => $this->reason,
            'status' => 'pending',
        ]);

        $this->showRequestModal = false;
        session()->flash('success', 'Permohonan kasbon berhasil dikirim! Menunggu persetujuan admin.');
    }

    public function render()
    {
        $employee = auth()->user()->employee;

        $query = CashAdvanceModel::query();
        if ($employee) {
            $query->where('employee_id', $employee->id);
            if ($this->filterMonthOnly) {
                $query->whereMonth('date', $this->filterMonthOnly);
            }
            if ($this->filterYearOnly) {
                $query->whereYear('date', $this->filterYearOnly);
            }
            $cashAdvances = $query->orderBy('date', 'desc')->paginate(10);
        } else {
            $cashAdvances = collect();
        }

        return view('livewire.employee.cash-advance', [
            'cashAdvances' => $cashAdvances,
        ])->layout('layouts.app');
    }
}
