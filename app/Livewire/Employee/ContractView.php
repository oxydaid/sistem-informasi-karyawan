<?php

namespace App\Livewire\Employee;

use App\Models\Contract;
use App\Models\Employee;
use Livewire\Component;

class ContractView extends Component
{
    public function render()
    {
        $employee = Employee::where('user_id', auth()->id())->firstOrFail();
        $contracts = Contract::with('position.department')
            ->where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.employee.contract-view', [
            'contracts' => $contracts,
            'employee' => $employee,
        ])->layout('layouts.app');
    }
}
