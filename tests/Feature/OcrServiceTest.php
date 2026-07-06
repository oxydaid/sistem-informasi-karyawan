<?php

use App\Services\OcrService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('it can parse ktp response from ocr space', function () {
    Http::fake([
        'api.ocr.space/*' => Http::response([
            'ParsedResults' => [
                [
                    'ParsedText' => "NIK: 3573042604030003\nNama: AGHATA HAFIS MAHARDIKA\nTempat/Tgl Lahir: MALANG, 26-04-2003\nJenis kelamin: LAKELAKI\nAlamat: JL. RAYA CANDI III-E /202\nAgama: ISLAM\nStatus Perkawinan: BELUM KAWIN\nPekerjaan: PELAJAR/MAHASISWA\nKewarganegaraan: WNI",
                ],
            ],
            'OCRExitCode' => 1,
            'IsErroredOnProcessing' => false,
        ]),
    ]);

    $ocr = new OcrService;

    // Create a mock temporary file
    $tempFile = tempnam(sys_get_temp_dir(), 'ktp_test');
    file_put_contents($tempFile, 'fake image data');

    $result = $ocr->parseKtp($tempFile);

    @unlink($tempFile);

    expect($result)->toBeArray()
        ->and($result['nik'])->toBe('3573042604030003')
        ->and($result['name'])->toBe('AGHATA HAFIS MAHARDIKA')
        ->and($result['tempat_lahir'])->toBe('MALANG')
        ->and($result['tanggal_lahir'])->toBe('2003-04-26')
        ->and($result['jenis_kelamin'])->toBe('Laki-laki')
        ->and($result['status_kawin'])->toBe('Belum Kawin')
        ->and($result['kewarganegaraan'])->toBe('WNI');
});
