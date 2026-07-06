<div>
    @php $title = 'Overview HRIS'; @endphp
    
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500">Selamat datang kembali! Berikut ringkasan statistik dan aktivitas sistem manajemen karyawan Anda.</p>
        </div>
    </div>

    <!-- Stats Card Grid -->
    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Stat Item 1 -->
        <div class="overflow-hidden bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Karyawan</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_employees'] }}</p>
            </div>
        </div>

        <!-- Stat Item 2 -->
        <div class="overflow-hidden bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pelamar Baru</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['pending_applicants'] }}</p>
            </div>
        </div>

        <!-- Stat Item 3 -->
        <div class="overflow-hidden bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-50 text-purple-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Cuti Pending</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['pending_leaves'] }}</p>
            </div>
        </div>

        <!-- Stat Item 4 -->
        <div class="overflow-hidden bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Payroll Aktif</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['active_payrolls'] }}</p>
            </div>
        </div>
    </div>

    <!-- Lists Grid -->
    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Recent Applicants List -->
        <div class="lg:col-span-6 bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Pelamar Terbaru</h3>
                <a href="{{ route('admin.applicants') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentApplicants as $applicant)
                    <div class="flex items-center justify-between py-3.5">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $applicant->name }}</p>
                            <p class="text-xs text-slate-400">NIK: {{ $applicant->nik }} | WA: {{ $applicant->phone }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold capitalize
                            {{ $applicant->status === 'pending' ? 'bg-amber-50 text-amber-700' : '' }}
                            {{ $applicant->status === 'reviewed' ? 'bg-sky-50 text-sky-700' : '' }}
                            {{ $applicant->status === 'interviewing' ? 'bg-purple-50 text-purple-700' : '' }}
                            {{ $applicant->status === 'accepted' ? 'bg-emerald-50 text-emerald-700' : '' }}
                            {{ $applicant->status === 'rejected' ? 'bg-rose-50 text-rose-700' : '' }}
                        ">
                            {{ $applicant->status }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">Belum ada pelamar terdaftar.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Leave Requests List -->
        <div class="lg:col-span-6 bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Pengajuan Cuti Terkini</h3>
                <a href="{{ route('admin.leaves') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentLeaves as $leave)
                    <div class="flex items-center justify-between py-3.5">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $leave->employee->user->name }}</p>
                            <p class="text-xs text-slate-400">
                                {{ $leave->start_date->format('d M') }} s/d {{ $leave->end_date->format('d M Y') }} ({{ $leave->days_requested }} Hari)
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold capitalize
                            {{ $leave->status === 'pending' ? 'bg-amber-50 text-amber-700' : '' }}
                            {{ $leave->status === 'approved_manager' ? 'bg-purple-50 text-purple-700' : '' }}
                            {{ $leave->status === 'approved_hrd' ? 'bg-emerald-50 text-emerald-700' : '' }}
                            {{ $leave->status === 'rejected' ? 'bg-rose-50 text-rose-700' : '' }}
                        ">
                            {{ str_replace('_', ' ', $leave->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">Belum ada pengajuan cuti terdaftar.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
