<?php

namespace App\Livewire\Admin;

use App\Models\Attendance as AttendanceModel;
use App\Models\Employee;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class Attendance extends Component
{
    use WithPagination;

    public $monthYear = ''; // Format: MM-YYYY

    public $search = '';

    // Modal state variables
    public $showModal = false;

    public $modalSenderName = '';

    public $modalDay = '';

    public $modalDate = '';

    public $modalStatus = 'hadir';

    public $modalPhotoUrl = null;

    public $modalCaption = null;

    protected $queryString = ['search', 'monthYear'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingMonthYear()
    {
        $this->resetPage();
    }

    public function mount()
    {
        if (empty($this->monthYear)) {
            $this->monthYear = now()->format('m-Y'); // e.g. "07-2026"
        }
    }

    /**
     * Trigger manual API sync for today.
     */
    public function syncToday(AttendanceService $attendanceService)
    {
        try {
            $today = now('Asia/Jakarta')->format('Y-m-d');
            $result = $attendanceService->fetchAndSync($today);

            $this->dispatch('toast',
                type: 'success',
                message: "Sync berhasil! Impor baru: {$result['imported']}, Dilewati: {$result['skipped']}."
            );
        } catch (\Exception $e) {
            $this->dispatch('toast',
                type: 'error',
                message: 'Sync gagal: '.$e->getMessage()
            );
        }
    }

    /**
     * Trigger manual API sync for the entire selected month.
     */
    public function syncSelectedMonth(AttendanceService $attendanceService)
    {
        try {
            [$month, $year] = explode('-', $this->monthYear);
            $month = (int) $month;
            $year = (int) $year;

            $dateObj = Carbon::createFromDate($year, $month, 1);
            $from = $dateObj->startOfMonth()->format('Y-m-d');
            
            // Limit sync to today if the month is current month
            $today = now('Asia/Jakarta');
            if ($today->format('m-Y') === $this->monthYear) {
                $to = $today->format('Y-m-d');
            } else {
                $to = $dateObj->endOfMonth()->format('Y-m-d');
            }

            $result = $attendanceService->fetchAndSyncRange($from, $to);

            $this->dispatch('toast', 
                type: 'success', 
                message: "Sync 1 bulan berhasil! Impor baru: {$result['imported']}, Dilewati: {$result['skipped']}."
            );
        } catch (\Exception $e) {
            $this->dispatch('toast', 
                type: 'error', 
                message: "Sync bulanan gagal: " . $e->getMessage()
            );
        }
    }

    /**
     * Update the employee mapping for a given sender_name.
     */
    public function mapEmployee($senderName, $employeeId)
    {
        $empId = empty($employeeId) ? null : (int) $employeeId;

        if ($empId) {
            // Remove this sender name from any other employee first to keep it unique
            Employee::where('whatsapp_sender_name', $senderName)->update([
                'whatsapp_sender_name' => null
            ]);
            
            // Update the selected employee with this sender name
            Employee::where('id', $empId)->update([
                'whatsapp_sender_name' => $senderName
            ]);

            // Update all existing attendance records for this sender_name to point to the employee
            AttendanceModel::where('sender_name', $senderName)->update([
                'employee_id' => $empId
            ]);
        } else {
            // Clear mapping from employee
            Employee::where('whatsapp_sender_name', $senderName)->update([
                'whatsapp_sender_name' => null
            ]);
            
            // Clear mapping from all logs for this sender
            AttendanceModel::where('sender_name', $senderName)->update([
                'employee_id' => null
            ]);
        }

        $this->dispatch('toast',
            type: 'success',
            message: "Pemetaan karyawan berhasil disimpan secara permanen!"
        );
    }

    /**
     * Open modal to edit daily attendance status.
     */
    public function openEditModal($senderName, $day)
    {
        [$month, $year] = explode('-', $this->monthYear);
        $dateStr = sprintf('%s-%s-%02d', $year, $month, $day);

        $this->modalSenderName = $senderName;
        $this->modalDay = $day;
        $this->modalDate = $dateStr;

        // Retrieve existing attendance record
        $attendance = AttendanceModel::where('sender_name', $senderName)
            ->whereDate('date', $dateStr)
            ->first();

        if ($attendance) {
            $this->modalStatus = $attendance->status;
            $this->modalPhotoUrl = $attendance->photo_url;
            $this->modalCaption = $attendance->caption;
        } else {
            // Default to 'alpha' for past days, and 'libur' or 'alpha' for future/today
            $todayStr = now('Asia/Jakarta')->format('Y-m-d');
            $this->modalStatus = $dateStr < $todayStr ? 'alpha' : 'libur';
            $this->modalPhotoUrl = null;
            $this->modalCaption = null;
        }

        $this->showModal = true;
    }

    /**
     * Save the edited status.
     */
    public function saveStatus()
    {
        $attendance = AttendanceModel::where('sender_name', $this->modalSenderName)
            ->whereDate('date', $this->modalDate)
            ->first();

        // Resolve mapping
        $employeeId = $this->resolveEmployeeId($this->modalSenderName);

        if ($attendance) {
            $attendance->update([
                'status' => $this->modalStatus,
                'employee_id' => $employeeId,
            ]);
        } else {
            AttendanceModel::create([
                'sender_name' => $this->modalSenderName,
                'employee_id' => $employeeId,
                'date' => $this->modalDate,
                'status' => $this->modalStatus,
            ]);
        }

        $this->showModal = false;
        $this->dispatch('toast', type: 'success', message: 'Status absensi berhasil disimpan!');
    }

    /**
     * Helper to resolve employee_id from sender_name.
     */
    protected function resolveEmployeeId(string $senderName): ?int
    {
        // 1. Try to find employee with exact whatsapp_sender_name match
        $employee = Employee::where('whatsapp_sender_name', $senderName)->first();
        if ($employee) {
            return $employee->id;
        }

        // 2. Try direct case-insensitive name match on user's name
        $matchedEmployee = Employee::whereHas('user', function ($q) use ($senderName) {
            $q->where('name', 'like', $senderName);
        })->first();

        if ($matchedEmployee) {
            // Auto-associate it so it's saved permanently!
            $matchedEmployee->update([
                'whatsapp_sender_name' => $senderName
            ]);
            return $matchedEmployee->id;
        }

        // 3. Fallback: Try to find the most recent manual mapping for this sender_name in attendances table
        $prevMapped = AttendanceModel::where('sender_name', $senderName)
            ->whereNotNull('employee_id')
            ->orderBy('date', 'desc')
            ->first();

        if ($prevMapped) {
            Employee::where('id', $prevMapped->employee_id)->update([
                'whatsapp_sender_name' => $senderName
            ]);
            return $prevMapped->employee_id;
        }

        return null;
    }

    public function render()
    {
        // Parse month and year to calculate days in month
        [$month, $year] = explode('-', $this->monthYear);
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');

        // Fetch all active employees
        $employeesQuery = Employee::with('user');
        if ($this->search) {
            $employeesQuery->whereHas('user', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%');
            });
        }
        $allEmployees = $employeesQuery->get();

        // Fetch all attendances for the month
        $attendancesForMonth = AttendanceModel::whereBetween('date', [$startDate, $endDate])->get();
        $groupedAttendances = $attendancesForMonth->groupBy('sender_name');

        $rows = [];
        $seenSenderNames = [];

        // 1. Add all active employees as rows
        foreach ($allEmployees as $employee) {
            $senderName = $employee->whatsapp_sender_name;
            
            if ($senderName) {
                $seenSenderNames[] = $senderName;
            } else {
                $senderName = $employee->user->name;
            }

            $rows[] = (object) [
                'sender_name' => $senderName,
                'employee' => $employee,
                'employee_id' => $employee->id,
                'display_name' => $employee->user->name,
                'is_mapped' => !empty($employee->whatsapp_sender_name),
            ];
        }

        // 2. Add unmapped senders who checked in this month
        foreach ($groupedAttendances as $senderName => $records) {
            // Check if this sender name is already associated with an employee
            $isAssociated = $allEmployees->contains(function ($emp) use ($senderName) {
                return $emp->whatsapp_sender_name === $senderName;
            });

            if (!$isAssociated) {
                if ($this->search && !str_contains(strtolower($senderName), strtolower($this->search))) {
                    continue;
                }

                $rows[] = (object) [
                    'sender_name' => $senderName,
                    'employee' => null,
                    'employee_id' => null,
                    'display_name' => $senderName,
                    'is_mapped' => false,
                ];
            }
        }

        // Paginate the combined array manually
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = array_slice($rows, ($currentPage - 1) * $perPage, $perPage);

        $paginatedRows = new LengthAwarePaginator($currentItems, count($rows), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => request()->query(),
        ]);

        // Load all attendances for O(1) map lookup
        $attendanceMap = [];
        foreach ($attendancesForMonth as $att) {
            $attendanceMap[$att->sender_name][$att->date->format('Y-m-d')] = $att;
        }

        // Prepare lists for mappings
        $employeesList = Employee::with('user')->get();

        return view('livewire.admin.attendance', [
            'rows' => $paginatedRows,
            'daysInMonth' => $daysInMonth,
            'attendanceMap' => $attendanceMap,
            'employeesList' => $employeesList,
            'year' => $year,
            'month' => $month,
        ])->layout('layouts.app');
    }
}
