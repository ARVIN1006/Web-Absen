import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function Reimbursements({ reimbursements, flash }) {
    const [showModal, setShowModal] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        title: "",
        amount: "",
        type: "",
        description: "",
        attachment: null,
    });

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(amount);
    };

    const getStatusBadge = (status) => {
        const variants = {
            pending: "bg-yellow-500/10 text-yellow-500",
            approved: "bg-green-500/10 text-green-500",
            paid: "bg-blue-500/10 text-blue-500",
            rejected: "bg-red-500/10 text-red-500",
        };
        const variant = variants[status] || variants.pending;
        const text = {
            pending: "MENUNGGU",
            approved: "DISETUJUI",
            paid: "DIBAYAR",
            rejected: "DITOLAK",
        };

        return (
            <span className={`px-3 py-1 rounded-lg text-xs font-bold ${variant}`}>
                {text[status] || text.pending}
            </span>
        );
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("reimbursements.store"), {
            onSuccess: () => {
                setShowModal(false);
                reset();
            },
        });
    };

    const [activeTab, setActiveTab] = useState("all");

    const filteredReimbursements = reimbursements.filter((reim) => {
        if (activeTab === "all") return true;
        return reim.status === activeTab;
    });

    return (
        <AuthenticatedLayout>
            <Head title="Reimbursement Saya" />

            <div className="max-w-[1200px] mx-auto p-6">
                {/* Header */}
                <div className="glass rounded-3xl p-6 mb-6 flex justify-between items-center border border-[var(--border-glass)] backdrop-blur-xl">
                    <div>
                        <h1 className="text-2xl font-bold text-[var(--text-main)]">Reimbursement Saya</h1>
                        <p className="text-[var(--text-muted)] mt-1">Ajukan klaim pengeluaran bisnis Anda.</p>
                    </div>
                    <button
                        onClick={() => setShowModal(true)}
                        className="bg-blue-500 text-white px-5 py-2.5 rounded-xl font-semibold flex items-center gap-2 hover:opacity-80 transition-all"
                    >
                        + Ajukan Klaim
                    </button>
                </div>

                {/* Tabs */}
                <div className="flex gap-2 mb-6 overflow-x-auto pb-2">
                    {["all", "pending", "approved", "paid", "rejected"].map((tab) => (
                        <button
                            key={tab}
                            onClick={() => setActiveTab(tab)}
                            className={`px-5 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap ${
                                activeTab === tab
                                    ? "bg-blue-500 text-white shadow-lg"
                                    : "glass text-[var(--text-muted)] hover:bg-[var(--hover-bg)]"
                            }`}
                        >
                            {tab === "all" ? "Semua" : tab.toUpperCase()}
                        </button>
                    ))}
                </div>

                {/* Flash Message */}
                {flash?.success && (
                    <div className="bg-green-500/15 border border-green-500/30 text-green-500 px-5 py-3 rounded-xl mb-5 font-medium">
                        {flash.success}
                    </div>
                )}

                {/* List */}
                <div className="space-y-4">
                    {filteredReimbursements.length === 0 ? (
                        <div className="text-center py-20 glass rounded-3xl border border-dashed border-[var(--border-glass)]">
                            <div className="text-4xl mb-4 opacity-40">📄</div>
                            <p className="text-[var(--text-muted)]">
                                Tidak ada pengajuan reimbursement dengan status ini.
                            </p>
                        </div>
                    ) : (
                        filteredReimbursements.map((reim) => (
                            <div key={reim.id} className="glass rounded-2xl p-5 flex flex-col md:flex-row justify-between gap-4 border border-[var(--border-glass)] hover:border-blue-500/30 transition-all">
                                <div className="flex-1">
                                    <div className="flex items-center gap-3 mb-2">
                                        <h3 className="font-bold text-[var(--text-main)]">{reim.title}</h3>
                                        {getStatusBadge(reim.status)}
                                    </div>
                                    <p className="text-sm text-[var(--text-muted)] mb-3">{reim.description}</p>
                                    <div className="flex flex-wrap gap-4 text-xs text-[var(--text-muted)]">
                                        <span className="flex items-center gap-1">
                                            🏷️ Tipe: <span className="font-bold text-[var(--text-main)] capitalize">{reim.type}</span>
                                        </span>
                                        <span className="flex items-center gap-1">
                                            📅 Diajukan: <span className="font-bold text-[var(--text-main)]">{new Date(reim.created_at).toLocaleDateString("id-ID")}</span>
                                        </span>
                                    </div>
                                </div>
                                <div className="text-right flex flex-col justify-between">
                                    <div className="text-xl font-black text-[var(--text-main)]">
                                        {formatCurrency(reim.amount)}
                                    </div>
                                    {reim.attachment_path && (
                                        <a
                                            href={`/storage/${reim.attachment_path}`}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="text-xs text-blue-500 hover:underline font-bold flex items-center justify-end gap-1 mt-2"
                                        >
                                            <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Lihat Lampiran
                                        </a>
                                    )}
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div className="bg-[var(--bg-body)] border border-[var(--border-glass)] rounded-2xl w-full max-w-md overflow-hidden">
                        <div className="px-6 py-4 border-b border-[var(--border-glass)] flex justify-between items-center">
                            <h3 className="text-lg font-bold text-[var(--text-main)]">Ajukan Reimbursement</h3>
                            <button
                                onClick={() => setShowModal(false)}
                                className="text-[var(--text-muted)] hover:text-[var(--text-main)] text-2xl"
                            >
                                &times;
                            </button>
                        </div>
                        <form onSubmit={handleSubmit} className="p-6 space-y-4" encType="multipart/form-data">
                            <div>
                                <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                    Judul Klaim <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.title}
                                    onChange={(e) => setData("title", e.target.value)}
                                    className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-glass)] focus:border-blue-500 outline-none transition-all"
                                    placeholder="e.g., Perjalanan Dinas ke Jakarta"
                                    required
                                />
                                {errors.title && <p className="text-red-500 text-sm mt-1">{errors.title}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                    Jumlah (Rp) <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    value={data.amount}
                                    onChange={(e) => setData("amount", e.target.value)}
                                    className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-glass)] focus:border-blue-500 outline-none transition-all"
                                    placeholder="500000"
                                    required
                                />
                                {errors.amount && <p className="text-red-500 text-sm mt-1">{errors.amount}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                    Tipe <span className="text-red-500">*</span>
                                </label>
                                <select
                                    value={data.type}
                                    onChange={(e) => setData("type", e.target.value)}
                                    className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-glass)] focus:border-blue-500 outline-none transition-all"
                                    required
                                >
                                    <option value="">Pilih Tipe</option>
                                    <option value="transport">Transportasi</option>
                                    <option value="meal">Makanan</option>
                                    <option value="accommodation">Akomodasi</option>
                                    <option value="other">Lainnya</option>
                                </select>
                                {errors.type && <p className="text-red-500 text-sm mt-1">{errors.type}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                    Deskripsi
                                </label>
                                <textarea
                                    value={data.description}
                                    onChange={(e) => setData("description", e.target.value)}
                                    rows="2"
                                    className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-glass)] focus:border-blue-500 outline-none transition-all resize-none"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                    Lampiran <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={(e) => setData("attachment", e.target.files[0])}
                                    className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-glass)] focus:border-blue-500 outline-none transition-all"
                                    required
                                />
                                {errors.attachment && <p className="text-red-500 text-sm mt-1">{errors.attachment}</p>}
                            </div>
                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full bg-blue-500 text-white py-3 rounded-xl font-bold hover:opacity-90 transition-all disabled:opacity-50"
                            >
                                {processing ? "Mengirim..." : "Ajukan Klaim"}
                            </button>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
