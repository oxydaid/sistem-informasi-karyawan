<div>
    @php $title = 'Manajemen Kontrak Kerja (SPK)'; @endphp

    <!-- Header Section -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500">Kelola dokumen Surat Perjanjian Kerja (SPK), masa berlaku kontrak karyawan, dan tanda tangan digital pelamar.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button type="button" wire:click="openCreateModal" class="inline-flex items-center justify-center px-4 py-2.5 bg-primary text-white text-sm font-semibold rounded-2xl shadow-md hover:opacity-90 transition">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Buat Kontrak Baru
            </button>
        </div>
    </div>


    <!-- Search & Filters Container (Responsive with AlpineJS) -->
    <div x-data="{ openFilter: false }">
        <!-- Desktop/Tablet View (Inline) -->
        <div class="hidden md:flex mt-8 flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-4 rounded-3xl border border-slate-200/60 shadow-sm">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama karyawan/pelamar atau NIK..." 
                       class="block w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
            </div>
            
            <div class="flex flex-wrap gap-4">
                <select wire:model.live="filterType" 
                        class="block rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                    <option value="">Semua Tipe Pekerjaan</option>
                    <option value="magang">Magang</option>
                    <option value="pkl">PKL</option>
                    <option value="kontrak">Kontrak (PKWT)</option>
                    <option value="tetap">Tetap (PKWTT)</option>
                    <option value="freelance">Freelance</option>
                </select>

                <select wire:model.live="filterStatus" 
                        class="block rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                    <option value="">Semua Status Kontrak</option>
                    <option value="active">Aktif</option>
                    <option value="ending_soon">Segera Berakhir (< 30 Hari)</option>
                    <option value="expired">Kedaluwarsa (Expired)</option>
                    <option value="unsigned">Belum TTD</option>
                </select>
            </div>
        </div>

        <!-- Mobile Floating Action Button (FAB) -->
        <button type="button" @click="openFilter = true" 
                class="md:hidden fixed bottom-6 right-6 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-primary text-white shadow-lg shadow-sky-500/35 hover:scale-105 active:scale-95 transition focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
        </button>

        <!-- Mobile Filter Modal -->
        <div x-show="openFilter" class="relative z-50 md:hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <!-- Backdrop -->
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
                        
                        <!-- Header -->
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-sm font-bold text-slate-900">Pencarian & Filter</h3>
                            <button type="button" @click="openFilter = false" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content Fields -->
                        <div class="space-y-4">
                            <!-- Search -->
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 font-semibold">Cari Kontrak</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-4.5 w-4.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Nama, NIK..." 
                                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-4 py-2 text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 text-xs">
                                </div>
                            </div>

                            <!-- Filter tipe pekerjaan -->
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 font-semibold">Tipe Pekerjaan</label>
                                <select wire:model.live="filterType" 
                                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 text-xs font-medium">
                                    <option value="">Semua Tipe Pekerjaan</option>
                                    <option value="magang">Magang</option>
                                    <option value="pkl">PKL</option>
                                    <option value="kontrak">Kontrak (PKWT)</option>
                                    <option value="tetap">Tetap (PKWTT)</option>
                                    <option value="freelance">Freelance</option>
                                </select>
                            </div>

                            <!-- Filter status -->
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 font-semibold">Filter Status</label>
                                <select wire:model.live="filterStatus" 
                                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 text-xs font-medium">
                                    <option value="">Semua Status Kontrak</option>
                                    <option value="active">Aktif</option>
                                    <option value="ending_soon">Segera Berakhir (< 30 Hari)</option>
                                    <option value="expired">Kedaluwarsa (Expired)</option>
                                    <option value="unsigned">Belum TTD</option>
                                </select>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-6 pt-3 border-t border-slate-100 flex justify-end">
                            <button type="button" @click="openFilter = false" 
                                    class="w-full py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:opacity-90 transition text-center shadow-md shadow-sky-500/25">
                                Terapkan & Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table List -->
    <div class="mt-6 overflow-hidden bg-white shadow-sm border border-slate-200/60 rounded-3xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">
                    <tr>
                        <th class="px-6 py-4">Karyawan / Pelamar</th>
                        <th class="px-6 py-4">Jabatan & Divisi</th>
                        <th class="px-6 py-4">Tipe Pekerjaan</th>
                        <th class="px-6 py-4">Masa Kontrak</th>
                        <th class="px-6 py-4">Gaji Pokok</th>
                        <th class="px-6 py-4 text-center">Status TTD</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($contracts as $c)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $c->applicant->name }}</div>
                                <div class="text-xs text-slate-400">NIK: {{ $c->applicant->nik }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if($c->position)
                                    <div class="font-medium text-slate-800">{{ $c->position->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $c->position->department->name ?? '-' }}</div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold capitalize
                                    {{ $c->employment_type === 'tetap' ? 'bg-emerald-50 text-emerald-800' : '' }}
                                    {{ $c->employment_type === 'kontrak' ? 'bg-sky-50 text-sky-800' : '' }}
                                    {{ $c->employment_type === 'magang' ? 'bg-amber-50 text-amber-800' : '' }}
                                    {{ $c->employment_type === 'pkl' ? 'bg-purple-50 text-purple-800' : '' }}
                                    {{ $c->employment_type === 'freelance' ? 'bg-slate-100 text-slate-800' : '' }}
                                ">
                                    {{ $c->employment_type }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-medium text-slate-800">
                                    {{ $c->start_date ? $c->start_date->format('d M Y') : '-' }} s/d
                                </div>
                                <div class="text-xs text-slate-400">
                                    {{ $c->end_date ? $c->end_date->format('d M Y') : 'Selesai/Permanen' }}
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-slate-800">
                                Rp {{ number_format($c->salary, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                @if($c->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        Disetujui
                                    </span>
                                @elseif($c->status === 'uploaded')
                                    <div class="flex flex-col items-center gap-1.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-50 text-amber-700">
                                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                            Perlu Verifikasi
                                        </span>
                                        <button type="button" wire:click="approveUploadedContract({{ $c->id }})" 
                                                class="px-2.5 py-1 bg-primary text-white text-[10px] font-bold rounded-lg hover:opacity-90 transition">
                                            Setujui & Buat Akun
                                        </button>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-100 text-slate-600">
                                        <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                        Draft SPK
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right space-x-2">
                                <button type="button" wire:click="downloadPdf({{ $c->id }})" title="Unduh Draf SPK (PDF)"
                                        class="inline-flex items-center justify-center h-9.5 w-9.5 rounded-xl border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 transition">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </button>
                                @if($c->signed_contract_path)
                                    <a href="{{ asset('storage/' . $c->signed_contract_path) }}" target="_blank" title="Lihat Scan SPK Fisik"
                                       class="inline-flex items-center justify-center h-9.5 w-9.5 rounded-xl border border-primary/20 text-primary bg-sky-50/20 hover:bg-sky-50 transition">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                @endif
                                <button type="button" wire:click="openEditModal({{ $c->id }})" title="Edit Detail Kontrak"
                                        class="inline-flex items-center justify-center h-9.5 w-9.5 rounded-xl border border-slate-200 text-primary bg-sky-50/20 hover:bg-sky-50 transition">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button type="button" wire:click="confirmDelete({{ $c->id }})" title="Hapus Kontrak"
                                        class="inline-flex items-center justify-center h-9.5 w-9.5 rounded-xl border border-slate-200 text-rose-600 bg-white hover:bg-rose-50 transition">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="mt-2 text-sm font-medium text-slate-400">Tidak ada data kontrak kerja ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($contracts->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="$set('showModal', false)"></div>

                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

                <!-- Modal Content Card (Z-index 10 to ensure it is in front of backdrop) -->
                <div class="relative z-10 inline-block transform overflow-hidden rounded-3xl bg-white text-left align-bottom shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl sm:align-middle border border-slate-100">
                    
                    <!-- Header -->
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-base font-bold text-slate-900">{{ $isEdit ? 'Ubah Kontrak Kerja Karyawan' : 'Buat Kontrak Kerja Baru' }}</h3>
                        <button type="button" wire:click="$set('showModal', false)" class="rounded-xl p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form Body -->
                    <form wire:submit.prevent="saveContract" class="p-6 space-y-4">
                        <!-- Select Applicant -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Pelamar / Karyawan</label>
                            @if($isEdit)
                                <div class="px-4 py-3 bg-slate-100 text-slate-700 text-sm font-semibold rounded-2xl border border-slate-200">
                                    {{ $contracts->find($contractId)->applicant->name ?? '' }} (NIK: {{ $contracts->find($contractId)->applicant->nik ?? '' }})
                                </div>
                            @else
                                <select wire:model.live="applicantId" 
                                        class="block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                    <option value="">-- Pilih Pelamar yang Diterima --</option>
                                    @foreach($availableApplicants as $app)
                                        <option value="{{ $app->id }}">{{ $app->name }} (NIK: {{ $app->nik }} - {{ $app->status }})</option>
                                    @endforeach
                                </select>
                            @endif
                            @error('applicantId') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Grid Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Position -->
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jabatan / Posisi</label>
                                <select wire:model.live="positionId" 
                                        class="block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach($positions as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->department->name ?? '-' }})</option>
                                    @endforeach
                                </select>
                                @error('positionId') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Employment Type -->
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tipe Pekerjaan</label>
                                <select wire:model.live="employmentType" 
                                        class="block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="magang">Magang</option>
                                    <option value="pkl">PKL</option>
                                    <option value="kontrak">Kontrak (PKWT)</option>
                                    <option value="tetap">Tetap (PKWTT)</option>
                                    <option value="freelance">Freelance</option>
                                </select>
                                @error('employmentType') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Date range -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Start Date -->
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                                <input wire:model.live="startDate" type="date" 
                                       class="block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                @error('startDate') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- End Date -->
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Berakhir (Opsional)</label>
                                <input wire:model.live="endDate" type="date" 
                                       class="block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                @error('endDate') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Salary & Signed status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                            <!-- Salary -->
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Gaji Pokok (Rp)</label>
                                <input wire:model.live="salary" type="number" placeholder="Misal: 5000000"
                                       class="block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                @error('salary') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Is Signed Checkbox -->
                            <div class="pt-6">
                                <label class="flex items-center cursor-pointer select-none">
                                    <input wire:model.live="isSigned" type="checkbox" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                    <span class="ml-3 text-sm font-semibold text-slate-700">Tandai Sudah TTD</span>
                                </label>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-3">
                            <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-2xl transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 bg-primary text-white text-sm font-semibold rounded-2xl shadow-md hover:opacity-90 transition">
                                Simpan Kontrak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($confirmingDeletion)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="$set('confirmingDeletion', false)"></div>

                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

                <!-- Card (Z-index 10) -->
                <div class="relative z-10 inline-block transform overflow-hidden rounded-3xl bg-white text-left align-bottom shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md sm:align-middle p-6 border border-slate-100">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 sm:mx-0 sm:h-10 sm:w-10 border border-rose-100">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-base font-bold text-slate-900" id="modal-title">Hapus Kontrak Kerja</h3>
                            <div class="mt-2">
                                <p class="text-sm text-slate-500">Apakah Anda yakin ingin menghapus kontrak kerja ini? Tindakan ini juga akan menghapus berkas draf SPK terkait dari penyimpanan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <button type="button" wire:click="$set('confirmingDeletion', false)" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-2xl transition">
                            Batal
                        </button>
                        <button type="button" wire:click="deleteContract" class="px-5 py-2.5 bg-rose-600 text-white text-sm font-semibold rounded-2xl shadow-md hover:bg-rose-700 transition">
                            Hapus Permanen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
