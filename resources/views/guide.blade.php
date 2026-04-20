<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Demo - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #0f172a;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --bg-glass: rgba(30, 41, 59, 0.7);
            --border-glass: rgba(255, 255, 255, 0.1);
            --primary: #3b82f6;
            --primary-hover: #2563eb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(59, 130, 246, 0.15), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(16, 185, 129, 0.15), transparent 25%);
            background-attachment: fixed;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            color: var(--text-main);
            text-decoration: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 24px;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: rgba(255,255,255,0.1);
        }

        .card {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 40px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #60a5fa, #34d399);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h2 {
            font-size: 20px;
            font-weight: 600;
            margin-top: 32px;
            margin-bottom: 16px;
            border-bottom: 1px solid var(--border-glass);
            padding-bottom: 8px;
        }

        h3 {
            font-size: 16px;
            color: #60a5fa;
            margin-top: 24px;
        }

        p {
            color: var(--text-muted);
            font-size: 15px;
        }

        ul, ol {
            color: var(--text-muted);
            font-size: 15px;
            padding-left: 20px;
        }

        li {
            margin-bottom: 8px;
        }

        .alert {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
            color: #fcd34d;
            padding: 16px;
            border-radius: 12px;
            margin: 24px 0;
            font-size: 14px;
        }

        code {
            background: rgba(0,0,0,0.3);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            color: #fbbf24;
        }

        @media (max-width: 600px) {
            .card { padding: 24px; }
            h1 { font-size: 24px; }
        }
    </style>
</head>
<body>

    <div class="container">
        <a href="{{ route('login') }}" class="btn-back">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Login
        </a>

        <div class="card">
            <h1>🚀 Panduan Uji Coba (Demo Mandiri)</h1>
            <p>Selamat datang di sistem HRIS berbasis Pengenalan Wajah & GPS. Dokumen ini akan memandu Bapak/Ibu untuk mensimulasikan dua peran utama: <strong>Sebagai Karyawan</strong> dan <strong>Sebagai Manajer/HRD</strong>.</p>

            <div class="alert">
                <strong>Penting:</strong> Pastikan Anda memberikan izin (Allow) pada browser untuk mengakses <strong>Kamera</strong> dan <strong>Lokasi (GPS)</strong> agar fitur keamanan biometrik dapat bekerja.
            </div>

            <div style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); color: #60a5fa; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-size: 14px;">
                <strong>💡 Info Demo:</strong> Untuk keperluan uji coba mandiri, kami telah mengaktifkan <em>"Area Demo (Global Access)"</em> dengan radius tak terbatas. Bapak/Ibu tetap bisa melihat kalkulasi jarak GPS yang asli, namun sistem tetap mengizinkan absensi dari lokasi manapun.
            </div>

            <h2>👤 PERAN 1: Mencoba Sebagai Karyawan</h2>
            <p>Di tahap ini, Anda akan merasakan canggihnya absen dengan pemindaian biometrik AI tanpa perlu alat tambahan.</p>
            <ol>
                <li>Kembali ke halaman <a href="{{ route('login') }}" style="color: #60a5fa;">Login Utama</a> dan klik tombol <strong>Daftar Akun Baru</strong>.</li>
                <li>Isi form data diri singkat dan pilih Departemen/Jabatan Anda.</li>
                <li>Di bagian bawah, posisikan wajah ke kamera. Setelah kotak pendeteksi stabil (warna biru), klik <strong>Ambil Foto Referensi</strong>.</li>
                <li>Selesaikan pendaftaran dan sistem akan membawa Anda masuk ke Dashboard.</li>
                <li>Klik tombol <strong>Absen Sekarang</strong>.</li>
                <li><strong>Coba trik ini:</strong> Tutupi wajah Anda dengan tangan atau arahkan kamera ke ruang kosong. Anda akan melihat tombol absen <strong>TERKUNCI</strong> (Anti-Kecurangan).</li>
                <li>Kembalikan wajah Anda. Saat status berubah menjadi <em>"Wajah Cocok"</em>, klik <strong>Absen Masuk</strong>.</li>
                <li>(Opsional) Anda juga bisa mencoba mengajukan cuti fiktif pada menu <strong>Cuti & Izin</strong>.</li>
            </ol>

            <h2>👑 PERAN 2: Mencoba Sebagai Manajer / HRD</h2>
            <p>Sekarang, mari kita lihat bagaimana sistem ini mempermudah pekerjaan admin dan personalia dalam memantau data secara <em>real-time</em>.</p>
            <ol>
                <li>Silakan <strong>Logout</strong> dari akun karyawan yang baru Anda buat.</li>
                <li>Kembali ke halaman Login dan gunakan akses Manajer berikut:<br>
                    Email: <code>admin@admin.com</code><br>
                    Password: <code>password</code>
                </li>
                <li>Setelah masuk, Anda akan disambut oleh <strong>Admin Dashboard</strong> dengan grafik kehadiran dan riwayat langsung (Anda akan melihat data absen Anda sendiri tadi).</li>
                <li>Buka menu <strong>Kelola Karyawan</strong> di panel kiri. Data Anda tadi sudah otomatis terdaftar.</li>
                <li>Buka menu <strong>Pengajuan Cuti</strong>. Jika tadi Anda mengajukan cuti, Anda dapat menyetujui (Approve) atau Menolaknya di sini.</li>
                <li>Buka menu <strong>Laporan Absensi</strong>. Di sini Anda dapat memfilter data dan mengklik <strong>Export PDF / CSV</strong> untuk melihat hasil laporan yang siap pakai untuk Payroll bulanan.</li>
            </ol>

            <h2 style="margin-top: 40px; text-align: center; border: none;">Selamat Mencoba! ✨</h2>
        </div>
    </div>

</body>
</html>
