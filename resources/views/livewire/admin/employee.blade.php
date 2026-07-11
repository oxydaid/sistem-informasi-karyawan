<div>
    @php $title = 'Data Karyawan'; @endphp
    

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Data Karyawan</h1>
            <p class="mt-2 text-sm text-slate-500">Daftar lengkap seluruh karyawan aktif beserta jabatan, status kerja, dan sisa kuota cuti tahunan.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <button type="button" wire:click="openCreateModal" class="block rounded-2xl bg-primary px-4 py-2.5 text-center text-sm font-semibold text-white shadow-sm hover:opacity-90 transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                Tambah Karyawan
            </button>
        </div>
    </div>

    <!-- Search & Filters Container (Responsive with AlpineJS) -->
    <div x-data="{ openFilter: false }">
        <!-- Desktop/Tablet View (Inline - Rounded-full styling matching Image 2) -->
        <div class="hidden md:flex mt-8 flex-col md:flex-row md:items-center gap-4 bg-white p-3 rounded-full border border-slate-200/60 shadow-sm">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, email, NIK KTP, atau NIK Karyawan..." 
                       class="block w-full rounded-full border border-slate-100 bg-slate-50/40 pl-11 pr-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
            </div>
            
            <div class="flex gap-3 pr-2">
                <!-- Filter Posisi/Jabatan -->
                <select wire:model.live="filterPosition" 
                        class="block rounded-full border border-slate-200 bg-white px-4 py-2.5 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm font-medium">
                    <option value="">Semua Jabatan</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                    @endforeach
                </select>

                <!-- Filter Status Kerja -->
                <select wire:model.live="filterStatus" 
                        class="block rounded-full border border-slate-200 bg-white px-4 py-2.5 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm font-medium">
                    <option value="">Semua Status Kerja</option>
                    <option value="tetap">Tetap (PKWTT)</option>
                    <option value="kontrak">Kontrak (PKWT)</option>
                    <option value="magang">Magang</option>
                    <option value="pkl">PKL</option>
                    <option value="freelance">Freelance</option>
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
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 font-semibold">Cari Karyawan</label>
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

                            <!-- Filter Jabatan -->
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 font-semibold">Filter Jabatan</label>
                                <select wire:model.live="filterPosition" 
                                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 text-xs font-medium">
                                    <option value="">Semua Jabatan</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter status -->
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 font-semibold">Filter Status</label>
                                <select wire:model.live="filterStatus" 
                                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 text-xs font-medium">
                                    <option value="">Semua Status Kerja</option>
                                    <option value="tetap">Tetap (PKWTT)</option>
                                    <option value="kontrak">Kontrak (PKWT)</option>
                                    <option value="magang">Magang</option>
                                    <option value="pkl">PKL</option>
                                    <option value="freelance">Freelance</option>
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

    <!-- Table (Styling matching Image 2 layout) -->
    <div class="mt-6 overflow-hidden bg-white shadow-sm border border-slate-200/60 rounded-3xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">
                    <tr>
                        <th class="px-6 py-4">Karyawan</th>
                        <th class="px-6 py-4">NIK Internal / KTP</th>
                        <th class="px-6 py-4">Divisi & Jabatan</th>
                        <th class="px-6 py-4">Status & Join Date</th>
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
                                    @if(!empty($emp->documents['pas_foto']) && \Storage::disk('public')->exists($emp->documents['pas_foto']))
                                        <img class="h-10 w-10 flex-shrink-0 rounded-full object-cover border border-slate-200" src="{{ asset('storage/' . $emp->documents['pas_foto']) }}" alt="">
                                    @else
                                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-600 border border-slate-200 text-xs">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="font-bold text-slate-900 text-sm">{{ $emp->user->name ?? 'Tidak Ada User' }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5">{{ $emp->user->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-bold text-slate-900 font-mono text-xs">{{ $emp->employee_id_number }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">KTP: {{ $emp->nik }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-bold text-slate-800 text-sm">{{ $emp->position->name ?? '-' }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $emp->position->department->name ?? '-' }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold capitalize
                                    {{ $emp->employment_status === 'tetap' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                    {{ $emp->employment_status === 'kontrak' ? 'bg-sky-50 text-sky-700' : '' }}
                                    {{ $emp->employment_status === 'magang' ? 'bg-amber-50 text-amber-700' : '' }}
                                    {{ $emp->employment_status === 'pkl' ? 'bg-purple-50 text-purple-700' : '' }}
                                    {{ $emp->employment_status === 'freelance' ? 'bg-slate-50 text-slate-700' : '' }}
                                ">
                                    {{ $emp->employment_status }}
                                </div>
                                <div class="mt-1 text-xs text-slate-400">Join: {{ $emp->join_date ? $emp->join_date->format('d M Y') : '-' }}</div>
                            </td>

                             <td class="whitespace-nowrap px-6 py-4 text-right text-xs font-medium space-x-2">
                                <button type="button" wire:click="downloadZip('{{ $emp->nik }}')" 
                                        class="inline-flex h-8.5 w-8.5 items-center justify-center rounded-xl bg-sky-50 border border-sky-100 text-primary hover:bg-sky-100 transition" title="Unduh Berkas ZIP">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </button>
                                <button type="button" wire:click="openEditModal({{ $emp->id }})" 
                                        class="inline-flex h-8.5 w-8.5 items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-800 transition" title="Edit Karyawan">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button type="button" wire:click="confirmDelete({{ $emp->id }})" 
                                        class="inline-flex h-8.5 w-8.5 items-center justify-center rounded-xl bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-100 hover:text-rose-700 transition" title="Hapus Karyawan">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                    <span class="mt-2 text-sm font-medium text-slate-400">Tidak ada data karyawan ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $employees->links() }}
            </div>
        @endif
    </div>

    <!-- Create & Edit Employee Modal -->
    @if($showEmployeeModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="$set('showEmployeeModal', false)"></div>

                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

                <div class="relative z-10 inline-block transform overflow-hidden rounded-3xl bg-white text-left align-middle shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-slate-200">
                    <!-- Header -->
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">{{ $isEditing ? 'Ubah Data Karyawan' : 'Tambah Karyawan Baru' }}</h3>
                            <p class="text-xs text-slate-500">Isi formulir secara lengkap untuk mendaftarkan atau memperbarui data karyawan.</p>
                        </div>
                        <button type="button" wire:click="$set('showEmployeeModal', false)" class="rounded-xl p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form Body -->
                    <form wire:submit.prevent="saveEmployee" class="p-6 max-h-[600px] overflow-y-auto">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            
                            <!-- Left Column: Inputs -->
                            <div class="lg:col-span-7 space-y-4">
                                <!-- Account Details Section -->
                                <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1">Detail Akun Pengguna</h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span></label>
                                        <input wire:model="name" type="text" placeholder="Masukkan nama lengkap..."
                                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition font-semibold">
                                        @error('name') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Email <span class="text-rose-500">*</span></label>
                                        <input wire:model="email" type="email" placeholder="karyawan@perusahaan.com"
                                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition font-semibold">
                                        @error('email') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-span-2">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                                            Kata Sandi (Password) 
                                            @if($isEditing)
                                                <span class="text-[9px] text-slate-400 font-medium normal-case">(Kosongkan jika tidak diganti)</span>
                                            @else
                                                <span class="text-rose-500">*</span>
                                            @endif
                                        </label>
                                        <input wire:model="password" type="password" placeholder="Minimal 8 karakter..."
                                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition font-semibold">
                                        @error('password') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Employee Profile Section -->
                                <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1 pt-2">Profil Karyawan</h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">NIK KTP <span class="text-rose-500">*</span></label>
                                        <input wire:model="nik" type="text" maxLength="16" placeholder="NIK 16 Digit..."
                                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition font-semibold font-mono tracking-wider">
                                        @error('nik') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">No. WhatsApp <span class="text-rose-500">*</span></label>
                                        <input wire:model="phone" type="text" placeholder="08xxxxxxxxxx"
                                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition font-semibold font-semibold">
                                        @error('phone') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">NIK Karyawan (Internal)</label>
                                        <input wire:model="employee_id_number" type="text" placeholder="Auto-generate jika dikosongkan..."
                                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition font-semibold font-mono">
                                        @error('employee_id_number') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Posisi Jabatan <span class="text-rose-500">*</span></label>
                                        <select wire:model="position_id" 
                                                class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition font-semibold">
                                            <option value="">-- Pilih Jabatan --</option>
                                            @foreach($positions as $pos)
                                                <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->department->name ?? '-' }})</option>
                                            @endforeach
                                        </select>
                                        @error('position_id') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status Kerja <span class="text-rose-500">*</span></label>
                                        <select wire:model="employment_status" 
                                                class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition font-semibold font-semibold font-semibold">
                                            <option value="tetap">Tetap (PKWTT)</option>
                                            <option value="kontrak">Kontrak (PKWT)</option>
                                            <option value="magang">Magang</option>
                                            <option value="pkl">PKL</option>
                                            <option value="freelance">Freelance</option>
                                        </select>
                                        @error('employment_status') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Bergabung <span class="text-rose-500">*</span></label>
                                        <input wire:model="join_date" type="date"
                                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition font-semibold font-semibold">
                                        @error('join_date') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>



                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Gaji Pokok (Rp) <span class="text-xs text-slate-400 font-normal">(Kosongkan untuk pakai default divisi)</span></label>
                                        <input wire:model="base_salary" type="number" min="0" placeholder="Masukkan gaji pokok..."
                                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition font-semibold font-semibold">
                                        @error('base_salary') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Lengkap Karyawan</label>
                                    <textarea wire:model="address" rows="2" placeholder="Jl. Raya No. 123, Kota..."
                                              class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition font-semibold font-semibold"></textarea>
                                    @error('address') <span class="text-[10px] text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- KTP Identitas Section -->
                                <div class="flex items-center justify-between border-b border-slate-100 pb-1 pt-2">
                                    <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Data Identitas KTP Karyawan</h4>
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
                                <div class="grid grid-cols-2 gap-3 bg-slate-50/50 p-4 rounded-2xl border border-slate-200">
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Tempat Lahir</label>
                                        <input wire:model="tempat_lahir" type="text" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Tanggal Lahir</label>
                                        <input wire:model="tanggal_lahir" type="date" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Jenis Kelamin</label>
                                        <select wire:model="jenis_kelamin" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:outline-none transition">
                                            <option value="">-- Pilih --</option>
                                            <option value="Laki-laki">Laki-laki</option>
                                            <option value="Perempuan">Perempuan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Agama</label>
                                        <input wire:model="agama" type="text" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Status Perkawinan</label>
                                        <select wire:model="status_kawin" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:outline-none transition">
                                            <option value="">-- Pilih --</option>
                                            <option value="Belum Kawin">Belum Kawin</option>
                                            <option value="Kawin">Kawin</option>
                                            <option value="Cerai Hidup">Cerai Hidup</option>
                                            <option value="Cerai Mati">Cerai Mati</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Pekerjaan</label>
                                        <input wire:model="pekerjaan" type="text" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:outline-none transition">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[9px] font-bold text-slate-500 uppercase block mb-1">Kewarganegaraan</label>
                                        <select wire:model="kewarganegaraan" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-slate-900 text-xs focus:outline-none transition">
                                            <option value="WNI">WNI</option>
                                            <option value="WNA">WNA</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column: Documents Onboarding -->
                            <div class="lg:col-span-5 space-y-4 border-l border-slate-100 lg:pl-6">
                                <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-1">Berkas & Dokumen Onboarding</h4>
                                
                                @if($isEditing && ($signedContractPath || !empty($documents)))
                                    <div class="space-y-3">
                                        @if($signedContractPath)
                                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-sky-50 border border-sky-100/60 transition">
                                                <div class="flex items-center">
                                                    <svg class="h-5 w-5 text-primary mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <span class="text-xs font-bold text-slate-850">Scan Kontrak Kerja (SPK)</span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <button type="button" wire:click="setPreviewDoc('{{ asset('storage/' . $signedContractPath) }}')" class="px-2 py-1 text-[10px] font-bold text-primary bg-sky-100 rounded-lg hover:bg-sky-200 transition">
                                                        Lihat
                                                    </button>
                                                    <a href="{{ asset('storage/' . $signedContractPath) }}" download 
                                                       class="px-2 py-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition">
                                                        Unduh
                                                    </a>
                                                </div>
                                            </div>
                                        @endif

                                        @foreach($documents as $key => $doc)
                                            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 hover:bg-slate-100/75 border border-slate-100 transition">
                                                <div class="flex items-center">
                                                    <svg class="h-5 w-5 text-slate-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <span class="text-xs font-semibold text-slate-700 capitalize">{{ str_replace('_', ' ', $key) }}</span>
                                                </div>
                                                <button type="button" wire:click="setPreviewDoc('{{ asset('storage/' . $doc) }}')" class="px-2 py-1 text-[10px] font-bold text-primary bg-sky-50 rounded-lg hover:bg-sky-100 transition">
                                                    Lihat
                                                </button>
                                            </div>
                                        @endforeach
                                        
                                        @if($activeDocUrl)
                                            <div class="mt-4 border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                                                <div class="bg-slate-100 px-3 py-1.5 flex items-center justify-between border-b border-slate-200">
                                                    <span class="text-[10px] font-bold text-slate-500">Pratinjau Dokumen</span>
                                                    <button type="button" wire:click="$set('activeDocUrl', null)" class="text-slate-400 hover:text-slate-600 transition">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <iframe src="{{ $activeDocUrl }}" class="w-full h-80" frameborder="0"></iframe>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center py-8 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                                        <svg class="h-8 w-8 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z" />
                                        </svg>
                                        <p class="text-[10px] text-slate-400 font-semibold">Belum ada berkas onboarding yang diunggah.</p>
                                    </div>
                                @endif
                                <!-- UPLOAD BERKAS KARYAWAN -->
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/60 space-y-4 mt-4">
                                    <h5 class="text-xs font-bold text-slate-700 border-b border-slate-200 pb-1.5">Unggah / Ganti Berkas Karyawan</h5>
                                    
                                    <div class="space-y-3">
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
                                        <div>
                                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Dokumen Pendukung (Multi-upload)</label>
                                            <input type="file" wire:model="filePendukung" multiple class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="mt-6 flex justify-end space-x-3 border-t border-slate-100 pt-4">
                            <button type="button" wire:click="$set('showEmployeeModal', false)" 
                                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                                Batalkan
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl shadow-md shadow-sky-500/20 hover:opacity-90 transition">
                                {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Karyawan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="$set('showDeleteModal', false)"></div>

                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

                <div class="relative z-10 inline-block transform overflow-hidden rounded-3xl bg-white p-6 text-left align-middle shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-sm border border-slate-200">
                    <div class="text-center space-y-4">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-rose-50 text-rose-600 border border-rose-100">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Hapus Karyawan?</h3>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Apakah Anda yakin ingin menghapus data karyawan ini? Tindakan ini akan menghapus akun login karyawan dan tidak dapat dibatalkan.</p>
                        </div>
                        <div class="flex space-x-3 pt-2">
                            <button type="button" wire:click="$set('showDeleteModal', false)" 
                                    class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                                Batalkan
                            </button>
                            <button type="button" wire:click="deleteEmployee" 
                                    class="w-full py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-rose-600/25">
                                Hapus Permanen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

