<div>
    <div class="sm:mx-auto sm:w-full sm:max-w-2xl px-4">
        <!-- Logo -->
        <div class="flex justify-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-white shadow-lg shadow-sky-500/20">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                </svg>
            </div>
        </div>
        <h2 class="mt-4 text-center text-3xl font-extrabold tracking-tight text-slate-900">
            Onboarding Pelamar Kerja
        </h2>
        <p class="mt-2 text-center text-sm text-slate-500">
            Selamat, berkas Anda lolos screening! Tinjau kontrak kerja (SPK) di bawah ini dan lakukan tanda tangan digital.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-4xl px-4">
        @if($contract && $contract->status === 'approved')
            <!-- Onboarding Completed Card -->
            <div class="bg-white py-12 px-6 shadow-xl shadow-slate-200/50 rounded-3xl border border-slate-100 text-center space-y-6 max-w-xl mx-auto">
                <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                    </svg>
                </div>
                <div class="space-y-2">
                    <h3 class="text-2xl font-bold text-slate-900">Onboarding Selesai!</h3>
                    <p class="text-sm text-slate-500">
                        Kontrak SPK Anda telah sah disetujui oleh HRD. Akun portal karyawan Anda kini telah aktif.
                    </p>
                </div>
                
                <!-- Access Box -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200/60 text-left space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Informasi Akses Portal</h4>
                    <div>
                        <span class="text-xs font-semibold text-slate-500 block">Alamat Email</span>
                        <span class="text-sm font-bold text-slate-800">{{ $applicant->email }}</span>
                    </div>
                    <p class="text-[10px] text-slate-400">Silakan gunakan email terdaftar Anda untuk masuk ke portal. Jika Anda belum memiliki password, silakan hubungi HRD atau gunakan password default yang dikirimkan ke email Anda.</p>
                </div>

                <div class="pt-4">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-sky-500/30 transition">
                        Masuk ke Portal Karyawan
                    </a>
                </div>
            </div>
        @elseif($contract && $contract->status === 'uploaded')
            <!-- Waiting for HRD Verification Card -->
            <div class="bg-white py-12 px-6 shadow-xl shadow-slate-200/50 rounded-3xl border border-slate-100 text-center space-y-6 max-w-xl mx-auto">
                <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-amber-50 text-amber-600 border border-amber-200">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="space-y-2">
                    <h3 class="text-2xl font-bold text-slate-900">Berkas SPK Berhasil Diunggah!</h3>
                    <p class="text-sm text-slate-500">
                        Terima kasih. Berkas scan SPK fisik Anda yang telah ditandatangani sudah kami terima.
                    </p>
                </div>
                
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200/60 text-left space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Proses Verifikasi</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Tim HRD PT. Antigravity Network Indonesia saat ini sedang meninjau dan memverifikasi berkas yang Anda unggah. 
                        Setelah berkas disetujui oleh admin, akun portal karyawan Anda akan otomatis dibuat dan Anda akan mendapatkan akses penuh.
                    </p>
                </div>
            </div>
        @else
            <!-- Error or Info Alerts -->

            @if($applicant->status === 'pending')
                <!-- Pending Status Card -->
                <div class="bg-white p-12 shadow-xl shadow-slate-200/50 rounded-3xl border border-slate-100 text-center space-y-4 max-w-xl mx-auto border-dashed">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h6m-7-8h8a2 2 0 012 2v9a2 2 0 01-2 2H7a2 2 0 01-2-2V10a2 2 0 012-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Berkas Sedang Ditinjau</h3>
                    <p class="text-sm text-slate-500">
                        Terima kasih telah mendaftar. Berkas pendaftaran Anda saat ini sedang dalam proses screening awal oleh tim HRD. Mohon pantau halaman ini dan WhatsApp Anda untuk pemberitahuan selanjutnya.
                    </p>
                </div>
            @elseif($applicant->status === 'reviewed')
                <!-- Reviewed Status Card -->
                <div class="bg-white p-12 shadow-xl shadow-slate-200/50 rounded-3xl border border-slate-100 text-center space-y-4 max-w-xl mx-auto border-dashed">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Tinjauan Awal Selesai</h3>
                    <p class="text-sm text-slate-500">
                        Berkas lamaran Anda telah ditinjau dan dinyatakan memenuhi syarat administrasi awal. Kami sedang menjadwalkan langkah seleksi berikutnya. Info lebih lanjut akan segera dikirimkan ke nomor WhatsApp Anda.
                    </p>
                </div>
            @elseif($applicant->status === 'interviewing' || $applicant->status === 'interview')
                <!-- Interview Status Card -->
                <div class="bg-white p-12 shadow-xl shadow-slate-200/50 rounded-3xl border border-slate-100 text-center space-y-4 max-w-xl mx-auto border-dashed">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-purple-50 text-purple-600">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Tahap Wawancara (Interview)</h3>
                    <p class="text-sm text-slate-500">
                        Status lamaran Anda saat ini berada dalam **Tahap Wawancara (Interview)**. Silakan periksa detail jadwal dan rincian yang kami kirimkan melalui WhatsApp Anda, atau hubungi HRD kami jika ada kendala.
                    </p>
                </div>
            @elseif($applicant->status === 'rejected')
                <!-- Rejected Status Card -->
                <div class="bg-white p-12 shadow-xl shadow-slate-200/50 rounded-3xl border border-slate-100 text-center space-y-4 max-w-xl mx-auto border-dashed">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Lamaran Belum Sesuai</h3>
                    <p class="text-sm text-slate-500">
                        Terima kasih atas minat Anda untuk bergabung dengan kami. Setelah melakukan tinjauan mendalam, saat ini profil Anda belum sesuai dengan kebutuhan posisi yang dilamar. Tetap semangat dan semoga sukses di karir Anda selanjutnya!
                    </p>
                </div>
            @elseif(!$contract)
                <!-- Accepted but Contract not generated yet -->
                <div class="bg-white p-12 shadow-xl shadow-slate-200/50 rounded-3xl border border-slate-100 text-center space-y-4 max-w-xl mx-auto border-dashed">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Draf Kontrak Sedang Diproses</h3>
                    <p class="text-sm text-slate-500">
                        Selamat! Status lamaran Anda telah ditandai untuk diterima. Namun, tim HRD kami saat ini sedang menyusun draf Surat Perjanjian Kerja (SPK) Anda. Silakan hubungi tim HRD kami atau muat ulang halaman ini secara berkala.
                    </p>
                </div>
            @else
                <!-- Active Contract Review & Upload Form -->
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                    <!-- Left Column: Contract PDF Link & Details -->
                    <div class="lg:col-span-5 space-y-6">
                        <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
                            <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Ringkasan SPK</h3>
                            
                            <div class="space-y-4 text-sm">
                                <div>
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Posisi Ditawarkan</span>
                                    <p class="font-bold text-slate-800 mt-0.5">{{ $contract->position->name }}</p>
                                    <p class="text-xs text-slate-400">Divisi: {{ $contract->position->department->name }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Tipe Pekerjaan</span>
                                    <p class="font-bold text-slate-800 mt-0.5 capitalize">{{ $contract->employment_type }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Durasi Kontrak</span>
                                    <p class="font-bold text-slate-800 mt-0.5">
                                        {{ $contract->start_date->format('d M Y') }} s/d {{ $contract->end_date ? $contract->end_date->format('d M Y') : 'Permanen' }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Gaji Pokok Default</span>
                                    <p class="font-bold text-slate-800 mt-0.5">Rp {{ number_format($contract->salary, 0, ',', '.') }} / bulan</p>
                                </div>
                            </div>
                            
                            <div class="pt-4 border-t border-slate-100">
                                <a href="{{ asset('storage/' . $contract->contract_file_path) }}" target="_blank" 
                                   class="flex items-center justify-center gap-2 w-full rounded-2xl bg-slate-100 hover:bg-slate-200/80 px-4 py-3 text-sm font-semibold text-slate-700 transition">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Unduh Berkas Draf SPK (PDF)
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Upload Area -->
                    <div class="lg:col-span-7">
                        <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
                            <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Unggah Kontrak SPK Fisik</h3>
                            
                            <form wire:submit.prevent="uploadSignedContract" class="space-y-6">
                                <div class="space-y-2.5">
                                    <label class="block text-sm font-semibold text-slate-700">Langkah-langkah:</label>
                                    <ol class="list-decimal list-inside text-xs text-slate-500 space-y-1.5">
                                        <li>Unduh berkas draf SPK di sebelah kiri.</li>
                                        <li>Cetak (print) berkas PDF tersebut ke kertas.</li>
                                        <li>Tanda tangani berkas di atas materai/kolom tanda tangan.</li>
                                        <li>Scan berkas tersebut kembali (PDF/Gambar/JPG/PNG).</li>
                                        <li>Unggah berkas hasil scan pada form di bawah ini.</li>
                                    </ol>
                                </div>

                                <div>
                                    <label for="fileSignedContract" class="block text-sm font-semibold text-slate-700">Pilih Berkas Scan SPK <span class="text-rose-500">*</span></label>
                                    <input wire:model="fileSignedContract" id="fileSignedContract" type="file" accept=".pdf,.png,.jpg,.jpeg"
                                           class="mt-2 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-primary hover:file:bg-sky-100 transition">
                                    <p class="text-[10px] text-slate-400 mt-1.5">Format didukung: PDF, PNG, JPG, JPEG. Ukuran maksimum: 2MB.</p>
                                    @error('fileSignedContract') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div wire:loading wire:target="fileSignedContract" class="text-xs text-primary font-semibold">
                                    Sedang memproses berkas, mohon tunggu...
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4 border-t border-slate-100 flex justify-end">
                                    <button type="submit" wire:loading.attr="disabled"
                                            class="inline-flex items-center justify-center rounded-2xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-sky-500/30 transition active:scale-[0.98]">
                                        Kirim Kontrak Kerja Fisik
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
