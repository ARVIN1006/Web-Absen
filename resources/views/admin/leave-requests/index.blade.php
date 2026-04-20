@extends('layouts.app')

@section('content')
<style>
    .admin-wrap { max-width: 1200px; margin: 0 auto; padding: 24px; }
    .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .admin-header h1 { font-size: 24px; margin: 0; font-weight: 700; color: var(--text-main); }
    
    .admin-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 16px; overflow: hidden; margin-bottom:24px;}
    .table-resp { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; }
    .table th { padding: 14px 20px; color: var(--text-muted-dark); font-weight: 600; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid var(--border-glass); background: rgba(0,0,0,0.02); }
    .table td { padding: 14px 20px; border-bottom: 1px solid var(--border-glass); color: var(--text-main); vertical-align: middle; }
    .table tr:last-child td { border-bottom: none; }
    .table tr:hover td { background: var(--hover-bg); }

    .btn-action { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; border: none; cursor: pointer; }
    .btn-primary { background: #10b981; color: white; }
    .btn-delete { background: rgba(239,68,68,0.1); color: #ef4444; }
    .btn-action:hover { opacity: 0.8; }
    
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
    .b-pending { background: rgba(245,158,11,0.15); color: #f59e0b; }
    .b-approved { background: rgba(16,185,129,0.15); color: #10b981; }
    .b-rejected { background: rgba(239,68,68,0.15); color: #ef4444; }

    /* Modal Form */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 16px; }
    .modal { background: var(--bg-body); border: 1px solid var(--border-glass); border-radius: 16px; width: 100%; max-width: 500px; overflow: hidden; }
    .modal-header { padding: 16px 20px; border-bottom: 1px solid var(--border-glass); display: flex; justify-content: space-between; align-items: center; }
    .modal-header h3 { margin: 0; font-size: 18px; color: var(--text-main); }
    .btn-close { background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 20px; }
    .modal-body { padding: 20px; }
    
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; }
    .form-control { width: 100%; padding: 12px 14px; border: 1px solid var(--border-glass); border-radius: 10px; background: rgba(0,0,0,0.05); color: var(--text-main); font-size: 14px; outline: none; }
    [data-theme="light"] .form-control { background: #f9fafb; border: 1px solid #e5e7eb; }
    .form-control:focus { border-color: #3b82f6; }

</style>

<div class="admin-wrap">

    <div class="admin-header">
        <h1>Daftar Pengajuan Cuti</h1>
    </div>

    @if(session('success'))
        <div style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #10b981; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-card">
        <div class="table-resp">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tgl Pengajuan</th>
                        <th>Karyawan</th>
                        <th>Tipe Cuti</th>
                        <th>Periode</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveRequests as $req)
                    <tr>
                        <td>{{ $req->created_at->translatedFormat('d F Y') }}</td>
                        <td>
                            <b>{{ $req->user->name }}</b><br>
                            <span style="font-size: 12px; color: var(--text-muted);">{{ $req->user->position ?: '-' }}</span>
                        </td>
                        <td>{{ $req->leaveType->name }}</td>
                        <td>
                            {{ $req->start_date->translatedFormat('d F Y') }} s/d {{ $req->end_date->translatedFormat('d F Y') }}<br>
                            <span style="font-size: 12px; color: var(--text-muted);">({{ $req->total_days }} Hari)</span>
                        </td>
                        <td>
                            {{ Str::limit($req->reason, 30) }}
                            @if($req->attachment_path)
                                <br><a href="{{ asset('storage/'.$req->attachment_path) }}" target="_blank" style="font-size:12px; color:#3b82f6;">Lihat Lampiran</a>
                            @endif
                        </td>
                        <td>
                            <span class="badge b-{{ $req->status }}">
                                {{ strtoupper($req->status) }}
                            </span>
                        </td>
                        <td>
                            @if($req->status == 'pending')
                            <div style="display:flex; gap:8px;">
                                <button type="button" class="btn-action btn-primary" onclick="openApproveModal({{ $req->id }})">Setujui</button>
                                <button type="button" class="btn-action btn-delete" onclick="openRejectModal({{ $req->id }})">Tolak</button>
                            </div>
                            @else
                                <span style="font-size: 12px; color: var(--text-muted);">
                                    Oleh: {{ $req->approver->name ?? '-' }}<br>
                                    @if($req->admin_note) Catatan: {{ $req->admin_note }} @endif
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if(count($leaveRequests) == 0)
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada pengajuan cuti.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Approve Modal --}}
<div id="modalApprove" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3>Setujui Pengajuan Cuti</h3>
            <button class="btn-close" onclick="closeModal('modalApprove')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="approveForm" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Catatan (Opsional)</label>
                    <textarea name="admin_note" class="form-control" rows="3" placeholder="Tambahkan pesan..."></textarea>
                </div>
                <button type="submit" class="btn-action btn-primary" style="width:100%; justify-content:center; padding:12px;">Konfirmasi Setuju</button>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div id="modalReject" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3>Tolak Pengajuan Cuti</h3>
            <button class="btn-close" onclick="closeModal('modalReject')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Alasan Penolakan</label>
                    <textarea name="admin_note" class="form-control" rows="3" required placeholder="Wajib diisi..."></textarea>
                </div>
                <button type="submit" class="btn-action btn-delete" style="width:100%; justify-content:center; padding:12px; background:#ef4444; color:white;">Konfirmasi Tolak</button>
            </form>
        </div>
    </div>
</div>

<script>
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    function openApproveModal(id) {
        let form = document.getElementById('approveForm');
        form.action = '/admin/leave-requests/' + id + '/approve';
        document.getElementById('modalApprove').style.display = 'flex';
    }
    function openRejectModal(id) {
        let form = document.getElementById('rejectForm');
        form.action = '/admin/leave-requests/' + id + '/reject';
        document.getElementById('modalReject').style.display = 'flex';
    }
</script>
@endsection
