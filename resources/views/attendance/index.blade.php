@extends('layouts.app')

@section('content')
<style>
    .att-wrap { max-width:900px; margin:0 auto; padding:24px 20px 20px; }
    .att-title h1 { font-size:22px;font-weight:700;color:var(--text-main);margin:0 0 4px; }
    .att-title p  { font-size:13px;color:var(--text-muted-dark);margin:0 0 20px; }
    .att-grid { display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start; }
    .att-card { background:var(--bg-glass);border:1px solid var(--border-glass);border-radius:18px;overflow:hidden; transition: all 0.3s; }
    .att-card-pad { padding:16px 18px; }

    /* Camera */
    .cam-header { display:flex;align-items:center;justify-content:space-between;padding:14px 16px 10px; }
    .cam-label { font-size:11px;font-weight:600;color:var(--text-muted-dark);text-transform:uppercase;letter-spacing:.06em; }
    .face-badge { display:flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600; }
    .face-badge .dot { width:6px;height:6px;border-radius:50%;flex-shrink:0; }
    .cam-body { position:relative;background:#000; aspect-ratio:4/3; }
    .cam-body video { display:block;width:100%;height:100%;object-fit:cover;transform:scaleX(-1); }
    .cam-body canvas { position:absolute;inset:0;width:100%;height:100%;transform:scaleX(-1); }
    .cam-overlay { position:absolute;inset:0;display:flex;align-items:center;justify-content:center;flex-direction:column;z-index:10; }
    .cam-face-frame { width:40%;padding-bottom:52%;position:relative;border-radius:50%;transition:border-color .3s,box-shadow .3s; }
    .cam-footer { text-align:center;padding:8px 0 10px;font-size:11px;color:var(--text-muted-dark); }

    /* Info column */
    .info-col { display:flex;flex-direction:column;gap:12px; }
    .info-card { background:var(--bg-glass);border:1px solid var(--border-glass);border-radius:16px;padding:14px 16px; transition: all 0.3s;}
    .info-card-head { display:flex;align-items:center;justify-content:space-between;margin-bottom:8px; }
    .info-card-label { font-size:11px;font-weight:600;color:var(--text-muted-dark);text-transform:uppercase;letter-spacing:.06em; }
    .loc-badge { display:flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600; }
    .loc-badge .dot { width:6px;height:6px;border-radius:50%;flex-shrink:0; }

    /* Action buttons */
    .att-btns { display:grid;grid-template-columns:1fr 1fr;gap:10px; }
    .att-btn {
        padding:16px 8px; border-radius:16px; border:none; cursor:pointer;
        font-weight:700; font-size:14px; color:#fff; text-align:center;
        transition:transform .12s,box-shadow .12s;
    }
    .att-btn:disabled { opacity:.38;cursor:not-allowed; }
    .att-btn:not(:disabled):active { transform:scale(0.96); }
    .att-btn-in  { background:linear-gradient(135deg,#3b82f6,#1d4ed8);box-shadow:0 4px 18px rgba(59,130,246,0.3); }
    .att-btn-out { background:linear-gradient(135deg,#10b981,#059669);box-shadow:0 4px 18px rgba(16,185,129,0.3); }
    .att-btn-in:not(:disabled):hover  { box-shadow:0 8px 28px rgba(59,130,246,0.45); }
    .att-btn-out:not(:disabled):hover { box-shadow:0 8px 28px rgba(16,185,129,0.45); }
    .att-btn-emoji { display:block;font-size:18px;margin-bottom:3px; }

    /* Toast */
    #toast { position:fixed;z-index:500;left:50%;transform:translateX(-50%);bottom:80px;width:calc(100% - 32px);max-width:400px; }
    @media (min-width:768px) { #toast { bottom:28px; } }
    #toastInner { padding:13px 18px;border-radius:16px;font-size:13.5px;font-weight:600;color:#fff;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,0.4); }

    /* Responsive: stack on mobile */
    @media (max-width:767px) {
        .att-wrap { padding:12px 12px 14px; }
        .att-title h1 { font-size:19px; }
        .att-title p { margin-bottom:14px; }
        .att-grid { grid-template-columns:1fr;gap:12px; }
        .cam-face-frame { width:46%;padding-bottom:58%; }
    }
</style>

<div class="att-wrap">
    <div class="att-title">
        <h1>Absensi Karyawan</h1>
        <p>PT Serunting Sakti Jaya &mdash; Pastikan wajah &amp; lokasi terdeteksi dengan benar.</p>
    </div>

    @if(!$company)
    <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);border-radius:14px;padding:16px;color:#ef4444;text-align:center;font-size:13.5px;">
        Pengaturan lokasi perusahaan belum dikonfigurasi. Hubungi administrator.
    </div>
    @else

    <div class="att-grid">
        {{-- KAMERA SELFIE --}}
        <div class="att-card">
            <div class="cam-header">
                <span class="cam-label">Kamera Selfie</span>
                <div id="faceStatus" class="face-badge" style="background:rgba(239,68,68,0.15);color:#ef4444;">
                    <span class="dot" style="background:#ef4444;" id="faceDot"></span>
                    <span id="faceTxt">Tidak Terdeteksi</span>
                </div>
            </div>
            <div class="cam-body">
                <video id="video" autoplay muted playsinline></video>
                <canvas id="canvas"></canvas>
                <div id="loading" class="cam-overlay" style="background:rgba(0,0,0,0.85);">
                    <div style="width:40px;height:40px;border:4px solid #3b82f6;border-top-color:transparent;border-radius:50%;animation:spin 1s linear infinite;margin-bottom:10px;"></div>
                    <p style="color:#60a5fa;font-size:13px;font-weight:600;margin:0;">Memuat AI...</p>
                    <p style="color:#9ca3af;font-size:11px;margin:4px 0 0;">Harap tunggu</p>
                </div>
                <div class="cam-overlay" style="pointer-events:none;z-index:20;">
                    <div id="faceFrame" class="cam-face-frame"
                         style="border:2.5px dashed rgba(239,68,68,0.7);box-shadow:0 0 0 9999px rgba(0,0,0,0.38);">
                    </div>
                </div>
            </div>
            <div class="cam-footer">Posisikan wajah di dalam lingkaran</div>
        </div>

        {{-- INFO + ACTION --}}
        <div class="info-col">
            {{-- Lokasi --}}
            <div class="info-card">
                <div class="info-card-head">
                    <span class="info-card-label">Lokasi GPS</span>
                    <div id="locBadge" class="loc-badge" style="background:rgba(107,114,128,0.15);color:var(--text-muted-dark);">
                        <span class="dot" style="background:var(--text-muted-dark);" id="locDot"></span>
                        <span id="locTxt">Mendeteksi...</span>
                    </div>
                </div>
                <div id="locText" style="font-size:13.5px;font-weight:500;color:var(--text-main);">Mengambil koordinat GPS...</div>
                <div id="distInfo" style="font-size:11.5px;color:var(--text-muted-dark);margin-top:4px;display:none;"></div>
                <div style="border-top:1px solid var(--border-line);margin-top:12px;padding-top:10px;">
                    <div style="font-size:11px;color:var(--text-muted-dark);">Kantor: <span style="color:var(--text-main);">{{ $company->name }}</span></div>
                    <div style="font-size:11px;color:var(--text-muted-dark);margin-top:3px;">Alamat: <span style="color:var(--text-main);font-size:10.5px;">Ruko Pesona Batavia, Kemang, Bogor</span></div>
                    <div style="font-size:11px;color:var(--text-muted-dark);margin-top:3px;">Radius sah: <span style="color:#f59e0b;font-weight:500;">{{ $company->radius }} meter</span></div>
                </div>
            </div>

            {{-- Status --}}
            <div class="info-card">
                <div style="font-size:11px;font-weight:600;color:var(--text-muted-dark);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Status</div>
                <div id="overallStatus" style="font-size:13.5px;color:var(--text-muted);">Menunggu deteksi wajah &amp; lokasi...</div>
            </div>

            {{-- Buttons --}}
            <div class="att-btns">
                <button id="btnCheckIn"  class="att-btn att-btn-in"  disabled onclick="submitAttendance('in')">
                    <span class="att-btn-emoji">&#128994;</span>Absen Masuk
                </button>
                <button id="btnCheckOut" class="att-btn att-btn-out" disabled onclick="submitAttendance('out')">
                    <span class="att-btn-emoji">&#128308;</span>Absen Pulang
                </button>
            </div>
            <p style="font-size:11px;color:var(--text-muted);text-align:center;margin:0;">Wajah + lokasi harus terdeteksi untuk absen.</p>
        </div>
    </div>

    @endif
</div>

{{-- Toast --}}
<div id="toast" style="display:none;">
    <div id="toastInner"></div>
</div>

<style>
@keyframes spin { to { transform:rotate(360deg); } }
</style>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
const video          = document.getElementById('video');
const canvas         = document.getElementById('canvas');
const loading        = document.getElementById('loading');
const faceDot        = document.getElementById('faceDot');
const faceTxt        = document.getElementById('faceTxt');
const faceStatus     = document.getElementById('faceStatus');
const faceFrame      = document.getElementById('faceFrame');
const locDot         = document.getElementById('locDot');
const locTxt         = document.getElementById('locTxt');
const locBadge       = document.getElementById('locBadge');
const locText        = document.getElementById('locText');
const distInfo       = document.getElementById('distInfo');
const overallStatus  = document.getElementById('overallStatus');
const btnIn          = document.getElementById('btnCheckIn');
const btnOut         = document.getElementById('btnCheckOut');

let isFaceDetected  = false;
let isLocationValid = false;
let userLocation    = null;
let faceMatcher     = null;

const company = {
    lat:    {{ $company->latitude }},
    lng:    {{ $company->longitude }},
    radius: {{ $company->radius }}
};

/* Load models and reference face */
async function loadModels() {
    const BASE = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights';
    try {
        await faceapi.nets.tinyFaceDetector.loadFromUri(BASE);
        await faceapi.nets.faceLandmark68Net.loadFromUri(BASE);
        await faceapi.nets.faceRecognitionNet.loadFromUri(BASE);
        
        loading.innerHTML = '<div style="width:40px;height:40px;border:4px solid #3b82f6;border-top-color:transparent;border-radius:50%;animation:spin 1s linear infinite;margin-bottom:10px;"></div><p style="color:#60a5fa;font-size:13px;font-weight:600;margin:0;">Memuat Data Wajah...</p>';
        
        await loadReferenceFace();
        startCamera();
    } catch(e) {
        loading.innerHTML = '<p style="color:#ef4444;text-align:center;padding:12px;font-size:13px;">Gagal memuat AI.<br>Periksa koneksi internet.</p>';
        console.error(e);
    }
}

async function loadReferenceFace() {
    @if(auth()->user()->face_reference_path)
    try {
        const refImgUrl = '{{ asset("storage/".auth()->user()->face_reference_path) }}';
        const img = await faceapi.fetchImage(refImgUrl);
        const refDetection = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
        if (refDetection) {
            faceMatcher = new faceapi.FaceMatcher(refDetection.descriptor, 0.55); // 0.55 threshold (lower = stricter)
        } else {
            throw new Error('Wajah referensi gagal diproses.');
        }
    } catch(e) {
        console.error('Error loading reference face:', e);
        // We will fallback to false/disabled if matcher isn't built
    }
    @else
        console.error('Belum ada foto referensi.');
    @endif
}

/* Camera */
function startCamera() {
    navigator.mediaDevices.getUserMedia({ video:{ facingMode:'user', width:{ideal:640}, height:{ideal:480} } })
        .then(stream => {
            video.srcObject = stream;
            video.onloadedmetadata = () => {
                canvas.width  = video.videoWidth;
                canvas.height = video.videoHeight;
                loading.style.display = 'none';
                runFaceDetection();
                initGeolocation();
            };
        })
        .catch(() => {
            loading.innerHTML = '<p style="color:#ef4444;text-align:center;padding:16px;font-size:13px;">&#10060; Kamera ditolak.<br>Izinkan kamera di pengaturan browser.</p>';
        });
}

/* Face Detection & Matching */
async function runFaceDetection() {
    setInterval(async () => {
        if (!faceMatcher) {
            setFaceStatus(false, 'Data Wajah Error');
            updateReadiness();
            return;
        }

        // Run full detection: bounding box + landmarks + descriptor
        const d = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize:160, scoreThreshold:.5 }))
                               .withFaceLandmarks()
                               .withFaceDescriptor();
                               
        canvas.getContext('2d').clearRect(0,0,canvas.width,canvas.height);

        if (d) {
            const match = faceMatcher.findBestMatch(d.descriptor);
            if (match.label !== 'unknown') {
                setFaceStatus(true, 'Wajah Cocok ('+Math.round((1-match.distance)*100)+'%)');
            } else {
                setFaceStatus(false, 'Wajah Tidak Dikenali');
            }
        } else {
            setFaceStatus(false, 'Tidak Terdeteksi');
        }
        updateReadiness();
    }, 800); // 800ms to reduce CPU load since descriptor calculation is heavy
}

function setFaceStatus(isValid, text) {
    isFaceDetected = isValid;
    if (isValid) {
        faceStatus.style.background = 'rgba(16,185,129,0.15)';
        faceStatus.style.color      = '#10b981';
        faceDot.style.background    = '#10b981';
        faceTxt.textContent         = text;
        faceFrame.style.borderColor = 'rgba(16,185,129,0.8)';
        faceFrame.style.borderStyle = 'solid';
        faceFrame.style.boxShadow   = '0 0 0 9999px rgba(0,0,0,0.35),0 0 20px rgba(16,185,129,0.4)';
    } else {
        faceStatus.style.background = 'rgba(239,68,68,0.15)';
        faceStatus.style.color      = '#ef4444';
        faceDot.style.background    = '#ef4444';
        faceTxt.textContent         = text;
        faceFrame.style.borderColor = 'rgba(239,68,68,0.7)';
        faceFrame.style.borderStyle = 'dashed';
        faceFrame.style.boxShadow   = '0 0 0 9999px rgba(0,0,0,0.38)';
    }
}

/* Geolocation */
function initGeolocation() {
    if (!('geolocation' in navigator)) {
        locText.textContent = 'GPS tidak didukung browser ini.';
        return;
    }
    navigator.geolocation.getCurrentPosition(pos => {
        userLocation = { lat: pos.coords.latitude, lng: pos.coords.longitude };
        const dist   = haversine(userLocation.lat, userLocation.lng, company.lat, company.lng);
        isLocationValid = dist <= company.radius;

        distInfo.style.display = 'block';
        distInfo.textContent   = 'Jarak dari kantor: ' + Math.round(dist) + ' meter';

        if (isLocationValid) {
            locText.innerHTML           = '<span style="color:#059669;font-weight:600;">&#10004; Lokasi Sah</span>';
            locBadge.style.background   = 'rgba(16,185,129,0.15)';
            locBadge.style.color        = '#059669';
            locDot.style.background     = '#059669';
            locTxt.textContent          = 'Sah';
            if(document.documentElement.getAttribute('data-theme') === 'dark') {
               locText.querySelector('span').style.color = '#34d399';
               locBadge.style.color = '#34d399';
               locDot.style.background = '#34d399';
            }
        } else {
            locText.innerHTML           = '<span style="color:#dc2626;font-weight:600;">&#10008; Di Luar Area (' + Math.round(dist) + 'm)</span>';
            locBadge.style.background   = 'rgba(239,68,68,0.15)';
            locBadge.style.color        = '#dc2626';
            locDot.style.background     = '#dc2626';
            locTxt.textContent          = 'Tidak Sah';
             if(document.documentElement.getAttribute('data-theme') === 'dark') {
               locText.querySelector('span').style.color = '#f87171';
               locBadge.style.color = '#f87171';
               locDot.style.background = '#f87171';
            }
        }
        updateReadiness();
    }, () => {
        locText.textContent = 'Gagal mendapatkan lokasi. Izinkan GPS di browser.';
        locText.style.color = '#ef4444';
    }, { enableHighAccuracy:true, timeout:12000 });
}

/* Readiness */
function updateReadiness() {
    const ready = isFaceDetected && isLocationValid;
    btnIn.disabled  = !ready;
    btnOut.disabled = !ready;
    if (ready) {
        overallStatus.innerHTML  = '<span style="color:#059669;font-weight:600;" id="stR">✔ Siap! Klik tombol di bawah untuk absen.</span>';
        if(document.documentElement.getAttribute('data-theme') === 'dark') document.getElementById('stR').style.color = '#34d399';
    } else {
        const m = [];
        if (!isFaceDetected)  m.push('wajah terdeteksi');
        if (!isLocationValid) m.push(userLocation ? 'berada di lokasi kantor' : 'izin lokasi GPS');
        overallStatus.textContent = 'Membutuhkan: ' + m.join(' & ') + '.';
        overallStatus.style.color = 'var(--text-muted)';
    }
}

/* Haversine */
function haversine(lat1,lon1,lat2,lon2) {
    const R=6371e3, r=d=>d*Math.PI/180;
    const a = Math.sin(r(lat2-lat1)/2)**2 + Math.cos(r(lat1))*Math.cos(r(lat2))*Math.sin(r(lon2-lon1)/2)**2;
    return R*2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));
}

