<div>
    @php $title = 'Riwayat Slip Gaji Bulanan'; @endphp

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500">Tinjau rincian penerimaan gaji bulanan Anda, lengkap dengan rincian bonus KPI dan potongan absensi.</p>
        </div>
    </div>

    <!-- Payroll Table List -->
    <div class="mt-8 bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">
                    <tr>
                        <th class="px-6 py-4">Periode Gaji</th>
                        <th class="px-6 py-4">Gaji Pokok</th>
                        <th class="px-6 py-4">Bonus KPI</th>
                        <th class="px-6 py-4">Potongan (KPI / Cuti)</th>
                        <th class="px-6 py-4">Potongan Kasbon</th>
                        <th class="px-6 py-4">Gaji Bersih (Net)</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($payrolls as $pay)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-slate-900">
                                {{ $pay->month_year }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                Rp {{ number_format($pay->base_salary, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-emerald-600 font-semibold">
                                + Rp {{ number_format($pay->kpi_bonus, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-rose-600 font-semibold">
                                - Rp {{ number_format($pay->kpi_deduction + $pay->leave_deduction, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-rose-600 font-semibold">
                                - Rp {{ number_format($pay->cash_advance_deduction, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-black text-slate-900">
                                Rp {{ number_format($pay->net_salary, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if($pay->payslip_file_path)
                                    <a href="{{ asset('storage/' . $pay->payslip_file_path) }}" target="_blank" 
                                       class="inline-flex items-center justify-center h-8.5 px-3 rounded-xl border border-slate-200 text-xs font-semibold text-primary bg-sky-50/20 hover:bg-sky-50 transition">
                                        Unduh PDF
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 capitalize">{{ $pay->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                                Belum ada slip gaji yang diproses untuk Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
