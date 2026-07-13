<?php

namespace App\Console\Commands;

use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Exception;

class FetchAttendanceMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-attendance-month {month? : Bulan yang ingin ditarik (format MM-YYYY atau YYYY-MM), kosongkan untuk bulan ini}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Melakukan fetch data absensi satu bulan penuh dari eBilling API dan menyimpan ke database lokal';

    /**
     * Execute the console command.
     */
    public function handle(AttendanceService $attendanceService)
    {
        $monthInput = $this->argument('month');
        $this->info("Memulai proses sync absensi bulanan eBilling...");

        try {
            if (empty($monthInput)) {
                $now = Carbon::now('Asia/Jakarta');
                $month = $now->month;
                $year = $now->year;
            } else {
                // Support both MM-YYYY and YYYY-MM
                if (str_contains($monthInput, '-')) {
                    $parts = explode('-', $monthInput);
                    if (strlen($parts[0]) == 4) { // YYYY-MM
                        $year = (int)$parts[0];
                        $month = (int)$parts[1];
                    } else { // MM-YYYY
                        $month = (int)$parts[0];
                        $year = (int)$parts[1];
                    }
                } else {
                    throw new Exception("Format bulan tidak dikenal. Gunakan MM-YYYY atau YYYY-MM.");
                }
            }

            $dateObj = Carbon::createFromDate($year, $month, 1);
            $from = $dateObj->startOfMonth()->format('Y-m-d');
            
            // Limit the sync end date to today if it is the current month
            $today = Carbon::now('Asia/Jakarta');
            if ($today->format('m-Y') === sprintf('%02d-%04d', $month, $year)) {
                $to = $today->format('Y-m-d');
            } else {
                $to = $dateObj->endOfMonth()->format('Y-m-d');
            }

            $this->info("Menghubungi API eBilling untuk rentang: {$from} s/d {$to}...");
            $result = $attendanceService->fetchAndSyncRange($from, $to);
            
            $this->info("--------------------------------------------------");
            $this->info("Rentang Tanggal   : " . $result['from'] . " s/d " . $result['to']);
            $this->info("Total di API      : " . $result['total_from_api']);
            $this->info("Berhasil Diimpor  : " . $result['imported']);
            $this->info("Dilewati (Skip)   : " . $result['skipped']);
            $this->info("--------------------------------------------------");
            $this->info("Sync absensi bulanan berhasil diselesaikan!");
            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error("Gagal melakukan sync absensi bulanan: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
