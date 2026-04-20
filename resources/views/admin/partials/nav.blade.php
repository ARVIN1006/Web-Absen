<div class="admin-nav">
    <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('admin.departments.index') }}" class="admin-nav-item {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">Departemen</a>
    <a href="{{ route('admin.work-shifts.index') }}" class="admin-nav-item {{ request()->routeIs('admin.work-shifts.*') ? 'active' : '' }}">Shift Kerja</a>
    <a href="{{ route('admin.leave-types.index') }}" class="admin-nav-item {{ request()->routeIs('admin.leave-types.*') ? 'active' : '' }}">Tipe Cuti</a>
    <a href="{{ route('admin.leave-requests.index') }}" class="admin-nav-item {{ request()->routeIs('admin.leave-requests.*') ? 'active' : '' }}">Pengajuan Cuti</a>
    <a href="{{ route('admin.employees.index') }}" class="admin-nav-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">Kelola Karyawan</a>
    <a href="{{ route('admin.locations.index') }}" class="admin-nav-item {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">Lokasi Perusahaan</a>
    <a href="{{ route('admin.attendances.index') }}" class="admin-nav-item {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">Laporan Absensi</a>
</div>
