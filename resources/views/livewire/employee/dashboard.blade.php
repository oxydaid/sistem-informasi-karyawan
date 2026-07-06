<div>
    @php $title = 'Portal Karyawan'; @endphp

    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-primary to-primary/80 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-sky-500/10 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 flex items-center justify-center font-black text-2xl">
                {{ substr(auth()->user()->name, 0, 2) }}
            </div>
            <div>
                <h2 class="text-2xl font-black">Selamat Datang, {{ auth()->user()->name }}!</h2>
                <p class="text-sm text-sky-100 mt-1">Portal informasi terpadu karyawan PT. Antigravity Network Indonesia.</p>
            </div>
        </div>
        <div class="text-center md:text-right">
            <span class="text-xs font-semibold uppercase tracking-wider text-sky-200 block">ID Karyawan</span>
            <span class="text-lg font-extrabold">{{ $employee->employee_id_number }}</span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Leave Quota -->
        <div class="overflow-hidden bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Sisa Kuota Cuti</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $employee->leave_quota }} Hari</p>
            </div>
        </div>

        <!-- Leaves Taken -->
        <div class="overflow-hidden bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-50 text-purple-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Cuti Disetujui</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $leavesCount }} Hari</p>
            </div>
        </div>

        <!-- KPI Average -->
        <div class="overflow-hidden bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Skor KPI Rerata</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($avgKpi, 1) }} <span class="text-xs text-slate-400 font-bold">/100</span></p>
            </div>
        </div>

        <!-- Contract Status -->
        <div class="overflow-hidden bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Status Kontrak</p>
                <p class="text-base font-extrabold text-slate-900 mt-1 capitalize">{{ $employee->employment_status }} (Aktif)</p>
            </div>
        </div>
    </div>

    <!-- Workspace Details Grid -->
    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-12">
        
        <!-- Left Side: Profile & Contract info -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
                <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Informasi Kepegawaian</h3>
                
                <div class="space-y-4 text-sm text-slate-700">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Divisi</span>
                        <span class="font-semibold text-slate-900">{{ $employee->position->department->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Jabatan</span>
                        <span class="font-semibold text-slate-900">{{ $employee->position->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">NIK KTP</span>
                        <span class="font-semibold text-slate-900">{{ $employee->nik }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">No. WhatsApp</span>
                        <span class="font-semibold text-slate-900">{{ $employee->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Tanggal Bergabung</span>
                        <span class="font-semibold text-slate-900">{{ $employee->join_date->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Active Contract Card -->
            @if($contract)
                <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Kontrak Aktif (SPK)</h3>
                    <div class="flex justify-between items-center bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div class="min-w-0">
                            <span class="text-xs font-semibold text-slate-400 block uppercase">Surat Perjanjian Kerja</span>
                            <span class="text-sm font-bold text-slate-800 truncate block">SPK_{{ strtoupper($contract->employment_type) }}</span>
                            <p class="text-[10px] text-slate-400 mt-1">Berlaku s/d {{ $contract->end_date ? $contract->end_date->format('d M Y') : 'Permanen' }}</p>
                        </div>
                        <a href="{{ asset('storage/' . $contract->contract_file_path) }}" target="_blank" 
                           class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white shadow-md shadow-sky-500/25 hover:bg-primary/95 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Side: Recent Payroll payslips -->
        <div class="lg:col-span-7">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
                <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Slip Gaji Terakhir</h3>
                
                <div class="divide-y divide-slate-100">
                    @forelse($recentPayrolls as $payroll)
                        <div class="flex items-center justify-between py-4">
                            <div>
                                <p class="text-sm font-bold text-slate-900">Gaji Periode {{ $payroll->month_year }}</p>
                                <p class="text-xs text-emerald-600 font-semibold mt-0.5">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }} | {{ $payroll->status }}</p>
                            </div>
                            @if($payroll->payslip_file_path)
                                <a href="{{ asset('storage/' . $payroll->payslip_file_path) }}" target="_blank" 
                                   class="inline-flex items-center justify-center h-8.5 px-3 rounded-xl border border-slate-200 text-xs font-semibold text-primary bg-sky-50/20 hover:bg-sky-50 transition">
                                    Unduh Slip
                                </a>
                            @else
                                <span class="text-xs text-slate-400">Slip belum diproses</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 text-center py-8">Belum ada slip gaji yang diproses.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
