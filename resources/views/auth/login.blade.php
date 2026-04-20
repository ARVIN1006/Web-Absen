<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0f1117">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Login – {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        const t = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', t);
        if (t === 'light') document.querySelector('meta[name="theme-color"]').setAttribute('content', '#f3f4f6');
    </script>
    <style>
        :root {
            --bg-body: #0f1117;
            --bg-card: rgba(255,255,255,0.05);
            --border-card: rgba(255,255,255,0.1);
            --border-focus: #3b82f6;
            --text-main: #ffffff;
            --text-muted: #9ca3af;
            --text-muted-dark: #6b7280;
            --btn-bg: linear-gradient(135deg,#3b82f6,#1d4ed8);
            --btn-shadow: rgba(59,130,246,0.35);
            --btn-alt-bg: rgba(255,255,255,0.04);
            --btn-alt-text: #d1d5db;
            --divider: rgba(255,255,255,0.08);
            --icon-color: #ffffff;
            --shadow-drop: rgba(0,0,0,0.5);
            --bg-icon: rgba(59,130,246,0.2);
            --icon-stroke: #60a5fa;
            --input-bg: rgba(255,255,255,0.05);
        }
        [data-theme="light"] {
            --bg-body: #f3f4f6;
            --bg-card: #ffffff;
            --border-card: #e5e7eb;
            --border-focus: #3b82f6;
            --text-main: #111827;
            --text-muted: #4b5563;
            --text-muted-dark: #6b7280;
            --btn-bg: linear-gradient(135deg,#3b82f6,#2563eb);
            --btn-shadow: rgba(59,130,246,0.25);
            --btn-alt-bg: #ffffff;
            --btn-alt-text: #374151;
            --divider: #d1d5db;
            --icon-color: #374151;
            --shadow-drop: rgba(0,0,0,0.1);
            --bg-icon: rgba(59,130,246,0.15);
            --icon-stroke: #3b82f6;
            --input-bg: #f9fafb;
        }

        *,*::before,*::after{box-sizing:border-box;-webkit-tap-highlight-color:transparent;}
        body{margin:0;font-family:'Inter',sans-serif;background:var(--bg-body);min-height:100vh;min-height:100dvh;display:flex;align-items:stretch; transition: background 0.3s;}
        
        /* Global Select & Option visibility fix */
        select option {
            background-color: #1a1d27;
            color: #ffffff;
        }
        [data-theme="light"] select option {
            background-color: #ffffff;
            color: #111827;
        }

        /* ── Left panel: branding ── */
        .auth-left{
            flex:1;
            display:flex;flex-direction:column;align-items:center;justify-content:center;
            padding:48px 40px;
            position:relative;overflow:hidden;
            background:linear-gradient(145deg,#0d1b2a 0%,#0f2744 50%,#071c38 100%);
            border-right: 1px solid var(--divider);
        }
        .auth-left::before{
            content:'';position:absolute;inset:0;
            background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233b82f6' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .auth-left-glow1{position:absolute;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(59,130,246,0.25),transparent);top:-80px;left:-80px;pointer-events:none;}
        .auth-left-glow2{position:absolute;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(16,185,129,0.18),transparent);bottom:-60px;right:-60px;pointer-events:none;}
        .auth-left-content{position:relative;z-index:1;text-align:center;max-width:380px;}
        .auth-logo{width:90px;height:auto;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;}
        .auth-logo img{max-width:100%;height:auto;object-fit:contain;}
        .auth-left h1{font-size:28px;font-weight:800;color:#fff;margin:0 0 12px;line-height:1.2;}
        .auth-left p{font-size:15px;color:rgba(255,255,255,0.5);margin:0 0 36px;line-height:1.6;}
        .feature-list{list-style:none;padding:0;margin:0;text-align:left;}
        .feature-list li{display:flex;align-items:center;gap:10px;font-size:13.5px;color:rgba(255,255,255,0.6);padding:8px 0;}
        .feat-check{width:20px;height:20px;border-radius:50%;background:var(--bg-icon);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .feat-check svg{width:11px;height:11px;color:var(--icon-stroke);}

        /* ── Right panel: form ── */
        .auth-right{
            width:100%;max-width:480px;
            display:flex;flex-direction:column;align-items:center;justify-content:center;
            padding:48px 40px;
            background:var(--bg-body);
            position:relative;
            transition: background 0.3s;
        }

        .theme-toggle-btn {
            position: absolute; top: 20px; right: 20px;
            background: none; border: none; padding: 8px; border-radius: 10px;
            cursor: pointer; color: var(--text-muted); display:flex; align-items:center; gap:6px;
            transition: all 0.15s;
        }
        .theme-toggle-btn:hover { background: var(--bg-card); color: var(--text-main); }

        .auth-form-wrap{width:100%;max-width:380px;}
        .auth-right h2{font-size:22px;font-weight:700;color:var(--text-main);margin:0 0 6px;}
        .auth-right .subtitle{font-size:13.5px;color:var(--text-muted-dark);margin:0 0 28px;}

        /* Inputs */
        .field{margin-bottom:18px;}
        .field label{display:block;font-size:12.5px;font-weight:600;color:var(--text-muted);margin-bottom:6px;letter-spacing:.03em;}
        .field input{
            width:100%;background:var(--input-bg);
            border:1.5px solid var(--border-card);
            border-radius:13px;padding:13px 16px;
            color:var(--text-main);font-size:15px;font-family:'Inter',sans-serif;
            outline:none;transition:all 0.2s;
            -webkit-appearance:none;
        }
        .field input::placeholder { color: var(--text-muted-dark); }
        .field input:focus{border-color:var(--border-focus);box-shadow:0 0 0 3px rgba(59,130,246,0.15); background: var(--bg-body);}
        .field .pw-wrap{position:relative;}
        .field .pw-wrap input{padding-right:46px;}
        .field .eye-btn{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted-dark);padding:4px;display:flex;align-items:center;line-height:1;}
        .field-error{color:#ef4444;font-size:12px;margin-top:5px;}

        .btn-submit{
            width:100%;padding:14px;border-radius:14px;border:none;cursor:pointer;
            font-size:15px;font-weight:700;color:#fff;font-family:'Inter',sans-serif;
            background:var(--btn-bg);
            box-shadow:0 4px 20px var(--btn-shadow);
            transition:transform .12s,box-shadow .12s;
        }
        .btn-submit:hover{transform:translateY(-1px);box-shadow:0 8px 28px var(--btn-shadow);}
        .btn-submit:active{transform:scale(.97);}

        .auth-meta{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
        .check-label{display:flex;align-items:center;gap:7px;font-size:13px;color:var(--text-muted);cursor:pointer;}
        .check-label input{width:16px;height:16px;accent-color:#3b82f6;flex-shrink:0;}
        .auth-link{color:#3b82f6;font-size:13px;text-decoration:none;font-weight:500;}
        .auth-link:hover{color:#60a5fa;}
        .auth-alt{text-align:center;margin-top:22px;font-size:13px;color:var(--text-muted-dark);}
        .divider{display:flex;align-items:center;gap:12px;margin:20px 0;}
        .divider span{flex:1;height:1px;background:var(--divider);}
        .divider p{font-size:12px;color:var(--text-muted-dark);white-space:nowrap;margin:0;}
        .copyright{margin-top:28px;font-size:11.5px;color:var(--text-muted-dark);text-align:center;}

        .btn-alt {
            display:block;width:100%;padding:13px;border-radius:14px;
            border:1.5px solid var(--border-card);font-size:14px;font-weight:600;
            color:var(--btn-alt-text);text-align:center;text-decoration:none;
            background:var(--btn-alt-bg);transition:all .15s;
        }
        .btn-alt:hover {
            border-color: rgba(59,130,246,0.5); color: #3b82f6;
        }

        /* Flash messages */
        .flash-success{background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.25);border-radius:12px;padding:12px 14px;font-size:13px;color:#059669;margin-bottom:18px;}
        [data-theme="dark"] .flash-success { color: #34d399; }
        .flash-error{background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.2);border-radius:12px;padding:12px 14px;font-size:13px;color:#dc2626;margin-bottom:18px;}
        [data-theme="dark"] .flash-error { color: #f87171; }

        /* Mobile: hide left panel */
        @media (max-width:860px){
            .auth-left{display:none;}
            .auth-right{max-width:100%;padding:32px 20px;}
            body{justify-content:center;}
        }
        @media (max-width:460px){
            .auth-right{padding:24px 16px;}
        }
    </style>
</head>
<body>
    {{-- DEMO MODE BANNER --}}
    <div style="background: linear-gradient(to right, #3b82f6, #8b5cf6); color: white; text-align: center; padding: 6px; font-size: 11px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; z-index: 9999; position: absolute; top: 0; left: 0; right: 0;">
        Demo Mode: Sistem Absensi & HRD Management
    </div>

    {{-- Left branding panel --}}
    <div class="auth-left">
        <div class="auth-left-glow1"></div>
        <div class="auth-left-glow2"></div>
        <div class="auth-left-content">
            <div class="auth-logo">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
            </div>
            <h1>Sistem Absensi</h1>
            <p>{{ config('app.name') }}</p>
        </div>
    </div>

    {{-- Right form panel --}}
    <div class="auth-right">
        
        <button class="theme-toggle-btn" onclick="toggleTheme()" aria-label="Toggle Theme">
            <svg class="icon-sun" style="width:18px;height:18px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            <svg class="icon-moon" style="width:18px;height:18px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        </button>

        <div class="auth-form-wrap">

            {{-- Mobile logo --}}
            <div style="display:none;text-align:center;margin-bottom:24px;" id="mobileLogo">
                <div style="width:60px;margin:0 auto 12px;display:flex;justify-content:center;">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-width:100%;height:auto;object-fit:contain;">
                </div>
                <div style="font-size:16px;font-weight:700;color:var(--text-main);">{{ config('app.name') }}</div>
                <div style="font-size:12px;color:var(--text-muted-dark);margin-top:2px;">Sistem Absensi Karyawan</div>
            </div>

            <h2>Selamat Datang</h2>
            <p class="subtitle">Masuk ke akun karyawan Anda</p>

            {{-- Demo Credentials Box --}}
            <div style="background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.2); border-radius: 14px; padding: 16px; margin-bottom: 24px;">
                <div style="font-size: 11px; font-weight: 700; color: #3b82f6; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.05em;">Demo Access</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <div style="font-size: 10px; color: var(--text-muted-dark);">Admin Role</div>
                        <div style="font-size: 12px; color: var(--text-main); font-weight: 600;">admin@admin.com</div>
                    </div>
                    <div>
                        <div style="font-size: 10px; color: var(--text-muted-dark);">Employee Role</div>
                        <div style="font-size: 12px; color: var(--text-main); font-weight: 600;">test@example.com</div>
                    </div>
                </div>
                <div style="margin-top: 8px; border-top: 1px solid rgba(255,255,255,0.05); pt-8; font-size: 11px; color: var(--text-muted);">
                    Password: <span style="font-weight: 600; color: var(--text-main);">password</span>
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <a href="{{ route('guide') }}" style="display: flex; align-items: center; justify-content: center; gap: 8px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 12px; border-radius: 12px; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    📖 Baca Panduan Uji Coba (Bagi Klien)
                </a>
            </div>

            {{-- Flash status --}}
            @if(session('status'))
                <div class="flash-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label for="email">Email Karyawan</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           placeholder="nama@perusahaan.com">
                    @error('email')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="pw-wrap">
                        <input id="password" type="password" name="password"
                               required autocomplete="current-password"
                               placeholder="Masukkan password">
                        <button type="button" class="eye-btn" onclick="togglePw('password',this)" aria-label="Tampilkan password">
                            <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="auth-meta">
                    <label class="check-label">
                        <input type="checkbox" name="remember" id="remember_me">
                        Ingat saya
                    </label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-link">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-submit">Masuk</button>
            </form>

            @if(Route::has('register'))
            <div class="divider">
                <span></span><p>atau</p><span></span>
            </div>
            <a href="{{ route('register') }}" class="btn-alt">
                Daftar Akun Baru
            </a>
            @endif

            <div class="copyright">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.
            </div>
        </div>
    </div>
</body>
<script>
function togglePw(id, btn) {
    var inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.style.color = inp.type === 'text' ? '#3b82f6' : 'var(--text-muted-dark)';
}
// Show mobile logo on small screens
if (window.innerWidth <= 860) {
    document.getElementById('mobileLogo').style.display = 'block';
}
window.addEventListener('resize', function() {
    document.getElementById('mobileLogo').style.display = window.innerWidth <= 860 ? 'block' : 'none';
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
</html>
