<div>
    @php
        $title = 'Laporan Kinerja KPI Saya';
        $currKpiData = $evaluation ? [$evaluation->kehadiran, $evaluation->keahlian, $evaluation->keaktifan, $evaluation->kedisiplinan] : [0,0,0,0];
        $prevKpiData = $prevEvaluation ? [$prevEvaluation->kehadiran, $prevEvaluation->keahlian, $prevEvaluation->keaktifan, $prevEvaluation->kedisiplinan] : [0,0,0,0];
    @endphp

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500">Tinjau evaluasi kinerja bulanan Anda berdasarkan 4 pilar utama. Bandingkan performa bulan ini dengan bulan lalu.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <!-- Period Selector -->
            <div class="bg-white p-2 rounded-2xl border border-slate-200/60 shadow-sm flex items-center gap-2">
                <span class="text-xs font-bold text-slate-600 pl-2">Periode:</span>
                <select wire:model.live="monthYear" 
                        class="block rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-1.5 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-xs font-bold">
                    @for($i = 0; $i < 6; $i++)
                        @php $d = now()->subMonths($i); @endphp
                        <option value="{{ $d->format('m-Y') }}">{{ $d->translatedFormat('F Y') }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-12"
         x-data="{
             initChart() {
                 const ctx = document.getElementById('radarChartEmployee').getContext('2d');
                 if (window.myEmpRadarChart) {
                     window.myEmpRadarChart.destroy();
                 }
                 window.myEmpRadarChart = new Chart(ctx, {
                     type: 'radar',
                     data: {
                         labels: ['Kehadiran', 'Keahlian', 'Keaktifan', 'Kedisiplinan'],
                         datasets: [
                             {
                                 label: 'Bulan Ini (' + '{{ $monthYear }}' + ')',
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
             }
         }"
         x-init="initChart()"
         x-effect="
             let trigger = $wire.monthYear;
             $nextTick(() => { initChart(); });
         ">

        <!-- Left: Spider Chart -->
        <div class="lg:col-span-5 bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm flex flex-col items-center justify-center min-h-[350px]">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-6">Analisis Statistik Jaring</h3>
            <div class="w-full max-w-xs h-64 relative">
                <canvas id="radarChartEmployee"></canvas>
            </div>
        </div>

        <!-- Right: Metrics Breakdown -->
        <div class="lg:col-span-7 bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Rincian Penilaian Dimensi Kinerja</h3>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-xl">
                    Nilai Akhir (Mean): {{ number_format(($evaluation ? $evaluation->score : 0) / 20, 2) }} / 5.00
                </span>
            </div>

            @if($evaluation)
                <div class="space-y-4">
                    @php
                        $metricsList = [
                            'kehadiran' => ['label' => 'Kehadiran Staf', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'sky'],
                            'keahlian' => ['label' => 'Keahlian & Sertifikasi', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'color' => 'emerald'],
                            'keaktifan' => ['label' => 'Keaktifan & Kerja Sama', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'purple'],
                            'kedisiplinan' => ['label' => 'Kedisiplinan & Sopan Santun', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber']
                        ];
                    @endphp

                    @foreach($metricsList as $key => $info)
                        @php
                            $scoreVal = $evaluation->$key;
                            $percent = $scoreVal * 20; // 1 -> 20%, 5 -> 100%
                            $notesVal = $evaluation->{$key.'_notes'};
                        @endphp
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/50 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-{{ $info['color'] }}-50 text-{{ $info['color'] }}-600">
                                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $info['icon'] }}" />
                                        </svg>
                                    </span>
                                    <span class="text-xs font-bold text-slate-800">{{ $info['label'] }}</span>
                                </div>
                                <span class="text-xs font-extrabold text-slate-900 bg-white border border-slate-200/60 px-2.5 py-1 rounded-lg">
                                    {{ $scoreVal }} / 5
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="space-y-1">
                                <div class="h-2 w-full rounded-full bg-slate-200 overflow-hidden">
                                    <div class="h-full rounded-full bg-primary" style="width: {{ $percent }}%;"></div>
                                </div>
                            </div>

                            <!-- Note -->
                            <div class="text-[11px] text-slate-500 leading-relaxed bg-white p-2.5 rounded-xl border border-slate-200/40 font-semibold">
                                <strong class="text-[10px] text-slate-400 uppercase tracking-wider block mb-1">Catatan Evaluator:</strong>
                                {{ $notesVal ?: 'Tidak ada catatan khusus.' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="h-full flex flex-col items-center justify-center p-12 bg-slate-50 rounded-2xl border border-slate-200/60 border-dashed text-center">
                    <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="mt-3 text-xs font-bold text-slate-400">Belum Ada Evaluasi</span>
                    <p class="text-[10px] text-slate-400 mt-1 max-w-xs">Data KPI Anda belum diterbitkan oleh atasan/HRD untuk periode {{ $monthYear }}.</p>
                </div>
            @endif
        </div>
    </div>
</div>
