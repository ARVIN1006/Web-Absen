<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f1117">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>{{ config('app.name') }} – Absensi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Init Theme
        const t = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', t);
        if (t === 'light') document.querySelector('meta[name="theme-color"]').setAttribute('content', '#f3f4f6');
    </script>

    <style>
        :root {
            --bg-body: #0f1117;
            --bg-sidebar: #12151f;
            --bg-glass: rgba(255,255,255,0.05);
            --border-glass: rgba(255,255,255,0.08);
            --text-main: #ffffff;
            --text-muted: #9ca3af;
            --text-muted-dark: #6b7280;
            --dropdown-bg: #1a1d27;
            --hover-bg: rgba(255,255,255,0.06);
            --border-line: rgba(255,255,255,0.07);
            --shadow-drop: rgba(0,0,0,0.5);
            --icon-color: #ffffff;
            --logo-text: #ffffff;
            --sidebar-width: 260px;
        }
        [data-theme="light"] {
            --bg-body: #f3f4f6;
            --bg-sidebar: #ffffff;
            --bg-glass: #ffffff;
            --border-glass: #e5e7eb;
            --text-main: #111827;
            --text-muted: #4b5563;
            --text-muted-dark: #6b7280;
            --dropdown-bg: #ffffff;
            --hover-bg: rgba(0,0,0,0.04);
            --border-line: #e5e7eb;
            --shadow-drop: rgba(0,0,0,0.1);
            --icon-color: #374151;
            --logo-text: #111827;
        }

        *, *::before, *::after { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { margin:0; background:var(--bg-body); color:var(--text-main); font-family:'Inter',sans-serif; transition: background 0.3s, color 0.3s; overflow-x: hidden; }
        
        /* Global Select & Option visibility fix */
        select option {
            background-color: #1a1d27;
            color: #ffffff;
        }
        [data-theme="light"] select option {
            background-color: #ffffff;
            color: #111827;
        }

        /* ─── Custom Scrollbar ─── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.2); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(156, 163, 175, 0.4); }
        [data-theme="dark"] ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); }
        [data-theme="dark"] ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
        
        .sidebar-nav::-webkit-scrollbar { width: 4px; }

        .glass { background:var(--bg-glass); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); border:1px solid var(--border-glass); transition: all 0.3s; }

        /* ─── Layout ─── */
        .app-container { display: flex; min-height: 100vh; }
        .main-content { flex: 1; min-width: 0; display: flex; flex-direction: column; transition: all 0.3s; }

        /* ─── Sidebar ─── */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-line);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            border-bottom: 1px solid var(--border-line);
        }

        .sidebar-nav {
            flex: 1;
            padding: 20px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            overflow-y: auto;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 12px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .nav-item:hover {
            background: var(--hover-bg);
            color: var(--text-main);
        }
        .nav-item.active {
            background: rgba(59,130,246,0.1);
            color: #3b82f6;
        }
        .nav-item svg { width: 18px; height: 18px; flex-shrink: 0; }

        .nav-section-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted-dark);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 16px 14px 8px;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--border-line);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            border-radius: 12px;
            background: var(--hover-bg);
        }
        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg,#3b82f6,#10b981);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff; font-size: 14px;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: 13px; font-weight: 600; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-email { font-size: 11px; color: var(--text-muted-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* ─── Mobile Top Bar ─── */
        .mobile-header {
            display: none;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            height: 56px;
            background: var(--bg-sidebar);
            border-bottom: 1px solid var(--border-line);
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 950;
        }

        /* ─── Desktop content offset ─── */
        @media (min-width: 1024px) {
            .main-content { padding-left: var(--sidebar-width); }
        }

        /* ─── Top Navbar (Desktop) ─── */
        .top-navbar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            height: 64px;
            padding: 0 32px;
            background: var(--bg-body);
            border-bottom: 1px solid var(--border-line);
            position: sticky;
            top: 0;
            z-index: 800;
        }

        .navbar-actions { display: flex; align-items: center; gap: 20px; }
        .nav-icon-btn { 
            position: relative; color: var(--text-muted); 
            cursor: pointer; transition: color 0.2s; 
            display: flex; align-items: center; justify-content: center;
        }
        .nav-icon-btn:hover { color: var(--text-main); }
        .nav-icon-btn .badge-dot { 
            position: absolute; top: -2px; right: -2px; 
            width: 8px; height: 8px; background: #ef4444; 
            border-radius: 50%; border: 2px solid var(--bg-body);
        }

        @media (max-width: 1023px) {
            .top-navbar { display: none; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .mobile-header { display: flex; }
            .sidebar-overlay.show { display: block; }
        }
        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.5; }
        }
    </style>
