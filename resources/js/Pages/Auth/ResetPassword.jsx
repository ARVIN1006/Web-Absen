import { useState, useEffect } from "react";
import { Head, Link, useForm } from "@inertiajs/react";

export default function ResetPassword({ token, email }) {
    const [theme, setTheme] = useState("dark");
    const { data, setData, post, processing, errors, reset } = useForm({
        token: token,
        email: email,
        password: "",
        password_confirmation: "",
    });

    useEffect(() => {
        const savedTheme = localStorage.getItem("theme") || "dark";
        setTheme(savedTheme);
        document.documentElement.setAttribute("data-theme", savedTheme);
    }, []);

    const submit = (e) => {
        e.preventDefault();
        post(route("password.store"), {
            onFinish: () => reset("password", "password_confirmation"),
        });
    };

    return (
        <>
            <Head title="Reset Password" />
            
            <div className="min-h-screen flex bg-[#0f1117]" data-theme={theme}>
                {/* Left Panel - Branding */}
                <div className="hidden lg:flex flex-1 flex-col items-center justify-center p-10 relative overflow-hidden bg-gradient-to-br from-[#0d1b2a] via-[#0f2744] to-[#071c38] border-r border-white/10">
                    <div className="absolute top-[-80px] left-[-80px] w-[400px] h-[400px] rounded-full bg-blue-500/20 blur-3xl pointer-events-none" />
                    <div className="absolute bottom-[-60px] right-[-60px] w-[300px] h-[300px] rounded-full bg-green-500/20 blur-3xl pointer-events-none" />
                    
                    <div className="relative z-10 text-center max-w-[380px]">
                        <div className="w-[90px] h-auto mx-auto mb-6">
                            <svg viewBox="0 0 100 100" className="w-full h-auto">
                                <circle cx="50" cy="50" r="45" fill="rgba(16,185,129,0.2)" stroke="#10b981" strokeWidth="3" />
                                <rect x="35" y="45" width="30" height="25" rx="3" fill="none" stroke="#34d399" strokeWidth="3" />
                                <circle cx="50" cy="35" r="8" fill="none" stroke="#34d399" strokeWidth="3" />
                                <path d="M42 52 L48 58 L58 48" fill="none" stroke="#34d399" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        </div>
                        <h1 className="text-[26px] font-extrabold text-white mb-3 leading-tight">
                            Password Baru
                        </h1>
                        <p className="text-sm text-white/50 leading-relaxed">
                            Buat password baru yang kuat untuk melindungi akun Anda.
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

                        <h2 className="text-2xl font-bold text-white mb-2">Reset Password</h2>
                        <p className="text-[#9ca3af] text-sm mb-8">
                            Masukkan password baru untuk akun {email}
                        </p>

                        <form onSubmit={submit} className="space-y-5">
                            <input type="hidden" name="token" value={data.token} />

                            <div>
                                <label className="block text-xs font-semibold text-[#6b7280] uppercase tracking-wider mb-2">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData("email", e.target.value)}
                                    className="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white focus:border-green-500 focus:outline-none transition-all"
                                    required
                                    readOnly
                                />
                                {errors.email && (
                                    <p className="mt-2 text-sm text-red-500">{errors.email}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-[#6b7280] uppercase tracking-wider mb-2">
                                    Password Baru
                                </label>
                                <input
                                    type="password"
                                    value={data.password}
                                    onChange={(e) => setData("password", e.target.value)}
                                    className="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/30 focus:border-green-500 focus:outline-none transition-all"
                                    placeholder="••••••••"
                                    required
                                />
                                {errors.password && (
                                    <p className="mt-2 text-sm text-red-500">{errors.password}</p>
                                )}
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
                                {errors.password_confirmation && (
                                    <p className="mt-2 text-sm text-red-500">{errors.password_confirmation}</p>
                                )}
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full py-3.5 rounded-xl font-bold text-white bg-gradient-to-r from-green-500 to-green-700 shadow-lg shadow-green-500/30 hover:shadow-green-500/50 hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? "Menyimpan..." : "Reset Password"}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
}
