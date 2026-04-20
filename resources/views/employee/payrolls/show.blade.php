@extends('layouts.app')

@section('content')
<style>
    .slip-wrap { max-width: 600px; margin: 40px auto; padding: 24px; }
    .slip-card { background: white; color: #1e293b; border-radius: 0; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-top: 8px solid #3b82f6; position: relative; }
    .slip-header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; }
    .slip-logo { font-weight: 800; font-size: 24px; color: #3b82f6; margin-bottom: 4px; }
    
    .slip-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 40px; font-size: 14px; }
    .slip-meta b { color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px; }
    
    .slip-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
    .slip-table th { text-align: left; padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 12px; }
    .slip-table td { padding: 16px 0; border-bottom: 1px solid #f1f5f9; font-size: 15px; }
    
    .slip-total { display: flex; justify-content: space-between; align-items: center; padding: 20px; background: #f8fafc; border-radius: 12px; margin-top: 20px; }
    .slip-total b { font-size: 14px; color: #64748b; }
    .slip-total span { font-size: 20px; font-weight: 800; color: #1e293b; }
    
    .slip-footer { margin-top: 40px; text-align: center; font-size: 12px; color: #94a3b8; }
    
    @media print {
        .nav, .sidebar, .btn-print { display: none !important; }
        .slip-wrap { margin: 0; padding: 0; max-width: 100%; }
        .slip-card { box-shadow: none; border: 1px solid #eee; }
    }
</style>

<div class="slip-wrap">
    <div style="display:flex; justify-content:space-between; margin-bottom:16px;">
        <a href="{{ route('payrolls.user_index') }}" style="text-decoration:none; color:#64748b; font-size:14px;">&larr; Kembali</a>
        <button onclick="window.print()" class="btn-print" style="background:#3b82f6; color:white; border:none; padding:8px 16px; border-radius:8px; cursor:pointer; font-weight:600;">Cetak Slip</button>
    </div>

    <div class="slip-card">
        <div class="slip-header">
            <div class="slip-logo">HRIS DEMO</div>
            <div style="font-size:14px; color:#64748b;">Slip Gaji Karyawan</div>
            <div style="font-weight:700; margin-top:10px; font-size:18px;">Periode {{ date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) }}</div>
        </div>

        <div class="slip-meta">
            <div>
                <b>Nama Karyawan</b>
                {{ $payroll->user->name }}
            </div>
            <div>
                <b>Jabatan</b>
                {{ $payroll->user->position->name ?? '-' }}
            </div>
            <div>
                <b>ID Karyawan</b>
                #EMP{{ str_pad($payroll->user->id, 4, '0', STR_PAD_LEFT) }}
            </div>
            <div>
                <b>Status Pembayaran</b>
                <span style="color: {{ $payroll->status == 'paid' ? '#10b981' : '#f59e0b' }}; font-weight: 700;">{{ strtoupper($payroll->status) }}</span>
            </div>
        </div>

        <table class="slip-table">
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th style="text-align:right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Gaji Pokok</td>
                    <td style="text-align:right;">Rp {{ number_format($payroll->basic_salary, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Tunjangan Lembur (Otomatis)</td>
                    <td style="text-align:right;">Rp {{ number_format($payroll->overtime_pay, 0, ',', '.') }}</td>
                </tr>
                @if($payroll->bonus > 0)
                <tr>
                    <td>Bonus / Insentif</td>
                    <td style="text-align:right;">Rp {{ number_format($payroll->bonus, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($payroll->deductions > 0)
                <tr>
                    <td>Potongan</td>
                    <td style="text-align:right; color:#ef4444;">- Rp {{ number_format($payroll->deductions, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="slip-total">
            <b>Total Gaji Bersih (Net)</b>
            <span>Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</span>
        </div>

        <div class="slip-footer">
            Dokumen ini dihasilkan secara otomatis oleh sistem HRIS pada {{ date('d/m/Y H:i') }}<br>
            Tidak memerlukan tanda tangan basah.
        </div>
    </div>
</div>
@endsection
