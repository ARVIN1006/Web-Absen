@extends('layouts.app')

@section('content')
<style>
    .profile-wrap { max-width: 800px; margin: 0 auto; padding: 24px; }
    .profile-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 24px; padding: 32px; }
    .profile-header { display: flex; align-items: center; gap: 24px; margin-bottom: 32px; }
    .profile-avatar { width: 80px; height: 80px; border-radius: 20px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 700; }
    
    .form-section { margin-bottom: 32px; }
    .form-section-title { font-size: 14px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px; border-bottom: 1px solid var(--border-glass); padding-bottom: 8px; }
    
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }
    
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; }
    .form-control { width: 100%; padding: 12px 14px; border: 1px solid var(--border-glass); border-radius: 12px; background: rgba(0,0,0,0.05); color: var(--text-main); font-size: 14px; outline: none; }
    .form-control:focus { border-color: #3b82f6; }
    .form-control:disabled { opacity: 0.6; cursor: not-allowed; }
    
    .btn-save { background: #3b82f6; color: white; padding: 12px 24px; border-radius: 12px; border: none; font-weight: 600; cursor: pointer; transition: 0.2s; }
    .btn-save:hover { background: #2563eb; transform: translateY(-2px); }
</style>

<div class="profile-wrap">
    @if(session('success'))
        <div style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #10b981; padding: 16px; border-radius: 16px; margin-bottom: 24px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">{{ substr($user->name, 0, 1) }}</div>
            <div>
                <h1 style="margin: 0; font-size: 24px;">{{ $user->name }}</h1>
                <p style="margin: 4px 0 0; color: var(--text-muted);">{{ $user->position->name ?? 'Karyawan' }} • {{ $user->department->name ?? '-' }}</p>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf @method('PUT')
            
            <div class="form-section">
                <div class="form-section-title">Informasi Pekerjaan (Read Only)</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Email Perusahaan</label>
                        <input type="text" class="form-control" value="{{ $user->email }}" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Bergabung</label>
                        <input type="text" class="form-control" value="{{ $user->joined_at ? date('d M Y', strtotime($user->joined_at)) : '-' }}" disabled>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">Data Pribadi (Bisa Diubah)</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nomor WhatsApp/HP</label>
                        <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" placeholder="0812...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="gender" class="form-control">
                            <option value="">Pilih...</option>
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $user->birth_date) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIK (KTP)</label>
                        <input type="text" class="form-control" value="{{ $user->nik }}" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="address" class="form-control" rows="3">{{ old('address', $user->address) }}</textarea>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">Keamanan Akun</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Password Baru (Kosongkan jika tidak ganti)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>

            <div style="text-align: right; margin-top: 16px;">
                <button type="submit" class="btn-save">Simpan Perubahan Profil</button>
            </div>
        </form>
    </div>
</div>
@endsection
