@extends('layouts.app')

@section('content')
<style>
    .prof-wrap { max-width:800px; margin:0 auto; padding:28px 20px 40px; }
    .prof-header { margin-bottom:24px; }
    .prof-title { font-size:24px; font-weight:700; color:var(--text-main); margin:0 0 6px; }
    .prof-subtitle { font-size:13.5px; color:var(--text-muted-dark); margin:0; }

    .prof-card { 
        background:var(--bg-glass); border:1px solid var(--border-glass); 
        border-radius:18px; padding:24px; margin-bottom:20px;
        transition: all 0.3s;
    }
    
    .prof-card header { margin-bottom:20px; }
    .prof-card header h2 { font-size:16px; font-weight:600; color:var(--text-main); margin:0 0 4px; }
    .prof-card header p { font-size:13px; color:var(--text-muted); margin:0; }

    .form-group { margin-bottom:16px; }
    .form-label { display:block; font-size:12.5px; font-weight:600; color:var(--text-muted); margin-bottom:6px; }
    .form-input { 
        width:100%; background:rgba(0,0,0,0.02); border:1.5px solid var(--border-line); 
        border-radius:12px; padding:12px 14px; color:var(--text-main); font-size:14.5px; 
        font-family:'Inter',sans-serif; outline:none; transition:all .2s;
    }
    [data-theme="dark"] .form-input { background:rgba(255,255,255,0.03); }
    .form-input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.15); }
    
    .form-error { color:#ef4444; font-size:12px; margin-top:6px; }
    .form-status { font-size:13px; color:#10b981; font-weight:600; display:flex; align-items:center; gap:6px; }

    .btn-save {
        padding:12px 20px; border-radius:12px; border:none; cursor:pointer;
        font-size:14px; font-weight:600; color:#fff; background:linear-gradient(135deg,#3b82f6,#2563eb);
        box-shadow:0 4px 16px rgba(59,130,246,0.3); transition:transform .15s;
    }
    .btn-save:hover { transform:translateY(-1px); }
    
    .btn-danger {
        padding:12px 20px; border-radius:12px; border:none; cursor:pointer;
        font-size:14px; font-weight:600; color:#fff; background:linear-gradient(135deg,#ef4444,#dc2626);
        box-shadow:0 4px 16px rgba(239,68,68,0.3); transition:transform .15s;
    }
    .btn-danger:hover { transform:translateY(-1px); }
    .btn-cancel {
        padding:12px 20px; border-radius:12px; cursor:pointer; text-decoration:none;
        font-size:14px; font-weight:600; color:var(--text-main); background:transparent; border:1px solid var(--border-line);
        transition:all .15s;
    }
    .btn-cancel:hover { background:var(--hover-bg); }

    .flex-actions { display:flex; align-items:center; gap:12px; }

    /* Modal for Delete */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); backdrop-filter:blur(4px); z-index:999; align-items:center; justify-content:center; padding:20px; }
    .modal-box { background:var(--dropdown-bg); border:1px solid var(--border-glass); border-radius:20px; padding:24px; max-width:480px; width:100%; box-shadow:0 20px 40px rgba(0,0,0,0.4); }
    .modal-title { font-size:18px; font-weight:700; color:var(--text-main); margin:0 0 8px; }
    .modal-text { font-size:13.5px; color:var(--text-muted); margin:0 0 20px; line-height:1.6; }

    @media (max-width:767px) {
        .prof-wrap { padding:20px 16px; }
        .prof-card { padding:20px; }
    }
</style>

<div class="prof-wrap">
    <div class="prof-header">
        <h1 class="prof-title">Profil Karyawan</h1>
        <p class="prof-subtitle">Kelola informasi akun dan kata sandi Anda</p>
    </div>

    <div class="prof-card">
        @include('profile.partials.update-profile-information-form')
    </div>

    <div class="prof-card">
        @include('profile.partials.update-password-form')
    </div>

    <div class="prof-card" style="border-color:rgba(239,68,68,0.3);">
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection
