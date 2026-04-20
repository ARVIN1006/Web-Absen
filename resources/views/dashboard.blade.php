@extends('layouts.app')

@section('content')
<style>
    .db-wrap { max-width:1100px; margin:0 auto; padding:28px 20px 20px; }
    .db-banner {
        border-radius:20px; padding:24px 28px; margin-bottom:20px;
        position:relative; overflow:hidden;
        background:linear-gradient(135deg,rgba(59,130,246,0.15),rgba(16,185,129,0.12));
        border:1px solid rgba(59,130,246,0.22);
    }
    .db-banner-glow {
        position:absolute;right:0;top:0;width:260px;height:260px;border-radius:50%;
        background:radial-gradient(circle,rgba(59,130,246,0.2),transparent);
        transform:translate(35%,-35%);pointer-events:none;
    }
    .db-banner-inner { position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap; }
    .db-banner h1 { font-size:22px;font-weight:700;color:var(--text-main);margin:4px 0 0; }
    .db-banner p  { font-size:12px;color:var(--text-muted);margin:0; }
    .btn-absen {
        display:inline-flex;align-items:center;gap:8px;
        padding:11px 20px;border-radius:14px;font-weight:600;font-size:13.5px;color:#fff;
        text-decoration:none;white-space:nowrap;
        background:linear-gradient(135deg,#3b82f6,#1d4ed8);
        box-shadow:0 4px 18px rgba(59,130,246,0.35);
        transition:transform .15s,box-shadow .15s;
    }
    .btn-absen:hover { transform:translateY(-2px);box-shadow:0 8px 28px rgba(59,130,246,0.45); }
    .btn-absen svg { width:16px;height:16px;flex-shrink:0; }

    /* Stats */
    .db-stats { display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px; }
    .db-card { border-radius:16px;padding:18px 16px;background:var(--bg-glass);border:1px solid var(--border-glass); transition: all 0.3s; }
    .db-card-head { display:flex;align-items:center;justify-content:space-between;margin-bottom:12px; }
    .db-card-label { font-size:11px;font-weight:600;color:var(--text-muted-dark);text-transform:uppercase;letter-spacing:.06em; }
    .db-card-icon { width:32px;height:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
    .db-card-val { font-size:26px;font-weight:700;color:var(--text-main);line-height:1; }
    .db-card-sub { font-size:11px;color:var(--text-muted-dark);margin-top:4px; }

    /* History */
    .db-hist { border-radius:18px;padding:22px;background:var(--bg-glass);border:1px solid var(--border-glass); transition: all 0.3s; }
    .db-hist-head { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px; }
    .db-hist-head h2 { font-size:15px;font-weight:600;color:var(--text-main);margin:0; }
    .db-hist-head a  { font-size:12.5px;color:#3b82f6;text-decoration:none; }
    .hist-row { display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-radius:13px;background:var(--hover-bg);border:1px solid var(--border-line);margin-bottom:8px; transition: all 0.3s;}
    .hist-row:last-child { margin-bottom:0; }
    .hist-left { display:flex;align-items:center;gap:12px;min-width:0; }
    .hist-thumb { width:40px;height:40px;border-radius:11px;object-fit:cover;border:1.5px solid var(--border-line);flex-shrink:0; }
    .hist-thumb-ph { width:40px;height:40px;border-radius:11px;background:var(--border-line);display:flex;align-items:center;justify-content:center;flex-shrink:0; }
    .hist-name { font-size:13.5px;font-weight:600;color:var(--text-main);white-space:nowrap; }
    .hist-date { font-size:11px;color:var(--text-muted-dark);margin-top:2px;white-space:nowrap; }
    .hist-right { display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;margin-left:12px; }
    .hist-time { font-size:14px;font-weight:700;color:var(--text-main); }
    .badge { font-size:10.5px;font-weight:600;padding:2px 9px;border-radius:20px; }
    .badge-sah  { background:rgba(16,185,129,0.15);color:#059669; }
    [data-theme="dark"] .badge-sah { color: #34d399; }
    .badge-fail { background:rgba(239,68,68,0.15);color:#dc2626; }
    [data-theme="dark"] .badge-fail { color: #f87171; }
    
    .empty-box { text-align:center;padding:40px 16px; }
    .empty-icon { width:52px;height:52px;border-radius:16px;background:var(--hover-bg);display:flex;align-items:center;justify-content:center;margin:0 auto 12px; }

    @media (max-width:767px) {
        .db-wrap { padding:14px 12px 12px; }
        .db-banner { padding:16px; margin-bottom:14px; border-radius:16px; }
        .db-banner h1 { font-size:18px; }
        .db-stats { grid-template-columns:1fr 1fr; gap:10px; margin-bottom:14px; }
        .db-stats .full { grid-column:span 2; }
        .db-card { padding:14px 12px; }
        .db-card-val { font-size:22px; }
        .db-hist { padding:14px 12px; border-radius:16px; }
    }
</style>

<div class="db-wrap">

    {{-- Welcome Banner --}}
    <div class="db-banner">
        <div class="db-banner-glow"></div>
        <div class="db-banner-inner">
            <div>
                <p>Selamat Datang</p>
                <h1>{{ Auth::user()->name }}</h1>
                <p style="margin-top:6px;">{{ now()->translatedFormat('dddd, d F Y') }} &bull; {{ now()->format('H:i') }} WIB</p>
            </div>
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <a href="{{ route('employee.leave-requests.index') }}" class="btn-absen" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9); box-shadow:0 4px 18px rgba(139,92,246,0.35);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Cuti & Izin
                </a>
                <a href="{{ route('attendance.index') }}" class="btn-absen">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Absen Sekarang
                </a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    @php
        $todayIn    = $attendances->where('type','in')->first();
        $todayOut   = $attendances->where('type','out')->first();
        $monthCount = \App\Models\Attendance::where('user_id',auth()->id())
                        ->where('type','in')
                        ->whereMonth('created_at',now()->month)
                        ->count();
    @endphp
    <div class="db-stats">
        <div class="db-card">
            <div class="db-card-head">
                <span class="db-card-label">Masuk</span>
                <div class="db-card-icon" style="background:rgba(59,130,246,0.15);">
                    <svg style="width:15px;height:15px;color:#3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                </div>
            </div>
            <div class="db-card-val">{{ $todayIn ? \Carbon\Carbon::parse($todayIn->created_at)->format('H:i') : '--:--' }}</div>
            <div class="db-card-sub">{{ $todayIn ? 'Tercatat ✓' : 'Belum absen' }}</div>
        </div>
        <div class="db-card">
            <div class="db-card-head">
                <span class="db-card-label">Pulang</span>
                <div class="db-card-icon" style="background:rgba(16,185,129,0.15);">
                    <svg style="width:15px;height:15px;color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </div>
            </div>
            <div class="db-card-val">{{ $todayOut ? \Carbon\Carbon::parse($todayOut->created_at)->format('H:i') : '--:--' }}</div>
            <div class="db-card-sub">{{ $todayOut ? 'Tercatat ✓' : 'Belum absen' }}</div>
        </div>
        <div class="db-card full">
            <div class="db-card-head">
                <span class="db-card-label">Hadir Bulan Ini</span>
                <div class="db-card-icon" style="background:rgba(139,92,246,0.15);">
                    <svg style="width:15px;height:15px;color:#8b5cf6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="db-card-val">{{ $monthCount }} <span style="font-size:14px;font-weight:400;color:var(--text-muted);">hari</span></div>
            <div class="db-card-sub">{{ now()->locale('id')->isoFormat('MMMM YYYY') }}</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1.2fr; gap: 20px;">
        {{-- Announcements --}}
        <div class="db-hist">
            <div class="db-hist-head">
                <h2>Pusat Informasi</h2>
            </div>
            @if($activeAnnouncements->isEmpty())
                <div class="empty-box">
                    <p style="color:var(--text-muted-dark);font-size:13.5px;">Belum ada pengumuman terbaru.</p>
                </div>
            @else
                @foreach($activeAnnouncements as $ann)
                <div style="padding: 16px; border-radius: 16px; background: rgba(59,130,246,0.03); border: 1px solid var(--border-glass); margin-bottom: 12px;">
                    <div style="font-size: 10px; color: #3b82f6; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">{{ $ann->created_at->translatedFormat('d M Y') }}</div>
                    <h3 style="margin: 0 0 8px; font-size: 15px;">{{ $ann->title }}</h3>
                    <p style="margin: 0; font-size: 13px; color: var(--text-muted); line-height: 1.5;">{{ Str::limit($ann->content, 120) }}</p>
                    <a href="#" style="display: inline-block; margin-top: 10px; font-size: 12px; color: #3b82f6; font-weight: 600; text-decoration: none;">Baca Selengkapnya &rarr;</a>
                </div>
                @endforeach
            @endif
        </div>

        {{-- History --}}
        <div class="db-hist">
            <div class="db-hist-head">
                <h2>Riwayat Hari Ini</h2>
                <a href="{{ route('attendance.index') }}">Lihat Semua &rarr;</a>
            </div>
            @if($todayAttendance->isEmpty())
                <div class="empty-box">
                    <div class="empty-icon">
                        <svg style="width:26px;height:26px;color:var(--text-muted-dark);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <p style="color:var(--text-muted-dark);font-size:13.5px;margin:0;">Belum ada absen hari ini.</p>
                </div>
            @else
                @foreach($todayAttendance as $att)
                <div class="hist-row">
                    <div class="hist-left">
                        <div style="width:32px; height:32px; border-radius:8px; background:{{ $att->type == 'in' ? 'rgba(59,130,246,0.1)' : 'rgba(245,158,11,0.1)' }}; display:flex; align-items:center; justify-content:center;">
                            <svg style="width:16px; height:16px; color:{{ $att->type == 'in' ? '#3b82f6' : '#f59e0b' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($att->type == 'in')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 8l4 4m0 0l-4 4m4-4H3"/>
                                @endif
                            </svg>
                        </div>
                        <div>
                            <div class="hist-name">{{ $att->type === 'in' ? 'Masuk' : 'Pulang' }}</div>
                            <div class="hist-time">{{ $att->created_at->format('H:i') }} WIB</div>
                        </div>
                    </div>
                    <span class="badge {{ $att->status === 'valid' ? 'badge-sah' : 'badge-fail' }}">
                        {{ strtoupper($att->status) }}
                    </span>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
