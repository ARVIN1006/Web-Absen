import { useState, useEffect } from "react";
import { Head, Link, useForm } from "@inertiajs/react";
import PublicDemoGuide from "@/Components/PublicDemoGuide";

export default function Login({ status }) {
    const [theme, setTheme] = useState("dark");
    const { data, setData, post, processing, errors } = useForm({
        email: "",
        password: "",
        remember: false,
    });

    useEffect(() => {
        const savedTheme = localStorage.getItem("theme") || "dark";
        setTheme(savedTheme);
        document.documentElement.setAttribute("data-theme", savedTheme);
    }, []);

    const submit = (e) => {
        e.preventDefault();
        post(route("login"));
    };

    return (
        <>
            <Head title="Login" />
            
            <div className="min-h-screen flex bg-[#0f1117]" data-theme={theme}>
                {/* Left Panel - Branding */}
                <div className="hidden lg:flex flex-1 flex-col items-center justify-center p-10 relative overflow-hidden bg-gradient-to-br from-[#0d1b2a] via-[#0f2744] to-[#071c38] border-r border-white/10">
                    <div className="absolute top-[-80px] left-[-80px] w-[400px] h-[400px] rounded-full bg-blue-500/20 blur-3xl pointer-events-none" />
                    <div className="absolute bottom-[-60px] right-[-60px] w-[300px] h-[300px] rounded-full bg-green-500/20 blur-3xl pointer-events-none" />
                    
                    <div className="relative z-10 text-center max-w-[380px]">
                        <div className="w-[90px] h-auto mx-auto mb-6">
                            <svg viewBox="0 0 100 100" className="w-full h-auto">
                                <circle cx="50" cy="50" r="45" fill="rgba(59,130,246,0.2)" stroke="#3b82f6" strokeWidth="3" />
                                <circle cx="50" cy="40" r="15" fill="none" stroke="#60a5fa" strokeWidth="2" />
                                <path d="M30 75 Q50 55 70 75" fill="none" stroke="#60a5fa" strokeWidth="2" />
                            </svg>
                        </div>
                        <h1 className="text-[26px] font-extrabold text-white mb-3 leading-tight">
                            Selamat Datang
                        </h1>
                        <p className="text-sm text-white/50 leading-relaxed">
                            Sistem Absensi dengan Pengenalan Wajah untuk keamanan dan kemudahan Anda.
                        </p>
                        
                        <div className="mt-8 p-4 rounded-2xl bg-yellow-500/10 border border-yellow-500/20 text-left">
                            <div className="text-xs font-bold text-yellow-500 uppercase tracking-wider mb-2">
                                Akun Demo
                            </div>
                            <div className="space-y-3 text-xs text-white/60">
                                <div className="rounded-xl bg-white/5 p-3">
                                    <div className="font-bold text-white">Admin HR</div>
                                    <div className="mt-1 font-mono">admin@gmail.com / password</div>
                                    <div className="mt-1 text-white/40">Dashboard admin, approval, payroll, laporan</div>
                                </div>
                                <div className="rounded-xl bg-white/5 p-3">
                                    <div className="font-bold text-white">Karyawan</div>
                                    <div className="mt-1 font-mono">test@gmail.com / password</div>
                                    <div className="mt-1 text-white/40">Presensi wajah, cuti, reimbursement, profil</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Right Panel - Form */}
                <div className="flex-1 flex items-center justify-center p-6 lg:p-10">
                    <div className="w-full max-w-[420px]">
                        {status && (
                            <div className="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-500 text-sm">
                                {status}
                            </div>
                        )}

                        <h2 className="text-2xl font-bold text-white mb-2">Masuk ke Akun</h2>
                        <p className="text-[#9ca3af] text-sm mb-8">
                            Belum punya akun?{" "}
                            <Link href={route("register")} className="text-blue-500 hover:underline font-medium">
                                Daftar sekarang
                            </Link>
                        </p>

                        <form onSubmit={submit} className="space-y-5">
                            <div>
                                <label className="block text-xs font-semibold text-[#6b7280] uppercase tracking-wider mb-2">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData("email", e.target.value)}
                                    className="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/30 focus:border-blue-500 focus:outline-none transition-all"
                                    placeholder="nama@email.com"
                                    required
                                />
                                {errors.email && (
                                    <p className="mt-2 text-sm text-red-500">{errors.email}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-[#6b7280] uppercase tracking-wider mb-2">
                                    Password
                                </label>
                                <input
                                    type="password"
                                    value={data.password}
                                    onChange={(e) => setData("password", e.target.value)}
                                    className="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/30 focus:border-blue-500 focus:outline-none transition-all"
                                    placeholder="••••••••"
                                    required
                                />
                                {errors.password && (
                                    <p className="mt-2 text-sm text-red-500">{errors.password}</p>
                                )}
                            </div>

                            <div className="flex items-center justify-between">
                                <label className="flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        checked={data.remember}
                                        onChange={(e) => setData("remember", e.target.checked)}
                                        className="w-4 h-4 rounded border-white/20 bg-white/5 text-blue-500 focus:ring-blue-500"
                                    />
                                    <span className="text-sm text-[#9ca3af]">Ingat saya</span>
                                </label>
                                <Link
                                    href={route("password.request")}
                                    className="text-sm text-blue-500 hover:underline"
                                >
                                    Lupa password?
                                </Link>
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full py-3.5 rounded-xl font-bold text-white bg-gradient-to-r from-blue-500 to-blue-700 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? "Memuat..." : "Masuk"}
                            </button>
                        </form>

                        <div className="mt-8 text-center">
                            <Link
                                href={route("guide")}
                                className="inline-flex items-center gap-2 text-sm text-[#6b7280] hover:text-blue-500 transition-colors"
                            >
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Panduan Penggunaan
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <PublicDemoGuide current="login" />
        </>
    );
}
