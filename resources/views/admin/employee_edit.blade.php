@extends('layouts.app')

@section('content')
<style>
    .admin-wrap { max-width: 600px; margin: 0 auto; padding: 24px; }
    .admin-header { margin-bottom: 24px; }
    .admin-header h1 { font-size: 24px; margin: 0 0 8px; font-weight: 700; color: var(--text-main); }
    .admin-header p { font-size: 14px; margin: 0; color: var(--text-muted); }
    
    .form-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 16px; padding: 24px; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; }
    .form-control { width: 100%; padding: 12px 14px; border: 1px solid var(--border-glass); border-radius: 10px; background: rgba(0,0,0,0.05); color: var(--text-main); font-size: 14px; outline: none; }
    [data-theme="light"] .form-control { background: #f9fafb; border: 1px solid #e5e7eb; }
    .form-control:focus { border-color: #3b82f6; }
    .text-danger { color: #ef4444; font-size: 12px; margin-top: 4px; display: block; }
    
    .btn-primary { width: 100%; padding: 12px; border-radius: 10px; background: #3b82f6; color: white; border: none; font-weight: 600; font-size: 14px; cursor: pointer; transition: background 0.2s; }
    .btn-primary:hover { background: #2563eb; }
    .btn-back { display: inline-block; margin-bottom: 16px; font-size: 13px; color: #3b82f6; text-decoration: none; }
</style>

<div class="admin-wrap">
    <a href="{{ route('admin.employees.index') }}" class="btn-back">&larr; Kembali ke Daftar Karyawan</a>
    
    <div class="admin-header">
        <h1>Edit Karyawan</h1>
        <p>Update data profil karyawan.</p>
    </div>

    <div class="form-card">
        <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $employee->name) }}" required>
                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}" required>
                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Jabatan (Position)</label>
                <input type="text" name="position" class="form-control" value="{{ old('position', $employee->position) }}" required>
                @error('position') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection
