@extends('layouts.app')

@section('content')
<style>
    .admin-wrap { max-width: 1400px; margin: 0 auto; padding: 24px; }
    .admin-header { margin-bottom: 32px; }
    .admin-header h1 { font-size: 28px; font-weight: 800; color: var(--text-main); margin-bottom: 4px; }
    .admin-header p { color: var(--text-muted); font-size: 14px; }
    
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 32px; }
    .stat-card { 
        background: var(--bg-glass); 
        border: 1px solid var(--border-glass); 
        border-radius: 24px; 
        padding: 20px; 
        transition: all 0.3s ease;
    }
    .stat-card:hover { transform: translateY(-4px); border-color: rgba(59, 130, 246, 0.4); }
    
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 12px;
    }
    .stat-name { font-size: 12px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
    .stat-val { font-size: 28px; font-weight: 800; color: var(--text-main); margin-top: 4px; }

    /* Quick Actions */
    .action-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px; }
    .action-card { 
        background: var(--bg-glass); 
        border: 1px solid var(--border-glass); 
        border-radius: 20px; 
        padding: 16px; 
        display: flex; 
        align-items: center; 
        gap: 16px; 
        text-decoration: none; 
        transition: all 0.2s;
        cursor: pointer;
    }
    .action-card:hover { background: rgba(59, 130, 246, 0.1); border-color: #3b82f6; transform: scale(1.02); }
    .action-icon { 
        width: 40px; height: 40px; border-radius: 10px; 
        background: rgba(59, 130, 246, 0.1); color: #3b82f6;
        display: flex; align-items: center; justify-content: center;
    }
    .action-info b { display: block; font-size: 14px; color: var(--text-main); }
    .action-info span { font-size: 11px; color: var(--text-muted); }

    .grid-main { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 32px; }
    @media (max-width: 1024px) { .grid-main { grid-template-columns: 1fr; } }

    .admin-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 24px; overflow: hidden; }
    .admin-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border-glass); font-weight: 700; font-size: 16px; display: flex; justify-content: space-between; align-items: center; color: var(--text-main); }
    
    .table-resp { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; }
    .table th { padding: 16px 24px; color: var(--text-muted-dark); font-weight: 600; font-size: 11px; text-transform: uppercase; border-bottom: 1px solid var(--border-glass); background: rgba(0,0,0,0.02); }
    .table td { padding: 16px 24px; border-bottom: 1px solid var(--border-glass); color: var(--text-main); vertical-align: middle; }
</style>

