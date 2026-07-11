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

    // Verify quota is untouched and unpaid_days is equal to days requested (3)
    $employee->refresh();
    $req1->refresh();
    expect($employee->leave_quota)->toBe(12)
        ->and($req1->status)->toBe('approved_hrd')
        ->and($req1->unpaid_days)->toBe(3);

    // 4. Submit a leave request (10 days) via Admin component manually
    Livewire::test(AdminLeaveRequestComponent::class)
        ->set('employeeId', $employee->id)
        ->set('startDate', now()->addMonth()->format('Y-m-01'))
        ->set('endDate', now()->addMonth()->format('Y-m-10')) // 10 days total
        ->set('reason', 'Urusan mendesak panjang')
        ->set('status', 'approved_hrd') // Approve directly
        ->call('createLeaveRequest')
        ->assertHasNoErrors();

    // Verify quota remains untouched
    $employee->refresh();
    expect($employee->leave_quota)->toBe(12);

    $req2 = LeaveRequest::orderBy('id', 'desc')->first();
    expect($req2->unpaid_days)->toBe(10); // All 10 days are unpaid

    // 5. Delete the leave request, verifying quota is untouched
    Livewire::test(AdminLeaveRequestComponent::class)
        ->call('confirmDelete', $req2->id)
        ->call('deleteLeaveRequest');

    $employee->refresh();
    expect($employee->leave_quota)->toBe(12);
});
