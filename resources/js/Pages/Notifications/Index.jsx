import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, router } from "@inertiajs/react";

function formatDate(value) {
    if (!value) return "-";

    return new Date(value).toLocaleString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function badgeTone(type) {
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
}

export default function NotificationsIndex({ notifications }) {
    const markAsRead = (id) => {
        router.post(route("notifications.read", id), {}, { preserveScroll: true });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Notifikasi" />

            <div className="mobile-page space-y-5">
                <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p className="text-xs font-semibold uppercase tracking-[0.24em] text-[var(--text-dark-muted)]">
                                Communication Center
                            </p>
                            <h1 className="mt-2 font-heading text-2xl font-bold text-[var(--text-main)]">
                                Notifikasi
                            </h1>
                            <p className="mt-2 max-w-2xl text-sm text-slate-500">
                                Semua pengajuan, approval, payroll, dan pengumuman terbaru terkumpul di satu tempat.
                            </p>
                        </div>

                        <button
                            type="button"
                            onClick={() => router.post(route("notifications.read-all"), {}, { preserveScroll: true })}
                            className="inline-flex items-center justify-center rounded-full bg-[var(--primary-color)] px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90"
                        >
                            Tandai semua dibaca
                        </button>
                    </div>
                </section>

                <section className="mobile-card-list">
                    {notifications.data.length === 0 ? (
                        <div className="rounded-[24px] border border-dashed border-slate-300 bg-white p-6 text-center text-sm text-slate-500 shadow-sm">
                            Belum ada notifikasi.
                        </div>
                    ) : (
                        notifications.data.map((item) => (
                            <article
                                key={item.id}
                                className={`rounded-[24px] border p-5 shadow-sm transition ${
                                    item.read_at ? "border-slate-200 bg-white" : "border-[rgba(70,72,212,0.24)] bg-[rgba(70,72,212,0.04)]"
                                }`}
                            >
                                <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div className="space-y-3">
                                        <div className="flex flex-wrap items-center gap-2">
                                            <span className={`rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] ${badgeTone(item.type)}`}>
                                                {item.type.replaceAll("_", " ")}
                                            </span>
                                            {!item.read_at && (
                                                <span className="rounded-full bg-[var(--primary-soft)] px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--primary-color)]">
                                                    Baru
                                                </span>
                                            )}
                                        </div>

                                        <div>
                                            <h2 className="font-heading text-lg font-semibold text-[var(--text-main)]">
                                                {item.title}
                                            </h2>
                                            <p className="mt-2 text-sm leading-6 text-slate-600">{item.message}</p>
                                        </div>

                                        <p className="text-xs font-medium uppercase tracking-[0.18em] text-slate-400">
                                            {formatDate(item.created_at)}
                                        </p>
                                    </div>

                                    <div className="flex gap-2 sm:flex-col sm:items-end">
                                        {!item.read_at && (
                                            <button
                                                type="button"
                                                onClick={() => markAsRead(item.id)}
                                                className="rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold uppercase tracking-[0.12em] text-slate-700 transition hover:border-[var(--primary-color)] hover:text-[var(--primary-color)]"
                                            >
                                                Tandai dibaca
                                            </button>
                                        )}

                                        <Link
                                            href={route("notifications.index")}
                                            className="rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.12em] text-slate-500 transition hover:border-slate-300 hover:text-slate-700"
                                        >
                                            Inbox
                                        </Link>
                                    </div>
                                </div>
                            </article>
                        ))
                    )}
                </section>
            </div>
        </AuthenticatedLayout>
    );
}
