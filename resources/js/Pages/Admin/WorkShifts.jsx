import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";

const dayLabels = {
    mon: "Sen",
    tue: "Sel",
    wed: "Rab",
    thu: "Kam",
    fri: "Jum",
    sat: "Sab",
    sun: "Min",
};

export default function WorkShifts({ shifts, flash }) {
    return (
        <AuthenticatedLayout>
            <Head title="Master Shift Kerja" />

            <section className="ui-card overflow-hidden">
                <div className="flex flex-col gap-5 p-6 lg:flex-row lg:items-end lg:justify-between lg:p-8">
                    <div>
                        <div className="ui-section-title">Work Shift Master</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">Shift kerja dan hari operasional</h1>
                        <p className="mt-3 max-w-2xl text-sm leading-6 text-[var(--text-muted)]">Master shift sekarang mendukung branch, jam istirahat, work days, dan default assignment.</p>
                    </div>
                    <Link href={route("admin.work-shifts.create")} className="ui-button-primary">Tambah Shift</Link>
                </div>
            </section>

            {flash?.success && <div className="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{flash.success}</div>}
            {flash?.error && <div className="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{flash.error}</div>}

            <section className="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                {shifts.map((shift) => (
                    <div key={shift.id} className="ui-card p-6">
                        <div className="flex items-start justify-between gap-3 border-b border-[var(--border-line)] pb-4">
                            <div>
                                <div className="font-heading text-xl font-semibold">{shift.name}</div>
                                <div className="mt-1 text-sm text-[var(--text-muted)]">{shift.branch?.name || "Semua cabang"}</div>
                            </div>
                            {shift.is_default && <span className="ui-badge bg-indigo-50 text-[var(--primary-color)]">Default</span>}
                        </div>
                        <div className="mt-4 space-y-2 text-sm text-[var(--text-muted)]">
                            <div>Masuk: <span className="font-medium text-[var(--text-main)]">{shift.clock_in_time}</span></div>
                            <div>Pulang: <span className="font-medium text-[var(--text-main)]">{shift.clock_out_time}</span></div>
                            <div>Istirahat: <span className="font-medium text-[var(--text-main)]">{shift.break_start_time || "-"} - {shift.break_end_time || "-"}</span></div>
                            <div>Toleransi: <span className="font-medium text-[var(--text-main)]">{shift.late_tolerance_minutes} menit</span></div>
                        </div>
                        <div className="mt-4 flex flex-wrap gap-2">
                            {(shift.work_days || []).length ? shift.work_days.map((day) => (
                                <span key={day} className="ui-badge bg-slate-100 text-slate-600">{dayLabels[day] || day}</span>
                            )) : <span className="text-sm text-[var(--text-muted)]">Hari kerja belum diatur</span>}
                        </div>
                        <div className="mt-6 flex gap-2">
                            <Link href={route("admin.work-shifts.edit", shift.id)} className="ui-button-secondary flex-1 text-center">Edit</Link>
                            <Link href={route("admin.work-shifts.edit", shift.id)} className="rounded-md border border-[var(--border-strong)] px-4 py-2.5 text-sm font-semibold text-[var(--text-main)] transition hover:bg-[var(--bg-subtle)]">Buka</Link>
                        </div>
                    </div>
                ))}
            </section>
        </AuthenticatedLayout>
    );
}
