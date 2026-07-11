<div>
    @php $title = 'Persetujuan Pengajuan Cuti'; @endphp

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500">Kelola dan tinjau seluruh permohonan cuti karyawan yang masuk. Persetujuan dilakukan berjenjang (Manager -> HRD).</p>
        </div>
        @if(in_array(auth()->user()->role, ['hrd', 'super_admin']))
            <div class="mt-4 sm:mt-0">
                <button type="button" wire:click="openCreateModal" 
                        class="inline-flex items-center justify-center rounded-2xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-sky-500/20 hover:bg-primary/95 transition">
                    <svg class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Pengajuan
                </button>
            </div>
        @endif
    </div>


    <!-- Filters -->
    <div class="mt-8 flex flex-wrap gap-2">
        <button type="button" wire:click="$set('filterStatus', '')" 
                class="px-4 py-2 rounded-2xl border text-xs font-semibold transition
                {{ $filterStatus === '' ? 'border-primary bg-sky-50/20 text-slate-900' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
            Semua Pengajuan
        </button>
        <button type="button" wire:click="$set('filterStatus', 'pending')" 
                class="px-4 py-2 rounded-2xl border text-xs font-semibold transition
                {{ $filterStatus === 'pending' ? 'border-primary bg-sky-50/20 text-slate-900' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
            Menunggu Manager
        </button>
        <button type="button" wire:click="$set('filterStatus', 'approved_manager')" 
                class="px-4 py-2 rounded-2xl border text-xs font-semibold transition
                {{ $filterStatus === 'approved_manager' ? 'border-primary bg-sky-50/20 text-slate-900' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
            Menunggu HRD
        </button>
        <button type="button" wire:click="$set('filterStatus', 'approved_hrd')" 
                class="px-4 py-2 rounded-2xl border text-xs font-semibold transition
                {{ $filterStatus === 'approved_hrd' ? 'border-primary bg-sky-50/20 text-slate-900' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
            Disetujui
        </button>
        <button type="button" wire:click="$set('filterStatus', 'rejected')" 
                class="px-4 py-2 rounded-2xl border text-xs font-semibold transition
                {{ $filterStatus === 'rejected' ? 'border-primary bg-sky-50/20 text-slate-900' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
            Ditolak
        </button>
    </div>

    <!-- Table List -->
    <div class="mt-6 overflow-hidden bg-white shadow-sm border border-slate-200/60 rounded-3xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">
                    <tr>
                        <th class="px-6 py-4">Karyawan</th>
                        <th class="px-6 py-4">Periode & Durasi</th>
                        <th class="px-6 py-4">Alasan</th>
                        <th class="px-6 py-4">Bukti Berkas</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $req->employee->user->name }}</div>
                                <div class="text-xs text-slate-400">ID: {{ $req->employee->employee_id_number }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $req->start_date->format('d M Y') }} s/d {{ $req->end_date->format('d M Y') }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">Durasi: <strong>{{ $req->days_requested }} Hari</strong></div>
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate font-medium text-slate-700">
                                {{ $req->reason }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if($req->proof_file_path)
                                    <a href="{{ asset('storage/' . $req->proof_file_path) }}" target="_blank" class="text-xs font-bold text-primary hover:underline">
                                        Lihat Bukti
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold capitalize
                                    {{ $req->status === 'pending' ? 'bg-amber-50 text-amber-800' : '' }}
                                    {{ $req->status === 'approved_manager' ? 'bg-purple-50 text-purple-800' : '' }}
                                    {{ $req->status === 'approved_hrd' ? 'bg-emerald-50 text-emerald-800' : '' }}
                                    {{ $req->status === 'rejected' ? 'bg-rose-50 text-rose-800' : '' }}
                                ">
                                    {{ $req->status === 'approved_manager' ? 'Disetujui Manager' : '' }}
                                    {{ $req->status === 'approved_hrd' ? 'Disetujui HRD' : '' }}
                                    {{ $req->status === 'pending' ? 'Pending (Manager)' : '' }}
                                    {{ $req->status === 'rejected' ? 'Ditolak' : '' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right space-x-2">
                                @php
                                    $userRole = auth()->user()->role;
                                    $canApprove = false;
                                    $btnLabel = '';
                                    
                                    if ($userRole === 'manager' && $req->status === 'pending') {
                                        $canApprove = true;
                                        $btnLabel = 'Setujui (Manager)';
                                    } elseif (in_array($userRole, ['hrd', 'super_admin']) && in_array($req->status, ['pending', 'approved_manager'])) {
                                        $canApprove = true;
                                        $btnLabel = 'Setujui Akhir (HRD)';
                                    }
                                @endphp

                                <div class="flex items-center justify-end gap-2">
                                    @if($canApprove)
                                        <button type="button" wire:click="approve({{ $req->id }})" 
                                                class="inline-flex items-center justify-center h-8.5 px-3 rounded-xl bg-primary text-xs font-semibold text-white hover:bg-primary/95 transition shadow-sm">
                                            {{ $btnLabel }}
                                        </button>
                                        <button type="button" wire:click="reject({{ $req->id }})" 
                                                class="inline-flex items-center justify-center h-8.5 px-3 rounded-xl border border-rose-200 text-xs font-semibold text-rose-600 bg-rose-50/20 hover:bg-rose-50 transition">
                                            Tolak
                                        </button>
                                    @endif
                                    @if(in_array(auth()->user()->role, ['hrd', 'super_admin']))
                                        <button type="button" wire:click="openEditModal({{ $req->id }})" title="Edit Pengajuan"
                                                class="inline-flex items-center justify-center h-8.5 w-8.5 rounded-xl border border-slate-200 text-primary bg-sky-50/10 hover:bg-sky-50 transition">
                                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button type="button" wire:click="confirmDelete({{ $req->id }})" title="Hapus Pengajuan"
                                                class="inline-flex items-center justify-center h-8.5 w-8.5 rounded-xl border border-slate-200 text-rose-600 bg-white hover:bg-rose-50 transition">
                                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @elseif(!$canApprove)
                                        <span class="text-xs text-slate-400">Tidak ada aksi</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                Tidak ada pengajuan cuti masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <!-- Create Leave Request Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showCreateModal', false)"></div>
                <div class="relative z-10 w-full max-w-lg transform overflow-hidden rounded-3xl bg-white p-6 text-left shadow-2xl transition-all border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Buat Pengajuan Cuti Baru</h3>
                    <form wire:submit.prevent="createLeaveRequest" class="space-y-4">
                        <!-- Searchable Select for Employee -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Pilih Karyawan <span class="text-rose-500">*</span></label>
                            <div class="mt-1.5 relative">
                                <input type="text" 
                                       wire:model.live.debounce.300ms="searchEmployee" 
                                       @focus="open = true" 
                                       placeholder="Ketik nama karyawan..." 
                                       class="block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-955 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm font-semibold">
                                
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    @if($employeeId)
                                        <button type="button" wire:click="clearEmployeeSelection" @click="open = false" class="text-slate-400 hover:text-slate-600 transition">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @else
                                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <div x-show="open" 
                                 class="absolute z-50 mt-1 w-full rounded-2xl bg-white border border-slate-200 shadow-xl max-h-48 overflow-y-auto"
                                 style="display: none;">
                                <ul class="py-1">
                                    @forelse($searchEmployees as $emp)
                                        <li>
                                            <button type="button" 
                                                    wire:click="selectEmployeeForForm({{ $emp->id }}, '{{ addslashes($emp->user->name) }}')"
                                                    @click="open = false"
                                                    class="w-full text-left px-4 py-2 hover:bg-slate-50 text-slate-800 text-sm font-semibold transition flex justify-between items-center">
                                                <span>{{ $emp->user->name }}</span>
                                                <span class="text-xs text-slate-400 font-mono">{{ $emp->employee_id_number }}</span>
                                            </button>
                                        </li>
                                    @empty
                                        <li class="px-4 py-3 text-xs text-slate-450 text-center font-medium">Tidak ada karyawan ditemukan.</li>
                                    @endforelse
                                </ul>
                            </div>
                            @error('employeeId') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="startDate" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Mulai <span class="text-rose-500">*</span></label>
                                <input wire:model="startDate" id="startDate" type="date"
                                       class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-955 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                @error('startDate') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="endDate" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Selesai <span class="text-rose-500">*</span></label>
                                <input wire:model="endDate" id="endDate" type="date"
                                       class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-955 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                @error('endDate') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Status Awal</label>
                            <select wire:model="status" id="status" class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-955 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                <option value="pending">Pending (Menunggu Manager)</option>
                                <option value="approved_manager">Disetujui Manager (Menunggu HRD)</option>
                                <option value="approved_hrd">Disetujui HRD (Potong Kuota)</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>

                        <div>
                            <label for="reason" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Alasan Cuti <span class="text-rose-500">*</span></label>
                            <textarea wire:model="reason" id="reason" rows="3" placeholder="Tuliskan alasan pengajuan cuti..."
                                      class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-955 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm"></textarea>
                            @error('reason') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="fileProof" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Unggah Bukti (Opsional)</label>
                            <input wire:model="fileProof" id="fileProof" type="file" accept=".pdf,.png,.jpg,.jpeg"
                                   class="mt-1.5 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-primary hover:file:bg-sky-100 transition">
                            @error('fileProof') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex justify-end space-x-3 border-t border-slate-100 pt-4 mt-6">
                            <button type="button" wire:click="$set('showCreateModal', false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl shadow transition">Simpan Pengajuan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Leave Request Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showEditModal', false)"></div>
                <div class="relative z-10 w-full max-w-lg transform overflow-hidden rounded-3xl bg-white p-6 text-left shadow-2xl transition-all border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Ubah Pengajuan Cuti</h3>
                    <form wire:submit.prevent="updateLeaveRequest" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="startDate" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Mulai <span class="text-rose-500">*</span></label>
                                <input wire:model="startDate" id="startDate" type="date"
                                       class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-955 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                @error('startDate') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="endDate" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Selesai <span class="text-rose-500">*</span></label>
                                <input wire:model="endDate" id="endDate" type="date"
                                       class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-955 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                @error('endDate') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Status Cuti</label>
                            <select wire:model="status" id="status" class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-955 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                <option value="pending">Pending (Menunggu Manager)</option>
                                <option value="approved_manager">Disetujui Manager (Menunggu HRD)</option>
                                <option value="approved_hrd">Disetujui HRD (Potong Kuota)</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>

                        <div>
                            <label for="reason" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Alasan Cuti <span class="text-rose-500">*</span></label>
                            <textarea wire:model="reason" id="reason" rows="3" placeholder="Tuliskan alasan pengajuan cuti..."
                                      class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-955 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm"></textarea>
                            @error('reason') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="fileProof" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Unggah Bukti Baru (Kosongkan jika tidak diganti)</label>
                            <input wire:model="fileProof" id="fileProof" type="file" accept=".pdf,.png,.jpg,.jpeg"
                                   class="mt-1.5 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-primary hover:file:bg-sky-100 transition">
                            @error('fileProof') <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex justify-end space-x-3 border-t border-slate-100 pt-4 mt-6">
                            <button type="button" wire:click="$set('showEditModal', false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl shadow transition">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Leave Request Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showDeleteModal', false)"></div>
                <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 text-left shadow-2xl transition-all border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-2">Hapus Pengajuan Cuti</h3>
                    <p class="text-xs text-slate-500 mb-4 font-semibold">Apakah Anda yakin ingin menghapus permohonan cuti ini secara permanen dari sistem?</p>
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="$set('showDeleteModal', false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Batal</button>
                        <button type="button" wire:click="deleteLeaveRequest" class="px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-xl shadow hover:bg-rose-700 transition">Hapus Permanen</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
