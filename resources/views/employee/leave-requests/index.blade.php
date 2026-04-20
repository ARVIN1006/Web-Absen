@extends('layouts.app')

@section('content')
<style>
    .db-wrap { max-width:1100px; margin:0 auto; padding:28px 20px 20px; }
    .db-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .db-header h1 { font-size: 24px; margin: 0; font-weight: 700; color: var(--text-main); }
    
    .btn-primary { background: #3b82f6; color: white; padding: 10px 16px; border-radius: 10px; text-decoration:none; display:inline-flex; align-items:center; gap:8px; font-weight: 600; font-size: 13px;}
    .btn-back { display: inline-block; margin-bottom: 16px; font-size: 13px; color: #3b82f6; text-decoration: none; font-weight:600;}

    .db-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 16px; overflow: hidden; margin-bottom:24px;}
    .table-resp { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; }
    .table th { padding: 14px 20px; color: var(--text-muted-dark); font-weight: 600; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid var(--border-glass); background: rgba(0,0,0,0.02); }
    .table td { padding: 14px 20px; border-bottom: 1px solid var(--border-glass); color: var(--text-main); vertical-align: middle; }
    .table tr:last-child td { border-bottom: none; }
    .table tr:hover td { background: var(--hover-bg); }

    .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
    .b-pending { background: rgba(245,158,11,0.15); color: #f59e0b; }
    .b-approved { background: rgba(16,185,129,0.15); color: #10b981; }
    .b-rejected { background: rgba(239,68,68,0.15); color: #ef4444; }
</style>

<div class="db-wrap">
    <a href="{{ route('dashboard') }}" class="btn-back">&larr; Kembali ke Dashboard</a>

    <div class="db-header">
        <h1>Riwayat Pengajuan Cuti</h1>
        <a href="{{ route('employee.leave-requests.create') }}" class="btn-primary">+ Ajukan Cuti</a>
    </div>

    @if(session('success'))
        <div style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #10b981; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    <div class="db-card">
        <div class="table-resp">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tgl Pengajuan</th>
                        <th>Tipe Cuti</th>
                        <th>Periode</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Catatan HRD</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveRequests as $req)
                    <tr>
                        <td>{{ $req->created_at->format('d M Y') }}</td>
                        <td><b>{{ $req->leaveType->name }}</b></td>
                        <td>
                            {{ $req->start_date->format('d M Y') }} - {{ $req->end_date->format('d M Y') }}<br>
                            <span style="font-size: 12px; color: var(--text-muted);">({{ $req->total_days }} Hari)</span>
                        </td>
                        <td>
                            {{ Str::limit($req->reason, 30) }}
                        </td>
                        <td>
                            <span class="badge b-{{ $req->status }}">
                                {{ strtoupper($req->status) }}
                            </span>
                        </td>
                        <td>{{ $req->admin_note ?: '-' }}</td>
                    </tr>
                    @endforeach
                    @if(count($leaveRequests) == 0)
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada riwayat pengajuan cuti.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
