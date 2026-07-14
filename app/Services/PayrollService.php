<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\CashAdvance;
use App\Models\Employee;
use App\Models\KpiEvaluation;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PayrollService
{
    /**
     * Calculate monthly payroll for a given employee and month-year.
     */
    public function calculateMonthlyPayroll(Employee $employee, string $monthYear): Payroll
    {
        // 1. Get base salary from employee (fallback to position base salary if empty)
        $baseSalary = ! empty($employee->base_salary) && $employee->base_salary > 0
            ? $employee->base_salary
            : ($employee->position->base_salary ?? 0);

        // 2. KPI adjustments (no longer needed in calculation, set to 0)
        $kpiBonus = 0;
        $kpiDeduction = 0;

        // 3. Fetch approved leaves with unpaid days in this month
        // We parse the month and year from the monthYear string (format: MM-YYYY)
        [$month, $year] = explode('-', $monthYear);

        $unpaidDaysCount = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved_hrd')
            ->whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->sum('unpaid_days');

        // Unpaid leave deduction: unpaid_days * leave_deduction_amount from app settings
        $settings = AppSetting::first();
        $deductionAmount = $settings->leave_deduction_amount ?? 50000;
        $leaveDeduction = $unpaidDaysCount * $deductionAmount;

        // 3.5 Fetch approved, unpaid cash advances up to this month
        $existingPayroll = Payroll::where('employee_id', $employee->id)
            ->where('month_year', $monthYear)
            ->first();

        if ($existingPayroll) {
            CashAdvance::where('payroll_id', $existingPayroll->id)->update(['payroll_id' => null]);
        }

        $endOfMonth = Carbon::createFromFormat('m-Y', $monthYear)->endOfMonth()->format('Y-m-d');
        $cashAdvancesToDeduct = CashAdvance::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereNull('payroll_id')
            ->where('date', '<=', $endOfMonth)
            ->get();

        $cashAdvanceDeduction = $cashAdvancesToDeduct->sum('amount');

        // 4. Net salary calculation
        $netSalary = ($baseSalary + $kpiBonus) - $kpiDeduction - $leaveDeduction - $cashAdvanceDeduction;
        if ($netSalary < 0) {
            $netSalary = 0;
        }

        // 5. Update or Create payroll record
        $payroll = Payroll::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'month_year' => $monthYear,
            ],
            [
                'base_salary' => $baseSalary,
                'kpi_bonus' => $kpiBonus,
                'kpi_deduction' => $kpiDeduction,
                'leave_deduction' => $leaveDeduction,
                'cash_advance_deduction' => $cashAdvanceDeduction,
                'net_salary' => $netSalary,
                'status' => 'draft',
            ]
        );

        if ($cashAdvancesToDeduct->isNotEmpty()) {
            CashAdvance::whereIn('id', $cashAdvancesToDeduct->pluck('id'))->update(['payroll_id' => $payroll->id]);
        }

        return $payroll;
    }

    /**
     * Calculate monthly payroll in bulk for all employees for a given month-year.
     * Uses optimized bulk queries and memory indexes to handle 1,000+ employees efficiently.
     */
    public function calculateBulkMonthlyPayroll(string $monthYear): void
    {
        [$month, $year] = explode('-', $monthYear);
        $endOfMonth = Carbon::createFromFormat('m-Y', $monthYear)->endOfMonth()->format('Y-m-d');
        $daysInMonth = Carbon::createFromFormat('m-Y', $monthYear)->daysInMonth;

        // 1. Bulk load all KPIs for this period
        $kpis = KpiEvaluation::where('month_year', $monthYear)->get()->keyBy('employee_id');

        // 2. Bulk load all leave requests for this period and sum total unpaid days
        $leaves = LeaveRequest::where('status', 'approved_hrd')
            ->whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->selectRaw('employee_id, SUM(unpaid_days) as total_unpaid')
            ->groupBy('employee_id')
            ->pluck('total_unpaid', 'employee_id');

        // 3. Clear existing relations of cash advances linked to payrolls of this month
        $existingPayrollIds = Payroll::where('month_year', $monthYear)->pluck('id');
        if ($existingPayrollIds->isNotEmpty()) {
            CashAdvance::whereIn('payroll_id', $existingPayrollIds)->update(['payroll_id' => null]);
        }

        // Load all approved cash advances up to this period that are not linked to any payroll
        $cashAdvancesGrouped = CashAdvance::where('status', 'approved')
            ->whereNull('payroll_id')
            ->where('date', '<=', $endOfMonth)
            ->get()
            ->groupBy('employee_id');

        // Load leave deduction amount from settings
        $settings = AppSetting::first();
        $deductionAmount = $settings->leave_deduction_amount ?? 50000;

        // 4. Chunk process active employees only to keep memory low
        Employee::where('is_active', true)->with(['position'])->chunk(100, function ($employees) use ($monthYear, $leaves, $cashAdvancesGrouped, $deductionAmount) {
            foreach ($employees as $employee) {
                // Base salary from employee (fallback to position base salary if empty)
                $baseSalary = ! empty($employee->base_salary) && $employee->base_salary > 0
                    ? $employee->base_salary
                    : ($employee->position->base_salary ?? 0);

                // KPI adjustments (no longer needed, set to 0)
                $kpiBonus = 0;
                $kpiDeduction = 0;

                // Leaves deduction
                $unpaidDaysCount = $leaves->get($employee->id, 0);
                $leaveDeduction = $unpaidDaysCount * $deductionAmount;

                // Cash advance deduction
                $employeeCashAdvances = $cashAdvancesGrouped->get($employee->id, collect());
                $cashAdvanceDeduction = $employeeCashAdvances->sum('amount');

                // Take home pay / net salary
                $netSalary = ($baseSalary + $kpiBonus) - $kpiDeduction - $leaveDeduction - $cashAdvanceDeduction;
                if ($netSalary < 0) {
                    $netSalary = 0;
                }

                // Update or create payroll record
                $payroll = Payroll::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'month_year' => $monthYear,
                    ],
                    [
                        'base_salary' => $baseSalary,
                        'kpi_bonus' => $kpiBonus,
                        'kpi_deduction' => $kpiDeduction,
                        'leave_deduction' => $leaveDeduction,
                        'cash_advance_deduction' => $cashAdvanceDeduction,
                        'net_salary' => $netSalary,
                        'status' => 'draft',
                    ]
                );

                // Link them
                if ($employeeCashAdvances->isNotEmpty()) {
                    CashAdvance::whereIn('id', $employeeCashAdvances->pluck('id'))->update(['payroll_id' => $payroll->id]);
                }
            }
        });
    }

    /**
     * Generate Payslip PDF for a payroll record.
     */
    public function generateSlipPdf(Payroll $payroll): string
    {
        $payroll->load('employee.user', 'employee.position.department');

        $fileName = 'payslip_'.$payroll->employee->employee_id_number.'_'.str_replace('-', '_', $payroll->month_year).'.pdf';
        $storagePath = 'payslips/'.$fileName;

        $pdf = Pdf::loadView('pdf.payslip_template', ['payroll' => $payroll]);

        Storage::disk('public')->put($storagePath, $pdf->output());

        $payroll->update(['payslip_file_path' => $storagePath]);

        return $storagePath;
    }
}
