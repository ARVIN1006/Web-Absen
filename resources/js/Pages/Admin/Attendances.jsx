import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, router, useForm } from "@inertiajs/react";

function StatCard({ label, value }) {
    return (
        <div className="ui-card p-5">
            <div className="ui-section-title">{label}</div>
            <div className="mt-3 font-heading text-3xl font-bold text-black">{value}</div>
        </div>
    );
}

function Pagination({ links }) {
    if (!links || links.length <= 3) return null;

    return (
        <nav className="flex flex-wrap items-center justify-end gap-2 border-t border-[var(--border-line)] px-6 py-4">
            {links.map((link, index) => (
                <Link
                    key={`${link.label}-${index}`}
                    href={link.url || "#"}
                    preserveScroll
                    className={`rounded-md border px-3 py-2 text-sm font-semibold ${
                        link.active
                            ? "border-[var(--primary-color)] bg-[var(--primary-color)] text-white"
                            : "border-[var(--border-line)] bg-white text-[var(--text-main)] hover:bg-[var(--bg-subtle)]"
                    } ${!link.url ? "pointer-events-none opacity-40" : ""}`}
                    dangerouslySetInnerHTML={{ __html: link.label }}
                />
            ))}
        </nav>
    );
}

export default function Attendances({ attendances, todayStats, flash, pendingCorrections, filters = {} }) {
    const { delete: destroy } = useForm();
    const filterForm = useForm({
        start_date: filters.start_date || "",
        end_date: filters.end_date || "",
        search: filters.search || "",
    });

    const submitFilters = (event) => {
        event.preventDefault();
        router.get(route("admin.attendances.index"), filterForm.data, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const resetFilters = () => {
        filterForm.setData({ start_date: "", end_date: "", search: "" });
        router.get(route("admin.attendances.index"), {}, { preserveScroll: true });
    };

    const exportUrl = (type) => {
        const params = new URLSearchParams();
        Object.entries(filterForm.data).forEach(([key, value]) => {
            if (value) params.append(key, value);
        });
        const base = route(type === "pdf" ? "admin.export.pdf" : "admin.export.csv");
        const query = params.toString();
        return query ? `${base}?${query}` : base;
    };

    return (
        <AuthenticatedLayout>
            <Head title="Rekap Absensi" />

            <section className="ui-card overflow-hidden">
                <div className="flex flex-col gap-5 p-6 lg:flex-row lg:items-end lg:justify-between lg:p-8">
                    <div>
                        <div className="ui-section-title">Attendance Monitor</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">Rekap kehadiran dan validasi presensi</h1>
                    </div>
                    <div className="flex flex-wrap gap-3">
                        <Link href={exportUrl("csv")} className="ui-button-secondary">
                            Export CSV
                        </Link>
                        <Link href={exportUrl("pdf")} className="ui-button-secondary">
                            Export PDF
                        </Link>
                        <Link href={route("admin.attendance-corrections.index")} className="ui-button-primary">
                            Koreksi Absensi
                        </Link>
                    </div>
                </div>
            </section>

            <section className="mt-6 grid gap-5 md:grid-cols-5">
                <StatCard label="Total Staff" value={todayStats.total} />
                <StatCard label="Hadir" value={todayStats.present} />
                <StatCard label="Terlambat" value={todayStats.late} />
                <StatCard label="Absen" value={todayStats.absent} />
                <StatCard label="Koreksi Pending" value={pendingCorrections} />
            </section>

            {flash?.success && <div className="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{flash.success}</div>}

            <section className="ui-card mt-6 p-6">
                <form onSubmit={submitFilters} className="grid gap-4 lg:grid-cols-[1fr_1fr_1.5fr_auto_auto] lg:items-end">
                    <label className="block">
                        <span className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Mulai</span>
                        <input type="date" value={filterForm.data.start_date} onChange={(event) => filterForm.setData("start_date", event.target.value)} className="ui-input" />
                    </label>
                    <label className="block">
                        <span className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Sampai</span>
                        <input type="date" value={filterForm.data.end_date} onChange={(event) => filterForm.setData("end_date", event.target.value)} className="ui-input" />
                    </label>
                    <label className="block">
                        <span className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Nama karyawan</span>
                        <input value={filterForm.data.search} onChange={(event) => filterForm.setData("search", event.target.value)} className="ui-input" placeholder="Cari nama karyawan" />
                    </label>
                    <button type="submit" className="ui-button-primary">Filter</button>
                    <button type="button" onClick={resetFilters} className="ui-button-secondary">Reset</button>
                </form>
            </section>

            <section className="ui-card mt-6 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-[var(--border-line)] text-sm">
                        <thead className="bg-[var(--bg-subtle)] text-left text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--text-soft)]">
                            <tr>
                                <th className="px-6 py-4">Karyawan</th>
                                <th className="px-6 py-4">Organisasi</th>
                                <th className="px-6 py-4">Waktu</th>
                                <th className="px-6 py-4">Status</th>
                                <th className="px-6 py-4">Lokasi</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[var(--border-line)]">
                            {attendances.data.length === 0 ? (
                                <tr>
                                    <td colSpan="6" className="px-6 py-12 text-center text-sm text-[var(--text-muted)]">
                                        Tidak ada data absensi untuk filter ini.
                                    </td>
                                </tr>
                            ) : attendances.data.map((attendance) => (
                                <tr key={attendance.id}>
                                    <td className="px-6 py-4">
                                        <div className="font-semibold text-[var(--text-main)]">{attendance.user?.name}</div>
                                        <div className="mt-1 text-xs text-[var(--text-muted)]">{attendance.type === "in" ? "Check-in" : "Check-out"}</div>
                                    </td>
                                    <td className="px-6 py-4 text-[var(--text-muted)]">
                                        <div>{attendance.user?.department?.name || "-"}</div>
                                        <div className="mt-1">{attendance.user?.position?.name || "-"}</div>
                                    </td>
                                    <td className="px-6 py-4 text-[var(--text-muted)]">
                                        <div>Tanggal: {attendance.attendance_date || attendance.created_at?.slice(0, 10)}</div>
                                        <div className="mt-1">Check-in: {attendance.check_in_at || "-"}</div>
                                        <div className="mt-1">Check-out: {attendance.check_out_at || "-"}</div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex flex-wrap gap-2">
                                            <span className={`ui-badge ${attendance.late_status === "late" ? "bg-amber-50 text-[var(--warning-color)]" : "bg-emerald-50 text-[var(--success-color)]"}`}>
                                                {attendance.late_status === "late" ? `Late ${attendance.late_minutes || 0}m` : "On time"}
                                            </span>
                                            <span className="ui-badge bg-indigo-50 text-[var(--primary-color)]">
                                                Overtime {attendance.overtime_minutes || 0}m
                                            </span>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 text-[var(--text-muted)]">
                                        <div>{attendance.location?.name || "-"}</div>
                                        <div className="mt-1">{attendance.latitude || "-"}, {attendance.longitude || "-"}</div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex justify-end gap-2">
                                            <button type="button" onClick={() => destroy(route("admin.attendances.destroy", attendance.id))} className="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
                <Pagination links={attendances.links} />
            </section>
        </AuthenticatedLayout>
    );
}
