import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, router, useForm } from "@inertiajs/react";
import { useMemo, useState } from "react";

const monthNames = [
    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
    "Juli", "Agustus", "September", "Oktober", "November", "Desember",
];

const formatCurrency = (amount) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(Number(amount || 0));

const statusClasses = {
    draft: "bg-amber-50 text-[var(--warning-color)]",
    reviewed: "bg-blue-50 text-blue-600",
    paid: "bg-emerald-50 text-[var(--success-color)]",
};

export default function Payrolls({ payrolls, month, year, flash, components = [] }) {
    const [activePayroll, setActivePayroll] = useState(null);
    const generateForm = useForm({ month, year });
    const editForm = useForm({ status: "draft", items: [] });

    const summary = useMemo(() => {
        return payrolls.reduce(
            (total, payroll) => ({
                earnings: total.earnings + Number(payroll.total_earnings || 0),
                deductions: total.deductions + Number(payroll.total_deductions || 0),
                net: total.net + Number(payroll.net_salary || 0),
            }),
            { earnings: 0, deductions: 0, net: 0 }
        );
    }, [payrolls]);

    const openDetail = (payroll) => {
        setActivePayroll(payroll);
        editForm.setData({
            status: payroll.status || "draft",
            items: (payroll.component_items || []).map((item) => ({
                id: item.id,
                amount: item.amount || 0,
                notes: item.notes || "",
                component: item.payroll_component,
            })),
        });
    };

    const updateStatus = (payroll, status) => {
        router.patch(route("admin.payrolls.updateStatus", payroll.id), { status }, {
            preserveScroll: true,
        });
    };

    const submitEdit = (event) => {
        event.preventDefault();
        editForm.patch(route("admin.payrolls.update", activePayroll.id), {
            preserveScroll: true,
            onSuccess: () => setActivePayroll(null),
        });
    };

    const setItem = (index, key, value) => {
        editForm.setData(
            "items",
            editForm.data.items.map((item, itemIndex) =>
                itemIndex === index ? { ...item, [key]: value } : item
            )
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Kelola Payroll" />

            <section className="ui-card overflow-hidden">
                <div className="flex flex-col gap-5 p-6 lg:flex-row lg:items-end lg:justify-between lg:p-8">
                    <div>
                        <div className="ui-section-title">Payroll Center</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">Kelola payroll</h1>
                        <p className="mt-2 text-sm text-[var(--text-muted)]">
                            Periode aktif: {monthNames[month - 1]} {year}
                        </p>
                    </div>
                    <div className="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <Link href={route("admin.payroll-components.index")} className="ui-button-secondary">
                            Komponen Payroll
                        </Link>
                        <form onSubmit={(event) => { event.preventDefault(); generateForm.post(route("admin.payrolls.generate")); }} className="grid gap-3 sm:grid-cols-[1fr_110px_auto]">
                            <select value={generateForm.data.month} onChange={(event) => generateForm.setData("month", event.target.value)} className="ui-input">
                                {monthNames.map((name, index) => (
                                    <option key={name} value={index + 1}>{name}</option>
                                ))}
                            </select>
                            <input type="number" value={generateForm.data.year} onChange={(event) => generateForm.setData("year", event.target.value)} className="ui-input" min="2020" />
                            <button type="submit" disabled={generateForm.processing} className="ui-button-primary">
                                {generateForm.processing ? "..." : "Generate"}
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            {flash?.success && <div className="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{flash.success}</div>}
            {flash?.info && <div className="mt-6 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-medium text-blue-700">{flash.info}</div>}

            <section className="mt-6 grid gap-5 md:grid-cols-4">
                <div className="ui-card p-5">
                    <div className="ui-section-title">Payroll</div>
                    <div className="mt-3 font-heading text-3xl font-bold text-black">{payrolls.length}</div>
                </div>
                <div className="ui-card p-5">
                    <div className="ui-section-title">Earnings</div>
                    <div className="mt-3 font-heading text-2xl font-bold text-black">{formatCurrency(summary.earnings)}</div>
                </div>
                <div className="ui-card p-5">
                    <div className="ui-section-title">Deductions</div>
                    <div className="mt-3 font-heading text-2xl font-bold text-red-600">{formatCurrency(summary.deductions)}</div>
                </div>
                <div className="ui-card p-5">
                    <div className="ui-section-title">Net Salary</div>
                    <div className="mt-3 font-heading text-2xl font-bold text-emerald-600">{formatCurrency(summary.net)}</div>
                </div>
            </section>

            <section className="ui-card mt-6 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-[var(--border-line)] text-sm">
                        <thead className="bg-[var(--bg-subtle)] text-left text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--text-soft)]">
                            <tr>
                                <th className="px-6 py-4">Karyawan</th>
                                <th className="px-6 py-4 text-right">Earnings</th>
                                <th className="px-6 py-4 text-right">Deductions</th>
                                <th className="px-6 py-4 text-right">Net</th>
                                <th className="px-6 py-4 text-center">Status</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[var(--border-line)]">
                            {payrolls.length === 0 ? (
                                <tr>
                                    <td colSpan="6" className="px-6 py-12 text-center text-sm text-[var(--text-muted)]">
                                        Belum ada data payroll untuk periode ini.
                                    </td>
                                </tr>
                            ) : payrolls.map((payroll) => (
                                <tr key={payroll.id}>
                                    <td className="px-6 py-4">
                                        <div className="font-semibold text-[var(--text-main)]">{payroll.user?.name}</div>
                                        <div className="mt-1 text-xs text-[var(--text-muted)]">{payroll.user?.position?.name || "-"}</div>
                                    </td>
                                    <td className="px-6 py-4 text-right">{formatCurrency(payroll.total_earnings)}</td>
                                    <td className="px-6 py-4 text-right text-red-600">-{formatCurrency(payroll.total_deductions)}</td>
                                    <td className="px-6 py-4 text-right font-semibold">{formatCurrency(payroll.net_salary)}</td>
                                    <td className="px-6 py-4 text-center">
                                        <span className={`ui-badge ${statusClasses[payroll.status] || "bg-slate-100 text-slate-500"}`}>{payroll.status}</span>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex flex-wrap justify-end gap-2">
                                            <button type="button" onClick={() => openDetail(payroll)} className="ui-button-secondary px-3 py-2">Detail/Edit</button>
                                            {payroll.status === "draft" && (
                                                <button type="button" onClick={() => updateStatus(payroll, "reviewed")} className="ui-button-primary px-3 py-2">Review</button>
                                            )}
                                            {payroll.status !== "paid" && (
                                                <button type="button" onClick={() => updateStatus(payroll, "paid")} className="ui-button-primary px-3 py-2">Lunas</button>
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </section>

            {components.length === 0 && (
                <div className="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700">
                    Belum ada komponen payroll aktif selain komponen otomatis.
                </div>
            )}

            {activePayroll && (
                <div className="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                    <div className="max-h-[90vh] w-full max-w-4xl overflow-hidden rounded-xl bg-white shadow-[var(--shadow-pop)]">
                        <div className="flex items-center justify-between border-b border-[var(--border-line)] px-6 py-4">
                            <div>
                                <h2 className="font-heading text-xl font-semibold">{activePayroll.user?.name}</h2>
                                <p className="mt-1 text-sm text-[var(--text-muted)]">{activePayroll.payroll_number}</p>
                            </div>
                            <button type="button" onClick={() => setActivePayroll(null)} className="text-sm text-[var(--text-muted)]">Tutup</button>
                        </div>
                        <form onSubmit={submitEdit} className="max-h-[calc(90vh-85px)] overflow-y-auto p-6">
                            <div className="mb-5 grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
                                <label className="block">
                                    <span className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Status</span>
                                    <select value={editForm.data.status} onChange={(event) => editForm.setData("status", event.target.value)} className="ui-input">
                                        <option value="draft">Draft</option>
                                        <option value="reviewed">Reviewed</option>
                                        <option value="paid">Paid</option>
                                    </select>
                                </label>
                                <div className="rounded-lg bg-[var(--bg-subtle)] px-4 py-3 text-right">
                                    <div className="text-xs uppercase tracking-[0.14em] text-[var(--text-soft)]">Net sekarang</div>
                                    <div className="font-heading text-xl font-bold text-[var(--text-main)]">{formatCurrency(activePayroll.net_salary)}</div>
                                </div>
                            </div>

                            <div className="space-y-3">
                                {editForm.data.items.map((item, index) => (
                                    <div key={item.id} className="grid gap-3 rounded-lg border border-[var(--border-line)] p-4 md:grid-cols-[1.2fr_180px_1fr] md:items-start">
                                        <div>
                                            <div className="font-semibold text-[var(--text-main)]">{item.component?.name || "Komponen"}</div>
                                            <div className="mt-1 text-xs text-[var(--text-muted)]">{item.component?.code} / {item.component?.type}</div>
                                        </div>
                                        <input type="number" min="0" value={item.amount} onChange={(event) => setItem(index, "amount", event.target.value)} className="ui-input text-right" />
                                        <input value={item.notes} onChange={(event) => setItem(index, "notes", event.target.value)} className="ui-input" placeholder="Catatan" />
                                    </div>
                                ))}
                            </div>

                            <div className="mt-6 flex flex-col gap-3 sm:flex-row">
                                <button type="submit" disabled={editForm.processing} className="ui-button-primary">
                                    {editForm.processing ? "Menyimpan..." : "Simpan Payroll"}
                                </button>
                                <button type="button" onClick={() => setActivePayroll(null)} className="ui-button-secondary">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
