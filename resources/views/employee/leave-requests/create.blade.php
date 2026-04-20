@extends('layouts.app')

@section('content')
<style>
    .db-wrap { max-width:700px; margin:0 auto; padding:28px 20px 20px; }
    .db-header { margin-bottom: 24px; }
    .db-header h1 { font-size: 24px; margin: 0 0 8px; font-weight: 700; color: var(--text-main); }
    .db-header p { font-size: 14px; margin: 0; color: var(--text-muted); }
    
    .btn-back { display: inline-block; margin-bottom: 16px; font-size: 13px; color: #3b82f6; text-decoration: none; font-weight:600;}

    .form-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 16px; padding: 24px; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; }
    .form-control { width: 100%; padding: 12px 14px; border: 1px solid var(--border-glass); border-radius: 10px; background: rgba(0,0,0,0.05); color: var(--text-main); font-size: 14px; outline: none; }
    [data-theme="light"] .form-control { background: #f9fafb; border: 1px solid #e5e7eb; }
    .form-control:focus { border-color: #3b82f6; }
    .text-danger { color: #ef4444; font-size: 12px; margin-top: 4px; display: block; }
    
    .btn-primary { width: 100%; padding: 14px; border-radius: 10px; background: #3b82f6; color: white; border: none; font-weight: 600; font-size: 14px; cursor: pointer; transition: background 0.2s; }
    .btn-primary:hover { background: #2563eb; }

    .help-text { font-size: 12px; color: var(--text-muted-dark); margin-top: 4px; display: block; }
</style>

<div class="db-wrap">
    <a href="{{ route('employee.leave-requests.index') }}" class="btn-back">&larr; Kembali ke Riwayat Cuti</a>
    
    <div class="db-header">
        <h1>Formulir Pengajuan Cuti</h1>
        <p>Isi formulir di bawah ini untuk mengajukan cuti atau izin.</p>
    </div>

    @if($errors->any())
        <div style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 500;">
            <ul style="margin:0; padding-left:20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <form action="{{ route('employee.leave-requests.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Tipe Cuti</label>
                <select name="leave_type_id" id="leave_type_id" class="form-control" required onchange="checkAttachment()">
                    <option value="">-- Pilih Tipe Cuti --</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}" data-req-attach="{{ $type->requires_attachment }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} (Maks. {{ $type->max_days_per_year }} Hari/Tahun)
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label class="form-label">Mulai Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required min="{{ date('Y-m-d') }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required min="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alasan Cuti/Izin</label>
                <textarea name="reason" class="form-control" rows="4" required placeholder="Jelaskan alasan secara singkat...">{{ old('reason') }}</textarea>
            </div>

            <div class="form-group" id="attachmentGroup" style="display:none;">
                <label class="form-label">Upload Lampiran <span style="color:#ef4444;">*</span></label>
                <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                <span class="help-text">Wajib diupload untuk tipe cuti ini. Format: JPG, PNG, PDF (Maks. 2MB).</span>
            </div>

            <button type="submit" class="btn-primary">Kirim Pengajuan Cuti</button>
        </form>
    </div>
</div>

<script>
    function checkAttachment() {
        const select = document.getElementById('leave_type_id');
        const selectedOpt = select.options[select.selectedIndex];
        const attachmentGroup = document.getElementById('attachmentGroup');
        const attachmentInput = attachmentGroup.querySelector('input');
        
        if (selectedOpt && selectedOpt.dataset.reqAttach == "1") {
            attachmentGroup.style.display = 'block';
            attachmentInput.required = true;
        } else {
            attachmentGroup.style.display = 'none';
            attachmentInput.required = false;
        }
    }
    
    // Run on load to handle old input state
    document.addEventListener('DOMContentLoaded', checkAttachment);
</script>
@endsection
