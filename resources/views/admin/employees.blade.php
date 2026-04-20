@extends('layouts.app')

@section('content')
<style>
    .admin-wrap { max-width: 1200px; margin: 0 auto; padding: 24px; }
    .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .admin-header h1 { font-size: 24px; margin: 0; font-weight: 700; color: var(--text-main); }
    
    .admin-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 16px; overflow: hidden; }
    .table-resp { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; }
    .table th { padding: 14px 20px; color: var(--text-muted-dark); font-weight: 600; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid var(--border-glass); background: rgba(0,0,0,0.02); }
    .table td { padding: 14px 20px; border-bottom: 1px solid var(--border-glass); color: var(--text-main); vertical-align: middle; }
    .table tr:last-child td { border-bottom: none; }
    .table tr:hover td { background: var(--hover-bg); }

    .btn-action { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; border: none; cursor: pointer; }
    .btn-edit { background: rgba(59,130,246,0.1); color: #3b82f6; }
    .btn-delete { background: rgba(239,68,68,0.1); color: #ef4444; }
    .btn-action:hover { opacity: 0.8; }
    
</style>

<div class="admin-wrap">

    <div class="admin-header">
        <h1>Kelola Karyawan</h1>
        <a href="{{ route('admin.employees.create') }}" class="btn-action" style="background: #3b82f6; color: white; padding: 10px 16px; border-radius: 12px; font-size: 13px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Karyawan
        </a>
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
                        <th>Tgl Daftar</th>
                        <th>Nama & Email</th>
                        <th>Jabatan</th>
                        <th>Status Wajah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $emp)
                    <tr>
                        <td>{{ $emp->created_at->format('d M Y') }}</td>
                        <td>
                            <b>{{ $emp->name }}</b><br>
                            <span style="font-size: 12px; color: var(--text-muted);">{{ $emp->email }}</span>
                        </td>
                        <td>{{ $emp->position->name ?? '-' }}</td>
                        <td>
                            @if($emp->face_reference_path)
                                <span style="display:inline-block; padding:4px 8px; border-radius:8px; background:rgba(16,185,129,0.1); color:#10b981; font-weight:600; font-size:11px;">Terdaftar</span>
                            @else
                                <span style="display:inline-block; padding:4px 8px; border-radius:8px; background:rgba(239,68,68,0.1); color:#ef4444; font-weight:600; font-size:11px;">Belum</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:8px;">
                                <a href="{{ route('admin.employees.edit', $emp->id) }}" class="btn-action btn-edit">Edit</a>
                                <form action="{{ route('admin.employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus karyawan ini? Data absensinya juga akan terhapus.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if(count($employees) == 0)
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada karyawan.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
