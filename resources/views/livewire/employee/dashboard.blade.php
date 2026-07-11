<div>
    @php $title = 'Portal Karyawan'; @endphp

    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-primary to-primary/80 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-sky-500/10 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="relative group">
                @if(!empty($employee->documents['pas_foto']) && \Storage::disk('public')->exists($employee->documents['pas_foto']))
                    <img class="h-16 w-16 rounded-full object-cover border border-white/30 shadow-inner group-hover:opacity-85 transition cursor-pointer" src="{{ asset('storage/' . $employee->documents['pas_foto']) }}" alt="">
                @else
                    <div class="h-16 w-16 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 flex items-center justify-center font-black text-2xl group-hover:opacity-85 transition cursor-pointer">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                @endif
                <label class="absolute inset-0 flex items-center justify-center bg-black/40 rounded-full opacity-0 group-hover:opacity-100 transition cursor-pointer">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <input type="file" wire:model="filePasFoto" class="hidden">
                </label>
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
    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
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
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">KPI Rerata Kumulatif</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($avgKpi / 20, 2) }} <span class="text-xs text-slate-400 font-bold">/ 5.00</span></p>
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

    <!-- KPI Analytics Section for Employee -->
    @php
        $currKpiData = $currentKpi ? [$currentKpi->kehadiran, $currentKpi->keahlian, $currentKpi->keaktifan, $currentKpi->kedisiplinan] : [0,0,0,0];
        $prevKpiData = $prevKpi ? [$prevKpi->kehadiran, $prevKpi->keahlian, $prevKpi->keaktifan, $prevKpi->kedisiplinan] : [0,0,0,0];
    @endphp
    <div class="mt-8 bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6"
         x-data="{
             initCharts() {
                 if (typeof window.Chart === 'undefined') {
                     setTimeout(() => { this.initCharts(); }, 150);
                     return;
                 }
                 const canvasRadar = document.getElementById('radarChartEmployeeOverview');
                 const canvasLine = document.getElementById('lineChartEmployeeOverview');
                 if (!canvasRadar || !canvasLine) {
                     setTimeout(() => { this.initCharts(); }, 150);
                     return;
                 }
                 const ctxRadar = canvasRadar.getContext('2d');
                 if (window.radarChartEmployeeOverview) window.radarChartEmployeeOverview.destroy();
                 window.radarChartEmployeeOverview = new Chart(ctxRadar, {
                     type: 'radar',
                     data: {
                         labels: ['Kehadiran', 'Keahlian', 'Keaktifan', 'Kedisiplinan'],
                         datasets: [
                             {
                                 label: 'Bulan Ini (' + '{{ now()->format('m-Y') }}' + ')',
                                 data: @json($currKpiData),
                                 fill: true,
                                 backgroundColor: 'rgba(14, 165, 233, 0.2)',
                                 borderColor: '#0ea5e9',
                                 pointBackgroundColor: '#0ea5e9',
                             },
                             {
                                 label: 'Bulan Sebelumnya',
                                 data: @json($prevKpiData),
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

                 const ctxLine = canvasLine.getContext('2d');
                 if (window.lineChartEmployeeOverview) window.lineChartEmployeeOverview.destroy();
                 window.lineChartEmployeeOverview = new Chart(ctxLine, {
                     type: 'line',
                     data: {
                         labels: @json($historyLabels),
                         datasets: [
                             {
                                 label: 'Kehadiran',
                                 data: @json($historyData['kehadiran']),
                                 borderColor: '#0ea5e9',
                                 backgroundColor: '#0ea5e9',
                                 tension: 0.3,
                                 fill: false
                             },
                             {
                                 label: 'Keahlian',
                                 data: @json($historyData['keahlian']),
                                 borderColor: '#10b981',
                                 backgroundColor: '#10b981',
                                 tension: 0.3,
                                 fill: false
                             },
                             {
                                 label: 'Keaktifan',
                                 data: @json($historyData['keaktifan']),
                                 borderColor: '#a855f7',
                                 backgroundColor: '#a855f7',
                                 tension: 0.3,
                                 fill: false
                             },
                             {
                                 label: 'Kedisiplinan',
                                 data: @json($historyData['kedisiplinan']),
                                 borderColor: '#f59e0b',
                                 backgroundColor: '#f59e0b',
                                 tension: 0.3,
                                 fill: false
                             },
                             {
                                 label: 'Overall Mean',
                                 data: @json($historyData['mean']),
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
         }"
         x-init="$nextTick(() => { initCharts(); })">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-4 gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Statistik & Analisis KPI Saya</h3>
                <p class="text-xs text-slate-400">Tinjau grafik jaring dan tren historis performa bulanan Anda.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left: Spider Chart -->
            <div class="lg:col-span-5 flex flex-col items-center justify-center p-4 bg-slate-50 rounded-2xl border border-slate-200/50 min-h-[300px]">
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Grafik Jaring Performa (Spider Chart)</h4>
                <div class="w-full max-w-xs h-64 relative">
                    <canvas id="radarChartEmployeeOverview"></canvas>
                </div>
            </div>

            <!-- Right: Line Chart -->
            <div class="lg:col-span-7 flex flex-col items-center justify-center p-4 bg-slate-50 rounded-2xl border border-slate-200/50 min-h-[300px]">
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Tren Performa 6 Bulan Terakhir (Line Chart)</h4>
                <div class="w-full h-64 relative">
                    <canvas id="lineChartEmployeeOverview"></canvas>
                </div>
            </div>
        </div>

        <!-- Notes & Overall Score -->
        <div class="mt-6 pt-6 border-t border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-50 pb-2">
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Ulasan & Catatan Evaluator Bulan Ini</h4>
                <span class="text-xs font-bold text-primary bg-sky-50 border border-sky-100 px-3 py-1 rounded-xl">
                    Nilai Akhir (Mean): {{ number_format(($currentKpi ? $currentKpi->score : 0) / 20, 2) }} / 5.00
                </span>
            </div>

            @if($currentKpi)
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
                                {{ $notes ?: 'Tidak ada catatan khusus.' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-center text-xs font-medium text-slate-400">
                    Data KPI Anda belum diterbitkan oleh atasan/HRD untuk bulan ini.
                </div>
            @endif
        </div>
    </div>
</div>
