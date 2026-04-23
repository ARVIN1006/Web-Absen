# Demo Mobile Runbook - Sistem Absensi Wajah HRIS

Panduan ini dibuat agar presenter, tester, atau user baru bisa menjalankan aplikasi, membuka dari mobile, dan mengetes fitur utama tanpa perlu menebak alur. Ikuti dari atas ke bawah saat demo.

## 1. Ringkasan Aplikasi

Aplikasi ini adalah sistem HRIS dan absensi wajah berbasis Laravel + Inertia React. Fitur utama yang bisa didemokan:

- Login role admin dan employee.
- Dashboard HRIS.
- Presensi dengan foto wajah, status hadir, dan geofence lokasi.
- Employee self-service: profil pribadi, cuti, reimbursement, payroll, direktori, koreksi absensi.
- Admin HR: karyawan, departemen, jabatan, shift, lokasi, cabang, approval, payroll, notifikasi, audit trail.
- Mode demo agar aplikasi tetap bisa diuji di laptop/HP tanpa harus berada di lokasi kantor asli.

## 2. Kebutuhan Sebelum Menjalankan

Pastikan sudah tersedia:

- PHP 8.3 atau lebih baru.
- Composer.
- Node.js dan npm.
- Browser Chrome atau Edge terbaru.
- SQLite, sudah digunakan default oleh project.
- HP dan laptop berada di jaringan Wi-Fi/hotspot yang sama jika ingin membuka dari mobile.

Cek cepat versi:

```bash
php -v
composer -V
node -v
npm -v
```

## 3. Setup Demo Pertama Kali

Jalankan dari folder project:

```bash
composer install
npm install
composer demo:prepare
```

Perintah `composer demo:prepare` akan:

- Membuat `database/database.sqlite` jika belum ada.
- Membuat `.env` dari `.env.example` jika belum ada.
- Generate `APP_KEY`.
- Reset database dan seed data demo.
- Membuat storage link.
- Build asset frontend.

## 4. Pengaturan `.env` Untuk Demo

Pastikan nilai ini aktif di `.env`:

```env
APP_ENV=local
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

DEMO_MODE=true
DEMO_BYPASS_FACE_VERIFICATION=true
DEMO_BYPASS_GEOFENCE=true
```

Arti mode demo:

- `DEMO_MODE=true`: aplikasi berjalan dalam mode presentasi.
- `DEMO_BYPASS_FACE_VERIFICATION=true`: presensi tidak gagal jika kamera/wajah referensi belum ideal.
- `DEMO_BYPASS_GEOFENCE=true`: presensi tidak gagal karena posisi GPS di luar area kantor.

Untuk demo produksi/realistic test, ubah bypass ke `false`, tapi pastikan lokasi dan referensi wajah sudah benar.

## 5. Menjalankan Di Laptop

Mode paling sederhana:

```bash
php artisan serve
```

Buka:

```text
http://127.0.0.1:8000
```

Jika sedang develop frontend dan ingin hot reload:

```bash
npm run dev
```

Jalankan `php artisan serve` dan `npm run dev` di terminal berbeda.

## 6. Menjalankan Untuk Dibuka Dari Mobile

### 6.1 Cari IP Laptop

Di Windows PowerShell:

```powershell
ipconfig
```

Cari `IPv4 Address`, contoh:

```text
192.168.1.25
```

### 6.2 Jalankan Laravel Agar Bisa Diakses HP

Gunakan host `0.0.0.0`:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Jika memakai Vite dev server:

```bash
npm run dev -- --host=0.0.0.0
```

### 6.3 Buka Dari HP

Pastikan HP dan laptop memakai Wi-Fi/hotspot yang sama. Buka browser HP:

```text
http://192.168.1.25:8000
```

Ganti `192.168.1.25` dengan IP laptop kamu.

### 6.4 Jika Tidak Bisa Dibuka Dari HP

Cek ini satu per satu:

