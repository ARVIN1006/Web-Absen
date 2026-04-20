@extends('layouts.app')

@section('content')
<style>
    .admin-wrap { max-width: 1200px; margin: 0 auto; padding: 24px; }
    .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap:wrap; gap:16px;}
    .admin-header h1 { font-size: 24px; margin: 0; font-weight: 700; color: var(--text-main); }
    
    .filter-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 16px; padding: 16px 20px; margin-bottom: 24px; display:flex; gap:16px; align-items:flex-end; flex-wrap:wrap;}
    .form-group { margin-bottom: 0; display:flex; flex-direction:column; gap:6px;}
    .form-label { font-size: 12px; font-weight: 600; color: var(--text-muted); }
    .form-control { padding: 10px 14px; border: 1px solid var(--border-glass); border-radius: 10px; background: rgba(0,0,0,0.05); color: var(--text-main); font-size: 13px; outline: none; min-width:180px;}
    [data-theme="light"] .form-control { background: #f9fafb; border: 1px solid #e5e7eb; color: #111827; }
    
    .btn { padding: 10px 16px; border-radius: 10px; font-weight: 600; font-size: 13px; cursor: pointer; border: none; text-decoration:none; display:inline-flex; align-items:center; gap:6px;}
    .btn-primary { background: #3b82f6; color: white; }
    .btn-outline { background: transparent; border:1px solid var(--border-glass); color: var(--text-main); }
    .btn-export-csv { background: #10b981; color: white; }
    .btn-export-pdf { background: #ef4444; color: white; }
    .btn:hover { opacity: 0.85; }

    .admin-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 16px; overflow: hidden; }
    .table-resp { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; }
    .table th { padding: 14px 20px; color: var(--text-muted-dark); font-weight: 600; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid var(--border-glass); background: rgba(0,0,0,0.02); }
    .table td { padding: 14px 20px; border-bottom: 1px solid var(--border-glass); color: var(--text-main); vertical-align: middle; }
    .table tr:last-child td { border-bottom: none; }
    .table tr:hover td { background: var(--hover-bg); }

    .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
    .b-in { background: rgba(59,130,246,0.15); color: #3b82f6; }
    .b-out { background: rgba(245,158,11,0.15); color: #f59e0b; }
    .b-valid { background: rgba(16,185,129,0.15); color: #10b981; }
    .b-invalid { background: rgba(239,68,68,0.15); color: #ef4444; }
    
    .pagination { margin: 20px; }

</style>

<div class="admin-wrap">

    <div class="admin-header">
        <h1>Laporan Absensi</h1>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('admin.export.csv', request()->all()) }}" class="btn btn-export-csv">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>    
                Export CSV
            </a>
            <a href="{{ route('admin.export.pdf', request()->all()) }}" target="_blank" class="btn btn-export-pdf">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Export PDF
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.attendances.index') }}" class="filter-card">
        <div class="form-group">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="form-group">
            <label class="form-label">Tanggal Akhir</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="form-group">
            <label class="form-label">Cari Nama</label>
            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nama karyawan...">
        </div>
        <div class="form-group" style="flex-direction:row; gap:8px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline">Reset</a>
        </div>
    </form>

    <div class="admin-card">
        <div class="table-resp">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tgl & Waktu</th>
                        <th>Karyawan</th>
                        <th>Tipe</th>
                        <th>Lokasi Lapor (Lat, Lng)</th>
                        <th>Status</th>
                        <th>Foto Validasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $att)
                    <tr>
                        <td>
                            <b>{{ $att->created_at->translatedFormat('d F Y') }}</b><br>
                            <span style="font-size: 12px; color: var(--text-muted);">{{ $att->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td>
                            <b>{{ $att->user->name }}</b><br>
                            <span style="font-size: 12px; color: var(--text-muted);">{{ $att->user->position->name ?? '-' }}</span>
                        </td>
                        <td><span class="badge {{ $att->type == 'in' ? 'b-in' : 'b-out' }}">{{ $att->type == 'in' ? 'MASUK' : 'PULANG' }}</span></td>
                        <td style="font-size:12px; color:var(--text-muted);">
                            {{ $att->latitude }},<br>{{ $att->longitude }}
                        </td>
                        <td><span class="badge {{ $att->status == 'valid' ? 'b-valid' : 'b-invalid' }}">{{ strtoupper($att->status) }}</span></td>
                        <td>
                            <div style="display:flex; gap:8px; align-items:center;">
                                @if($att->image_path)
                                    <a href="{{ asset('storage/'.$att->image_path) }}" target="_blank">
                                        <img src="{{ asset('storage/'.$att->image_path) }}" alt="Foto" style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-glass);">
                                    </a>
                                @else
                                    <span style="color:var(--text-muted); font-size:12px;">No Photo</span>
                                @endif
                                
                                <form action="{{ route('admin.attendances.destroy', $att->id) }}" method="POST" onsubmit="return confirm('Hapus log absensi ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background:rgba(239,68,68,0.1); color:#ef4444; border:none; padding:8px; border-radius:8px; cursor:pointer;" title="Hapus Log">
                                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if(count($attendances) == 0)
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 20px;">Data absensi tidak ditemukan.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="pagination">
            {{ $attendances->withQueryString()->links('pagination::bootstrap-5') }} <!-- Asumsi pakai standard bootstrap pagination views dari Laravel -->
        </div>
    </div>
</div>
@endsection
