<div>
    @php $title = 'Evaluasi KPI Bulanan'; @endphp

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500">Nilai kinerja karyawan Anda secara bulanan. Nilai ini akan secara langsung memengaruhi bonus dan potongan pada sistem penggajian (payroll).</p>
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

    <div class="mt-6 grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Left Side: Employees List -->
        <div class="lg:col-span-7">
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
                                <th class="px-6 py-4">KPI ({{ $monthYear }})</th>
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
                                                    Skor: {{ $evaluations[$emp->id]->score }}
                                                </span>
                                                @if($evaluations[$emp->id]->bonus_adjustment > 0)
                                                    <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-1.5 py-0.5 rounded-md">+Bonus</span>
                                                @endif
                                                @if($evaluations[$emp->id]->deduction_adjustment > 0)
                                                    <span class="text-[10px] text-rose-600 font-bold bg-rose-50 px-1.5 py-0.5 rounded-md">-Potong</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400 font-medium">Belum dinilai</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <button type="button" wire:click="selectEmployee({{ $emp->id }})" 
                                                class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-primary shadow-sm hover:bg-slate-50 transition">
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

        <!-- Right Side: KPI Entry Form -->
        <div class="lg:col-span-5">
            @if($showForm && $selectedEmployee)
                <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Form Evaluasi KPI</h3>
                        <p class="text-xs text-slate-400">Karyawan: {{ $selectedEmployee->user->name }} | Periode: {{ $monthYear }}</p>
                    </div>

                    <form wire:submit.prevent="saveKpi" class="space-y-4">
                        <!-- Score -->
                        <div>
                            <label for="score" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Skor KPI (1 - 100)</label>
                            <input wire:model="score" id="score" type="number" min="1" max="100" placeholder="Misal: 85" 
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            @error('score') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Bonus Adjustment -->
                        <div>
                            <label for="bonus" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Bonus Insentif Tambahan (Rp)</label>
                            <input wire:model="bonus" id="bonus" type="number" placeholder="Misal: 500000" 
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            @error('bonus') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Deduction Adjustment -->
                        <div>
                            <label for="deduction" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Potongan Performa Buruk (Rp)</label>
                            <input wire:model="deduction" id="deduction" type="number" placeholder="Misal: 100000" 
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            @error('deduction') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Remarks -->
                        <div>
                            <label for="remarks" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Catatan Performa / Feedback</label>
                            <textarea wire:model="remarks" id="remarks" rows="3" placeholder="Masukkan ulasan performa kerja staf selama bulan ini..." 
                                      class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm"></textarea>
                            @error('remarks') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

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
            @else
                <div class="h-full flex flex-col items-center justify-center bg-white p-12 rounded-3xl border border-slate-200/60 shadow-sm border-dashed">
                    <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="mt-3 text-sm font-medium text-slate-400 text-center">Pilih salah satu karyawan di sebelah kiri untuk memberikan skor KPI bulanan.</span>
                </div>
            @endif
        </div>
    </div>
</div>
