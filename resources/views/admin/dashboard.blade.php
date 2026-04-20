@extends('layouts.app')

@section('content')
<style>
    :root {
        --accent-primary: #3b82f6;
        --accent-secondary: #8b5cf6;
        --card-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3);
    }

    .dashboard-container {
        max-width: 1440px;
        margin: 0 auto;
        padding-bottom: 40px;
    }

    /* ─── Hero Section ─── */
    .hero-section {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1));
        border: 1px solid var(--border-glass);
        border-radius: 32px;
        padding: 40px;
        margin-bottom: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        backdrop-filter: blur(10px);
    }
    .hero-text h1 { font-size: 32px; font-weight: 800; margin: 0; background: linear-gradient(to right, var(--text-main), #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .hero-text p { color: var(--text-muted); margin-top: 8px; font-size: 15px; }

    /* ─── Bento Grid ─── */
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: auto;
        gap: 24px;
    }

    .bento-item {
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        border-radius: 28px;
        padding: 24px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .bento-item:hover { transform: translateY(-6px); border-color: rgba(59, 130, 246, 0.3); box-shadow: var(--card-shadow); }

    /* Size Variants */
    .col-span-2 { grid-column: span 2; }
    .col-span-3 { grid-column: span 3; }
    .row-span-2 { grid-row: span 2; }

    /* ─── Stat Cards ─── */
    .stat-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .stat-icon-wrap {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
    }
    .stat-label { font-size: 13px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .stat-value { font-size: 36px; font-weight: 800; color: var(--text-main); margin-top: 4px; }
    .stat-delta { font-size: 12px; margin-top: 8px; display: flex; align-items: center; gap: 4px; }
    .delta-up { color: #10b981; }

    /* ─── Chart Card ─── */
    .chart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .chart-title { font-size: 18px; font-weight: 700; color: var(--text-main); }
    .chart-container { height: 320px; width: 100%; }

    /* ─── Table Card ─── */
    .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .custom-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .custom-table th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
    .custom-table td { padding: 16px; background: rgba(255,255,255,0.02); vertical-align: middle; }
    .custom-table tr td:first-child { border-radius: 12px 0 0 12px; }
    .custom-table tr td:last-child { border-radius: 0 12px 12px 0; }
    
    .avatar-stack { display: flex; align-items: center; gap: 12px; }
    .avatar-img { width: 36px; height: 36px; border-radius: 10px; background: var(--hover-bg); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; }

    /* ─── Quick Actions ─── */
    .action-pill {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 20px; border-radius: 16px;
        background: rgba(255,255,255,0.03); border: 1px solid var(--border-glass);
        color: var(--text-main); text-decoration: none; font-size: 14px; font-weight: 600;
        transition: all 0.2s;
    }
    .action-pill:hover { background: var(--accent-primary); color: white; transform: translateX(4px); }

    @media (max-width: 1200px) {
        .bento-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .bento-grid { grid-template-columns: 1fr; }
        .col-span-2, .col-span-3 { grid-column: span 1; }
        .hero-section { flex-direction: column; text-align: center; gap: 24px; padding: 24px; }
    }
</style>

<div class="dashboard-container">
    {{-- HERO SECTION --}}
    <div class="hero-section">
        <div class="hero-text">
            <h1>Halo, {{ Auth::user()->name }}! 👋</h1>
            <p>Sistem HRD kamu mendeteksi <b>{{ $presentToday }}</b> kehadiran valid hari ini.</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('admin.attendances.index') }}" class="action-pill" style="background: var(--accent-primary); border: none; color: white;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan Lengkap
            </a>
            <div class="action-pill">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v14a2 2 0 002 2z"/></svg>
                {{ now()->translatedFormat('d M Y') }}
            </div>
        </div>
    </div>

    <div class="bento-grid">
        {{-- STAT 1 --}}
        <div class="bento-item">
            <div class="stat-header">
                <div class="stat-icon-wrap" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="stat-label">Total Karyawan</span>
            </div>
            <div class="stat-value">{{ $totalEmployees }}</div>
            <div class="stat-delta delta-up">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Stabil
            </div>
        </div>

        {{-- STAT 2 --}}
        <div class="bento-item">
            <div class="stat-header">
                <div class="stat-icon-wrap" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="stat-label">Hadir Hari Ini</span>
            </div>
            <div class="stat-value">{{ $presentToday }}</div>
            <div class="stat-delta" style="color: var(--text-muted);">
                Target: {{ $totalEmployees }}
            </div>
        </div>

        {{-- STAT 3 --}}
        <div class="bento-item">
            <div class="stat-header">
                <div class="stat-icon-wrap" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="stat-label">Belum Absen</span>
            </div>
            <div class="stat-value">{{ $absentToday }}</div>
            <div class="stat-delta" style="color: #ef4444;">
                Segera ingatkan
            </div>
        </div>

        {{-- QUICK LINKS --}}
        <div class="bento-item row-span-2">
            <div class="chart-header">
                <span class="chart-title">Akses Cepat</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <a href="{{ route('admin.employees.create') }}" class="action-pill">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Staf Baru
                </a>
                <a href="{{ route('admin.approvals') }}" class="action-pill">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Pusat Persetujuan
                </a>
                <a href="{{ route('admin.locations.index') }}" class="action-pill">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    Geofencing Kantor
                </a>
                <a href="{{ route('admin.announcements.index') }}" class="action-pill">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    Pusat Informasi
                </a>
                
                <div style="margin-top: 12px; padding: 16px; background: rgba(139, 92, 246, 0.1); border-radius: 20px; border: 1px dashed rgba(139, 92, 246, 0.3);">
                    <div style="font-size: 11px; font-weight: 700; color: #8b5cf6; text-transform: uppercase;">Kontrak Habis</div>
                    <div style="font-size: 20px; font-weight: 800; color: var(--text-main); margin: 4px 0;">{{ $expiringContractsCount }} Orang</div>
                    <a href="{{ route('admin.employees.index') }}?filter=expiring" style="font-size: 11px; color: #8b5cf6; text-decoration: none; font-weight: 700;">Lihat Semua &rarr;</a>
                </div>
            </div>
        </div>

        {{-- CHART CARD --}}
        <div class="bento-item col-span-3">
            <div class="chart-header">
                <span class="chart-title">Trend Kehadiran Mingguan</span>
                <div style="display: flex; gap: 8px;">
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-muted);">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></span> Masuk
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-muted);">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></span> Pulang
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        {{-- RECENT ACTIVITY --}}
        <div class="bento-item col-span-4">
            <div class="table-header">
                <span class="chart-title">Aktivitas Presensi Terbaru</span>
                <a href="{{ route('admin.attendances.index') }}" style="font-size: 13px; font-weight: 600; color: var(--accent-primary); text-decoration: none;">Lihat Semua Laporan</a>
            </div>
            <div style="overflow-x: auto;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Karyawan</th>
                            <th>Waktu & Tanggal</th>
                            <th>Jenis</th>
                            <th>Verifikasi</th>
                            <th>Visual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAttendances as $att)
                        <tr>
                            <td>
                                <div class="avatar-stack">
                                    <div class="avatar-img">{{ strtoupper(substr($att->user->name, 0, 1)) }}</div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 14px;">{{ $att->user->name }}</div>
                                        <div style="font-size: 12px; color: var(--text-muted);">{{ $att->user->position->name ?? 'Staf' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $att->created_at->translatedFormat('H:i') }} WIB</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $att->created_at->translatedFormat('d M Y') }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $att->type == 'in' ? 'b-in' : 'b-out' }}" style="padding: 6px 12px; border-radius: 8px;">
                                    {{ $att->type == 'in' ? 'MASUK' : 'PULANG' }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background: {{ $att->status == 'valid' ? '#10b981' : '#ef4444' }};"></div>
                                    <span style="font-size: 12px; font-weight: 700;">{{ strtoupper($att->status) }}</span>
                                </div>
                            </td>
                            <td>
                                @if($att->image_path)
                                    <img src="{{ asset('storage/'.$att->image_path) }}" style="width: 40px; height: 40px; border-radius: 12px; object-fit: cover; border: 2px solid var(--border-glass);">
                                @else
                                    <div style="width: 40px; height: 40px; border-radius: 12px; background: var(--hover-bg); display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    
    let gradIn = ctx.createLinearGradient(0, 0, 0, 300);
    gradIn.addColorStop(0, 'rgba(59, 130, 246, 0.3)');   
    gradIn.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

    let gradOut = ctx.createLinearGradient(0, 0, 0, 300);
    gradOut.addColorStop(0, 'rgba(245, 158, 11, 0.2)');   
    gradOut.addColorStop(1, 'rgba(245, 158, 11, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                {
                    label: 'Masuk',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: gradIn,
                    borderWidth: 4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: 'rgba(255,255,255,0.2)',
                    pointRadius: 4,
                    pointHoverRadius: 8,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Pulang',
                    data: {!! json_encode($chartDataOut) !!},
                    borderColor: '#f59e0b',
                    backgroundColor: gradOut,
                    borderWidth: 4,
                    pointBackgroundColor: '#f59e0b',
                    pointBorderColor: 'rgba(255,255,255,0.2)',
                    pointRadius: 4,
                    pointHoverRadius: 8,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    padding: 16,
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 14, weight: '700' },
                    bodyFont: { size: 14 },
                    displayColors: true,
                    cornerRadius: 12
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.05)', drawBorder: false },
                    ticks: { color: '#64748b', font: { weight: '600' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { weight: '600' } }
                }
            }
        }
    });
</script>
@endsection
on
