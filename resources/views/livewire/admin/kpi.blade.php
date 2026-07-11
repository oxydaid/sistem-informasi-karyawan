<div>
    @php $title = 'Evaluasi KPI Bulanan'; @endphp

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500">Nilai kinerja karyawan Anda secara bulanan berdasarkan 4 dimensi utama: Kehadiran, Keahlian, Keaktifan, dan Kedisiplinan.</p>
        </div>
    </div>

    <!-- Month & Year Selector -->
    <div class="mt-8 bg-white p-4 rounded-3xl border border-slate-200/60 shadow-sm flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <span class="text-sm font-bold text-slate-700">Periode Penilaian:</span>
            <select wire:model.live="monthYear" 
                    class="block rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                @for($i = 0; $i < 6; $i++)
                    @php $d = now()->subMonths($i); @endphp
                    <option value="{{ $d->format('m-Y') }}">{{ $d->translatedFormat('F Y') }}</option>
                @endfor
            </select>
        </div>
    </div>

    <!-- Single Full-Width Table Layout -->
    <div class="mt-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <h3 class="text-base font-bold text-slate-900">Daftar Karyawan</h3>
            </div>

            <!-- Search & Filter Jabatan (Rounded full) -->
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                        <svg class="h-4.5 w-4.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau NIK..." 
                           class="block w-full rounded-full border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                </div>
                
                <select wire:model.live="filterPosition" 
                        class="block rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                    <option value="">Semua Jabatan</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">
                        <tr>
                            <th class="px-6 py-4">Karyawan</th>
                            <th class="px-6 py-4">Jabatan</th>
                            <th class="px-6 py-4">Rerata KPI (1-5)</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($employees as $emp)
                            @php
                                $words = explode(' ', $emp->user->name ?? 'K');
                                $initials = '';
                                if (count($words) >= 2) {
                                    $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                } else {
                                    $initials = strtoupper(substr($words[0], 0, 2));
                                }
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-600 border border-slate-200 text-xs">
                                            {{ $initials }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-bold text-slate-900 text-sm">{{ $emp->user->name ?? 'Tidak Ada User' }}</div>
                                            <div class="text-xs text-slate-400 mt-0.5">{{ $emp->employee_id_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="font-bold text-slate-800 text-sm">{{ $emp->position->name ?? '-' }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $emp->position->department->name ?? '-' }}</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if(isset($evaluations[$emp->id]))
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center h-8 px-3 rounded-xl text-xs font-bold bg-sky-50 text-sky-700">
                                                Rerata: {{ number_format($evaluations[$emp->id]->score / 20, 2) }} / 5.00
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium">Belum dinilai</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                    @if(isset($evaluations[$emp->id]))
                                        <button type="button" wire:click="viewDetail({{ $emp->id }})" 
                                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                                            Detail Chart
                                        </button>
                                    @endif
                                    <button type="button" wire:click="selectEmployee({{ $emp->id }})" 
                                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-primary shadow-sm hover:bg-slate-50 transition">
                                        {{ isset($evaluations[$emp->id]) ? 'Edit Nilai' : 'Beri Nilai' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                    Tidak ada karyawan terdaftar untuk dinilai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($employees->hasPages())
                <div class="pt-4 border-t border-slate-100">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Entry Form Modal -->
    @if($showForm && $selectedEmployee)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 md:p-8 space-y-6">
                <div class="flex items-center justify-between border-b border-dashed border-slate-200 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Form Evaluasi KPI</h3>
                        <p class="text-xs text-slate-400">Karyawan: {{ $selectedEmployee->user->name }} | Periode: {{ $monthYear }}</p>
                    </div>
                    <button type="button" wire:click="$set('showForm', false)" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveKpi" class="space-y-5">
                    @php
                        $metrics = [
                            'kehadiran' => 'Kehadiran (Skala 1-5)',
                            'keahlian' => 'Keahlian (Skala 1-5)',
                            'keaktifan' => 'Keaktifan (Skala 1-5)',
                            'kedisiplinan' => 'Kedisiplinan (Skala 1-5)'
                        ];
                    @endphp

                    @foreach($metrics as $key => $label)
                        <div class="space-y-2 p-3 rounded-2xl bg-slate-50 border border-slate-200/40">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">{{ $label }} <span class="text-rose-500">*</span></label>
                            
                            <!-- Rating Cards -->
                            <div class="flex gap-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <label class="flex-1 text-center cursor-pointer select-none">
                                        <input type="radio" wire:model="{{ $key }}" value="{{ $i }}" class="sr-only peer">
                                        <div class="py-2 rounded-xl border border-slate-200 bg-white peer-checked:border-primary peer-checked:bg-sky-50 text-xs font-extrabold text-slate-700 peer-checked:text-primary hover:bg-slate-50/50 transition">
                                            {{ $i }}
                                        </div>
                                    </label>
                                @endfor
                            </div>
                            @error($key) <span class="text-[10px] text-rose-600 font-semibold block mt-0.5">{{ $message }}</span> @enderror

                            <!-- Note -->
                            <div class="mt-2">
                                <input type="text" wire:model="{{ $key }}_notes" placeholder="Tulis catatan penunjang untuk nilai ini..."
                                       class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-900 placeholder-slate-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition font-semibold">
                                @error($key.'_notes') <span class="text-[10px] text-rose-600 font-semibold block mt-0.5">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endforeach

                    <!-- Actions -->
                    <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="$set('showForm', false)" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-primary px-5 py-2 text-xs font-semibold text-white shadow-md shadow-sky-500/20 hover:bg-primary/95 transition">
                            Simpan Penilaian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Detail KPI Modal with Spider Chart -->
    @if($showDetailModal && $detailEmployee)
        @php
            $currKpiData = $detailEvaluation ? [$detailEvaluation->kehadiran, $detailEvaluation->keahlian, $detailEvaluation->keaktifan, $detailEvaluation->kedisiplinan] : [0,0,0,0];
            $prevKpiData = $detailPrevEvaluation ? [$detailPrevEvaluation->kehadiran, $detailPrevEvaluation->keahlian, $detailPrevEvaluation->keaktifan, $detailPrevEvaluation->kedisiplinan] : [0,0,0,0];
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
             x-data x-init="
                $nextTick(() => {
                    const ctx = document.getElementById('radarChartAdmin').getContext('2d');
                    if (window.myRadarChart) {
                        window.myRadarChart.destroy();
                    }
                    window.myRadarChart = new Chart(ctx, {
                        type: 'radar',
                        data: {
                            labels: ['Kehadiran', 'Keahlian', 'Keaktifan', 'Kedisiplinan'],
                            datasets: [
                                {
                                    label: 'Bulan Ini (' + $wire.monthYear + ')',
                                    data: @json($currKpiData),
                                    fill: true,
                                    backgroundColor: 'rgba(14, 165, 233, 0.2)',
                                    borderColor: '#0ea5e9',
                                    pointBackgroundColor: '#0ea5e9',
                                    pointBorderColor: '#fff',
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: '#0ea5e9'
                                },
                                {
                                    label: 'Bulan Sebelumnya',
                                    data: @json($prevKpiData),
                                    fill: true,
                                    backgroundColor: 'rgba(148, 163, 184, 0.2)',
                                    borderColor: '#94a3b8',
                                    pointBackgroundColor: '#94a3b8',
                                    pointBorderColor: '#fff',
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: '#94a3b8'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                r: {
                                    angleLines: { display: true },
                                    suggestedMin: 0,
                                    suggestedMax: 5,
                                    ticks: { stepSize: 1 }
                                }
                            }
                        }
                    });
                });
             ">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto p-6 md:p-8 space-y-6">
                <div class="flex items-center justify-between border-b border-dashed border-slate-200 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Analisis Kinerja KPI Karyawan</h3>
                        <p class="text-xs text-slate-400">Karyawan: {{ $detailEmployee->user->name }} | ID: {{ $detailEmployee->employee_id_number }}</p>
                    </div>
                    <button type="button" wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Left: Spider Chart -->
                    <div class="flex flex-col items-center justify-center p-4 bg-slate-50 rounded-2xl border border-slate-200/50">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Statistik Jaring (Spider Chart)</h4>
                        <div class="w-full max-w-xs h-64 relative">
                            <canvas id="radarChartAdmin"></canvas>
                        </div>
                    </div>

                    <!-- Right: Key Metrics & Comments -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Metrik Detail & Catatan</h4>
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">
                                Nilai Akhir (Mean): {{ number_format(($detailEvaluation ? $detailEvaluation->score : 0) / 20, 2) }} / 5.00
                            </span>
                        </div>

                        @php
                            $keysList = [
                                'kehadiran' => ['label' => 'Kehadiran', 'color' => 'sky'],
                                'keahlian' => ['label' => 'Keahlian', 'color' => 'emerald'],
                                'keaktifan' => ['label' => 'Keaktifan', 'color' => 'purple'],
                                'kedisiplinan' => ['label' => 'Kedisiplinan', 'color' => 'amber']
                            ];
                        @endphp

                        @foreach($keysList as $k => $info)
                            @php
                                $val = $detailEvaluation ? $detailEvaluation->$k : 0;
                                $notes = $detailEvaluation ? $detailEvaluation->{$k.'_notes'} : '';
                            @endphp
                            <div class="p-3 bg-slate-50 border border-slate-200/40 rounded-xl space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-800">{{ $info['label'] }}</span>
                                    <span class="text-xs font-extrabold text-primary">{{ $val }} / 5</span>
                                </div>
                                <div class="text-[11px] text-slate-500 leading-relaxed italic bg-white p-2 rounded-lg border border-slate-100">
                                    {{ $notes ?: 'Tidak ada catatan khusus.' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="button" wire:click="$set('showDetailModal', false)"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200/80 text-xs font-semibold text-slate-700 rounded-xl transition">
                        Tutup Detail
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
