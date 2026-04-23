import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";

function EmptyState({ icon, message }) {
    return (
        <div className="py-12 text-center text-slate-500">
            <div className="mb-4 text-4xl opacity-30">{icon}</div>
            <p>{message}</p>
        </div>
    );
}

function ApprovalCard({ title, badgeClassName, badgeText, children }) {
    return (
        <div className="flex flex-col rounded-[28px] border border-slate-200 bg-white p-8 shadow-sm">
            <div className="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 className="text-xl font-extrabold text-[var(--text-main)]">{title}</h3>
                <span className={`rounded-full px-3 py-1 text-xs font-extrabold ${badgeClassName}`}>
                    {badgeText}
                </span>
            </div>
            <div className="space-y-4">{children}</div>
        </div>
    );
}

export default function Approvals({ leaveRequests, reimbursements, attendanceCorrections }) {
    const totalPending =
        leaveRequests.length + reimbursements.length + attendanceCorrections.length;

    return (
        <AuthenticatedLayout>
            <Head title="Pusat Persetujuan" />

            <div className="space-y-6">
                <div className="rounded-[28px] border border-slate-200 bg-white p-8 shadow-sm">
                    <p className="text-sm font-semibold uppercase tracking-[0.2em] text-[var(--text-dark-muted)]">
                        Approval Hub
                    </p>
                    <div className="mt-3 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <h1 className="font-heading text-4xl font-black text-[var(--text-main)]">
                                Pusat Persetujuan
                            </h1>
                            <p className="mt-2 max-w-2xl text-sm text-slate-500">
                                Kelola cuti, reimbursement, dan koreksi absensi dalam satu dashboard
                                terpadu untuk tim HR dan approver.
                            </p>
                        </div>
                        <div className="rounded-3xl bg-slate-950 px-5 py-4 text-white">
                            <div className="text-xs uppercase tracking-[0.18em] text-slate-400">
                                Total Pending
                            </div>
                            <div className="mt-1 font-data text-3xl font-bold">{totalPending}</div>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-6 xl:grid-cols-3">
                    <ApprovalCard
                        title="Permohonan Cuti"
                        badgeClassName="bg-blue-50 text-blue-700"
                        badgeText={`${leaveRequests.length} Menunggu`}
                    >
                        {leaveRequests.length === 0 ? (
                            <EmptyState icon="OK" message="Semua pengajuan cuti telah diproses." />
                        ) : (
                            leaveRequests.map((req) => (
                                <div
                                    key={req.id}
                                    className="rounded-[24px] border border-slate-200 bg-slate-50 p-6 transition hover:-translate-y-1 hover:border-blue-200 hover:bg-blue-50/60"
                                >
                                    <div className="mb-2 flex items-start justify-between">
                                        <div className="font-extrabold text-[var(--text-main)]">
                                            {req.user?.name}
                                        </div>
                                        <div className="text-xs font-semibold uppercase text-slate-400">
                                            {new Date(req.created_at).toLocaleDateString("id-ID", {
                                                day: "numeric",
                                                month: "short",
                                            })}
                                        </div>
                                    </div>
                                    <span className="mb-3 inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-extrabold uppercase text-blue-700">
                                        {req.leave_type?.name || "Izin"}
                                    </span>
                                    <div className="mb-4 text-sm leading-relaxed text-slate-500">
                                        <div className="mb-1 font-bold text-[var(--text-main)]">
                                            {new Date(req.start_date).toLocaleDateString("id-ID")} -{" "}
                                            {new Date(req.end_date).toLocaleDateString("id-ID")}
                                        </div>
                                        {req.reason
                                            ? `"${req.reason.slice(0, 80)}${req.reason.length > 80 ? "..." : ""}"`
                                            : "Tanpa catatan."}
                                    </div>
                                    <Link
                                        href={route("admin.leave-requests.index")}
                                        className="flex w-full items-center justify-center rounded-2xl bg-blue-600 py-3 font-bold text-white transition hover:bg-blue-700"
                                    >
                                        Proses Sekarang
                                    </Link>
                                </div>
                            ))
                        )}
                    </ApprovalCard>

                    <ApprovalCard
                        title="Klaim Reimbursement"
                        badgeClassName="bg-emerald-50 text-emerald-700"
                        badgeText={`${reimbursements.length} Menunggu`}
                    >
                        {reimbursements.length === 0 ? (
                            <EmptyState icon="OK" message="Semua klaim biaya telah diverifikasi." />
                        ) : (
                            reimbursements.map((req) => (
                                <div
                                    key={req.id}
                                    className="rounded-[24px] border border-slate-200 bg-slate-50 p-6 transition hover:-translate-y-1 hover:border-emerald-200 hover:bg-emerald-50/60"
                                >
                                    <div className="mb-2 flex items-start justify-between">
                                        <div className="font-extrabold text-[var(--text-main)]">
                                            {req.user?.name}
                                        </div>
                                        <div className="text-xs font-semibold uppercase text-slate-400">
                                            {new Date(req.created_at).toLocaleDateString("id-ID", {
                                                day: "numeric",
                                                month: "short",
                                            })}
                                        </div>
                                    </div>
                                    <span className="mb-3 inline-block rounded-full bg-emerald-100 px-3 py-1 text-xs font-extrabold uppercase text-emerald-700">
                                        Rp {new Intl.NumberFormat("id-ID").format(req.amount)}
                                    </span>
                                    <div className="mb-4 text-sm leading-relaxed text-slate-500">
                                        <div className="mb-1 font-bold text-[var(--text-main)]">
                                            {req.title}
                                        </div>
                                        {req.description
                                            ? `"${req.description.slice(0, 80)}${req.description.length > 80 ? "..." : ""}"`
                                            : "Tanpa keterangan tambahan."}
                                    </div>
                                    <Link
                                        href={`${route("admin.reimbursements.index")}?status=pending`}
                                        className="flex w-full items-center justify-center rounded-2xl bg-emerald-600 py-3 font-bold text-white transition hover:bg-emerald-700"
                                    >
                                        Verifikasi Klaim
                                    </Link>
                                </div>
                            ))
                        )}
                    </ApprovalCard>

                    <ApprovalCard
                        title="Koreksi Absensi"
                        badgeClassName="bg-amber-50 text-amber-700"
                        badgeText={`${attendanceCorrections.length} Menunggu`}
                    >
                        {attendanceCorrections.length === 0 ? (
                            <EmptyState icon="OK" message="Semua koreksi absensi sudah ditinjau." />
                        ) : (
                            attendanceCorrections.map((item) => (
                                <div
                                    key={item.id}
                                    className="rounded-[24px] border border-slate-200 bg-slate-50 p-6 transition hover:-translate-y-1 hover:border-amber-200 hover:bg-amber-50/60"
                                >
                                    <div className="mb-2 flex items-start justify-between">
                                        <div className="font-extrabold text-[var(--text-main)]">
                                            {item.user?.name}
                                        </div>
                                        <div className="text-xs font-semibold uppercase text-slate-400">
                                            {new Date(item.created_at).toLocaleDateString("id-ID", {
                                                day: "numeric",
                                                month: "short",
                                            })}
                                        </div>
                                    </div>
                                    <span className="mb-3 inline-block rounded-full bg-amber-100 px-3 py-1 text-xs font-extrabold uppercase text-amber-700">
                                        {item.correction_type?.replaceAll("_", " ") || "Koreksi"}
                                    </span>
                                    <div className="mb-4 text-sm leading-relaxed text-slate-500">
                                        <div className="mb-1 font-bold text-[var(--text-main)]">
                                            {item.attendance?.attendance_date
                                                ? new Date(item.attendance.attendance_date).toLocaleDateString("id-ID")
                                                : "Tanggal absensi tidak tersedia"}
                                        </div>
                                        {item.reason
                                            ? `"${item.reason.slice(0, 80)}${item.reason.length > 80 ? "..." : ""}"`
                                            : "Tanpa alasan tambahan."}
                                    </div>
                                    <Link
                                        href={route("admin.attendance-corrections.index")}
                                        className="flex w-full items-center justify-center rounded-2xl bg-amber-500 py-3 font-bold text-white transition hover:bg-amber-600"
                                    >
                                        Tinjau Koreksi
                                    </Link>
                                </div>
                            ))
                        )}
                    </ApprovalCard>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
