<div>
    <div class="sm:mx-auto sm:w-full sm:max-w-3xl px-4">
        <!-- Brand / Header -->
        <div class="flex justify-center">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary text-white shadow-xl shadow-sky-500/25">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
        </div>
        <h2 class="mt-4 text-center text-3xl font-extrabold tracking-tight text-slate-900">
            Form Pendaftaran Calon Karyawan
        </h2>
        <p class="mt-2 text-center text-sm text-slate-500 max-w-lg mx-auto">
            Silakan lengkapi data profil, pilih posisi pekerjaan yang Anda minati, dan unggah dokumen administrasi di bawah ini untuk memulai proses rekrutmen.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-3xl px-4 mb-16">
        <!-- Tab Navigation -->
        <div class="flex justify-center mb-6">
            <div class="inline-flex rounded-full bg-slate-100 p-1 border border-slate-200/60 shadow-inner">
                <button type="button" wire:click="$set('activeTab', 'register')" 
                        class="px-5 py-2 text-xs font-bold rounded-full transition duration-150 {{ $activeTab === 'register' ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    Daftar Lamaran Baru
                </button>
                <button type="button" wire:click="$set('activeTab', 'check_status')" 
                        class="px-5 py-2 text-xs font-bold rounded-full transition duration-150 {{ $activeTab === 'check_status' ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    Cek Status Pendaftaran
                </button>
            </div>
        </div>

        @if($successMessage)
            <div class="bg-white py-12 px-6 shadow-xl shadow-slate-200/50 rounded-3xl border border-slate-100 text-center space-y-6">
                <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                    </svg>
                </div>
                <div class="space-y-2">
                    <h3 class="text-2xl font-bold text-slate-900">Pendaftaran Berhasil Dikirim!</h3>
                    <p class="text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                        Berkas lamaran dan biodata Anda telah tersimpan dengan aman di sistem kepegawaian. Tim HRD kami akan meninjau kualifikasi Anda dan menghubungi Anda melalui email atau WhatsApp.
                    </p>
                </div>
                <div class="pt-4">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl bg-primary px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-sky-500/25 hover:opacity-95 transition">
                        Kembali ke Halaman Login
                    </a>
                </div>
            </div>
        @elseif($activeTab === 'check_status')
            <!-- Check status form -->
            <div class="bg-white py-8 px-6 shadow-xl shadow-slate-200/50 rounded-3xl border border-slate-100 sm:px-10 max-w-md mx-auto">
                <div class="text-center space-y-2 mb-6">
                    <h3 class="text-base font-bold text-slate-900">Periksa Status Pendaftaran</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Masukkan Nomor NIK KTP dan alamat email Anda yang terdaftar pada lamaran sebelumnya.</p>
                </div>
                

                <form wire:submit.prevent="checkApplicationStatus" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor NIK KTP (16 Digit)</label>
                        <input wire:model="checkNik" type="text" maxlength="16" placeholder="Masukkan 16 digit NIK..." 
                               class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-xs font-semibold font-mono tracking-wider">
                        @error('checkNik') <span class="mt-1 text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email Terdaftar</label>
                        <input wire:model="checkEmail" type="email" placeholder="nama@email.com" 
                               class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-xs font-semibold">
                        @error('checkEmail') <span class="mt-1 text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" 
                            class="w-full inline-flex items-center justify-center rounded-xl bg-primary px-4 py-3 text-xs font-bold text-white shadow-md shadow-sky-500/25 hover:opacity-95 transition">
                        Periksa Status Lamaran
                    </button>
                </form>
            </div>
        @else
            <div class="bg-white py-8 px-6 shadow-xl shadow-slate-200/50 rounded-3xl border border-slate-100 sm:px-10">
                <form wire:submit.prevent="submitApplication" class="space-y-8">
                    
                    <!-- Section 1: Biodata Pelamar (Wajib) -->
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center justify-between">
                            <span>1. Biodata Utama</span>
                            <span class="text-[10px] text-rose-500 font-semibold">* Wajib diisi</span>
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span></label>
                                <input wire:model="name" id="name" type="text" placeholder="Contoh: Budi Santoso" 
                                       class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-xs font-semibold">
                                @error('name') <span class="mt-1 text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor NIK KTP (16 Digit) <span class="text-rose-500">*</span></label>
                                <input wire:model="nik" type="text" maxlength="16" placeholder="Masukkan 16 digit NIK KTP Anda..." 
                                       class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-xs font-semibold font-mono tracking-wider">
                                @error('nik') <span class="mt-1 text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Posisi yang Dilamar <span class="text-rose-500">*</span></label>
                                <select wire:model="position_id" 
                                        class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-700 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-xs font-semibold">
                                    <option value="">-- Pilih Posisi Pekerjaan --</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->department->name ?? '-' }})</option>
                                    @endforeach
                                </select>
                                @error('position_id') <span class="mt-1 text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email Aktif <span class="text-rose-500">*</span></label>
                                <input wire:model="email" id="email" type="email" placeholder="nama@email.com" 
                                       class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-xs font-semibold">
                                @error('email') <span class="mt-1 text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. Handphone / WhatsApp <span class="text-rose-500">*</span></label>
                                <input wire:model="phone" id="phone" type="text" placeholder="Contoh: 08123456789" 
                                       class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-xs font-semibold">
                                @error('phone') <span class="mt-1 text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Keterangan / Kompetensi Utama <span class="text-rose-500">*</span></label>
                                <input wire:model="keterangan" type="text" placeholder="Contoh: FO Splicer, Pengalaman Mikrotik 2 Tahun..." 
                                       class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-xs font-semibold">
                                @error('keterangan') <span class="mt-1 text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Dokumen Wajib (Wajib) -->
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center justify-between">
                            <span>2. Dokumen Persyaratan Wajib (Format Gambar/PDF, Max 2MB)</span>
                            <span class="text-[10px] text-rose-500 font-semibold">* Wajib diunggah</span>
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-4 rounded-2xl border border-slate-200/50">
                            <!-- KTP -->
                            <div class="space-y-1 relative">
                                <label class="block text-xs font-bold text-slate-700">Kartu Tanda Penduduk (KTP) <span class="text-rose-500">*</span></label>
                                <input type="file" wire:model="fileKtp" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                
                                <div wire:loading wire:target="fileKtp" class="text-xs text-sky-600 font-semibold flex items-center gap-1.5 mt-1">
                                    <svg class="animate-spin h-3.5 w-3.5 text-sky-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Mengunggah berkas KTP...</span>
                                </div>
                                
                                @if($isScanningKtp)
                                    <div class="text-xs text-emerald-600 font-semibold flex items-center gap-1.5 mt-1 animate-pulse">
                                        <svg class="animate-spin h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>Memindai data KTP (OCR)...</span>
                                    </div>
                                @endif
                                @error('fileKtp') <span class="text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                            </div>

                            <!-- KK -->
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700">Kartu Keluarga (KK) <span class="text-rose-500">*</span></label>
                                <input type="file" wire:model="fileKk" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                @error('fileKk') <span class="text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Ijazah -->
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700">Ijazah Pendidikan Terakhir <span class="text-rose-500">*</span></label>
                                <input type="file" wire:model="fileIjazah" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                @error('fileIjazah') <span class="text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                            </div>

                            <!-- SKCK -->
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700">SKCK Kepolisian (Aktif) <span class="text-rose-500">*</span></label>
                                <input type="file" wire:model="fileSkck" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                @error('fileSkck') <span class="text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Pas Foto -->
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700">Pas Foto Resmi Terbaru <span class="text-rose-500">*</span></label>
                                <input type="file" wire:model="filePasFoto" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                @error('filePasFoto') <span class="text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                            </div>

                            <!-- CV -->
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700">CV / Resume Profil <span class="text-rose-500">*</span></label>
                                <input type="file" wire:model="fileCv" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                @error('fileCv') <span class="text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Dokumen Tambahan (Opsional) -->
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">
                            3. Dokumen Pendukung Tambahan (Opsional)
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/50 p-4 rounded-2xl border border-slate-200/40">
                            <!-- SIM -->
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-600">Surat Izin Mengemudi (SIM A/C)</label>
                                <input type="file" wire:model="fileSim" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                @error('fileSim') <span class="text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Sertifikat Keahlian -->
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-600">Sertifikat Kompetensi / Pelatihan</label>
                                <input type="file" wire:model="fileSertifikat" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                @error('fileSertifikat') <span class="text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Multiple Supporting Documents -->
                            <div class="space-y-1 md:col-span-2">
                                <label class="block text-xs font-bold text-slate-600">Dokumen Pendukung Lainnya (Dapat memilih lebih dari 1 berkas)</label>
                                <input type="file" wire:model="filePendukung" multiple class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:shadow-sm file:border file:border-slate-200 hover:file:bg-slate-50 transition">
                                @error('filePendukung.*') <span class="text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6">
                        <button type="submit" wire:loading.attr="disabled"
                                class="flex w-full justify-center rounded-2xl bg-primary px-6 py-4 text-sm font-semibold text-white shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-sky-500/30 hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition active:scale-[0.98] disabled:opacity-75 disabled:cursor-not-allowed">
                            <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove>Kirim Lamaran Kerja</span>
                            <span wire:loading>Sedang mengunggah berkas...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
