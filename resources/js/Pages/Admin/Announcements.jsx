import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function Announcements({ announcements, flash }) {
    const [showAddModal, setShowAddModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [editingAnn, setEditingAnn] = useState(null);

    const {
        data,
        setData,
        post,
        put,
        delete: destroy,
        patch,
        processing,
        errors,
        reset,
    } = useForm({
        title: "",
        content: "",
        type: "info",
    });

    const typeColors = {
        info: { bg: "rgba(59, 130, 246, 0.1)", color: "#3b82f6", border: "rgba(59, 130, 246, 0.2)" },
        warning: { bg: "rgba(245, 158, 11, 0.1)", color: "#f59e0b", border: "rgba(245, 158, 11, 0.2)" },
        urgent: { bg: "rgba(239, 68, 68, 0.1)", color: "#ef4444", border: "rgba(239, 68, 68, 0.2)" },
        event: { bg: "rgba(139, 92, 246, 0.1)", color: "#8b5cf6", border: "rgba(139, 92, 246, 0.2)" },
    };

    const typeIcons = {
        info: (
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        ),
        warning: (
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        ),
        urgent: (
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        ),
        event: (
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        ),
    };

    const openEditModal = (ann) => {
        setEditingAnn(ann);
        setData({
            title: ann.title,
            content: ann.content,
            type: ann.type || "info",
        });
        setShowEditModal(true);
    };

    const closeAddModal = () => {
        setShowAddModal(false);
        reset();
    };

    const closeEditModal = () => {
        setShowEditModal(false);
        setEditingAnn(null);
        reset();
    };

    const handleAddSubmit = (e) => {
        e.preventDefault();
        post(route("admin.announcements.store"), {
            onSuccess: closeAddModal,
        });
    };

    const handleEditSubmit = (e) => {
        e.preventDefault();
        put(route("admin.announcements.update", editingAnn.id), {
            onSuccess: closeEditModal,
        });
    };

    const handleToggle = (id) => {
        patch(route("admin.announcements.toggle", id));
    };

    const handleDelete = (id) => {
        if (confirm("Hapus pengumuman ini?")) {
            destroy(route("admin.announcements.destroy", id));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Pusat Informasi" />

            <div className="ann-container max-w-[1200px] mx-auto p-6">
                {/* Header */}
                <div className="glass rounded-3xl p-6 mb-8 flex justify-between items-center border border-[var(--border-glass)] backdrop-blur-xl">
                    <div>
                        <h1 className="text-3xl font-extrabold bg-gradient-to-r from-[var(--text-main)] to-[var(--text-muted)] bg-clip-text text-transparent">
                            Pusat Informasi
                        </h1>
                        <p className="text-[var(--text-muted)] mt-1">
                            Kelola pengumuman dan berita untuk seluruh karyawan.
                        </p>
                    </div>
                    <button
                        onClick={() => setShowAddModal(true)}
                        className="bg-gradient-to-r from-blue-500 to-blue-700 text-white px-6 py-4 rounded-2xl font-bold flex items-center gap-3 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all"
                    >
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Buat Pengumuman
                    </button>
                </div>

                {/* Flash Message */}
                {flash?.success && (
                    <div className="bg-green-500/10 border-l-4 border-green-500 text-green-500 px-5 py-4 rounded-xl mb-6 flex items-center gap-3">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                        </svg>
                        <span className="font-semibold">{flash.success}</span>
                    </div>
                )}

                {/* Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {announcements.map((ann) => (
                        <div
                            key={ann.id}
                            className="glass rounded-3xl p-6 relative flex flex-col justify-between hover:-translate-y-1 hover:border-blue-500/50 hover:shadow-xl transition-all"
                        >
                            <div>
                                <div className="flex justify-between items-start mb-3">
                                    <span
                                        className="px-3 py-1.5 rounded-xl text-xs font-extrabold uppercase tracking-wider flex items-center gap-1.5 border"
                                        style={{
                                            backgroundColor: typeColors[ann.type || "info"].bg,
                                            color: typeColors[ann.type || "info"].color,
                                            borderColor: typeColors[ann.type || "info"].border,
                                        }}
                                    >
                                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {typeIcons[ann.type || "info"]}
                                        </svg>
                                        {ann.type?.toUpperCase() || "INFO"}
                                    </span>
                                    <span
                                        className={`w-2 h-2 rounded-full ${
                                            ann.is_active ? "bg-green-500 shadow-[0_0_8px_#10b981]" : "bg-red-500"
                                        }`}
                                        title={ann.is_active ? "Aktif" : "Non-aktif"}
                                    />
                                </div>

                                <h3 className="text-xl font-bold mb-2 text-[var(--text-main)]">{ann.title}</h3>
                                <div className="text-xs text-[var(--text-muted)] mb-4 flex items-center gap-2">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {new Date(ann.created_at).toLocaleDateString("id-ID", {
                                        day: "numeric",
                                        month: "short",
                                        year: "numeric",
                                        hour: "2-digit",
                                        minute: "2-digit",
                                    })}
                                </div>
                                <div className="text-sm text-[var(--text-main)] opacity-80 leading-relaxed mb-4 line-clamp-4">
                                    {ann.content}
                                </div>
                            </div>

                            <div className="flex gap-2 pt-4 border-t border-[var(--border-glass)]">
                                <button
                                    onClick={() => handleToggle(ann.id)}
                                    className="w-11 h-11 rounded-xl border border-[var(--border-glass)] bg-black/5 text-[var(--text-muted)] flex items-center justify-center hover:bg-blue-500/10 hover:text-blue-500 hover:border-blue-500 transition-all"
                                    title={ann.is_active ? "Non-aktifkan" : "Aktifkan"}
                                >
                                    {ann.is_active ? (
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                        </svg>
                                    ) : (
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    )}
                                </button>
                                <button
                                    onClick={() => openEditModal(ann)}
                                    className="w-11 h-11 rounded-xl border border-[var(--border-glass)] bg-black/5 text-[var(--text-muted)] flex items-center justify-center hover:bg-blue-500/10 hover:text-blue-500 hover:border-blue-500 transition-all"
                                    title="Edit"
                                >
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    onClick={() => handleDelete(ann.id)}
                                    className="w-11 h-11 rounded-xl border border-[var(--border-glass)] bg-black/5 text-red-500 flex items-center justify-center hover:bg-red-500/10 hover:border-red-500 transition-all"
                                    title="Hapus"
                                >
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    ))}
                </div>

                {announcements.length === 0 && (
                    <div className="text-center py-20 glass rounded-3xl border border-dashed border-[var(--border-glass)]">
                        <div className="text-5xl mb-6 opacity-50">📢</div>
                        <h3 className="text-xl font-bold text-[var(--text-main)] mb-2">Belum Ada Pengumuman</h3>
                        <p className="text-[var(--text-muted)]">Klik tombol di atas untuk menerbitkan informasi pertama Anda.</p>
                    </div>
                )}
            </div>

            {/* Add Modal */}
            {showAddModal && (
                <Modal title="Terbitkan Pengumuman" onClose={closeAddModal}>
                    <form onSubmit={handleAddSubmit} className="space-y-4">
                        <div>
                            <label className="block text-xs font-bold text-[var(--text-muted-dark)] uppercase tracking-wide mb-2">
                                Judul Pengumuman
                            </label>
                            <input
                                type="text"
                                value={data.title}
                                onChange={(e) => setData("title", e.target.value)}
                                className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-blue-500 focus:bg-blue-500/5 outline-none transition-all"
                                placeholder="Contoh: Jadwal Libur Lebaran 2026"
                                required
                            />
                            {errors.title && <p className="text-red-500 text-sm mt-1">{errors.title}</p>}
                        </div>
                        <div>
                            <label className="block text-xs font-bold text-[var(--text-muted-dark)] uppercase tracking-wide mb-2">
                                Kategori / Tipe
                            </label>
                            <select
                                value={data.type}
                                onChange={(e) => setData("type", e.target.value)}
                                className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-blue-500 outline-none transition-all"
                                required
                            >
                                <option value="info">Informasi Umum</option>
                                <option value="warning">Peringatan / Notice</option>
                                <option value="urgent">Mendesak / Urgent</option>
                                <option value="event">Acara / Event</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-bold text-[var(--text-muted-dark)] uppercase tracking-wide mb-2">
                                Isi Pengumuman
                            </label>
                            <textarea
                                value={data.content}
                                onChange={(e) => setData("content", e.target.value)}
                                rows="6"
                                className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-blue-500 focus:bg-blue-500/5 outline-none transition-all resize-none"
                                placeholder="Tuliskan rincian pengumuman di sini secara lengkap..."
                                required
                            />
                            {errors.content && <p className="text-red-500 text-sm mt-1">{errors.content}</p>}
                        </div>
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full bg-gradient-to-r from-blue-500 to-blue-700 text-white py-4 rounded-xl font-bold hover:shadow-lg transition-all disabled:opacity-50"
                        >
                            {processing ? "Menerbitkan..." : "Terbitkan Sekarang"}
                        </button>
                    </form>
                </Modal>
            )}

            {/* Edit Modal */}
            {showEditModal && (
                <Modal title="Edit Pengumuman" onClose={closeEditModal}>
                    <form onSubmit={handleEditSubmit} className="space-y-4">
                        <div>
                            <label className="block text-xs font-bold text-[var(--text-muted-dark)] uppercase tracking-wide mb-2">
                                Judul Pengumuman
                            </label>
                            <input
                                type="text"
                                value={data.title}
                                onChange={(e) => setData("title", e.target.value)}
                                className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-blue-500 focus:bg-blue-500/5 outline-none transition-all"
                                required
                            />
                            {errors.title && <p className="text-red-500 text-sm mt-1">{errors.title}</p>}
                        </div>
                        <div>
                            <label className="block text-xs font-bold text-[var(--text-muted-dark)] uppercase tracking-wide mb-2">
                                Kategori / Tipe
                            </label>
                            <select
                                value={data.type}
                                onChange={(e) => setData("type", e.target.value)}
                                className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-blue-500 outline-none transition-all"
                                required
                            >
                                <option value="info">Informasi Umum</option>
                                <option value="warning">Peringatan / Notice</option>
                                <option value="urgent">Mendesak / Urgent</option>
                                <option value="event">Acara / Event</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-bold text-[var(--text-muted-dark)] uppercase tracking-wide mb-2">
                                Isi Pengumuman
                            </label>
                            <textarea
                                value={data.content}
                                onChange={(e) => setData("content", e.target.value)}
                                rows="6"
                                className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-blue-500 focus:bg-blue-500/5 outline-none transition-all resize-none"
                                required
                            />
                            {errors.content && <p className="text-red-500 text-sm mt-1">{errors.content}</p>}
                        </div>
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full bg-gradient-to-r from-blue-500 to-blue-700 text-white py-4 rounded-xl font-bold hover:shadow-lg transition-all disabled:opacity-50"
                        >
                            {processing ? "Menyimpan..." : "Simpan Perubahan"}
                        </button>
                    </form>
                </Modal>
            )}
        </AuthenticatedLayout>
    );
}

function Modal({ title, children, onClose }) {
    return (
        <div className="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div className="bg-[var(--bg-body)] border border-[var(--border-glass)] rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden">
                <div className="px-6 py-4 border-b border-[var(--border-line)] bg-black/5 flex justify-between items-center">
                    <h3 className="font-extrabold text-lg">{title}</h3>
                    <button onClick={onClose} className="text-[var(--text-muted)] hover:text-[var(--text-main)] text-2xl">
                        &times;
                    </button>
                </div>
                <div className="p-6">{children}</div>
            </div>
        </div>
    );
}
