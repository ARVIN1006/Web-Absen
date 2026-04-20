<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0f1117">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Daftar Akun – {{ config('app.name') }}</title>
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
            --border-focus: #10b981;
            --text-main: #ffffff;
            --text-muted: #9ca3af;
            --text-muted-dark: #6b7280;
            --btn-bg: linear-gradient(135deg,#10b981,#059669);
            --btn-shadow: rgba(16,185,129,0.35);
            --divider: rgba(255,255,255,0.08);
            --input-bg: rgba(255,255,255,0.05);
            --notice-bg: rgba(250,204,21,0.08);
            --notice-border: rgba(250,204,21,0.2);
            --pw-track: rgba(255,255,255,0.08);
            --bg-glass: rgba(255,255,255,0.05);
            --border-glass: rgba(255,255,255,0.08);
        }
        [data-theme="light"] {
            --bg-body: #f3f4f6;
            --bg-card: #ffffff;
            --border-card: #e5e7eb;
            --border-focus: #10b981;
            --text-main: #111827;
            --text-muted: #4b5563;
            --text-muted-dark: #6b7280;
            --btn-bg: linear-gradient(135deg,#10b981,#059669);
            --btn-shadow: rgba(16,185,129,0.25);
            --divider: #d1d5db;
            --input-bg: #f9fafb;
            --notice-bg: #fffbeb;
            --notice-border: #fde68a;
            --pw-track: #e5e7eb;
            --bg-glass: #ffffff;
            --border-glass: #e5e7eb;
        }

        *,*::before,*::after{box-sizing:border-box;-webkit-tap-highlight-color:transparent;}
        body{margin:0;font-family:'Inter',sans-serif;background:var(--bg-body);min-height:100vh;display:flex;align-items:stretch; transition: background 0.3s;}

        /* Left branding */
        .auth-left{
            width:40%; max-width:480px; display:flex;flex-direction:column;align-items:center;justify-content:center;
            padding:48px 40px;position:relative;overflow:hidden;
            background:linear-gradient(145deg,#0d1b2a 0%,#0f2744 50%,#071c38 100%);
            border-right: 1px solid var(--divider);
        }
        .auth-left::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233b82f6' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");}
        .glow1{position:absolute;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(59,130,246,0.22),transparent);top:-80px;left:-80px;pointer-events:none;}
        .glow2{position:absolute;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(16,185,129,0.16),transparent);bottom:-60px;right:-60px;pointer-events:none;}
        .left-content{position:relative;z-index:1;text-align:center;max-width:380px;}
        .logo-box{width:90px;height:auto;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;}
        .logo-box img{max-width:100%;height:auto;object-fit:contain;}
        .left-content h1{font-size:26px;font-weight:800;color:#fff;margin:0 0 12px;line-height:1.2;}
        .left-content p{font-size:14px;color:rgba(255,255,255,0.5);margin:0;line-height:1.6;}

        /* Notice box */
        .notice{margin-top:32px;padding:16px 18px;border-radius:16px;background:var(--notice-bg);border:1px solid var(--notice-border);text-align:left;}
        .notice-title{font-size:12px;font-weight:700;color:#f59e0b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;}
        .notice p{font-size:12.5px;color:rgba(255,255,255,0.5);margin:0;line-height:1.6;}
        [data-theme="light"] .notice p { color: #92400e; }

        /* Right form */
        .auth-right{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px;background:var(--bg-body); position:relative; overflow-y:auto; transition: background 0.3s;}
        
        .theme-toggle-btn {
            position: absolute; top: 20px; right: 20px;
            background: none; border: none; padding: 8px; border-radius: 10px;
            cursor: pointer; color: var(--text-muted); display:flex; align-items:center; gap:6px;
            transition: all 0.15s;
        }
        .theme-toggle-btn:hover { background: var(--bg-card); color: var(--text-main); }

        .auth-form-wrap{width:100%;max-width:700px;margin:auto;}
        .auth-right h2{font-size:22px;font-weight:700;color:var(--text-main);margin:0 0 6px;}
        .auth-right .subtitle{font-size:13.5px;color:var(--text-muted-dark);margin:0 0 28px;}

        .split-form { display:flex; gap:32px; align-items:flex-start; }
        .form-col { flex:1; min-width:280px; }
        .cam-col { width:300px; flex-shrink:0; }

        /* Inputs */
        .field{margin-bottom:16px;}
        .field label{display:block;font-size:12.5px;font-weight:600;color:var(--text-muted);margin-bottom:6px;letter-spacing:.03em;}
        .field input{width:100%;background:var(--input-bg);border:1.5px solid var(--border-card);border-radius:12px;padding:12px 14px;color:var(--text-main);font-size:14.5px;font-family:'Inter',sans-serif;outline:none;transition:all .2s;}
        .field input::placeholder { color: var(--text-muted-dark); }
        .field input:focus{border-color:var(--border-focus);box-shadow:0 0 0 3px rgba(16,185,129,0.15); background: var(--bg-body);}
        .field select{
            width:100%;background:var(--input-bg);border:1.5px solid var(--border-card);border-radius:12px;padding:12px 14px;color:var(--text-main);font-size:14.5px;font-family:'Inter',sans-serif;outline:none;transition:all .2s;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 16px;
        }
        select option {
            background-color: #1a1d27;
            color: #ffffff;
        }
        [data-theme="light"] select option {
            background-color: #ffffff;
            color: #111827;
        }
        .field select:focus{border-color:var(--border-focus);box-shadow:0 0 0 3px rgba(16,185,129,0.15); background: var(--bg-body);}
        .field .pw-wrap{position:relative;}
        .field .pw-wrap input{padding-right:46px;}
        .field .eye-btn{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted-dark);padding:4px;display:flex;align-items:center;}
        .field-hint{font-size:11px;color:var(--text-muted-dark);margin-top:4px;}
        .field-error{color:#ef4444;font-size:12px;margin-top:5px;}

        /* Password strength */
        .pw-strength{height:3px;border-radius:2px;margin-top:6px;background:var(--pw-track);overflow:hidden;}
        .pw-strength-bar{height:100%;border-radius:2px;width:0;transition:width .3s,background .3s;}
        .pw-strength-label{font-size:11px;color:var(--text-muted-dark);margin-top:4px;}

        /* Camera Box */
        .cam-box { background:var(--bg-glass); border:1px solid var(--border-glass); border-radius:18px; padding:16px; transition:all 0.3s; }
        .cam-inner { position:relative; background:#000; border-radius:12px; overflow:hidden; aspect-ratio:4/3; margin-bottom:12px;}
        .cam-inner video { width:100%; height:100%; object-fit:cover; transform:scaleX(-1); display:block; }
        .cam-inner canvas { position:absolute; inset:0; width:100%; height:100%; transform:scaleX(-1); }
        .cam-overlay { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; }
        .face-frame { width:55%; padding-bottom:70%; border:2px dashed rgba(255,255,255,0.4); border-radius:50%; box-shadow:0 0 0 999px rgba(0,0,0,0.5); transition:border-color .3s; }
        #camSnapshot { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transform:scaleX(-1); display:none; z-index:20; }
        
        .btn-capture {
            width:100%; padding:12px; border-radius:10px; border:none; cursor:pointer;
            font-size:13.5px; font-weight:600; color:#fff;
            background:linear-gradient(135deg,#3b82f6,#2563eb); transition:transform .12s;
            display:flex; align-items:center; justify-content:center; gap:8px;
        }
        .btn-capture:disabled { opacity:.5; cursor:not-allowed; }
        .btn-capture:not(:disabled):active { transform:scale(0.96); }
        
        .btn-submit{width:100%;padding:14px;border-radius:14px;border:none;cursor:pointer;font-size:15px;font-weight:700;color:#fff;font-family:'Inter',sans-serif;background:var(--btn-bg);box-shadow:0 4px 20px var(--btn-shadow);transition:transform .12s,box-shadow .12s;margin-top:10px;}
        .btn-submit:hover{transform:translateY(-1px);box-shadow:0 8px 28px rgba(16,185,129,0.45);}
        .btn-submit:active{transform:scale(.97);}
        .btn-submit:disabled{opacity:.5;cursor:not-allowed;transform:none;}

        .divider{display:flex;align-items:center;gap:12px;margin:24px 0;}
        .divider span{flex:1;height:1px;background:var(--divider);}
        .divider p{font-size:12px;color:var(--text-muted-dark);white-space:nowrap;margin:0;}

        .auth-link{color:#10b981;font-size:13px;font-weight:500;text-decoration:none;}
        [data-theme="dark"] .auth-link { color: #34d399; }
        .auth-link:hover{color:#059669;}
        [data-theme="dark"] .auth-link:hover { color: #6ee7b7; }
        .auth-alt{text-align:center;font-size:13px;color:var(--text-muted-dark);}
        .copyright{margin-top:24px;font-size:11.5px;color:var(--text-muted-dark);text-align:center;}

        @media (max-width:960px){
            .auth-left{display:none;}
            body{justify-content:center;}
        }
        @media (max-width:767px){
            .split-form { flex-direction:column-reverse; gap:24px; }
            .cam-col { width:100%; }
            .auth-right{padding:24px 16px;}
            .auth-form-wrap{max-width:400px;}
        }
    </style>
</head>
<body>

    {{-- Branding panel --}}
    <div class="auth-left">
        <div class="glow1"></div>
        <div class="glow2"></div>
        <div class="left-content">
            <div class="logo-box">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
            </div>
            <h1>Daftar Akun<br>Karyawan Baru</h1>
            <p>Buat akun untuk mulai menggunakan sistem absensi digital {{ config('app.name') }}.</p>

            <div class="notice">
                <div class="notice-title">&#128274; Informasi Penting</div>
                <p>Pendaftaran akun hanya diperuntukkan bagi karyawan {{ config('app.name') }}. Sistem memerlukan foto wajah Anda sebagai standar validasi biometrik absen harian.</p>
            </div>
        </div>
    </div>

    {{-- Form panel --}}
    <div class="auth-right">

        <button class="theme-toggle-btn" onclick="toggleTheme()" aria-label="Toggle Theme">
            <svg class="icon-sun" style="width:18px;height:18px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            <svg class="icon-moon" style="width:18px;height:18px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        </button>

        <div class="auth-form-wrap">

            {{-- Mobile logo --}}
            <div style="display:none;text-align:center;margin-bottom:22px;" id="mobileLogo">
                <div style="width:60px;margin:0 auto 10px;display:flex;justify-content:center;">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-width:100%;height:auto;object-fit:contain;">
                </div>
                <div style="font-size:15px;font-weight:700;color:var(--text-main);">{{ config('app.name') }}</div>
                <div style="font-size:11px;color:var(--text-muted-dark);margin-top:2px;">Pendaftaran dengan Wajah</div>
            </div>

            <h2>Buat Akun Baru</h2>
            <p class="subtitle">Isi biodata dan daftarkan wajah Anda</p>

            <form method="POST" action="{{ route('register') }}" id="regForm" onsubmit="return validateForm()">
                @csrf
                <input type="hidden" name="face_data" id="face_data">

                <div class="split-form">
                    
                    {{-- KIRI: FORM --}}
                    <div class="form-col">
                        <div class="field">
                            <label for="name">Nama Lengkap</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required placeholder="Nama sesuai KTP">
                            @error('name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="field">
                            <label for="position">Jabatan / Posisi</label>
                            <select id="position" name="position" required>
                                <option value="" disabled selected>Pilih Jabatan</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos }}" {{ old('position') == $pos ? 'selected' : '' }}>
                                        {{ $pos }}
                                    </option>
                                @endforeach
                            </select>
                            @error('position')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="field">
                            <label for="department_id">Departemen</label>
                            <select id="department_id" name="department_id" required>
                                <option value="" disabled selected>Pilih Departemen</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="field">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="email@perusahaan.com">
                            @error('email')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="field">
                            <label for="password">Password</label>
                            <div class="pw-wrap">
                                <input id="password" type="password" name="password" required placeholder="Minimal 8 karakter" oninput="checkStrength(this.value)">
                                <button type="button" class="eye-btn" onclick="togglePw('password',this)">
                                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            <div class="pw-strength"><div id="pwBar" class="pw-strength-bar"></div></div>
                            <div id="pwLabel" class="pw-strength-label"></div>
                            @error('password')<div class="field-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="field">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <div class="pw-wrap">
                                <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Ulangi password" oninput="checkMatch()">
                                <button type="button" class="eye-btn" onclick="togglePw('password_confirmation',this)">
                                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            <div id="matchMsg" class="field-hint"></div>
                        </div>
                    </div>

                    {{-- KANAN: WEBCAM --}}
                    <div class="cam-col">
                        <div class="cam-box">
                            <div style="font-size:12.5px;font-weight:600;color:var(--text-main);margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                                <svg style="width:16px;height:16px;color:#3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Verifikasi Wajah Asli
                            </div>
                            <div class="cam-inner">
                                <video id="camVideo" autoplay muted playsinline></video>
                                <canvas id="camCanvas"></canvas>
                                <img id="camSnapshot" src="" alt="Snapshot">
                                <div id="camLoading" class="cam-overlay" style="background:rgba(0,0,0,0.8);z-index:30;">
                                    <div style="text-align:center;">
                                        <div style="width:30px;height:30px;border:3px solid #3b82f6;border-top-color:transparent;border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 8px;"></div>
                                        <div style="font-size:11px;color:#9ca3af;">Memuat Kamera AI...</div>
                                    </div>
                                </div>
                                <div class="cam-overlay" id="frameOverlay" style="pointer-events:none;z-index:10;">
                                    <div class="face-frame" id="faceFrame"></div>
                                </div>
                            </div>
                            <p id="faceHelp" style="font-size:11px;color:var(--text-muted-dark);text-align:center;margin:0 0 12px;min-height:30px;">Posisikan wajah di dalam lingkaran dan tunggu terdeteksi.</p>
                            
                            <button type="button" class="btn-capture" id="btnCapture" disabled onclick="captureFace()">
                                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Ambil Foto Referensi
                            </button>
                            <button type="button" class="btn-capture" id="btnRetake" style="display:none;background:var(--btn-alt-bg);color:var(--text-main);border:1px solid var(--border-card);" onclick="retakeFace()">
                                Ulangi Foto
                            </button>
                        </div>
                    </div>
                </div>

                <div style="margin-top:20px;">
                    <button type="submit" class="btn-submit" id="submitBtn">Daftar Sekarang</button>
                    <div id="submitError" style="color:#ef4444;font-size:12.5px;text-align:center;margin-top:8px;font-weight:600;display:none;"></div>
                </div>
            </form>

            <div class="divider"><span></span><p>sudah punya akun?</p><span></span></div>
            <div class="auth-alt">
                <a href="{{ route('login') }}" class="auth-link">Masuk di sini &rarr;</a>
            </div>

            <div class="copyright">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.
            </div>
        </div>
    </div>

</body>
<style> @keyframes spin { to { transform:rotate(360deg); } } </style>
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
// Mobile logo visibility
function updateLogo() { document.getElementById('mobileLogo').style.display = window.innerWidth <= 960 ? 'block' : 'none'; }
updateLogo(); window.addEventListener('resize', updateLogo);

// Form and Password Logic
function togglePw(id, btn) {
    var inp = document.getElementById(id); inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.style.color = inp.type === 'text' ? '#10b981' : 'var(--text-muted-dark)';
}
function checkStrength(val) {
    var bar = document.getElementById('pwBar'); var label = document.getElementById('pwLabel'); var score = 0;
    if (val.length >= 8) score++; if (/[A-Z]/.test(val)) score++; if (/[0-9]/.test(val)) score++; if (/[^A-Za-z0-9]/.test(val)) score++;
    var map = [ {w:'0%',bg:'transparent',t:''}, {w:'25%',bg:'#ef4444',t:'Lemah'}, {w:'50%',bg:'#f97316',t:'Cukup'}, {w:'75%',bg:'#eab308',t:'Baik'}, {w:'100%',bg:'#10b981',t:'Kuat'} ];
    bar.style.width = map[score].w; bar.style.background = map[score].bg; label.textContent = map[score].t; label.style.color = map[score].bg;
    checkMatch();
}
function checkMatch() {
    var pw = document.getElementById('password').value, conf = document.getElementById('password_confirmation').value, msg = document.getElementById('matchMsg');
    if (!conf) { msg.textContent = ''; return; }
    if (pw === conf) { msg.innerHTML = '&#10004; Password cocok'; msg.style.color = '#10b981'; } 
    else { msg.innerHTML = '&#10008; Password tidak cocok'; msg.style.color = '#ef4444'; }
}

// Theme Logic
function updateThemeIcons() {
    const isLight = document.documentElement.getAttribute('data-theme') === 'light';
    document.querySelectorAll('.icon-sun').forEach(el => el.style.display = isLight ? 'none' : 'block');
    document.querySelectorAll('.icon-moon').forEach(el => el.style.display = isLight ? 'block' : 'none');
}
updateThemeIcons();
function toggleTheme() {
    const html = document.documentElement; const isLight = html.getAttribute('data-theme') === 'light'; const newTheme = isLight ? 'dark' : 'light';
    html.setAttribute('data-theme', newTheme); localStorage.setItem('theme', newTheme);
    document.querySelector('meta[name="theme-color"]').setAttribute('content', newTheme === 'light' ? '#f3f4f6' : '#0f1117');
    updateThemeIcons();
}

// Face Detection Logic
const video = document.getElementById('camVideo');
const canvas = document.getElementById('camCanvas');
const loading = document.getElementById('camLoading');
const faceFrame = document.getElementById('faceFrame');
const btnCapture = document.getElementById('btnCapture');
const btnRetake = document.getElementById('btnRetake');
const snapshot = document.getElementById('camSnapshot');
const frameOverlay = document.getElementById('frameOverlay');
const faceHelp = document.getElementById('faceHelp');
const faceDataInp = document.getElementById('face_data');
let faceInterval; let isFaceDetected = false;

async function loadModels() {
    const BASE = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights';
    try {
        await faceapi.nets.tinyFaceDetector.loadFromUri(BASE);
        startCamera();
    } catch(e) {
        loading.innerHTML = '<div style="font-size:12px;color:#ef4444;text-align:center;padding:10px;">Gagal memuat AI. Pastikan internet aktif.</div>';
    }
}
function startCamera() {
    if(!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        loading.innerHTML = `
            <div style="text-align:center;padding:16px;">
                <p style="color:#ef4444;font-size:12px;font-weight:700;margin-bottom:4px;">&#10060; Browser Tidak Mendukung</p>
                <p style="color:#9ca3af;font-size:11px;line-height:1.4;">Kamera butuh <b>HTTPS</b> atau <b>localhost</b>.</p>
            </div>`;
        return;
    }
    navigator.mediaDevices.getUserMedia({ video:{ facingMode:'user' } })
    .then(stream => {
        video.srcObject = stream;
        video.onloadedmetadata = () => {
            canvas.width = video.videoWidth; canvas.height = video.videoHeight;
            loading.style.display = 'none';
            detectFaceLoop();
        };
    }).catch(() => {
        loading.innerHTML = '<div style="font-size:12px;color:#ef4444;text-align:center;padding:10px;">Izin kamera ditolak. Izinkan di browser Anda.</div>';
    });
}
async function detectFaceLoop() {
    faceInterval = setInterval(async () => {
        const det = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold:0.5 }));
        const ctx = canvas.getContext('2d'); ctx.clearRect(0,0,canvas.width,canvas.height);
        
        if(det.length > 0) {
            isFaceDetected = true;
            faceFrame.style.borderColor = '#10b981';
            btnCapture.disabled = false;
            faceHelp.innerHTML = '<span style="color:#10b981;font-weight:600;">Wajah terdeteksi! Silakan ambil foto.</span>';
        } else {
            isFaceDetected = false;
            faceFrame.style.borderColor = 'rgba(255,255,255,0.4)';
            btnCapture.disabled = true;
            faceHelp.innerHTML = 'Posisikan wajah di dalam lingkaran dan tunggu terdeteksi.';
        }
    }, 500);
}

function captureFace() {
    if(!isFaceDetected) return;
    clearInterval(faceInterval);
    const ctx = canvas.getContext('2d');
    ctx.translate(canvas.width, 0); ctx.scale(-1, 1);
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    const dataURL = canvas.toDataURL('image/jpeg', 0.85);
    
    // Save to hidden input
    faceDataInp.value = dataURL;
    
    // Show on UI
    snapshot.src = dataURL;
    snapshot.style.display = 'block';
    video.style.display = 'none';
    frameOverlay.style.display = 'none';
    btnCapture.style.display = 'none';
    btnRetake.style.display = 'flex';
    faceHelp.innerHTML = '<span style="color:#3b82f6;font-weight:600;">Foto referensi wajah berhasil disimpan.</span>';
}

function retakeFace() {
    faceDataInp.value = '';
    snapshot.style.display = 'none';
    video.style.display = 'block';
    frameOverlay.style.display = 'flex';
    btnRetake.style.display = 'none';
    btnCapture.style.display = 'flex';
    btnCapture.disabled = true;
    faceHelp.innerHTML = 'Menganalisis ulang wajah...';
    detectFaceLoop();
}

function validateForm() {
    const err = document.getElementById('submitError');
    if (!faceDataInp.value) {
        err.innerHTML = '&#10008; Anda belum mengambil foto referensi wajah!';
        err.style.display = 'block';
        setTimeout(() => err.style.display='none', 4000);
        return false;
    }
    return true;
}

// Init
window.onload = loadModels;
</script>
</html>