- HP dan laptop harus satu jaringan.
- Windows Firewall mungkin memblokir port `8000`.
- Coba matikan VPN sementara.
- Pastikan command memakai `--host=0.0.0.0`, bukan hanya `php artisan serve` default.
- Coba buka dari laptop dulu: `http://127.0.0.1:8000`.
- Coba buka dari laptop pakai IP sendiri: `http://192.168.1.25:8000`.

## 7. Catatan Penting Kamera Di Mobile

Browser mobile biasanya hanya mengizinkan kamera pada:

- `https://...`
- `http://localhost`

Jika HP membuka aplikasi via `http://IP-LAPTOP:8000`, akses kamera bisa diblokir oleh browser. Untuk demo mobile ada dua pilihan:

### Opsi Aman Untuk Presentasi

Gunakan mode demo:

```env
DEMO_MODE=true
DEMO_BYPASS_FACE_VERIFICATION=true
DEMO_BYPASS_GEOFENCE=true
```

Dengan ini alur presensi tetap bisa ditunjukkan walaupun kamera/GPS tidak sempurna.

### Opsi Test Kamera Nyata Di Mobile

Gunakan HTTPS tunnel seperti Ngrok atau Cloudflare Tunnel, lalu buka URL HTTPS dari HP.

Contoh dengan Ngrok:

```bash
php artisan serve --host=0.0.0.0 --port=8000
ngrok http 8000
```

Lalu buka URL HTTPS dari Ngrok di HP.

## 8. Akun Demo

Gunakan akun berikut:

| Role | Email | Password | Kegunaan |
|---|---|---|---|
| Admin | `admin@gmail.com` | `password` | Demo dashboard admin, master data, approval, payroll |
| Employee | `test@gmail.com` | `password` | Demo self-service employee |
| Employee sample | `budi@example.com` | `password` | Data sample direktori/karyawan |
| Employee sample | `siti@example.com` | `password` | Data sample direktori/karyawan |
| Employee sample | `agus@example.com` | `password` | Data sample direktori/karyawan |
| Employee sample | `dewi@example.com` | `password` | Data sample direktori/karyawan |
| Employee sample | `eko@example.com` | `password` | Data sample direktori/karyawan |

## 9. Alur Demo Admin

Login sebagai admin:

```text
admin@gmail.com / password
```

Urutan demo yang disarankan:

1. Buka Dashboard Admin.
2. Tunjukkan statistik HRIS dan ringkasan presensi.
3. Buka Karyawan, lalu lihat detail salah satu karyawan.
4. Tunjukkan data profil, dokumen, histori karier, dan audit trail.
5. Buka Departemen, Jabatan, Shift, Cabang, Lokasi.
6. Buka Approval Center untuk cuti, reimbursement, dan koreksi absensi.
7. Buka Payroll dan Komponen Payroll.
8. Buka Notifikasi di navbar dan Inbox Notifikasi.
9. Buka Audit Trail untuk bukti aktivitas sistem.
10. Logout.

Checklist admin berhasil:

- Bisa login admin.
- Bisa membuka data karyawan.
- Bisa melihat master data HR.
- Bisa membuka approval.
- Bisa melihat payroll.
- Bisa melihat notifikasi.

## 10. Alur Demo Employee Mobile

Login sebagai employee dari HP:

```text
test@gmail.com / password
```

Urutan demo mobile yang disarankan:

1. Buka Dashboard Employee.
2. Buka Presensi.
3. Coba alur check-in atau check-out.
4. Buka Profil Saya.
5. Update profil pribadi, misalnya nomor HP atau alamat.
6. Buka Pengajuan Cuti dan buat request cuti.
7. Buka Reimbursement dan buat klaim sederhana.
8. Buka Payroll Saya untuk melihat slip/payroll.
9. Buka Direktori Karyawan dan coba search/filter.
10. Buka Notifikasi.

Checklist employee berhasil:

- Bisa login dari mobile.
- Menu tidak kepotong di layar HP.
- Notifikasi tidak kepotong di mobile.
- Direktori nyaman discroll dan filter bisa dipakai.
- User bisa update profil pribadi.
- User bisa mengirim request cuti/reimbursement.

## 11. Data Yang Bisa Diuji User

### Profil Pribadi

User bisa mengubah:

