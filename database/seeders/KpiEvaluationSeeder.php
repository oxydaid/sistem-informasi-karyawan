<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\KpiEvaluation;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KpiEvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get evaluator (Super Admin / Manager / HRD)
        $evaluator = User::where('role', 'super_admin')->first();
        if (! $evaluator) {
            $evaluator = User::first();
        }

        // Get a default position for any newly created employees
        $position = Position::first();

        // 2. Ensure we have at least 10 employees to seed 10 KPI evaluations for this month
        $existingEmployeesCount = Employee::count();
        $needed = 10 - $existingEmployeesCount;

        if ($needed > 0) {
            for ($i = 1; $i <= $needed; $i++) {
                $num = $existingEmployeesCount + $i;
                $user = User::create([
                    'name' => 'Staff Test '.$num,
                    'email' => "staff.test{$num}@skynet.com",
                    'password' => Hash::make('password123'),
                    'role' => 'employee',
                ]);

                Employee::create([
                    'user_id' => $user->id,
                    'position_id' => $position->id,
                    'employee_id_number' => 'EMP-TEST-'.str_pad($num, 3, '0', STR_PAD_LEFT),
                    'nik' => '1234567890123'.str_pad($num, 3, '0', STR_PAD_LEFT),
                    'phone' => '081234567'.str_pad($num, 3, '0', STR_PAD_LEFT),
                    'employment_status' => 'kontrak',
                    'join_date' => '2026-01-01',
                    'leave_quota' => 12,
                    'base_salary' => 4500000,
                ]);
            }
        }

        // 3. Clear existing KPI evaluations for the current month to prevent duplicate issues
        $currentMonth = now()->format('m-Y');
        KpiEvaluation::where('month_year', $currentMonth)->delete();

        // 4. Seed exactly 10 KPI evaluations for the current month
        $employees = Employee::take(10)->get();

        // Set scores: some high (> 3), some low (< 3)
        // Score is (mean * 20).
        // If mean = 2.5, score = 50.
        // If mean = 4, score = 80.
        // Below 3 mean: underperforming_kpi (score < 60)
        $kpiTemplates = [
            // 1. Below 3 (underperforming)
            [
                'kehadiran' => 2,
                'keahlian' => 2,
                'keaktifan' => 3,
                'kedisiplinan' => 2,
                'notes' => 'Perlu ditingkatkan lagi kedisiplinan dan kehadirannya.',
            ],
            // 2. Below 3 (underperforming)
            [
                'kehadiran' => 1,
                'keahlian' => 3,
                'keaktifan' => 2,
                'kedisiplinan' => 2,
                'notes' => 'Kehadiran sangat minim dan perlu pembinaan khusus.',
            ],
            // 3. Below 3 (underperforming)
            [
                'kehadiran' => 2,
                'keahlian' => 2,
                'keaktifan' => 2,
                'kedisiplinan' => 3,
                'notes' => 'Kinerja keahlian dan keaktifan di bawah ekspektasi.',
            ],
            // 4. Above 3 (normal/good)
            [
                'kehadiran' => 4,
                'keahlian' => 4,
                'keaktifan' => 3,
                'kedisiplinan' => 4,
                'notes' => 'Kinerja baik dan stabil.',
            ],
            // 5. Above 3 (normal/good)
            [
                'kehadiran' => 5,
                'keahlian' => 4,
                'keaktifan' => 4,
                'kedisiplinan' => 5,
                'notes' => 'Sangat disiplin dan kehadiran sempurna.',
            ],
            // 6. Above 3 (normal/good)
            [
                'kehadiran' => 3,
                'keahlian' => 4,
                'keaktifan' => 3,
                'kedisiplinan' => 4,
                'notes' => 'Kinerja sesuai target bulanan.',
            ],
            // 7. Above 3 (normal/good)
            [
                'kehadiran' => 5,
                'keahlian' => 5,
                'keaktifan' => 4,
                'kedisiplinan' => 4,
                'notes' => 'Keahlian teknis luar biasa.',
            ],
            // 8. Above 3 (normal/good)
            [
                'kehadiran' => 4,
                'keahlian' => 3,
                'keaktifan' => 4,
                'kedisiplinan' => 4,
                'notes' => 'Aktif berpartisipasi dalam tim.',
            ],
            // 9. Above 3 (normal/good)
            [
                'kehadiran' => 3,
                'keahlian' => 3,
                'keaktifan' => 4,
                'kedisiplinan' => 4,
                'notes' => 'Kinerja cukup memuaskan.',
            ],
            // 10. Above 3 (normal/good)
            [
                'kehadiran' => 5,
                'keahlian' => 5,
                'keaktifan' => 5,
                'kedisiplinan' => 5,
                'notes' => 'Luar biasa, pertahankan performa terbaik ini.',
            ],
        ];

        foreach ($employees as $index => $employee) {
            $template = $kpiTemplates[$index] ?? [
                'kehadiran' => rand(3, 5),
                'keahlian' => rand(3, 5),
                'keaktifan' => rand(3, 5),
                'kedisiplinan' => rand(3, 5),
                'notes' => 'Kinerja cukup stabil.',
            ];

            $kehadiran = $template['kehadiran'];
            $keahlian = $template['keahlian'];
            $keaktifan = $template['keaktifan'];
            $kedisiplinan = $template['kedisiplinan'];
            $mean = ($kehadiran + $keahlian + $keaktifan + $kedisiplinan) / 4;
            $score = $mean * 20;

            KpiEvaluation::create([
                'employee_id' => $employee->id,
                'evaluator_id' => $evaluator->id,
                'month_year' => $currentMonth,
                'score' => $score,
                'kehadiran' => $kehadiran,
                'kehadiran_notes' => $template['notes'],
                'keahlian' => $keahlian,
                'keahlian_notes' => $template['notes'],
                'keaktifan' => $keaktifan,
                'keaktifan_notes' => $template['notes'],
                'kedisiplinan' => $kedisiplinan,
                'kedisiplinan_notes' => $template['notes'],
            ]);
        }
    }
}
