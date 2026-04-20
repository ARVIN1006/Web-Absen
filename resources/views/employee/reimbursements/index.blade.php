@extends('layouts.app')

@section('content')
<style>
    .reim-wrap { max-width: 1000px; margin: 0 auto; padding: 24px; }
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

    .btn-new { background: #3b82f6; color: white; padding: 10px 20px; border-radius: 12px; border: none; font-weight: 600; cursor: pointer; text-decoration: none; }
    
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 16px; }
    .modal { background: var(--bg-body); border: 1px solid var(--border-glass); border-radius: 20px; width: 100%; max-width: 500px; }
    .modal-header { padding: 20px; border-bottom: 1px solid var(--border-glass); display: flex; justify-content: space-between; align-items: center; }
    .modal-body { padding: 20px; }
    
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; }
    .form-control { width: 100%; padding: 12px; border: 1px solid var(--border-glass); border-radius: 10px; background: rgba(0,0,0,0.05); color: var(--text-main); }
</style>

<div class="reim-wrap">
    <div class="reim-header">
        <h1>Klaim Reimbursement</h1>
        <button class="btn-new" onclick="document.getElementById('modalAdd').style.display='flex'">+ Ajukan Klaim Baru</button>
    </div>

    @if(session('success'))
        <div style="background: rgba(16,185,129,0.15); color: #10b981; padding: 16px; border-radius: 16px; margin-bottom: 24px;">{{ session('success') }}</div>
    @endif

    <div class="reim-card">
        <div class="table-resp">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Klaim</th>
                        <th>Kategori</th>
                        <th>Nominal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reimbursements as $reim)
                    <tr>
                        <td>{{ $reim->created_at->format('d M Y') }}</td>
                        <td>
                            <b>{{ $reim->title }}</b><br>
                            <span style="font-size:12px; color:var(--text-muted);">{{ Str::limit($reim->description, 40) }}</span>
                        </td>
                        <td>{{ ucfirst($reim->type) }}</td>
                        <td><b>Rp {{ number_format($reim->amount, 0, ',', '.') }}</b></td>
                        <td>
                            <span class="status-badge s-{{ $reim->status }}">{{ $reim->status }}</span>
                            @if($reim->admin_note)
                                <div style="font-size:10px; color:#ef4444; margin-top:4px;">Ket: {{ $reim->admin_note }}</div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if(count($reimbursements) == 0)
                    <tr><td colspan="5" style="text-align:center; padding:32px; color:var(--text-muted);">Belum ada riwayat klaim.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalAdd" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 style="margin:0;">Ajukan Reimbursement</h3>
            <button onclick="document.getElementById('modalAdd').style.display='none'" style="background:none; border:none; font-size:24px; color:var(--text-muted); cursor:pointer;">&times;</button>
        </div>
        <div class="modal-body">
            <form action="{{ route('reimbursements.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Judul Klaim</label>
                    <input type="text" name="title" class="form-control" required placeholder="Mis: Parkir Kantor / Biaya Berobat">
                </div>
                <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Nominal (Rp)</label>
                        <input type="number" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="type" class="form-control" required>
                            <option value="medical">Medis</option>
                            <option value="travel">Transportasi</option>
                            <option value="office">Kebutuhan Kantor</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Foto Struk / Bukti (Max 2MB)</label>
                    <input type="file" name="attachment" class="form-control" required accept="image/*">
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan Tambahan</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn-new" style="width:100%; margin-top:8px;">Kirim Pengajuan</button>
            </form>
        </div>
    </div>
</div>
@endsection
