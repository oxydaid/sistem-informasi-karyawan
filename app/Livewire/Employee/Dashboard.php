<?php

namespace App\Livewire\Employee;

use App\Models\Contract;
use App\Models\Employee;
use App\Models\KpiEvaluation;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use Livewire\Component;

class Dashboard extends Component
{
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
