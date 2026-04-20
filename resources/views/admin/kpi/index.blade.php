@extends('layouts.app')

@section('content')
<style>
    .kpi-wrap { max-width: 1100px; margin: 0 auto; padding: 24px; }
    .kpi-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .kpi-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 20px; overflow: hidden; }
    .table { width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; }
    .table th { padding: 14px 20px; color: var(--text-muted-dark); border-bottom: 1px solid var(--border-glass); font-size: 11px; text-transform: uppercase; }
    .table td { padding: 14px 20px; border-bottom: 1px solid var(--border-glass); color: var(--text-main); }
    .score-badge { padding: 4px 10px; border-radius: 20px; font-weight: 700; font-size: 12px; }
    .s-high { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .s-mid { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .s-low { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
</style>

<div class="kpi-wrap">
    <div class="kpi-header">
        <h1>Penilaian Kinerja (KPI)</h1>
        <button onclick="document.getElementById('modalAdd').style.display='flex'" style="background:#3b82f6; color:white; border:none; padding:10px 20px; border-radius:12px; cursor:pointer; font-weight:600;">+ Beri Penilaian</button>
    </div>

    @if(session('success'))
        <div style="background: rgba(16,185,129,0.15); color: #10b981; padding: 16px; border-radius: 16px; margin-bottom: 24px;">{{ session('success') }}</div>
    @endif

    <div class="kpi-card">
        <table class="table">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Periode</th>
                    <th>Absensi</th>
                    <th>Performa</th>
                    <th>Sikap</th>
                    <th>Total Skor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kpis as $kpi)
                @php 
                    $avg = ($kpi->attendance_score + $kpi->performance_score + $kpi->attitude_score) / 3;
                    $class = $avg >= 80 ? 's-high' : ($avg >= 60 ? 's-mid' : 's-low');
                @endphp
                <tr>
                    <td><b>{{ $kpi->user->name }}</b></td>
                    <td>{{ $kpi->period }}</td>
                    <td>{{ $kpi->attendance_score }}</td>
                    <td>{{ $kpi->performance_score }}</td>
                    <td>{{ $kpi->attitude_score }}</td>
                    <td><span class="score-badge {{ $class }}">{{ number_format($avg, 1) }}</span></td>
                </tr>
                @endforeach
                @if($kpis->isEmpty())
                <tr><td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">Belum ada penilaian yang dilakukan.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div id="modalAdd" style="position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); display:none; align-items:center; justify-content:center; z-index:1000; padding:16px;">
    <div style="background:var(--bg-body); border-radius:24px; width:100%; max-width:500px; padding:32px; border:1px solid var(--border-glass);">
        <h3 style="margin-top:0;">Form Penilaian Karyawan</h3>
        <form action="{{ route('admin.kpi.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-muted); margin-bottom:6px;">Karyawan</label>
                <select name="user_id" class="form-control" style="width:100%; padding:12px; border-radius:12px; background:rgba(0,0,0,0.05); color:var(--text-main); border:1px solid var(--border-glass);">
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-muted); margin-bottom:6px;">Periode</label>
                <input type="text" name="period" class="form-control" required placeholder="Mis: Q1 2024" style="width:100%; padding:12px; border-radius:12px; background:rgba(0,0,0,0.05); color:var(--text-main); border:1px solid var(--border-glass);">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:16px;">
                <div>
                    <label style="font-size:11px; font-weight:600; color:var(--text-muted);">Absensi</label>
                    <input type="number" name="attendance_score" class="form-control" min="0" max="100" required style="width:100%; padding:10px; border-radius:10px; background:rgba(0,0,0,0.05); color:var(--text-main); border:1px solid var(--border-glass);">
                </div>
                <div>
                    <label style="font-size:11px; font-weight:600; color:var(--text-muted);">Performa</label>
                    <input type="number" name="performance_score" class="form-control" min="0" max="100" required style="width:100%; padding:10px; border-radius:10px; background:rgba(0,0,0,0.05); color:var(--text-main); border:1px solid var(--border-glass);">
                </div>
                <div>
                    <label style="font-size:11px; font-weight:600; color:var(--text-muted);">Sikap</label>
                    <input type="number" name="attitude_score" class="form-control" min="0" max="100" required style="width:100%; padding:10px; border-radius:10px; background:rgba(0,0,0,0.05); color:var(--text-main); border:1px solid var(--border-glass);">
                </div>
            </div>
            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-muted); margin-bottom:6px;">Catatan/Feedback</label>
                <textarea name="feedback" class="form-control" rows="3" style="width:100%; padding:12px; border-radius:12px; background:rgba(0,0,0,0.05); color:var(--text-main); border:1px solid var(--border-glass);"></textarea>
            </div>
            <div style="display:flex; gap:12px;">
                <button type="button" onclick="document.getElementById('modalAdd').style.display='none'" style="flex:1; padding:12px; border-radius:12px; border:1px solid var(--border-glass); background:transparent; color:var(--text-main); cursor:pointer;">Batal</button>
                <button type="submit" style="flex:2; padding:12px; border-radius:12px; border:none; background:#3b82f6; color:white; cursor:pointer; font-weight:600;">Simpan Skor</button>
            </div>
        </form>
    </div>
</div>
@endsection
