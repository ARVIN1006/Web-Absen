import { useState, useEffect } from "react";
import { Head, Link, useForm } from "@inertiajs/react";

export default function ForgotPassword({ status }) {
    const [theme, setTheme] = useState("dark");
    const { data, setData, post, processing, errors } = useForm({
        email: "",
    });

    useEffect(() => {
        const savedTheme = localStorage.getItem("theme") || "dark";
        setTheme(savedTheme);
        document.documentElement.setAttribute("data-theme", savedTheme);
    }, []);

    const submit = (e) => {
        e.preventDefault();
        post(route("password.email"));
    };

    return (
        <>
            <Head title="Lupa Password" />
            
            <div className="min-h-screen flex bg-[#0f1117]" data-theme={theme}>
                {/* Left Panel - Branding */}
                <div className="hidden lg:flex flex-1 flex-col items-center justify-center p-10 relative overflow-hidden bg-gradient-to-br from-[#0d1b2a] via-[#0f2744] to-[#071c38] border-r border-white/10">
                    <div className="absolute top-[-80px] left-[-80px] w-[400px] h-[400px] rounded-full bg-blue-500/20 blur-3xl pointer-events-none" />
                    <div className="absolute bottom-[-60px] right-[-60px] w-[300px] h-[300px] rounded-full bg-orange-500/20 blur-3xl pointer-events-none" />
                    
                    <div className="relative z-10 text-center max-w-[380px]">
                        <div className="w-[90px] h-auto mx-auto mb-6">
                            <svg viewBox="0 0 100 100" className="w-full h-auto">
                                <circle cx="50" cy="50" r="45" fill="rgba(245,158,11,0.2)" stroke="#f59e0b" strokeWidth="3" />
                                <path d="M35 45 L45 55 L65 35" fill="none" stroke="#fbbf24" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />
                                <circle cx="50" cy="70" r="5" fill="#fbbf24" />
                            </svg>
                        </div>
                        <h1 className="text-[26px] font-extrabold text-white mb-3 leading-tight">
                            Pemulihan Akun
                        </h1>
                        <p className="text-sm text-white/50 leading-relaxed">
                            Kami akan membantu Anda mengakses kembali akun Anda.
                        </p>
                    </div>
                </div>

                {/* Right Panel - Form */}
                <div className="flex-1 flex items-center justify-center p-6 lg:p-10">
                    <div className="w-full max-w-[420px]">
                        <Link
                            href={route("login")}
                            className="inline-flex items-center gap-2 text-sm text-[#6b7280] hover:text-blue-500 mb-6 transition-colors"
                        >
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali ke Login
                        </Link>

                        <h2 className="text-2xl font-bold text-white mb-2">Lupa Password?</h2>
                        <p className="text-[#9ca3af] text-sm mb-8">
                            Masukkan email Anda dan kami akan mengirimkan link untuk reset password.
                        </p>

                        {status && (
                            <div className="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-500 text-sm">
                                {status}
                            </div>
                        )}

                        <form onSubmit={submit} className="space-y-5">
                            <div>
                                <label className="block text-xs font-semibold text-[#6b7280] uppercase tracking-wider mb-2">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData("email", e.target.value)}
                                    className="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/30 focus:border-orange-500 focus:outline-none transition-all"
                                    placeholder="nama@email.com"
                                    required
                                />
                                {errors.email && (
                                    <p className="mt-2 text-sm text-red-500">{errors.email}</p>
                                )}
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full py-3.5 rounded-xl font-bold text-white bg-gradient-to-r from-orange-500 to-orange-700 shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? "Mengirim..." : "Kirim Link Reset"}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
}
