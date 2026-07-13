<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

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
     * Determine default Kehadiran score from eBilling attendance.
     */
    public function getKehadiranScore(Employee $employee, string $monthYear): int
    {
        try {
            [$month, $year] = explode('-', $monthYear);
            $month = (int) $month;
            $year = (int) $year;

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            // Determine max day to evaluate (up to today if current month, otherwise full month)
            $today = Carbon::now('Asia/Jakarta');
            if ($today->format('m-Y') === $monthYear) {
                $maxDay = min($today->day, $startDate->daysInMonth);
            } else {
                $maxDay = $startDate->daysInMonth;
            }

            // Find if there is a mapped sender name for this employee
            $prevMapping = Attendance::where('employee_id', $employee->id)
                ->whereNotNull('sender_name')
                ->first();
            $senderName = $prevMapping ? $prevMapping->sender_name : ($employee->user->name ?? '');

            // Query all attendance records for this month
            $attendances = Attendance::where(function ($q) use ($employee, $senderName) {
                $q->where('employee_id', $employee->id);
                if ($senderName) {
                    $q->orWhere('sender_name', $senderName);
                }
            })
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get()
                ->keyBy(function ($item) {
                    return $item->date->format('Y-m-d');
                });

            $presentDays = 0;
            $excusedDays = 0;

            for ($d = 1; $d <= $maxDay; $d++) {
                $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);

                if (isset($attendances[$dateStr])) {
                    $status = $attendances[$dateStr]->status;
                    if ($status === 'hadir') {
                        $presentDays++;
                    } elseif (in_array($status, ['cuti', 'izin', 'libur'])) {
                        $excusedDays++;
                    }
                }
            }

            $denominator = $maxDay - $excusedDays;

            if ($denominator <= 0) {
                return 5; // Default to full score if no expected working days
            }

            $percentage = ($presentDays / $denominator) * 100;
            $percentage = min(100, $percentage);

            if ($percentage >= 90) {
                return 5;
            } elseif ($percentage >= 80) {
                return 4;
            } elseif ($percentage >= 70) {
                return 3;
            } elseif ($percentage >= 60) {
                return 2;
            } else {
                return 1;
            }
        } catch (\Exception $e) {
            // Fallback default in case of error
            return 3;
        }
    }
}
