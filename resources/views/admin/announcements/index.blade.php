@extends('layouts.app')

@section('content')
<style>
    .ann-wrap { max-width: 1100px; margin: 0 auto; padding: 24px; }
    .ann-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    
    .ann-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 20px; overflow: hidden; margin-bottom: 20px; }
    .ann-body { padding: 20px; }
    
    .btn-primary { background: #3b82f6; color: white; padding: 10px 20px; border-radius: 12px; border: none; font-weight: 600; cursor: pointer; text-decoration: none; }
    
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 16px; }
    .modal { background: var(--bg-body); border: 1px solid var(--border-glass); border-radius: 20px; width: 100%; max-width: 600px; }
    .modal-header { padding: 20px; border-bottom: 1px solid var(--border-glass); display: flex; justify-content: space-between; align-items: center; }
    .modal-body { padding: 20px; }
    
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; }
    .form-control { width: 100%; padding: 12px; border: 1px solid var(--border-glass); border-radius: 10px; background: rgba(0,0,0,0.05); color: var(--text-main); font-family: inherit; }
    
    .status-active { color: #10b981; font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 4px; }
    .status-inactive { color: #ef4444; font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 4px; }
</style>

<div class="ann-wrap">
    <div class="ann-header">
        <h1>Pusat Informasi</h1>
        <button class="btn-primary" onclick="document.getElementById('modalAdd').style.display='flex'">+ Terbitkan Pengumuman</button>
    </div>

    @if(session('success'))
        <div style="background: rgba(16,185,129,0.15); color: #10b981; padding: 16px; border-radius: 16px; margin-bottom: 24px;">{{ session('success') }}</div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
        @foreach($announcements as $ann)
        <div class="ann-card">
            <div class="ann-body">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                        <div style="font-size: 11px; color: var(--text-muted); margin-bottom: 4px;">{{ $ann->created_at->format('d M Y, H:i') }}</div>
                        <h3 style="margin: 0 0 12px;">{{ $ann->title }}</h3>
                    </div>
                    @if($ann->is_active)
                        <span class="status-active"><span style="width:8px; height:8px; background:#10b981; border-radius:50%;"></span> Aktif</span>
                    @else
                        <span class="status-inactive"><span style="width:8px; height:8px; background:#ef4444; border-radius:50%;"></span> Non-Aktif</span>
                    @endif
                </div>
                
                <div style="color: var(--text-main); line-height: 1.6; margin-bottom: 20px; white-space: pre-wrap;">{{ $ann->content }}</div>
                
                <div style="display: flex; gap: 12px; padding-top: 16px; border-top: 1px solid var(--border-glass);">
                    <form action="{{ route('admin.announcements.toggle', $ann->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" style="background:none; border:none; color:#3b82f6; font-weight:600; cursor:pointer; font-size:13px;">{{ $ann->is_active ? 'Non-aktifkan' : 'Aktifkan' }}</button>
                    </form>
                    <button onclick="openEditModal({{ $ann }})" style="background:none; border:none; color:#3b82f6; font-weight:600; cursor:pointer; font-size:13px;">Edit</button>
                    <form action="{{ route('admin.announcements.destroy', $ann->id) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none; border:none; color:#ef4444; font-weight:600; cursor:pointer; font-size:13px;">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
        
        @if(count($announcements) == 0)
            <div style="text-align:center; padding:60px; background:var(--bg-glass); border-radius:24px; color:var(--text-muted);">Belum ada pengumuman yang diterbitkan.</div>
        @endif
    </div>
</div>

<div id="modalAdd" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 style="margin:0;">Terbitkan Pengumuman Baru</h3>
            <button onclick="document.getElementById('modalAdd').style.display='none'" style="background:none; border:none; font-size:24px; color:var(--text-muted); cursor:pointer;">&times;</button>
        </div>
        <div class="modal-body">
            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Judul Pengumuman</label>
                    <input type="text" name="title" class="form-control" required placeholder="Mis: Libur Bersama Idul Fitri">
                </div>
                <div class="form-group">
                    <label class="form-label">Isi Pengumuman</label>
                    <textarea name="content" class="form-control" rows="8" required placeholder="Tulis rincian pengumuman di sini..."></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width:100%;">Terbitkan Sekarang</button>
            </form>
        </div>
    </div>
</div>

<div id="modalEdit" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 style="margin:0;">Edit Pengumuman</h3>
            <button onclick="document.getElementById('modalEdit').style.display='none'" style="background:none; border:none; font-size:24px; color:var(--text-muted); cursor:pointer;">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Judul Pengumuman</label>
                    <input type="text" name="title" id="editTitle" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Isi Pengumuman</label>
                    <textarea name="content" id="editContent" class="form-control" rows="8" required></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width:100%;">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(ann) {
        document.getElementById('editForm').action = '/admin/announcements/' + ann.id;
        document.getElementById('editTitle').value = ann.title;
        document.getElementById('editContent').value = ann.content;
        document.getElementById('modalEdit').style.display = 'flex';
    }
</script>
@endsection
