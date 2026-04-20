@extends('layouts.app')

@section('content')
<style>
    :root {
        --accent-primary: #3b82f6;
        --accent-secondary: #10b981;
        --accent-purple: #8b5cf6;
        --card-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3);
    }

    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding-bottom: 40px;
    }

    /* ─── Hero Section ─── */
    .hero-banner {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(16, 185, 129, 0.1));
        border: 1px solid var(--border-glass);
        border-radius: 32px;
        padding: 40px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }
    .hero-banner-glow {
        position: absolute; right: -50px; top: -50px; width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.2), transparent 70%);
        pointer-events: none;
    }
    .hero-content { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; gap: 24px; flex-wrap: wrap; }
    .hero-text h1 { font-size: 32px; font-weight: 800; margin: 0; background: linear-gradient(to right, var(--text-main), #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .hero-text p { color: var(--text-muted); margin-top: 8px; font-size: 15px; }

    .btn-primary-gradient {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 14px 28px; border-radius: 18px; font-weight: 700; color: white;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.5);
        text-decoration: none; transition: all 0.3s;
    }
    .btn-primary-gradient:hover { transform: translateY(-3px); box-shadow: 0 15px 30px -5px rgba(59, 130, 246, 0.6); }

    /* ─── Bento Grid ─── */
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    .bento-item {
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 24px;
        padding: 24px;
        transition: all 0.3s;
    }
    .bento-item:hover { transform: translateY(-5px); border-color: rgba(59, 130, 246, 0.3); }

    .col-span-2 { grid-column: span 2; }
    .row-span-2 { grid-row: span 2; }

    /* ─── Stats ─── */
    .stat-box { display: flex; flex-direction: column; gap: 8px; }
    .stat-label { font-size: 11px; font-weight: 700; color: var(--text-muted-dark); text-transform: uppercase; letter-spacing: 0.1em; }
    .stat-value { font-size: 28px; font-weight: 800; color: var(--text-main); }
    .stat-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; }

    /* ─── Announcement Card ─── */
    .ann-card {
        padding: 16px; border-radius: 20px; background: rgba(255,255,255,0.02);
        border: 1px solid var(--border-glass); margin-bottom: 12px;
        transition: all 0.2s;
    }
    .ann-card:hover { background: rgba(59, 130, 246, 0.05); transform: translateX(4px); }

    /* ─── History Rows ─── */
    .hist-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px; border-radius: 16px; background: rgba(255,255,255,0.02);
        margin-bottom: 10px; border: 1px solid transparent; transition: all 0.2s;
    }
    .hist-item:hover { border-color: var(--border-glass); background: rgba(255,255,255,0.04); }

    @media (max-width: 992px) {
        .bento-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .bento-grid { grid-template-columns: 1fr; }
        .col-span-2 { grid-column: span 1; }
        .hero-banner { padding: 30px 24px; text-align: center; }
        .hero-content { flex-direction: column; }
    }
</style>

