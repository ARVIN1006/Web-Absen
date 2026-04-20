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
    .btn-primary { background: #3b82f6; color: white; padding: 10px 16px; border-radius: 10px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;}
    .btn-edit { background: rgba(59,130,246,0.1); color: #3b82f6; }
    .btn-delete { background: rgba(239,68,68,0.1); color: #ef4444; }
    .btn-action:hover, .btn-primary:hover { opacity: 0.8; }

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
        <h1>Kelola Tipe Cuti</h1>
        <button onclick="openAddModal()" class="btn-primary">+ Tambah Tipe Cuti</button>
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
                        <th>Tipe Cuti</th>
                        <th>Kuota / Tahun</th>
                        <th>Butuh Lampiran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveTypes as $type)
                    <tr>
                        <td>
                            <b>{{ $type->name }}</b><br>
                            <span style="font-size: 12px; color: var(--text-muted);">{{ $type->description ?: '-' }}</span>
                        </td>
                        <td>{{ $type->max_days_per_year }} Hari</td>
                        <td>
                            @if($type->requires_attachment)
                                <span style="background: rgba(245,158,11,0.1); color: #f59e0b; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600;">Ya</span>
                            @else
                                <span style="color: var(--text-muted);">-</span>
                            @endif
                        </td>
                        <td>
                            @if($type->is_active)
                                <span style="background: rgba(16,185,129,0.1); color: #10b981; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600;">Aktif</span>
                            @else
                                <span style="background: rgba(239,68,68,0.1); color: #ef4444; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600;">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:8px;">
                                <button type="button" class="btn-action btn-edit" onclick="openEditModal({{ $type }})">Edit</button>
                                <form action="{{ route('admin.leave-types.destroy', $type->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tipe cuti ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if(count($leaveTypes) == 0)
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada tipe cuti.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div id="modalAdd" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3>Tambah Tipe Cuti Baru</h3>
            <button class="btn-close" onclick="closeModal('modalAdd')">&times;</button>
        </div>
        <div class="modal-body">
            <form action="{{ route('admin.leave-types.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Cuti</label>
                    <input type="text" name="name" class="form-control" required placeholder="Mis. Cuti Tahunan">
                </div>
                <div class="form-group">
                    <label class="form-label">Maksimal Hari per Tahun</label>
                    <input type="number" name="max_days_per_year" class="form-control" required value="12">
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi (Opsional)</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" style="display:flex; align-items:center; gap:8px;">
                        <input type="checkbox" name="requires_attachment" value="1" style="width:16px; height:16px;">
                        Wajib upload lampiran (surat dokter, dsb)
                    </label>
                </div>
                <div class="form-group">
                    <label class="form-label" style="display:flex; align-items:center; gap:8px;">
                        <input type="checkbox" name="is_active" value="1" checked style="width:16px; height:16px;">
                        Aktif
                    </label>
                </div>
                <button type="submit" class="btn-primary" style="width:100%; justify-content:center;">Simpan</button>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div id="modalEdit" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3>Edit Tipe Cuti</h3>
            <button class="btn-close" onclick="closeModal('modalEdit')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Nama Cuti</label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Maksimal Hari per Tahun</label>
                    <input type="number" name="max_days_per_year" id="editMaxDays" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi (Opsional)</label>
                    <textarea name="description" id="editDesc" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" style="display:flex; align-items:center; gap:8px;">
                        <input type="hidden" name="requires_attachment" value="0">
                        <input type="checkbox" name="requires_attachment" id="editReqAttach" value="1" style="width:16px; height:16px;">
                        Wajib upload lampiran
                    </label>
                </div>
                <div class="form-group">
                    <label class="form-label" style="display:flex; align-items:center; gap:8px;">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="editIsActive" value="1" style="width:16px; height:16px;">
                        Aktif
                    </label>
                </div>
                <button type="submit" class="btn-primary" style="width:100%; justify-content:center;">Update</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('modalAdd').style.display = 'flex';
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    function openEditModal(type) {
        let form = document.getElementById('editForm');
        form.action = '/admin/leave-types/' + type.id;
        document.getElementById('editName').value = type.name;
        document.getElementById('editMaxDays').value = type.max_days_per_year;
        document.getElementById('editDesc').value = type.description || '';
        document.getElementById('editReqAttach').checked = type.requires_attachment == 1;
        document.getElementById('editIsActive').checked = type.is_active == 1;
        document.getElementById('modalEdit').style.display = 'flex';
    }
</script>
@endsection
