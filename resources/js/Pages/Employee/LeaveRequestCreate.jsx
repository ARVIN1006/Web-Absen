import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import { useState, useEffect } from "react";

export default function LeaveRequestCreate({ leaveTypes }) {
    const { data, setData, post, processing, errors } = useForm({
        leave_type_id: "",
        start_date: "",
        end_date: "",
        reason: "",
        attachment: null,
    });

    const [selectedLeaveType, setSelectedLeaveType] = useState(null);
    const [totalDays, setTotalDays] = useState(0);

    useEffect(() => {
        if (data.start_date && data.end_date) {
            const start = new Date(data.start_date);
            const end = new Date(data.end_date);
            const diff = Math.max(0, Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1);
            setTotalDays(diff);
        }
    }, [data.start_date, data.end_date]);

    useEffect(() => {
        const lt = leaveTypes.find((t) => t.id == data.leave_type_id);
        setSelectedLeaveType(lt || null);
    }, [data.leave_type_id, leaveTypes]);

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("employee.leave-requests.store"));
    };

    return (
        <AuthenticatedLayout>
            <Head title="Ajukan Cuti" />

            <div className="max-w-2xl mx-auto p-6">
                {/* Back Button */}
                <div className="mb-6">
                    <Link
                        href={route("employee.leave-requests.index")}
                        className="text-[var(--text-muted)] hover:text-[var(--text-main)] flex items-center gap-2 transition-colors"
                    >
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali
                    </Link>
                </div>

                <div className="glass rounded-3xl p-8">
                    <h1 className="text-2xl font-bold mb-6">Ajukan Pengajuan Cuti</h1>

                    <form onSubmit={handleSubmit} className="space-y-6" encType="multipart/form-data">
                        {/* Leave Type */}
                        <div>
                            <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                Tipe Cuti <span className="text-red-500">*</span>
                            </label>
                            <select
                                value={data.leave_type_id}
                                onChange={(e) => setData("leave_type_id", e.target.value)}
                                className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-blue-500 focus:bg-blue-500/5 outline-none transition-all"
                                required
                            >
                                <option value="">Pilih Tipe Cuti</option>
                                {leaveTypes.map((lt) => (
                                    <option key={lt.id} value={lt.id}>
                                        {lt.name} (Max: {lt.max_days_per_year} hari/tahun)
                                    </option>
                                ))}
                            </select>
                            {errors.leave_type_id && (
                                <p className="text-red-500 text-sm mt-1">{errors.leave_type_id}</p>
                            )}
                            {selectedLeaveType?.requires_attachment && (
                                <p className="text-xs text-yellow-500 mt-1">
                                    * Tipe cuti ini wajib melampirkan file pendukung
                                </p>
                            )}
                        </div>

                        {/* Date Range */}
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                    Tanggal Mulai <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    value={data.start_date}
                                    onChange={(e) => setData("start_date", e.target.value)}
                                    min={new Date().toISOString().split("T")[0]}
                                    className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-blue-500 outline-none transition-all"
                                    required
                                />
                                {errors.start_date && (
                                    <p className="text-red-500 text-sm mt-1">{errors.start_date}</p>
                                )}
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                    Tanggal Selesai <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    value={data.end_date}
                                    onChange={(e) => setData("end_date", e.target.value)}
                                    min={data.start_date || new Date().toISOString().split("T")[0]}
                                    className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-blue-500 outline-none transition-all"
                                    required
                                />
                                {errors.end_date && (
                                    <p className="text-red-500 text-sm mt-1">{errors.end_date}</p>
                                )}
                            </div>
                        </div>

                        {/* Total Days */}
                        {totalDays > 0 && (
                            <div className="p-4 bg-blue-500/10 rounded-xl">
                                <span className="text-sm text-[var(--text-muted)]">Total Hari: </span>
                                <span className="font-bold text-blue-500">{totalDays} hari</span>
                            </div>
                        )}

                        {/* Reason */}
                        <div>
                            <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                Alasan Cuti <span className="text-red-500">*</span>
                            </label>
                            <textarea
                                value={data.reason}
                                onChange={(e) => setData("reason", e.target.value)}
                                rows="3"
                                className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-blue-500 focus:bg-blue-500/5 outline-none transition-all resize-none"
                                placeholder="Jelaskan alasan pengajuan cuti..."
                                required
                            />
                            {errors.reason && <p className="text-red-500 text-sm mt-1">{errors.reason}</p>}
                        </div>

                        {/* Attachment */}
                        <div>
                            <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                Lampiran
                                {selectedLeaveType?.requires_attachment && (
                                    <span className="text-red-500"> *</span>
                                )}
                            </label>
                            <input
                                type="file"
                                accept=".jpg,.jpeg,.png,.pdf"
                                onChange={(e) => setData("attachment", e.target.files[0])}
                                className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-blue-500 outline-none transition-all"
                            />
                            <p className="text-xs text-[var(--text-muted)] mt-1">
                                Format: JPG, PNG, PDF (Max: 2MB)
                            </p>
                            {errors.attachment && (
                                <p className="text-red-500 text-sm mt-1">{errors.attachment}</p>
                            )}
                        </div>

                        {/* Submit Buttons */}
                        <div className="flex gap-4 pt-4">
                            <button
                                type="submit"
                                disabled={processing}
                                className="bg-gradient-to-r from-blue-500 to-blue-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all disabled:opacity-50"
                            >
                                {processing ? "Mengirim..." : "Ajukan Cuti"}
                            </button>
                            <Link
                                href={route("employee.leave-requests.index")}
                                className="px-8 py-3 rounded-xl font-bold border border-[var(--border-line)] hover:bg-[var(--hover-bg)] transition-all"
                            >
                                Batal
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
