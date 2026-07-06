<div>
    @php $title = 'Riwayat Kontrak Kerja (SPK)'; @endphp

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500">Daftar lengkap Surat Perjanjian Kerja (SPK) Anda yang terdaftar secara resmi di perusahaan.</p>
        </div>
    </div>

    <!-- Contracts List -->
    <div class="mt-8 space-y-4">
        @forelse($contracts as $contract)
            <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-slate-900 capitalize">Tipe: {{ $contract->employment_type }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                            Aktif & Signed
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">Jabatan: {{ $contract->position->name }} ({{ $contract->position->department->name }})</p>
                    <p class="text-xs text-slate-400">
                        Durasi: {{ $contract->start_date->format('d M Y') }} s/d {{ $contract->end_date ? $contract->end_date->format('d M Y') : 'Permanen' }}
                    </p>
                </div>
                <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
                    <div class="text-left md:text-right">
                        <span class="text-xs text-slate-400 block">Gaji Pokok Terikat</span>
                        <span class="text-sm font-bold text-slate-900">Rp {{ number_format($contract->salary, 0, ',', '.') }}</span>
                    </div>
                    @if($contract->contract_file_path)
                        <a href="{{ asset('storage/' . $contract->contract_file_path) }}" target="_blank" 
                           class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white shadow-md shadow-sky-500/25 hover:bg-primary/95 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white p-12 text-center border border-slate-200/60 rounded-3xl">
                <p class="text-sm text-slate-400">Belum ada riwayat kontrak kerja yang tercatat.</p>
            </div>
        @endforelse
    </div>
</div>
