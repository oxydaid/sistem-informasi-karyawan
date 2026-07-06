# Sistem Informasi Manajemen Karyawan (Skynet)

Sistem Informasi Manajemen Karyawan (Skynet) adalah platform internal berbasis web yang dirancang untuk mengelola seluruh siklus kerja staf (Employee Lifecycle), mulai dari proses rekrutmen/pendaftaran, penandatanganan kontrak kerja (onboarding), evaluasi kinerja (KPI), manajemen cuti, hingga kalkulasi payroll bulanan dengan integrasi WhatsApp Gateway dan OCR (Optical Character Recognition) untuk verifikasi KTP.

---

## 📋 Persyaratan Sistem (System Requirements)
Untuk menjalankan aplikasi ini dengan lancar, pastikan server Anda memenuhi spesifikasi berikut:
* **PHP:** `^8.4` (dengan ekstensi wajib: `pdo_mysql`, `mbstring`, `openssl`, `xml`, `zip`, `gd`, `curl`)
* **Node.js:** `^22.0` atau lebih tinggi (digunakan untuk build aset frontend dan menjalankan micro-service WhatsApp Gateway)
* **Database:** MySQL `^8.0` atau MariaDB `^10.4` atau PostgreSQL
* **Composer:** `^2.2` (untuk manajemen dependensi backend Laravel)
* **API Key OCR:** Akun gratis atau berbayar dari [ocr.space](https://ocr.space/ocrapi) untuk memindai berkas KTP secara otomatis.
* **WhatsApp:** Akun WhatsApp aktif di ponsel pintar untuk dipasangkan (*link device*) menggunakan pemindaian QR Code pada panel kontrol admin.

---

## 🧱 Stack Teknologi & Kustomisasi
* **Framework Backend:** Laravel (versi stabil terbaru)
* **Frontend State & Interactivity:** Livewire v4 & Alpine.js
* **CSS Framework:** Tailwind CSS v4 (styling langsung di `app.css` tanpa config)
* **Database:** MariaDB / MySQL / PostgreSQL
* **PDF Generator:** Barryvdh DomPDF
* **WhatsApp API client:** Node.js Express + `@whiskeysockets/baileys` (terletak di subdirektori `/whatsapp`)

---

## ⚙️ Detail Fitur Utama Sistem

### 1. Sistem Pengenalan Karakter KTP (OCR Space API Integration)
Fitur ini mempermudah pelamar saat mendaftar dengan memindai berkas KTP secara otomatis untuk meminimalkan input manual dan kesalahan ketik.
* **Mesin OCR:** Menggunakan **OCR.space API Engine 2** karena memiliki akurasi tinggi dan performa cepat untuk membaca struktur data pada kartu identitas/tabel dalam tulisan latin/Indonesia.
* **Mekanisme Kerja:** 
  1. Pelamar mengunggah foto/scan KTP pada form pendaftaran.
  2. Trigger Livewire `updatedFileKtp()` mendeteksi unggahan berkas, memvalidasi ukuran file (max 2MB), dan secara asinkron mengirimkan berkas multipart ke server API OCR.space.
  3. Respons teks mentah (*raw parsed text*) diproses menggunakan algoritma pencocokan pola regex canggih untuk mengekstrak data identitas terstruktur:
     - **NIK:** Pemindaian 16 digit angka berurutan secara konsisten.
     - **Nama:** Deteksi teks baris pertama setelah label "Nama".
     - **Tempat/Tgl Lahir:** Pemisahan teks berbasis tanda koma `,` dan parsing format tanggal `DD-MM-YYYY` menjadi standar database `YYYY-MM-DD`.
     - **Alamat Lengkap:** Menggabungkan baris alamat utama dengan pencarian regex untuk nomor RT/RW, Kelurahan/Desa, dan Kecamatan.
     - **Status Pernikahan, Agama, Jenis Kelamin, Kewarganegaraan, & Pekerjaan:** Pencocokan kata kunci case-insensitive (misal: "Belum Kawin", "Kawin", "Laki-laki", "WNI").
  4. Data NIK dan Nama yang berhasil dipindai langsung diisi otomatis (*auto-fill*) ke dalam input form pendaftaran secara real-time.

---

### 2. WhatsApp Gateway Service (Express & Baileys Baileys)
Sistem memiliki server mikro WhatsApp Gateway asinkron yang berjalan secara lokal untuk menjamin efisiensi pengiriman notifikasi slip gaji bulanan kepada karyawan.
* **Arsitektur:** Dibangun menggunakan Node.js Express dan library `@whiskeysockets/baileys` untuk mengkoneksikan sesi WhatsApp Web lokal menggunakan otentikasi multi-file-auth.
* **Otentikasi Kunci Rahasia:** Untuk mencegah penyalahgunaan endpoint gateway, middleware server Node.js membaca kolom `whatsapp_gateway_secret` secara dinamis langsung dari database MariaDB `app_settings` untuk memvalidasi header token `X-Gateway-Secret`.
* **Fitur Utama:**
  * **Pairing QR Code:** Halaman administrasi admin menyediakan QR Code real-time yang memuat gambar pairing code terupdate (auto-refresh via pooling request status).
  * **Pengiriman Media PDF Slip Gaji:** Menggunakan modul pendeteksi tipe mime otomatis untuk mendistribusikan berkas slip gaji resmi PDF (dengan URL berkas storage Laravel publik) yang langsung terlampir di WhatsApp karyawan.
  * **Perlindungan Terhadap Ban (Anti-Ban Blast Engine):** Untuk mencegah pemblokiran nomor WhatsApp pengirim akibat pengiriman pesan massal (*blast*) secara bersamaan, loop pemrosesan notifikasi menerapkan **asynchronous delay/sleep selama 5 detik** per nomor telepon.

---

### 3. Logika Penghitungan Gaji & Payroll Engine
Payroll Engine dirancang untuk menghitung pendapatan bersih bulanan staf secara akurat dengan melibatkan seluruh variabel kehadiran, performa, dan potongan pinjaman.
* **Rumus Perhitungan Take Home Pay (Net Salary):**
  $$\text{Net Salary} = (\text{Gaji Pokok} + \text{Bonus KPI}) - \text{Potongan KPI} - \text{Potongan Cuti Unpaid} - \text{Potongan Kasbon}$$
* **Detail Variabel:**
  * **Gaji Pokok:** Ditarik dari kolom profil individu karyawan. Jika kosong, sistem otomatis menggunakan default gaji pokok dari master jabatan/posisi kerja karyawan tersebut.
  * **Bonus & Potongan KPI:** Nominal penyesuaian dinamis yang ditarik dari modul **Evaluasi Kinerja KPI** staf bersangkutan untuk bulan-tahun berjalan.
  * **Potongan Cuti Unpaid:** Denda finansial bagi karyawan yang mengambil cuti melebihi sisa jatah kuota tahunan mereka. Jumlah hari kelebihan cuti (*unpaid_days*) dikalikan dengan parameter `leave_deduction_amount` (Rp) yang diatur di global App Settings.
  * **Potongan Kasbon (Cash Advance):** Kasbon disetujui (*status: approved*) dengan tanggal pinjaman sebelum akhir bulan berjalan akan ditarik ke dalam perhitungan payroll. Untuk mencegah double-deduction, kasbon ditautkan langsung ke `payroll_id`. Saat payroll bulan tersebut disetujui (*approved*), seluruh kasbon yang terkait otomatis beralih status menjadi `settled` dan tidak akan terpotong lagi di periode berikutnya.
* **Optimasi Kinerja (1.000+ Karyawan):**
  Pemrosesan bulk payroll bulanan dioptimasi agar ramah memori server:
  - Menggunakan query bulk preloading `with(['position'])` untuk menghindari masalah query N+1.
  - Membaca semua KPI dan data cuti bulanan sekali jalan, kemudian melakukan pengindeksan data di memori menggunakan PHP associative array (`keyBy('employee_id')`).
  - Menerapkan pemrosesan bertahap (*chunk processing*) sebanyak 100 data karyawan per siklus (`Employee::chunk(100)`) agar konsumsi RAM tetap konstan dan rendah.

---

## 👥 Peran Pengguna (Role-Based Access Control)
Sistem menggunakan modul otorisasi berbasis peran (Role) yang terbagi menjadi:
1. **Super Admin / Direktur:** Akses penuh ke seluruh menu administrasi, manajemen pengguna, konfigurasi global, serta approval cuti/payroll.
2. **HRD:** Mengelola modul rekrutmen pelamar, pembuatan draf SPK kontrak kerja, verifikasi dokumen administratif, dan data profil karyawan.
3. **Manager / Kepala Divisi:** Mengisi skor kinerja KPI bulanan staf bawahan per divisi dan menyetujui pengajuan cuti tingkat pertama.
4. **Finance (Keuangan):** Memvalidasi perhitungan payroll bulanan, memproses kasbon karyawan, dan mendistribusikan slip gaji digital.
5. **Karyawan:** Mengakses dasbor pribadi untuk melihat KPI, mengajukan cuti, meminta kasbon, meninjau kontrak kerja, dan mengunduh slip gaji.
6. **Pelamar (Guest / Calon Karyawan):** Mengakses formulir pendaftaran lamaran kerja serta halaman onboarding untuk mengunggah berkas kontrak fisik yang ditandatangani.

---

## 🔑 Akun Uji Coba Default (Seeder)
* **Super Admin:** `admin@skynet.com` / Password: `admin123`
* **HRD Staff:** `hrd@skynet.com` / Password: `hrd123`
* **Finance Staff:** `finance@skynet.com` / Password: `finance123`
* **NOC Manager:** `manager@skynet.com` / Password: `manager123`
* **Karyawan (NOC Staff User):** `employee@skynet.com` / Password: `employee123`
* **NIK Pelamar Lolos Seleksi (Siti Aminah):** `3273012345678902` (Untuk Onboarding E-Sign)

---

## 📂 Daftar Halaman Aplikasi

Berikut adalah seluruh daftar halaman aplikasi SI Karyawan yang telah diterjemahkan ke rute Bahasa Indonesia:

| No | Nama Halaman | URL Rute (Bahasa Indonesia) | Peran Akses | Deskripsi & Alur Prosedur Kerja |
|:---|:---|:---|:---|:---|
| 1 | **Form Masuk (Login)** | `/masuk` | Tamu (Guest) | Halaman otentikasi pengguna. Mengarahkan pengguna secara otomatis ke dasbor admin atau karyawan berdasarkan role masing-masing. |
| 2 | **Pendaftaran Lowongan (Apply)** | `/daftar` | Tamu (Guest) | Halaman pendaftaran pelamar umum. Terintegrasi dengan **OCR Space API**; ketika berkas KTP diunggah, sistem memindai secara otomatis untuk mengisi kolom NIK dan Nama Pelamar. |
| 3 | **Penerimaan & Onboarding** | `/penerimaan/{token}` | Tamu (Pelamar) | Tempat pelamar yang lolos seleksi meninjau draf SPK kontrak kerja (PDF) dan mengunggah berkas kontrak fisik bertanda tangan. Persetujuan berkas ini otomatis mengubah status menjadi Karyawan dan men-generate akun pengguna baru. |
| 4 | **Ringkasan Dasbor Admin** | `/admin/ringkasan` | Admin / HRD / Keuangan | Menampilkan metrik visual (total staf, pengajuan cuti tertunda, status payroll, dll) beserta feed aktivitas terbaru. |
| 5 | **Pengaturan Global** | `/admin/pengaturan` | Super Admin | Mengatur branding instansi (logo, favicon, nama, deskripsi), warna primer/sekunder tema dinamis, API Key OCR, monitor status, dan QR Code WhatsApp Gateway. |
| 6 | **Manajemen Pengguna** | `/admin/pengguna` | Super Admin | Modul CRUD manajemen akun user dan penugasan role kepegawaian. |
| 7 | **Review Berkas Pelamar** | `/admin/pelamar` | Admin / HRD | Memeriksa berkas pendaftaran pelamar secara real-time melalui modal 3-kolom (sisi kiri edit biodata, kolom tengah pratinjau berkas instan, sisi kanan checklist administrasi). |
| 8 | **Pengisian Kontrak Kerja** | `/admin/pelamar/{id}/kontrak` | Admin / HRD | Menentukan jabatan, status kerja, masa kontrak, nominal gaji pokok, dan men-generate draf SPK dalam format PDF untuk diulas pelamar. |
| 9 | **Daftar Kontrak Kerja** | `/admin/kontrak` | Admin / HRD | Mengelola riwayat status tanda tangan kontrak (SPK) aktif. Menyediakan tombol **"Download Berkas ZIP"** untuk mengunduh semua berkas NIK terkait dalam satu file kompresi `.zip`. |
| 10 | **Daftar Karyawan** | `/admin/karyawan` | Admin / HRD | CRUD data kepegawaian. Jika NIK internal dikosongkan, sistem secara otomatis men-generate NIK baru berformat `EMP-[DEPARTEMEN]-[RANDOM]`. |
| 11 | **Departemen & Jabatan** | `/admin/departemen` | Admin / HRD | CRUD departemen/divisi kerja dan posisi jabatan lengkap dengan konfigurasi gaji pokok default. |
| 12 | **Persetujuan Cuti** | `/admin/cuti` | Admin / HRD / Manager | Persetujuan pengajuan cuti staf. Manager menyetujui di tingkat divisi, HRD memfinalisasi di tingkat pusat. Sistem otomatis menghitung potongan hari tidak dibayar (*unpaid days*) jika melampaui sisa kuota. |
| 13 | **Evaluasi Kinerja (KPI)** | `/admin/kpi` | Admin / Manager | Penilaian bulanan staf bawahan secara individual (skor 1-100, penyesuaian nominal bonus, potongan performa, serta catatan evaluasi). |
| 14 | **Proses Penggajian (Payroll)** | `/admin/penggajian` | Admin / Keuangan | Kalkulasi gaji bulanan secara bulk (massal) untuk efisiensi performa 1000+ data karyawan, pratinjau PDF, dan blast slip gaji via WhatsApp secara asinkron dengan jeda waktu aman 5 detik. |
| 15 | **Manajemen Kasbon Staf** | `/admin/kasbon` | Admin / Keuangan | Persetujuan/penolakan pinjaman uang muka karyawan. Jika disetujui, nominal kasbon akan langsung dikurangkan secara otomatis pada slip gaji bulan berjalan. |
| 16 | **Dasbor Karyawan** | `/karyawan/dasbor` | Karyawan | Panel ringkasan profil staf, sisa kuota cuti tahunan, status KPI bulanan, dan log pengajuan kasbon terbaru. |
| 17 | **Kontrak Kerja Karyawan** | `/karyawan/kontrak` | Karyawan | Karyawan dapat melihat status kontrak aktif, sisa durasi masa kerja, dan mengunduh draf SPK digital resmi mereka. |
| 18 | **Pengajuan Cuti Karyawan** | `/karyawan/cuti` | Karyawan | Formulir bagi karyawan untuk mengirim pengajuan cuti kerja baru beserta tanggal mulai, tanggal selesai, alasan, dan berkas bukti fisik. |
| 19 | **Slip Gaji Karyawan** | `/karyawan/penggajian` | Karyawan | Histori slip gaji digital karyawan yang telah berstatus PAID lengkap dengan tombol unduh slip PDF resmi. |
| 20 | **Pengajuan Kasbon Karyawan** | `/karyawan/kasbon` | Karyawan | Staf mengajukan pinjaman/kasbon darurat beserta jumlah dana dan alasan tertulis. |

---

## 🚀 Panduan Instalasi (Deployment & Installation Guide)

Pilih salah satu metode deployment di bawah ini yang sesuai dengan infrastruktur server Anda:

### 🌐 Metode A: Native Nginx (Server Ubuntu/Debian)
Langkah deployment langsung pada VPS dengan OS Ubuntu:
1. **Instal Nginx, PHP 8.4, dan MariaDB:**
   ```bash
   sudo apt update
   sudo apt install nginx mariadb-server php8.4-fpm php8.4-mysql php8.4-xml php8.4-curl php8.4-gd php8.4-zip php8.4-mbstring php8.4-intl php8.4-bcmath -y
   ```
2. **Klon Project & Kelola Permission Folder:**
   Letakkan project di direktori `/var/www/si-karyawan` dan atur hak akses web server:
   ```bash
   sudo chown -R www-data:www-data /var/www/si-karyawan
   sudo chmod -R 775 /var/www/si-karyawan/storage /var/www/si-karyawan/bootstrap/cache
   ```
3. **Konfigurasi Nginx Server Block:**
   Buat berkas konfigurasi baru `/etc/nginx/sites-available/si-karyawan`:
   ```nginx
   server {
       listen 80;
       server_name hris.company.com; # Ubah domain Anda
       root /var/www/si-karyawan/public;

       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";

       index index.php;
       charset utf-8;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location = /favicon.ico { access_log off; log_not_found off; }
       location = /robots.txt  { access_log off; log_not_found off; }

       error_page 404 /index.php;

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```
   Aktifkan tautan konfigurasi lalu muat ulang Nginx:
   ```bash
   sudo ln -s /etc/nginx/sites-available/si-karyawan /etc/nginx/sites-enabled/
   sudo systemctl reload nginx
   ```
4. **Instalasi Node.js & Jalankan WhatsApp Daemon (PM2):**
   ```bash
   # Pasang Node.js LTS
   curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
   sudo apt-get install -y nodejs
   
   # Jalankan PM2 untuk menjaga service WA tetap hidup
   sudo npm install pm2 -g
   cd /var/www/si-karyawan/whatsapp
   npm install
   pm2 start index.js --name "wa-gateway"
   pm2 save
   ```

---

### 🐳 Metode B: Docker Compose (Direkomendasikan)
Metode containerized untuk kemudahan duplikasi lingkungan kerja tanpa perlu instalasi PHP/Node lokal:
1. **Pastikan Docker & Docker Compose Terpasang.**
2. **Jalankan Container Bulanan:**
   Jalankan perintah ini di root direktori project:
   ```bash
   docker compose up -d --build
   ```
3. **Inisialisasi Project inside Container:**
   ```bash
   docker compose exec app composer install
   docker compose exec app php artisan migrate:fresh --seed
   docker compose exec app php artisan storage:link
   ```
4. **Selesai.** Aplikasi dapat diakses melalui browser di alamat `http://localhost`. WhatsApp gateway otomatis aktif di `http://localhost:4000`.

---

### 🎛️ Metode C: cPanel Shared Hosting (Manual Upload)
Jika Anda menggunakan shared hosting konvensional tanpa akses root terminal:
1. **Unggah dan Pisahkan Direktori:**
   - Ekstrak seluruh file proyek Anda.
   - Pindahkan folder `/public` dari proyek ke dalam direktori `public_html`.
   - Untuk alasan keamanan agar kode backend PHP tidak dapat diakses langsung oleh publik, letakkan seluruh folder sisa (seperti `/app`, `/bootstrap`, `/config`, dll) di direktori utama hosting Anda (luar `public_html`, contoh: `/home/username/si-karyawan`).
2. **Sesuaikan Path Bootstrapping:**
   Buka file `public_html/index.php` dan sesuaikan path require agar mengarah ke direktori baru di luar folder publik:
   ```php
   require __DIR__.'/../si-karyawan/vendor/autoload.php';
   $app = require_once __DIR__.'/../si-karyawan/bootstrap/app.php';
   ```
3. **Atur Izin Menulis Berkas (File Permission):**
   Ubah permission folder `/home/username/si-karyawan/storage` dan `/home/username/si-karyawan/bootstrap/cache` ke **775** atau **755** (tergantung spesifikasi hosting) agar server web dapat menulis berkas unggahan dan cache.
4. **Jalankan WhatsApp Gateway (Node.js App):**
   - Di dasbor cPanel, cari menu **"Setup Node.js App"**.
   - Klik **Create Application**.
   - Set **Application Root** ke `/home/username/si-karyawan/whatsapp` dan **Startup File** ke `index.js`.
   - Jalankan perintah NPM Install melalui interface cPanel untuk memasang seluruh dependensi gateway.
5. **Konfigurasi Cron Job Scheduler Laravel:**
   Masuk ke menu **Cron Jobs** di cPanel dan buat cron baru setiap 1 menit sekali untuk memicu antrian pengiriman PDF dan webhook:
   ```bash
   * * * * * /usr/local/bin/php /home/username/si-karyawan/artisan schedule:run >> /dev/null 2>&1
   ```

---

## 🧪 Panduan Menjalankan Pengujian (Testing)
Aplikasi ini dilengkapi pengujian terstruktur menggunakan framework **Pest PHP** untuk menjamin fungsionalitas logika bisnis inti:
```bash
# Menjalankan seluruh test suite
vendor/bin/pest
```
Pengujian mencakup:
1. `PayrollServiceTest`: Kalkulasi gaji bulanan tunggal dan bulk.
2. `LeaveRequestQuotaTest`: Pengurangan kuota cuti, deteksi cuti *unpaid*, dan restorasi kuota jika dibatalkan.
3. `CashAdvanceSettlementTest`: Keterikatan kasbon dengan payroll untuk mencegah pemotongan ganda.
4. `WhatsappGatewayServiceTest`: Pemformatan nomor JID WhatsApp dan mock HTTP request.
5. `ContractServiceTest`: Pembuatan dokumen SPK (PDF) otomatis.
6. `OcrServiceTest`: Parsing identitas KTP via OCR Space API.
7. `UserManagementTest`: Otorisasi hak akses dashboard.
