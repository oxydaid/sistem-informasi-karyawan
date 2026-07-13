<?php

namespace App\Console\Commands;

use App\Services\AttendanceService;
use Exception;
use Illuminate\Console\Command;

class FetchAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-attendance {date? : Tanggal absensi yang ingin ditarik (format Y-m-d), kosongkan untuk hari ini}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Melakukan fetch data absensi harian dari eBilling API dan menyimpan ke database lokal';

    /**
     * Execute the console command.
     */
    public function handle(AttendanceService $attendanceService)
    {
        $date = $this->argument('date');
        $this->info('Memulai proses sync absensi eBilling...');

        try {
            $result = $attendanceService->fetchAndSync($date);

            $this->info('--------------------------------------------------');
            $this->info('Tanggal Sync      : '.$result['date']);
            $this->info('Total di API      : '.$result['total_from_api']);
            $this->info('Berhasil Diimpor  : '.$result['imported']);
            $this->info('Dilewati (Skip)   : '.$result['skipped']);
            $this->info('--------------------------------------------------');
            $this->info('Sync absensi berhasil diselesaikan!');

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('Gagal melakukan sync absensi: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
