<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji Bulanan</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #333;
            margin: 1cm;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 0.5cm;
        }
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            font-size: 9pt;
            margin: 5px 0 0 0;
            color: #555;
        }
        .info-table {
            width: 100%;
            margin-bottom: 0.5cm;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .info-table td.label {
            width: 20%;
            color: #666;
        }
        .info-table td.colon {
            width: 2%;
        }
        .info-table td.value {
            width: 28%;
            font-weight: bold;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0.5cm;
        }
        .details-table th, .details-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .details-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
        }
        .details-table td.amount {
            text-align: right;
        }
        .total-box {
            border: 2px solid #333;
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 0.5cm;
            text-align: right;
        }
        .total-box span {
            font-size: 12pt;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 1.5cm;
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
            height: 1.5cm;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ \App\Models\AppSetting::first()->company_name ?? 'PT. ANTIGRAVITY NETWORK INDONESIA' }}</h1>
        <p>Jl. Raya Digital No. 7, Jakarta Pusat | Telp: (021) 1234567</p>
        <h2 style="font-size: 11pt; margin: 10px 0 0 0; text-transform: uppercase;">SLIP GAJI KARYAWAN</h2>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Karyawan</td>
            <td class="colon">:</td>
            <td class="value">{{ $payroll->employee->user->name }}</td>
            <td class="label">Periode Gaji</td>
            <td class="colon">:</td>
            <td class="value">{{ $payroll->month_year }}</td>
        </tr>
        <tr>
            <td class="label">NIK Internal</td>
            <td class="colon">:</td>
            <td class="value">{{ $payroll->employee->employee_id_number }}</td>
            <td class="label">Divisi</td>
            <td class="colon">:</td>
            <td class="value">{{ $payroll->employee->position->department->name }}</td>
        </tr>
        <tr>
            <td class="label">Status Kerja</td>
            <td class="colon">:</td>
            <td class="value" style="text-transform: capitalize;">{{ $payroll->employee->employment_status }}</td>
            <td class="label">Jabatan</td>
            <td class="colon">:</td>
            <td class="value">{{ $payroll->employee->position->name }}</td>
        </tr>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th colspan="2">Rincian Penerimaan (Earnings)</th>
                <th colspan="2">Rincian Pemotongan (Deductions)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 30%;">Gaji Pokok</td>
                <td class="amount" style="width: 20%;">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                <td style="width: 30%;">Potongan KPI</td>
                <td class="amount" style="width: 20%;">Rp {{ number_format($payroll->kpi_deduction, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bonus Performa KPI</td>
                <td class="amount">Rp {{ number_format($payroll->kpi_bonus, 0, ',', '.') }}</td>
                <td>Potongan Cuti (Unpaid)</td>
                <td class="amount">Rp {{ number_format($payroll->leave_deduction, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="amount"></td>
                <td>Potongan Kasbon</td>
                <td class="amount">Rp {{ number_format($payroll->cash_advance_deduction, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Earnings</td>
                <td class="amount" style="font-weight: bold;">Rp {{ number_format($payroll->base_salary + $payroll->kpi_bonus, 0, ',', '.') }}</td>
                <td style="font-weight: bold;">Total Deductions</td>
                <td class="amount" style="font-weight: bold;">Rp {{ number_format($payroll->kpi_deduction + $payroll->leave_deduction + $payroll->cash_advance_deduction, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-box">
        <span>TAKE HOME PAY (GAJI BERSIH): Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</span>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p>Penerima,</p>
            <div class="signature-space"></div>
            <p><strong>( {{ $payroll->employee->user->name }} )</strong></p>
        </div>
        
        <div class="signature-box right">
            <p>Finance Departemen,</p>
            <div class="signature-space"></div>
            <p><strong>( Keuangan Officer )</strong></p>
        </div>
    </div>

</body>
</html>
