# HRIS Face Attendance Demo

Aplikasi HRIS berbasis Laravel + Inertia React dengan fokus pada presensi wajah, geofence kantor, employee self service, approval, payroll, audit trail, dan notifikasi. Paket ini juga sudah disiapkan untuk kebutuhan demo client agar bisa dibawa presentasi di mana saja.

## Fitur Utama

- Presensi berbasis wajah dan lokasi kantor
- Master data HRIS: cabang, departemen, posisi, tipe karyawan, shift, hari libur
- Employee self service: cuti, reimbursement, slip gaji, profil, direktori
- Approval center untuk cuti, reimbursement, dan koreksi absensi
- Payroll dan komponen payroll
- Audit trail dan notification center
- UI mobile-first untuk kebutuhan supervisor dan employee

## Tech Stack

- Backend: Laravel 13, PHP 8.3
- Frontend: Inertia React, Vite, Tailwind CSS
- Database default demo: SQLite

## Quick Start

```bash
composer install
npm install
composer demo:prepare
php artisan serve
```

Login demo:

- Admin: `admin@gmail.com` / `password`
- Employee: `test@gmail.com` / `password`

## Demo Mode

File `.env.example` sudah berisi mode presentasi:

- `DEMO_MODE=true`
- `DEMO_BYPASS_FACE_VERIFICATION=true`
- `DEMO_BYPASS_GEOFENCE=true`

Dengan konfigurasi ini, demo presensi tetap bisa dilakukan dari lokasi mana saja tanpa tergantung GPS kantor asli atau data wajah final. Untuk simulasi operasional normal, ubah flag tersebut ke `false`.

## Dokumen Pendukung

- Demo runbook: [DEMO_PRESENTATION.md](DEMO_PRESENTATION.md)
- Panduan pengguna: [TUTORIAL_PENGGUNAAN.md](TUTORIAL_PENGGUNAAN.md)
- Catatan workflow database: [database_schema_workflow.md](database_schema_workflow.md)
