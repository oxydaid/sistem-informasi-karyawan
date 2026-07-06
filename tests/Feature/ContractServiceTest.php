<?php

use App\Models\Applicant;
use App\Models\Department;
use App\Models\Position;
use App\Services\ContractService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('it generates contract draft spk pdf and saves record', function () {
    // 1. Setup disk fake
    Storage::fake('public');

    // 2. Setup mock data
    $dept = Department::create(['name' => 'IT Department']);
    $position = Position::create([
        'department_id' => $dept->id,
        'name' => 'Web Developer',
        'base_salary' => 6000000,
    ]);

    $applicant = Applicant::create([
        'name' => 'Agresha Hafisa',
        'nik' => '3573042604030003',
        'email' => 'agresha@company.com',
        'phone' => '081234567890',
        'status' => 'pending',
    ]);

    // 3. Run ContractService
    $service = new ContractService;
    $contract = $service->generateDraft(
        $applicant,
        'tetap', // employmentType
        $position->id,
        now()->format('Y-m-d'), // startDate
        null, // endDate (null for permanent)
        6500000 // salary
    );

    // 4. Assertions
    expect($contract)->not->toBeNull()
        ->and($contract->applicant_id)->toBe($applicant->id)
        ->and($contract->position_id)->toBe($position->id)
        ->and($contract->employment_type)->toBe('tetap')
        ->and($contract->salary)->toEqual(6500000)
        ->and($contract->is_signed)->toBeFalse();

    $this->assertDatabaseHas('contracts', [
        'applicant_id' => $applicant->id,
        'position_id' => $position->id,
        'employment_type' => 'tetap',
        'salary' => 6500000,
    ]);

    // Verify PDF file was stored in public disk
    expect($contract->contract_file_path)->not->toBeNull();
    Storage::disk('public')->assertExists($contract->contract_file_path);
});
