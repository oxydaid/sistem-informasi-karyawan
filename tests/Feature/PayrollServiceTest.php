<?php

use App\Models\AppSetting;
use App\Models\Department;
use App\Models\Employee;
use App\Models\KpiEvaluation;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\Position;
use App\Models\User;
use App\Services\PayrollService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it calculates payroll correctly using settings leave deduction amount', function () {
    // 1. Setup settings
    AppSetting::firstOrCreate([], [
        'app_name' => 'ISP HRIS',
        'company_name' => 'PT SKYNET INDONESIA',
        'leave_deduction_amount' => 60000, // 60k per day
    ]);

    // 2. Setup user and department/position
    $user = User::create([
        'name' => 'John Doe',
        'email' => 'john@doe.com',
        'password' => bcrypt('password'),
        'role' => 'employee',
    ]);

    $dept = Department::create(['name' => 'IT']);
    $pos = Position::create([
        'department_id' => $dept->id,
        'name' => 'Developer',
        'base_salary' => 5000000,
    ]);

    $employee = Employee::create([
        'user_id' => $user->id,
        'position_id' => $pos->id,
        'employee_id_number' => 'EMP-0001',
        'nik' => '1234567890123456',
        'phone' => '08123456789',
        'address' => 'Test Street',
        'employment_status' => 'tetap',
        'join_date' => now()->format('Y-m-d'),
        'leave_quota' => 12,
    ]);

    // 3. Create approved leave requests with unpaid days
    LeaveRequest::create([
        'employee_id' => $employee->id,
        'start_date' => now()->format('Y-m-01'),
        'end_date' => now()->format('Y-m-03'),
        'reason' => 'Family business',
        'status' => 'approved_hrd',
        'days_requested' => 3,
        'unpaid_days' => 3,
    ]);

    // 4. Calculate monthly payroll
    $payrollService = new PayrollService;
    $monthYear = now()->format('m-Y');
    $payroll = $payrollService->calculateMonthlyPayroll($employee, $monthYear);

    // Leave deduction = 3 days * 60,000 = 180,000
    // Net salary = 5,000,000 - 180,000 = 4,820,000
    expect($payroll->base_salary)->toEqual(5000000)
        ->and($payroll->leave_deduction)->toEqual(180000)
        ->and($payroll->net_salary)->toEqual(4820000);
});

test('it calculates bulk payroll correctly for all employees', function () {
    // 1. Setup settings
    AppSetting::firstOrCreate([], [
        'app_name' => 'ISP HRIS',
        'company_name' => 'PT SKYNET INDONESIA',
        'leave_deduction_amount' => 50000,
    ]);

    $dept = Department::create(['name' => 'IT']);
    $pos = Position::create([
        'department_id' => $dept->id,
        'name' => 'Developer',
        'base_salary' => 4000000,
    ]);

    // Setup two employees
    $user1 = User::create([
        'name' => 'John',
        'email' => 'john@company.com',
        'password' => bcrypt('password'),
        'role' => 'employee',
    ]);
    $emp1 = Employee::create([
        'user_id' => $user1->id,
        'position_id' => $pos->id,
        'employee_id_number' => 'EMP-001',
        'nik' => '1111111111111111',
        'phone' => '08123456781',
        'employment_status' => 'tetap',
        'join_date' => now()->format('Y-m-d'),
        'leave_quota' => 12,
    ]);

    $user2 = User::create([
        'name' => 'Jane',
        'email' => 'jane@company.com',
        'password' => bcrypt('password'),
        'role' => 'employee',
    ]);
    $emp2 = Employee::create([
        'user_id' => $user2->id,
        'position_id' => $pos->id,
        'employee_id_number' => 'EMP-002',
        'nik' => '2222222222222222',
        'phone' => '08123456782',
        'employment_status' => 'tetap',
        'join_date' => now()->format('Y-m-d'),
        'leave_quota' => 12,
    ]);

    // KPI adjustment for emp1
    KpiEvaluation::create([
        'employee_id' => $emp1->id,
        'evaluator_id' => $user1->id, // self/test evaluator
        'month_year' => now()->format('m-Y'),
        'score' => 85,
        'bonus_adjustment' => 200000,
        'deduction_adjustment' => 0,
    ]);

    // Unpaid leave for emp2
    LeaveRequest::create([
        'employee_id' => $emp2->id,
        'start_date' => now()->format('Y-m-01'),
        'end_date' => now()->format('Y-m-02'),
        'reason' => 'Sakit',
        'status' => 'approved_hrd',
        'days_requested' => 2,
        'unpaid_days' => 2,
    ]);

    $payrollService = new PayrollService;
    $monthYear = now()->format('m-Y');
    $payrollService->calculateBulkMonthlyPayroll($monthYear);

    // Verify emp1 payroll: Base 4M + 200K KPI Bonus = 4.2M
    $p1 = Payroll::where('employee_id', $emp1->id)->where('month_year', $monthYear)->first();
    expect($p1)->not->toBeNull()
        ->and($p1->base_salary)->toEqual(4000000)
        ->and($p1->kpi_bonus)->toEqual(200000)
        ->and($p1->net_salary)->toEqual(4200000);

    // Verify emp2 payroll: Base 4M - 2 days unpaid (100k) = 3.9M
    $p2 = Payroll::where('employee_id', $emp2->id)->where('month_year', $monthYear)->first();
    expect($p2)->not->toBeNull()
        ->and($p2->base_salary)->toEqual(4000000)
        ->and($p2->leave_deduction)->toEqual(100000)
        ->and($p2->net_salary)->toEqual(3900000);
});
