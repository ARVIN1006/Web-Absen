@extends('layouts.app')

@section('content')
<style>
    .admin-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .admin-header {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    .admin-header h1 {
        font-size: 1.875rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--text-main) 0%, var(--text-muted) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #3b82f6;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
        transition: all 0.2s;
    }
    .btn-back:hover { transform: translateX(-4px); }

    /* Bento Form Sections */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
    }

    .form-section {
        background: var(--bg-glass);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--border-glass);
        border-radius: 24px;
        padding: 1.75rem;
        box-shadow: 0 4px 20px var(--shadow-drop);
    }

    .section-title {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-muted-dark);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 1px dashed var(--border-line);
        padding-bottom: 0.75rem;
    }

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

    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        background: rgba(0,0,0,0.03);
        border: 1px solid var(--border-glass);
        color: var(--text-main);
        font-size: 0.9rem;
        transition: all 0.2s;
        outline: none;
    }

    .form-input:focus {
        background: rgba(59, 130, 246, 0.05);
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
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
        margin-top: 1rem;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(37, 99, 235, 0.4);
    }

    .error-text { color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; }
</style>

<div class="admin-container">
    <a href="{{ route('admin.employees.index') }}" class="btn-back">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Daftar
    </a>

    <div class="admin-header">
        <div>
            <h1>Edit Karyawan</h1>
            <p style="color: var(--text-muted); margin: 0.25rem 0 0;">Update informasi detail untuk <strong>{{ $employee->name }}</strong></p>
        </div>
    </div>

    <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-grid">
            {{-- SEKSI 1: PROFIL & KEAMANAN --}}
            <div class="form-section">
                <div class="section-title">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil & Keamanan
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $employee->name) }}" required>
                    @error('name') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email Perusahaan</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $employee->email) }}" required>
                    @error('email') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">NIK (KTP)</label>
                    <input type="text" name="nik" class="form-input" value="{{ old('nik', $employee->nik) }}" placeholder="16 Digit NIK">
                </div>

                <div class="form-group">
                    <label class="form-label">Ganti Password (Kosongkan jika tidak berubah)</label>
                    <input type="password" name="password" class="form-input" placeholder="••••••••">
                    @error('password') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="gender" class="form-input">
                            <option value="">Pilih...</option>
                            <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="form-input" value="{{ old('birth_date', $employee->birth_date) }}">
                    </div>
                </div>
            </div>

            {{-- SEKSI 2: PENEMPATAN & JADWAL --}}
            <div class="form-section">
                <div class="section-title">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Struktur Organisasi
                </div>

                <div class="form-group">
                    <label class="form-label">Departemen</label>
                    <select name="department_id" class="form-input">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Jabatan / Posisi</label>
                    <select name="position_id" class="form-input" required>
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="section-title" style="border:none; margin-top: 1rem; margin-bottom: 0.5rem; padding: 0;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Jadwal Kerja
                    </label>
                    <label class="form-label">Shift Default</label>
                    <select name="work_shift_id" class="form-input">
                        <option value="">-- Pilih Shift Kerja --</option>
                        @foreach($workShifts as $shift)
                            <option value="{{ $shift->id }}" {{ old('work_shift_id', $employee->work_shift_id) == $shift->id ? 'selected' : '' }}>
                                {{ $shift->name }} ({{ date('H:i', strtotime($shift->start_time)) }} - {{ date('H:i', strtotime($shift->end_time)) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Tanggal Bergabung</label>
                        <input type="date" name="joined_at" class="form-input" value="{{ old('joined_at', $employee->joined_at) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Akhir Kontrak</label>
                        <input type="date" name="contract_end_at" class="form-input" value="{{ old('contract_end_at', $employee->contract_end_at) }}">
                    </div>
                </div>
            </div>

            {{-- SEKSI 3: KONTAK & ALAMAT (FULL WIDTH) --}}
            <div class="form-section" style="grid-column: span 2;">
                <div class="section-title">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    Kontak & Alamat
                </div>
                <div class="form-grid" style="grid-template-columns: 1fr 2fr;">
                    <div>
                        <div class="form-group">
                            <label class="form-label">Nomor WhatsApp/HP</label>
                            <input type="text" name="phone_number" class="form-input" value="{{ old('phone_number', $employee->phone_number) }}" placeholder="0812...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nomor NPWP</label>
                            <input type="text" name="npwp" class="form-input" value="{{ old('npwp', $employee->npwp) }}" placeholder="00.000.000.0-000.000">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat Lengkap Sesuai Domisili</label>
                        <textarea name="address" class="form-input" rows="4" placeholder="Jl. Nama Jalan No. 123, Kelurahan, Kecamatan, Kota...">{{ old('address', $employee->address) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 2rem; gap: 1rem;">
            <button type="submit" class="btn-submit" style="width: auto; padding: 1rem 3rem;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                Simpan Perubahan Data Karyawan
            </button>
        </div>
    </form>
</div>
@endsection
