<div>
    @php $title = 'Pengajuan Cuti Tahunan'; @endphp

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500">Ajukan cuti kerja tahunan Anda dan tinjau status persetujuan dari Manager serta HRD.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button type="button" wire:click="$toggle('showCreateForm')" 
                    class="inline-flex items-center justify-center rounded-2xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-sky-500/30 transition">
                {{ $showCreateForm ? 'Batal' : 'Ajukan Cuti Baru' }}
            </button>
        </div>
    </div>

    <!-- Stats Quota Info -->
    <div class="mt-6 p-6 bg-white rounded-3xl border border-slate-200/60 shadow-sm flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Kuota Cuti Tersedia</h3>
            <p class="text-3xl font-black text-slate-900 mt-1">{{ $employee->leave_quota }} Hari</p>
        </div>
        <div class="text-right">
            <span class="text-xs text-slate-400 font-semibold block">Default Kuota Tahunan</span>
            <span class="text-sm font-bold text-slate-800">12 Hari / Tahun</span>
        </div>
    </div>


    @if($showCreateForm)
        <!-- Request Leave Form Card -->
        <div class="mt-6 bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Formulir Pengajuan Cuti</h3>
            
            <form wire:submit.prevent="submitRequest" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Start Date -->
                    <div>
                        <label for="startDate" class="block text-sm font-semibold text-slate-700">Tanggal Mulai Cuti</label>
                        <input wire:model="startDate" id="startDate" type="date" 
                               class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                        @error('startDate') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="endDate" class="block text-sm font-semibold text-slate-700">Tanggal Selesai Cuti</label>
                        <input wire:model="endDate" id="endDate" type="date" 
                               class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                        @error('endDate') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Reason -->
                    <div class="md:col-span-2">
                        <label for="reason" class="block text-sm font-semibold text-slate-700">Alasan Mengambil Cuti</label>
                        <textarea wire:model="reason" id="reason" rows="3" placeholder="Sebutkan keperluan cuti Anda (misal: acara keluarga, berobat, dll)"
                                  class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm"></textarea>
                        @error('reason') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Proof File -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700">Dokumen Bukti Pendukung (Opsional, Misal: Surat Sakit Dokter)</label>
                        <input type="file" wire:model="fileProof" class="mt-1.5 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        @error('fileProof') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" wire:click="$set('showCreateForm', false)" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex items-center justify-center rounded-2xl bg-primary px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-sky-500/30 transition active:scale-[0.98]">
                        <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Kirim Permohonan Cuti</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Past Requests List -->
    <div class="mt-8 bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
        <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Riwayat Pengajuan Cuti</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">
                    <tr>
                        <th class="px-6 py-4">Periode Cuti</th>
                        <th class="px-6 py-4">Durasi</th>
                        <th class="px-6 py-4">Alasan</th>
                        <th class="px-6 py-4">Bukti</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($pastRequests as $req)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $req->start_date->format('d M Y') }}</div>
                                <div class="text-xs text-slate-400">s/d {{ $req->end_date->format('d M Y') }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-slate-800">
                                {{ $req->days_requested }} Hari
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate font-medium text-slate-700">
                                {{ $req->reason }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if($req->proof_file_path)
                                    <a href="{{ asset('storage/' . $req->proof_file_path) }}" target="_blank" class="text-xs font-bold text-primary hover:underline">
                                        Lihat File
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
                                    {{ $req->status === 'approved_manager' ? 'Menunggu HRD' : '' }}
                                    {{ $req->status === 'approved_hrd' ? 'Disetujui HRD' : '' }}
                                    {{ $req->status === 'pending' ? 'Menunggu Manager' : '' }}
                                    {{ $req->status === 'rejected' ? 'Ditolak' : '' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                Belum ada pengajuan cuti yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
