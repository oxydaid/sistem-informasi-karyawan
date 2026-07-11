<?php

namespace App\Livewire\Employee;

use App\Models\Contract;
use App\Models\Employee;
use App\Models\KpiEvaluation;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\Position;
use Livewire\Component;
use Livewire\WithFileUploads;

class Dashboard extends Component
{
    use WithFileUploads;

    public $filePasFoto;

    public function updatedFilePasFoto()
    {
        $this->validate([
            'filePasFoto' => 'required|image|max:2048',
        ], [
            'filePasFoto.image' => 'Pas foto harus berupa gambar (jpg, jpeg, png).',
            'filePasFoto.max' => 'Ukuran pas foto maksimal 2MB.',
        ]);

        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        // Store new pas foto
        $filename = 'pas_foto.'.$this->filePasFoto->getClientOriginalExtension();
        $path = $this->filePasFoto->storeAs("berkas/{$employee->nik}", $filename, 'public');

        // Update documents array
        $docs = $employee->documents ?? [];
        $docs['pas_foto'] = $path;
        $employee->update(['documents' => $docs]);

        $this->dispatch('toast', type: 'success', message: 'Pas foto profil Anda berhasil diperbarui!');
    }

    public function render()
    {
        $user = auth()->user();
        $employee = Employee::with(['position.department'])->where('user_id', $user->id)->first();

        if (! $employee) {
            $position = Position::first();
            if ($position) {
                $employee = Employee::create([
                    'user_id' => $user->id,
                    'position_id' => $position->id,
                    'employee_id_number' => 'EMP-ADM-'.str_pad($user->id, 3, '0', STR_PAD_LEFT),
                    'nik' => '99'.str_pad($user->id, 14, '0', STR_PAD_LEFT),
                    'phone' => '081200000000',
                    'employment_status' => 'tetap',
                    'join_date' => '2025-01-01',
                    'leave_quota' => 12,
                ]);
                $employee->load('position.department');
            }
        }

        if ($employee) {
            // Seed 6 months of dummy KPI data if they don't exist
            for ($i = 5; $i >= 0; $i--) {
                $m = now()->subMonths($i);
                $my = $m->format('m-Y');

                $exists = KpiEvaluation::where('employee_id', $employee->id)
                    ->where('month_year', $my)
                    ->exists();

                if (! $exists) {
                    $kehadiran = rand(4, 5);
                    $keahlian = rand(3, 5);
                    $keaktifan = rand(3, 5);
                    $kedisiplinan = rand(4, 5);
                    $mean = ($kehadiran + $keahlian + $keaktifan + $kedisiplinan) / 4;
                    $score = $mean * 20;

                    KpiEvaluation::create([
                        'employee_id' => $employee->id,
                        'evaluator_id' => $user->id,
                        'month_year' => $my,
                        'score' => $score,
                        'kehadiran' => $kehadiran,
                        'kehadiran_notes' => 'Kehadiran sangat baik.',
                        'keahlian' => $keahlian,
                        'keahlian_notes' => 'Keahlian mumpuni.',
                        'keaktifan' => $keaktifan,
                        'keaktifan_notes' => 'Cukup aktif membantu tim.',
                        'kedisiplinan' => $kedisiplinan,
                        'kedisiplinan_notes' => 'Disiplin dan tepat waktu.',
                    ]);
                }
            }
        }

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

        // KPI Analytics for logged-in employee
        $currentKpi = KpiEvaluation::where('employee_id', $employee->id)
            ->where('month_year', now()->format('m-Y'))
            ->first();

        $prevMonth = now()->subMonth()->format('m-Y');
        $prevKpi = KpiEvaluation::where('employee_id', $employee->id)
            ->where('month_year', $prevMonth)
            ->first();

        // Last 6 months history
        $historyLabels = [];
        $historyData = [
            'kehadiran' => [],
            'keahlian' => [],
            'keaktifan' => [],
            'kedisiplinan' => [],
            'mean' => [],
        ];

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

        return view('livewire.employee.dashboard', [
            'employee' => $employee,
            'contract' => $contract,
            'avgKpi' => $avgKpi,
            'leavesCount' => $leavesCount,
            'recentPayrolls' => $recentPayrolls,
            'currentKpi' => $currentKpi,
            'prevKpi' => $prevKpi,
            'historyLabels' => $historyLabels,
            'historyData' => $historyData,
        ])->layout('layouts.app');
    }
}
