<?php

namespace App\Livewire\Admin;

use App\Models\Applicant;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use Livewire\Component;

class Overview extends Component
{
    public function render()
    {
        $stats = [
            'total_employees' => Employee::count(),
            'pending_applicants' => Applicant::where('status', 'pending')->count(),
            'pending_leaves' => LeaveRequest::where('status', 'pending')->count(),
            'active_payrolls' => Payroll::where('month_year', now()->format('m-Y'))->count(),
        ];

        $recentApplicants = Applicant::orderBy('created_at', 'desc')->take(5)->get();
        $recentLeaves = LeaveRequest::with('employee.user')->orderBy('created_at', 'desc')->take(5)->get();

        return view('livewire.admin.overview', [
            'stats' => $stats,
            'recentApplicants' => $recentApplicants,
            'recentLeaves' => $recentLeaves,
        ])->layout('layouts.app');
    }
}
