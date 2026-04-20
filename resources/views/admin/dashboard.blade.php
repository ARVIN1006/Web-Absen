@extends('layouts.app')

@section('content')
<style>
    .admin-wrap { max-width: 1200px; margin: 0 auto; padding: 24px; }
    .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .admin-header h1 { font-size: 24px; margin: 0; font-weight: 700; color: var(--text-main); }
    
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 16px; padding: 20px; transition: all 0.3s;}
    .stat-name { font-size: 13px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
    .stat-val { font-size: 32px; font-weight: 800; color: var(--text-main); }
    
    .admin-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 16px; overflow: hidden; }
    .admin-card-header { padding: 16px 20px; border-bottom: 1px solid var(--border-glass); font-weight: 600; font-size: 16px; display: flex; justify-content: space-between; align-items: center; }
    .table-resp { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; }
    .table th { padding: 12px 20px; color: var(--text-muted-dark); font-weight: 600; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid var(--border-glass); background: rgba(0,0,0,0.02); }
    .table td { padding: 14px 20px; border-bottom: 1px solid var(--border-glass); color: var(--text-main); }
    .table tr:last-child td { border-bottom: none; }
    .table tr:hover td { background: var(--hover-bg); }
    
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
    .b-in { background: rgba(59,130,246,0.15); color: #3b82f6; }
    .b-out { background: rgba(245,158,11,0.15); color: #f59e0b; }
    .b-valid { background: rgba(16,185,129,0.15); color: #10b981; }
    .b-invalid { background: rgba(239,68,68,0.15); color: #ef4444; }

    .admin-nav { display: flex; gap: 10px; margin-bottom: 24px; overflow-x: auto; padding-bottom: 5px; }
    .admin-nav-item { padding: 10px 16px; border-radius: 12px; background: var(--bg-glass); border: 1px solid var(--border-glass); color: var(--text-muted); font-size: 13px; font-weight: 600; text-decoration: none; white-space: nowrap; transition: all 0.2s;}
    .admin-nav-item:hover { background: var(--hover-bg); color: var(--text-main); }
    .admin-nav-item.active { background: #3b82f6; color: white; border-color: #3b82f6; }
</style>

<div class="admin-wrap">
    @include('admin.partials.nav')

    <div class="admin-header">
        <h1>Dashboard Admin</h1>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-name">Total Karyawan</div>
            <div class="stat-val">{{ $totalEmployees }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-name">Total Absensi Hari Ini</div>
            <div class="stat-val">{{ $todayAttendancesCount }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-name">Karyawan Hadir</div>
            <div class="stat-val" style="color: #10b981;">{{ $presentToday }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-name">Karyawan Belum Hadir</div>
            <div class="stat-val" style="color: #ef4444;">{{ $absentToday }}</div>
        </div>
    </div>

    <div class="admin-card" style="margin-bottom: 24px; padding: 20px;">
        <div style="font-weight: 600; font-size: 16px; margin-bottom: 16px; color: var(--text-main);">Statistik Kehadiran (7 Hari Terakhir)</div>
        <canvas id="attendanceChart" height="80"></canvas>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            10 Absensi Terbaru
            <a href="{{ route('admin.attendances.index') }}" style="font-size: 13px; color: #3b82f6; text-decoration: none;">Lihat Semua &rarr;</a>
        </div>
        <div class="table-resp">
            <table class="table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Karyawan</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Foto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAttendances as $att)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($att->created_at)->format('d M Y - H:i:s') }}</td>
                        <td>
                            <b>{{ $att->user->name }}</b><br>
                            <span style="font-size: 12px; color: var(--text-muted);">{{ $att->user->position ?? '-' }}</span>
                        </td>
                        <td><span class="badge {{ $att->type == 'in' ? 'b-in' : 'b-out' }}">{{ $att->type == 'in' ? 'MASUK' : 'PULANG' }}</span></td>
                        <td><span class="badge {{ $att->status == 'valid' ? 'b-valid' : 'b-invalid' }}">{{ strtoupper($att->status) }}</span></td>
                        <td>
                            @if($att->image_path)
                                <img src="{{ asset('storage/'.$att->image_path) }}" alt="Foto" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-glass);">
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if(count($recentAttendances) == 0)
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada absensi hari ini.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    
    // Gradient fill
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)');   
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Karyawan Hadir',
                data: {!! json_encode($chartData) !!},
                borderColor: '#3b82f6',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>
@endsection
