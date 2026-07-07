<?php

namespace App\Livewire\Admin;

use App\Models\Employee;
use App\Models\KpiEvaluation;
use App\Models\Position;
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

    public $score = '';

    public $bonus = '';

    public $deduction = '';

    public $remarks = '';

    public $showForm = false;

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
            'score' => 'required|integer|min:1|max:100',
            'bonus' => 'required|numeric|min:0',
            'deduction' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'score' => 'Skor KPI',
            'bonus' => 'Bonus Insentif (Rp)',
            'deduction' => 'Potongan KPI (Rp)',
            'remarks' => 'Catatan Performa',
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
            $this->score = $existing->score;
            $this->bonus = $existing->bonus_adjustment;
            $this->deduction = $existing->deduction_adjustment;
            $this->remarks = $existing->remarks;
        } else {
            $this->reset(['score', 'bonus', 'deduction', 'remarks']);
            $this->bonus = 0;
            $this->deduction = 0;
        }

        $this->showForm = true;
    }

    public function saveKpi()
    {
        $this->validate();

        KpiEvaluation::updateOrCreate(
            [
                'employee_id' => $this->selectedEmployeeId,
                'month_year' => $this->monthYear,
            ],
            [
                'evaluator_id' => auth()->id(),
                'score' => $this->score,
                'bonus_adjustment' => $this->bonus,
                'deduction_adjustment' => $this->deduction,
                'remarks' => $this->remarks,
            ]
        );

        $this->showForm = false;
        $this->reset(['selectedEmployeeId', 'selectedEmployee', 'score', 'bonus', 'deduction', 'remarks']);
        $this->dispatch('toast', type: 'success', message: 'Penilaian KPI bulanan berhasil disimpan!');
    }

    public function render()
    {
        $user = auth()->user();
        $query = Employee::with(['user', 'position.department'])
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
