<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AttendanceService
{
    protected string $baseUrl;

    protected string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.ebilling.base_url', 'https://ebilling.sky.net.id');
        $this->token = config('services.ebilling.token', '');
    }

    /**
     * Fetch attendance records for a specific date and sync with database.
     *
     * @param  string|null  $date  Format: Y-m-d
     * @return array Summary of import results
     *
     * @throws Exception
     */
    public function fetchAndSync(?string $date = null): array
    {
        if (empty($date)) {
            $date = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        }

        if (empty($this->token)) {
            throw new Exception('eBilling Attendance Token is not configured. Please set EBILLING_ATTENDANCE_TOKEN in your .env file.');
        }

        $url = rtrim($this->baseUrl, '/').'/api/attendances/export';

        Log::info("Fetching attendance from eBilling API: {$url} for date: {$date}");

        try {
            $page = 1;
            $attendances = [];
            do {
                $response = Http::withHeaders([
                    'X-Attendance-Token' => $this->token,
                    'Accept' => 'application/json',
                ])->timeout(30)->get($url, [
                    'date' => $date,
                    'per_page' => 500,
                    'page' => $page,
                ]);

                if ($response->failed()) {
                    $status = $response->status();
                    $body = $response->body();
                    Log::error("eBilling API Error (HTTP {$status}): {$body}");
                    throw new Exception("eBilling API returned error status {$status}: {$body}");
                }

                $data = $response->json();
                $pageAttendances = $data['attendances'] ?? [];
                $attendances = array_merge($attendances, $pageAttendances);
                
                $total = $data['total'] ?? 0;
                $page++;
            } while (count($attendances) < $total && count($pageAttendances) > 0);

            $importedCount = 0;
            $skippedCount = 0;

            foreach ($attendances as $item) {
                $senderName = $item['sender_name'] ?? null;
                $messageId = $item['message_id'] ?? null;
                $checkedInAtStr = $item['checked_in_at'] ?? null;

                if (! $senderName || ! $checkedInAtStr) {
                    $skippedCount++;

                    continue;
                }

                // Parse check-in time using timezone Asia/Jakarta
                $checkedInAt = Carbon::parse($checkedInAtStr)->timezone('Asia/Jakarta');
                $dateStr = $checkedInAt->format('Y-m-d');

                // Check if message_id is already imported, or if sender already has attendance on that date
                $exists = Attendance::where(function ($q) use ($messageId, $senderName, $dateStr) {
                    if ($messageId) {
                        $q->where('message_id', $messageId);
                    }
                    $q->orWhere(function ($inner) use ($senderName, $dateStr) {
                        $inner->where('sender_name', $senderName)
                            ->whereDate('date', $dateStr);
                    });
                })->first();

                if ($exists) {
                    // Skip if it already exists, keeping the existing status intact
                    $skippedCount++;

                    continue;
                }

                // Attempt to resolve employee_id
                $employeeId = $this->resolveEmployeeId($senderName);

                // Insert new daily attendance log
                Attendance::create([
                    'sender_name' => $senderName,
                    'employee_id' => $employeeId,
                    'date' => $dateStr,
                    'status' => 'hadir',
                    'message_id' => $messageId,
                    'photo_url' => $item['photo_url'] ?? null,
                    'caption' => $item['caption'] ?? null,
                ]);

                $importedCount++;
            }

            return [
                'success' => true,
                'date' => $date,
                'total_from_api' => count($attendances),
                'imported' => $importedCount,
                'skipped' => $skippedCount,
            ];

        } catch (Exception $e) {
            Log::error('Failed to sync attendance from eBilling: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch attendance records for a date range and sync with database.
     *
     * @param string $from Format: Y-m-d
     * @param string $to Format: Y-m-d
     * @return array Summary of import results
     * @throws Exception
     */
    public function fetchAndSyncRange(string $from, string $to): array
    {
        if (empty($this->token)) {
            throw new Exception("eBilling Attendance Token is not configured. Please set EBILLING_ATTENDANCE_TOKEN in your .env file.");
        }

        $url = rtrim($this->baseUrl, '/') . '/api/attendances/export';
        
        Log::info("Fetching attendance range from eBilling API: {$url} from {$from} to {$to}");

        try {
            $page = 1;
            $attendances = [];
            do {
                $response = Http::withHeaders([
                    'X-Attendance-Token' => $this->token,
                    'Accept' => 'application/json',
                ])->timeout(60)->get($url, [
                    'from' => $from,
                    'to' => $to,
                    'per_page' => 500,
                    'page' => $page,
                ]);

                if ($response->failed()) {
                    $status = $response->status();
                    $body = $response->body();
                    Log::error("eBilling API Error (HTTP {$status}): {$body}");
                    throw new Exception("eBilling API returned error status {$status}: {$body}");
                }

                $data = $response->json();
                $pageAttendances = $data['attendances'] ?? [];
                $attendances = array_merge($attendances, $pageAttendances);

                $total = $data['total'] ?? 0;
                $page++;
            } while (count($attendances) < $total && count($pageAttendances) > 0);
            
            $importedCount = 0;
            $skippedCount = 0;

            foreach ($attendances as $item) {
                $senderName = $item['sender_name'] ?? null;
                $messageId = $item['message_id'] ?? null;
                $checkedInAtStr = $item['checked_in_at'] ?? null;

                if (!$senderName || !$checkedInAtStr) {
                    $skippedCount++;
                    continue;
                }

                $checkedInAt = Carbon::parse($checkedInAtStr)->timezone('Asia/Jakarta');
                $dateStr = $checkedInAt->format('Y-m-d');

                // Check if message_id is already imported, or if sender already has attendance on that date
                $exists = Attendance::where(function ($q) use ($messageId, $senderName, $dateStr) {
                    if ($messageId) {
                        $q->where('message_id', $messageId);
                    }
                    $q->orWhere(function ($inner) use ($senderName, $dateStr) {
                        $inner->where('sender_name', $senderName)
                              ->whereDate('date', $dateStr);
                    });
                })->first();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                $employeeId = $this->resolveEmployeeId($senderName);

                Attendance::create([
                    'sender_name' => $senderName,
                    'employee_id' => $employeeId,
                    'date' => $dateStr,
                    'status' => 'hadir',
                    'message_id' => $messageId,
                    'photo_url' => $item['photo_url'] ?? null,
                    'caption' => $item['caption'] ?? null,
                ]);

                $importedCount++;
            }

            return [
                'success' => true,
                'from' => $from,
                'to' => $to,
                'total_from_api' => count($attendances),
                'imported' => $importedCount,
                'skipped' => $skippedCount,
            ];

        } catch (Exception $e) {
            Log::error("Failed to sync attendance range from eBilling: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Smartly resolve the employee ID from the sender's name.
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
        $prevMapped = Attendance::where('sender_name', $senderName)
            ->whereNotNull('employee_id')
            ->orderBy('date', 'desc')
            ->first();

        if ($prevMapped) {
            // Auto-save the found mapping back to the employee for future permanence
            Employee::where('id', $prevMapped->employee_id)->update([
                'whatsapp_sender_name' => $senderName
            ]);
            return $prevMapped->employee_id;
        }

        return null;
    }
}
