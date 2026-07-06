<?php

use App\Livewire\Admin\UserManagement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest or non super admin cannot access user management', function () {
    $user = User::create([
        'name' => 'Staff',
        'email' => 'staff@company.com',
        'password' => bcrypt('password'),
        'role' => 'hrd', // not super_admin
    ]);

    $this->actingAs($user);

    // Should abort with 403 Forbidden
    Livewire::test(UserManagement::class)
        ->assertStatus(403);
});

test('super admin can view and create user', function () {
    $admin = User::create([
        'name' => 'Super Admin',
        'email' => 'admin@company.com',
        'password' => bcrypt('password'),
        'role' => 'super_admin',
    ]);

    $this->actingAs($admin);

    Livewire::test(UserManagement::class)
        ->assertStatus(200)
        ->set('name', 'New Employee')
        ->set('email', 'new@employee.com')
        ->set('password', 'secret123')
        ->set('role', 'employee')
        ->call('createUser')
        ->assertHasNoErrors()
        ->assertSet('showCreateModal', false);

    $this->assertDatabaseHas('users', [
        'name' => 'New Employee',
        'email' => 'new@employee.com',
        'role' => 'employee',
    ]);
});
