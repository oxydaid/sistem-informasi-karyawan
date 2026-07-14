<?php

namespace App\Livewire\Admin;

use App\Models\Applicant;
use App\Models\Employee;
use App\Models\KpiEvaluation;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\Position;
use App\Services\KpiService;
use Livewire\Component;

class Overview extends Component
{
    public function render()
    {
        $stats = [
            'total_employees' => Employee::where('is_active', true)->count(),
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

        if (! $employee) {
            $position = Position::first();
            if ($position) {
                $employee = Employee::create([
                    'user_id' => auth()->id(),
                    'position_id' => $position->id,
                    'employee_id_number' => 'EMP-ADM-'.str_pad(auth()->id(), 3, '0', STR_PAD_LEFT),
                    'nik' => '99'.str_pad(auth()->id(), 14, '0', STR_PAD_LEFT),
                    'phone' => '081200000000',
                    'employment_status' => 'tetap',
                    'join_date' => '2025-01-01',
                    'leave_quota' => 12,
                ]);
            }
        }

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
            // Seed 6 months of dummy KPI data if they don't exist
            for ($i = 5; $i >= 0; $i--) {
                $m = now()->subMonths($i);
                $my = $m->format('m-Y');

                $exists = KpiEvaluation::where('employee_id', $employee->id)
                    ->where('month_year', $my)
                    ->exists();

                if (! $exists) {
                    $kpiService = new KpiService;
                    $kehadiran = rand(4, 5);
                    $keahlian = $kpiService->getKeahlianScore($employee);
                    $keaktifan = rand(3, 5);
                    $kedisiplinan = rand(4, 5);
                    $mean = ($kehadiran + $keahlian + $keaktifan + $kedisiplinan) / 4;
                    $score = $mean * 20;

                    KpiEvaluation::create([
                        'employee_id' => $employee->id,
                        'evaluator_id' => auth()->id(),
                        'month_year' => $my,
                        'score' => $score,
                        'kehadiran' => $kehadiran,
                        'kehadiran_notes' => 'Kehadiran sangat baik.',
                        'keahlian' => $keahlian,
                        'keahlian_notes' => $keahlian === 5 ? 'Memiliki sertifikat keahlian resmi.' : 'Keahlian mumpuni.',
                        'keaktifan' => $keaktifan,
                        'keaktifan_notes' => 'Cukup aktif membantu tim.',
                        'kedisiplinan' => $kedisiplinan,
                        'kedisiplinan_notes' => 'Disiplin dan tepat waktu.',
                    ]);
                }
            }

            $currentKpi = KpiEvaluation::where('employee_id', $employee->id)
                ->where('month_year', now()->format('m-Y'))
                ->first();

            $prevMonth = now()->subMonth()->format('m-Y');
            $prevKpi = KpiEvaluation::where('employee_id', $employee->id)
                ->where('month_year', $prevMonth)
                ->first();

            // Last 3 months history
            for ($i = 2; $i >= 0; $i--) {
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
