<?php

namespace App\Livewire\Admin;

use App\Models\Employee;
use App\Models\KpiEvaluation;
use App\Models\Position;
use App\Services\KpiService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Kpi extends Component
{
    use WithPagination;

    public $monthYear = ''; // MM-YYYY (default to current month)

    // Search & Filter
    public $search = '';

    public $filterPosition = '';

    // Form inputs
    public $selectedEmployeeId = null;

    public $selectedEmployee = null;

    // Metrics scale 1-5
    public $kehadiran = 0;

    public $kehadiran_notes = '';

    public $keahlian = 3;

    public $keahlian_notes = '';

    public $keaktifan = 0;

    public $keaktifan_notes = '';

    public $kedisiplinan = 0;

    public $kedisiplinan_notes = '';

    public $showForm = false;

    // Detail modal state
    public $showDetailModal = false;

    public $detailEmployee = null;

    public $detailEvaluation = null;

    public $detailPrevEvaluation = null;

    protected $queryString = ['search', 'filterPosition'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterPosition()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'kehadiran' => 'required|integer|min:1|max:5',
            'kehadiran_notes' => 'nullable|string|max:1000',
            'keahlian' => 'required|integer|min:1|max:5',
            'keahlian_notes' => 'nullable|string|max:1000',
            'keaktifan' => 'required|integer|min:1|max:5',
            'keaktifan_notes' => 'nullable|string|max:1000',
            'kedisiplinan' => 'required|integer|min:1|max:5',
            'kedisiplinan_notes' => 'nullable|string|max:1000',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'kehadiran' => 'Kehadiran',
            'kehadiran_notes' => 'Catatan Kehadiran',
            'keahlian' => 'Keahlian',
            'keahlian_notes' => 'Catatan Keahlian',
            'keaktifan' => 'Keaktifan',
            'keaktifan_notes' => 'Catatan Keaktifan',
            'kedisiplinan' => 'Kedisiplinan',
            'kedisiplinan_notes' => 'Catatan Kedisiplinan',
        ];
    }

    public function mount()
    {
        $this->monthYear = now()->format('m-Y'); // e.g. "07-2026"
    }

    public function selectEmployee($id)
    {
        $this->selectedEmployeeId = $id;
        $this->selectedEmployee = Employee::with('user')->findOrFail($id);

        // Load existing evaluation for this employee and month-year if it exists
        $existing = KpiEvaluation::where('employee_id', $id)
            ->where('month_year', $this->monthYear)
            ->first();

        if ($existing) {
            $this->kehadiran = $existing->kehadiran;
            $this->kehadiran_notes = $existing->kehadiran_notes;
            $this->keahlian = $existing->keahlian;
            $this->keahlian_notes = $existing->keahlian_notes;
            $this->keaktifan = $existing->keaktifan;
            $this->keaktifan_notes = $existing->keaktifan_notes;
            $this->kedisiplinan = $existing->kedisiplinan;
            $this->kedisiplinan_notes = $existing->kedisiplinan_notes;
        } else {
            // Defaults
            $kpiService = new KpiService;
            $this->keahlian = $kpiService->getKeahlianScore($this->selectedEmployee);
            $this->kehadiran = $kpiService->getKehadiranScore($this->selectedEmployee, $this->monthYear);

            $this->keaktifan = 0;
            $this->kedisiplinan = 0;

            $this->kehadiran_notes = '';
            $this->keahlian_notes = '';
            $this->keaktifan_notes = '';
            $this->kedisiplinan_notes = '';
        }

        $this->showForm = true;
    }

    public function saveKpi()
    {
        $this->validate();

        // Calculate aggregate score: average of 4 metrics mapped to 100-scale
        $aggregateScore = ($this->kehadiran + $this->keahlian + $this->keaktifan + $this->kedisiplinan) / 4 * 20;

        KpiEvaluation::updateOrCreate(
            [
                'employee_id' => $this->selectedEmployeeId,
                'month_year' => $this->monthYear,
            ],
            [
                'evaluator_id' => auth()->id(),
                'score' => $aggregateScore,
                'kehadiran' => $this->kehadiran,
                'kehadiran_notes' => $this->kehadiran_notes,
                'keahlian' => $this->keahlian,
                'keahlian_notes' => $this->keahlian_notes,
                'keaktifan' => $this->keaktifan,
                'keaktifan_notes' => $this->keaktifan_notes,
                'kedisiplinan' => $this->kedisiplinan,
                'kedisiplinan_notes' => $this->kedisiplinan_notes,
            ]
        );

        $this->showForm = false;
        $this->reset(['selectedEmployeeId', 'selectedEmployee', 'kehadiran', 'kehadiran_notes', 'keahlian', 'keahlian_notes', 'keaktifan', 'keaktifan_notes', 'kedisiplinan', 'kedisiplinan_notes']);
        $this->dispatch('toast', type: 'success', message: 'Penilaian KPI bulanan berhasil disimpan!');
    }

    public function viewDetail($id)
    {
        $this->detailEmployee = Employee::with('user')->findOrFail($id);

        $this->detailEvaluation = KpiEvaluation::where('employee_id', $id)
            ->where('month_year', $this->monthYear)
            ->first();

        // Load previous month's evaluation for comparison
        $prevMonth = Carbon::createFromFormat('m-Y', $this->monthYear)->subMonth()->format('m-Y');
        $this->detailPrevEvaluation = KpiEvaluation::where('employee_id', $id)
            ->where('month_year', $prevMonth)
            ->first();

        $this->showDetailModal = true;

        // Extract radar datasets
        $currData = $this->detailEvaluation ? [
            $this->detailEvaluation->kehadiran,
            $this->detailEvaluation->keahlian,
            $this->detailEvaluation->keaktifan,
            $this->detailEvaluation->kedisiplinan,
        ] : [0, 0, 0, 0];

        $prevData = $this->detailPrevEvaluation ? [
            $this->detailPrevEvaluation->kehadiran,
            $this->detailPrevEvaluation->keahlian,
            $this->detailPrevEvaluation->keaktifan,
            $this->detailPrevEvaluation->kedisiplinan,
        ] : [0, 0, 0, 0];

        $this->dispatch('renderRadarChart', current: $currData, previous: $prevData);
    }

    public function render()
    {
        $user = auth()->user();
        $query = Employee::with(['user', 'position.department'])
            ->where('is_active', true)
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->whereHas('user', function ($u) {
                        $u->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%');
                    })->orWhere('employee_id_number', 'like', '%'.$this->search.'%')
                        ->orWhere('nik', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterPosition, function ($q) {
                $q->where('position_id', $this->filterPosition);
            });

        // If manager, only show employees of the same department
        if ($user->role === 'manager') {
            $managerEmp = Employee::where('user_id', $user->id)->first();
            if ($managerEmp) {
                $deptId = $managerEmp->position->department_id;
                $query->whereHas('position', function ($q) use ($deptId) {
                    $q->where('department_id', $deptId);
                })->where('user_id', '!=', $user->id); // don't evaluate oneself
            }
        }

        $employees = $query->paginate(5);

        // Load existing evaluations for this month to display in table
        $evaluations = KpiEvaluation::where('month_year', $this->monthYear)
            ->get()
            ->keyBy('employee_id');

        $positions = Position::with('department')->get();

        return view('livewire.admin.kpi', [
            'employees' => $employees,
            'evaluations' => $evaluations,
            'positions' => $positions,
        ])->layout('layouts.app');
    }
}
