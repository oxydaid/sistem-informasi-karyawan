<div>
    @php $title = 'Review Pelamar Rekrutmen'; @endphp

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500">Kelola berkas lamaran, lakukan screening administratif, dan kelola proses wawancara hingga onboarding pelamar kerja.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button type="button" wire:click="openCreateModal" 
                    class="inline-flex items-center justify-center rounded-2xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-sky-500/20 hover:bg-primary/95 transition">
                <svg class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Pelamar
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
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, email, NIK KTP, atau No HP..." 
                       class="block w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
            </div>
            
            <div class="flex gap-4">
                <select wire:model.live="filterStatus" 
                        class="block rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                    <option value="">Semua Status Pelamar</option>
                    <option value="pending">Pending (Baru)</option>
                    <option value="reviewed">Reviewed (Diperiksa)</option>
                    <option value="interviewing">Interviewing (Wawancara)</option>
                    <option value="accepted">Accepted (Diterima)</option>
                    <option value="rejected">Rejected (Ditolak)</option>
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

        <!-- Mobile Filter Modal (Bottom Sheet style dialog) -->
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
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 font-semibold">Cari Pelamar</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-4.5 w-4.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Nama, email, NIK..." 
                                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-4 py-2 text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 text-xs">
                                </div>
                            </div>

                            <!-- Filter status -->
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 font-semibold">Filter Status</label>
                                <select wire:model.live="filterStatus" 
                                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 text-xs font-medium">
                                    <option value="">Semua Status Pelamar</option>
                                    <option value="pending">Pending (Baru)</option>
                                    <option value="reviewed">Reviewed (Diperiksa)</option>
                                    <option value="interviewing">Interviewing (Wawancara)</option>
                                    <option value="accepted">Accepted (Diterima)</option>
                                    <option value="rejected">Rejected (Ditolak)</option>
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
                        <th class="px-6 py-4">Nama Pelamar</th>
                        <th class="px-6 py-4">NIK KTP</th>
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4">Status Lamaran</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($applicants as $app)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $app->name }}</div>
                                <div class="text-xs text-slate-400">Terdaftar: {{ $app->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-800">
                                {{ $app->nik }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $app->email }}</div>
                                <div class="text-xs text-slate-400">WA: {{ $app->phone }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold capitalize
                                    {{ $app->status === 'pending' ? 'bg-amber-50 text-amber-800' : '' }}
                                    {{ $app->status === 'reviewed' ? 'bg-sky-50 text-sky-800' : '' }}
                                    {{ $app->status === 'interviewing' ? 'bg-purple-50 text-purple-800' : '' }}
                                    {{ $app->status === 'accepted' ? 'bg-emerald-50 text-emerald-800' : '' }}
                                    {{ $app->status === 'rejected' ? 'bg-rose-50 text-rose-800' : '' }}
                                ">
                                    {{ $app->status }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 space-x-2">
                                <button type="button" wire:click="selectApplicant({{ $app->id }})" 
                                        class="inline-flex items-center justify-center h-8.5 px-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 transition">
                                    Berkas & Detail
                                </button>
                                <button type="button" wire:click="downloadZip('{{ $app->nik }}')" 
                                        class="inline-flex items-center justify-center h-8.5 px-3 rounded-xl border border-slate-200 text-xs font-semibold text-primary bg-sky-50/20 hover:bg-sky-50 transition">
                                    ZIP Berkas
                                </button>
                                @if($app->status === 'accepted')
                                    <button type="button" wire:click="sendAcceptanceNotification({{ $app->id }})" 
                                            class="inline-flex items-center justify-center h-8.5 px-3 rounded-xl border border-emerald-200 text-xs font-semibold text-emerald-700 bg-emerald-50/30 hover:bg-emerald-50 transition" title="Kirim Ulang Notifikasi Email & WA">
                                        Kirim Notif
                                    </button>
                                @endif
                                <button type="button" wire:click="confirmDelete({{ $app->id }})" 
                                        class="inline-flex items-center justify-center h-8.5 w-8.5 rounded-xl border border-slate-200 text-rose-600 bg-white hover:bg-rose-50 transition" title="Hapus Pelamar">
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                    <span class="mt-2 text-sm font-medium text-slate-400">Tidak ada pelamar baru ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($applicants->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $applicants->links() }}
            </div>
        @endif
    </div>

    <!-- Preview Modal -->
    @if($showPreviewModal && $selectedApplicant)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closePreviewModal"></div>

                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

                <!-- Modal Content Card (Light theme matching dashboard) -->
                <div class="relative z-10 inline-block transform overflow-hidden rounded-3xl bg-white text-left align-middle shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-7xl border border-slate-200">
                    
                    <!-- Header -->
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Review & Edit Data Pelamar</h3>
                            <p class="text-xs text-slate-500">Kelola dan ubah data berkas fisik serta data KTP pelamar secara manual.</p>
                        </div>
                        <button type="button" wire:click="closePreviewModal" class="rounded-xl p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 bg-white text-slate-800">
                        
                        <!-- KOLOM KIRI (5/12): Form Registrasi & Edit Pelamar (Semua Field Bisa Diedit) -->
                        <form wire:submit.prevent="saveApplicantData" class="lg:col-span-5 space-y-5 overflow-y-auto max-h-[600px] pr-3">
                            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Form Biodata Pelamar</h4>
                                <button type="submit" class="px-3.5 py-1.5 bg-primary text-white text-xs font-semibold rounded-xl shadow hover:opacity-90 transition">
                                    Simpan Perubahan
                                </button>
                            </div>

                            <!-- Nama & NIK -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap (Sesuai KTP)</label>
                                    <input wire:model="editName" type="text" 
                                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-xs font-semibold">
                                    @error('editName') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nomor Induk Kependudukan (NIK)</label>
                                    <input wire:model="editNik" type="text" maxLength="16"
                                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-xs font-semibold font-mono tracking-wider">
                                    @error('editNik') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Posisi dilamar & WhatsApp -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Posisi Yang Dilamar <span class="text-rose-500">*</span></label>
                                    <select wire:model="editPositionId" 
                                            class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-xs font-semibold">
                                        <option value="">-- Pilih Posisi --</option>
                                        @foreach($positions as $pos)
                                            <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->department->name ?? '-' }})</option>
                                        @endforeach
                                    </select>
                                    @error('editPositionId') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">No. WhatsApp Pelamar</label>
                                    <input wire:model="editPhone" type="text" 
                                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-xs font-semibold">
                                    @error('editPhone') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Email & Keterangan -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Email</label>
                                    <input wire:model="editEmail" type="email" 
                                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-xs font-semibold">
                                    @error('editEmail') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Keterangan / Kompetensi Tambahan</label>
                                    <input wire:model="editKeterangan" type="text" placeholder="Contoh: FO Splicing Expert, Cisco CCNA"
                                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-xs font-semibold">
                                    @error('editKeterangan') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- UPLOAD BERKAS BARU -->
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/60 space-y-4">
                                <h5 class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-1.5">Unggah Berkas Baru (Opsional / Mengganti Berkas Lama)</h5>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">KTP (Format Gambar untuk OCR)</label>
                                        <input type="file" wire:model="fileKtp" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                        @error('fileKtp') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Kartu Keluarga (KK)</label>
                                        <input type="file" wire:model="fileKk" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                        @error('fileKk') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Ijazah</label>
                                        <input type="file" wire:model="fileIjazah" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                        @error('fileIjazah') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">SKCK</label>
                                        <input type="file" wire:model="fileSkck" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                        @error('fileSkck') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Pas Foto</label>
                                        <input type="file" wire:model="filePasFoto" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                        @error('filePasFoto') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">CV</label>
                                        <input type="file" wire:model="fileCv" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                        @error('fileCv') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">SIM (Opsional)</label>
                                        <input type="file" wire:model="fileSim" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                        @error('fileSim') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Sertifikat (Opsional)</label>
                                        <input type="file" wire:model="fileSertifikat" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                        @error('fileSertifikat') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Dokumen Pendukung Tambahan (Bisa multi-upload)</label>
                                    <input type="file" wire:model="filePendukung" multiple class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                </div>
                            </div>

                            <!-- DATA KTP OCR (DIISI MANUAL) -->
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/60 space-y-4">
                                <div class="flex items-center justify-between border-b border-slate-200 pb-1.5">
                                    <h5 class="text-xs font-bold text-slate-700">Data Identitas KTP (Diedit Manual)</h5>
                                    <button type="button" wire:click="scanOcrManual" wire:loading.attr="disabled"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 shadow-sm transition active:scale-[0.98]">
                                        <svg wire:loading wire:target="scanOcrManual" class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg wire:loading.remove wire:target="scanOcrManual" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Scan KTP (OCR)</span>
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Tempat Lahir</label>
                                        <input wire:model="editTempatLahir" type="text"
                                               class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:border-primary focus:outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Tanggal Lahir</label>
                                        <input wire:model="editTanggalLahir" type="date"
                                               class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:border-primary focus:outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Jenis Kelamin</label>
                                        <select wire:model="editJenisKelamin" 
                                                class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:border-primary focus:outline-none transition">
                                            <option value="">-- Pilih --</option>
                                            <option value="Laki-laki">Laki-laki</option>
                                            <option value="Perempuan">Perempuan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Agama</label>
                                        <input wire:model="editAgama" type="text"
                                               class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:border-primary focus:outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Status Perkawinan</label>
                                        <select wire:model="editStatusKawin" 
                                                class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:border-primary focus:outline-none transition">
                                            <option value="">-- Pilih --</option>
                                            <option value="Belum Kawin">Belum Kawin</option>
                                            <option value="Kawin">Kawin</option>
                                            <option value="Cerai Hidup">Cerai Hidup</option>
                                            <option value="Cerai Mati">Cerai Mati</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Pekerjaan</label>
                                        <input wire:model="editPekerjaan" type="text"
                                               class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:border-primary focus:outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Kewarganegaraan</label>
                                        <select wire:model="editKewarganegaraan" 
                                                class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:border-primary focus:outline-none transition">
                                            <option value="WNI">WNI</option>
                                            <option value="WNA">WNA</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="pt-1.5">
                                    <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Alamat Lengkap (Sesuai KTP)</label>
                                    <textarea wire:model="editAlamat" rows="2.5"
                                              class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:border-primary focus:outline-none transition"></textarea>
                                </div>
                            </div>
                        </form>

                        <!-- KOLOM TENGAH (4/12): Pratinjau Berkas Aktif (Light theme) -->
                        <div class="lg:col-span-4 flex flex-col space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-200/70">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-2.5">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Pratinjau Berkas Aktif</h4>
                                @if($activeDocUrl)
                                    <span class="text-[10px] text-slate-500 font-semibold truncate max-w-[150px]" title="{{ basename($activeDocUrl) }}">
                                        {{ basename($activeDocUrl) }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex-1 flex items-center justify-center bg-slate-200/50 rounded-xl overflow-hidden min-h-[480px] border border-slate-200 p-2">
                                @if($activeDocUrl)
                                    @php $ext = strtolower(pathinfo($activeDocUrl, PATHINFO_EXTENSION)); @endphp

                                    @if($ext === 'pdf')
                                        <iframe src="{{ asset('storage/' . $activeDocUrl) }}#toolbar=0&navpanes=0" class="w-full h-[520px] rounded-lg border-0 bg-white"></iframe>
                                    @elseif(in_array($ext, ['png', 'jpg', 'jpeg', 'webp']))
                                        <div class="relative w-full h-[520px] flex items-center justify-center bg-slate-100 rounded-lg p-4">
                                            <img src="{{ asset('storage/' . $activeDocUrl) }}" class="max-w-full max-h-full object-contain rounded-lg shadow-md border border-slate-200">
                                        </div>
                                    @else
                                        <div class="text-center p-6 space-y-3">
                                            <svg class="h-12 w-12 text-slate-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                            <h5 class="text-sm font-bold text-slate-600">Berkas tidak dapat dipratinjau</h5>
                                            <p class="text-xs text-slate-500">Format ini hanya bisa diunduh secara langsung.</p>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center p-6 space-y-3">
                                        <svg class="h-14 w-14 text-slate-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <h5 class="text-sm font-bold text-slate-600">Belum ada dokumen dipilih</h5>
                                        <p class="text-xs text-slate-500">Klik salah satu dokumen di panel sebelah kanan untuk pratinjau di sini.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- KOLOM KANAN (3/12): Verifikasi Administratif & Status Seleksi -->
                        <div class="lg:col-span-3 space-y-6 overflow-y-auto max-h-[600px] pr-2">
                            <!-- Verifikasi Administratif -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 px-1">Verifikasi Administratif</h4>
                                
                                @if(is_array($selectedApplicant->documents))
                                    @php 
                                        $docsList = [
                                            'ktp' => 'KTP Identitas',
                                            'kk' => 'Kartu Keluarga (KK)',
                                            'ijazah' => 'Ijazah Terakhir',
                                            'skck' => 'SKCK Kepolisian',
                                            'pas_foto' => 'Pas Foto Terbaru',
                                            'cv' => 'CV / Surat Lamaran',
                                            'sertifikat' => 'Sertifikat Keahlian',
                                            'sim' => 'SIM Pengemudi',
                                        ];
                                        $verifiedDocs = $selectedApplicant->metadata['verified_docs'] ?? [];
                                    @endphp

                                    @foreach($docsList as $key => $label)
                                        @php 
                                            $path = $selectedApplicant->documents[$key] ?? null;
                                            $isVerified = $verifiedDocs[$key] ?? false;
                                        @endphp
                                        
                                        <div class="p-3 bg-white border rounded-2xl flex items-center justify-between transition group {{ $path ? ($activeDocUrl === $path ? 'border-primary bg-slate-50' : 'border-slate-200/60') : 'border-slate-100 opacity-60' }}">
                                            <div class="min-w-0 flex-1 pr-2">
                                                <div class="flex items-center space-x-1">
                                                    <span class="text-xs font-semibold text-slate-700 truncate">{{ $label }}</span>
                                                    @if($path)
                                                        <span class="text-[9px] text-emerald-600 font-bold bg-emerald-50 px-1 rounded">Terunggah</span>
                                                    @else
                                                        <span class="text-[9px] text-rose-600 font-bold bg-rose-50 px-1 rounded">Wajib</span>
                                                    @endif
                                                </div>
                                                <div class="text-[10px] text-slate-400 truncate mt-0.5 font-mono">
                                                    @if($path)
                                                        {{ basename($path) }}
                                                    @else
                                                        Belum ada berkas
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            @if($path)
                                                <div class="flex items-center space-x-1.5 flex-shrink-0">
                                                    <!-- Preview Button -->
                                                    <button type="button" wire:click="previewFile('{{ $path }}')" 
                                                            class="h-7 w-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition" title="Lihat Berkas">
                                                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                        </svg>
                                                    </button>
                                                    
                                                    <!-- Verification Toggle Checkbox -->
                                                    <button type="button" wire:click="toggleDocVerification('{{ $key }}')" 
                                                            class="h-7 w-7 rounded-lg flex items-center justify-center transition border {{ $isVerified ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-slate-50 text-slate-400 border-slate-200 hover:border-slate-350' }}" title="Verifikasi">
                                                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach

                                    <!-- Dokumen Pendukung (Multiple) -->
                                    @if(isset($selectedApplicant->documents['pendukung']) && is_array($selectedApplicant->documents['pendukung']))
                                        @foreach($selectedApplicant->documents['pendukung'] as $index => $subpath)
                                            <div class="p-3 bg-white border rounded-2xl flex items-center justify-between transition group {{ $activeDocUrl === $subpath ? 'border-primary bg-slate-50' : 'border-slate-200/60' }}">
                                                <div class="min-w-0 flex-1 pr-2">
                                                    <div class="flex items-center space-x-1">
                                                        <span class="text-xs font-semibold text-slate-700 truncate">Dokumen Pendukung {{ $index + 1 }}</span>
                                                        <span class="text-[9px] text-emerald-600 font-bold bg-emerald-50 px-1 rounded">Opsional</span>
                                                    </div>
                                                    <div class="text-[10px] text-slate-400 truncate mt-0.5 font-mono">
                                                        {{ basename($subpath) }}
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-1.5 flex-shrink-0">
                                                    <button type="button" wire:click="previewFile('{{ $subpath }}')" 
                                                            class="h-7 w-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition" title="Lihat Berkas">
                                                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                @endif
                            </div>

                            <!-- Manajemen Status Seleksi / Alur Kerja -->
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/60 space-y-3">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Manajemen Status Seleksi</h4>
                                <div class="space-y-2 text-xs">
                                    <div class="flex justify-between items-center text-slate-600">
                                        <span>Status Lamaran:</span>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full font-bold capitalize bg-slate-200 text-slate-800">
                                            {{ $selectedApplicant->status }}
                                        </span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 pt-2">
                                    @if($selectedApplicant->status !== 'reviewed')
                                        <button type="button" wire:click="updateStatus({{ $selectedApplicant->id }}, 'reviewed')" 
                                                class="px-2 py-2 bg-sky-50 text-sky-700 border border-sky-200 text-[11px] font-bold rounded-xl hover:bg-sky-100 transition">
                                            Reviewed
                                        </button>
                                    @endif
                                    @if($selectedApplicant->status !== 'interviewing')
                                        <button type="button" wire:click="updateStatus({{ $selectedApplicant->id }}, 'interviewing')" 
                                                class="px-2 py-2 bg-purple-50 text-purple-700 border border-purple-200 text-[11px] font-bold rounded-xl hover:bg-purple-100 transition">
                                            Interview
                                        </button>
                                    @endif
                                    @if($selectedApplicant->status !== 'accepted')
                                        <button type="button" wire:click="updateStatus({{ $selectedApplicant->id }}, 'accepted')" 
                                                class="px-2 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold rounded-xl hover:bg-emerald-100 transition col-span-2">
                                            Diterima (SPK Kontrak)
                                        </button>
                                    @else
                                        <button type="button" wire:click="sendAcceptanceNotification({{ $selectedApplicant->id }})" 
                                                class="px-2 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold rounded-xl hover:bg-emerald-100 transition col-span-2">
                                            Kirim Ulang Notif Email & WA
                                        </button>
                                    @endif
                                    @if($selectedApplicant->status !== 'rejected')
                                        <button type="button" wire:click="updateStatus({{ $selectedApplicant->id }}, 'rejected')" 
                                                class="px-2 py-2 bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-bold rounded-xl hover:bg-rose-100 transition col-span-2">
                                            Tolak Pelamar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Create Applicant Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showCreateModal', false)"></div>
                <div class="relative z-10 inline-block transform overflow-hidden rounded-3xl bg-white text-left align-middle shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-slate-200">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Tambah Pelamar Baru</h3>
                            <p class="text-xs text-slate-500">Daftarkan data pelamar secara manual ke dalam sistem rekrutmen.</p>
                        </div>
                        <button type="button" wire:click="$set('showCreateModal', false)" class="rounded-xl p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveApplicantData" class="p-6 space-y-4 max-h-[600px] overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                                <input wire:model="editName" type="text" placeholder="Masukkan nama..." class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-slate-900 focus:bg-white focus:outline-none transition">
                                @error('editName') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">NIK (KTP) <span class="text-rose-500">*</span></label>
                                <input wire:model="editNik" type="text" maxLength="16" placeholder="Masukkan NIK 16 digit..." class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-slate-900 focus:bg-white focus:outline-none transition">
                                @error('editNik') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Posisi Dilamar <span class="text-rose-500">*</span></label>
                                <select wire:model="editPositionId" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-slate-900 focus:bg-white focus:outline-none transition">
                                    <option value="">-- Pilih Posisi --</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->department->name ?? '-' }})</option>
                                    @endforeach
                                </select>
                                @error('editPositionId') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">No. WhatsApp <span class="text-rose-500">*</span></label>
                                <input wire:model="editPhone" type="text" placeholder="Masukkan no HP..." class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-slate-900 focus:bg-white focus:outline-none transition">
                                @error('editPhone') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email <span class="text-rose-500">*</span></label>
                                <input wire:model="editEmail" type="email" placeholder="Masukkan email..." class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-slate-900 focus:bg-white focus:outline-none transition">
                                @error('editEmail') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kompetensi / Keterangan</label>
                                <input wire:model="editKeterangan" type="text" placeholder="Misal: Cisco CCNA, FO Splicing" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-semibold text-slate-900 focus:bg-white focus:outline-none transition">
                            </div>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-4">
                            <h5 class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-1.5">Data Identitas KTP</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Tempat Lahir</label>
                                    <input wire:model="editTempatLahir" type="text" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Tanggal Lahir</label>
                                    <input wire:model="editTanggalLahir" type="date" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Jenis Kelamin</label>
                                    <select wire:model="editJenisKelamin" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:outline-none transition">
                                        <option value="">-- Pilih --</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Agama</label>
                                    <input wire:model="editAgama" type="text" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Status Perkawinan</label>
                                    <select wire:model="editStatusKawin" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:outline-none transition">
                                        <option value="">-- Pilih --</option>
                                        <option value="Belum Kawin">Belum Kawin</option>
                                        <option value="Kawin">Kawin</option>
                                        <option value="Cerai Hidup">Cerai Hidup</option>
                                        <option value="Cerai Mati">Cerai Mati</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Pekerjaan</label>
                                    <input wire:model="editPekerjaan" type="text" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Kewarganegaraan</label>
                                    <select wire:model="editKewarganegaraan" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:outline-none transition">
                                        <option value="WNI">WNI</option>
                                        <option value="WNA">WNA</option>
                                    </select>
                                </div>
                            </div>
                            <div class="pt-1.5">
                                <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Alamat Lengkap</label>
                                <textarea wire:model="editAlamat" rows="2" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:outline-none transition"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 border-t border-slate-100 pt-4 mt-6">
                            <button type="button" wire:click="$set('showCreateModal', false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl shadow hover:opacity-90 transition">Simpan Pelamar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showDeleteModal', false)"></div>
                <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 text-left shadow-2xl transition-all border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-2">Hapus Data Pelamar</h3>
                    <p class="text-xs text-slate-500 mb-4 font-semibold">Apakah Anda yakin ingin menghapus data pelamar beserta semua berkas fisiknya secara permanen?</p>
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="$set('showDeleteModal', false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Batal</button>
                        <button type="button" wire:click="deleteApplicant" class="px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-xl shadow hover:bg-rose-700 transition">Hapus Permanen</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
