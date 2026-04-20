@extends('layouts.app')

@section('content')
<style>
    .admin-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .admin-header h1 {
        font-size: 1.875rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--text-main) 0%, var(--text-muted) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0;
    }

    /* Bento Stats */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--bg-glass);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--border-glass);
        border-radius: 24px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        box-shadow: 0 4px 20px var(--shadow-drop);
    }

    .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Table Styling */
    .table-card {
        background: var(--bg-glass);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--border-glass);
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 8px 32px var(--shadow-drop);
    }

    .table-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border-line);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(0,0,0,0.02);
    }

    .search-input {
        background: rgba(0,0,0,0.05);
        border: 1px solid var(--border-line);
        border-radius: 12px;
        padding: 0.6rem 1rem;
        font-size: 0.85rem;
        color: var(--text-main);
        outline: none;
        width: 300px;
        transition: all 0.2s;
    }

    .search-input:focus { border-color: #3b82f6; background: rgba(59,130,246,0.05); }

    .premium-table {
        width: 100%;
        border-collapse: collapse;
    }

    .premium-table th {
        padding: 1.25rem 2rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-muted-dark);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: rgba(0,0,0,0.03);
    }

    .premium-table td {
        padding: 1.25rem 2rem;
        border-bottom: 1px solid var(--border-line);
        vertical-align: middle;
        transition: all 0.2s;
    }

    .premium-table tr:hover td {
        background: var(--hover-bg);
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
    }

    .status-pill {
        padding: 0.35rem 0.75rem;
        border-radius: 99px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .pill-green { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .pill-red { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .pill-blue { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }

    .btn-action {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border-line);
        background: var(--bg-body);
        color: var(--text-muted);
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-edit:hover { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    .btn-delete:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

    .btn-add {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: #fff;
        padding: 0.75rem 1.5rem;
        border-radius: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        transition: all 0.3s;
    }
    .btn-add:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(37, 99, 235, 0.4); }

    @media (max-width: 1024px) {
        .search-input { width: 100%; margin-top: 1rem; }
        .table-header { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="admin-container">
    <div class="admin-header">
        <h1>Kelola Karyawan</h1>
        <a href="{{ route('admin.employees.create') }}" class="btn-add">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Karyawan
        </a>
    </div>

    {{-- Stats Bento --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <div style="font-size: 0.75rem; color: var(--text-muted-dark); text-transform: uppercase; font-weight: 700;">Total Karyawan</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">{{ count($employees) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <div style="font-size: 0.75rem; color: var(--text-muted-dark); text-transform: uppercase; font-weight: 700;">Wajah Terverifikasi</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">{{ $employees->whereNotNull('face_reference_path')->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <div style="font-size: 0.75rem; color: var(--text-muted-dark); text-transform: uppercase; font-weight: 700;">Departemen</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">{{ $employees->pluck('department_id')->unique()->count() }}</div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; color: #10b981; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span style="font-size: 0.9rem; font-weight: 600;">{{ session('success') }}</span>
        </div>
    @endif

    <div class="table-card">
        <div class="table-header">
            <h2 style="margin:0; font-size: 1.1rem; font-weight: 700;">Daftar Karyawan</h2>
            <input type="text" id="employeeSearch" class="search-input" placeholder="Cari nama atau email...">
        </div>
        <div style="overflow-x: auto;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Jabatan & Dept</th>
                        <th>Detail Kontak</th>
                        <th>Verifikasi Wajah</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="employeeTableBody">
                    @forelse($employees as $emp)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="avatar">{{ substr($emp->name, 0, 1) }}</div>
                                <div>
                                    <div style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">{{ $emp->name }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">Bergabung: {{ $emp->joined_at ? date('d M Y', strtotime($emp->joined_at)) : $emp->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 600; font-size: 0.85rem; color: var(--text-main);">{{ $emp->position->name ?? '-' }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $emp->department->name ?? 'Staf Umum' }}</div>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; color: var(--text-main);">{{ $emp->email }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $emp->phone_number ?? '-' }}</div>
                        </td>
                        <td>
                            @if($emp->face_reference_path)
                                <span class="status-pill pill-green">Terdaftar</span>
                            @else
                                <span class="status-pill pill-red">Belum Ada</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <a href="{{ route('admin.employees.edit', $emp->id) }}" class="btn-action btn-edit" title="Edit">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('Hapus karyawan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete" title="Hapus">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 4rem; color: var(--text-muted);">
                            <div style="font-size: 2rem; margin-bottom: 1rem;">👥</div>
                            <p>Belum ada data karyawan terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('employeeSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#employeeTableBody tr');
        
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    });
</script>
@endsection
