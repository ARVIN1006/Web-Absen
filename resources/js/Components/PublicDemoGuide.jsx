import { Link } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";

function Icon({ path, className = "h-4 w-4" }) {
    return (
        <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d={path} />
        </svg>
    );
}

export default function PublicDemoGuide({ current = "login" }) {
    const [isOpen, setIsOpen] = useState(false);
    const [activeStep, setActiveStep] = useState(0);

    const steps = useMemo(() => [
        {
            key: "login",
            title: "Mulai dari halaman login",
            description: "Jelaskan bahwa client bisa langsung mencoba akun demo tanpa membuat data baru terlebih dahulu.",
            href: route("login"),
            cta: "Buka Login",
            note: "Akun demo: admin@gmail.com / password",
        },
        {
            key: "register",
            title: "Lihat proses registrasi",
            description: "Perlihatkan input data karyawan, pilihan jabatan/departemen, lalu tahap pengambilan wajah.",
            href: route("register"),
            cta: "Buka Register",
            note: "Cocok untuk menjelaskan enrollment wajah pertama kali.",
        },
        {
            key: "guide",
            title: "Baca panduan singkat",
            description: "Jika client ingin konteks sebelum login, halaman panduan menjelaskan fitur utama dalam format ringkas.",
            href: route("guide"),
            cta: "Buka Panduan",
            note: "Gunakan ini sebagai briefing sebelum masuk dashboard.",
        },
        {
            key: "after-login",
            title: "Lanjut ke tour aplikasi",
            description: "Setelah login, tombol Demo Tour di kanan bawah akan memandu alur admin atau karyawan sesuai role.",
            href: route("login"),
            cta: "Masuk Demo",
            note: "Tour internal otomatis menyesuaikan role pengguna.",
        },
    ], []);

    const currentStep = steps[activeStep] ?? steps[0];

    useEffect(() => {
        const matchedIndex = steps.findIndex((step) => step.key === current);

        if (matchedIndex >= 0) {
            setActiveStep(matchedIndex);
        }
    }, [current, steps]);

    useEffect(() => {
        const hasSeenPublicGuide = window.localStorage.getItem("absensi-public-demo-guide-seen");

        if (!hasSeenPublicGuide) {
            const opener = window.setTimeout(() => setIsOpen(true), 700);
            window.localStorage.setItem("absensi-public-demo-guide-seen", "true");
            return () => window.clearTimeout(opener);
        }
    }, []);

    return (
        <>
            <button
                type="button"
                onClick={() => setIsOpen(true)}
                className="fixed bottom-5 right-5 z-40 inline-flex items-center gap-2 rounded-full bg-white px-4 py-3 text-sm font-extrabold text-slate-950 shadow-[0_18px_55px_rgba(0,0,0,0.38)] transition hover:-translate-y-0.5 hover:bg-sky-50"
            >
                <Icon path="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
                Panduan Demo
            </button>

            <div
                className={`fixed inset-0 z-[70] bg-black/55 backdrop-blur-[2px] transition ${
                    isOpen ? "opacity-100" : "pointer-events-none opacity-0"
                }`}
                onClick={() => setIsOpen(false)}
            />

            <section
                className={`fixed bottom-4 left-4 right-4 z-[80] overflow-hidden rounded-[30px] border border-white/15 bg-[#101827] text-white shadow-[0_30px_90px_rgba(0,0,0,0.45)] transition duration-300 md:left-auto md:right-6 md:w-[430px] ${
                    isOpen ? "translate-y-0 opacity-100" : "pointer-events-none translate-y-6 opacity-0"
                }`}
                aria-label="Panduan demo halaman awal"
            >
                <div className="border-b border-white/10 bg-gradient-to-br from-sky-500/20 via-transparent to-emerald-500/10 px-5 py-5">
                    <div className="flex items-start justify-between gap-4">
                        <div>
                            <p className="text-[11px] font-bold uppercase tracking-[0.2em] text-sky-200">First Visit Guide</p>
                            <h2 className="mt-2 text-xl font-extrabold">Panduan sejak halaman awal</h2>
                        </div>
                        <button
                            type="button"
                            onClick={() => setIsOpen(false)}
                            className="rounded-full bg-white/10 p-2 transition hover:bg-white/20"
                            aria-label="Tutup panduan"
                        >
                            <Icon path="M6 18L18 6M6 6l12 12" />
                        </button>
                    </div>
                </div>

                <div className="max-h-[calc(100vh-12rem)] overflow-y-auto p-5">
                    <div className="rounded-[24px] border border-white/10 bg-white/5 p-4">
                        <span className="rounded-full bg-sky-400 px-3 py-1 text-xs font-extrabold text-slate-950">
                            Step {activeStep + 1}/{steps.length}
                        </span>
                        <h3 className="mt-4 text-lg font-extrabold">{currentStep.title}</h3>
                        <p className="mt-2 text-sm leading-6 text-slate-300">{currentStep.description}</p>
                        <p className="mt-3 rounded-2xl bg-amber-400/10 px-3 py-2 text-xs leading-5 text-amber-100">
                            {currentStep.note}
                        </p>

                        <div className="mt-4 flex flex-wrap gap-2">
                            <Link
                                href={currentStep.href}
                                onClick={() => setIsOpen(false)}
                                className="inline-flex items-center justify-center rounded-full bg-sky-400 px-4 py-2.5 text-sm font-extrabold text-slate-950 transition hover:bg-sky-300"
                            >
                                {currentStep.cta}
                            </Link>
                            <button
                                type="button"
                                onClick={() => setActiveStep((value) => Math.min(value + 1, steps.length - 1))}
                                disabled={activeStep === steps.length - 1}
                                className="inline-flex items-center justify-center rounded-full border border-white/15 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-40"
                            >
                                Step Berikutnya
                            </button>
                        </div>
                    </div>

                    <div className="mt-4 rounded-[24px] border border-emerald-300/20 bg-emerald-300/10 p-4">
                        <div className="text-[11px] font-bold uppercase tracking-[0.18em] text-emerald-200">Akun yang bisa dicoba client</div>
                        <div className="mt-3 grid gap-2 text-xs text-emerald-50">
                            <div className="rounded-2xl bg-black/20 p-3">
                                <div className="font-bold">Admin HR</div>
                                <div className="mt-1 font-mono">admin@gmail.com / password</div>
                                <div className="mt-1 text-emerald-100/70">Untuk demo dashboard, master data, approval, payroll, laporan.</div>
                            </div>
                            <div className="rounded-2xl bg-black/20 p-3">
                                <div className="font-bold">Karyawan</div>
                                <div className="mt-1 font-mono">test@gmail.com / password</div>
                                <div className="mt-1 text-emerald-100/70">Untuk demo presensi wajah, cuti, reimbursement, profil.</div>
                            </div>
                        </div>
                    </div>

                    <div className="mt-4 grid gap-2">
                        {steps.map((step, index) => (
                            <button
                                key={step.key}
                                type="button"
                                onClick={() => setActiveStep(index)}
                                className={`flex items-start gap-3 rounded-2xl border px-4 py-3 text-left transition ${
                                    index === activeStep ? "border-sky-300 bg-sky-300/10" : "border-white/10 bg-white/[0.03] hover:bg-white/[0.07]"
                                }`}
                            >
                                <span
                                    className={`flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-extrabold ${
                                        index === activeStep ? "bg-sky-300 text-slate-950" : "bg-white/10 text-slate-300"
                                    }`}
                                >
                                    {index + 1}
                                </span>
                                <span>
                                    <span className="block text-sm font-bold">{step.title}</span>
                                    <span className="mt-1 block text-xs text-slate-400">{step.cta}</span>
                                </span>
                            </button>
                        ))}
                    </div>
                </div>
            </section>
        </>
    );
}
