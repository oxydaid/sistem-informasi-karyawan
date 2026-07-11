<?php

namespace App\Services;

use App\Models\Employee;

class KpiService
{
    /**
     * Determine default Keahlian score based on certs.
     * Scale 1-5: 5 if cert exists, 3 otherwise.
     */
    public function getKeahlianScore(Employee $employee): int
    {
        $docs = $employee->documents ?? [];
        if (! empty($docs['sertifikat'])) {
            return 5;
        }

        return 3;
    }

    /**
     * Determine default Kehadiran score (for future API integrations).
     */
    public function getKehadiranScore(Employee $employee, string $monthYear): int
    {
        // Placeholder for future integrations, default to 4 for now
        return 4;
    }
}
