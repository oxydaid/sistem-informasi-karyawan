# Panduan Deployment cPanel Shared Hosting

Dokumen ini menjelaskan langkah-langkah untuk mendeploy aplikasi Sistem Informasi Karyawan (Laravel 11+) beserta WhatsApp Gateway (Node.js) di cPanel Shared Hosting.

---

## 📋 Prasyarat
1. **Versi PHP**: Minimal PHP 8.4 (Dapat diatur melalui **"Select PHP Version"** atau **"MultiPHP Manager"** di cPanel. Versi ini dibutuhkan karena dependensi pada composer.lock memerlukan PHP >= 8.4.1).
2. **Extensions PHP**: Pastikan extension berikut aktif: `pdo_mysql`, `gd`, `zip`, `bcmath`, `intl`, `mbstring`, `xml`, `fileinfo`.
3. **Fitur Node.js**: Layanan hosting Anda harus mendukung Node.js (cari menu **"Setup Node.js App"** di cPanel).

---

## 🛠️ Langkah 1: Persiapan Database & Berkas

### 1. Buat Database MySQL/MariaDB
1. Masuk ke cPanel dan buka menu **MySQL® Database Wizard**.
2. Buat database baru (contoh: `username_karyawan`).
3. Buat user database baru (contoh: `username_user`) dan buat password yang kuat.
4. Hubungkan user ke database dengan mencentang pilihan **"ALL PRIVILEGES"**.
5. Catat nama database, user, dan password untuk konfigurasi `.env`.

### 2. Upload Berkas ke cPanel
Ada dua metode yang bisa digunakan untuk mengunggah berkas:

#### Metode A: Pemisahan Direktori (Direkomendasikan & Paling Aman)
Untuk mencegah source code backend PHP diakses langsung dari web browser, pisahkan folder proyek:
1. Buat folder baru di luar folder web root (`public_html`), misalnya `/home/username/si-karyawan`.
2. Ekstrak seluruh file proyek Anda (kecuali isi folder `/public`) ke dalam folder `/home/username/si-karyawan` tersebut.
3. Upload seluruh isi yang ada di dalam folder `/public` proyek ke dalam direktori `public_html` (atau subdomain Anda).
4. Ubah file `public_html/index.php` untuk mengarahkan bootstrap ke folder luar:
   ```php
   // Baris 9 - Periksa file maintenance
   if (file_exists($maintenance = __DIR__.'/../si-karyawan/storage/framework/maintenance.php')) {
       require $maintenance;
   }

   // Baris 14 - Composer autoloader
   require __DIR__.'/../si-karyawan/vendor/autoload.php';

   // Baris 18 - Bootstrap Laravel
   $app = require_once __DIR__.'/../si-karyawan/bootstrap/app.php';
   ```

#### Metode B: Satu Folder Menggunakan `.htaccess` (Praktis)
Jika Anda ingin meletakkan seluruh folder proyek di satu tempat (misalnya langsung di dalam `public_html` atau subdomain):
1. Ekstrak seluruh file proyek Anda langsung ke dalam `public_html`.
2. Pastikan file `.htaccess` bawaan di root proyek terunggah. File `.htaccess` ini akan secara otomatis mengarahkan traffic ke subfolder `/public` dan memblokir akses publik ke folder sensitif seperti `.env`, `/vendor`, dll.

---

## ⚙️ Langkah 2: Konfigurasi Berkas `.env`

