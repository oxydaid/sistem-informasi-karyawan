<div wire:poll.5s="updateGatewayStatus">
    @php $title = 'App Settings'; @endphp

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="mt-2 text-sm text-slate-500">Kelola identitas aplikasi, skema warna tema, SEO dasar, dan akun media sosial perusahaan.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button type="submit" form="settings-form" wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center rounded-2xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-sky-500/30 transition active:scale-[0.98]">
                <svg wire:loading wire:target="saveSettings" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Simpan Pengaturan</span>
            </button>
        </div>
    </div>


    <form id="settings-form" wire:submit.prevent="saveSettings" class="mt-8 space-y-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
            <!-- Branding & SEO Form Card -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Branding Card -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Branding & SEO</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="appName" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Aplikasi</label>
                            <input wire:model="appName" id="appName" type="text" placeholder="Misal: ISP HRIS" 
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            @error('appName') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="appDescription" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Deskripsi Aplikasi</label>
                            <textarea wire:model="appDescription" id="appDescription" rows="4" placeholder="Deskripsi singkat aplikasi HRIS..." 
                                      class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm"></textarea>
                            @error('appDescription') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Konfigurasi Perusahaan & Payroll Card -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Konfigurasi Perusahaan & Penggajian</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="companyName" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Resmi Perusahaan (SPK & Slip Gaji)</label>
                            <input wire:model="companyName" id="companyName" type="text" placeholder="Misal: PT SKYNET INDONESIA" 
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm font-semibold">
                            @error('companyName') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="leaveDeductionAmount" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Nominal Potongan Cuti per Hari (Rp)</label>
                            <input wire:model="leaveDeductionAmount" id="leaveDeductionAmount" type="number" min="0" placeholder="Misal: 50000" 
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm font-semibold">
                            @error('leaveDeductionAmount') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Social Media Card -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Media Sosial & Kontak</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="facebookUrl" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Facebook URL</label>
                            <input wire:model="facebookUrl" id="facebookUrl" type="url" placeholder="https://facebook.com/username" 
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            @error('facebookUrl') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="instagramUrl" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Instagram URL</label>
                            <input wire:model="instagramUrl" id="instagramUrl" type="url" placeholder="https://instagram.com/username" 
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            @error('instagramUrl') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="linkedinUrl" class="block text-xs font-bold uppercase tracking-wider text-slate-500">LinkedIn URL</label>
                            <input wire:model="linkedinUrl" id="linkedinUrl" type="url" placeholder="https://linkedin.com/company/username" 
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            @error('linkedinUrl') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="whatsappUrl" class="block text-xs font-bold uppercase tracking-wider text-slate-500">No. WhatsApp / WhatsApp Link</label>
                            <input wire:model="whatsappUrl" id="whatsappUrl" type="url" placeholder="https://wa.me/628123456789" 
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                        </div>
                    </div>
                </div>

                <!-- Integration Settings Card -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Kunci API & Integrasi</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="ocrSpaceApiKey" class="block text-xs font-bold uppercase tracking-wider text-slate-500">OCR Space API Key</label>
                            <input wire:model="ocrSpaceApiKey" id="ocrSpaceApiKey" type="text" placeholder="Masukkan OCR Space API Key..." 
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            @error('ocrSpaceApiKey') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="whatsappGatewaySecret" class="block text-xs font-bold uppercase tracking-wider text-slate-500">WhatsApp Gateway Secret Key</label>
                            <input wire:model="whatsappGatewaySecret" id="whatsappGatewaySecret" type="text" placeholder="Kunci rahasia pengaman gateway..." 
                                   class="mt-1.5 block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-950 placeholder-slate-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            @error('whatsappGatewaySecret') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo, Colors Theme Side Card -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Theme Colors Card -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Warna Tema (Theme Colors)</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="primaryColor" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Warna Utama (Primary)</label>
                            <div class="mt-1.5 flex gap-2 items-center">
                                <input wire:model="primaryColor" id="primaryColor" type="color" 
                                       class="h-10 w-10 border border-slate-200 rounded-xl cursor-pointer">
                                <input wire:model.live="primaryColor" type="text" placeholder="#0ea5e9" 
                                       class="flex-1 rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 text-slate-950 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            </div>
                            @error('primaryColor') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="secondaryColor" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Warna Kedua (Secondary)</label>
                            <div class="mt-1.5 flex gap-2 items-center">
                                <input wire:model="secondaryColor" id="secondaryColor" type="color" 
                                       class="h-10 w-10 border border-slate-200 rounded-xl cursor-pointer">
                                <input wire:model.live="secondaryColor" type="text" placeholder="#334155" 
                                       class="flex-1 rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 text-slate-950 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                            </div>
                            @error('secondaryColor') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Logo & Favicon Card -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Logo & Favicon</h3>
                    
                    <div class="space-y-4">
                        <!-- Logo Upload -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Logo Perusahaan</label>
                            <input type="file" wire:model="fileLogo" class="mt-1.5 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                            @error('fileLogo') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            
                            @if ($fileLogo)
                                <div class="mt-3 p-2 bg-slate-50 rounded-xl border border-slate-100 flex justify-center">
                                    <img src="{{ $fileLogo->temporaryUrl() }}" class="h-12 w-auto object-contain">
                                </div>
                            @elseif ($appSettings->logo_path)
                                <div class="mt-3 p-2 bg-slate-50 rounded-xl border border-slate-100 flex justify-center">
                                    <img src="{{ asset('storage/' . $appSettings->logo_path) }}" class="h-12 w-auto object-contain">
                                </div>
                            @endif
                        </div>

                        <!-- Favicon Upload -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Favicon</label>
                            <input type="file" wire:model="fileFavicon" class="mt-1.5 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                            @error('fileFavicon') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            
                            @if ($fileFavicon)
                                <div class="mt-3 p-2 bg-slate-50 rounded-xl border border-slate-100 flex justify-center">
                                    <img src="{{ $fileFavicon->temporaryUrl() }}" class="h-8 w-8 object-contain">
                                </div>
                            @elseif ($appSettings->favicon_path)
                                <div class="mt-3 p-2 bg-slate-50 rounded-xl border border-slate-100 flex justify-center">
                                    <img src="{{ asset('storage/' . $appSettings->favicon_path) }}" class="h-8 w-8 object-contain">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Live WhatsApp Gateway Card -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-slate-900 border-b border-dashed border-slate-200 pb-3">Status WhatsApp Gateway</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status</span>
                            @if($gatewayStatus === 'connected')
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Terhubung
                                </span>
                            @elseif($gatewayStatus === 'connecting')
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700 border border-amber-100">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menghubungkan
                                </span>
                            @elseif($gatewayStatus === 'qrcode')
                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700 border border-blue-100 animate-pulse">
                                    Pindai QR Code
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2 py-1 text-xs font-semibold text-rose-700 border border-rose-100">
                                    Terputus
                                </span>
                            @endif
                        </div>

                        @if($gatewayStatus === 'connected')
                            <div class="p-4 bg-emerald-50/50 rounded-2xl border border-emerald-100/60 space-y-3 text-left">
                                <div>
                                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">ID Pengguna</span>
                                    <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $gatewayUser['id'] ?? '-' }}</p>
                                </div>
                                @if(isset($gatewayUser['name']))
                                <div>
                                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Nama Akun</span>
                                    <p class="text-xs font-semibold text-slate-600 mt-0.5">{{ $gatewayUser['name'] }}</p>
                                </div>
                                @endif
                                
                                <button type="button" wire:click="disconnectWhatsapp" wire:loading.attr="disabled"
                                        class="w-full mt-2 inline-flex items-center justify-center rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100/80 transition active:scale-[0.98]">
                                    <span>Putuskan Sesi / Logout</span>
                                </button>
                            </div>
                        @elseif($gatewayStatus === 'qrcode' && $gatewayQr)
                            <div class="flex flex-col items-center justify-center p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                                <p class="text-[11px] font-semibold text-slate-500 mb-3 leading-relaxed">Pindai QR Code di bawah menggunakan aplikasi WhatsApp Anda:</p>
                                <img src="{{ $gatewayQr }}" class="w-36 h-36 border border-slate-200 bg-white rounded-xl p-1.5 shadow-sm" />
                                <a href="{{ $gatewayUrl }}/qr" target="_blank" class="mt-3 text-[10px] font-bold text-primary hover:underline">Buka halaman QR penuh</a>
                            </div>
                        @else
                            <div class="p-4 bg-rose-50/50 rounded-2xl border border-rose-100/60 text-center">
                                <p class="text-xs font-semibold text-rose-700 leading-relaxed">
                                    {{ $gatewayMessage ?: 'Server WhatsApp Gateway terputus atau belum aktif.' }}
                                </p>
                                <p class="text-[10px] text-slate-400 mt-2">Pastikan server Node.js di folder <code class="bg-slate-100 px-1 py-0.5 rounded">whatsapp/</code> sedang berjalan.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
