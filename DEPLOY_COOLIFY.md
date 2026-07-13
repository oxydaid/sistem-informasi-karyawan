# Panduan Deployment Coolify

[Coolify](https://coolify.io/) adalah platform self-hosted PaaS alternatif Heroku/Render yang sangat ramah terhadap Docker Compose dan Nixpacks. Aplikasi ini terdiri dari dua layanan (Laravel PHP + Node.js WhatsApp Gateway) yang dapat dideploy di Coolify dengan dua metode berikut.

---

## 🚀 Metode A: Docker Compose Stack (Direkomendasikan)
Metode ini adalah yang termudah karena mengonsolidasikan database, Redis, Laravel, dan WhatsApp Gateway dalam satu grup layanan (*Stack*).

### Langkah-langkah:
1. Masuk ke dashboard Coolify Anda.
2. Pilih atau buat **Project** dan **Environment** baru.
3. Klik **+ Add Resource** -> **Docker Compose**.
4. Pilih sumber pengunggahan (misalnya menghubungkan GitHub repository Anda).
5. Pada konfigurasi docker-compose, pastikan Coolify membaca berkas `docker-compose.prod.yml` yang telah disediakan di root proyek.
6. Konfigurasikan **Environment Variables** di tab **Environment Variables** Coolify:
   - `DB_DATABASE` (contoh: `manajemen_karyawan`)
   - `DB_USERNAME` (contoh: `karyawan_user`)
   - `DB_PASSWORD` (contoh: `password_karyawan_123`)
   - `APP_KEY` (generate key Laravel Anda)
7. Simpan konfigurasi dan klik **Deploy**.
8. Buka tab **Destinations / Ports** untuk mengatur domain publik ke container `app` (port `80`) dan container `whatsapp` (port `6969`).

---

## 📦 Metode B: Standalone Nixpacks (Dua Aplikasi Terpisah)
Jika Anda ingin memisahkan hosting aplikasi web Laravel dengan Node.js WhatsApp Gateway (misalnya menggunakan database eksternal yang dikelola oleh Coolify), Anda dapat menggunakan builder **Nixpacks**.

### Bagian 1: Deploy Aplikasi Laravel (Web Frontend)
1. Di Coolify, klik **+ Add Resource** -> **Public Repository / Private Repository** (GitHub).
2. Hubungkan repository Anda, dan set **Base Directory** ke `/` (root).
3. Pilih Build Pack: **Nixpacks** (secara otomatis akan membaca `nixpacks.toml` yang sudah kita buat).
4. Di tab **Environment Variables**, tambahkan:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_KEY=base64:...`
   - Hubungkan kredensial database (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) yang disediakan oleh Database Service di Coolify.
5. Klik **Deploy**. Coolify akan otomatis men-compile assets melalui pnpm, memasang php extensions, dan menjalankan migrasi database.

### Bagian 2: Deploy WhatsApp Gateway (Node.js Service)
1. Klik **+ Add Resource** -> **Public Repository / Private Repository** (GitHub yang sama).
2. Di konfigurasi pembuatan, ubah **Base Directory** menjadi `/whatsapp`.
3. Pilih Build Pack: **Nixpacks** (Nixpacks akan otomatis mendeteksi Node.js karena ada file `/whatsapp/package.json`).
4. Di tab **Environment Variables**, masukkan:
   - `DB_HOST` (IP database server Coolify Anda)
   - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (sesuaikan dengan database Laravel)
   - `WA_GATEWAY_PORT=6969`
   - `WA_GATEWAY_HOST=0.0.0.0`
5. Atur port target di Coolify ke `6969` agar layanan Express dapat diakses secara publik atau privat.
6. Klik **Deploy**.

---

## 🛠️ Sinkronisasi Aplikasi (Webhooks & API)
Setelah kedua aplikasi berjalan:
1. Catat URL publik dari WhatsApp Gateway Anda (misal `https://wa.domain-anda.com`).
2. Masuk ke halaman setting **Environment Variables** di aplikasi Laravel Anda, lalu ubah nilai `WA_GATEWAY_URL` dengan URL tersebut:
   ```env
   WA_GATEWAY_URL=https://wa.domain-anda.com
   ```
3. Lakukan deploy ulang / restart pada container Laravel agar perubahan `.env` diterapkan.
