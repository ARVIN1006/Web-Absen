@extends('layouts.app')

@section('content')
<style>
    .appr-wrap { max-width: 1100px; margin: 0 auto; padding: 24px; }
    .appr-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    @media (max-width: 991px) { .appr-grid { grid-template-columns: 1fr; } }
    
    .appr-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 20px; padding: 24px; }
    .appr-title { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
    .badge-count { background: #3b82f6; color: white; padding: 2px 8px; border-radius: 8px; font-size: 12px; }
    
    .req-item { padding: 16px; border-radius: 16px; background: rgba(0,0,0,0.02); border: 1px solid var(--border-glass); margin-bottom: 12px; transition: 0.3s; }
    .req-item:hover { border-color: #3b82f6; background: rgba(59,130,246,0.02); }
    
    .req-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
    .req-user { font-weight: 700; font-size: 14px; }
    .req-type { font-size: 11px; color: #3b82f6; font-weight: 700; text-transform: uppercase; }
    
    .req-body { font-size: 13px; color: var(--text-muted); margin-bottom: 16px; }
    .req-footer { display: flex; gap: 8px; }
    .btn-sml { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; }
</style>

<div class="appr-wrap">
    <div style="margin-bottom:32px;">
        <h1>Pusat Persetujuan</h1>
        <p style="color:var(--text-muted);">Kelola semua permintaan karyawan dalam satu layar.</p>
    </div>

    <div class="appr-grid">
        {{-- Leave Requests --}}
        <div class="appr-card">
            <div class="appr-title">
                <h3 style="margin:0;">Permohonan Cuti/Izin</h3>
                <span class="badge-count">{{ $leaveRequests->count() }} Menunggu</span>
            </div>
            
            @foreach($leaveRequests as $req)
            <div class="req-item">
                <div class="req-head">
                    <div>
                        <div class="req-user">{{ $req->user->name }}</div>
                        <div class="req-type">{{ $req->leaveType->name ?? 'Izin' }}</div>
                    </div>
                    <div style="font-size:11px; color:var(--text-muted);">{{ $req->created_at->diffForHumans() }}</div>
                </div>
                <div class="req-body">
                    <div style="margin-bottom:4px;"><b>{{ date('d/m/y', strtotime($req->start_date)) }} - {{ date('d/m/y', strtotime($req->end_date)) }}</b></div>
                    {{ Str::limit($req->reason, 60) }}
                </div>
                <div class="req-footer">
                    <a href="{{ route('admin.leave-requests.index') }}" class="btn-sml" style="background:#3b82f6; color:white; text-decoration:none;">Proses di Menu Cuti</a>
                </div>
            </div>
            @endforeach
            @if($leaveRequests->isEmpty())
                <div style="text-align:center; padding:40px; color:var(--text-muted); font-size:13px;">Tidak ada permohonan cuti baru.</div>
            @endif
        </div>

        {{-- Reimbursements --}}
        <div class="appr-card">
            <div class="appr-title">
                <h3 style="margin:0;">Klaim Reimbursement</h3>
                <span class="badge-count">{{ $reimbursements->count() }} Menunggu</span>
            </div>
            
            @foreach($reimbursements as $req)
            <div class="req-item">
                <div class="req-head">
                    <div>
                        <div class="req-user">{{ $req->user->name }}</div>
                        <div class="req-type">Rp {{ number_format($req->amount, 0, ',', '.') }}</div>
                    </div>
                    <div style="font-size:11px; color:var(--text-muted);">{{ $req->created_at->diffForHumans() }}</div>
                </div>
                <div class="req-body">
                    <div style="margin-bottom:4px;"><b>{{ $req->title }}</b></div>
                    {{ Str::limit($req->description, 60) }}
                </div>
                <div class="req-footer">
                    <a href="{{ route('admin.reimbursements.index') }}?status=pending" class="btn-sml" style="background:#3b82f6; color:white; text-decoration:none;">Proses di Menu Klaim</a>
                </div>
            </div>
            @endforeach
            @if($reimbursements->isEmpty())
                <div style="text-align:center; padding:40px; color:var(--text-muted); font-size:13px;">Tidak ada klaim reimbursement baru.</div>
            @endif
        </div>
    </div>
</div>
@endsection
