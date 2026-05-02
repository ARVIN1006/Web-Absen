import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function Reimbursements({ reimbursements, flash, filters = {} }) {
    const [showProcessModal, setShowProcessModal] = useState(false);
    const [processingReimbursement, setProcessingReimbursement] = useState(null);
    const [actionType, setActionType] = useState("approved");

    const { data, setData, patch, processing, reset } = useForm({
        status: "approved",
        admin_note: "",
    });
    const filterForm = useForm({
        status: filters.status || "",
    });

    const openProcessModal = (reimbursement, action) => {
        setProcessingReimbursement(reimbursement);
        setActionType(action);
        setData({
            status: action,
            admin_note: "",
        });
        setShowProcessModal(true);
    };

    const closeModal = () => {
        setShowProcessModal(false);
        setProcessingReimbursement(null);
        reset();
    };

    const handleSubmit = (event) => {
        event.preventDefault();
        patch(route("admin.reimbursements.updateStatus", processingReimbursement.id), {
            onSuccess: closeModal,
        });
    };

    const getStatusBadge = (status) => {
        const styles = {
            pending: "bg-amber-50 text-[var(--warning-color)]",
            approved: "bg-emerald-50 text-[var(--success-color)]",
            paid: "bg-blue-50 text-blue-600",
            rejected: "bg-red-50 text-red-600",
        };

        return <span className={`ui-badge ${styles[status] || "bg-slate-100 text-slate-500"}`}>{status}</span>;
    };

    const submitFilters = (event) => {
        event.preventDefault();
        router.get(route("admin.reimbursements.index"), filterForm.data, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Reimbursement" />

            <div className="mobile-page">
                <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div className="ui-section-title">Expense Review</div>
                    <h1 className="mt-3 text-2xl font-extrabold text-[var(--text-main)] sm:text-3xl">
                        Klaim Reimbursement
                    </h1>
                    <p className="mt-2 text-sm text-[var(--text-muted)]">
                        Verifikasi dan proses klaim biaya karyawan.
                    </p>
                </div>

                {flash?.success && (
                    <div className="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                        {flash.success}
                    </div>
                )}
                {flash?.error && (
                    <div className="mt-4 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700">
                        {flash.error}
                    </div>
                )}

                <form onSubmit={submitFilters} className="mt-6 grid gap-3 rounded-[20px] border border-slate-200 bg-white p-4 sm:grid-cols-[1fr_auto_auto] sm:items-end">
                    <label className="block">
                        <span className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Status</span>
                        <select value={filterForm.data.status} onChange={(event) => filterForm.setData("status", event.target.value)} className="ui-input">
                            <option value="">Semua status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="paid">Paid</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </label>
                    <button type="submit" className="ui-button-primary">Filter</button>
                    <button type="button" onClick={() => { filterForm.setData("status", ""); router.get(route("admin.reimbursements.index"), {}, { preserveScroll: true }); }} className="ui-button-secondary">Reset</button>
                </form>

                <div className="mt-6 hidden overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm lg:block">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-[var(--border-line)] bg-[var(--bg-subtle)]">
                                    <th className="px-6 py-4 text-left text-xs font-bold uppercase text-[var(--text-soft)]">Karyawan</th>
                                    <th className="px-6 py-4 text-left text-xs font-bold uppercase text-[var(--text-soft)]">Judul Klaim</th>
                                    <th className="px-6 py-4 text-left text-xs font-bold uppercase text-[var(--text-soft)]">Jumlah</th>
                                    <th className="px-6 py-4 text-left text-xs font-bold uppercase text-[var(--text-soft)]">Status</th>
                                    <th className="px-6 py-4 text-left text-xs font-bold uppercase text-[var(--text-soft)]">Tanggal</th>
                                    <th className="px-6 py-4 text-right text-xs font-bold uppercase text-[var(--text-soft)]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {reimbursements.length === 0 ? (
                                    <tr>
                                        <td colSpan="6" className="px-6 py-12 text-center text-sm text-[var(--text-muted)]">
                                            Belum ada data reimbursement.
                                        </td>
                                    </tr>
                                ) : (
                                    reimbursements.map((reim) => (
                                        <tr key={reim.id} className="border-b border-[var(--border-line)] hover:bg-[var(--hover-bg)]">
                                            <td className="px-6 py-4">
                                                <div className="font-semibold text-[var(--text-main)]">{reim.user?.name}</div>
                                                <div className="text-xs text-[var(--text-muted)]">{reim.user?.position?.name || "Karyawan"}</div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="font-semibold text-[var(--text-main)]">{reim.title}</div>
                                                <div className="text-xs text-[var(--text-muted)]">{reim.description}</div>
                                            </td>
                                            <td className="px-6 py-4 font-semibold text-[var(--text-main)]">
                                                Rp {new Intl.NumberFormat("id-ID").format(reim.amount)}
                                            </td>
                                            <td className="px-6 py-4">{getStatusBadge(reim.status)}</td>
                                            <td className="px-6 py-4 text-sm text-[var(--text-muted)]">
                                                {new Date(reim.created_at).toLocaleDateString("id-ID", {
                                                    day: "numeric",
                                                    month: "short",
                                                    year: "numeric",
                                                })}
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                {reim.status === "pending" ? (
                                                    <div className="flex justify-end gap-2">
                                                        <button onClick={() => openProcessModal(reim, "approved")} className="ui-button-primary">
                                                            Setujui
                                                        </button>
                                                        <button onClick={() => openProcessModal(reim, "rejected")} className="ui-button-secondary border-red-200 text-red-600 hover:bg-red-50">
                                                            Tolak
                                                        </button>
                                                    </div>
                                                ) : (
                                                    <div className="flex justify-end gap-2">
                                                        {reim.status === "approved" && (
                                                            <button onClick={() => openProcessModal(reim, "paid")} className="ui-button-primary">
                                                                Tandai Dibayar
                                                            </button>
                                                        )}
                                                        <div className="text-xs text-[var(--text-muted)]">
                                                            Oleh: {reim.approver?.name || "-"}
                                                            {reim.reimbursed_at && <div>Dibayar: {new Date(reim.reimbursed_at).toLocaleDateString("id-ID")}</div>}
                                                        </div>
                                                    </div>
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
                    {reimbursements.length === 0 ? (
                        <div className="rounded-[24px] border border-dashed border-[var(--border-strong)] bg-white p-8 text-center text-sm text-[var(--text-muted)]">
                            Belum ada data reimbursement.
                        </div>
                    ) : (
                        reimbursements.map((reim) => (
                            <div key={reim.id} className="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                                <div className="flex items-start justify-between gap-3">
                                    <div>
                                        <div className="font-semibold text-[var(--text-main)]">{reim.user?.name}</div>
                                        <div className="text-xs text-[var(--text-muted)]">{reim.user?.position?.name || "Karyawan"}</div>
                                    </div>
                                    {getStatusBadge(reim.status)}
                                </div>

                                <div className="mt-4">
                                    <div className="font-semibold text-[var(--text-main)]">{reim.title}</div>
                                    <div className="mt-1 text-sm text-[var(--text-muted)]">{reim.description}</div>
                                </div>

                                <div className="mt-4 rounded-2xl bg-[var(--bg-subtle)] px-4 py-3">
                                    <div className="text-xs uppercase tracking-[0.16em] text-[var(--text-soft)]">Jumlah</div>
                                    <div className="mt-1 font-heading text-xl font-bold text-[var(--text-main)]">
                                        Rp {new Intl.NumberFormat("id-ID").format(reim.amount)}
                                    </div>
                                </div>

                                <div className="mt-3 text-xs text-[var(--text-muted)]">
                                    Diajukan {new Date(reim.created_at).toLocaleDateString("id-ID", {
                                        day: "numeric",
                                        month: "short",
                                        year: "numeric",
                                    })}
                                </div>

                                <div className="mt-4">
                                    {reim.status === "pending" ? (
                                        <div className="grid grid-cols-2 gap-3">
                                            <button onClick={() => openProcessModal(reim, "approved")} className="ui-button-primary w-full">
                                                Setujui
                                            </button>
                                            <button onClick={() => openProcessModal(reim, "rejected")} className="ui-button-secondary w-full border-red-200 text-red-600 hover:bg-red-50">
                                                Tolak
                                            </button>
                                        </div>
                                    ) : (
                                        <div className="rounded-2xl border border-[var(--border-line)] px-4 py-3 text-sm text-[var(--text-muted)]">
                                            Oleh: {reim.approver?.name || "-"}
                                            {reim.reimbursed_at && <div className="mt-1">Dibayar: {new Date(reim.reimbursed_at).toLocaleDateString("id-ID")}</div>}
                                            {reim.admin_note && (
                                                <div className="mt-1 italic text-[var(--text-main)]">&quot;{reim.admin_note}&quot;</div>
                                            )}
                                            {reim.status === "approved" && (
                                                <button onClick={() => openProcessModal(reim, "paid")} className="ui-button-primary mt-3 w-full">
                                                    Tandai Dibayar
                                                </button>
                                            )}
                                        </div>
                                    )}
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>

            {showProcessModal && processingReimbursement && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
                    <div className="w-full max-w-md overflow-hidden rounded-3xl border border-[var(--border-line)] bg-[var(--bg-body)] shadow-2xl">
                        <div className="flex items-center justify-between border-b border-[var(--border-line)] bg-black/5 px-6 py-4">
                            <h3 className="font-extrabold text-lg">
                                {actionType === "approved" ? "Setujui Reimbursement" : actionType === "paid" ? "Tandai Reimbursement Dibayar" : "Tolak Reimbursement"}
                            </h3>
                            <button onClick={closeModal} className="text-2xl text-[var(--text-muted)] hover:text-[var(--text-main)]">
                                &times;
                            </button>
                        </div>
                        <div className="p-6">
                            <div className="mb-4 rounded-xl bg-black/5 p-4">
                                <div className="text-sm text-[var(--text-muted)]">Karyawan</div>
                                <div className="font-bold text-[var(--text-main)]">{processingReimbursement.user?.name}</div>
                                <div className="mt-2 text-sm text-[var(--text-muted)]">Judul Klaim</div>
                                <div className="font-bold text-[var(--text-main)]">{processingReimbursement.title}</div>
                                <div className="mt-2 text-sm text-[var(--text-muted)]">Jumlah</div>
                                <div className="font-bold text-blue-600">
                                    Rp {new Intl.NumberFormat("id-ID").format(processingReimbursement.amount)}
                                </div>
                            </div>

                            <form onSubmit={handleSubmit} className="space-y-4">
                                <div>
                                    <label className="mb-2 block text-xs font-bold uppercase tracking-wide text-[var(--text-soft)]">
                                        {actionType === "rejected" ? "Alasan Penolakan *" : "Catatan (Opsional)"}
                                    </label>
                                    <textarea
                                        value={data.admin_note}
                                        onChange={(event) => setData("admin_note", event.target.value)}
                                        rows="3"
                                        className="ui-input"
                                        placeholder={actionType === "rejected" ? "Wajib diisi agar karyawan tahu alasannya..." : "Berikan catatan jika diperlukan..."}
                                        required={actionType === "rejected"}
                                    />
                                </div>
                                <button type="submit" disabled={processing} className={`w-full rounded-xl py-4 font-bold text-white ${actionType === "rejected" ? "bg-red-500" : actionType === "paid" ? "bg-blue-500" : "bg-green-500"}`}>
                                    {processing ? "Memproses..." : actionType === "approved" ? "Konfirmasi Persetujuan" : actionType === "paid" ? "Konfirmasi Pembayaran" : "Konfirmasi Penolakan"}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
