<div>
    @php $title = 'Manajemen User & RBAC'; @endphp

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500 font-medium">Kelola akun pengguna sistem, atur hak akses peran (RBAC), dan buat atau nonaktifkan akun staf.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button wire:click="openCreateModal" type="button"
                    class="inline-flex items-center justify-center rounded-2xl bg-primary px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-sky-500/30 transition duration-150 active:scale-[0.98]">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Tambah Pengguna
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-between items-center bg-white p-4 rounded-3xl border border-slate-200/60 shadow-sm">
        <!-- Search -->
        <div class="relative w-full sm:max-w-xs">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau email..."
                   class="block w-full rounded-2xl border border-slate-200 bg-slate-50 pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
        </div>

        <!-- Role Filter -->
        <div class="w-full sm:w-auto">
            <select wire:model.live="filterRole"
                    class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                <option value="">Semua Peran / Role</option>
                <option value="super_admin">Super Admin</option>
                <option value="hrd">HRD</option>
                <option value="finance">Keuangan (Finance)</option>
                <option value="manager">Manager / Kepala Divisi</option>
                <option value="employee">Karyawan</option>
            </select>
        </div>
    </div>

    <!-- Table Section -->
    <div class="mt-6 bg-white border border-slate-200/60 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200/60 text-slate-600 font-bold text-xs uppercase tracking-wider whitespace-nowrap">
                        <th class="px-6 py-4">Nama Pengguna</th>
                        <th class="px-6 py-4">Alamat Email</th>
                        <th class="px-6 py-4">Hak Akses / Peran</th>
                        <th class="px-6 py-4">Tanggal Dibuat</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold border border-slate-200/60 text-xs">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeColor = match($user->role) {
                                        'super_admin' => 'bg-indigo-50 border-indigo-100 text-indigo-700',
                                        'hrd' => 'bg-emerald-50 border-emerald-100 text-emerald-700',
                                        'finance' => 'bg-amber-50 border-amber-100 text-amber-700',
                                        'manager' => 'bg-sky-50 border-sky-100 text-sky-700',
                                        default => 'bg-slate-50 border-slate-100 text-slate-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-lg border px-2 py-0.5 text-xs font-bold capitalize {{ $badgeColor }}">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openEditModal({{ $user->id }})" type="button"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-slate-400 hover:text-primary hover:bg-slate-100 border border-transparent hover:border-slate-200 transition active:scale-95">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    @if(auth()->id() !== $user->id)
                                        <button wire:click="openDeleteModal({{ $user->id }})" type="button"
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-slate-100 border border-transparent hover:border-slate-200 transition active:scale-95">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="mt-2 text-xs font-semibold">Tidak ada data pengguna yang ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Create Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeCreateModal"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative z-10 inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                    <form wire:submit.prevent="createUser">
                        <div class="p-6">
                            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Tambah Pengguna Baru</h3>
                            
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Nama Lengkap</label>
                                    <input wire:model="name" type="text" placeholder="Masukkan nama lengkap..."
                                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                                    @error('name') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Alamat Email</label>
                                    <input wire:model="email" type="email" placeholder="nama@email.com"
                                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                                    @error('email') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Password</label>
                                    <input wire:model="password" type="password" placeholder="Minimal 6 karakter..."
                                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                                    @error('password') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Hak Akses / Peran</label>
                                    <select wire:model="role"
                                            class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                                        <option value="super_admin">Super Admin</option>
                                        <option value="hrd">HRD</option>
                                        <option value="finance">Keuangan (Finance)</option>
                                        <option value="manager">Manager / Kepala Divisi</option>
                                        <option value="employee">Karyawan</option>
                                    </select>
                                    @error('role') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 rounded-b-3xl border-t border-slate-100">
                            <button type="submit"
                                    class="inline-flex justify-center rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white shadow-md hover:bg-primary/95 transition duration-150 active:scale-95">
                                Simpan
                            </button>
                            <button type="button" wire:click="closeCreateModal"
                                    class="inline-flex justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition active:scale-95">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeEditModal"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative z-10 inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                    <form wire:submit.prevent="updateUser">
                        <div class="p-6">
                            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Edit Data Pengguna</h3>
                            
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Nama Lengkap</label>
                                    <input wire:model="name" type="text" placeholder="Masukkan nama lengkap..."
                                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                                    @error('name') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Alamat Email</label>
                                    <input wire:model="email" type="email" placeholder="nama@email.com"
                                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                                    @error('email') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                                    <input wire:model="password" type="password" placeholder="Masukkan password baru..."
                                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                                    @error('password') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Hak Akses / Peran</label>
                                    <select wire:model="role"
                                            class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                                        <option value="super_admin">Super Admin</option>
                                        <option value="hrd">HRD</option>
                                        <option value="finance">Keuangan (Finance)</option>
                                        <option value="manager">Manager / Kepala Divisi</option>
                                        <option value="employee">Karyawan</option>
                                    </select>
                                    @error('role') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 rounded-b-3xl border-t border-slate-100">
                            <button type="submit"
                                    class="inline-flex justify-center rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white shadow-md hover:bg-primary/95 transition duration-150 active:scale-95">
                                Simpan Perubahan
                            </button>
                            <button type="button" wire:click="closeEditModal"
                                    class="inline-flex justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition active:scale-95">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeDeleteModal"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative z-10 inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100">
                    <div class="p-6">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-sm font-bold text-slate-900" id="modal-title">Hapus Pengguna</h3>
                                <div class="mt-2">
                                    <p class="text-xs text-slate-500 leading-relaxed">Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 rounded-b-3xl border-t border-slate-100">
                        <button type="button" wire:click="deleteUser"
                                class="inline-flex justify-center rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-md hover:bg-rose-700 transition duration-150 active:scale-95">
                            Ya, Hapus
                        </button>
                        <button type="button" wire:click="closeDeleteModal"
                                class="inline-flex justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition active:scale-95">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
