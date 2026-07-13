<div>
    @php $title = 'Overview HRIS'; @endphp
    
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500">Selamat datang kembali! Berikut ringkasan statistik dan aktivitas sistem manajemen karyawan Anda.</p>
        </div>
    </div>

    <!-- Stats Card Grid -->
    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5">
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

        <!-- Stat Item 5: Underperforming KPI (Mean < 3) -->
        <div class="overflow-hidden bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider text-rose-500">KPI Perlu Evaluasi</p>
                <p class="text-2xl font-black text-rose-600 mt-1">{{ $stats['underperforming_kpi'] }}</p>
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
    <!-- Admin's Own KPI Analytics Section -->
    @if($employee)
        @php
            $currKpiData = $currentKpi ? [$currentKpi->kehadiran, $currentKpi->keahlian, $currentKpi->keaktifan, $currentKpi->kedisiplinan] : [0,0,0,0];
            $prevKpiData = $prevKpi ? [$prevKpi->kehadiran, $prevKpi->keahlian, $prevKpi->keaktifan, $prevKpi->kedisiplinan] : [0,0,0,0];
        @endphp
        <div class="mt-8 bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-4 gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Statistik & Analisis KPI Anda</h3>
                    <p class="text-xs text-slate-400">Analisis visual spider chart dan line chart per dimensi penilaian untuk kinerja Anda.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8" wire:ignore>
                <!-- Left: Spider Chart -->
                <div class="lg:col-span-5 flex flex-col items-center justify-center p-4 bg-slate-50 rounded-2xl border border-slate-200/50 min-h-[300px]">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Perbandingan Bulan Ini vs Bulan Lalu (Spider Chart)</h4>
                    <div class="w-full max-w-xs h-64 relative">
                        <canvas id="radarChartAdminOverview"></canvas>
                    </div>
                </div>

                <!-- Right: Line Chart -->
                <div class="lg:col-span-7 flex flex-col items-center justify-center p-4 bg-slate-50 rounded-2xl border border-slate-200/50 min-h-[300px]">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Tren Performa 3 Bulan Terakhir (Line Chart)</h4>
                    <div class="w-full h-64 relative">
                        <canvas id="lineChartAdminOverview"></canvas>
                    </div>
                </div>
            </div>

            <script>
            (function() {
                var currKpiData      = @json($currKpiData);
                var prevKpiData      = @json($prevKpiData);
                var histLabels       = @json($historyLabels);
                var histKehadiran    = @json($historyData['kehadiran']);
                var histKeahlian     = @json($historyData['keahlian']);
                var histKeaktifan    = @json($historyData['keaktifan']);
                var histKedisiplinan = @json($historyData['kedisiplinan']);
                var histMean         = @json($historyData['mean']);
                var bulanIni         = '{{ now()->format('m-Y') }}';

                function initOverviewCharts() {
                    if (typeof window.Chart === 'undefined') {
                        setTimeout(initOverviewCharts, 100);
                        return;
                    }
                    var canvasRadar = document.getElementById('radarChartAdminOverview');
                    var canvasLine  = document.getElementById('lineChartAdminOverview');
                    if (!canvasRadar || !canvasLine) {
                        setTimeout(initOverviewCharts, 100);
                        return;
                    }

                    if (window._radarChartAdminOverview) {
                        window._radarChartAdminOverview.destroy();
                    }
                    window._radarChartAdminOverview = new window.Chart(canvasRadar.getContext('2d'), {
                        type: 'radar',
                        data: {
                            labels: ['Kehadiran', 'Keahlian', 'Keaktifan', 'Kedisiplinan'],
                            datasets: [
                                {
                                    label: 'Bulan Ini (' + bulanIni + ')',
                                    data: currKpiData,
                                    fill: true,
                                    backgroundColor: 'rgba(14, 165, 233, 0.2)',
                                    borderColor: '#0ea5e9',
                                    pointBackgroundColor: '#0ea5e9',
                                },
                                {
                                    label: 'Bulan Sebelumnya',
                                    data: prevKpiData,
                                    fill: true,
                                    backgroundColor: 'rgba(148, 163, 184, 0.2)',
                                    borderColor: '#94a3b8',
                                    pointBackgroundColor: '#94a3b8',
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                r: { suggestedMin: 0, suggestedMax: 5, ticks: { stepSize: 1 } }
                            }
                        }
                    });

                    if (window._lineChartAdminOverview) {
                        window._lineChartAdminOverview.destroy();
                    }
                    window._lineChartAdminOverview = new window.Chart(canvasLine.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: histLabels,
                            datasets: [
                                {
                                    label: 'Kehadiran',
                                    data: histKehadiran,
                                    borderColor: '#0ea5e9',
                                    backgroundColor: '#0ea5e9',
                                    tension: 0.3,
                                    fill: false
                                },
                                {
                                    label: 'Keahlian',
                                    data: histKeahlian,
                                    borderColor: '#10b981',
                                    backgroundColor: '#10b981',
                                    tension: 0.3,
                                    fill: false
                                },
                                {
                                    label: 'Keaktifan',
                                    data: histKeaktifan,
                                    borderColor: '#a855f7',
                                    backgroundColor: '#a855f7',
                                    tension: 0.3,
                                    fill: false
                                },
                                {
                                    label: 'Kedisiplinan',
                                    data: histKedisiplinan,
                                    borderColor: '#f59e0b',
                                    backgroundColor: '#f59e0b',
                                    tension: 0.3,
                                    fill: false
                                },
                                {
                                    label: 'Overall Mean',
                                    data: histMean,
                                    borderColor: '#ef4444',
                                    backgroundColor: '#ef4444',
                                    borderDash: [5, 5],
                                    tension: 0.3,
                                    fill: false
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { suggestedMin: 0, suggestedMax: 5, ticks: { stepSize: 1 } }
                            }
                        }
                    });
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initOverviewCharts);
                } else {
                    initOverviewCharts();
                }

                document.addEventListener('livewire:navigated', initOverviewCharts);
            })();
            </script>

            <!-- Notes & Overall Score -->
            <div class="mt-6 pt-6 border-t border-slate-100 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-50 pb-2">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Ulasan Catatan Bulan Ini</h4>
                    <span class="text-xs font-bold text-primary bg-sky-50 border border-sky-100 px-3 py-1 rounded-xl">
                        Nilai Akhir (Mean): {{ number_format(($currentKpi ? $currentKpi->score : 0) / 20, 2) }} / 5.00
                    </span>
                </div>

                @if($currentKpi)
                    @if(($currentKpi->score / 20) < 3)
                        <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-start gap-3">
                            <div class="flex-shrink-0 text-rose-600 bg-white p-1.5 rounded-xl border border-rose-100 shadow-sm">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-rose-800">Evaluasi Performa & Peningkatan Diri</h5>
                                <p class="text-xs text-rose-600/90 mt-1 leading-relaxed">Nilai rata-rata KPI Anda bulan ini berada di bawah standar target (3.00 / 5.00). Harap lakukan evaluasi diri pada aspek-aspek yang kurang optimal, tetap semangat, dan mari bersama-sama tingkatkan performa kerja di bulan berikutnya!</p>
                            </div>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @php
                            $keys = [
                                'kehadiran' => ['label' => 'Kehadiran', 'color' => 'sky'],
                                'keahlian' => ['label' => 'Keahlian', 'color' => 'emerald'],
                                'keaktifan' => ['label' => 'Keaktifan', 'color' => 'purple'],
                                'kedisiplinan' => ['label' => 'Kedisiplinan', 'color' => 'amber']
                            ];
                        @endphp

                        @foreach($keys as $k => $info)
                            @php
                                $val = $currentKpi ? $currentKpi->$k : 0;
                                $notes = $currentKpi ? $currentKpi->{$k.'_notes'} : '';
                            @endphp
                            <div class="p-3 bg-slate-50 border border-slate-200/40 rounded-xl space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-800">{{ $info['label'] }}</span>
                                    <span class="text-xs font-extrabold text-primary">{{ $val }} / 5</span>
                                </div>
                                <div class="text-[11px] text-slate-500 leading-relaxed italic bg-white p-2 rounded-lg border border-slate-100 min-h-[50px]">
                                    {{ $notes ?: 'Tidak ada catatan.' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-center text-xs font-medium text-slate-400">
                        Data KPI Anda belum diterbitkan oleh atasan untuk bulan ini.
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
