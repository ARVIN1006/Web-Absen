@extends('layouts.app')

@section('content')
<style>
    .profile-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Profile Header & Banner */
    .profile-card {
        background: var(--bg-glass);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--border-glass);
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 10px 40px var(--shadow-drop);
        position: relative;
    }

    .profile-banner {
        height: 160px;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 50%, #d946ef 100%);
        position: relative;
    }

    .profile-header-content {
        padding: 0 2rem 2rem;
        margin-top: -60px;
        display: flex;
        align-items: flex-end;
        gap: 1.5rem;
        border-bottom: 1px solid var(--border-line);
        position: relative;
        z-index: 5;
    }

    .avatar-wrapper {
        width: 120px;
        height: 120px;
        border-radius: 28px;
        background: var(--bg-body);
        padding: 6px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    .avatar-inner {
        width: 100%;
        height: 100%;
        border-radius: 22px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 800;
        color: #fff;
        text-transform: uppercase;
        box-shadow: inset 0 0 20px rgba(0,0,0,0.1);
    }

    .user-meta h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
    }

    .user-meta p {
        font-size: 0.95rem;
        color: var(--text-muted);
        margin: 0.25rem 0 0.5rem;
    }

    .badge-group {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.8rem;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-verified { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
    .badge-pending { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }

    /* Bento Sections */
    .profile-body {
        padding: 2rem;
    }

    .section-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    @media (max-width: 768px) {
        .section-grid { grid-template-columns: 1fr; }
        .profile-header-content { flex-direction: column; align-items: center; text-align: center; margin-top: -80px; }
        .badge-group { justify-content: center; }
    }

    .info-section {
        margin-bottom: 2.5rem;
    }

    .section-header {
        display: flex;
        align-items: center; gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px dashed var(--border-line);
    }

    .section-header i {
        color: #3b82f6;
    }

    .section-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-muted-dark);
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    /* Form Design */
    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .input-wrapper {
        position: relative;
    }

    .form-input {
        width: 100%;
        padding: 0.85rem 1rem;
        border-radius: 14px;
        background: rgba(0,0,0,0.03);
        border: 1px solid var(--border-glass);
        color: var(--text-main);
        font-size: 0.95rem;
        transition: all 0.2s;
        outline: none;
    }

    .form-input:focus {
        background: rgba(59, 130, 246, 0.05);
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .form-input:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background: rgba(0,0,0,0.08);
    }

    textarea.form-input { resize: none; }

    .btn-submit {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: #fff;
        padding: 1rem 2rem;
        border-radius: 16px;
        border: none;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        justify-content: center;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(37, 99, 235, 0.4);
    }

    .btn-submit:active { transform: scale(0.98); }

    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border-left: 4px solid #10b981;
        padding: 1.25rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        color: #10b981;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
</style>

<div class="profile-container">
    @if(session('success'))
        <div class="alert-success">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <strong style="display:block; font-size:0.95rem;">Berhasil!</strong>
                <span style="font-size:0.85rem;">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="profile-card">
        <div class="profile-banner"></div>
        
        <div class="profile-header-content">
            <div class="avatar-wrapper">
                <div class="avatar-inner">
                    {{ substr($user->name, 0, 1) }}
                </div>
            </div>
            <div class="user-meta">
                <h1>{{ $user->name }}</h1>
                <p>{{ $user->position->name ?? 'Karyawan' }} &bull; {{ $user->department->name ?? 'Staf Umum' }}</p>
                <div class="badge-group">
                    <span class="status-badge badge-verified">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 11.37c1.51.32 3.754 2.021 4.733 4.381 1.553-7.017 9.52-11.239 11.735-12.807-3.911 1.702-9.313 4.462-10.95 11.035-1.571-1.605-2.86-2.323-5.518-2.609z"/></svg>
                        Aktif
                    </span>
                    @if($user->face_reference_path)
                    <span class="status-badge badge-verified">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Wajah Terdaftar
                    </span>
                    @else
                    <span class="status-badge badge-pending">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Daftarkan Wajah
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="profile-body">
            @csrf @method('PUT')
            
            <div class="section-grid">
                {{-- KIRI --}}
                <div class="info-sidebar">
                    <div class="info-section">
                        <div class="section-header">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span class="section-title">Informasi Pekerjaan</span>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email Institusi</label>
                            <input type="text" class="form-input" value="{{ $user->email }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">NIK / Nomor Karyawan</label>
                            <input type="text" class="form-input" value="{{ $user->nik ?? 'Belum Diatur' }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Mulai Bergabung</label>
                            <input type="text" class="form-input" value="{{ $user->joined_at ? date('d F Y', strtotime($user->joined_at)) : 'Data tidak tersedia' }}" disabled>
                        </div>
                    </div>

                    <div class="info-section">
                        <div class="section-header">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span class="section-title">Keamanan</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-input" placeholder="••••••••">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                {{-- KANAN --}}
                <div class="info-main">
                    <div class="info-section">
                        <div class="section-header">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span class="section-title">Biodata Personal</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nomor Handphone (WhatsApp)</label>
                            <input type="text" name="phone_number" class="form-input" value="{{ old('phone_number', $user->phone_number) }}" placeholder="Contoh: 08123456789">
                        </div>

                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                            <div class="form-group">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="gender" class="form-input">
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="birth_date" class="form-input" value="{{ old('birth_date', $user->birth_date) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Alamat Domisili</label>
                            <textarea name="address" class="form-input" rows="4" placeholder="Alamat lengkap saat ini...">{{ old('address', $user->address) }}</textarea>
                        </div>
                    </div>
                    
                    <div style="text-align: right; margin-top: 2rem;">
                        <button type="submit" class="btn-submit">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
