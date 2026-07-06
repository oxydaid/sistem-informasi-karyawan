<?php

namespace App\Services;

use App\Models\Applicant;
use App\Models\Contract;
use App\Models\Position;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContractService
{
    /**
     * Otomatis generate draf SPK dalam format PDF berdasarkan data pelamar.
     */
    public function generateDraft(Applicant $applicant, string $employmentType, int $positionId, string $startDate, ?string $endDate, float $salary): Contract
    {
        // 1. Tentukan nama file yang unik
        $fileName = 'SPK_'.Str::upper($employmentType).'_'.Str::slug($applicant->name).'_'.time().'.pdf';
        $storagePath = 'contracts/'.$fileName;

        // 2. Siapkan data yang akan di-inject ke template Blade PDF
        $position = Position::with('department')->findOrFail($positionId);
        $data = [
            'applicant' => $applicant,
            'employment_type' => $employmentType,
            'position' => $position,
            'start_date' => Carbon::parse($startDate)->translatedFormat('d F Y'),
            'end_date' => $endDate ? Carbon::parse($endDate)->translatedFormat('d F Y') : 'Selesai / Permanen',
            'salary' => $salary,
            'date' => now()->translatedFormat('d F Y'),
        ];

        // 3. Render HTML dari view Blade khusus template kontrak dan konversi ke PDF
        $pdf = Pdf::loadView('pdf.contract_template', $data);

        // 4. Simpan file PDF ke storage (disk: local / public)
        Storage::disk('public')->put($storagePath, $pdf->output());

        // 5. Catat atau perbarui data kontrak di database
        return Contract::updateOrCreate(
            ['applicant_id' => $applicant->id],
            [
                'position_id' => $positionId,
                'employment_type' => $employmentType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'salary' => $salary,
                'contract_file_path' => $storagePath,
                'is_signed' => false,
            ]
        );
    }
}