/* Submit */
async function submitAttendance(type) {
    btnIn.disabled = btnOut.disabled = true;
    const snap = document.createElement('canvas');
    snap.width  = video.videoWidth;
    snap.height = video.videoHeight;
    const ctx   = snap.getContext('2d');
    ctx.translate(snap.width,0); ctx.scale(-1,1);
    ctx.drawImage(video,0,0);
    try {
        const res  = await fetch('{{ route("attendance.store") }}', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({ image:snap.toDataURL('image/jpeg',.85), latitude:userLocation.lat, longitude:userLocation.lng, type })
        });
        const data = await res.json();
        showToast(data.success, data.message);
        if (data.success) setTimeout(() => location.href='{{ route("dashboard") }}', 2000);
        else updateReadiness();
    } catch {
        showToast(false,'Gagal mengirim. Periksa koneksi internet.');
        updateReadiness();
    }
}

/* Toast */
function showToast(ok, msg) {
    const t = document.getElementById('toast');
    const i = document.getElementById('toastInner');
    i.textContent     = (ok ? '✔ ' : '✘ ') + msg;
    i.style.background = ok ? 'linear-gradient(135deg,#059669,#10b981)' : 'linear-gradient(135deg,#dc2626,#ef4444)';
    t.style.display   = 'block';
    setTimeout(() => { t.style.display='none'; }, 3500);
}

loadModels();
</script>
@endsection
