@extends('layouts.app')

@section('content')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<style>
    .att-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .att-header {
        margin-bottom: 2rem;
        text-align: left;
    }

    .att-header h1 {
        font-size: 1.875rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--text-main) 0%, var(--text-muted) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }

    .att-header p {
        color: var(--text-muted);
        font-size: 0.95rem;
    }

    /* Bento Layout */
    .att-grid {
        display: grid;
        grid-template-columns: 1fr 1.1fr;
        gap: 1.5rem;
    }

    .glass-card {
        background: var(--bg-glass);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--border-glass);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 8px 32px var(--shadow-drop);
        display: flex;
        flex-direction: column;
    }

    /* Camera Section */
    .cam-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-line);
    }

    .status-badge {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.9rem;
        border-radius: 99px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        transition: all 0.3s ease;
    }

    .cam-viewport {
        position: relative;
        background: #000;
        aspect-ratio: 4/3;
        overflow: hidden;
    }

    video#video { width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); }
    canvas#canvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; transform: scaleX(-1); z-index: 5; }

    .cam-overlay { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; z-index: 10; pointer-events: none; }
    .face-guide { width: 50%; aspect-ratio: 1/1.2; border: 2px dashed rgba(255,255,255,0.3); border-radius: 50%; transition: all 0.3s ease; box-shadow: 0 0 0 9999px rgba(0,0,0,0.4); }
    .face-guide.active { border: 3px solid #10b981; border-style: solid; box-shadow: 0 0 0 9999px rgba(0,0,0,0.2), 0 0 30px rgba(16,185,129,0.4); }

    /* Map Section */
    #map {
        height: 240px;
        width: 100%;
        z-index: 1;
        border-bottom: 1px solid var(--border-line);
    }

    .info-content {
        padding: 1.5rem;
        flex: 1;
    }

    .info-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--text-muted-dark);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 1rem;
        display: block;
    }

    /* Action Buttons */
    .action-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--border-line);
        background: rgba(0,0,0,0.1);
    }

    .action-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .btn-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        padding: 1.25rem;
        border-radius: 20px;
        border: none;
        cursor: pointer;
        font-weight: 700;
        font-size: 0.95rem;
        color: #fff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-in { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3); }
    .btn-out { background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3); }
    .btn-action:disabled { opacity: 0.4; cursor: not-allowed; filter: grayscale(0.5); }
    .btn-action:active:not(:disabled) { transform: scale(0.95); }

    #loadingOverlay {
        position: absolute; inset: 0; background: rgba(15, 17, 23, 0.9);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        z-index: 100; backdrop-filter: blur(8px);
    }

    .spinner { width: 40px; height: 40px; border: 3px solid var(--border-line); border-top-color: #3b82f6; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 1rem; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Demo Button */
    .btn-demo {
        margin-top: 1rem;
        background: rgba(245, 158, 11, 0.1);
        border: 1px dashed #f59e0b;
        color: #f59e0b;
        padding: 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .btn-demo:hover { background: rgba(245, 158, 11, 0.2); }

    @media (max-width: 1024px) {
        .att-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="att-container">
    <header class="att-header">
        <h1>Presensi Kehadiran</h1>
        <p>Verifikasi wajah dan lokasi kantor dalam satu sistem terintegrasi.</p>
    </header>

    @if($locations->isEmpty())
        <div class="glass-card" style="padding: 3rem; text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📍</div>
            <h3 style="color: var(--text-main);">Lokasi Belum Terdaftar</h3>
            <p style="color: var(--text-muted);">Silakan hubungi administrator untuk mengatur koordinat kantor.</p>
            <button onclick="syncDemoLocation()" class="btn-demo" style="max-width: 200px; margin: 1rem auto;">
                Set Lokasi Saya (Demo)
            </button>
        </div>
    @else
        <div class="att-grid">
            {{-- KIRI: KAMERA --}}
            <div class="glass-card">
                <div class="cam-header">
                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted-dark); text-transform: uppercase;">Autentikasi Wajah</span>
                    <div id="faceStatusBadge" class="status-badge" style="background: #6b728020; color: #6b7280;">
                        <span id="faceStatusText">Initializing...</span>
                    </div>
                </div>

                <div class="cam-viewport">
                    <video id="video" autoplay muted playsinline></video>
                    <canvas id="canvas"></canvas>
                    <div class="cam-overlay"><div id="faceGuide" class="face-guide"></div></div>
                    <div id="loadingOverlay">
                        <div class="spinner"></div>
                        <p id="loadingText" style="color: var(--text-main); font-weight: 600; font-size: 0.85rem;">Menyiapkan Sistem...</p>
                    </div>
                </div>

                <div class="info-content">
                    <span class="info-label">Konfirmasi Sistem</span>
                    <div id="readyIndicator" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; border-radius: 18px; background: #6b728010; border: 1px solid var(--border-line);">
                        <div id="readyDot" style="width: 12px; height: 12px; border-radius: 50%; background: #6b7280;"></div>
                        <div>
                            <div id="readyText" style="font-size: 0.95rem; font-weight: 700; color: var(--text-muted);">Menunggu Verifikasi</div>
                            <div id="readySub" style="font-size: 0.75rem; color: var(--text-muted-dark);">Wajah & Lokasi harus sesuai</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN: GPS & MAP --}}
            <div class="glass-card">
                <div id="map"></div>
                
                <div class="info-content">
                    <span class="info-label">Verifikasi Geolocation</span>
                    <div style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem;">
                        <div id="locIcon" style="width: 44px; height: 44px; border-radius: 12px; background: #6b728020; display: flex; align-items: center; justify-content: center; color: #6b7280; flex-shrink:0;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h4 id="locStatusTitle" style="margin:0; font-size: 1rem; font-weight: 700;">Mencari Sinyal GPS...</h4>
                            <p id="locStatusDesc" style="margin: 2px 0 0; font-size: 0.85rem; color: var(--text-muted);">Pastikan izin lokasi aktif di browser Anda.</p>
                        </div>
                    </div>

                    <div style="background: var(--hover-bg); padding: 1rem; border-radius: 16px; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <div style="font-size: 0.7rem; color: var(--text-muted-dark); text-transform: uppercase; font-weight: 700;">Cabang</div>
                            <div id="targetBranch" style="font-size: 0.9rem; font-weight: 600; color: var(--text-main);">-</div>
                        </div>
                        <div>
                            <div style="font-size: 0.7rem; color: var(--text-muted-dark); text-transform: uppercase; font-weight: 700;">Jarak</div>
                            <div id="currentDist" style="font-size: 0.9rem; font-weight: 700; color: #f59e0b;">- m</div>
                        </div>
                    </div>

                    @if(Auth::user()->isAdmin())
                        <button onclick="syncDemoLocation()" class="btn-demo">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Jadikan Lokasi Saya Sebagai Kantor (Demo)
                        </button>
                    @endif
                </div>

                <div class="action-footer">
                    <div class="action-grid">
                        <button id="btnCheckIn" class="btn-action btn-in" disabled onclick="submitAttendance('in')">
                            <span style="font-size: 1.25rem;">📥</span>
                            Absen Masuk
                        </button>
                        <button id="btnCheckOut" class="btn-action btn-out" disabled onclick="submitAttendance('out')">
                            <span style="font-size: 1.25rem;">📤</span>
                            Absen Pulang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Toast --}}
