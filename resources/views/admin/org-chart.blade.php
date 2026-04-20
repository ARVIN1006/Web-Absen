@extends('layouts.app')

@section('content')
<style>
    .org-wrap { max-width: 1200px; margin: 0 auto; padding: 24px; }
    .org-title { margin-bottom: 32px; text-align: center; }
    
    .org-tree { display: flex; flex-direction: column; align-items: center; gap: 40px; }
    .org-root { background: #3b82f6; color: white; padding: 16px 32px; border-radius: 16px; font-weight: 700; font-size: 18px; box-shadow: 0 10px 25px rgba(59,130,246,0.3); }
    
    .org-depts { display: flex; gap: 24px; flex-wrap: wrap; justify-content: center; width: 100%; }
    .org-dept-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 20px; width: 280px; padding: 20px; }
    .org-dept-name { font-weight: 700; font-size: 14px; text-transform: uppercase; color: var(--text-muted-dark); border-bottom: 1px solid var(--border-glass); padding-bottom: 12px; margin-bottom: 16px; text-align: center; }
    
    .org-member { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding: 10px; border-radius: 12px; background: rgba(255,255,255,0.05); }
    .org-member:last-child { margin-bottom: 0; }
    .org-member-avatar { width: 32px; height: 32px; border-radius: 8px; background: #8b5cf6; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
    .org-member-info { min-width: 0; }
    .org-member-name { font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .org-member-pos { font-size: 11px; color: var(--text-muted); }
</style>

<div class="org-wrap">
    <div class="org-title">
        <h1>Struktur Organisasi</h1>
        <p style="color:var(--text-muted);">Hierarki perusahaan berdasarkan departemen</p>
    </div>

    <div class="org-tree">
        <div class="org-root">DIREKTUR UTAMA / CEO</div>
        
        <div class="org-depts">
            @foreach($departments as $dept)
            <div class="org-dept-card">
                <div class="org-dept-name">{{ $dept->name }}</div>
                @foreach($dept->users as $user)
                <div class="org-member">
                    <div class="org-member-avatar">{{ substr($user->name, 0, 1) }}</div>
                    <div class="org-member-info">
                        <div class="org-member-name">{{ $user->name }}</div>
                        <div class="org-member-pos">{{ $user->position->name ?? '-' }}</div>
                    </div>
                </div>
                @endforeach
                @if($dept->users->isEmpty())
                    <p style="text-align:center; font-size:12px; color:var(--text-muted);">Belum ada anggota</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
