<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Perjanjian Kerja (SPK)</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #333;
            margin: 1.5cm;
        }
        .header {
            text-align: center;
            margin-bottom: 2cm;
        }
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            font-size: 10pt;
            margin: 5px 0 0 0;
            color: #666;
        }
        .section-title {
            font-weight: bold;
            text-align: center;
            margin-top: 1cm;
            margin-bottom: 0.5cm;
            text-transform: uppercase;
        }
        .content {
            text-align: justify;
            margin-bottom: 1cm;
        }
        .table-party {
            width: 100%;
            margin-bottom: 0.5cm;
            border-collapse: collapse;
        }
        .table-party td {
            padding: 4px 0;
            vertical-align: top;
        }
        .table-party td.label {
            width: 30%;
        }
        .table-party td.colon {
            width: 2%;
        }
        .table-party td.value {
            width: 68%;
        }
        .signature-section {
            margin-top: 2cm;
            width: 100%;
        }
        .signature-box {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .signature-box.right {
            text-align: right;
            float: right;
        }
        .signature-space {
            height: 2cm;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>SURAT PERJANJIAN KERJA (SPK)</h1>
        <p>Nomor: SPK/{{ strtoupper($employment_type) }}/{{ $applicant->nik }}/{{ date('Y') }}</p>
    </div>

    <div class="content">
        <p>Pada hari ini, <strong>{{ $date }}</strong>, yang bertanda tangan di bawah ini:</p>
        
        <table class="table-party">
            <tr>
                <td class="label">Nama Perusahaan</td>
                <td class="colon">:</td>
                <td class="value"><strong>{{ \App\Models\AppSetting::first()->company_name ?? 'PT. ANTIGRAVITY NETWORK INDONESIA' }}</strong></td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td class="colon">:</td>
                <td class="value">Jl. Raya Digital No. 7, Jakarta Pusat</td>
            </tr>
            <tr>
                <td class="label">Representasi</td>
                <td class="colon">:</td>
                <td class="value">HRD Departemen (selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>)</td>
            </tr>
        </table>

        <p>Dan:</p>

        <table class="table-party">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="colon">:</td>
                <td class="value"><strong>{{ $applicant->name }}</strong></td>
            </tr>
            <tr>
                <td class="label">NIK KTP</td>
                <td class="colon">:</td>
                <td class="value">{{ $applicant->nik }}</td>
            </tr>
            <tr>
                <td class="label">No. Telepon</td>
                <td class="colon">:</td>
                <td class="value">{{ $applicant->phone }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td class="colon">:</td>
                <td class="value">{{ $applicant->email }} (selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>)</td>
            </tr>
        </table>

        <p>PIHAK PERTAMA dan PIHAK KEDUA secara bersama-sama sepakat untuk mengikatkan diri dalam Perjanjian Kerja dengan ketentuan sebagai berikut:</p>

        <div class="section-title">PASAL 1: STATUS, JABATAN DAN JANGKA WAKTU</div>
        <p>PIHAK PERTAMA menerima PIHAK KEDUA bekerja sebagai <strong>{{ $position->name }}</strong> pada Divisi <strong>{{ $position->department->name }}</strong> dengan status pekerjaan <strong>{{ strtoupper($employment_type) }}</strong>. Perjanjian kerja ini berlaku terhitung mulai tanggal <strong>{{ $start_date }}</strong> sampai dengan tanggal <strong>{{ $end_date }}</strong>.</p>

        <div class="section-title">PASAL 2: HAK DAN GAJI</div>
        <p>PIHAK KEDUA berhak menerima pembayaran gaji bulanan (gaji pokok) sebesar <strong>Rp {{ number_format($salary, 0, ',', '.') }}</strong> yang akan dibayarkan pada akhir bulan kerja. PIHAK KEDUA juga berhak atas penyesuaian insentif bonus berdasarkan penilaian kinerja (KPI) bulanan serta wajib mematuhi pemotongan gaji jika melanggar ketentuan kuota cuti atau melakukan pelanggaran operasional.</p>

        <div class="section-title">PASAL 3: TATA TERTIB</div>
        <p>PIHAK KEDUA berkewajiban untuk menjalankan tugas pekerjaannya dengan penuh tanggung jawab, mematuhi arahan Kepala Divisi, menjaga kerahasiaan data perusahaan, serta tidak melakukan perbuatan yang merugikan nama baik {{ \App\Models\AppSetting::first()->company_name ?? 'PT. Antigravity Network Indonesia' }}.</p>

        <p>Demikian Surat Perjanjian Kerja ini dibuat secara sadar, tanpa paksaan, dan disetujui bersama oleh kedua belah pihak.</p>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p>PIHAK KEDUA (Pelamar)</p>
            <div class="signature-space"></div>
            <p><strong>( {{ $applicant->name }} )</strong></p>
        </div>
        
        <div class="signature-box right">
            <p>PIHAK PERTAMA (Perusahaan)</p>
            <div class="signature-space"></div>
            <p><strong>( HRD Departemen )</strong></p>
        </div>
    </div>

</body>
</html>
