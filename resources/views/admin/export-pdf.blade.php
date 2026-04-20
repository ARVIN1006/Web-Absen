<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #222; padding-bottom: 10px; }
        .header h1 { margin: 0 0 5px; font-size: 20px; }
        .header p { margin: 0; font-size: 12px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; }
        .text-center { text-align: center; }
        .badge-valid { color: #059669; font-weight: bold; }
        .badge-invalid { color: #dc2626; font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; font-size: 11px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Laporan Rekapitulasi Absensi Karyawan</p>
        <p style="margin-top: 5px;"><strong>Periode:</strong> {{ $period }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Karyawan</th>
                <th width="15%">Jabatan</th>
                <th width="15%">Tanggal</th>
                <th width="10%">Waktu</th>
                <th width="15%">Tipe</th>
                <th width="20%">Status Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $att)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $att->user->name }}</td>
                <td>{{ $att->user->position ?? '-' }}</td>
                <td>{{ $att->created_at->translatedFormat('d F Y') }}</td>
                <td>{{ $att->created_at->format('H:i') }}</td>
                <td>{{ $att->type == 'in' ? 'Masuk' : 'Pulang' }}</td>
                <td>
                    <span class="{{ $att->status == 'valid' ? 'badge-valid' : 'badge-invalid' }}">
                        {{ strtoupper($att->status) }}
                    </span>
                    <br>
                    <span style="font-size: 9px; color: #777;">{{ $att->latitude }}, {{ $att->longitude }}</span>
                </td>
            </tr>
            @endforeach
            @if(count($attendances) == 0)
            <tr>
                <td colspan="7" class="text-center">Tidak ada data absensi untuk periode ini.</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
    </div>

</body>
</html>
