import { useEffect, useMemo, useRef, useState } from "react";
import { Link, router, usePage } from "@inertiajs/react";
import NavLink from "@/Components/NavLink";

function Icon({ path, className = "h-[18px] w-[18px]" }) {
    return (
        <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d={path} />
        </svg>
    );
}

function SidebarGroup({ title, children }) {
    return (
        <div className="pt-5 first:pt-0">
            <div className="px-6 pb-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--text-dark-muted)]">
                {title}
            </div>
            <div>{children}</div>
        </div>
    );
}

function UserAvatar({ name, small = false }) {
    const sizeClass = small ? "h-8 w-8 text-xs" : "h-10 w-10 text-sm";

    return (
        <div className={`${sizeClass} flex items-center justify-center rounded-full bg-slate-200 font-semibold text-slate-700 overflow-hidden`}>
            {name?.charAt(0)?.toUpperCase() || "U"}
        </div>
    );
}

function DemoTourPanel({ userRole }) {
    const [isOpen, setIsOpen] = useState(false);
    const [activeStep, setActiveStep] = useState(0);

    const employeeSteps = useMemo(() => [
        {
            title: "Mulai dari dashboard",
            description: "Tunjukkan ringkasan status hari ini, pengumuman, dan riwayat absensi sebagai landing page karyawan.",
            href: route("dashboard"),
            routeName: "dashboard",
            cta: "Buka Dashboard",
        },
        {
            title: "Demo presensi wajah",
            description: "Arahkan client ke proses kamera, lokasi, dan validasi wajah. Ini adalah fitur utama yang paling perlu dilihat.",
            href: route("attendance.index"),
            routeName: "attendance.index",
            cta: "Buka Presensi",
        },
        {
            title: "Self-service HR",
            description: "Perlihatkan alur pengajuan cuti agar client memahami proses request dari sisi karyawan.",
            href: route("employee.leave-requests.index"),
            routeName: "employee.leave-requests.*",
            cta: "Lihat Cuti",
        },
        {
            title: "Profil dan data pribadi",
            description: "Tutup demo karyawan dengan profil, dokumen, dan data personal yang bisa dikelola sendiri.",
            href: route("profile.index"),
            routeName: "profile.*",
            cta: "Buka Profil",
        },
    ], []);

    const adminSteps = useMemo(() => [
        {
            title: "Executive overview",
            description: "Mulai dari KPI kehadiran, absensi terbaru, dan tren 7 hari untuk memberi gambaran nilai bisnis.",
            href: route("admin.dashboard"),
            routeName: "admin.dashboard",
            cta: "Buka Admin",
        },
        {
            title: "Master data karyawan",
            description: "Perlihatkan data karyawan, struktur organisasi, posisi, dan kelengkapan profil HRIS.",
            href: route("admin.employees.index"),
            routeName: "admin.employees.*",
            cta: "Lihat Karyawan",
        },
        {
            title: "Rekap dan audit presensi",
            description: "Masuk ke rekap kehadiran untuk menunjukkan bukti presensi, status, koreksi, dan kontrol admin.",
            href: route("admin.attendances.index"),
            routeName: "admin.attendances.*",
            cta: "Buka Rekap",
        },
        {
            title: "Approval center",
            description: "Tunjukkan keputusan cuti, reimbursement, dan koreksi absensi dari satu tempat terpusat.",
            href: route("admin.approvals"),
            routeName: "admin.approvals",
            cta: "Buka Approval",
        },
        {
            title: "Laporan analitik",
            description: "Akhiri dengan heatmap performa atau labor cost untuk memperlihatkan insight manajemen.",
            href: route("admin.reports.performance-heatmap"),
            routeName: "admin.reports.*",
            cta: "Buka Laporan",
        },
    ], []);

    const steps = useMemo(() => (userRole === "admin" ? adminSteps : employeeSteps), [adminSteps, employeeSteps, userRole]);
    const currentStep = steps[activeStep] ?? steps[0];
    const progress = Math.round(((activeStep + 1) / steps.length) * 100);

    useEffect(() => {
        const hasSeenTour = window.localStorage.getItem("absensi-demo-tour-seen");

        if (!hasSeenTour) {
            const opener = window.setTimeout(() => setIsOpen(true), 800);
            window.localStorage.setItem("absensi-demo-tour-seen", "true");
            return () => window.clearTimeout(opener);
        }
    }, []);

    useEffect(() => {
        const matchedIndex = steps.findIndex((step) => route().current(step.routeName));

        if (matchedIndex >= 0) {
            setActiveStep(matchedIndex);
        }
    }, [steps]);

    const goToStep = (index) => {
        setActiveStep(index);
        setIsOpen(true);
    };

    return (
        <>
            <button
                type="button"
                onClick={() => setIsOpen(true)}
                className="fixed bottom-5 right-5 z-40 inline-flex items-center gap-2 rounded-full bg-slate-950 px-4 py-3 text-sm font-bold text-white shadow-[0_18px_45px_rgba(15,23,42,0.32)] transition hover:-translate-y-0.5 hover:bg-slate-800"
            >
                <Icon path="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" className="h-4 w-4" />
                Demo Tour
            </button>

            <div
                className={`fixed inset-0 z-[70] bg-slate-950/45 backdrop-blur-[2px] transition ${
                    isOpen ? "opacity-100" : "pointer-events-none opacity-0"
                }`}
                onClick={() => setIsOpen(false)}
            />

            <section
                className={`fixed bottom-4 left-4 right-4 z-[80] overflow-hidden rounded-[30px] border border-white/70 bg-white shadow-[0_30px_90px_rgba(15,23,42,0.28)] transition duration-300 md:left-auto md:right-6 md:w-[430px] ${
                    isOpen ? "translate-y-0 opacity-100" : "pointer-events-none translate-y-6 opacity-0"
                }`}
                aria-label="Panel panduan demo"
            >
                <div className="bg-slate-950 px-5 py-5 text-white">
                    <div className="flex items-start justify-between gap-4">
                        <div>
                            <p className="text-[11px] font-semibold uppercase tracking-[0.2em] text-sky-200">Client Demo Assistant</p>
                            <h2 className="mt-2 font-heading text-xl font-extrabold">Jalur demo terpandu</h2>
                        </div>
                        <button
                            type="button"
                            onClick={() => setIsOpen(false)}
                            className="rounded-full bg-white/10 p-2 text-white transition hover:bg-white/20"
                            aria-label="Tutup panduan demo"
                        >
                            <Icon path="M6 18L18 6M6 6l12 12" className="h-4 w-4" />
                        </button>
                    </div>

                    <div className="mt-5 h-2 overflow-hidden rounded-full bg-white/15">
                        <div className="h-full rounded-full bg-sky-300 transition-all" style={{ width: `${progress}%` }} />
                    </div>
                </div>

                <div className="max-h-[calc(100vh-12rem)] overflow-y-auto p-5">
                    <div className="rounded-[24px] border border-slate-200 bg-slate-50 p-4">
                        <div className="flex items-center justify-between gap-3">
                            <span className="rounded-full bg-slate-950 px-3 py-1 text-xs font-bold text-white">
                                Step {activeStep + 1}/{steps.length}
                            </span>
                            <span className="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                                {userRole === "admin" ? "Admin Flow" : "Employee Flow"}
                            </span>
                        </div>
                        <h3 className="mt-4 font-heading text-lg font-bold text-slate-950">{currentStep.title}</h3>
                        <p className="mt-2 text-sm leading-6 text-slate-600">{currentStep.description}</p>

                        <div className="mt-4 flex flex-wrap gap-2">
                            <Link href={currentStep.href} className="ui-button-primary" onClick={() => setIsOpen(false)}>
                                {currentStep.cta}
                            </Link>
                            <button
                                type="button"
                                onClick={() => setActiveStep((value) => Math.min(value + 1, steps.length - 1))}
                                className="ui-button-secondary"
                                disabled={activeStep === steps.length - 1}
                            >
                                Step Berikutnya
                            </button>
                        </div>
                    </div>

                    <div className="mt-4 space-y-2">
                        {steps.map((step, index) => (
                            <button
                                key={step.title}
                                type="button"
                                onClick={() => goToStep(index)}
                                className={`flex w-full items-start gap-3 rounded-2xl border px-4 py-3 text-left transition ${
                                    index === activeStep
                                        ? "border-[var(--primary-color)] bg-[rgba(70,72,212,0.08)]"
                                        : "border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50"
                                }`}
                            >
                                <span
                                    className={`mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold ${
                                        index === activeStep ? "bg-[var(--primary-color)] text-white" : "bg-slate-100 text-slate-500"
                                    }`}
                                >
                                    {index + 1}
                                </span>
                                <span>
                                    <span className="block text-sm font-bold text-slate-900">{step.title}</span>
                                    <span className="mt-1 block text-xs leading-5 text-slate-500">{step.cta}</span>
                                </span>
                            </button>
                        ))}
                    </div>

                    <div className="mt-4 rounded-2xl bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-900">
                        Tips presenter: mulai dari dashboard, jelaskan masalah bisnisnya, lalu klik CTA tiap step agar client merasa alurnya natural.
                    </div>
                </div>
            </section>
        </>
    );
}