<div id="toast" style="display:none; position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%); z-index: 9999;">
    <div id="toastContent" style="padding: 1rem 2rem; border-radius: 99px; color: #fff; font-weight: 700; box-shadow: 0 10px 40px rgba(0,0,0,0.5); display: flex; align-items: center; gap: 0.75rem;">
        <span id="toastMsg"></span>
    </div>
</div>

<!-- Scripts -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const faceGuide = document.getElementById('faceGuide');
    const faceStatusText = document.getElementById('faceStatusText');
    const faceStatusBadge = document.getElementById('faceStatusBadge');
    const locIcon = document.getElementById('locIcon');
    const locStatusTitle = document.getElementById('locStatusTitle');
    const locStatusDesc = document.getElementById('locStatusDesc');
    const targetBranch = document.getElementById('targetBranch');
    const currentDist = document.getElementById('currentDist');
    const readyDot = document.getElementById('readyDot');
    const readyText = document.getElementById('readyText');
    const readySub = document.getElementById('readySub');
    const btnIn = document.getElementById('btnCheckIn');
    const btnOut = document.getElementById('btnCheckOut');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const loadingText = document.getElementById('loadingText');

    let isFaceDetected = false;
    let isLocationValid = false;
    let userLocation = null;
    let faceMatcher = null;
    let map, userMarker, officeMarker, radiusCircle;
    let currentOffice = null;
    const locations = @json($locations);

    const BASE_MODELS = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights';

    async function init() {
        try {
            loadingText.textContent = 'Memuat AI Detector...';
            await faceapi.nets.tinyFaceDetector.loadFromUri(BASE_MODELS);
            await faceapi.nets.faceLandmark68Net.loadFromUri(BASE_MODELS);
            await faceapi.nets.faceRecognitionNet.loadFromUri(BASE_MODELS);
            
            loadingText.textContent = 'Sinkronisasi Wajah...';
            await loadReference();
            
            loadingText.textContent = 'Membuka Kamera...';
            await startCamera();
            
            initMap();
        } catch (err) {
            console.error(err);
            loadingOverlay.innerHTML = '<p style="color:#ef4444;">Sistem Error. Cek Koneksi.</p>';
        }
    }

    async function loadReference() {
        @if(auth()->user()->face_reference_path)
            try {
                const url = '/storage/{{ auth()->user()->face_reference_path }}';
                const img = await faceapi.fetchImage(url);
                const detect = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
                if (detect) faceMatcher = new faceapi.FaceMatcher(detect.descriptor, 0.55);
            } catch (e) { console.error('Ref load failed'); }
        @endif
    }

    async function startCamera() {
        if (!navigator.mediaDevices?.getUserMedia) return;
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } });
        video.srcObject = stream;
        video.onloadedmetadata = () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            loadingOverlay.style.display = 'none';
            trackFace();
            trackLocation();
        };
    }

    function trackFace() {
        setInterval(async () => {
            if (!faceMatcher) { updateFaceUI(false, 'Data Wajah Kosong'); return; }
            const det = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 160, scoreThreshold: 0.5 }))
                                    .withFaceLandmarks().withFaceDescriptor();
            
            const displaySize = { width: video.videoWidth, height: video.videoHeight };
            faceapi.matchDimensions(canvas, displaySize);
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            if (det) {
                const resized = faceapi.resizeResults(det, displaySize);
                faceapi.draw.drawFaceLandmarks(canvas, resized, { drawLines: true, color: '#10b981', lineWidth: 1 });
                const best = faceMatcher.findBestMatch(det.descriptor);
                if (best.label !== 'unknown') updateFaceUI(true, 'Wajah Cocok');
                else updateFaceUI(false, 'Wajah Tidak Dikenal');
            } else {
                updateFaceUI(false, 'Mencari Wajah...');
            }
            checkStatus();
        }, 400);
    }

    function updateFaceUI(ok, txt) {
        isFaceDetected = ok;
        faceStatusText.textContent = txt;
        faceStatusBadge.style.background = ok ? '#10b98120' : '#ef444420';
        faceStatusBadge.style.color = ok ? '#10b981' : '#ef4444';
        if (ok) faceGuide.classList.add('active'); else faceGuide.classList.remove('active');
    }

    function initMap() {
        map = L.map('map', { zoomControl: false }).setView([0, 0], 2);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
    }

    function trackLocation() {
        if (!navigator.geolocation) return;
        navigator.geolocation.watchPosition(pos => {
            userLocation = { lat: pos.coords.latitude, lng: pos.coords.longitude };
            
            let min = Infinity, target = null;
            
            // Priority update based on if we just synced
            const activeLocations = currentOffice ? [currentOffice] : locations;

            activeLocations.forEach(l => {
                const d = calcDist(userLocation.lat, userLocation.lng, parseFloat(l.latitude), parseFloat(l.longitude));
                if (d < min) { min = d; target = l; }
            });

            isLocationValid = min <= target.radius;
            targetBranch.textContent = target.name;
            currentDist.textContent = Math.round(min) + ' m';
            currentDist.style.color = isLocationValid ? '#10b981' : '#f59e0b';

            updateMap(userLocation, target);

            if (isLocationValid) {
                locIcon.style.background = '#10b98120'; locIcon.style.color = '#10b981';
                locStatusTitle.textContent = 'Lokasi Sah';
                locStatusDesc.textContent = 'Anda berada di area ' + target.name;
            } else {
                locIcon.style.background = '#f59e0b20'; locIcon.style.color = '#f59e0b';
                locStatusTitle.textContent = 'Di Luar Area';
                locStatusDesc.textContent = 'Mendekatlah ke area kantor';
            }
            checkStatus();
        }, null, { enableHighAccuracy: true });
    }

    function updateMap(user, office) {
        const uPos = [user.lat, user.lng];
        const oPos = [parseFloat(office.latitude), parseFloat(office.longitude)];

        if (!userMarker) {
            userMarker = L.circleMarker(uPos, { color: '#3b82f6', radius: 8, fillOpacity: 0.8 }).addTo(map).bindPopup("Anda");
            officeMarker = L.marker(oPos).addTo(map).bindPopup(office.name);
            radiusCircle = L.circle(oPos, { radius: office.radius, color: '#10b981', fillOpacity: 0.1 }).addTo(map);
            map.fitBounds(L.featureGroup([userMarker, radiusCircle]).getBounds(), { padding: [20, 20] });
        } else {
            userMarker.setLatLng(uPos);
            officeMarker.setLatLng(oPos);
            radiusCircle.setLatLng(oPos).setRadius(office.radius);
        }
    }

    function checkStatus() {
        const ok = isFaceDetected && isLocationValid;
        btnIn.disabled = !ok; btnOut.disabled = !ok;
        readyDot.style.background = ok ? '#10b981' : '#ef4444';
        readyDot.style.boxShadow = ok ? '0 0 10px #10b981' : 'none';
        readyText.textContent = ok ? 'Sistem Siap' : 'Menunggu Verifikasi';
        readyText.style.color = ok ? '#10b981' : 'var(--text-muted)';
        readySub.textContent = ok ? 'Silakan pilih aksi absen' : (isFaceDetected ? 'Lokasi belum sah' : 'Wajah belum terdeteksi');
    }

    async function syncDemoLocation() {
        if (!userLocation) { toast('Tunggu hingga GPS terdeteksi', false); return; }
        
        try {
            const res = await fetch('{{ route("attendance.demo-sync") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ latitude: userLocation.lat, longitude: userLocation.lng })
            });
            const d = await res.json();
            if (d.success) {
                toast(d.message, true);
                // Force update UI locally to avoid waiting for next watchPosition cycle
                currentOffice = { name: 'Kantor Demo', latitude: userLocation.lat, longitude: userLocation.lng, radius: 100 };
                trackLocation();
                setTimeout(() => location.reload(), 1500); // Reload to sync all locations from DB
            }
        } catch (e) { toast('Gagal update lokasi demo', false); }
    }

    function calcDist(lat1, lon1, lat2, lon2) {
        const R = 6371e3, r = d => d * Math.PI / 180;
        const a = Math.sin(r(lat2 - lat1) / 2) ** 2 + Math.cos(r(lat1)) * Math.cos(r(lat2)) * Math.sin(r(lon2 - lon1) / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    async function submitAttendance(type) {
        btnIn.disabled = btnOut.disabled = true;
        const s = document.createElement('canvas');
        s.width = video.videoWidth; s.height = video.videoHeight;
        const ctx = s.getContext('2d');
        ctx.translate(s.width, 0); ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0);
        
        try {
            const res = await fetch('{{ route("attendance.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ image: s.toDataURL('image/jpeg', 0.8), latitude: userLocation.lat, longitude: userLocation.lng, type })
            });
            const d = await res.json();
            toast(d.message, d.success);
            if (d.success) setTimeout(() => location.href = '{{ route("dashboard") }}', 2000);
        } catch (e) { toast('Gagal mengirim data', false); checkStatus(); }
    }

    function toast(msg, ok) {
        const t = document.getElementById('toast');
        const c = document.getElementById('toastContent');
        document.getElementById('toastMsg').textContent = (ok ? '✔️ ' : '❌ ') + msg;
        c.style.background = ok ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #ef4444, #dc2626)';
        t.style.display = 'block';
        setTimeout(() => t.style.display = 'none', 4000);
    }

    init();
</script>
@endsection
