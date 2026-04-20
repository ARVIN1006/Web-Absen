@extends('layouts.app')

@section('content')
<style>
    .dir-wrap { max-width: 1200px; margin: 0 auto; padding: 24px; }
    .dir-header { margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center; }
    .search-box { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 12px; padding: 10px 20px; width: 300px; color: var(--text-main); }
    
    .dir-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
    .employee-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 20px; padding: 24px; transition: 0.3s; display: flex; align-items: center; gap: 16px; }
    .employee-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-color: #3b82f6; }
    
    .emp-avatar { width: 60px; height: 60px; border-radius: 15px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; flex-shrink: 0; }
    .emp-info { min-width: 0; }
    .emp-name { font-weight: 700; font-size: 16px; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .emp-pos { font-size: 12px; color: #3b82f6; font-weight: 600; margin-bottom: 2px; }
    .emp-dept { font-size: 11px; color: var(--text-muted); }
    
    .filter-pills { display: flex; gap: 10px; margin-bottom: 24px; overflow-x: auto; padding-bottom: 10px; }
    .pill { padding: 6px 16px; border-radius: 20px; background: var(--bg-glass); border: 1px solid var(--border-glass); font-size: 12px; font-weight: 600; cursor: pointer; color: var(--text-muted); text-decoration: none; white-space: nowrap; }
    .pill.active { background: #3b82f6; color: white; border-color: #3b82f6; }
</style>

<div class="dir-wrap">
    <div class="dir-header">
        <div>
            <h1 style="margin:0;">Direktori Karyawan</h1>
            <p style="color:var(--text-muted); font-size:14px; margin:4px 0 0;">Temukan dan hubungi rekan kerja Anda.</p>
        </div>
        <form action="" method="GET">
            <input type="text" name="search" class="search-box" placeholder="Cari nama atau jabatan..." value="{{ request('search') }}">
        </form>
    </div>

    <div class="filter-pills">
        <a href="{{ route('company-directory') }}" class="pill {{ !request('dept') ? 'active' : '' }}">Semua Divisi</a>
        @foreach($departments as $dept)
            <a href="?dept={{ $dept->id }}" class="pill {{ request('dept') == $dept->id ? 'active' : '' }}">{{ $dept->name }}</a>
        @endforeach
    </div>

    <div class="dir-grid">
        @foreach($employees as $emp)
        <div class="employee-card">
            <div class="emp-avatar">{{ substr($emp->name, 0, 1) }}</div>
            <div class="emp-info">
                <div class="emp-name">{{ $emp->name }}</div>
                <div class="emp-pos">{{ $emp->position->name ?? '-' }}</div>
                <div class="emp-dept">{{ $emp->department->name ?? '-' }}</div>
                <div style="margin-top:8px; display:flex; gap:8px;">
                    <a href="mailto:{{ $emp->email }}" title="Kirim Email" style="color:var(--text-muted);"><svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></a>
                    @if($emp->phone_number)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $emp->phone_number) }}" target="_blank" title="WhatsApp" style="color:var(--text-muted);"><svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($employees->isEmpty())
        <div style="text-align:center; padding:80px; background:var(--bg-glass); border-radius:24px; color:var(--text-muted);">
            Tidak ada karyawan yang ditemukan.
        </div>
    @endif
</div>
@endsection
