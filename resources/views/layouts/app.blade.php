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

    <title>{{ config('app.name', 'PT Serunting Sakti Jaya') }} – Absensi</title>

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
            --bg-nav: #12151f;
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
        }
        [data-theme="light"] {
            --bg-body: #f3f4f6;
            --bg-nav: #ffffff;
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
        body { margin:0; background:var(--bg-body); color:var(--text-main); font-family:'Inter',sans-serif; transition: background 0.3s, color 0.3s; }
        .glass { background:var(--bg-glass); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); border:1px solid var(--border-glass); transition: all 0.3s; }

        /* ─── Desktop top navbar ─── */
        .nav-desktop {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; height: 60px; background: var(--bg-nav);
            border-bottom: 1px solid var(--border-line);
            position: sticky; top: 0; z-index: 100; transition: background 0.3s, border-color 0.3s;
        }
        .nav-desktop .nav-left  { display:flex; align-items:center; gap:8px; }
        .nav-desktop .nav-links { display:flex; align-items:center; gap:4px; margin-left:28px; }
        .nav-desktop .nav-link  { display:flex; align-items:center; gap:6px; padding:7px 14px; border-radius:10px; text-decoration:none; font-size:13.5px; font-weight:500; color:var(--text-muted); transition:all .15s; white-space:nowrap; }
        .nav-desktop .nav-link:hover  { background:var(--hover-bg); color:var(--text-main); }
        .nav-desktop .nav-link.active { background:rgba(59,130,246,0.12); color:#3b82f6; }
        .nav-desktop .nav-right { display:flex; align-items:center; gap:12px; }
        .nav-user-name  { font-size:13px; font-weight:600; color:var(--text-main); }
        .nav-user-email { font-size:11px; color:var(--text-muted-dark); }
        .avatar-btn { width:36px;height:36px;border-radius:50%;border:none;cursor:pointer;font-weight:700;font-size:14px;color:#fff;background:linear-gradient(135deg,#3b82f6,#10b981);flex-shrink:0; }
        .dropdown-menu { position:absolute;right:0;top:calc(100% + 8px);width:192px;background:var(--dropdown-bg);border:1px solid var(--border-glass);border-radius:14px;padding:6px;z-index:200;box-shadow:0 16px 40px var(--shadow-drop);transition:all 0.3s; }
        .dropdown-item { display:flex;align-items:center;gap:8px;padding:9px 12px;border-radius:9px;font-size:13px;color:var(--text-main);text-decoration:none;cursor:pointer;background:none;border:none;width:100%;transition:background .15s; }
        .dropdown-item:hover { background:var(--hover-bg); }
        .dropdown-item.danger { color:#f87171; }
        .dropdown-item.danger:hover { color:#ef4444; }
        .dropdown-sep { height:1px;background:var(--border-line);margin:4px 0; }
        .relative { position:relative; }

        .btn-theme { background:none;border:none;color:var(--text-muted);padding:8px;border-radius:10px;cursor:pointer;transition:all 0.15s;display:flex;align-items:center;gap:6px;}
        .btn-theme:hover { background:var(--hover-bg);color:var(--text-main); }

        /* ─── Mobile top bar ─── */
        .nav-mobile {
            display: none; align-items: center; justify-content: space-between;
            padding: 0 16px; height: 54px; background: var(--bg-nav);
            border-bottom: 1px solid var(--border-line);
            position: sticky; top: 0; z-index: 100; transition: background 0.3s, border-color 0.3s;
        }

        /* ─── Mobile bottom nav ─── */
        .bottom-nav {
            display: none; position: fixed; bottom: 0; left: 0; right: 0;
            height: calc(58px + env(safe-area-inset-bottom, 0px));
            padding-bottom: env(safe-area-inset-bottom, 0px);
            background: var(--bg-nav); border-top: 1px solid var(--border-line); z-index: 200; align-items: stretch; transition: background 0.3s, border-color 0.3s;
        }
        .bn-item {
            flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px;
            text-decoration: none; color: var(--text-muted-dark); font-size: 10px; font-weight: 500; padding: 8px 0 6px; transition: color .15s;
        }
        .bn-item:hover, .bn-item.active { color: #3b82f6; }
        .bn-item svg { width:22px;height:22px;flex-shrink:0; }
        .bn-fab {
            flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px;
            text-decoration: none; color: var(--text-main); font-size: 10px; font-weight: 600; padding-bottom: 4px;
        }
        .bn-fab-icon {
            width: 46px; height: 46px; border-radius: 14px; background: linear-gradient(135deg,#3b82f6,#10b981);
            display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 16px rgba(59,130,246,0.4);
            margin-top: -20px; flex-shrink: 0;
        }
        .bn-fab-icon svg { width:22px;height:22px; color: #fff;}
        .bn-fab:active .bn-fab-icon { transform:scale(0.92); }

        /* ─── Page content padding ─── */
        .page-wrap { min-height:100vh; display:flex; flex-direction:column; }

        /* ─── Mobile breakpoint ─── */
        @media (max-width: 767px) {
            .nav-desktop { display: none !important; }
            .nav-mobile  { display: flex !important; }
            .bottom-nav  { display: flex !important; }
            main { padding-bottom: calc(64px + env(safe-area-inset-bottom, 0px)); }
        }
    </style>
</head>
<body>
<div class="page-wrap">
    {{-- DEMO MODE BANNER --}}
    <div style="background: linear-gradient(to right, #3b82f6, #8b5cf6); color: white; text-align: center; padding: 6px; font-size: 11px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; z-index: 9999; position: relative;">
        Demo Mode: Sistem Absensi & HRD Management
    </div>

    {{-- DESKTOP NAV --}}
    <nav class="nav-desktop">
        <div class="nav-left">
            <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
                <div style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-width:100%;height:100%;object-fit:contain;">
                </div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:var(--logo-text);line-height:1.1;transition:color 0.3s;">PT Serunting Sakti Jaya</div>
                    <div style="font-size:11px;color:var(--text-muted-dark);line-height:1.1;transition:color 0.3s;">Sistem Absensi</div>
                </div>
            </a>
            <div class="nav-links">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg style="width:15px;height:15px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                    <svg style="width:15px;height:15px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Absensi
                </a>
            </div>
        </div>
        <div class="nav-right">
            <button class="btn-theme" onclick="toggleTheme()" aria-label="Toggle Theme">
                <svg class="icon-sun" style="width:18px;height:18px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                <svg class="icon-moon" style="width:18px;height:18px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
            </button>
            <div style="text-align:right;">
                <div class="nav-user-name">{{ Auth::user()->name }}</div>
                <div class="nav-user-email">{{ Auth::user()->email }}</div>
            </div>
            <div class="relative" id="desktopDropWrap">
                <button class="avatar-btn" onclick="toggleDrop('desktopDrop')">
                    {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                </button>
                <div id="desktopDrop" class="dropdown-menu" style="display:none;">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profil Saya
                    </a>
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Admin Panel
                    </a>
                    @endif
                    <div class="dropdown-sep"></div>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="dropdown-item danger">
                            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- MOBILE TOP BAR --}}
    <nav class="nav-mobile">
        <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:8px;text-decoration:none;">
            <div style="width:30px;height:30px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-width:100%;height:100%;object-fit:contain;">
            </div>
            <div>
                <div style="font-size:12px;font-weight:700;color:var(--logo-text);line-height:1.1;transition:color 0.3s;">PT Serunting Sakti Jaya</div>
                <div style="font-size:10px;color:var(--text-muted-dark);line-height:1.1;transition:color 0.3s;">Sistem Absensi</div>
            </div>
        </a>
        <div style="display:flex;align-items:center;gap:10px;">
            <button class="btn-theme p-0" onclick="toggleTheme()" aria-label="Toggle Theme" style="padding:6px;">
                <svg class="icon-sun" style="width:18px;height:18px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                <svg class="icon-moon" style="width:18px;height:18px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
            </button>
            <div class="relative" id="mobileDropWrap">
                <button class="avatar-btn" style="width:32px;height:32px;font-size:13px;" onclick="toggleDrop('mobileDrop')">
                    {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                </button>
                <div id="mobileDrop" class="dropdown-menu" style="display:none;min-width:176px;right:0;">
                    <div style="padding:10px 12px 8px;border-bottom:1px solid var(--border-line);">
                        <div style="font-size:13px;font-weight:600;color:var(--text-main);">{{ Auth::user()->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted-dark);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Auth::user()->email }}</div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item" style="margin-top:4px;">
                        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profil
                    </a>
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Admin
                    </a>
                    @endif
                    <div class="dropdown-sep"></div>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="dropdown-item danger">
                            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- PAGE CONTENT --}}
    <main style="flex:1;">
        @yield('content')
    </main>

</div>

{{-- MOBILE BOTTOM NAV --}}
<nav class="bottom-nav">
    <a href="{{ route('dashboard') }}" class="bn-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:22px;height:22px;flex-shrink:0;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('attendance.index') }}" class="bn-fab">
        <div class="bn-fab-icon">
            <svg fill="none" stroke="white" viewBox="0 0 24 24" style="width:22px;height:22px;flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        Absensi
    </a>
    <a href="{{ route('profile.edit') }}" class="bn-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:22px;height:22px;flex-shrink:0;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        Profil
    </a>
</nav>

<script>
function toggleDrop(id) {
    var el = document.getElementById(id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    ['desktopDrop','mobileDrop'].forEach(function(id) {
        var drop = document.getElementById(id);
        var wrap = document.getElementById(id === 'desktopDrop' ? 'desktopDropWrap' : 'mobileDropWrap');
        if (drop && wrap && !wrap.contains(e.target)) {
            drop.style.display = 'none';
        }
    });
});

// Theme Logic
function updateThemeIcons() {
    const isLight = document.documentElement.getAttribute('data-theme') === 'light';
    document.querySelectorAll('.icon-sun').forEach(el => el.style.display = isLight ? 'none' : 'block');
    document.querySelectorAll('.icon-moon').forEach(el => el.style.display = isLight ? 'block' : 'none');
}
updateThemeIcons();

function toggleTheme() {
    const html = document.documentElement;
    const isLight = html.getAttribute('data-theme') === 'light';
    const newTheme = isLight ? 'dark' : 'light';
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    document.querySelector('meta[name="theme-color"]').setAttribute('content', newTheme === 'light' ? '#f3f4f6' : '#0f1117');
    updateThemeIcons();
}
</script>
</body>
</html>
