import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";

function StatCard({ label, value, tone = "default" }) {
    const toneClass = {
        success: "text-[var(--success-color)]",
        warning: "text-[var(--warning-color)]",
        primary: "text-[var(--primary-color)]",
        default: "text-[var(--text-main)]",
    };

    return (
        <div className="ui-card p-5">
            <div className="ui-section-title">{label}</div>
            <div className={`mt-3 font-heading text-3xl font-bold ${toneClass[tone]}`}>{value}</div>
        </div>
    );
}

export default function Dashboard({ activeAnnouncements, attendances }) {
    const hasCheckIn = attendances?.some((attendance) => attendance.type === "in");
    const hasCheckOut = attendances?.some((attendance) => attendance.type === "out");

    const statusText = !hasCheckIn ? "Belum absen" : hasCheckOut ? "Absensi selesai" : "Sudah check-in";
    const statusTone = !hasCheckIn ? "warning" : hasCheckOut ? "success" : "primary";

    return (
        <AuthenticatedLayout>
            <Head title="Dashboard" />

            <section className="ui-card overflow-hidden">
                <div className="grid gap-6 p-6 lg:grid-cols-[1.5fr_0.9fr] lg:p-8">
                    <div>
                        <div className="ui-section-title">Dashboard Karyawan</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black lg:text-4xl">Ringkasan presensi harian Anda</h1>
                        <p className="mt-3 max-w-2xl text-sm leading-6 text-[var(--text-muted)]">
                            Pantau status check-in, pengumuman aktif, dan riwayat absensi hari ini dalam satu tampilan yang lebih rapi dan fokus.
                        </p>
                        <div className="mt-6 flex flex-wrap gap-3">
                            <Link href={route("attendance.index")} className="ui-button-primary">
                                {hasCheckIn && !hasCheckOut ? "Lanjutkan Check-out" : "Buka Presensi"}
                            </Link>
                            <Link href={route("profile.index")} className="ui-button-secondary">
                                Lihat Profil
                            </Link>
                        </div>
                    </div>

                    <div className="ui-card-muted p-5">
                        <div className="ui-section-title">Status Hari Ini</div>
                        <div className={`mt-4 font-heading text-2xl font-bold ${statusTone === "success" ? "text-[var(--success-color)]" : statusTone === "warning" ? "text-[var(--warning-color)]" : "text-[var(--primary-color)]"}`}>
                            {statusText}
                        </div>
                        <div className="mt-5 space-y-3 text-sm text-[var(--text-muted)]">
                            <div className="flex items-center justify-between border-b border-[var(--border-line)] pb-3">
                                <span>Check-in</span>
                                <span className="font-semibold text-[var(--text-main)]">{hasCheckIn ? "Tercatat" : "Belum"}</span>
                            </div>
                            <div className="flex items-center justify-between">
                                <span>Check-out</span>
                                <span className="font-semibold text-[var(--text-main)]">{hasCheckOut ? "Tercatat" : "Belum"}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section className="mt-6 grid gap-5 md:grid-cols-3">
                <StatCard label="Status Hari Ini" value={statusText} tone={statusTone} />
                <StatCard label="Pengumuman Aktif" value={activeAnnouncements?.length || 0} tone="primary" />
                <StatCard label="Absensi Hari Ini" value={attendances?.length || 0} tone="default" />
            </section>

            <section className="mt-6 grid gap-6 lg:grid-cols-2">
                <div className="ui-card p-6">
                    <div className="flex items-center justify-between border-b border-[var(--border-line)] pb-4">
                        <h2 className="font-heading text-xl font-semibold">Pengumuman Terbaru</h2>
                        <span className="ui-badge bg-[var(--bg-subtle)] text-[var(--text-muted)]">Internal</span>
                    </div>

                    {activeAnnouncements?.length ? (
                        <div className="mt-5 space-y-4">
                            {activeAnnouncements.map((announcement) => (
                                <article key={announcement.id} className="rounded-lg border border-[var(--border-line)] bg-[var(--bg-subtle)] p-4">
                                    <div className="ui-badge bg-white text-[var(--primary-color)]">{announcement.type}</div>
                                    <h3 className="mt-3 font-heading text-lg font-semibold">{announcement.title}</h3>
                                    <p className="mt-2 text-sm leading-6 text-[var(--text-muted)]">{announcement.content}</p>
                                </article>
                            ))}
                        </div>
                    ) : (
                        <div className="mt-5 rounded-lg border border-dashed border-[var(--border-strong)] bg-[var(--bg-subtle)] p-8 text-center text-sm text-[var(--text-muted)]">
                            Belum ada pengumuman aktif saat ini.
                        </div>
                    )}
                </div>

                <div className="ui-card p-6">
                    <div className="flex items-center justify-between border-b border-[var(--border-line)] pb-4">
                        <h2 className="font-heading text-xl font-semibold">Riwayat Hari Ini</h2>
                        <span className="text-xs font-semibold uppercase tracking-[0.14em] text-[var(--text-soft)]">Live Log</span>
                    </div>

                    {attendances?.length ? (
                        <div className="mt-5 divide-y divide-[var(--border-line)]">
                            {attendances.map((attendance) => (
                                <div key={attendance.id} className="flex items-center justify-between py-4">
                                    <div>
                                        <div className="font-semibold text-[var(--text-main)]">
                                            {attendance.type === "in" ? "Check-in berhasil" : "Check-out berhasil"}
                                        </div>
                                        <div className="mt-1 text-sm text-[var(--text-muted)]">
                                            {new Date(attendance.created_at).toLocaleTimeString("id-ID", {
                                                hour: "2-digit",
                                                minute: "2-digit",
                                            })}
                                        </div>
                                    </div>
                                    <span
                                        className={`ui-badge ${
                                            attendance.type === "in"
                                                ? "bg-emerald-50 text-[var(--success-color)]"
                                                : "bg-amber-50 text-[var(--warning-color)]"
                                        }`}
                                    >
                                        {attendance.type === "in" ? "Masuk" : "Pulang"}
                                    </span>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="mt-5 rounded-lg border border-dashed border-[var(--border-strong)] bg-[var(--bg-subtle)] p-8 text-center text-sm text-[var(--text-muted)]">
                            Belum ada absensi hari ini.{" "}
                            <Link href={route("attendance.index")} className="font-semibold text-[var(--primary-color)] hover:underline">
                                Mulai presensi sekarang
                            </Link>
                            .
                        </div>
                    )}
                </div>
            </section>
        </AuthenticatedLayout>
    );
}
