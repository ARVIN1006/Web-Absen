@extends('layouts.app')

@section('content')
<style>
    .reim-wrap { max-width: 1200px; margin: 0 auto; padding: 24px; }
    .reim-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    
    .reim-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 20px; overflow: hidden; }
    .table-resp { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; }
    .table th { padding: 14px 20px; color: var(--text-muted-dark); font-weight: 600; font-size: 11px; text-transform: uppercase; border-bottom: 1px solid var(--border-glass); background: rgba(0,0,0,0.02); }
    .table td { padding: 14px 20px; border-bottom: 1px solid var(--border-glass); color: var(--text-main); vertical-align: middle; }
    
    .status-badge { padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .s-pending { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .s-approved { background: rgba(16, 185, 129, 0.15); color: #10b981; }
    .s-rejected { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

    .btn-approve { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid #10b981; padding: 6px 12px; border-radius: 8px; font-weight: 600; cursor: pointer; }
    .btn-reject { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid #ef4444; padding: 6px 12px; border-radius: 8px; font-weight: 600; cursor: pointer; }
    
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 16px; }
    .modal { background: var(--bg-body); border: 1px solid var(--border-glass); border-radius: 20px; width: 100%; max-width: 500px; }
    .modal-header { padding: 20px; border-bottom: 1px solid var(--border-glass); display: flex; justify-content: space-between; align-items: center; }
    .modal-body { padding: 20px; }
</style>

<div class="reim-wrap">
    <div class="reim-header">
        <h1>Persetujuan Reimbursement</h1>
        <div style="display:flex; gap:12px;">
            <a href="?status=pending" style="text-decoration:none; color:{{ request('status')=='pending' ? '#3b82f6' : 'var(--text-muted)' }}; font-weight:600; font-size:13px;">Menunggu</a>
            <a href="?status=approved" style="text-decoration:none; color:{{ request('status')=='approved' ? '#3b82f6' : 'var(--text-muted)' }}; font-weight:600; font-size:13px;">Disetujui</a>
            <a href="?status=rejected" style="text-decoration:none; color:{{ request('status')=='rejected' ? '#3b82f6' : 'var(--text-muted)' }}; font-weight:600; font-size:13px;">Ditolak</a>
            <a href="{{ route('admin.reimbursements.index') }}" style="text-decoration:none; color:{{ !request('status') ? '#3b82f6' : 'var(--text-muted)' }}; font-weight:600; font-size:13px;">Semua</a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(16,185,129,0.15); color: #10b981; padding: 16px; border-radius: 16px; margin-bottom: 24px;">{{ session('success') }}</div>
    @endif

    <div class="reim-card">
        <div class="table-resp">
            <table class="table">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Klaim</th>
                        <th>Nominal</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reimbursements as $reim)
                    <tr>
                        <td>
                            <b>{{ $reim->user->name }}</b><br>
                            <span style="font-size:12px; color:var(--text-muted);">{{ $reim->user->department->name ?? '-' }}</span>
                        </td>
                        <td>
                            <b>{{ $reim->title }}</b><br>
                            <span style="font-size:12px; color:var(--text-muted);">{{ ucfirst($reim->type) }}</span>
                        </td>
                        <td><b>Rp {{ number_format($reim->amount, 0, ',', '.') }}</b></td>
                        <td>
                            @if($reim->attachment_path)
                                <a href="{{ asset('storage/'.$reim->attachment_path) }}" target="_blank">
                                    <img src="{{ asset('storage/'.$reim->attachment_path) }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td><span class="status-badge s-{{ $reim->status }}">{{ $reim->status }}</span></td>
                        <td>
                            @if($reim->status == 'pending')
                                <div style="display:flex; gap:8px;">
                                    <button class="btn-approve" onclick="openActionModal({{ $reim }}, 'approved')">Setujui</button>
                                    <button class="btn-reject" onclick="openActionModal({{ $reim }}, 'rejected')">Tolak</button>
                                </div>
                            @else
                                <span style="font-size:12px; color:var(--text-muted);">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if(count($reimbursements) == 0)
                    <tr><td colspan="6" style="text-align:center; padding:32px; color:var(--text-muted);">Tidak ada pengajuan untuk kategori ini.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalAction" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 id="actionTitle" style="margin:0;">Proses Reimbursement</h3>
            <button onclick="document.getElementById('modalAction').style.display='none'" style="background:none; border:none; font-size:24px; color:var(--text-muted); cursor:pointer;">&times;</button>
        </div>
        <div class="modal-body">
            <form id="actionForm" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status" id="actionStatus">
                <div style="margin-bottom:20px;">
                    <p style="font-size:14px; color:var(--text-main);">Anda akan memproses klaim dari <b id="claimantName"></b> senilai <b id="claimAmount"></b>.</p>
                </div>
                <div style="margin-bottom:20px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-muted); margin-bottom:8px;">Catatan Admin (Opsional)</label>
                    <textarea name="admin_note" style="width:100%; padding:12px; border:1px solid var(--border-glass); border-radius:12px; background:rgba(0,0,0,0.05); color:var(--text-main);" rows="3" placeholder="Alasan penolakan atau catatan pembayaran..."></textarea>
                </div>
                <button type="submit" id="submitBtn" class="btn-approve" style="width:100%; padding:14px; border-radius:12px;">Konfirmasi Sekarang</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openActionModal(reim, status) {
        document.getElementById('actionForm').action = '/admin/reimbursements/' + reim.id + '/status';
        document.getElementById('actionStatus').value = status;
        document.getElementById('claimantName').innerText = reim.user.name;
        document.getElementById('claimAmount').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(reim.amount);
        
        let btn = document.getElementById('submitBtn');
        if(status === 'approved') {
            document.getElementById('actionTitle').innerText = 'Setujui Reimbursement';
            btn.innerText = 'Setujui Pengajuan';
            btn.className = 'btn-approve';
        } else {
            document.getElementById('actionTitle').innerText = 'Tolak Reimbursement';
            btn.innerText = 'Tolak Pengajuan';
            btn.className = 'btn-reject';
        }
        
        document.getElementById('modalAction').style.display = 'flex';
    }
</script>
@endsection
