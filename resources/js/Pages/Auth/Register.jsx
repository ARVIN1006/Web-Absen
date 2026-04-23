import { useState, useEffect, useRef } from "react";
import { Head, Link, useForm } from "@inertiajs/react";
import PublicDemoGuide from "@/Components/PublicDemoGuide";
import { extractFaceDescriptorFromImage, loadFaceModels } from "@/Utils/faceDescriptor";

export default function Register({ departments, positions }) {
    const [theme, setTheme] = useState("dark");
    const [step, setStep] = useState(1); // 1: form, 2: face capture
    const [photo, setPhoto] = useState(null);
    const [faceMessage, setFaceMessage] = useState("Memuat model verifikasi wajah...");
    const [capturingFace, setCapturingFace] = useState(false);
    const videoRef = useRef(null);
    const canvasRef = useRef(null);

    const { data, setData, post, processing, errors } = useForm({
        name: "",
        email: "",
        position_id: "",
        department_id: "",
        password: "",
        password_confirmation: "",
        face_data: "",
        face_descriptor: [],
    });

    useEffect(() => {
        const savedTheme = localStorage.getItem("theme") || "dark";
        setTheme(savedTheme);
        document.documentElement.setAttribute("data-theme", savedTheme);
        loadFaceModels()
            .then(() => setFaceMessage("Model wajah siap. Silakan ambil foto dengan wajah terlihat jelas."))
            .catch(() => setFaceMessage("Model wajah gagal dimuat. Refresh halaman lalu coba lagi."));
    }, []);

    // Camera functions
    const startCamera = async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            if (videoRef.current) {
                videoRef.current.srcObject = stream;
            }
        } catch (err) {
            console.error("Camera error:", err);
            alert("Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.");
        }
    };

    const stopCamera = () => {
        if (videoRef.current && videoRef.current.srcObject) {
            videoRef.current.srcObject.getTracks().forEach((track) => track.stop());
            videoRef.current.srcObject = null;
        }
    };

    const capturePhoto = async () => {
        if (!videoRef.current || !canvasRef.current) return;

        setCapturingFace(true);
        setFaceMessage("Mendeteksi wajah dan membuat descriptor...");

        const video = videoRef.current;
        const canvas = canvasRef.current;
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const ctx = canvas.getContext("2d");
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = canvas.toDataURL("image/jpeg", 0.8);

        try {
            const descriptor = await extractFaceDescriptorFromImage(imageData);
            setPhoto(imageData);
            setData({
                ...data,
                face_data: imageData,
                face_descriptor: descriptor,
            });
            setFaceMessage("Wajah berhasil terdaftar. Descriptor biometrik siap dipakai untuk presensi.");
            stopCamera();
        } catch (error) {
            setFaceMessage(error.message);
        } finally {
            setCapturingFace(false);
        }
    };

    const retakePhoto = () => {
        setPhoto(null);
        setData({
            ...data,
            face_data: "",
            face_descriptor: [],
        });
        setFaceMessage("Silakan ambil ulang foto wajah.");
        setTimeout(startCamera, 100);
    };

    const nextStep = (e) => {
        e.preventDefault();
        setStep(2);
        setTimeout(startCamera, 100);
    };

    const submit = (e) => {
        e.preventDefault();
        post(route("register"));
    };

    // Cleanup camera on unmount
    useEffect(() => {
        return () => stopCamera();
    }, []);

    return (
        <>
            <Head title="Daftar Akun" />
            
            <div className="min-h-screen flex bg-[#0f1117]" data-theme={theme}>
                {/* Left Panel - Branding */}
                <div className="hidden lg:flex flex-1 flex-col items-center justify-center p-10 relative overflow-hidden bg-gradient-to-br from-[#0d1b2a] via-[#0f2744] to-[#071c38] border-r border-white/10">
                    <div className="absolute top-[-80px] left-[-80px] w-[400px] h-[400px] rounded-full bg-blue-500/20 blur-3xl pointer-events-none" />
                    <div className="absolute bottom-[-60px] right-[-60px] w-[300px] h-[300px] rounded-full bg-green-500/20 blur-3xl pointer-events-none" />
                    
                    <div className="relative z-10 text-center max-w-[380px]">
                        <div className="w-[90px] h-auto mx-auto mb-6">
                            <svg viewBox="0 0 100 100" className="w-full h-auto">
                                <circle cx="50" cy="50" r="45" fill="rgba(16,185,129,0.2)" stroke="#10b981" strokeWidth="3" />
                                <circle cx="50" cy="40" r="15" fill="none" stroke="#34d399" strokeWidth="2" />
                                <path d="M30 75 Q50 55 70 75" fill="none" stroke="#34d399" strokeWidth="2" />
                            </svg>
                        </div>
                        <h1 className="text-[26px] font-extrabold text-white mb-3 leading-tight">
                            Bergabung Sekarang
                        </h1>
                        <p className="text-sm text-white/50 leading-relaxed">
                            Daftar untuk mengakses sistem absensi dengan teknologi pengenalan wajah.
                        </p>
                        
                        <div className="mt-8 p-4 rounded-2xl bg-yellow-500/10 border border-yellow-500/20 text-left">
                            <div className="text-xs font-bold text-yellow-500 uppercase tracking-wider mb-2">
                                Informasi Penting
                            </div>
                            <p className="text-xs text-white/50 leading-relaxed">
                                Pastikan wajah terlihat jelas saat pengambilan foto untuk verifikasi yang akurat.
                            </p>
                        </div>
                    </div>
                </div>

                {/* Right Panel - Form */}
                <div className="flex-1 flex items-center justify-center p-6 lg:p-10 overflow-y-auto">
                    <div className="w-full max-w-[420px] py-8">
                        <h2 className="text-2xl font-bold text-white mb-2">
                            {step === 1 ? "Buat Akun Baru" : "Verifikasi Wajah"}
                        </h2>
                        <p className="text-[#9ca3af] text-sm mb-8">
                            {step === 1 ? (
                                <>
                                    Sudah punya akun?{" "}
                                    <Link href={route("login")} className="text-blue-500 hover:underline font-medium">
                                        Masuk disini
                                    </Link>
                                </>
                            ) : (
                                "Ambil foto wajah Anda untuk verifikasi"
                            )}
                        </p>

                        {step === 1 ? (
                            <form onSubmit={nextStep} className="space-y-5">
                                <div>
                                    <label className="block text-xs font-semibold text-[#6b7280] uppercase tracking-wider mb-2">
                                        Nama Lengkap
                                    </label>
                                    <input
                                        type="text"
                                        value={data.name}
                                        onChange={(e) => setData("name", e.target.value)}
                                        className="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/30 focus:border-green-500 focus:outline-none transition-all"
                                        placeholder="John Doe"
                                        required
                                    />
                                    {errors.name && <p className="mt-2 text-sm text-red-500">{errors.name}</p>}
                                </div>

                                <div>
                                    <label className="block text-xs font-semibold text-[#6b7280] uppercase tracking-wider mb-2">
                                        Email
                                    </label>
                                    <input
                                        type="email"
                                        value={data.email}
                                        onChange={(e) => setData("email", e.target.value)}
                                        className="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/30 focus:border-green-500 focus:outline-none transition-all"
                                        placeholder="nama@email.com"
                                        required
                                    />
                                    {errors.email && <p className="mt-2 text-sm text-red-500">{errors.email}</p>}
                                </div>

                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-xs font-semibold text-[#6b7280] uppercase tracking-wider mb-2">
                                            Jabatan
                                        </label>
                                        <select
                                            value={data.position_id}
                                            onChange={(e) => setData("position_id", e.target.value)}
                                            className="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white focus:border-green-500 focus:outline-none transition-all"
                                            required
                                        >
                                            <option value="" className="bg-[#1a1d27]">Pilih...</option>
                                            {positions.map((pos) => (
                                                <option key={pos.id} value={pos.id} className="bg-[#1a1d27]">
                                                    {pos.name}
                                                </option>
                                            ))}
                                        </select>
                                        {errors.position_id && <p className="mt-2 text-sm text-red-500">{errors.position_id}</p>}
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-[#6b7280] uppercase tracking-wider mb-2">
                                            Departemen
                                        </label>
                                        <select
                                            value={data.department_id}
                                            onChange={(e) => setData("department_id", e.target.value)}
                                            className="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white focus:border-green-500 focus:outline-none transition-all"
                                            required
                                        >
                                            <option value="" className="bg-[#1a1d27]">Pilih...</option>
                                            {departments.map((dept) => (
                                                <option key={dept.id} value={dept.id} className="bg-[#1a1d27]">
                                                    {dept.name}
                                                </option>
                                            ))}
                                        </select>
                                        {errors.department_id && <p className="mt-2 text-sm text-red-500">{errors.department_id}</p>}
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-xs font-semibold text-[#6b7280] uppercase tracking-wider mb-2">
                                        Password
                                    </label>
                                    <input
                                        type="password"
                                        value={data.password}
                                        onChange={(e) => setData("password", e.target.value)}
                                        className="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/30 focus:border-green-500 focus:outline-none transition-all"
                                        placeholder="••••••••"
                                        required
                                    />
                                    {errors.password && <p className="mt-2 text-sm text-red-500">{errors.password}</p>}
                                </div>

                                <div>
                                    <label className="block text-xs font-semibold text-[#6b7280] uppercase tracking-wider mb-2">
                                        Konfirmasi Password
                                    </label>
                                    <input
                                        type="password"
                                        value={data.password_confirmation}
                                        onChange={(e) => setData("password_confirmation", e.target.value)}
                                        className="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/30 focus:border-green-500 focus:outline-none transition-all"
                                        placeholder="••••••••"
                                        required
                                    />
                                </div>

                                <button
                                    type="submit"
                                    className="w-full py-3.5 rounded-xl font-bold text-white bg-gradient-to-r from-green-500 to-green-700 shadow-lg shadow-green-500/30 hover:shadow-green-500/50 hover:-translate-y-0.5 transition-all"
                                >
                                    Lanjutkan →
                                </button>
                            </form>
                        ) : (
                            <form onSubmit={submit} className="space-y-5">
                                {!photo ? (
                                    <>
                                        <div className="relative aspect-[4/3] bg-black rounded-2xl overflow-hidden border-2 border-dashed border-white/20">
                                            <video
                                                ref={videoRef}
                                                autoPlay
                                                playsInline
                                                muted
                                                className="w-full h-full object-cover"
                                            />
                                            <div className="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                <div className="w-48 h-48 rounded-full border-2 border-white/30" />
                                            </div>
                                        </div>
                                        <p className="text-center text-sm text-[#9ca3af]">
                                            Posisikan wajah Anda di dalam lingkaran
                                        </p>
                                        <p className="rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-center text-sm text-[#9ca3af]">
                                            {faceMessage}
                                        </p>
                                        <button
                                            type="button"
                                            onClick={capturePhoto}
                                            disabled={capturingFace}
                                            className="w-full py-3.5 rounded-xl font-bold text-white bg-gradient-to-r from-green-500 to-green-700 shadow-lg shadow-green-500/30 hover:shadow-green-500/50 hover:-translate-y-0.5 transition-all disabled:opacity-50"
                                        >
                                            {capturingFace ? "Memproses wajah..." : "Ambil Foto"}
                                        </button>
                                    </>
                                ) : (
                                    <>
                                        <div className="relative aspect-[4/3] bg-black rounded-2xl overflow-hidden">
                                            <img src={photo} alt="Captured" className="w-full h-full object-cover" />
                                        </div>
                                        <p className="rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3 text-center text-sm text-green-300">
                                            {faceMessage}
                                        </p>
                                        <div className="flex gap-3">
                                            <button
                                                type="button"
                                                onClick={retakePhoto}
                                                className="flex-1 py-3.5 rounded-xl font-bold bg-white/10 text-white border border-white/20 hover:bg-white/20 transition-all"
                                            >
                                                🔄 Ulangi
                                            </button>
                                            <button
                                                type="submit"
                                                disabled={processing || data.face_descriptor.length !== 128}
                                                className="flex-1 py-3.5 rounded-xl font-bold text-white bg-gradient-to-r from-green-500 to-green-700 shadow-lg shadow-green-500/30 hover:shadow-green-500/50 hover:-translate-y-0.5 transition-all disabled:opacity-50"
                                            >
                                                {processing ? "Mendaftar..." : "✅ Daftar"}
                                            </button>
                                        </div>
                                    </>
                                )}

                                {errors.face_data && <p className="text-sm text-red-500 text-center">{errors.face_data}</p>}

                                <button
                                    type="button"
                                    onClick={() => {
                                        stopCamera();
                                        setStep(1);
                                    }}
                                    className="w-full py-2 text-sm text-[#6b7280] hover:text-white transition-colors"
                                >
                                    ← Kembali ke form
                                </button>
                            </form>
                        )}
                    </div>
                </div>
            </div>

            <canvas ref={canvasRef} className="hidden" />
            <PublicDemoGuide current="register" />
        </>
    );
}
