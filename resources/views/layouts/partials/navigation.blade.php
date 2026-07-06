@php
    $role = auth()->user()->role ?? '';
    
    // Check helper for active state
    $isActive = function($routePatterns) {
        foreach ((array) $routePatterns as $pattern) {
            if (request()->routeIs($pattern) || request()->is($pattern . '*')) {
                return 'bg-primary text-white shadow-md shadow-sky-500/15';
            }
        }
        return 'text-slate-600 hover:bg-slate-50 hover:text-slate-900';
    };
@endphp

<!-- Shared Navigation based on Roles -->

<!-- Overview for Admins / Managers / HRD / Finance -->
@if(in_array($role, ['super_admin', 'hrd', 'finance', 'manager']))
    <a href="{{ route('admin.overview') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['admin.overview']) }}">
        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
        </svg>
        Overview
    </a>
@endif

<!-- HRD & Super Admin: Rekrutmen & Kontrak -->
@if(in_array($role, ['super_admin', 'hrd']))
    <div class="space-y-1 pt-2">
        <p class="px-4 text-xs font-bold tracking-wider text-slate-400 uppercase">Rekrutmen & Staf</p>
        
        <a href="{{ route('admin.applicants') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['admin.applicants', 'admin.manage-contract']) }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            Review Pelamar
        </a>

        <!-- Kontrak Kerja -->
        <a href="{{ route('admin.contracts') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['admin.contracts']) }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Kontrak Kerja
        </a>

        <!-- Employees List -->
        <a href="{{ route('admin.employees') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['admin.employees']) }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Data Karyawan
        </a>

        <!-- Departments & Positions -->
        <a href="{{ route('admin.departments') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['admin.departments', 'admin.positions']) }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            Divisi & Jabatan
        </a>
    </div>
@endif

<!-- Manager & HRD & Super Admin: KPI & Cuti -->
@if(in_array($role, ['super_admin', 'manager', 'hrd']))
    <div class="space-y-1 pt-2">
        <p class="px-4 text-xs font-bold tracking-wider text-slate-400 uppercase">Operasional</p>
        
        <!-- KPI Evaluations -->
        @if(in_array($role, ['super_admin', 'manager']))
            <a href="{{ route('admin.kpi') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['admin.kpi']) }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Evaluasi KPI
            </a>
        @endif

        <!-- Leaves Requests Approval -->
        <a href="{{ route('admin.leaves') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['admin.leaves']) }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Persetujuan Cuti
        </a>
    </div>
@endif

<!-- Finance & Super Admin: Payroll -->
@if(in_array($role, ['super_admin', 'finance']))
    <div class="space-y-1 pt-2">
        <p class="px-4 text-xs font-bold tracking-wider text-slate-400 uppercase">Keuangan</p>
        
        <a href="{{ route('admin.payrolls') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['admin.payrolls']) }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Manajemen Gaji
        </a>

        <a href="{{ route('admin.cash-advances') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['admin.cash-advances']) }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Kasbon Karyawan
        </a>
    </div>
@endif

<!-- Super Admin Only: System Settings -->
@if($role === 'super_admin')
    <div class="space-y-1 pt-2">
        <p class="px-4 text-xs font-bold tracking-wider text-slate-400 uppercase">Pengaturan</p>
        
        <a href="{{ route('admin.settings') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['admin.settings']) }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            App Settings
        </a>
        
        <a href="{{ route('admin.users') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['admin.users']) }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Manajemen User
        </a>
    </div>
@endif

<!-- Employee Navigation -->
@if($role === 'employee')
    <div class="space-y-1">
        <p class="px-4 text-xs font-bold tracking-wider text-slate-400 uppercase">Karyawan Menu</p>

        <a href="{{ route('employee.dashboard') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['employee.dashboard']) }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            My Dashboard
        </a>

        <a href="{{ route('employee.contracts') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['employee.contracts']) }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Status Kontrak (SPK)
        </a>

        <a href="{{ route('employee.leaves') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['employee.leaves']) }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Pengajuan Cuti
        </a>

        <a href="{{ route('employee.payrolls') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['employee.payrolls']) }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Slip Gaji Saya
        </a>

        <a href="{{ route('employee.cash-advances') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition duration-150 {{ $isActive(['employee.cash-advances']) }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Kasbon Saya
        </a>
    </div>
@endif
