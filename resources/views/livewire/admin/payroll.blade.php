<div>
    @php $title = 'Proses Penggajian Staf (Payroll)'; @endphp

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500">Kelola perhitungan gaji staf bulanan, validasi penyesuaian finansial, terbitkan slip gaji, dan lakukan pembayaran gaji.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-2">
            @if($payrolls->where('status', 'draft')->isNotEmpty())
                <button type="button" wire:click="approveAllPayroll" 
                        class="inline-flex items-center justify-center rounded-2xl bg-sky-50 px-5 py-2.5 text-sm font-semibold text-sky-700 border border-sky-200 hover:bg-sky-100/80 transition active:scale-[0.98]">
                    <span>Setujui Semua (Approve All)</span>
                </button>
            @endif
            <button type="button" wire:click="blastPayslipWa" 
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center rounded-2xl bg-emerald-50 px-5 py-2.5 text-sm font-semibold text-emerald-700 border border-emerald-200 hover:bg-emerald-100/80 transition active:scale-[0.98]">
                <svg wire:loading wire:target="blastPayslipWa" class="animate-spin -ml-1 mr-2 h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Blast Slip Gaji WA</span>
            </button>
            <button type="button" wire:click="generatePayroll" 
                    class="inline-flex items-center justify-center rounded-2xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-sky-500/30 transition active:scale-[0.98]">
                Kalkulasi Gaji Bulan Ini
            </button>
        </div>
    </div>


    <!-- Month Selector -->
    <div class="mt-8 bg-white p-4 rounded-3xl border border-slate-200/60 shadow-sm flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <span class="text-sm font-bold text-slate-700">Periode Gaji:</span>
            <select wire:model.live="monthYear" 
                    class="block rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                @for($i = 0; $i < 6; $i++)
                    @php $d = now()->subMonths($i); @endphp
                    <option value="{{ $d->format('m-Y') }}">{{ $d->translatedFormat('F Y') }}</option>
                @endfor
            </select>
        </div>
    </div>

    <!-- Payroll Table List -->
    <div class="mt-6 overflow-hidden bg-white shadow-sm border border-slate-200/60 rounded-3xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">
                    <tr>
                        <th class="px-6 py-4">Karyawan</th>
                        <th class="px-6 py-4">Gaji Pokok</th>
                        <th class="px-6 py-4">Potongan Kasbon</th>
                        <th class="px-6 py-4">Gaji Bersih (Net)</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($payrolls as $pay)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $pay->employee->user->name }}</div>
                                <div class="text-xs text-slate-400">{{ $pay->employee->employee_id_number }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                Rp {{ number_format($pay->base_salary, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-rose-600 font-semibold">
                                - Rp {{ number_format($pay->cash_advance_deduction, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-bold text-slate-900">
                                Rp {{ number_format($pay->net_salary, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold capitalize
                                    {{ $pay->status === 'draft' ? 'bg-amber-50 text-amber-800' : '' }}
                                    {{ $pay->status === 'approved' ? 'bg-sky-50 text-sky-800' : '' }}
                                    {{ $pay->status === 'paid' ? 'bg-emerald-50 text-emerald-800' : '' }}
                                ">
                                    {{ $pay->status }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right space-x-2">
                                @if($pay->status === 'draft')
                                    <button type="button" wire:click="approvePayroll({{ $pay->id }})" 
                                            class="inline-flex items-center justify-center h-8 px-2.5 rounded-xl bg-sky-500 text-xs font-semibold text-white hover:bg-sky-600 transition shadow-sm">
                                        Approve
                                    </button>
                                @elseif($pay->status === 'approved')
                                    <button type="button" wire:click="payPayroll({{ $pay->id }})" 
                                            class="inline-flex items-center justify-center h-8 px-2.5 rounded-xl bg-emerald-500 text-xs font-semibold text-white hover:bg-emerald-600 transition shadow-sm">
                                        Bayar (Paid)
                                    </button>
                                @elseif($pay->status === 'paid')
                                    @if($pay->payslip_file_path)
                                        <a href="{{ asset('storage/' . $pay->payslip_file_path) }}" target="_blank" 
                                           class="inline-flex items-center justify-center h-8.5 px-3 rounded-xl border border-slate-200 text-xs font-semibold text-primary bg-sky-50/20 hover:bg-sky-50 transition">
                                            Unduh Slip
                                        </a>
                                    @endif
                                    <button type="button" wire:click="sendPayslipWa({{ $pay->id }})" 
                                            wire:loading.attr="disabled"
                                            class="inline-flex items-center justify-center h-8.5 px-3 rounded-xl bg-emerald-50 text-xs font-semibold text-emerald-700 hover:bg-emerald-100/80 transition active:scale-[0.98] border border-emerald-200 shadow-sm">
                                        Kirim WA
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-400">
                                Belum ada perhitungan penggajian untuk periode ini. Silakan klik tombol "Kalkulasi Gaji Bulan Ini".
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
