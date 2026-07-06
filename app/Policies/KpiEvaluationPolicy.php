<?php

namespace App\Policies;

use App\Models\KpiEvaluation;
use App\Models\User;

class KpiEvaluationPolicy
{
    /**
     * Menentukan apakah user bisa melihat daftar evaluasi KPI.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'hrd', 'manager']);
    }

    /**
     * Menentukan apakah user bisa membuat penilaian KPI baru.
     */
    public function create(User $user): bool
    {
        // Manager hanya bisa menilai jika ada alur relasi, di sini kita izinkan hrd/admin/manager
        return in_array($user->role, ['super_admin', 'hrd', 'manager']);
    }

    /**
     * Menentukan apakah seorang karyawan biasa bisa melihat detail KPI miliknya sendiri.
     */
    public function view(User $user, KpiEvaluation $kpiEvaluation): bool
    {
        if (in_array($user->role, ['super_admin', 'hrd', 'manager'])) {
            return true;
        }

        // Karyawan hanya bisa melihat KPI miliknya sendiri
        return $user->employee && $user->employee->id === $kpiEvaluation->employee_id;
    }
}
