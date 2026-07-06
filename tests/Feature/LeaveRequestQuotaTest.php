<?php

use App\Livewire\Admin\LeaveRequest as AdminLeaveRequestComponent;
use App\Livewire\Employee\LeaveRequest as EmployeeLeaveRequestComponent;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('it processes leave request quota deductions and unpaid days calculation correctly', function () {
    // 1. Setup Employee
    $user = User::create([
        'name' => 'John Leave',
        'email' => 'john.leave@company.com',
        'password' => bcrypt('password'),
        'role' => 'employee',
    ]);
    $dept = Department::create(['name' => 'IT']);
    $pos = Position::create([
        'department_id' => $dept->id,
        'name' => 'Staff',
        'base_salary' => 5000000,
    ]);
    $employee = Employee::create([
        'user_id' => $user->id,
        'position_id' => $pos->id,
        'employee_id_number' => 'EMP-IT-01',
        'nik' => '1234567890123457',
        'phone' => '08123456789',
        'employment_status' => 'tetap',
        'join_date' => now()->format('Y-m-d'),
        'leave_quota' => 12,
    ]);

    // 2. Submit leave request via Employee Livewire Component (3 days)
    $this->actingAs($user);

    Livewire::test(EmployeeLeaveRequestComponent::class)
        ->set('startDate', now()->format('Y-m-d'))
        ->set('endDate', now()->addDays(2)->format('Y-m-d')) // 3 days total
        ->set('reason', 'Liburan keluarga')
        ->call('submitRequest')
        ->assertHasNoErrors();

    // Verify draft leave request in DB
    $this->assertDatabaseHas('leave_requests', [
        'employee_id' => $employee->id,
        'days_requested' => 3,
        'status' => 'pending',
        'unpaid_days' => 0,
    ]);

    $req1 = LeaveRequest::where('employee_id', $employee->id)->first();

    // 3. Approve leave request via Admin Livewire Component
    $admin = User::create([
        'name' => 'HRD Admin',
        'email' => 'hrd@company.com',
        'password' => bcrypt('password'),
        'role' => 'hrd',
    ]);
    $this->actingAs($admin);

    Livewire::test(AdminLeaveRequestComponent::class)
        ->call('approve', $req1->id);

    // Verify quota is deducted and unpaid_days remains 0
    $employee->refresh();
    $req1->refresh();
    expect($employee->leave_quota)->toBe(9)
        ->and($req1->status)->toBe('approved_hrd')
        ->and($req1->unpaid_days)->toBe(0);

    // 4. Submit an excess leave request (10 days) via Admin component manually
    Livewire::test(AdminLeaveRequestComponent::class)
        ->set('employeeId', $employee->id)
        ->set('startDate', now()->addMonth()->format('Y-m-01'))
        ->set('endDate', now()->addMonth()->format('Y-m-10')) // 10 days total
        ->set('reason', 'Urusan mendesak panjang')
        ->set('status', 'approved_hrd') // Approve directly
        ->call('createLeaveRequest')
        ->assertHasNoErrors();

    // Verify direct quota deduction and unpaid days calculation
    $employee->refresh();
    expect($employee->leave_quota)->toBe(0); // Quota was 9, requested 10, so new quota is 0

    $req2 = LeaveRequest::orderBy('id', 'desc')->first();
    expect($req2->unpaid_days)->toBe(1); // 10 requested - 9 quota = 1 unpaid day

    // 5. Delete the excess leave request, verifying quota is restored
    Livewire::test(AdminLeaveRequestComponent::class)
        ->call('confirmDelete', $req2->id)
        ->call('deleteLeaveRequest');

    $employee->refresh();
    // Quota should restore from 0 back to 9 (restores days_requested - unpaid_days = 10 - 1 = 9)
    expect($employee->leave_quota)->toBe(9);
});
