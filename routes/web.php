<?php

use App\Livewire\Admin\Applicant;
use App\Livewire\Admin\AppSetting;
use App\Livewire\Admin\CashAdvance;
use App\Livewire\Admin\Contract;
use App\Livewire\Admin\Department;
use App\Livewire\Admin\Employee;
use App\Livewire\Admin\Kpi;
use App\Livewire\Admin\LeaveRequest;
use App\Livewire\Admin\ManageContract;
use App\Livewire\Admin\Overview;
use App\Livewire\Admin\Payroll;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Applicant\ApplicationDetail;
use App\Livewire\Applicant\Apply;
use App\Livewire\Auth\Login;
use App\Livewire\Employee\ContractView;
use App\Livewire\Employee\Dashboard;
use App\Livewire\Employee\KpiView;
use App\Livewire\Employee\PayrollView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Root Route (Handles intelligent redirection for guests & authenticated users)
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role === 'employee') {
            return redirect()->route('employee.dashboard');
        }

        return redirect()->route('admin.overview');
    }

    return redirect()->route('login');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::livewire('/masuk', Login::class)->name('login');
    Route::livewire('/daftar', Apply::class)->name('apply');

    // Onboarding / E-Sign Contract page for applicant (uses token)
    Route::livewire('/penerimaan/{token}', ApplicationDetail::class)->name('applicant.onboarding');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // Logout Action
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');

    // Admin & Staff Dashboard Group
    Route::prefix('admin')->group(function () {
        Route::livewire('/ringkasan', Overview::class)->name('admin.overview');
        Route::livewire('/pengaturan', AppSetting::class)->name('admin.settings');
        Route::livewire('/pengguna', UserManagement::class)->name('admin.users');
        Route::livewire('/pelamar', Applicant::class)->name('admin.applicants');
        Route::livewire('/pelamar/{applicantId}/kontrak', ManageContract::class)->name('admin.manage-contract');
        Route::livewire('/kontrak', Contract::class)->name('admin.contracts');
        Route::livewire('/karyawan', Employee::class)->name('admin.employees');
        Route::livewire('/departemen', Department::class)->name('admin.departments');
        Route::livewire('/cuti', LeaveRequest::class)->name('admin.leaves');
        Route::livewire('/kpi', Kpi::class)->name('admin.kpi');
        Route::livewire('/penggajian', Payroll::class)->name('admin.payrolls');
        Route::livewire('/kasbon', CashAdvance::class)->name('admin.cash-advances');
    });

    // Employee Dashboard Group
    Route::prefix('karyawan')->group(function () {
        Route::livewire('/dasbor', Dashboard::class)->name('employee.dashboard');
        Route::livewire('/kontrak', ContractView::class)->name('employee.contracts');
        Route::livewire('/cuti', App\Livewire\Employee\LeaveRequest::class)->name('employee.leaves');
        Route::livewire('/penggajian', PayrollView::class)->name('employee.payrolls');
        Route::livewire('/kasbon', App\Livewire\Employee\CashAdvance::class)->name('employee.cash-advances');
        Route::livewire('/kpi', KpiView::class)->name('employee.kpi');
    });
});
