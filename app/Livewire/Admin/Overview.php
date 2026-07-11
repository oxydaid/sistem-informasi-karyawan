<?php

namespace App\Livewire\Admin;

use App\Models\Applicant;
use App\Models\Employee;
use App\Models\KpiEvaluation;
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
            'underperforming_kpi' => KpiEvaluation::where('month_year', now()->format('m-Y'))
                ->where('score', '<', 60)
                ->count(),
        ];

        $recentApplicants = Applicant::orderBy('created_at', 'desc')->take(5)->get();
        $recentLeaves = LeaveRequest::with('employee.user')->orderBy('created_at', 'desc')->take(5)->get();

        $employee = Employee::where('user_id', auth()->id())->first();

        $currentKpi = null;
        $prevKpi = null;
        $historyLabels = [];
        $historyData = [
            'kehadiran' => [],
            'keahlian' => [],
            'keaktifan' => [],
            'kedisiplinan' => [],
            'mean' => [],
        ];

        if ($employee) {
            $currentKpi = KpiEvaluation::where('employee_id', $employee->id)
                ->where('month_year', now()->format('m-Y'))
                ->first();

            $prevMonth = now()->subMonth()->format('m-Y');
            $prevKpi = KpiEvaluation::where('employee_id', $employee->id)
                ->where('month_year', $prevMonth)
                ->first();

            // Last 6 months history
            for ($i = 5; $i >= 0; $i--) {
                $m = now()->subMonths($i);
                $my = $m->format('m-Y');
                $historyLabels[] = $m->translatedFormat('M Y');

                $eval = KpiEvaluation::where('employee_id', $employee->id)
                    ->where('month_year', $my)
                    ->first();

                $historyData['kehadiran'][] = $eval ? $eval->kehadiran : 0;
                $historyData['keahlian'][] = $eval ? $eval->keahlian : 0;
                $historyData['keaktifan'][] = $eval ? $eval->keaktifan : 0;
                $historyData['kedisiplinan'][] = $eval ? $eval->kedisiplinan : 0;
                $historyData['mean'][] = $eval ? ($eval->score / 20) : 0;
            }
        }

        return view('livewire.admin.overview', [
            'stats' => $stats,
            'recentApplicants' => $recentApplicants,
            'recentLeaves' => $recentLeaves,
            'employee' => $employee,
            'currentKpi' => $currentKpi,
            'prevKpi' => $prevKpi,
            'historyLabels' => $historyLabels,
            'historyData' => $historyData,
        ])->layout('layouts.app');
    }
}
