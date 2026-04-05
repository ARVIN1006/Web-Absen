# Sistem Absensi Wajah - PT Serunting Sakti Jaya

Aplikasi presensi (absensi) modern berbasis **Pengenalan Wajah Berbasis AI (Face Recognition)** dan **Geo-Location**. Dibangun secara khusus untuk lingkungan korporat **PT Serunting Sakti Jaya**, aplikasi ini menjamin keaslian data absensi karyawan dan mencegah kecurangan.

## 🌟 Fitur Utama

- **Pendaftaran Biometrik Wajah**: Semua karyawan diwajibkan mendaftar dengan memindai foto wajah secara _real-time_ via web-camera untuk disimpan sebagai _Master Data_.
- **Validasi Absensi Berbasis AI**: Karyawan hanya bisa *Check-In* jika wajahnya cocok dengan foto rujukan di sistem (Didukung oleh library `face-api.js` komputasi AI dilakukan secara mandiri di sisi-klien (*browser* HP/Laptop pengguna) agar server bebas dari antrean pemrosesan berat).
- **Desain UI/UX Ppremium**: Antarmuka korporat profesional yang dilengkapi dukungan *Light Mode* dan *Dark Mode*, responsif pada seluruh ukuran layar.
- **Lapisan Keamanan Hosting**: Modifikasi sistematis `.htaccess` yang menghalangi pencuri untuk mengakses konfigurasi `.env` dan direktori vital Laravel pada lingkungan asrama / *Shared Hosting*.

## 🛠 Tech Stack (Teknologi)

- **Backend**: Laravel 11 (PHP 8.2+) / MySQL 8+
- **Frontend**: Blade Templating, Vanilla CSS
- **Kecerdasan Buatan**: Face-api.js Framework (via CDN)

---

## 🚀 Panduan Instalasi Lokal (Laragon / XAMPP)

1. **Jalankan Instalasi Ekstensi / Dependensi:**
   ```bash
   composer install
   npm install
   ```

2. **Pengaturan `.env`:**
   Gandakan file `.env.example` ubah namanya menjadi `.env`, lalu buat *App Key* baru Anda melalui terminal:
   ```bash
   php artisan key:generate
   ```
   **Catatan:** Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` dengan pengaturan MySQL lokal Anda.

3. **Migrasi Struktur Database (Penting!):**
   Eksekusi perintah di bawah ini agar struktur tabel tercetak ke database MySQL Anda:
   ```bash
   php artisan migrate
   ```

4. **Sistem Penautan Foto (Storage Link):**
   Aplikasi menaruh foto biometrik ke wilayah rahasia (`storage/app/public/...`), maka Anda harus membuat lorong akses rahasia tersebut agar bisa diakses oleh Face API dengan mengetik:
   ```bash
   php artisan storage:link
   ```

5. **Kompilasi Aset Antarmuka:**
   Tarik file-file desain UI Anda dan manpatkan (*compile*) menjadi bentuk jadi siap-pakai minimalis untuk mesin produksi (hosting):
   ```bash
   npm run build
   ```

## ☁️ Panduan Publikasi ke Shared Hosting (Hostinger)

Bagi pengguna Hostinger / Shared Hosting lain tanpa kebebasan mengganti *Document Root*. Apabila semua jeroan folder `public/` dilepaskan bebas di `public_html/`:

1. **Memberi Arah Baru Pada Laravel (`index.php`)**
   Carilah tempat tertulisnya:
   ```php
   $app = require_once __DIR__.'/bootstrap/app.php';
   ```
   Tepat di bawahnya sisipkan komando per-rute-an spesifik berikut:
   ```php
   $app->usePublicPath(__DIR__);
   ```

2. **Kompilasi Folder `build`:**
   Jangan lupa Anda harus menyeret / meng-upload _folder_ hasil `build` (**Point Panduan Lokal ke-5**) ke `public_html/` sebagai penopang *stylesheet*.

3. **Proteksi File Rahasia (`.htaccess` WAJIB):**
   Timpa file `.htaccess` terdalam di root (tempat `.env` bernaung bersama *public_html*) dengan:

   ```apache
   <IfModule mod_rewrite.c>
       Options -Indexes
       RewriteEngine On

       # Proteksi file krusial dari maling
       <FilesMatch "^\.env|composer\.json|package\.json|\.gitignore">
           Order allow,deny
           Deny from all
       </FilesMatch>

       # Kunci laci arsip utama Laravel agar tidak bocor
       RedirectMatch 404 ^/(app|bootstrap|config|database|resources|routes|storage|tests|vendor)/

       # Pengalir arus HTTP biasa menuju file pintu gerbang index.php
       RewriteCond %{HTTP:Authorization} .
       RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteRule ^ index.php [L]
   </IfModule>
   ```

4. **Penyegaran Sistem Internal via SSH:**
   Masuklah ke SSH/Terminal Hosting Anda:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan storage:link
   ```

Beres! Aplikasi kebanggaan karyawan perusahaan sudah online.
