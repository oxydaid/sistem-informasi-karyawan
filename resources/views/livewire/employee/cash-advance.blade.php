<div>
    @php $title = 'Kasbon Saya'; @endphp

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500 font-medium">Ajukan kasbon baru atau lihat riwayat peminjaman kasbon Anda beserta status pemotongannya.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button type="button" wire:click="openRequestModal" 
                    class="block w-full sm:w-auto rounded-2xl bg-primary px-4 py-2.5 text-center text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">
                Ajukan Kasbon
            </button>
        </div>
    </div>


    <!-- Month Filter (Responsive with AlpineJS) -->
    <div x-data="{ openFilter: false }">
        <!-- Desktop View (Inline) -->
        <div class="hidden md:flex mt-8 bg-white p-4 rounded-3xl border border-slate-200/60 shadow-sm items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-slate-700">Filter:</span>
                <!-- Month Select -->
                <select wire:model.live="filterMonthOnly" 
                        class="block rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm font-semibold">
                    <option value="">Semua Bulan</option>
                    <option value="01">Januari</option>
                    <option value="02">Februari</option>
                    <option value="03">Maret</option>
                    <option value="04">April</option>
                    <option value="05">Mei</option>
                    <option value="06">Juni</option>
                    <option value="07">Juli</option>
                    <option value="08">Agustus</option>
                    <option value="09">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>

                <!-- Year Select -->
                <select wire:model.live="filterYearOnly" 
                        class="block rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm font-semibold">
                    <option value="">Semua Tahun</option>
                    @for($y = now()->year; $y >= 2024; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <!-- Mobile Floating Action Button (FAB) for FILTER -->
        <button type="button" @click="openFilter = true" 
                class="md:hidden fixed bottom-6 right-6 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-primary text-white shadow-lg shadow-sky-500/35 hover:scale-105 active:scale-95 transition focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
        </button>

        <!-- Mobile Filter Modal -->
        <div x-show="openFilter" class="relative z-50 md:hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div x-show="openFilter" 
                 x-transition:enter="transition-opacity ease-linear duration-200" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="transition-opacity ease-linear duration-150" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="openFilter" 
                         x-transition:enter="transition ease-out duration-300 transform" 
                         x-transition:enter-start="translate-y-10 opacity-0" 
                         x-transition:enter-end="translate-y-0 opacity-100" 
                         x-transition:leave="transition ease-in duration-200 transform" 
                         x-transition:leave-start="translate-y-0 opacity-100" 
                         x-transition:leave-end="translate-y-10 opacity-0" 
                         @click.away="openFilter = false" 
                         class="relative transform overflow-hidden rounded-3xl bg-white p-6 text-left shadow-2xl transition-all w-full max-w-sm border border-slate-100">
                        
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-sm font-bold text-slate-900">Filter Riwayat</h3>
                            <button type="button" @click="openFilter = false" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 font-semibold">Bulan</label>
                                <select wire:model.live="filterMonthOnly" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700 focus:border-primary focus:bg-white focus:outline-none transition font-medium">
                                    <option value="">Semua Bulan</option>
                                    <option value="01">Januari</option>
                                    <option value="02">Februari</option>
                                    <option value="03">Maret</option>
                                    <option value="04">April</option>
                                    <option value="05">Mei</option>
                                    <option value="06">Juni</option>
                                    <option value="07">Juli</option>
                                    <option value="08">Agustus</option>
                                    <option value="09">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 font-semibold">Tahun</label>
                                <select wire:model.live="filterYearOnly" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700 focus:border-primary focus:bg-white focus:outline-none transition font-medium">
                                    <option value="">Semua Tahun</option>
                                    @for($y = now()->year; $y >= 2024; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="button" @click="openFilter = false" class="w-full px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl shadow transition text-center font-bold">Terapkan Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200/60 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/75 whitespace-nowrap">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Tanggal</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Jumlah</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Keterangan / Alasan</th>
                        <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold uppercase tracking-wider text-slate-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($cashAdvances as $cash)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="whitespace-nowrap px-6 py-4 text-xs font-medium text-slate-600">
                                {{ $cash->date ? $cash->date->format('d M Y') : '-' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-bold text-slate-900 text-sm">
                                Rp {{ number_format($cash->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-500 max-w-sm truncate">
                                {{ $cash->reason }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold capitalize
                                    {{ $cash->status === 'pending' ? 'bg-amber-50 text-amber-700' : '' }}
                                    {{ $cash->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                    {{ $cash->status === 'rejected' ? 'bg-rose-50 text-rose-700' : '' }}
                                    {{ $cash->status === 'settled' ? 'bg-slate-100 text-slate-700' : '' }}
                                ">
                                    {{ $cash->status === 'settled' ? 'Selesai (Dipotong)' : $cash->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="mt-2 text-sm text-slate-400 font-semibold">Anda belum pernah mengajukan kasbon.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($cashAdvances instanceof \Illuminate\Pagination\LengthAwarePaginator && $cashAdvances->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $cashAdvances->links() }}
            </div>
        @endif
    </div>

    <!-- Request Modal -->
    @if($showRequestModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showRequestModal', false)"></div>
                <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 text-left shadow-2xl transition-all border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Ajukan Kasbon Baru</h3>
                    <form wire:submit.prevent="requestCashAdvance" class="space-y-4">
                        <div>
                            <label for="amount" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah Uang (Rp)</label>
                            <input wire:model="amount" id="amount" type="number" placeholder="Misal: 500000"
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-955 focus:bg-white focus:outline-none transition text-sm">
                            @error('amount') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="date" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Dibutuhkan</label>
                            <input wire:model="date" id="date" type="date"
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-955 focus:bg-white focus:outline-none transition text-sm">
                            @error('date') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="reason" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Alasan Peminjaman</label>
                            <textarea wire:model="reason" id="reason" rows="3" placeholder="Tulis alasan meminjam kasbon..."
                                      class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-955 focus:bg-white focus:outline-none transition text-sm"></textarea>
                            @error('reason') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                            <button type="button" wire:click="$set('showRequestModal', false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl shadow transition font-semibold">Kirim Pengajuan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
