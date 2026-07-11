<?php

use App\Livewire\Admin\Kpi as AdminKpiComponent;
use App\Livewire\Employee\KpiView as EmployeeKpiComponent;
use App\Models\Department;
use App\Models\Employee;
use App\Models\KpiEvaluation;
use App\Models\Position;
use App\Models\User;
use App\Services\KpiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('it calculates default keahlian score based on certificates correctly', function () {
    $user = User::create([
        'name' => 'John Service',
        'email' => 'john.service@company.com',
        'password' => bcrypt('password'),
        'role' => 'employee',
    ]);
    $dept = Department::create(['name' => 'IT']);
    $pos = Position::create([
        'department_id' => $dept->id,
        'name' => 'Staff',
        'base_salary' => 5000000,
    ]);

    // 1. Without certificate -> default 3
    $empWithoutCert = Employee::create([
        'user_id' => $user->id,
        'position_id' => $pos->id,
        'employee_id_number' => 'EMP-01',
        'nik' => '1234567890123451',
        'phone' => '08123456781',
        'employment_status' => 'tetap',
        'join_date' => now()->format('Y-m-d'),
        'documents' => [],
    ]);

    $service = new KpiService;
    expect($service->getKeahlianScore($empWithoutCert))->toBe(3);

    // 2. With certificate -> score 5
    $empWithCert = Employee::create([
        'user_id' => $user->id,
        'position_id' => $pos->id,
        'employee_id_number' => 'EMP-02',
        'nik' => '1234567890123452',
        'phone' => '08123456782',
        'employment_status' => 'tetap',
        'join_date' => now()->format('Y-m-d'),
        'documents' => ['sertifikat' => 'certificates/some-cert.pdf'],
    ]);

    expect($service->getKeahlianScore($empWithCert))->toBe(5);
});

test('admin can save KPI evaluation with 4 keys and notes', function () {
    $admin = User::create([
        'name' => 'HRD Admin',
        'email' => 'hrd@company.com',
        'password' => bcrypt('password'),
        'role' => 'hrd',
    ]);

    $user = User::create([
        'name' => 'Jane Employee',
        'email' => 'jane@company.com',
        'password' => bcrypt('password'),
        'role' => 'employee',
    ]);

    $dept = Department::create(['name' => 'Sales']);
    $pos = Position::create([
        'department_id' => $dept->id,
        'name' => 'Staff',
        'base_salary' => 4500000,
    ]);

    $employee = Employee::create([
        'user_id' => $user->id,
        'position_id' => $pos->id,
        'employee_id_number' => 'EMP-SL-01',
        'nik' => '1234567890123453',
        'phone' => '08123456783',
        'employment_status' => 'tetap',
        'join_date' => now()->format('Y-m-d'),
    ]);

    $this->actingAs($admin);

    Livewire::test(AdminKpiComponent::class)
        ->call('selectEmployee', $employee->id)
        ->set('kehadiran', 4)
        ->set('kehadiran_notes', 'Sangat rajin')
        ->set('keahlian', 5)
        ->set('keahlian_notes', 'Sertifikasi lengkap')
        ->set('keaktifan', 3)
        ->set('keaktifan_notes', 'Cukup aktif')
        ->set('kedisiplinan', 4)
        ->set('kedisiplinan_notes', 'Disiplin waktu')
        ->call('saveKpi')
        ->assertHasNoErrors();

    // Average score should be: (4 + 5 + 3 + 4) / 4 = 4.0. Out of 100: 4.0 * 20 = 80
    $this->assertDatabaseHas('kpi_evaluations', [
        'employee_id' => $employee->id,
        'score' => 80,
        'kehadiran' => 4,
        'kehadiran_notes' => 'Sangat rajin',
        'keahlian' => 5,
        'keahlian_notes' => 'Sertifikasi lengkap',
        'keaktifan' => 3,
        'keaktifan_notes' => 'Cukup aktif',
        'kedisiplinan' => 4,
        'kedisiplinan_notes' => 'Disiplin waktu',
    ]);
});

test('employee can view their own KPI evaluations', function () {
    $user = User::create([
        'name' => 'Worker Bee',
        'email' => 'worker@company.com',
        'password' => bcrypt('password'),
        'role' => 'employee',
    ]);

    $dept = Department::create(['name' => 'IT']);
    $pos = Position::create([
        'department_id' => $dept->id,
        'name' => 'Staff',
        'base_salary' => 4500000,
    ]);

    $employee = Employee::create([
        'user_id' => $user->id,
        'position_id' => $pos->id,
        'employee_id_number' => 'EMP-IT-02',
        'nik' => '1234567890123454',
        'phone' => '08123456784',
        'employment_status' => 'tetap',
        'join_date' => now()->format('Y-m-d'),
    ]);

    $eval = KpiEvaluation::create([
        'employee_id' => $employee->id,
        'evaluator_id' => $user->id,
        'month_year' => now()->format('m-Y'),
        'score' => 90,
        'kehadiran' => 5,
        'keaktifan' => 4,
        'kedisiplinan' => 4,
        'keahlian' => 5,
    ]);

    $this->actingAs($user);

    Livewire::test(EmployeeKpiComponent::class)
        ->assertSet('monthYear', now()->format('m-Y'))
        ->assertSet('currentEvaluation.id', $eval->id)
        ->assertDispatched('renderEmployeeRadarChart');
});
