<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        h1 { margin: 0 0 4px; font-size: 20px; }
        .period { margin-bottom: 18px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px; vertical-align: top; }
        th { background: #eef2ff; text-align: left; font-size: 10px; text-transform: uppercase; }
    </style>
</head>
<body>
    <h1>Laporan Absensi</h1>
    <div class="period">Periode: {{ $period }}</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Jabatan</th>
                <th>Tipe</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendances as $attendance)
                @php
                    $position = $attendance->user?->position;
                    $positionName = is_object($position) ? ($position->name ?? '-') : ($position ?: '-');
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $attendance->user?->name ?? '-' }}</td>
                    <td>{{ $attendance->user?->email ?? '-' }}</td>
                    <td>{{ $positionName }}</td>
                    <td>{{ $attendance->type === 'in' ? 'Masuk' : 'Pulang' }}</td>
                    <td>{{ optional($attendance->attendance_date ?? $attendance->created_at)->format('Y-m-d') }}</td>
                    <td>{{ optional($attendance->created_at)->format('H:i:s') }}</td>
                    <td>{{ $attendance->status }}</td>
                    <td>{{ $attendance->latitude }}, {{ $attendance->longitude }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Tidak ada data absensi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
