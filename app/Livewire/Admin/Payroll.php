<?php

namespace App\Livewire\Admin;

use App\Models\CashAdvance;
use App\Models\Payroll as PayrollModel;
use App\Services\PayrollService;
use App\Services\WhatsappGatewayService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Payroll extends Component
{
    public $monthYear = ''; // MM-YYYY

    public function mount()
    {
        $this->monthYear = now()->format('m-Y');
    }

    public function generatePayroll(PayrollService $payrollService)
    {
        $payrollService->calculateBulkMonthlyPayroll($this->monthYear);
        session()->flash('success', 'Perhitungan gaji bulanan berhasil diproses untuk seluruh staf secara optimal!');
    }

    public function approvePayroll($id)
    {
        $payroll = PayrollModel::findOrFail($id);
        $payroll->update(['status' => 'approved']);

        CashAdvance::where('payroll_id', $payroll->id)
            ->where('status', 'approved')
            ->update(['status' => 'settled']);

        session()->flash('success', 'Gaji karyawan telah disetujui dan kasbon terkait telah diselesaikan!');
    }

    public function payPayroll($id, PayrollService $payrollService)
    {
        $payroll = PayrollModel::findOrFail($id);

        // 1. Generate PDF payslip
        $payrollService->generateSlipPdf($payroll);

        // 2. Mark as paid
        $payroll->update(['status' => 'paid']);

        session()->flash('success', 'Status gaji ditandai PAID! Slip gaji PDF telah dibuat secara resmi.');
    }

    /**
     * Send a single payslip notification via WhatsApp
     */
    public function sendPayslipWa($id)
    {
        $payroll = PayrollModel::findOrFail($id);

        if ($payroll->status !== 'paid') {
            $this->dispatch('toast', type: 'error', message: 'Notifikasi hanya dapat dikirim jika status gaji PAID.');

            return;
        }

        if (empty($payroll->payslip_file_path)) {
            $payrollService = new PayrollService;
            $payrollService->generateSlipPdf($payroll);
            $payroll->refresh();
        }

        try {
            $waService = new WhatsappGatewayService;

            $formattedMonth = Carbon::createFromFormat('m-Y', $payroll->month_year)->translatedFormat('F Y');
            $message = 'Halo *'.$payroll->employee->user->name."*,\n\n"
                .'Berikut adalah rincian slip gaji resmi Anda untuk periode *'.$formattedMonth."*:\n\n"
                .'• Gaji Pokok: Rp '.number_format($payroll->base_salary, 0, ',', '.')."\n"
                .'• Bonus/KPI: Rp '.number_format($payroll->kpi_bonus, 0, ',', '.')."\n"
                .'• Potongan KPI: Rp '.number_format($payroll->kpi_deduction, 0, ',', '.')."\n"
                .'• Potongan Cuti Unpaid: Rp '.number_format($payroll->leave_deduction, 0, ',', '.')."\n"
                .'• Potongan Kasbon: Rp '.number_format($payroll->cash_advance_deduction, 0, ',', '.')."\n"
                ."-----------------------------------\n"
                .'*Total Gaji Diterima (Net): Rp '.number_format($payroll->net_salary, 0, ',', '.')."*\n\n"
                ."File slip gaji PDF resmi telah dilampirkan bersama pesan ini.\n\n"
                ."Terima kasih,\n"
                .'ISP HRIS Team';

            $pdfUrl = asset('storage/'.$payroll->payslip_file_path);
            $fileName = 'Payslip_'.str_replace('-', '_', $payroll->month_year).'.pdf';

            $waResponse = $waService->sendMessage($payroll->employee->phone, $message, $pdfUrl, 'application/pdf', $fileName);

            if ($waResponse['status'] ?? false) {
                $this->dispatch('toast', type: 'success', message: 'Notifikasi slip gaji berhasil dikirim ke '.$payroll->employee->user->name.'!');
            } else {
                $this->dispatch('toast', type: 'error', message: 'Gagal mengirim: '.($waResponse['message'] ?? 'Gateway Offline'));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payslip WhatsApp: '.$e->getMessage());
            $this->dispatch('toast', type: 'error', message: 'Terjadi kesalahan saat menghubungkan ke gateway.');
        }
    }

    /**
     * Blast payslip notifications to all paid employees for the selected period
     */
    public function blastPayslipWa()
    {
        $payrolls = PayrollModel::with(['employee.user'])
            ->where('month_year', $this->monthYear)
            ->where('status', 'paid')
            ->get();

        if ($payrolls->isEmpty()) {
            $this->dispatch('toast', type: 'error', message: 'Tidak ada data gaji PAID untuk periode ini.');

            return;
        }

        // Disable PHP execution timeout for bulk loops
        set_time_limit(0);

        $successCount = 0;
        $failCount = 0;
        $waService = new WhatsappGatewayService;

        foreach ($payrolls as $payroll) {
            if (empty($payroll->payslip_file_path)) {
                $payrollService = new PayrollService;
                $payrollService->generateSlipPdf($payroll);
                $payroll->refresh();
            }

            try {
                $formattedMonth = Carbon::createFromFormat('m-Y', $payroll->month_year)->translatedFormat('F Y');
                $message = 'Halo *'.$payroll->employee->user->name."*,\n\n"
                    .'Berikut adalah rincian slip gaji resmi Anda untuk periode *'.$formattedMonth."*:\n\n"
                    .'• Gaji Pokok: Rp '.number_format($payroll->base_salary, 0, ',', '.')."\n"
                    .'• Bonus/KPI: Rp '.number_format($payroll->kpi_bonus, 0, ',', '.')."\n"
                    .'• Potongan KPI: Rp '.number_format($payroll->kpi_deduction, 0, ',', '.')."\n"
                    .'• Potongan Cuti Unpaid: Rp '.number_format($payroll->leave_deduction, 0, ',', '.')."\n"
                    .'• Potongan Kasbon: Rp '.number_format($payroll->cash_advance_deduction, 0, ',', '.')."\n"
                    ."-----------------------------------\n"
                    .'*Total Gaji Diterima (Net): Rp '.number_format($payroll->net_salary, 0, ',', '.')."*\n\n"
                    ."File slip gaji PDF resmi telah dilampirkan bersama pesan ini.\n\n"
                    ."Terima kasih,\n"
                    .'ISP HRIS Team';

                $pdfUrl = asset('storage/'.$payroll->payslip_file_path);
                $fileName = 'Payslip_'.str_replace('-', '_', $payroll->month_year).'.pdf';

                $waResponse = $waService->sendMessage($payroll->employee->phone, $message, $pdfUrl, 'application/pdf', $fileName);

                if ($waResponse['status'] ?? false) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to blast payslip to '.$payroll->employee->user->name.': '.$e->getMessage());
                $failCount++;
            }

            // Safe delay spacing as requested to prevent WA ban
            sleep(5);
        }

        if ($successCount > 0 && $failCount === 0) {
            $this->dispatch('toast', type: 'success', message: "Blast selesai! Seluruh {$successCount} notifikasi slip gaji berhasil dikirim.");
        } elseif ($successCount > 0 && $failCount > 0) {
            $this->dispatch('toast', type: 'warning', message: "Blast selesai dengan peringatan: {$successCount} terkirim, {$failCount} gagal.");
        } else {
            $this->dispatch('toast', type: 'error', message: "Blast gagal! Seluruh {$failCount} pengiriman slip gaji mengalami kegagalan.");
        }
    }

    public function render()
    {
        $payrolls = PayrollModel::with(['employee.user', 'employee.position.department'])
            ->where('month_year', $this->monthYear)
            ->get();

        return view('livewire.admin.payroll', [
            'payrolls' => $payrolls,
        ])->layout('layouts.app');
    }
}
