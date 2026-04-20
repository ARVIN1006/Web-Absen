@extends('layouts.app')

@section('content')
<style>
    .rep-wrap { max-width: 1000px; margin: 0 auto; padding: 24px; }
    .rep-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 20px; padding: 24px; margin-bottom: 24px; }
    .table { width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; }
    .table th { padding: 14px; color: var(--text-muted-dark); border-bottom: 1px solid var(--border-glass); font-size: 11px; text-transform: uppercase; }
    .table td { padding: 14px; border-bottom: 1px solid var(--border-glass); color: var(--text-main); }
</style>

<div class="rep-wrap">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:32px;">
        <h1>Laporan Biaya Tenaga Kerja</h1>
        <form action="" method="GET">
            <select name="year" onchange="this.form.submit()" style="padding:10px; border-radius:10px; background:var(--bg-glass); color:var(--text-main); border:1px solid var(--border-glass);">
                <option value="2024" {{ $year == 2024 ? 'selected' : '' }}>2024</option>
                <option value="2025" {{ $year == 2025 ? 'selected' : '' }}>2025</option>
                <option value="2026" {{ $year == 2026 ? 'selected' : '' }}>2026</option>
            </select>
        </form>
    </div>

    <div class="rep-card">
        <h3 style="margin-top:0;">Ringkasan Pengeluaran {{ $year }}</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Total Gaji Pokok</th>
                    <th>Total Lembur</th>
                    <th>Total Pengeluaran (Net)</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach($monthlyCosts as $cost)
                <tr>
                    <td>{{ date('F', mktime(0, 0, 0, $cost->month, 1)) }}</td>
                    <td>Rp {{ number_format($cost->total_basic, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($cost->total_overtime, 0, ',', '.') }}</td>
                    <td><b>Rp {{ number_format($cost->total_net, 0, ',', '.') }}</b></td>
                </tr>
                @php $grandTotal += $cost->total_net; @endphp
                @endforeach
                @if($monthlyCosts->isEmpty())
                <tr><td colspan="4" style="text-align:center; padding:40px; color:var(--text-muted);">Belum ada data pengeluaran yang tercatat (Status: PAID).</td></tr>
                @endif
            </tbody>
            @if(!$monthlyCosts->isEmpty())
            <tfoot>
                <tr style="background:rgba(59,130,246,0.05);">
                    <td colspan="3" style="text-align:right; font-weight:700;">TOTAL TAHUNAN</td>
                    <td style="font-size:18px; font-weight:800; color:#3b82f6;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
