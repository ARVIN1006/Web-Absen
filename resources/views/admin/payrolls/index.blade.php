@extends('layouts.app')

@section('content')
<style>
    .pay-wrap { max-width: 1200px; margin: 0 auto; padding: 24px; }
    .pay-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    
    .pay-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 20px; overflow: hidden; }
    .table-resp { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; }
    .table th { padding: 14px 20px; color: var(--text-muted-dark); font-weight: 600; font-size: 11px; text-transform: uppercase; border-bottom: 1px solid var(--border-glass); background: rgba(0,0,0,0.02); }
    .table td { padding: 14px 20px; border-bottom: 1px solid var(--border-glass); color: var(--text-main); vertical-align: middle; }
    
    .badge { padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .b-draft { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .b-paid { background: rgba(16, 185, 129, 0.15); color: #10b981; }

    .btn-action { background: #3b82f6; color: white; padding: 8px 16px; border-radius: 10px; border: none; font-weight: 600; cursor: pointer; text-decoration: none; font-size: 13px; }
</style>

<div class="pay-wrap">
    <div class="pay-header">
        <h1>Manajemen Payroll</h1>
        <form action="{{ route('admin.payrolls.generate') }}" method="POST" style="display:flex; gap:10px; align-items:center;">
            @csrf
            <select name="month" class="form-control" style="padding: 10px; border-radius: 10px; border: 1px solid var(--border-glass); background: var(--bg-glass); color: var(--text-main);">
                @for($i=1; $i<=12; $i++)
                    <option value="{{ $i }}" {{ $i == $month ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                @endfor
            </select>
            <select name="year" class="form-control" style="padding: 10px; border-radius: 10px; border: 1px solid var(--border-glass); background: var(--bg-glass); color: var(--text-main);">
                @for($i=2024; $i<=2026; $i++)
                    <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            <button type="submit" class="btn-action">Generate Payroll</button>
        </form>
    </div>

    @if(session('success'))
        <div style="background: rgba(16,185,129,0.15); color: #10b981; padding: 16px; border-radius: 16px; margin-bottom: 24px;">{{ session('success') }}</div>
    @endif

    <div class="pay-card">
        <div class="table-resp">
            <table class="table">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Gaji Pokok</th>
                        <th>Lembur</th>
                        <th>Total Bersih</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrolls as $pay)
                    <tr>
                        <td>
                            <b>{{ $pay->user->name }}</b><br>
                            <span style="font-size:12px; color:var(--text-muted);">{{ $pay->user->position->name ?? '-' }}</span>
                        </td>
                        <td>Rp {{ number_format($pay->basic_salary, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($pay->overtime_pay, 0, ',', '.') }}</td>
                        <td><b>Rp {{ number_format($pay->net_salary, 0, ',', '.') }}</b></td>
                        <td><span class="badge b-{{ $pay->status }}">{{ $pay->status }}</span></td>
                        <td>
                            @if($pay->status == 'draft')
                                <form action="{{ route('admin.payrolls.updateStatus', $pay->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-action" style="background:#10b981;">Bayar</button>
                                </form>
                            @else
                                <span style="font-size:12px; color:var(--text-muted);">Dibayar: {{ $pay->paid_at ? date('d/m/y', strtotime($pay->paid_at)) : '-' }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if(count($payrolls) == 0)
                    <tr><td colspan="6" style="text-align:center; padding:32px; color:var(--text-muted);">Belum ada data payroll untuk periode ini.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
