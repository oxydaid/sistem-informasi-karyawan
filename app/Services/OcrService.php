<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OcrService
{
    /**
     * Parse KTP image and extract fields using OCR.space API.
     *
     * @param  string  $filePath  Local path to the temporary uploaded file
     * @return array Parsed KTP data fields
     */
    public function parseKtp(string $filePath): array
    {
        $settings = AppSetting::first();
        $apiKey = $settings->ocr_space_api_key ?? 'helloworld'; // fallback to public test key

        try {
            $response = Http::asMultipart()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post('https://api.ocr.space/parse/image', [
                    'apikey' => $apiKey,
                    'language' => 'auto', // Auto language detection for Engine 2 (supports Latin/Indonesian)
                    'isOverlayRequired' => 'false',
                    'OCREngine' => '2', // Engine 2 is much faster and handles tabular/ID card structures better
                ]);

            if ($response->successful()) {
                $result = $response->json();

                // Log complete raw output in debug for troubleshooting
                Log::debug('OCR Space KTP API raw response', ['result' => $result]);

                if (isset($result['ParsedResults'][0]['ParsedText'])) {
                    $text = $result['ParsedResults'][0]['ParsedText'];

                    return $this->extractKtpFields($text);
                }

                $errorMessage = $result['ErrorMessage'][0] ?? 'No text parsed from KTP image.';
                Log::warning('OCR Space returned empty parsed results', ['message' => $errorMessage]);

                return ['error' => $errorMessage];
            }

            Log::error('OCR Space API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['error' => 'Gagal menghubungi server OCR Space. Status: '.$response->status()];

        } catch (\Exception $e) {
            Log::error('OCR Service parseKtp exception', ['message' => $e->getMessage()]);

            return ['error' => 'Terjadi kesalahan sistem saat memproses OCR: '.$e->getMessage()];
        }
    }

    /**
     * Extract structured KTP details from raw OCR text using regex.
     *
     * @param  string  $text  Raw parsed text
     */
    protected function extractKtpFields(string $text): array
    {
        $data = [
            'nik' => '',
            'name' => '',
            'tempat_lahir' => '',
            'tanggal_lahir' => '',
            'jenis_kelamin' => '',
            'alamat' => '',
            'agama' => '',
            'status_kawin' => '',
            'pekerjaan' => '',
            'kewarganegaraan' => 'WNI', // default
        ];

        // 1. Extract NIK (16 consecutive digits)
        if (preg_match('/\b(\d{16})\b/', $text, $mNik)) {
            $data['nik'] = $mNik[1];
        } elseif (preg_match('/nik\D*(\d{16})/i', $text, $mNik)) {
            $data['nik'] = $mNik[1];
        }

        // Normalize text lines for clean line-by-line scanning
        $lines = explode("\n", $text);
        $cleanLines = array_map(function ($l) {
            // Trim and replace multiple spaces
            return trim(preg_replace('/\s+/', ' ', $l));
        }, $lines);
        $textNormalized = implode("\n", $cleanLines);

        // 2. Extract Name (Nama)
        if (preg_match('/nama\s*[:\-=]?\s*([^\n\r]+)/i', $textNormalized, $mName)) {
            $data['name'] = trim(preg_replace('/[^a-zA-Z\s]/', '', $mName[1]));
        }

        // 3. Extract Birth Place & Date (Tempat/Tgl Lahir)
        if (preg_match('/(?:tempat|tgl)\s*lahir\s*[:\-=]?\s*([^\n\r]+)/i', $textNormalized, $mBirth)) {
            $birthParts = explode(',', $mBirth[1]);
            if (count($birthParts) >= 2) {
                $data['tempat_lahir'] = trim($birthParts[0]);

                // Parse date from parts (e.g. DD-MM-YYYY)
                $dateStr = trim($birthParts[1]);
                if (preg_match('/(\d{2})[-–—\/](\d{2})[-–—\/](\d{4})/', $dateStr, $mDate)) {
                    // Convert to standard YYYY-MM-DD
                    $data['tanggal_lahir'] = $mDate[3].'-'.$mDate[2].'-'.$mDate[1];
                }
            } else {
                // Try searching for birth date separately in the line
                if (preg_match('/(\d{2})[-–—\/](\d{2})[-–—\/](\d{4})/', $mBirth[1], $mDate)) {
                    $data['tanggal_lahir'] = $mDate[3].'-'.$mDate[2].'-'.$mDate[1];
                    // Strip the date to get the place
                    $place = trim(str_replace($mDate[0], '', $mBirth[1]));
                    $data['tempat_lahir'] = trim(preg_replace('/[^a-zA-Z\s]/', '', $place));
                }
            }
        }

        // 4. Extract Gender (Jenis Kelamin)
        if (stripos($textNormalized, 'LAKI') !== false) {
            $data['jenis_kelamin'] = 'Laki-laki';
        } elseif (stripos($textNormalized, 'PEREMPUAN') !== false || stripos($textNormalized, 'WANITA') !== false) {
            $data['jenis_kelamin'] = 'Perempuan';
        }

        // 5. Extract Address (Alamat)
        preg_match('/alamat\s*[:\-=]?\s*([^\n\r]+)/i', $textNormalized, $mAddr);
        $address = isset($mAddr[1]) ? trim($mAddr[1]) : '';

        preg_match('/rt\s*\/?\s*rw\s*[:\-=]?\s*([^\n\r]+)/i', $textNormalized, $mRt);
        if (! empty($mRt[1])) {
            $address .= ' RT/RW: '.trim($mRt[1]);
        }

        preg_match('/kel\s*\/?\s*desa\s*[:\-=]?\s*([^\n\r]+)/i', $textNormalized, $mKel);
        if (! empty($mKel[1])) {
            $address .= ', Kel/Desa: '.trim($mKel[1]);
        }

        preg_match('/kecamatan\s*[:\-=]?\s*([^\n\r]+)/i', $textNormalized, $mKec);
        if (! empty($mKec[1])) {
            $address .= ', Kecamatan: '.trim($mKec[1]);
        }
        $data['alamat'] = $address;

        // 6. Extract Religion (Agama)
        $religions = ['islam', 'kristen', 'katolik', 'hindu', 'buddha', 'konghucu', 'khonghucu'];
        foreach ($religions as $r) {
            if (stripos($textNormalized, $r) !== false) {
                $religion = ucfirst($r);
                if ($religion === 'Khonghucu') {
                    $religion = 'Konghucu';
                }
                $data['agama'] = $religion;
                break;
            }
        }

        // 7. Extract Marital Status (Status Perkawinan)
        if (stripos($textNormalized, 'BELUM KAWIN') !== false) {
            $data['status_kawin'] = 'Belum Kawin';
        } elseif (stripos($textNormalized, 'KAWIN') !== false) {
            $data['status_kawin'] = 'Kawin';
        } elseif (stripos($textNormalized, 'CERAI HIDUP') !== false) {
            $data['status_kawin'] = 'Cerai Hidup';
        } elseif (stripos($textNormalized, 'CERAI MATI') !== false) {
            $data['status_kawin'] = 'Cerai Mati';
        } elseif (stripos($textNormalized, 'CERAI') !== false) {
            $data['status_kawin'] = 'Cerai Hidup';
        }

        // 8. Extract Occupation (Pekerjaan)
        if (preg_match('/pekerjaan\s*[:\-=]?\s*([^\n\r]+)/i', $textNormalized, $mJob)) {
            $data['pekerjaan'] = trim($mJob[1]);
        }

        // 9. Extract Nationality (Kewarganegaraan)
        if (stripos($textNormalized, 'WNI') !== false || stripos($textNormalized, 'INDONESIA') !== false) {
            $data['kewarganegaraan'] = 'WNI';
        } elseif (stripos($textNormalized, 'WNA') !== false) {
            $data['kewarganegaraan'] = 'WNA';
        }

        // Clean up empty fields or placeholders
        foreach ($data as $key => $val) {
            // Strip trash prefix characters if any
            $data[$key] = trim(preg_replace('/^[:\-=.\s]+/', '', $val));
        }

        return $data;
    }
}