export default function AuthenticatedLayout({ children }) {
    const { auth, notifications } = usePage().props;
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const [isNotificationOpen, setIsNotificationOpen] = useState(false);
    const [isUserMenuOpen, setIsUserMenuOpen] = useState(false);
    const [time, setTime] = useState(new Date());
    const notificationRef = useRef(null);
    const userMenuRef = useRef(null);

    useEffect(() => {
        const timer = window.setInterval(() => setTime(new Date()), 1000);
        return () => window.clearInterval(timer);
    }, []);

    useEffect(() => {
        const handleClickOutside = (event) => {
            if (notificationRef.current && !notificationRef.current.contains(event.target)) {
                setIsNotificationOpen(false);
            }

            if (userMenuRef.current && !userMenuRef.current.contains(event.target)) {
                setIsUserMenuOpen(false);
            }
        };

        document.addEventListener("mousedown", handleClickOutside);

        return () => document.removeEventListener("mousedown", handleClickOutside);
    }, []);

    const toggleSidebar = () => setIsSidebarOpen((value) => !value);
    const topRoute = auth.user.role === "admin" ? route("admin.dashboard") : route("dashboard");
    const unreadCount = notifications?.unread_count ?? 0;
    const notificationItems = notifications?.items ?? [];

    const primaryLinks = [
        {
            label: "Dashboard",
            href: route("dashboard"),
            active: route().current("dashboard"),
            icon: "M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6",
        },
        {
            label: "Presensi Wajah",
            href: route("attendance.index"),
            active: route().current("attendance.index"),
            icon: "M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9zm12 4a3 3 0 11-6 0 3 3 0 016 0z",
        },
    ];

    const selfServiceLinks = [
        {
            label: "Pengajuan Cuti",
            href: route("employee.leave-requests.index"),
            active: route().current("employee.leave-requests.*"),
            icon: "M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z",
        },
        {
            label: "Reimbursement",
            href: route("reimbursements.index"),
            active: route().current("reimbursements.*"),
            icon: "M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z",
        },
        {
            label: "Slip Gaji",
            href: route("payrolls.user_index"),
            active: route().current("payrolls.*"),
            icon: "M9 8h6m-6 4h6m-6 4h6M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z",
        },
        {
            label: "Direktori",
            href: route("company-directory"),
            active: route().current("company-directory"),
            icon: "M17 20h5v-1a4 4 0 00-5.9-3.5M9 20H4v-1a4 4 0 015.9-3.5M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 5a2 2 0 11-4 0 2 2 0 014 0zM7 12a2 2 0 11-4 0 2 2 0 014 0z",
        },
        {
            label: "Profil Saya",
            href: route("profile.index"),
            active: route().current("profile.*"),
            icon: "M5.121 17.804A7 7 0 1118.88 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z",
        },
    ];

    const adminCoreLinks =
        auth.user.role === "admin"
            ? [
                  {
                      label: "Admin Dashboard",
                      href: route("admin.dashboard"),
                      active: route().current("admin.dashboard"),
                      icon: "M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v8m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v12a2 2 0 01-2 2h-2a2 2 0 01-2-2z",
                  },
                  {
                      label: "Data Karyawan",
                      href: route("admin.employees.index"),
                      active: route().current("admin.employees.*"),
                      icon: "M17 20h5v-1a4 4 0 00-5.9-3.5M9 20H4v-1a4 4 0 015.9-3.5M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 5a2 2 0 11-4 0 2 2 0 014 0zM7 12a2 2 0 11-4 0 2 2 0 014 0z",
                  },
                  {
                      label: "Struktur Organisasi",
                      href: route("admin.departments.index"),
                      active:
                          route().current("admin.departments.*") ||
                          route().current("admin.positions.*") ||
                          route().current("admin.org-chart"),
                      icon: "M7 7h10M7 12h4m-4 5h10M5 5h2v2H5V5zm0 5h2v2H5v-2zm0 5h2v2H5v-2zm12-10h2v2h-2V5zm0 10h2v2h-2v-2",
                  },
                  {
                      label: "Cabang & Lokasi",
                      href: route("admin.branches.index"),
                      active:
                          route().current("admin.branches.*") ||
                          route().current("admin.locations.*") ||
                          route().current("admin.work-shifts.*"),
                      icon: "M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M9 13h.01M9 17h.01M15 9h.01M15 13h.01M15 17h.01",
                  },
                  {
                      label: "Kebijakan HR",
                      href: route("admin.employment-types.index"),
                      active:
                          route().current("admin.employment-types.*") ||
                          route().current("admin.leave-types.*") ||
                          route().current("admin.leave-balances.*"),
                      icon: "M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l6 6v10a2 2 0 01-2 2z",
                  },
                  {
                      label: "Rekap Kehadiran",
                      href: route("admin.attendances.index"),
                      active:
                          route().current("admin.attendances.*") ||
                          route().current("admin.attendance-corrections.*"),
                      icon: "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 7h.01M9 16h.01M12 12h3m-3 4h3",
                  },
                  {
                      label: "Approval Center",
                      href: route("admin.approvals"),
                      active:
                          route().current("admin.approvals") ||
                          route().current("admin.reimbursements.*"),
                      icon: "M9 12l2 2 4-4m6 2A9 9 0 113 12a9 9 0 0118 0z",
                  },
                  {
                      label: "KPI Karyawan",
                      href: route("admin.kpi.index"),
                      active: route().current("admin.kpi.*"),
                      icon: "M11 3h2v18h-2V3zm-6 8h2v10H5V11zm12-5h2v15h-2V6z",
                  },
                  {
                      label: "Manajemen Payroll",
                      href: route("admin.payrolls.index"),
                      active:
                          route().current("admin.payrolls.*") ||
                          route().current("admin.payroll-components.*"),
                      icon: "M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z",
                  },
                  {
                      label: "Laporan Analitik",
                      href: route("admin.reports.performance-heatmap"),
                      active:
                          route().current("admin.reports.*") ||
                          route().current("admin.activity-logs.*"),
                      icon: "M7 12h3v7H7v-7zm7-8h3v15h-3V4zM14 10h3v9h-3v-9zM4 16h3v3H4v-3z",
                  },
                  {
                      label: "Pengumuman",
                      href: route("admin.announcements.index"),
                      active: route().current("admin.announcements.*"),
                      icon: "M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9",
                  },
              ]
            : [];

    const adminQuickLinks =
        auth.user.role === "admin"
            ? [
                  {
                      label: "Departemen",
                      href: route("admin.departments.index"),
                      active: route().current("admin.departments.*"),
                      icon: "M4 6h16M4 12h10M4 18h16",
                  },
                  {
                      label: "Posisi",
                      href: route("admin.positions.index"),
                      active: route().current("admin.positions.*"),
                      icon: "M12 6v12m6-6H6",
                  },
                  {
                      label: "Org Chart",
                      href: route("admin.org-chart"),
                      active: route().current("admin.org-chart"),
                      icon: "M6 5h12v4H6V5zm-3 10h7v4H3v-4zm11 0h7v4h-7v-4zM12 9v3m-5 0h10",
                  },
                  {
                      label: "Cabang",
                      href: route("admin.branches.index"),
                      active: route().current("admin.branches.*"),
                      icon: "M4 21h16M6 21V8l6-3 6 3v13",
                  },
                  {
                      label: "Lokasi",
                      href: route("admin.locations.index"),
                      active: route().current("admin.locations.*"),
                      icon: "M12 21s7-4.35 7-11a7 7 0 10-14 0c0 6.65 7 11 7 11zm0-8a3 3 0 100-6 3 3 0 000 6z",
                  },
                  {
                      label: "Tipe Karyawan",
                      href: route("admin.employment-types.index"),
                      active: route().current("admin.employment-types.*"),
                      icon: "M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m14-10a4 4 0 10-8 0 4 4 0 008 0zm4 8v-2a4 4 0 00-3-3.87",
                  },
                  {
                      label: "Shift Kerja",
                      href: route("admin.work-shifts.index"),
                      active: route().current("admin.work-shifts.*"),
                      icon: "M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z",
                  },
                  {
                      label: "Tipe Cuti",
                      href: route("admin.leave-types.index"),
                      active: route().current("admin.leave-types.*"),
                      icon: "M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z",
                  },
                  {
                      label: "Hari Libur",
                      href: route("admin.holidays.index"),
                      active: route().current("admin.holidays.*"),
                      icon: "M8 7V3m8 4V3m-9 8h10M6 14h12M6 18h8M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z",
                  },
                  {
                      label: "Saldo Cuti",
                      href: route("admin.leave-balances.index"),
                      active: route().current("admin.leave-balances.*"),
                      icon: "M12 8c-3 0-5 1.343-5 3v5h10v-5c0-1.657-2-3-5-3zm0 0V5m0 11v3",
                  },
                  {
                      label: "Koreksi Absensi",
                      href: route("admin.attendance-corrections.index"),
                      active: route().current("admin.attendance-corrections.*"),
                      icon: "M9 12l2 2 4-4M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z",
                  },
                  {
                      label: "Reimbursement",
                      href: route("admin.reimbursements.index"),
                      active: route().current("admin.reimbursements.*"),
                      icon: "M9 14l2 2 4-4M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z",
                  },
                  {
                      label: "Komponen Payroll",
                      href: route("admin.payroll-components.index"),
                      active: route().current("admin.payroll-components.*"),
                      icon: "M5 12h14M12 5v14",
                  },
                  {
                      label: "Audit Trail",
                      href: route("admin.activity-logs.index"),
                      active: route().current("admin.activity-logs.*"),
                      icon: "M9 17v-6h6v6m-8 4h10a2 2 0 002-2V7l-5-4-5 4v12a2 2 0 002 2zm3-10h4",
                  },
                  {
                      label: "Labor Cost",
                      href: route("admin.reports.labor-cost"),
                      active: route().current("admin.reports.labor-cost"),
                      icon: "M4 19h16M7 16V8m5 8V5m5 11v-6",
                  },
                  {
                      label: "Pengumuman",
                      href: route("admin.announcements.index"),
                      active: route().current("admin.announcements.*"),
                      icon: "M7 8h10M7 12h8m-8 4h6M5 4h14v16H5V4z",
                  },
              ]
            : [];

    const formatNotificationDate = (value) => {
        if (!value) return "-";

        return new Date(value).toLocaleString("id-ID", {
            day: "2-digit",
            month: "short",
            hour: "2-digit",
            minute: "2-digit",
        });
    };

    const notificationTone = (type) => {
        switch (type) {
            case "announcement":
                return "bg-sky-100 text-sky-700";
            case "attendance_correction":
                return "bg-amber-100 text-amber-700";
            case "leave_request":
                return "bg-emerald-100 text-emerald-700";
            case "reimbursement":
                return "bg-orange-100 text-orange-700";
            case "payroll":
                return "bg-violet-100 text-violet-700";
            default:
                return "bg-slate-100 text-slate-700";
        }
    };

    const markNotificationAsRead = (id) => {
        router.post(route("notifications.read", id), {}, { preserveScroll: true, preserveState: true });
    };

    return (
        <div className="min-h-screen bg-[var(--bg-body)] text-[var(--text-main)]">
            <div
                className={`fixed inset-0 z-40 bg-slate-950/45 transition-opacity lg:hidden ${isSidebarOpen ? "opacity-100" : "pointer-events-none opacity-0"}`}
                onClick={toggleSidebar}
            />

            <aside
                className={`fixed inset-y-0 left-0 z-50 flex w-[260px] flex-col border-r border-slate-800 bg-[var(--sidebar-bg)] transition-transform duration-300 lg:translate-x-0 ${
                    isSidebarOpen ? "translate-x-0" : "-translate-x-full"
                }`}
            >
                <Link href={topRoute} className="px-6 py-6">
                    <div className="font-heading text-xl font-extrabold tracking-tight text-white">Absensi AI</div>
                    <p className="mt-1 text-xs text-slate-400">Enterprise Portal</p>
                </Link>

                <nav className="sidebar-scroll mt-2 flex-1 overflow-y-auto">
                    <SidebarGroup title="Menu Utama">
                        {primaryLinks.map((item) => (
                            <NavLink key={item.label} href={item.href} active={item.active}>
                                <Icon path={item.icon} />
                                <span className="font-heading text-sm font-medium">{item.label}</span>
                            </NavLink>
                        ))}
                    </SidebarGroup>

                    <SidebarGroup title="Self Service">
                        {selfServiceLinks.map((item) => (
                            <NavLink key={item.label} href={item.href} active={item.active}>
                                <Icon path={item.icon} />
                                <span className="font-heading text-sm font-medium">{item.label}</span>
                            </NavLink>
                        ))}
                    </SidebarGroup>

                    {adminCoreLinks.length > 0 && (
                        <SidebarGroup title="Manajemen HR">
                            {adminCoreLinks.map((item) => (
                                <NavLink key={item.label} href={item.href} active={item.active}>
                                    <Icon path={item.icon} />
                                    <span className="font-heading text-sm font-medium">{item.label}</span>
                                </NavLink>
                            ))}
                        </SidebarGroup>
                    )}

                    {adminQuickLinks.length > 0 && (
                        <SidebarGroup title="Master Data">
                            {adminQuickLinks.map((item) => (
                                <NavLink key={item.label} href={item.href} active={item.active}>
                                    <Icon path={item.icon} />
                                    <span className="font-heading text-sm font-medium">{item.label}</span>
                                </NavLink>
                            ))}
                        </SidebarGroup>
                    )}
                </nav>

                <div className="border-t border-slate-800 p-4">
                    <Link href={route("guide")} className="mt-1 flex items-center gap-3 rounded-md px-2 py-2 text-slate-400 transition hover:bg-slate-900/50 hover:text-slate-100">
                        <Icon path="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.518 11.591c.75 1.334-.213 2.99-1.742 2.99H3.48c-1.53 0-2.492-1.656-1.743-2.99L8.257 3.1zM12 9v3m0 4h.01" />
                        <span className="text-sm">Panduan</span>
                    </Link>
                    <Link
                        href={route("logout")}
                        method="post"
                        as="button"
                        className="mt-3 flex w-full items-center gap-3 rounded-md px-2 py-2 text-red-300 transition hover:bg-red-500/10 hover:text-red-200"
                    >
                        <Icon path="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        <span className="text-sm font-medium">Keluar</span>
                    </Link>
                </div>
            </aside>

            <div className="lg:ml-[260px]">
                <header className="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 shadow-sm lg:px-6">
                    <div className="flex items-center gap-4">
                        <button
                            onClick={toggleSidebar}
                            className="rounded-md p-2 text-slate-600 hover:bg-slate-100 lg:hidden"
                            type="button"
                        >
                            <Icon path="M4 6h16M4 12h16M4 18h16" className="h-5 w-5" />
                        </button>

                    </div>

                    <div className="flex items-center gap-2 md:gap-4">
                        <div className="hidden rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-500 lg:flex">
                            Live Time
                            <span className="ml-2 font-data text-[var(--text-main)]">
                                {time.toLocaleTimeString("id-ID", {
                                    hour: "2-digit",
                                    minute: "2-digit",
                                    second: "2-digit",
                                })}
                            </span>
                        </div>

                        <div className="relative" ref={notificationRef}>
                            <button
                                type="button"
                                onClick={() => setIsNotificationOpen((value) => !value)}
                                className="relative rounded-full p-2 text-slate-600 transition hover:bg-slate-50"
                            >
                                <Icon path="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                {unreadCount > 0 && (
                                    <span className="absolute -right-0.5 -top-0.5 flex min-h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">
                                        {unreadCount > 9 ? "9+" : unreadCount}
                                    </span>
                                )}
                            </button>

                            <div
                                className={`fixed left-3 right-3 top-16 z-40 max-h-[calc(100vh-5rem)] overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] transition md:absolute md:left-auto md:right-0 md:top-12 md:w-[min(92vw,24rem)] ${
                                    isNotificationOpen ? "translate-y-0 opacity-100" : "pointer-events-none translate-y-2 opacity-0"
                                }`}
                            >
                                <div className="border-b border-slate-100 px-4 py-4">
                                    <div className="flex items-start justify-between gap-3">
                                        <div>
                                            <p className="text-xs font-semibold uppercase tracking-[0.2em] text-[var(--text-dark-muted)]">
                                                Notification Center
                                            </p>
                                            <h3 className="mt-1 font-heading text-lg font-semibold text-[var(--text-main)]">
                                                Update Terbaru
                                            </h3>
                                        </div>

                                        {unreadCount > 0 && (
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    router.post(route("notifications.read-all"), {}, { preserveScroll: true, preserveState: true })
                                                }
                                                className="text-xs font-semibold uppercase tracking-[0.14em] text-[var(--primary-color)]"
                                            >
                                                Baca semua
                                            </button>
                                        )}
                                    </div>
                                </div>

                                <div className="max-h-[calc(100vh-14rem)] overflow-y-auto px-3 py-3 md:max-h-[28rem]">
                                    {notificationItems.length === 0 ? (
                                        <div className="rounded-[22px] border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                                            Belum ada notifikasi baru.
                                        </div>
                                    ) : (
                                        <div className="space-y-2">
                                            {notificationItems.map((item) => (
                                                <button
                                                    key={item.id}
                                                    type="button"
                                                    onClick={() => {
                                                        if (!item.read_at) {
                                                            markNotificationAsRead(item.id);
                                                        }
                                                        setIsNotificationOpen(false);
                                                    }}
                                                    className={`w-full rounded-[22px] border px-4 py-4 text-left transition ${
                                                        item.read_at
                                                            ? "border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50"
                                                            : "border-[rgba(70,72,212,0.22)] bg-[rgba(70,72,212,0.05)] hover:border-[rgba(70,72,212,0.4)]"
                                                    }`}
                                                >
                                                    <div className="flex items-start justify-between gap-3">
                                                        <div className="min-w-0">
                                                            <div className="flex flex-wrap items-center gap-2">
                                                                <span
                                                                    className={`rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] ${notificationTone(item.type)}`}
                                                                >
                                                                    {String(item.type).replaceAll("_", " ")}
                                                                </span>
                                                                {!item.read_at && (
                                                                    <span className="rounded-full bg-rose-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-rose-700">
                                                                        Baru
                                                                    </span>
                                                                )}
                                                            </div>
                                                            <p className="mt-3 text-sm font-semibold text-[var(--text-main)]">{item.title}</p>
                                                            <p className="mt-1 line-clamp-2 text-sm leading-6 text-slate-600">{item.message}</p>
                                                        </div>
                                                        <span className="shrink-0 text-[11px] font-medium uppercase tracking-[0.16em] text-slate-400">
                                                            {formatNotificationDate(item.created_at)}
                                                        </span>
                                                    </div>
                                                </button>
                                            ))}
                                        </div>
                                    )}
                                </div>

                                <div className="border-t border-slate-100 p-3">
                                    <Link
                                        href={route("notifications.index")}
                                        onClick={() => setIsNotificationOpen(false)}
                                        className="flex w-full items-center justify-center rounded-full bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90"
                                    >
                                        Buka inbox notifikasi
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div className="relative" ref={userMenuRef}>
                            <button
                                type="button"
                                onClick={() => setIsUserMenuOpen((value) => !value)}
                                className="flex items-center gap-2 rounded-full p-2 text-slate-700 transition hover:bg-slate-50"
                                aria-label="Buka menu profil"
                            >
                                <UserAvatar name={auth.user.name} small />
                                <span className="hidden text-sm font-medium text-slate-700 md:block">{auth.user.name}</span>
                                <Icon path="M19 9l-7 7-7-7" className="hidden h-4 w-4 text-slate-500 md:block" />
                            </button>

                            <div
                                className={`absolute right-0 top-12 z-50 w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] transition ${
                                    isUserMenuOpen ? "translate-y-0 opacity-100" : "pointer-events-none -translate-y-2 opacity-0"
                                }`}
                            >
                                <div className="border-b border-slate-100 px-4 py-3">
                                    <div className="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Account</div>
                                    <div className="mt-1 truncate font-heading text-sm font-semibold text-slate-900">{auth.user.name}</div>
                                    <div className="mt-1 truncate text-xs text-slate-500">{auth.user.email}</div>
                                </div>

                                <div className="p-2">
                                    <Link
                                        href={route("profile.index")}
                                        onClick={() => setIsUserMenuOpen(false)}
                                        className="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                    >
                                        <Icon path="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7zm0-2a4 4 0 100-8 4 4 0 000 8z" className="h-4 w-4" />
                                        Profil Saya
                                    </Link>
                                    <Link
                                        href={route("guide")}
                                        onClick={() => setIsUserMenuOpen(false)}
                                        className="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                    >
                                        <Icon path="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.518 11.591c.75 1.334-.213 2.99-1.742 2.99H3.48c-1.53 0-2.492-1.656-1.743-2.99L8.257 3.1zM12 9v3m0 4h.01" className="h-4 w-4" />
                                        Panduan
                                    </Link>
                                </div>

                                <div className="border-t border-slate-100 p-2">
                                    <Link
                                        href={route("logout")}
                                        method="post"
                                        as="button"
                                        onClick={() => setIsUserMenuOpen(false)}
                                        className="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                                    >
                                        <Icon path="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" className="h-4 w-4" />
                                        Keluar
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <main className="px-4 py-6 lg:px-6 lg:py-8">
                    <div className="mx-auto max-w-7xl">{children}</div>
                </main>
            </div>

            <DemoTourPanel userRole={auth.user.role} />
        </div>
    );
}