- Nama lengkap.
- Nomor HP.
- Email personal.
- Tempat dan tanggal lahir.
- Jenis kelamin.
- Alamat saat ini dan domisili.
- Status pernikahan, agama, kewarganegaraan.
- Informasi rekening.
- Password.

User tidak bisa mengubah sendiri:

- Email kerja/login.
- Role.
- Departemen.
- Jabatan.
- Cabang.
- Shift.
- Status karyawan.

Data tersebut dikelola admin agar struktur HR tetap konsisten.

### Presensi

Yang perlu dites:

- Halaman presensi bisa dibuka.
- Kamera/GPS tampil jika browser mengizinkan.
- Dalam mode demo, presensi tidak gagal karena wajah/GPS.
- Status check-in/check-out berubah setelah submit.

### Cuti

Yang perlu dites:

- User bisa melihat saldo cuti.
- User bisa membuat request cuti.
- Admin bisa melihat request di approval.
- Admin bisa approve/reject.

### Reimbursement

Yang perlu dites:

- User bisa membuat klaim reimbursement.
- Admin bisa melihat klaim.
- Admin bisa approve/reject.

### Payroll

Yang perlu dites:

- Admin bisa melihat/generate payroll.
- Employee bisa melihat payroll miliknya.

### Direktori

Yang perlu dites:

- Search nama/email/jabatan.
- Filter departemen.
- Card employee nyaman di mobile.
- Tombol email dan WhatsApp tersedia jika data ada.

## 12. Reset Data Demo

Jika data demo sudah berantakan, reset ulang:

```bash
composer demo:prepare
```

Peringatan: command ini menjalankan `migrate:fresh --seed`, jadi database lokal akan direset.

## 13. Troubleshooting Cepat

### Error database SQLite

Pastikan file database ada:

```bash
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --seed
```

### Asset React tidak update

Jalankan:

```bash
npm run build
```

Atau saat development:

```bash
npm run dev
```

### PowerShell menolak `npm run build`

Gunakan:

```powershell
npm.cmd run build
```

### Storage gambar tidak tampil

Jalankan:

```bash
php artisan storage:link
```

### Route/cache terasa aneh

Jalankan:

```bash
php artisan optimize:clear
```

### HP tidak bisa akses server laptop

- Pakai `php artisan serve --host=0.0.0.0 --port=8000`.
- Pastikan satu Wi-Fi.
- Buka `http://IP-LAPTOP:8000`.
- Izinkan firewall Windows untuk PHP.

### Kamera mobile tidak muncul

- Gunakan HTTPS tunnel untuk test kamera nyata.
- Untuk presentasi biasa, aktifkan bypass demo.

## 14. Script Yang Sering Dipakai

```bash
composer demo:prepare
php artisan serve
php artisan serve --host=0.0.0.0 --port=8000
npm run dev
npm.cmd run build
php artisan optimize:clear
php artisan test
```

## 15. Talking Points Untuk Presentasi

- Sistem ini menggabungkan HRIS, absensi wajah, approval, payroll, dan employee self-service dalam satu aplikasi.
- Employee bisa memakai aplikasi dari mobile untuk presensi, cuti, reimbursement, direktori, payroll, dan update profil pribadi.
- Admin tetap mengontrol data sensitif seperti jabatan, departemen, shift, role, dan status karyawan.
- Mode demo membuat presentasi aman dilakukan di mana saja tanpa bergantung lokasi kantor atau kondisi kamera.
- Untuk implementasi nyata, bypass demo dimatikan agar validasi wajah dan geofence berjalan normal.

## 16. Checklist Final Sebelum Demo

- `composer demo:prepare` sudah dijalankan.
- `.env` sudah memakai mode demo.
- `php artisan serve` aktif.
- Jika mobile: server dijalankan dengan `--host=0.0.0.0`.
- HP dan laptop satu jaringan.
- Akun admin dan employee berhasil login.
- Halaman dashboard, presensi, profil, direktori, approval, dan payroll bisa dibuka.
- Browser Chrome/Edge terbaru sudah siap.
- Hotspot cadangan tersedia jika Wi-Fi venue bermasalah.