</head>
<body>
    {{-- SIDEBAR OVERLAY --}}
    <div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <div class="app-container">
        {{-- SIDEBAR --}}
        <aside id="sidebar" class="sidebar">
            <a href="{{ route('dashboard') }}" class="sidebar-header">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="width:32px;height:32px;object-fit:contain;">
                <div>
                    <div style="font-size:14px;font-weight:700;color:var(--logo-text);line-height:1.2;">{{ config('app.name') }}</div>
                    <div style="font-size:11px;color:var(--text-muted-dark);line-height:1.2;">Sistem Absensi</div>
                </div>
            </a>

            <nav class="sidebar-nav">
                <div class="nav-section-label">Menu Utama</div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard Saya
                </a>
                <a href="{{ route('attendance.index') }}" class="nav-item {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Presensi Wajah
                </a>
                <a href="{{ route('profile.index') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil Saya
                </a>

                <div class="nav-section-label">Layanan Mandiri</div>
                <a href="{{ route('reimbursements.index') }}" class="nav-item {{ request()->routeIs('reimbursements.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Reimbursement
                </a>
                <a href="{{ route('payrolls.user_index') }}" class="nav-item {{ request()->routeIs('payrolls.user_index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Slip Gaji
                </a>
                <a href="{{ route('company-directory') }}" class="nav-item {{ request()->routeIs('company-directory') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Direktori Kontak
                </a>

                @if(Auth::user()->isAdmin())
                <div class="nav-section-label">Administrasi HR</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Panel Manajemen
                </a>
                <a href="{{ route('admin.approvals') }}" class="nav-item {{ request()->routeIs('admin.approvals') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Pusat Persetujuan
                </a>
                <a href="{{ route('admin.employees.index') }}" class="nav-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Data Karyawan
                </a>

                <div class="nav-section-label">Struktur & Jadwal</div>
                <a href="{{ route('admin.departments.index') }}" class="nav-item {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Departemen
                </a>
                <a href="{{ route('admin.positions.index') }}" class="nav-item {{ request()->routeIs('admin.positions.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Jabatan & Posisi
                </a>
                <a href="{{ route('admin.work-shifts.index') }}" class="nav-item {{ request()->routeIs('admin.work-shifts.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Shift Kerja
                </a>

                <div class="nav-section-label">Laporan & Sistem</div>
                <a href="{{ route('admin.attendances.index') }}" class="nav-item {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan Presensi
                </a>
                <a href="{{ route('admin.payrolls.index') }}" class="nav-item {{ request()->routeIs('admin.payrolls.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Manajemen Payroll
                </a>
                <a href="{{ route('admin.locations.index') }}" class="nav-item {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Lokasi Kantor
                </a>
                <a href="{{ route('admin.announcements.index') }}" class="nav-item {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    Pengumuman
                </a>
                @endif

                <div class="nav-section-label">Bantuan</div>
                <a href="{{ route('guide') }}" class="nav-item {{ request()->routeIs('guide') ? 'active' : '' }}" style="color: #34d399;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Panduan Aplikasi
                </a>
            </nav>

            <div class="sidebar-footer">
                <button class="btn-theme" onclick="toggleTheme()" style="justify-content:center; width:100%; padding:10px; border:1px solid var(--border-line); border-radius:12px;">
                    <span class="icon-sun" style="display:none; align-items:center; gap:8px;">
                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        Light Mode
                    </span>
                    <span class="icon-moon" style="display:none; align-items:center; gap:8px;">
                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        Dark Mode
                    </span>
                </button>

                <div class="user-profile">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-email">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="nav-item" style="width:100%; background:none; border:none; cursor:pointer; color:#f87171;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN CONTENT AREA --}}
        <div class="main-content">
            {{-- TOP NAVBAR (DESKTOP) --}}
            <header class="top-navbar">
                <div class="navbar-actions">
                    {{-- Global Real-time Clock --}}
                    <div style="display: flex; align-items: center; gap: 10px; padding-right: 15px; margin-right: 15px; border-right: 1px solid var(--border-line);">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 8px #10b981; animation: pulse 2s infinite;"></div>
                        <div id="global-clock" style="font-family: 'JetBrains Mono', monospace; font-size: 15px; font-weight: 700; color: var(--text-main); min-width: 85px;">00:00:00</div>
                    </div>
                    
                    <div class="nav-icon-btn">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="badge-dot"></span>
                    </div>
                    <div class="nav-icon-btn" onclick="toggleTheme()">
                        <svg class="icon-sun" width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <svg class="icon-moon" width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    </div>
                    <div style="width: 1px; height: 24px; background: var(--border-line);"></div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px;">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
                        <span style="font-size: 13px; font-weight: 600; color: var(--text-main);">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </header>

            {{-- MOBILE HEADER --}}
            <header class="mobile-header">
                <button onclick="toggleSidebar()" style="background:none; border:none; color:var(--text-main); padding:8px;">
                    <svg style="width:24px;height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div style="font-weight:700; font-size:15px;">{{ config('app.name') }}</div>
                <div style="width:40px;"></div> {{-- Spacer --}}
            </header>

            {{-- DEMO MODE BANNER --}}
            <div style="background: linear-gradient(to right, #3b82f6, #8b5cf6); color: white; text-align: center; padding: 6px; font-size: 11px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; z-index: 100; position: relative;">
                Demo Mode: Sistem Absensi & HRD Management
            </div>

            <main style="padding: 24px;">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    }

    // Theme Logic
    function updateThemeIcons() {
        const isLight = document.documentElement.getAttribute('data-theme') === 'light';
        document.querySelectorAll('.icon-sun').forEach(el => el.style.display = isLight ? 'none' : 'flex');
        document.querySelectorAll('.icon-moon').forEach(el => el.style.display = isLight ? 'flex' : 'none');
    }
    updateThemeIcons();

    // Real-time Clock
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeString = `${hours}:${minutes}:${seconds}`;
        
        const globalClock = document.getElementById('global-clock');
        const dashboardClock = document.getElementById('realtime-clock');
        
        if (globalClock) globalClock.textContent = timeString;
        if (dashboardClock) dashboardClock.textContent = `${hours}:${minutes}`;
    }

    setInterval(updateClock, 1000);
    updateClock();

    function toggleTheme() {
        const html = document.documentElement;
        const isLight = html.getAttribute('data-theme') === 'light';
        const newTheme = isLight ? 'dark' : 'light';
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        document.querySelector('meta[name="theme-color"]').setAttribute('content', newTheme === 'light' ? '#f3f4f6' : '#0f1117');
        updateThemeIcons();
    }

    // Real-time Clock Logic
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeStr = `${hours}:${minutes}:${seconds}`;
        const timeShortStr = `${hours}:${minutes}`;

        // Update Global Clock (with seconds)
        const globalClock = document.getElementById('global-clock');
        if (globalClock) globalClock.textContent = timeStr;

        // Update Dashboard Clock (if exists)
        const dashboardClock = document.getElementById('realtime-clock');
        if (dashboardClock) dashboardClock.textContent = timeShortStr;
    }
    setInterval(updateClock, 1000);
    updateClock();
    </script>
</body>
</html>
