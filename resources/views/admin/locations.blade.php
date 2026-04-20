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
        <h1>Kelola Lokasi Perusahaan</h1>
        <button onclick="openAddModal()" class="btn-primary">+ Tambah Lokasi</button>
    </div>

    @if(session('success'))
        <div style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #10b981; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 500;">
            Terdapat kesalahan pada isian form.
        </div>
    @endif

    <div class="admin-card">
        <div class="table-resp">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Lokasi</th>
                        <th>Koordinat (Lat, Lng)</th>
                        <th>Radius</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($locations as $loc)
                    <tr>
                        <td><b>{{ $loc->name }}</b></td>
                        <td>{{ $loc->latitude }}, {{ $loc->longitude }}</td>
                        <td>{{ $loc->radius }} meter</td>
                        <td>
                            <div style="display:flex; gap:8px;">
                                <button type="button" class="btn-action btn-edit" onclick="openEditModal({{ $loc }})">Edit</button>
                                <form action="{{ route('admin.locations.destroy', $loc->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus lokasi ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if(count($locations) == 0)
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada lokasi perusahaan.</td>
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
            <h3>Tambah Lokasi Baru</h3>
            <button class="btn-close" onclick="closeModal('modalAdd')">&times;</button>
        </div>
        <div class="modal-body">
            <form action="{{ route('admin.locations.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama / Deskripsi Lokasi</label>
                    <input type="text" name="name" class="form-control" required placeholder="Mis. Kantor Pusat">
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Latitude</label>
                        <input type="number" step="any" name="latitude" class="form-control" required placeholder="-6.123456">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Longitude</label>
                        <input type="number" step="any" name="longitude" class="form-control" required placeholder="106.123456">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Batas Radius (meter)</label>
                    <input type="number" name="radius" class="form-control" required placeholder="100" value="100">
                </div>
                <button type="submit" class="btn-primary" style="width:100%; justify-content:center;">Simpan Lokasi</button>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div id="modalEdit" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3>Edit Lokasi</h3>
            <button class="btn-close" onclick="closeModal('modalEdit')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Nama / Deskripsi Lokasi</label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Latitude</label>
                        <input type="number" step="any" name="latitude" id="editLat" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Longitude</label>
                        <input type="number" step="any" name="longitude" id="editLng" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Batas Radius (meter)</label>
                    <input type="number" name="radius" id="editRadius" class="form-control" required>
                </div>
                <button type="submit" class="btn-primary" style="width:100%; justify-content:center;">Update Lokasi</button>
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
    function openEditModal(loc) {
        let form = document.getElementById('editForm');
        form.action = '/admin/locations/' + loc.id;
        document.getElementById('editName').value = loc.name;
        document.getElementById('editLat').value = loc.latitude;
        document.getElementById('editLng').value = loc.longitude;
        document.getElementById('editRadius').value = loc.radius;
        document.getElementById('modalEdit').style.display = 'flex';
    }
</script>
@endsection
