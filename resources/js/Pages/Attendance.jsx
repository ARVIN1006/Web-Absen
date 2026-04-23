import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router, useForm } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import { extractFaceDescriptorFromImage, loadFaceModels } from "@/Utils/faceDescriptor";

function formatCoordinate(value) {
    return Number(value).toFixed(6);
}

function getDistance(lat1, lng1, lat2, lng2) {
    const earthRadius = 6371e3;
    const phi1 = (lat1 * Math.PI) / 180;
    const phi2 = (lat2 * Math.PI) / 180;
    const deltaPhi = ((lat2 - lat1) * Math.PI) / 180;
    const deltaLambda = ((lng2 - lng1) * Math.PI) / 180;

    const a =
        Math.sin(deltaPhi / 2) * Math.sin(deltaPhi / 2) +
        Math.cos(phi1) * Math.cos(phi2) * Math.sin(deltaLambda / 2) * Math.sin(deltaLambda / 2);

    return earthRadius * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
}

export default function Attendance({ locations, todayAttendances = [], demoMode, attendanceRules }) {
    const [cameraOpen, setCameraOpen] = useState(false);
    const [demoLocations, setDemoLocations] = useState(locations || []);
    const [location, setLocation] = useState(null);
    const [photo, setPhoto] = useState(null);
    const [message, setMessage] = useState(null);
    const [messageTone, setMessageTone] = useState("success");
    const [attendanceType, setAttendanceType] = useState("in");
    const [syncingDemoLocation, setSyncingDemoLocation] = useState(false);
    const [capturingFace, setCapturingFace] = useState(false);
    const [faceMessage, setFaceMessage] = useState("Model wajah sedang dimuat...");
    const videoRef = useRef(null);
    const canvasRef = useRef(null);

    const { data, setData } = useForm({
        image: "",
        latitude: "",
        longitude: "",
        type: "in",
        face_descriptor: [],
    });
    const [processingAttendance, setProcessingAttendance] = useState(false);

    const checkInRecord = todayAttendances.find((attendance) => attendance.type === "in");
    const checkOutRecord = todayAttendances.find((attendance) => attendance.type === "out");
    const hasCheckIn = Boolean(checkInRecord);
    const hasCheckOut = Boolean(checkOutRecord);

    const formatAttendanceTime = (value) => {
        if (!value) return "-";

        return new Date(value).toLocaleTimeString("id-ID", {
            hour: "2-digit",
            minute: "2-digit",
        });
    };

    useEffect(() => {
        setDemoLocations(locations || []);
    }, [locations]);

    useEffect(() => {
        if (!navigator.geolocation) {
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                setLocation({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                });
            },
            (error) => {
                console.error("Geolocation error:", error);
            }
        );
    }, []);

    useEffect(() => {
        loadFaceModels()
            .then(() => setFaceMessage("Model wajah siap. Ambil foto untuk verifikasi pemilik akun."))
            .catch(() => setFaceMessage("Model wajah gagal dimuat. Refresh halaman lalu coba lagi."));
    }, []);

    useEffect(() => {
        return () => {
            stopCameraStream();
        };
    }, []);

    useEffect(() => {
        if (cameraOpen && ((attendanceType === "in" && hasCheckIn) || (attendanceType === "out" && hasCheckOut))) {
            closeCamera();
        }
    }, [cameraOpen, attendanceType, hasCheckIn, hasCheckOut]);

    const openCamera = async (type) => {
        if (type === "in" && hasCheckIn) {
            setMessageTone("danger");
            setMessage(`Anda sudah Clock-in hari ini pukul ${formatAttendanceTime(checkInRecord.check_in_at || checkInRecord.created_at)}.`);
            return;
        }

        if (type === "out" && !hasCheckIn) {
            setMessageTone("danger");
            setMessage("Anda perlu Clock-in terlebih dahulu sebelum Clock-out.");
            return;
        }

        if (type === "out" && hasCheckOut) {
            setMessageTone("danger");
            setMessage(`Anda sudah Clock-out hari ini pukul ${formatAttendanceTime(checkOutRecord.check_out_at || checkOutRecord.created_at)}.`);
            return;
        }

        setAttendanceType(type);
        setPhoto(null);
        setData("face_descriptor", []);

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            setCameraOpen(true);
            if (videoRef.current) {
                videoRef.current.srcObject = stream;
            }
        } catch (error) {
            console.error("Camera error:", error);
            alert("Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.");
        }
    };

    const closeCamera = () => {
        stopCameraStream();
        setCameraOpen(false);
        setPhoto(null);
        setData("face_descriptor", []);
        setFaceMessage("Model wajah siap. Ambil foto untuk verifikasi pemilik akun.");
    };

    const stopCameraStream = () => {
        if (videoRef.current?.srcObject) {
            videoRef.current.srcObject.getTracks().forEach((track) => track.stop());
            videoRef.current.srcObject = null;
        }
    };

    const capturePhoto = async () => {
        if (!videoRef.current || !canvasRef.current) {
            return;
        }

        setCapturingFace(true);
        setFaceMessage("Mendeteksi wajah dan membuat descriptor presensi...");

        const video = videoRef.current;
        const canvas = canvasRef.current;
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const context = canvas.getContext("2d");
        context.translate(canvas.width, 0);
        context.scale(-1, 1);
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = canvas.toDataURL("image/jpeg", 0.8);

        try {
            const descriptor = await extractFaceDescriptorFromImage(imageData);
            setPhoto(imageData);
            setData("face_descriptor", descriptor);
            setFaceMessage("Wajah terdeteksi. Silakan konfirmasi presensi.");
        } catch (error) {
            setPhoto(null);
            setData("face_descriptor", []);
            setFaceMessage(error.message);
        } finally {
            setCapturingFace(false);
        }
    };

    const submitAttendance = () => {
        if (!photo || !location) {
            alert("Foto atau lokasi belum tersedia.");
            return;
        }

        setProcessingAttendance(true);
        router.post(route("attendance.store"), {
            image: photo,
            latitude: location.lat,
            longitude: location.lng,
            type: attendanceType,
            face_descriptor: data.face_descriptor,
        }, {
            onSuccess: (page) => {
                setMessageTone("success");
                setMessage(page.props.flash?.success || "Absensi berhasil direkam.");
                closeCamera();
            },
            onError: (errors) => {
                setMessageTone("danger");
                setMessage(errors.message || "Terjadi kesalahan saat mengirim absensi.");
            },
            onFinish: () => setProcessingAttendance(false),
        });
    };

    const syncDemoLocation = async () => {
        if (!location) {
            alert("Lokasi perangkat belum terdeteksi. Izinkan akses lokasi lalu coba lagi.");
            return;
        }

        setSyncingDemoLocation(true);

        try {
            const response = await window.axios.post(route("attendance.demo-sync"), {
                latitude: location.lat,
                longitude: location.lng,
            });

            if (response.data?.location) {
                setDemoLocations((items) => {
                    const exists = items.some((item) => item.id === response.data.location.id);

                    return exists
                        ? items.map((item) => (item.id === response.data.location.id ? response.data.location : item))
                        : [response.data.location, ...items];
                });
            }

            setMessageTone("success");
            setMessage(response.data?.message || "Lokasi demo sudah disesuaikan ke posisi perangkat ini.");
            router.reload({ only: ["locations"], preserveScroll: true });
        } catch (error) {
            setMessageTone("danger");
            setMessage(error.response?.data?.message || "Gagal menyesuaikan lokasi demo.");
        } finally {
            setSyncingDemoLocation(false);
        }
    };

    const resetTodayDemo = () => {
        if (!confirm("Reset Clock-in/Clock-out hari ini untuk akun ini agar demo bisa diulang?")) {
            return;
        }

        router.post(route("attendance.demo-reset-today"), {}, { preserveScroll: true });
    };

    const rankedLocations = location
        ? [...(demoLocations || [])]
              .map((item) => ({
                  ...item,
                  distance: getDistance(location.lat, location.lng, item.latitude, item.longitude),
              }))
              .sort((first, second) => first.distance - second.distance)
        : [];

    const activeLocation = rankedLocations[0] || null;
    const canCheckIn =
        activeLocation && location
            ? activeLocation.distance <= activeLocation.radius
            : false;
    const attendanceAllowed = attendanceRules?.bypass_geofence ? true : canCheckIn;

    return (
        <AuthenticatedLayout>
            <Head title="Absensi" />

            {demoMode && (
                <section className="mb-6 rounded-[24px] border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 shadow-sm">
                    <div className="font-semibold uppercase tracking-[0.16em] text-amber-700">Demo Mode</div>
                    <p className="mt-2 leading-6">
                        Presensi sedang berjalan dalam mode presentasi.{" "}
                        {attendanceRules?.bypass_face_verification ? "Validasi wajah dibypass. " : ""}
                        {attendanceRules?.bypass_geofence ? "Geofence dibypass agar demo bisa dijalankan dari lokasi mana saja." : ""}
                    </p>
                </section>
            )}

            <section className="mb-6 grid gap-4 md:grid-cols-3">
                <div className="ui-card p-4">
                    <div className="ui-section-title">Status Hari Ini</div>
                    <div className="mt-3 font-heading text-xl font-bold text-[var(--text-main)]">
                        {!hasCheckIn ? "Belum Clock-in" : hasCheckOut ? "Presensi selesai" : "Sudah Clock-in"}
                    </div>
                    <p className="mt-2 text-sm leading-6 text-[var(--text-muted)]">
                        Satu akun hanya bisa Clock-in dan Clock-out masing-masing satu kali per hari.
                    </p>
                    {(demoMode || hasCheckIn || hasCheckOut) && (
                        <button
                            type="button"
                            onClick={resetTodayDemo}
                            className="mt-4 w-full rounded-md border border-amber-300 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-800 transition hover:bg-amber-100"
                        >
                            Reset Presensi Hari Ini untuk Demo
                        </button>
                    )}
                </div>
                <div className="ui-card p-4">
                    <div className="ui-section-title">Clock-in</div>
                    <div className={`mt-3 font-heading text-2xl font-bold ${hasCheckIn ? "text-[var(--success-color)]" : "text-[var(--warning-color)]"}`}>
                        {hasCheckIn ? formatAttendanceTime(checkInRecord.check_in_at || checkInRecord.created_at) : "Belum"}
                    </div>
                    <p className="mt-2 text-sm text-[var(--text-muted)]">
                        {hasCheckIn ? "Data masuk sudah tercatat untuk akun ini." : "Mulai presensi masuk dari tombol di bawah."}
                    </p>
                </div>
                <div className="ui-card p-4">
                    <div className="ui-section-title">Clock-out</div>
                    <div className={`mt-3 font-heading text-2xl font-bold ${hasCheckOut ? "text-[var(--success-color)]" : "text-[var(--warning-color)]"}`}>
                        {hasCheckOut ? formatAttendanceTime(checkOutRecord.check_out_at || checkOutRecord.created_at) : "Belum"}
                    </div>
                    <p className="mt-2 text-sm text-[var(--text-muted)]">
                        {hasCheckOut ? "Data pulang sudah tercatat untuk akun ini." : hasCheckIn ? "Clock-out tersedia setelah jam pulang." : "Clock-in dulu untuk membuka alur pulang."}
                    </p>
                </div>
            </section>

            <section className="ui-card overflow-hidden">
                <div className="grid gap-6 p-6 lg:grid-cols-[1.4fr_0.8fr] lg:p-8">
                    <div>
                        <div className="ui-section-title">Face Attendance</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">
                            Presensi masuk dan pulang dengan verifikasi wajah
                        </h1>
                        <p className="mt-3 max-w-2xl text-sm leading-6 text-[var(--text-muted)]">
                            Sistem absensi hanya mengizinkan wajah pemilik akun dan hanya di lokasi
                            kantor yang sudah dikonfigurasi melalui geofence.
                        </p>
                    </div>

                    <div className="ui-card-muted p-5">
                        <div className="ui-section-title">Status Lokasi</div>
                        <div className="mt-4">
                            {location ? (
                                <div className="space-y-3 text-sm text-[var(--text-muted)]">
                                    <div className="flex items-start justify-between gap-3 border-b border-[var(--border-line)] pb-3">
                                        <span>Koordinat Anda</span>
                                        <span className="font-data text-right text-[var(--text-main)]">
                                            {formatCoordinate(location.lat)}, {formatCoordinate(location.lng)}
                                        </span>
                                    </div>
                                    {activeLocation && (
                                        <>
                                            <div className="flex items-center justify-between">
                                                <span>Lokasi Kantor Terdekat</span>
                                                <span className="font-semibold text-[var(--text-main)]">
                                                    {activeLocation.name}
                                                </span>
                                            </div>
                                            <div className="flex items-center justify-between">
                                                <span>Radius Aktif</span>
                                                <span className="font-data text-[var(--text-main)]">
                                                    {activeLocation.radius} m
                                                </span>
                                            </div>
                                            <div className="flex items-center justify-between">
                                                <span>Jarak Saat Ini</span>
                                                <span className="font-data text-[var(--text-main)]">
                                                    {Math.round(activeLocation.distance)} m
                                                </span>
                                            </div>
                                            <div className="flex items-center justify-between">
                                                <span>Mode Validasi</span>
                                                <span className="font-semibold text-[var(--text-main)]">
                                                    {activeLocation.enforce_face_verification
                                                        ? "Wajah + GPS"
                                                        : "GPS saja"}
                                                </span>
                                            </div>
                                            <div>
                                                <span
                                                    className={`ui-badge ${
                                                        attendanceAllowed
                                                            ? "bg-emerald-50 text-[var(--success-color)]"
                                                            : "bg-red-50 text-[var(--danger-color)]"
                                                    }`}
                                                >
                                                    {attendanceAllowed
                                                        ? "Dalam area kantor"
                                                        : "Di luar area kantor"}
                                                </span>
                                            </div>
                                            {!attendanceAllowed && (
                                                <div className="rounded-2xl border border-amber-200 bg-amber-50 px-3 py-3 text-xs leading-5 text-amber-800">
                                                    Untuk demo di lokasi client, klik tombol di bawah agar titik kantor demo digeser ke koordinat perangkat ini.
                                                </div>
                                            )}
                                            {(demoMode || !attendanceAllowed) && (
                                                <button
                                                    type="button"
                                                    onClick={syncDemoLocation}
                                                    disabled={syncingDemoLocation}
                                                    className="mt-2 w-full rounded-md bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-60"
                                                >
                                                    {syncingDemoLocation
                                                        ? "Menyesuaikan lokasi..."
                                                        : "Sesuaikan Lokasi Demo ke Posisi Saya"}
                                                </button>
                                            )}
                                        </>
                                    )}
                                </div>
                            ) : (
                                <div className="text-sm text-[var(--text-muted)]">
                                    Sedang mendeteksi lokasi perangkat...
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </section>

            {message && (
                <div
                    className={`mt-6 rounded-xl px-4 py-3 text-sm font-medium ${
                        messageTone === "success"
                            ? "border border-emerald-200 bg-emerald-50 text-emerald-700"
                            : "border border-red-200 bg-red-50 text-red-700"
                    }`}
                >
                    {message}
                </div>
            )}

            <section className="mt-6 grid gap-6 lg:grid-cols-2">
                <div className="ui-card p-6">
                    <div className="flex items-start justify-between gap-4 border-b border-[var(--border-line)] pb-4">
                        <div>
                            <div className="ui-section-title">Check-in</div>
                            <h2 className="font-heading mt-2 text-xl font-semibold">Absensi Masuk</h2>
                            <p className="mt-2 text-sm text-[var(--text-muted)]">
                                Rekam kehadiran awal hari kerja menggunakan kamera perangkat.
                            </p>
                            <p className="mt-2 text-xs text-[var(--text-muted)]">
                                Foto akan dibandingkan dengan wajah referensi milik akun Anda.
                            </p>
                        </div>
                        <span className="ui-badge bg-emerald-50 text-[var(--success-color)]">Masuk</span>
                    </div>

                    <button
                        onClick={() => openCamera("in")}
                        disabled={cameraOpen || hasCheckIn}
                        className="ui-button-primary mt-6 w-full disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {hasCheckIn ? "Clock-in Sudah Tercatat" : "Ambil Foto dan Check-in"}
                    </button>
                    {hasCheckIn && (
                        <div className="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm leading-6 text-emerald-700">
                            Akun ini sudah melakukan Clock-in hari ini. Untuk demo berulang, gunakan akun karyawan lain atau reset data demo.
                        </div>
                    )}
                </div>

                <div className="ui-card p-6">
                    <div className="flex items-start justify-between gap-4 border-b border-[var(--border-line)] pb-4">
                        <div>
                            <div className="ui-section-title">Check-out</div>
                            <h2 className="font-heading mt-2 text-xl font-semibold">Absensi Pulang</h2>
                            <p className="mt-2 text-sm text-[var(--text-muted)]">
                                Catat waktu pulang dengan validasi geofence dan identitas wajah yang sama.
                            </p>
                            <p className="mt-2 text-xs text-[var(--text-muted)]">
                                Check-out tetap harus dilakukan dari area kantor yang diizinkan.
                            </p>
                        </div>
                        <span className="ui-badge bg-amber-50 text-[var(--warning-color)]">Pulang</span>
                    </div>

                    <button
                        onClick={() => openCamera("out")}
                        disabled={cameraOpen || !hasCheckIn || hasCheckOut}
                        className="mt-6 inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {!hasCheckIn ? "Clock-in Dulu" : hasCheckOut ? "Clock-out Sudah Tercatat" : "Ambil Foto dan Check-out"}
                    </button>
                    {hasCheckOut && (
                        <div className="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm leading-6 text-emerald-700">
                            Akun ini sudah menyelesaikan presensi hari ini. Client tetap bisa melihat riwayatnya di dashboard atau rekap admin.
                        </div>
                    )}
                    {!hasCheckIn && (
                        <div className="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-800">
                            Clock-out akan aktif setelah Clock-in berhasil.
                        </div>
                    )}
                </div>
            </section>

            {cameraOpen && (
                <div className="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
                    <div className="w-full max-w-2xl rounded-xl border border-[var(--border-line)] bg-white shadow-[var(--shadow-pop)]">
                        <div className="flex items-center justify-between border-b border-[var(--border-line)] px-5 py-4">
                            <div>
                                <div className="ui-section-title">Capture</div>
                                <h3 className="font-heading mt-1 text-lg font-semibold">
                                    {attendanceType === "in" ? "Konfirmasi Check-in" : "Konfirmasi Check-out"}
                                </h3>
                            </div>
                            <button
                                type="button"
                                onClick={closeCamera}
                                className="rounded-md border border-[var(--border-line)] px-3 py-1.5 text-sm text-[var(--text-muted)] transition hover:bg-[var(--bg-subtle)]"
                            >
                                Tutup
                            </button>
                        </div>

                        <div className="p-5">
                            {!photo ? (
                                <>
                                    <div className="overflow-hidden rounded-xl border border-[var(--border-line)] bg-slate-950">
                                        <video
                                            ref={videoRef}
                                            autoPlay
                                            playsInline
                                            muted
                                            className="aspect-[4/3] w-full object-cover"
                                        />
                                    </div>
                                    <p className="mt-4 rounded-xl border border-[var(--border-line)] bg-[var(--bg-subtle)] px-4 py-3 text-sm text-[var(--text-muted)]">
                                        {faceMessage}
                                    </p>
                                    <button type="button" onClick={capturePhoto} disabled={capturingFace} className="ui-button-primary mt-5 w-full disabled:cursor-not-allowed disabled:opacity-60">
                                        {capturingFace ? "Memproses wajah..." : "Ambil Foto"}
                                    </button>
                                </>
                            ) : (
                                <>
                                    <div className="overflow-hidden rounded-xl border border-[var(--border-line)] bg-slate-950">
                                        <img src={photo} alt="Captured attendance" className="aspect-[4/3] w-full object-cover" />
                                    </div>
                                    <p className="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                                        {faceMessage}
                                    </p>
                                    <div className="mt-5 flex flex-col gap-3 sm:flex-row">
                                        <button type="button" onClick={() => { setPhoto(null); setData("face_descriptor", []); }} className="ui-button-secondary flex-1">
                                            Ulangi Foto
                                        </button>
                                        <button
                                            type="button"
                                            onClick={submitAttendance}
                                            disabled={processingAttendance || !attendanceAllowed || data.face_descriptor.length !== 128}
                                            className="flex-1 rounded-md bg-[var(--primary-color)] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[var(--primary-strong)] disabled:cursor-not-allowed disabled:opacity-60"
                                        >
                                            {processingAttendance ? "Mengirim..." : "Konfirmasi Absensi"}
                                        </button>
                                    </div>
                                    {!attendanceAllowed && (
                                        <p className="mt-4 text-sm text-[var(--danger-color)]">
                                            Anda berada di luar area kantor, sehingga absensi belum bisa dikirim.
                                        </p>
                                    )}
                                </>
                            )}
                        </div>
                    </div>
                </div>
            )}

            <canvas ref={canvasRef} className="hidden" />
        </AuthenticatedLayout>
    );
}
