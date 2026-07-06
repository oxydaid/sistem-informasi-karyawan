<div>
    @php $title = 'Divisi & Jabatan'; @endphp

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500">Kelola master data divisi perusahaan beserta daftar jabatan dan referensi gaji dasar.</p>
        </div>
    </div>


    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Departments Column (Left) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Add Department Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 mb-4">Tambah Divisi Baru</h3>
                <form wire:submit.prevent="addDepartment" class="space-y-4">
                    <div>
                        <label for="newDeptName" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Divisi</label>
                        <div class="mt-1.5 flex gap-2">
                            <input wire:model="newDeptName" id="newDeptName" type="text" placeholder="Misal: Marketing, IT, Support" 
                                   class="block flex-1 rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-sky-500/20 hover:bg-primary/95 transition">
                                Tambah
                            </button>
                        </div>
                        @error('newDeptName')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </form>
            </div>

            <!-- Departments List Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 mb-4">Daftar Divisi</h3>
                <div class="space-y-2">
                    @forelse($departments as $dept)
                        <div wire:click="selectDepartment({{ $dept->id }})" 
                                class="w-full flex items-center justify-between p-4 rounded-2xl border transition text-left group cursor-pointer
                                {{ $selectedDeptId === $dept->id 
                                    ? 'border-primary bg-sky-50/20 text-slate-900' 
                                    : 'border-slate-100 hover:bg-slate-50 text-slate-700' 
                                }}">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-lg flex items-center justify-center font-bold text-sm mr-3 transition
                                    {{ $selectedDeptId === $dept->id ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200' }}">
                                    {{ substr($dept->name, 0, 1) }}
                                </div>
                                <span class="font-semibold text-sm">{{ $dept->name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center h-6 px-2 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                    {{ $dept->positions->count() }} Jabatan
                                </span>
                                <button type="button" wire:click.stop="editDepartment({{ $dept->id }})" class="p-1.5 text-slate-400 hover:text-primary transition" title="Ubah Nama Divisi">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button type="button" wire:click.stop="confirmDeptDelete({{ $dept->id }})" class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="Hapus Divisi">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 text-center py-6">Belum ada divisi terdaftar.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Selected Department Detail & Positions Column (Right) -->
        <div class="lg:col-span-7">
            @if($selectedDepartment)
                <div class="space-y-6">
                    <!-- Positions List -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Daftar Jabatan</h3>
                                <p class="text-xs text-slate-400">Divisi: {{ $selectedDepartment->name }}</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @forelse($selectedDepartment->positions as $pos)
                                <div class="flex items-center justify-between p-4 bg-slate-50/50 rounded-2xl border border-slate-100">
                                    <span class="font-semibold text-sm text-slate-800">{{ $pos->name }}</span>
                                    <div class="flex items-center gap-4">
                                        <div class="text-right">
                                            <span class="text-sm font-bold text-slate-900">Rp {{ number_format($pos->base_salary, 0, ',', '.') }}</span>
                                            <p class="text-[10px] text-slate-400">Gaji Pokok Default</p>
                                        </div>
                                        <div class="flex items-center gap-1.5 border-l border-slate-200 pl-3">
                                            <button type="button" wire:click="editPosition({{ $pos->id }})" class="p-1.5 text-slate-400 hover:text-primary transition" title="Edit Jabatan">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button type="button" wire:click="confirmPosDelete({{ $pos->id }})" class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="Hapus Jabatan">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400 text-center py-8">Belum ada jabatan pada divisi ini.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Add Position Form -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm">
                        <h3 class="text-base font-bold text-slate-900 mb-4">Tambah Jabatan Baru di {{ $selectedDepartment->name }}</h3>
                        <form wire:submit.prevent="addPosition" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="newPosName" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Jabatan</label>
                                    <input wire:model="newPosName" id="newPosName" type="text" placeholder="Misal: NOC Staff, Sales Exec" 
                                           class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                    @error('newPosName')
                                        <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="newPosSalary" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Gaji Pokok Default (Rp)</label>
                                    <input wire:model="newPosSalary" id="newPosSalary" type="number" placeholder="Misal: 4500000" 
                                           class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                    @error('newPosSalary')
                                        <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-sky-500/20 hover:bg-primary/95 transition">
                                    Simpan Jabatan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="h-full flex flex-col items-center justify-center bg-white p-12 rounded-3xl border border-slate-200/60 shadow-sm border-dashed">
                    <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                    <span class="mt-3 text-sm font-medium text-slate-400 text-center">Pilih salah satu divisi di sebelah kiri untuk melihat jabatan atau menambah jabatan baru.</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Department Modal -->
    @if($editingDeptId)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="cancelEditDepartment"></div>
                <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 text-left shadow-2xl transition-all border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Ubah Nama Divisi</h3>
                    <form wire:submit.prevent="updateDepartment" class="space-y-4">
                        <div>
                            <label for="editingDeptName" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Divisi</label>
                            <input wire:model="editingDeptName" id="editingDeptName" type="text"
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-955 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            @error('editingDeptName')
                                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="cancelEditDepartment" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl shadow transition">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Department Modal -->
    @if($confirmingDeptDeleteId)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('confirmingDeptDeleteId', null)"></div>
                <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 text-left shadow-2xl transition-all border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-2">Hapus Divisi</h3>
                    <p class="text-xs text-slate-500 mb-4 font-semibold">Apakah Anda yakin ingin menghapus divisi ini? Menghapus divisi juga akan menghapus semua jabatan di dalamnya secara permanen.</p>
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="$set('confirmingDeptDeleteId', null)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Batal</button>
                        <button type="button" wire:click="deleteDepartment" class="px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-xl shadow hover:bg-rose-700 transition">Hapus Permanen</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Position Modal -->
    @if($editingPosId)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="cancelEditPosition"></div>
                <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 text-left shadow-2xl transition-all border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Ubah Detail Jabatan</h3>
                    <form wire:submit.prevent="updatePosition" class="space-y-4">
                        <div>
                            <label for="editingPosName" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Jabatan</label>
                            <input wire:model="editingPosName" id="editingPosName" type="text"
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-955 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            @error('editingPosName')
                                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="editingPosSalary" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Gaji Pokok Default (Rp)</label>
                            <input wire:model="editingPosSalary" id="editingPosSalary" type="number"
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-955 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            @error('editingPosSalary')
                                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="cancelEditPosition" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl shadow transition">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Position Modal -->
    @if($confirmingPosDeleteId)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('confirmingPosDeleteId', null)"></div>
                <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 text-left shadow-2xl transition-all border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-2">Hapus Jabatan</h3>
                    <p class="text-xs text-slate-500 mb-4 font-semibold">Apakah Anda yakin ingin menghapus jabatan ini secara permanen?</p>
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="$set('confirmingPosDeleteId', null)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Batal</button>
                        <button type="button" wire:click="deletePosition" class="px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-xl shadow hover:bg-rose-700 transition">Hapus Permanen</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
