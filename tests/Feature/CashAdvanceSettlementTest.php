<?php

use App\Livewire\Admin\Payroll as PayrollComponent;
use App\Models\AppSetting;
use App\Models\CashAdvance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use App\Services\PayrollService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('it correctly handles cash advance deduction linking and settlement', function () {
    // 1. Setup global settings
    AppSetting::firstOrCreate([], [
        'app_name' => 'ISP HRIS',
        'company_name' => 'PT SKYNET INDONESIA',
    ]);

    // 2. Setup Employee
    $user = User::create([
        'name' => 'John Cash',
        'email' => 'john.cash@company.com',
        'password' => bcrypt('password'),
        'role' => 'employee',
    ]);
    $dept = Department::create(['name' => 'Finance']);
    $pos = Position::create([
        'department_id' => $dept->id,
        'name' => 'Accountant',
        'base_salary' => 6000000,
    ]);
    $employee = Employee::create([
        'user_id' => $user->id,
        'position_id' => $pos->id,
        'employee_id_number' => 'EMP-FIN-01',
        'nik' => '1234567890123456',
        'phone' => '08123456789',
        'employment_status' => 'tetap',
        'join_date' => now()->format('Y-m-d'),
        'leave_quota' => 12,
    ]);

    // 3. Create an approved cash advance for Jan 2026
    $cash1 = CashAdvance::create([
        'employee_id' => $employee->id,
        'amount' => 500000,
        'date' => '2026-01-15',
        'reason' => 'Kebutuhan keluarga',
        'status' => 'approved',
    ]);

    // 4. Calculate monthly payroll for Jan 2026
    $payrollService = new PayrollService;
    $payrollJan = $payrollService->calculateMonthlyPayroll($employee, '01-2026');

    // Assert cash advance is deducted and linked
    $cash1->refresh();
    expect($payrollJan->cash_advance_deduction)->toEqual(500000)
        ->and($payrollJan->net_salary)->toEqual(5500000)
        ->and($cash1->payroll_id)->toBe($payrollJan->id)
        ->and($cash1->status)->toBe('approved'); // remains approved until payroll is approved

    // 5. Create another approved cash advance for Feb 2026
    $cash2 = CashAdvance::create([
        'employee_id' => $employee->id,
        'amount' => 300000,
        'date' => '2026-02-10',
        'reason' => 'Beli buku',
        'status' => 'approved',
    ]);

    // 6. Calculate monthly payroll for Feb 2026
    $payrollFeb = $payrollService->calculateMonthlyPayroll($employee, '02-2026');

    // Assert Feb payroll only deducts cash2, and cash1 is not double deducted
    $cash2->refresh();
    expect($payrollFeb->cash_advance_deduction)->toEqual(300000)
        ->and($payrollFeb->net_salary)->toEqual(5700000)
        ->and($cash2->payroll_id)->toBe($payrollFeb->id);

    // 7. Approve Jan 2026 payroll via Livewire Component Action
    $admin = User::create([
        'name' => 'HRD Admin',
        'email' => 'hrd@company.com',
        'password' => bcrypt('password'),
        'role' => 'hrd',
    ]);
    $this->actingAs($admin);

    Livewire::test(PayrollComponent::class)
        ->call('approvePayroll', $payrollJan->id);

    // Assert cash1 is settled, and cash2 remains approved (draft)
    $cash1->refresh();
    $cash2->refresh();
    expect($cash1->status)->toBe('settled')
        ->and($cash2->status)->toBe('approved');
});
