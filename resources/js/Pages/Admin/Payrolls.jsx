import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";

export default function Payrolls({ payrolls, month, year, flash }) {
    const { data, setData, post, patch, processing } = useForm({
        month,
        year,
    });

    const handleGenerate = (event) => {
        event.preventDefault();
        post(route("admin.payrolls.generate"));
    };

    const handleMarkAsPaid = (id) => {
        if (confirm("Tandai payroll ini sebagai LUNAS?")) {
            patch(route("admin.payrolls.updateStatus", id));
        }
    };

    const monthNames = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember",
    ];

    const formatCurrency = (amount) =>
        new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(amount);

    return (
        <AuthenticatedLayout>
            <Head title="Kelola Payroll" />

            <div className="mobile-page">
                <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div className="mobile-stack sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <div className="ui-section-title">Payroll Center</div>
                            <h1 className="mt-3 font-heading text-2xl font-bold text-[var(--text-main)] sm:text-3xl">
                                Kelola Payroll
                            </h1>
                            <p className="mt-2 text-sm text-[var(--text-muted)]">
                                Periode aktif: {monthNames[month - 1]} {year}
                            </p>
                        </div>

                        <div className="mobile-stack sm:items-end">
                            <Link href={route("admin.payroll-components.index")} className="ui-button-secondary w-full sm:w-auto">
                                Komponen Payroll
                            </Link>
                            <form onSubmit={handleGenerate} className="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_110px_auto]">
                                <select value={data.month} onChange={(event) => setData("month", event.target.value)} className="ui-input">
                                    {monthNames.map((name, index) => (
                                        <option key={index} value={index + 1}>{name}</option>
                                    ))}
                                </select>
                                <input
                                    type="number"
                                    value={data.year}
                                    onChange={(event) => setData("year", event.target.value)}
                                    className="ui-input"
                                    min="2020"
                                />
                                <button type="submit" disabled={processing} className="ui-button-primary w-full sm:w-auto">
                                    {processing ? "..." : "Generate"}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {flash?.success && (
                    <div className="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-medium text-emerald-700">
                        {flash.success}
                    </div>
                )}
                {flash?.info && (
                    <div className="mt-4 rounded-2xl border border-blue-200 bg-blue-50 px-5 py-3 text-sm font-medium text-blue-700">
                        {flash.info}
                    </div>
                )}

                <div className="mt-6 hidden overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm lg:block">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-[var(--border-line)] bg-[var(--bg-subtle)]">
                                    <th className="px-6 py-4 text-left text-xs font-bold uppercase text-[var(--text-soft)]">Karyawan</th>
                                    <th className="px-6 py-4 text-right text-xs font-bold uppercase text-[var(--text-soft)]">Gaji Pokok</th>
                                    <th className="px-6 py-4 text-right text-xs font-bold uppercase text-[var(--text-soft)]">Lembur</th>
                                    <th className="px-6 py-4 text-right text-xs font-bold uppercase text-[var(--text-soft)]">Earnings</th>
                                    <th className="px-6 py-4 text-right text-xs font-bold uppercase text-[var(--text-soft)]">Deductions</th>
                                    <th className="px-6 py-4 text-right text-xs font-bold uppercase text-[var(--text-soft)]">Total</th>
                                    <th className="px-6 py-4 text-center text-xs font-bold uppercase text-[var(--text-soft)]">Status</th>
                                    <th className="px-6 py-4 text-right text-xs font-bold uppercase text-[var(--text-soft)]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {payrolls.length === 0 ? (
                                    <tr>
                                        <td colSpan="8" className="px-6 py-12 text-center text-sm text-[var(--text-muted)]">
                                            Belum ada data payroll untuk periode ini. Klik Generate untuk membuat payroll baru.
                                        </td>
                                    </tr>
                                ) : (
                                    payrolls.map((payroll) => (
                                        <tr key={payroll.id} className="border-b border-[var(--border-line)] hover:bg-[var(--hover-bg)]">
                                            <td className="px-6 py-4">
                                                <div className="font-semibold text-[var(--text-main)]">{payroll.user?.name}</div>
                                                <div className="text-xs text-[var(--text-muted)]">{payroll.user?.position?.name || "-"}</div>
                                            </td>
                                            <td className="px-6 py-4 text-right">{formatCurrency(payroll.basic_salary)}</td>
                                            <td className="px-6 py-4 text-right text-blue-600">+{formatCurrency(payroll.overtime_pay)}</td>
                                            <td className="px-6 py-4 text-right">{formatCurrency(payroll.total_earnings ?? payroll.net_salary)}</td>
                                            <td className="px-6 py-4 text-right text-red-600">-{formatCurrency(payroll.total_deductions ?? payroll.deductions ?? 0)}</td>
                                            <td className="px-6 py-4 text-right font-semibold">{formatCurrency(payroll.net_salary)}</td>
                                            <td className="px-6 py-4 text-center">
                                                <span className={`ui-badge ${
                                                    payroll.status === "paid"
                                                        ? "bg-emerald-50 text-[var(--success-color)]"
                                                        : payroll.status === "draft"
                                                          ? "bg-amber-50 text-[var(--warning-color)]"
                                                          : "bg-slate-100 text-slate-500"
                                                }`}>
                                                    {payroll.status}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                {payroll.status !== "paid" ? (
                                                    <button onClick={() => handleMarkAsPaid(payroll.id)} className="ui-button-primary">
                                                        Tandai Lunas
                                                    </button>
                                                ) : (
                                                    <span className="text-xs text-[var(--text-muted)]">
                                                        {new Date(payroll.paid_at).toLocaleDateString("id-ID")}
                                                    </span>
                                                )}
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div className="mobile-card-list mt-6 lg:hidden">
                    {payrolls.length === 0 ? (
                        <div className="rounded-[24px] border border-dashed border-[var(--border-strong)] bg-white p-8 text-center text-sm text-[var(--text-muted)]">
                            Belum ada data payroll untuk periode ini. Klik Generate untuk membuat payroll baru.
                        </div>
                    ) : (
                        payrolls.map((payroll) => (
                            <div key={payroll.id} className="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                                <div className="flex items-start justify-between gap-3">
                                    <div>
                                        <div className="font-semibold text-[var(--text-main)]">{payroll.user?.name}</div>
                                        <div className="text-xs text-[var(--text-muted)]">{payroll.user?.position?.name || "-"}</div>
                                    </div>
                                    <span className={`ui-badge ${
                                        payroll.status === "paid"
                                            ? "bg-emerald-50 text-[var(--success-color)]"
                                            : payroll.status === "draft"
                                              ? "bg-amber-50 text-[var(--warning-color)]"
                                              : "bg-slate-100 text-slate-500"
                                    }`}>
                                        {payroll.status}
                                    </span>
                                </div>

                                <div className="mt-4 grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <div className="text-[var(--text-muted)]">Gaji Pokok</div>
                                        <div className="font-semibold text-[var(--text-main)]">{formatCurrency(payroll.basic_salary)}</div>
                                    </div>
                                    <div>
                                        <div className="text-[var(--text-muted)]">Lembur</div>
                                        <div className="font-semibold text-blue-600">+{formatCurrency(payroll.overtime_pay)}</div>
                                    </div>
                                    <div>
                                        <div className="text-[var(--text-muted)]">Earnings</div>
                                        <div className="font-semibold text-[var(--text-main)]">{formatCurrency(payroll.total_earnings ?? payroll.net_salary)}</div>
                                    </div>
                                    <div>
                                        <div className="text-[var(--text-muted)]">Deductions</div>
                                        <div className="font-semibold text-red-600">-{formatCurrency(payroll.total_deductions ?? payroll.deductions ?? 0)}</div>
                                    </div>
                                </div>

                                <div className="mt-4 rounded-2xl bg-[var(--bg-subtle)] px-4 py-3">
                                    <div className="text-xs uppercase tracking-[0.16em] text-[var(--text-soft)]">Net Salary</div>
                                    <div className="mt-1 font-heading text-xl font-bold text-[var(--text-main)]">{formatCurrency(payroll.net_salary)}</div>
                                </div>

                                <div className="mt-4">
                                    {payroll.status !== "paid" ? (
                                        <button onClick={() => handleMarkAsPaid(payroll.id)} className="ui-button-primary w-full">
                                            Tandai Lunas
                                        </button>
                                    ) : (
                                        <div className="text-sm text-[var(--text-muted)]">
                                            Lunas pada {new Date(payroll.paid_at).toLocaleDateString("id-ID")}
                                        </div>
                                    )}
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
