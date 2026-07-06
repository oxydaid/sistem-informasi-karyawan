<?php

namespace App\Livewire\Employee;

use App\Models\Employee;
use App\Models\Payroll;
use Livewire\Component;

class PayrollView extends Component
{
    public function render()
    {
        $employee = Employee::where('user_id', auth()->id())->firstOrFail();
        $payrolls = Payroll::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.employee.payroll-view', [
            'payrolls' => $payrolls,
        ])->layout('layouts.app');
    }
}
