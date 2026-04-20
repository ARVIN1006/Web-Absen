@extends('layouts.app')

@section('content')
<style>
    .heat-wrap { max-width: 1000px; margin: 0 auto; padding: 24px; }
    .heat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
    .heat-card { background: var(--bg-glass); border: 1px solid var(--border-glass); border-radius: 20px; padding: 24px; text-align: center; position: relative; overflow: hidden; }
    
    .heat-score { font-size: 36px; font-weight: 800; margin-bottom: 8px; position: relative; z-index: 1; }
    .heat-name { font-size: 14px; font-weight: 700; color: var(--text-muted-dark); text-transform: uppercase; }
    
    .heat-bg { position: absolute; bottom: 0; left: 0; width: 100%; transition: 0.5s; z-index: 0; }
</style>

<div class="heat-wrap">
    <div style="margin-bottom:32px; text-align:center;">
        <h1>Performance Heatmap</h1>
        <p style="color:var(--text-muted);">Visualisasi produktivitas antar departemen.</p>
    </div>

    <div class="heat-grid">
        @foreach($heatData as $data)
        @php 
            $color = '#ef4444'; // Red
            if($data['avg'] >= 80) $color = '#10b981'; // Green
            elseif($data['avg'] >= 60) $color = '#f59e0b'; // Orange
        @endphp
        <div class="heat-card">
            <div class="heat-bg" style="height: {{ $data['avg'] }}%; background: {{ $color }}; opacity: 0.1;"></div>
            <div class="heat-score" style="color: {{ $color }};">{{ number_format($data['avg'], 1) }}</div>
            <div class="heat-name">{{ $data['dept'] }}</div>
            <div style="font-size:11px; color:var(--text-muted); margin-top:8px;">{{ $data['count'] }} Penilaian</div>
        </div>
        @endforeach
    </div>

    <div style="margin-top:40px; padding:24px; background:var(--bg-glass); border:1px solid var(--border-glass); border-radius:20px;">
        <h3>Keterangan</h3>
        <div style="display:flex; gap:24px; font-size:13px;">
            <div style="display:flex; align-items:center; gap:8px;"><span style="width:12px; height:12px; border-radius:3px; background:#10b981;"></span> 80-100: Produktivitas Tinggi</div>
            <div style="display:flex; align-items:center; gap:8px;"><span style="width:12px; height:12px; border-radius:3px; background:#f59e0b;"></span> 60-79: Produktivitas Sedang</div>
            <div style="display:flex; align-items:center; gap:8px;"><span style="width:12px; height:12px; border-radius:3px; background:#ef4444;"></span> <60: Produktivitas Menurun</div>
        </div>
    </div>
</div>
@endsection
