<div>
    @php $title = 'Log Absensi Karyawan (eBilling)'; @endphp

    <div class="sm:flex sm:items-center sm:justify-between gap-4">
        <div class="flex-1">
            <p class="mt-2 text-sm text-slate-500 max-w-2xl">Rekapitulasi absensi bulanan hasil sinkronisasi API eBilling. Hubungkan nama pengirim absensi WhatsApp ke data karyawan internal, dan sesuaikan status kehadiran per hari.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex flex-wrap sm:flex-nowrap gap-2 flex-shrink-0">
            <!-- Sync Today Button -->
            <button type="button" wire:click="syncToday" 
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center rounded-2xl bg-sky-50 px-4 py-2.5 text-xs font-bold text-sky-700 border border-sky-200 hover:bg-sky-100/80 transition active:scale-[0.98]">
                <svg wire:loading wire:target="syncToday" class="animate-spin -ml-1 mr-2 h-4 w-4 text-sky-700" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Sync Hari Ini</span>
            </button>
            
            <!-- Sync Month Button -->
            <button type="button" wire:click="syncSelectedMonth" 
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center rounded-2xl bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-sky-500/25 hover:shadow-lg transition active:scale-[0.98]">
                <svg wire:loading wire:target="syncSelectedMonth" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Sync 1 Bulan Penuh</span>
            </button>
        </div>
    </div>

    <!-- Filters Area -->
    <div class="mt-8 bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <!-- Month Selector -->
        <div class="flex items-center gap-2">
            <span class="text-sm font-bold text-slate-700 whitespace-nowrap">Periode Absensi:</span>
            <select wire:model.live="monthYear" 
                    class="block rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm font-semibold">
                @for($i = 0; $i < 6; $i++)
                    @php $d = now()->subMonths($i); @endphp
                    <option value="{{ $d->format('m-Y') }}">{{ $d->translatedFormat('F Y') }}</option>
                @endfor
            </select>
        </div>

        <!-- Search Input -->
        <div class="relative flex-1 max-w-md">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                <svg class="h-4.5 w-4.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama pengirim atau karyawan..." 
                   class="block w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
        </div>
    </div>

    <!-- Legend Indicators Card -->
    <div class="mt-6 bg-slate-50 p-4 rounded-2xl border border-slate-200/40 flex flex-wrap gap-x-6 gap-y-2 text-xs font-semibold text-slate-600">
        <span class="text-slate-400 uppercase tracking-wider text-[10px] w-full mb-1">Panduan Simbol Status:</span>
        <div class="flex items-center gap-1.5">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </span>
            <span>Hadir</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-rose-50 text-rose-600 border border-rose-100 font-extrabold text-[10px]">
                ✕
            </span>
            <span>Alpha / Tidak Hadir</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-sky-50 text-sky-700 border border-sky-200/50 font-extrabold text-[11px]">
                C
            </span>
            <span>Cuti</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-50 text-amber-700 border border-amber-200/50 font-extrabold text-[11px]">
                I
            </span>
            <span>Izin</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-purple-50 text-purple-700 border border-purple-200/50 font-extrabold text-[11px]">
                L
            </span>
            <span>Libur (Hari Off)</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="text-slate-300 font-bold text-sm w-5 text-center leading-none">
                -
            </span>
            <span>Belum Hari Itu</span>
        </div>
    </div>

    <!-- Attendance Grid Table -->
    <div class="mt-6 overflow-hidden bg-white shadow-sm border border-slate-200/60 rounded-3xl">
        <div class="overflow-x-auto overflow-y-visible">
            <table class="min-w-full divide-y divide-slate-100 text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">
                    <tr>
                        <th class="px-6 py-4 min-w-[200px] sticky left-0 bg-slate-50 shadow-[2px_0_5px_rgba(0,0,0,0.02)] z-10">Nama / Pengirim</th>
                        <th class="px-6 py-4 min-w-[220px]">Pemetaan Karyawan (FK)</th>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            <th class="px-2 py-4 text-center w-8 border-l border-slate-100/50">{{ $d }}</th>
                        @endfor
                        <th class="px-6 py-4 text-center font-bold text-slate-700 border-l border-slate-200 bg-slate-50/50">Total Hadir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($rows as $row)
                        @php
                            // Hitung total hadir pada bulan terpilih
                            $totalHadir = 0;
                            if (isset($attendanceMap[$row->sender_name])) {
                                foreach ($attendanceMap[$row->sender_name] as $attRecord) {
                                    if ($attRecord->status === 'hadir') {
                                        $totalHadir++;
                                    }
                                }
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/40 transition">
                            <!-- Display Name -->
                            <td class="whitespace-nowrap px-6 py-4 font-bold text-slate-900 sticky left-0 bg-white shadow-[2px_0_5px_rgba(0,0,0,0.02)] z-10">
                                <div>{{ $row->display_name }}</div>
                                @if(!$row->is_mapped)
                                    <div class="text-[10px] font-semibold text-rose-500 mt-0.5 uppercase tracking-wide bg-rose-50 px-1.5 py-0.5 rounded-md inline-block">Belum Terpetakan</div>
                                @else
                                    <div class="text-[10px] font-semibold text-emerald-600 mt-0.5 uppercase tracking-wide bg-emerald-50 px-1.5 py-0.5 rounded-md inline-block">Terpetakan</div>
                                @endif
                            </td>

                            <!-- Mapping Selector -->
                            <td class="whitespace-nowrap px-6 py-4">
                                @if($row->employee_id)
                                    <button type="button" wire:click="openMapModal('{{ $row->sender_name }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition w-full justify-between shadow-sm">
                                        <span class="max-w-[120px] truncate text-slate-800">{{ $row->display_name }}</span>
                                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                @else
                                    <button type="button" wire:click="openMapModal('{{ $row->sender_name }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-dashed border-slate-300 bg-slate-50/50 hover:bg-slate-100 text-xs font-semibold text-primary transition w-full justify-center">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Hubungkan
                                    </button>
                                @endif
                            </td>

                            <!-- Daily Cells -->
                            @for($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $dateStr = sprintf('%s-%s-%02d', $year, $month, $d);
                                    $todayStr = now('Asia/Jakarta')->format('Y-m-d');
                                    $att = $attendanceMap[$row->sender_name][$dateStr] ?? null;
                                @endphp
                                <td class="px-1 py-3 text-center border-l border-slate-100/50">
                                    @if($att)
                                        @if($att->status === 'hadir')
                                            <!-- Hadir (Checkmark hijau) -->
                                            <button type="button" wire:click="openEditModal('{{ $row->sender_name }}', {{ $d }})"
                                                    title="Hadir: klik untuk detail" 
                                                    class="mx-auto flex h-6 w-6 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition shadow-sm border border-emerald-100/50">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        @elseif($att->status === 'izin')
                                            <!-- Izin (Kuning I) -->
                                            <button type="button" wire:click="openEditModal('{{ $row->sender_name }}', {{ $d }})"
                                                    title="Izin: klik untuk detail"
                                                    class="mx-auto flex h-6 w-6 items-center justify-center rounded-full bg-amber-50 text-amber-700 hover:bg-amber-100 transition font-extrabold text-[10px] shadow-sm border border-amber-200/50">
                                                I
                                            </button>
                                        @elseif($att->status === 'cuti')
                                            <!-- Cuti (Biru C) -->
                                            <button type="button" wire:click="openEditModal('{{ $row->sender_name }}', {{ $d }})"
                                                    title="Cuti: klik untuk detail"
                                                    class="mx-auto flex h-6 w-6 items-center justify-center rounded-full bg-sky-50 text-sky-700 hover:bg-sky-100 transition font-extrabold text-[10px] shadow-sm border border-sky-200/50">
                                                C
                                            </button>
                                        @elseif($att->status === 'libur')
                                            <!-- Libur (Ungu L) -->
                                            <button type="button" wire:click="openEditModal('{{ $row->sender_name }}', {{ $d }})"
                                                    title="Libur: klik untuk detail"
                                                    class="mx-auto flex h-6 w-6 items-center justify-center rounded-full bg-purple-50 text-purple-700 hover:bg-purple-100 transition font-extrabold text-[10px] shadow-sm border border-purple-200/50">
                                                L
                                            </button>
                                        @elseif($att->status === 'alpha')
                                            <!-- Alpha (Merah X) -->
                                            <button type="button" wire:click="openEditModal('{{ $row->sender_name }}', {{ $d }})"
                                                    title="Alpha: klik untuk detail"
                                                    class="mx-auto flex h-6 w-6 items-center justify-center rounded-full bg-rose-50 text-rose-600 hover:bg-rose-100 transition shadow-sm border border-rose-100/50">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        @endif
                                    @else
                                        <!-- No record exists -->
                                        @if($dateStr > $todayStr)
                                            <!-- Future day (Minus abu-abu) -->
                                            <div class="mx-auto flex h-6 w-6 items-center justify-center text-slate-300 font-bold text-xs">
                                                -
                                            </div>
                                        @else
                                            <!-- Past day / today without check-in (Alpha/Silang Merah default) -->
                                            <button type="button" wire:click="openEditModal('{{ $row->sender_name }}', {{ $d }})"
                                                    title="Tidak Absen (Default Alpha): klik untuk ubah status"
                                                    class="mx-auto flex h-6 w-6 items-center justify-center rounded-full bg-rose-50/50 text-rose-400 hover:bg-rose-100 hover:text-rose-600 transition shadow-sm border border-rose-100/30">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            @endfor

                            <!-- Total Hadir -->
                            <td class="whitespace-nowrap px-6 py-4 text-center font-bold text-slate-900 border-l border-slate-200 bg-slate-50/30">
                                <span class="inline-flex items-center justify-center h-7 px-3 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
                                    {{ $totalHadir }} Hari
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 3 + $daysInMonth }}" class="px-6 py-10 text-center text-slate-400">
                                Tidak ditemukan data absensi untuk periode ini. Silakan klik tombol "Sync Absensi Hari Ini" untuk menarik data terbaru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            {{ $rows->links() }}
        </div>
    </div>

    <!-- Edit Status Modal Dialog -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-150">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl w-full max-w-lg p-6 space-y-6">
                <!-- Modal header -->
                <div class="flex items-center justify-between border-b border-dashed border-slate-200 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Ubah Status Kehadiran</h3>
                        <p class="text-xs text-slate-400 mt-1">Pengirim: <span class="font-bold text-slate-700">{{ $modalSenderName }}</span> | Tanggal: <span class="font-bold text-slate-700">{{ Carbon\Carbon::parse($modalDate)->translatedFormat('d F Y') }}</span></p>
                    </div>
                    <button type="button" wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="space-y-4">
                    <!-- Status selector -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status Kehadiran</label>
                        <select wire:model="modalStatus" 
                                class="block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition font-semibold">
                            <option value="hadir">Hadir (Present)</option>
                            <option value="alpha">Alpha (Absent)</option>
                            <option value="izin">Izin (Permitted)</option>
                            <option value="cuti">Cuti (Leave)</option>
                            <option value="libur">Libur (Off Day)</option>
                        </select>
                    </div>

                    <!-- Photo & Caption details (only shown if photo is present) -->
                    @if($modalPhotoUrl)
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/50 space-y-3">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bukti Kehadiran (API eBilling)</h4>
                            <div class="flex justify-center">
                                <a href="{{ $modalPhotoUrl }}" target="_blank" class="block group relative overflow-hidden rounded-xl border bg-white shadow-sm hover:shadow transition">
                                    <img src="{{ $modalPhotoUrl }}" class="max-h-48 object-contain rounded-xl max-w-full group-hover:scale-105 transition duration-300" />
                                </a>
                            </div>
                            @if($modalCaption)
                                <div class="bg-white p-3 rounded-xl border border-slate-100 text-xs text-slate-600 leading-relaxed italic border-l-4 border-l-primary/40">
                                    "{{ $modalCaption }}"
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200/80 text-xs font-semibold text-slate-700 rounded-xl transition">
                        Batal
                    </button>
                    <button type="button" wire:click="saveStatus"
                            class="px-5 py-2.5 bg-primary hover:bg-sky-600 text-xs font-semibold text-white rounded-xl shadow-lg shadow-sky-500/20 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Searchable Employee Mapping Modal -->
    @if($showMapModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <!-- Modal backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showMapModal', false)"></div>

            <!-- Modal Content -->
            <div class="relative z-10 bg-white rounded-3xl border border-slate-200 shadow-2xl w-full max-w-md p-6 space-y-6">
                <!-- Modal header -->
                <div class="flex items-center justify-between border-b border-dashed border-slate-200 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Petakan Pengirim Absensi</h3>
                        <p class="text-xs text-slate-400 mt-1">Pengirim WhatsApp: <span class="font-bold text-slate-700">{{ $mapSenderName }}</span></p>
                    </div>
                    <button type="button" wire:click="$set('showMapModal', false)" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Cari Karyawan Aktif</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-4.5 w-4.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input wire:model.live.debounce.250ms="searchEmployee" type="text" placeholder="Ketik nama karyawan..." 
                                   class="block w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition font-semibold">
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Hasil Pencarian</label>
                        <div class="max-h-60 overflow-y-auto divide-y divide-slate-100 border border-slate-100 rounded-2xl bg-slate-50/20">
                            @forelse($searchEmployeesList as $emp)
                                <button type="button" wire:click="mapEmployee('{{ $mapSenderName }}', {{ $emp->id }})"
                                        class="w-full flex items-center justify-between p-3 hover:bg-slate-100/80 transition text-left">
                                    <div>
                                        <div class="text-sm font-bold text-slate-900">{{ $emp->user->name }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5">NIK: {{ $emp->employee_id_number }} | {{ $emp->position->name ?? '-' }}</div>
                                    </div>
                                    <svg class="h-5 w-5 text-primary opacity-0 hover:opacity-100 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @empty
                                <div class="p-4 text-center text-xs text-slate-400 font-medium">
                                    Tidak ada karyawan aktif yang cocok.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                    <button type="button" wire:click="mapEmployee('{{ $mapSenderName }}', '')"
                            class="px-4 py-2 border border-rose-200 bg-rose-50/40 hover:bg-rose-100 text-xs font-semibold text-rose-600 rounded-xl transition">
                        Hapus Pemetaan
                    </button>
                    
                    <button type="button" wire:click="$set('showMapModal', false)"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200/80 text-xs font-semibold text-slate-700 rounded-xl transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
