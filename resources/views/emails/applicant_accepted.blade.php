<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Selamat, Anda Diterima!</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #334155; margin: 0; padding: 20px; background-color: #f8fafc; }
        .card { max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        h2 { color: #0f172a; margin-top: 0; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #0ea5e9; color: white !important; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 16px; }
        .footer { margin-top: 32px; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Selamat, {{ $applicant->name }}!</h2>
        <p>Kami dengan senang hati memberitahukan bahwa Anda telah dinyatakan lolos seleksi dan diterima untuk bergabung di perusahaan kami.</p>
        <p>Silakan klik tombol di bawah ini untuk melihat kontrak kerja (SPK) Anda, melengkapi berkas onboarding, dan membubuhkan tanda tangan digital secara resmi:</p>
        
        <p style="text-align: center;">
            <a href="{{ $onboardingUrl }}" class="btn">Lengkapi Onboarding & TTD SPK</a>
        </p>
        
        <p>Jika tombol di atas tidak berfungsi, salin dan tempel tautan berikut ke browser Anda:</p>
        <p style="word-break: break-all; font-size: 13px; color: #64748b;">{{ $onboardingUrl }}</p>
        
        <p>Terima kasih dan sampai jumpa di hari kerja pertama!</p>
        
        <div class="footer">
            Pemberitahuan otomatis dari <strong>ISP HRIS Portal</strong>. Mohon tidak membalas email ini secara langsung.
        </div>
    </div>
</body>
</html>
