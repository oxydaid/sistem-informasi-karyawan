<div>
    @php $title = 'Generate Kontrak Pelamar'; @endphp

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500">Buat draf Surat Perjanjian Kerja (SPK) digital dengan menginput detail tipe pekerjaan, durasi kerja, dan nominal gaji.</p>
        </div>
    </div>


    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Left Side: Applicant Summary Info -->
        <div class="lg:col-span-4">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
                <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Profil Pelamar</h3>
                
                <div class="space-y-4">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Nama Lengkap</span>
                        <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ $selectedApplicant->name }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">NIK KTP</span>
                        <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ $selectedApplicant->nik }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Email</span>
                        <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ $selectedApplicant->email }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">No. Telepon / WA</span>
                        <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ $selectedApplicant->phone }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Contract Terms Form -->
        <div class="lg:col-span-8">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
                <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Ketentuan Kontrak Kerja (SPK)</h3>
                
                <form wire:submit.prevent="generateContract" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Select Position -->
                        <div>
                            <label for="positionId" class="block text-sm font-semibold text-slate-700">Jabatan Pekerjaan</label>
                            <select wire:model.live="positionId" id="positionId" 
                                    class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                <option value="">Pilih Jabatan...</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->department->name }})</option>
                                @endforeach
                            </select>
                            @error('positionId') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Select Employment Type -->
                        <div>
                            <label for="employmentType" class="block text-sm font-semibold text-slate-700">Tipe Hubungan Kerja</label>
                            <select wire:model="employmentType" id="employmentType" 
                                    class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                                <option value="">Pilih Tipe...</option>
                                <option value="tetap">Tetap (PKWTT)</option>
                                <option value="kontrak">Kontrak (PKWT)</option>
                                <option value="magang">Magang</option>
                                <option value="pkl">PKL / Praktik Kerja</option>
                                <option value="freelance">Freelance</option>
                            </select>
                            @error('employmentType') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="startDate" class="block text-sm font-semibold text-slate-700">Tanggal Mulai Bekerja</label>
                            <input wire:model="startDate" id="startDate" type="date" 
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            @error('startDate') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="endDate" class="block text-sm font-semibold text-slate-700">Tanggal Selesai Kontrak (Opsional)</label>
                            <input wire:model="endDate" id="endDate" type="date" placeholder="Kosongkan jika Karyawan Tetap"
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            @error('endDate') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Base Salary -->
                        <div class="md:col-span-2">
                            <label for="salary" class="block text-sm font-semibold text-slate-700">Gaji Pokok Default (Rp)</label>
                            <div class="mt-1.5 relative rounded-2xl shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <span class="text-sm font-semibold text-slate-400">Rp</span>
                                </div>
                                <input wire:model="salary" id="salary" type="number" placeholder="5000000"
                                       class="block w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-11 pr-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1.5">Nilai ini otomatis terisi berdasarkan base salary jabatan pilihan, namun HRD dibebankan wewenang untuk mengubahnya secara mandiri.</p>
                            @error('salary') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                        <a href="{{ route('admin.applicants') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Kembali
                        </a>
                        <button type="submit" wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center rounded-2xl bg-primary px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-sky-500/30 transition active:scale-[0.98]">
                            <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove>Generate Kontrak & Undang Onboarding</span>
                            <span wire:loading>Membuat draf PDF kontrak...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