<div class="admin-wrap">
    <div class="admin-header" style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="margin: 0;">Halo, {{ Auth::user()->name }}! 👋</h1>
            <p style="margin: 4px 0 0;">Selamat datang kembali di panel administrasi HRIS.</p>
        </div>
        <div style="text-align: right;">
            <div style="font-weight: 700; color: var(--text-main); font-size: 16px;">{{ now()->translatedFormat('l') }}</div>
            <div style="color: var(--text-muted); font-size: 13px;">{{ now()->translatedFormat('d F Y') }}</div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div style="margin-bottom: 12px; font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em;">Akses Cepat</div>
    <div class="action-grid">
        <a href="{{ route('admin.employees.create') }}" class="action-card">
            <div class="action-icon"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg></div>
            <div class="action-info"><b>Tambah Karyawan</b><span>Daftarkan staf baru</span></div>
        </a>
        <a href="{{ route('admin.leave-requests.index') }}" class="action-card">
            <div class="action-icon" style="color: #8b5cf6; background: rgba(139, 92, 246, 0.1);"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div class="action-info"><b>Persetujuan Cuti</b><span>{{ $pendingLeaves }} menunggu</span></div>
        </a>
        <a href="{{ route('admin.locations.index') }}" class="action-card">
            <div class="action-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.1);"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg></div>
            <div class="action-info"><b>Atur Lokasi</b><span>Geofencing kantor</span></div>
        </a>
        <a href="{{ route('admin.attendances.index') }}" class="action-card">
            <div class="action-icon" style="color: #f59e0b; background: rgba(245, 158, 11, 0.1);"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
            <div class="action-info"><b>Cetak Laporan</b><span>Export PDF/CSV</span></div>
        </a>
    </div>

    <!-- Main Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
            <div class="stat-name">Karyawan Aktif</div>
            <div class="stat-val">{{ $totalEmployees }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
            <div class="stat-name">Hadir Hari Ini</div>
            <div class="stat-val">{{ $presentToday }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></div>
            <div class="stat-name">Belum Absen</div>
            <div class="stat-val">{{ $absentToday }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div class="stat-name">Kontrak Akan Habis</div>
            <div class="stat-val">{{ $expiringContractsCount }}</div>
        </div>
    </div>

    @if($expiringContractsCount > 0)
    <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); border-radius: 16px; padding: 16px; margin-bottom: 32px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(239,68,68,0.2); color: #ef4444; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div style="flex: 1;">
            <b style="color: var(--text-main); font-size: 14px;">Peringatan Kontrak Kerja</b>
            <p style="margin: 2px 0 0; font-size: 13px; color: var(--text-muted);">Ada {{ $expiringContractsCount }} karyawan yang masa kontraknya berakhir dalam 30 hari ke depan.</p>
        </div>
        <a href="{{ route('admin.employees.index') }}?filter=expiring" style="background: #ef4444; color: white; padding: 8px 16px; border-radius: 10px; text-decoration: none; font-size: 12px; font-weight: 600;">Lihat Detail</a>
    </div>
    @endif

    <div class="grid-main">
        <div class="admin-card">
            <div class="admin-card-header">Trend Kehadiran</div>
            <div style="padding: 24px;">
                <canvas id="attendanceChart" height="250"></canvas>
            </div>
        </div>
        <div class="admin-card">
            <div class="admin-card-header">Ringkasan Data</div>
            <div style="padding: 20px; display: flex; flex-direction: column; gap: 12px;">
                <div style="padding: 16px; background: rgba(0,0,0,0.02); border-radius: 16px; display: flex; justify-content: space-between;">
                    <span style="font-size: 13px; color: var(--text-muted);">Departemen</span>
                    <b style="color: var(--text-main);">{{ $totalDepartments }}</b>
                </div>
                <div style="padding: 16px; background: rgba(0,0,0,0.02); border-radius: 16px; display: flex; justify-content: space-between;">
                    <span style="font-size: 13px; color: var(--text-muted);">Jabatan</span>
                    <b style="color: var(--text-main);">{{ $totalPositions }}</b>
                </div>
                <div style="padding: 16px; background: rgba(0,0,0,0.02); border-radius: 16px; display: flex; justify-content: space-between;">
                    <span style="font-size: 13px; color: var(--text-muted);">Shift Kerja</span>
                    <b style="color: var(--text-main);">{{ $totalShifts }}</b>
                </div>
                <div style="padding: 16px; background: rgba(0,0,0,0.02); border-radius: 16px; display: flex; justify-content: space-between;">
                    <span style="font-size: 13px; color: var(--text-muted);">Titik Lokasi</span>
                    <b style="color: var(--text-main);">{{ $totalLocations }}</b>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            Aktivitas Absensi Terbaru
            <a href="{{ route('admin.attendances.index') }}" style="font-size: 12px; color: #3b82f6; text-decoration: none;">Semua Data &rarr;</a>
        </div>
        <div class="table-resp">
            <table class="table">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Waktu</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Foto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAttendances as $att)
                    <tr>
                        <td>
                            <b>{{ $att->user->name }}</b><br>
                            <span style="font-size: 11px; color: var(--text-muted);">{{ $att->user->position->name ?? '-' }}</span>
                        </td>
                        <td>{{ $att->created_at->translatedFormat('H:i') }} WIB</td>
                        <td><span class="badge {{ $att->type == 'in' ? 'b-in' : 'b-out' }}">{{ $att->type == 'in' ? 'Masuk' : 'Pulang' }}</span></td>
                        <td><span class="badge {{ $att->status == 'valid' ? 'b-valid' : 'b-invalid' }}">{{ strtoupper($att->status) }}</span></td>
                        <td>
                            @if($att->image_path)
                                <img src="{{ asset('storage/'.$att->image_path) }}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 6px;">
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    
    let gradIn = ctx.createLinearGradient(0, 0, 0, 300);
    gradIn.addColorStop(0, 'rgba(59, 130, 246, 0.2)');   
    gradIn.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

    let gradOut = ctx.createLinearGradient(0, 0, 0, 300);
    gradOut.addColorStop(0, 'rgba(245, 158, 11, 0.15)');   
    gradOut.addColorStop(1, 'rgba(245, 158, 11, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                {
                    label: 'Absen Masuk',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: gradIn,
                    borderWidth: 3,
                    pointBackgroundColor: '#3b82f6',
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Absen Pulang',
                    data: {!! json_encode($chartDataOut) !!},
                    borderColor: '#f59e0b',
                    backgroundColor: gradOut,
                    borderWidth: 3,
                    pointBackgroundColor: '#f59e0b',
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'top',
                    align: 'end',
                    labels: { color: '#94a3b8', font: { size: 12, weight: '600' }, usePointStyle: true }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    padding: 12,
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 13 },
                    bodyFont: { size: 13 },
                    usePointStyle: true
                }
            },
            interaction: { mode: 'nearest', axis: 'x', intersect: false },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { color: '#64748b', stepSize: 1 }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b' }
                }
            }
        }
    });
</script>
@endsection