<div class="dashboard-container">
    {{-- WELCOME HERO --}}
    <div class="hero-banner">
        <div class="hero-banner-glow"></div>
        <div class="hero-content">
            <div class="hero-text">
                <p style="text-transform: uppercase; letter-spacing: 0.2em; font-weight: 700; font-size: 11px; color: #3b82f6;">Personal Dashboard</p>
                <h1>Halo, {{ Auth::user()->name }}! 👋</h1>
                <p>{{ now()->translatedFormat('l, d F Y') }} &bull; <span id="realtime-clock">{{ now()->format('H:i') }}</span> WIB</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="{{ route('attendance.index') }}" class="btn-primary-gradient">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Presensi Sekarang
                </a>
            </div>
        </div>
    </div>

    @php
        $todayIn    = $attendances->where('type','in')->first();
        $todayOut   = $attendances->where('type','out')->first();
        $monthCount = \App\Models\Attendance::where('user_id',auth()->id())
                        ->where('type','in')
                        ->whereMonth('created_at',now()->month)
                        ->count();
    @endphp

    <div class="bento-grid">
        {{-- STATS IN --}}
        <div class="bento-item">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            </div>
            <div class="stat-box">
                <span class="stat-label">Absen Masuk</span>
                <div class="stat-value">{{ $todayIn ? \Carbon\Carbon::parse($todayIn->created_at)->format('H:i') : '--:--' }}</div>
                <div style="font-size: 12px; color: {{ $todayIn ? '#10b981' : 'var(--text-muted)' }}; font-weight: 600;">
                    {{ $todayIn ? '✓ Sudah Absen' : '• Belum Absen' }}
                </div>
            </div>
        </div>

        {{-- STATS OUT --}}
        <div class="bento-item">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </div>
            <div class="stat-box">
                <span class="stat-label">Absen Pulang</span>
                <div class="stat-value">{{ $todayOut ? \Carbon\Carbon::parse($todayOut->created_at)->format('H:i') : '--:--' }}</div>
                <div style="font-size: 12px; color: {{ $todayOut ? '#10b981' : 'var(--text-muted)' }}; font-weight: 600;">
                    {{ $todayOut ? '✓ Sudah Pulang' : '• Belum Pulang' }}
                </div>
            </div>
        </div>

        {{-- STATS MONTH --}}
        <div class="bento-item">
            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div class="stat-box">
                <span class="stat-label">Kehadiran Bulan Ini</span>
                <div class="stat-value">{{ $monthCount }} Hari</div>
                <div style="font-size: 12px; color: var(--text-muted); font-weight: 600;">
                    Periode {{ now()->translatedFormat('F Y') }}
                </div>
            </div>
        </div>

        {{-- ANNOUNCEMENTS --}}
        <div class="bento-item col-span-2">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 700;">Informasi Terbaru</h3>
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            @if($activeAnnouncements->isEmpty())
                <div style="text-align: center; padding: 40px 0;">
                    <p style="color: var(--text-muted); font-size: 14px;">Belum ada pengumuman hari ini.</p>
                </div>
            @else
                @foreach($activeAnnouncements as $ann)
                <div class="ann-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <span style="font-size: 11px; font-weight: 700; color: #3b82f6; text-transform: uppercase;">{{ $ann->created_at->translatedFormat('d M Y') }}</span>
                    </div>
                    <h4 style="margin: 0 0 8px; font-size: 15px; font-weight: 700;">{{ $ann->title }}</h4>
                    <p style="margin: 0; font-size: 13px; color: var(--text-muted); line-height: 1.6;">{{ Str::limit($ann->content, 140) }}</p>
                </div>
                @endforeach
            @endif
        </div>

        {{-- HISTORY --}}
        <div class="bento-item">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 700;">Riwayat Hari Ini</h3>
                <a href="{{ route('attendance.index') }}" style="font-size: 12px; color: #3b82f6; text-decoration: none; font-weight: 600;">Lihat Semua</a>
            </div>
            @if($todayAttendance->isEmpty())
                <div style="text-align: center; padding: 40px 0;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: var(--hover-bg); display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p style="color: var(--text-muted); font-size: 13px;">Belum ada riwayat presensi.</p>
                </div>
            @else
                @foreach($todayAttendance as $att)
                <div class="hist-item">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: {{ $att->type == 'in' ? 'rgba(59, 130, 246, 0.1)' : 'rgba(245, 158, 11, 0.1)' }}; display: flex; align-items: center; justify-content: center;">
                            <svg width="16" height="16" style="color: {{ $att->type == 'in' ? '#3b82f6' : '#f59e0b' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($att->type == 'in')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 8l4 4m0 0l-4 4m4-4H3"/>
                                @endif
                            </svg>
                        </div>
                        <div>
                            <div style="font-weight: 700; font-size: 13px;">{{ $att->type == 'in' ? 'Masuk' : 'Pulang' }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">{{ $att->created_at->format('H:i') }} WIB</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $att->status == 'valid' ? '#10b981' : '#ef4444' }};"></span>
                        <span style="font-size: 11px; font-weight: 700; color: {{ $att->status == 'valid' ? '#10b981' : '#ef4444' }};">{{ strtoupper($att->status) }}</span>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
        const clockEl = document.getElementById('realtime-clock');
        if (clockEl) clockEl.textContent = timeStr;
    }
    setInterval(updateClock, 1000);
</script>
@endsection
