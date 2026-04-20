@extends('layouts.app')

@section('content')
<style>
    .pay-wrap { max-width: 800px; margin: 0 auto; padding: 24px; }
    .pay-header { margin-bottom: 24px; }
    .pay-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 20px; overflow: hidden; }
    .table-resp { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; }
    .table th { padding: 14px 20px; color: var(--text-muted-dark); font-weight: 600; font-size: 11px; text-transform: uppercase; border-bottom: 1px solid var(--border-glass); background: rgba(0,0,0,0.02); }
    .table td { padding: 14px 20px; border-bottom: 1px solid var(--border-glass); color: var(--text-main); vertical-align: middle; }
    .btn-view { color: #3b82f6; text-decoration: none; font-weight: 600; font-size: 13px; }
</style>

<div class="pay-wrap">
    <div class="pay-header">
        <h1>Riwayat Slip Gaji</h1>
        <p style="color:var(--text-muted); font-size:14px;">Berikut adalah riwayat penghasilan bulanan Anda.</p>
    </div>

    <div class="pay-card">
        <div class="table-resp">
            <table class="table">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Gaji Bersih</th>
                        <th>Status</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrolls as $pay)
                    <tr>
                        <td><b>{{ date('F Y', mktime(0, 0, 0, $pay->month, 1, $pay->year)) }}</b></td>
                        <td>Rp {{ number_format($pay->net_salary, 0, ',', '.') }}</td>
                        <td>
                            <span style="color: {{ $pay->status == 'paid' ? '#10b981' : '#f59e0b' }}; font-weight: 700; font-size: 11px;">
                                {{ strtoupper($pay->status) }}
                            </span>
                        </td>
                        <td><a href="{{ route('payrolls.show', $pay->id) }}" class="btn-view">Buka Slip &rarr;</a></td>
                    </tr>
                    @endforeach
                    @if(count($payrolls) == 0)
                    <tr><td colspan="4" style="text-align:center; padding:32px; color:var(--text-muted);">Belum ada slip gaji yang tersedia.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