1. Cari file `.env` di folder utama proyek (misal `/home/username/si-karyawan/.env`). Jika belum ada, salin file `.env.example` dan ubah namanya menjadi `.env`.
2. Sesuaikan konfigurasi koneksi database Anda:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domain-anda.com

   DB_CONNECTION=mariadb
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=username_karyawan
   DB_USERNAME=username_user
   DB_PASSWORD=PasswordDatabaseAnda
   ```
3. Generate Application Key. Jika Anda memiliki akses SSH terminal, jalankan:
   ```bash
   php artisan key:generate
   ```
   Jika tidak memiliki akses SSH, Anda bisa membuat route sementara di `routes/web.php` atau menjalankan script PHP berikut di hosting untuk meng-generate key:
   ```php
   // Letakkan sementara di routes/web.php lalu akses via browser
   Route::get('/init-app', function() {
       Artisan::call('key:generate');
       Artisan::call('migrate --force');
       Artisan::call('storage:link');
       return "Inisialisasi Berhasil!";
   });
   ```

---

## 🔒 Langkah 3: Perizinan Folder (Folder Permissions)
Agar Laravel dapat berjalan dengan normal dan menulis log serta file upload, sesuaikan permission folder berikut menjadi **775** atau **755** (tergantung konfigurasi keamanan server hosting Anda):
- `/storage` (dan seluruh subfolder di dalamnya)
- `/bootstrap/cache`

---

## 🕒 Langkah 4: Konfigurasi Cron Job Laravel Scheduler
cPanel memerlukan Cron Job manual untuk menjalankan scheduler Laravel (mengirim email, merapikan antrian, dll):
1. Cari menu **Cron Jobs** di dasbor cPanel.
2. Pada bagian **Common Settings**, pilih **Once Per Minute (* * * * *)**.
3. Di kolom **Command**, masukkan perintah berikut (pastikan path PHP dan artisan sesuai):
   ```bash
   /usr/local/bin/php /home/username/si-karyawan/artisan schedule:run >> /dev/null 2>&1
   ```
   *Catatan: Lokasi path `/usr/local/bin/php` dapat berbeda tergantung server. Anda bisa melihat path PHP yang benar di cPanel.*

---

## 💬 Langkah 5: Setup Node.js App untuk WhatsApp Gateway
Untuk menjalankan WhatsApp Gateway (Baileys + Express) secara persisten di shared hosting:
1. Cari menu **"Setup Node.js App"** di cPanel.
2. Klik **Create Application**.
3. Isi konfigurasi sebagai berikut:
   - **Node.js version**: Pilih versi terbaru yang didukung (misal versi `22.x`).
   - **Application Mode**: `Production`.
   - **Application Root**: Arahkan ke folder whatsapp, contoh: `si-karyawan/whatsapp`.
   - **Application URL**: Subdomain atau subpath yang ingin digunakan (contoh: `https://domain-anda.com/wa-gateway` atau biarkan default jika diakses via internal port).
   - **Application startup file**: Isi dengan `index.js`.
4. Klik **Create**.
5. Setelah aplikasi dibuat, cPanel akan mendeteksi file `package.json`. Klik tombol **"Run NPM Install"** untuk mengunduh semua package Node.js.
6. Tambahkan environment variables di bagian bawah menu Node.js tersebut jika diperlukan (misal `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` jika Node.js tidak bisa mengakses `.env` di luar foldernya).
7. Klik **Start Application**.
8. Buka tab browser baru dan akses URL yang didaftarkan untuk memastikan WhatsApp Gateway berjalan (contoh: `https://domain-anda.com/wa-gateway/status`).
9. Sesuaikan nilai `WA_GATEWAY_URL` di file `.env` Laravel dengan URL Node.js ini agar sistem Laravel dapat mengirim request kirim pesan WhatsApp.

---

## 🔍 Troubleshooting cPanel
* **Error 500 / Permission Denied**: Pastikan permission file `/public/index.php` adalah `644`. Permission folder storage tidak boleh `777` di sebagian shared hosting (gunakan `755` atau `775`).
* **Symlink Storage Tidak Berjalan**: Jika hosting tidak mengizinkan symlink via script PHP, Anda bisa membuat file PHP baru di `public_html/symlink.php` dengan isi:
  ```php
  <?php
  symlink('/home/username/si-karyawan/storage/app/public', '/home/username/public_html/storage');
  ```
  Akses berkas tersebut sekali lewat browser `https://domain-anda.com/symlink.php` lalu hapus berkas tersebut demi keamanan.
