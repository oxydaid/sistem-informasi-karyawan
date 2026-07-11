<?php

namespace App\Livewire\Employee;

use App\Models\Employee;
use App\Models\KpiEvaluation;
use Carbon\Carbon;
use Livewire\Component;

class KpiView extends Component
{
    public $monthYear = ''; // MM-YYYY

    public $employee;

    public $currentEvaluation = null;

    public $previousEvaluation = null;

    public function mount()
    {
        $this->employee = Employee::where('user_id', auth()->id())->firstOrFail();
        $this->monthYear = now()->format('m-Y');
        $this->loadEvaluations();
    }

    public function updatedMonthYear()
    {
        $this->loadEvaluations();
    }

    public function loadEvaluations()
    {
        $this->currentEvaluation = KpiEvaluation::where('employee_id', $this->employee->id)
            ->where('month_year', $this->monthYear)
            ->first();

        // Previous month calculation
        $prevMonth = Carbon::createFromFormat('m-Y', $this->monthYear)->subMonth()->format('m-Y');
        $this->previousEvaluation = KpiEvaluation::where('employee_id', $this->employee->id)
            ->where('month_year', $prevMonth)
            ->first();

        $currData = $this->currentEvaluation ? [
            $this->currentEvaluation->kehadiran,
            $this->currentEvaluation->keahlian,
            $this->currentEvaluation->keaktifan,
            $this->currentEvaluation->kedisiplinan,
        ] : [0, 0, 0, 0];

        $prevData = $this->previousEvaluation ? [
            $this->previousEvaluation->kehadiran,
            $this->previousEvaluation->keahlian,
            $this->previousEvaluation->keaktifan,
            $this->previousEvaluation->kedisiplinan,
        ] : [0, 0, 0, 0];

        $this->dispatch('renderEmployeeRadarChart', current: $currData, previous: $prevData);
    }

    public function render()
    {
        return view('livewire.employee.kpi-view', [
            'evaluation' => $this->currentEvaluation,
            'prevEvaluation' => $this->previousEvaluation,
        ])->layout('layouts.app');
    }
}
