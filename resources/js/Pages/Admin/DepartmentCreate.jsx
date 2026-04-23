import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";

export default function DepartmentCreate({ users }) {
    const { data, setData, post, processing, errors } = useForm({
        name: "",
        code: "",
        description: "",
        head_id: "",
        is_active: true,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("admin.departments.store"));
    };

    return (
        <AuthenticatedLayout>
            <Head title="Tambah Departemen" />

            <div className="max-w-2xl mx-auto p-6">
                <div className="mb-6">
                    <Link
                        href={route("admin.departments.index")}
                        className="text-[var(--text-muted)] hover:text-[var(--text-main)] flex items-center gap-2 transition-colors"
                    >
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali
                    </Link>
                </div>

                <div className="glass rounded-3xl p-8">
                    <h1 className="text-2xl font-bold mb-6">Tambah Departemen Baru</h1>

                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div>
                            <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                Nama Departemen <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                value={data.name}
                                onChange={(e) => setData("name", e.target.value)}
                                className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-blue-500 focus:bg-blue-500/5 outline-none transition-all"
                                placeholder="e.g., Human Resources, IT Department"
                                required
                            />
                            {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                Kode Departemen <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                value={data.code}
                                onChange={(e) => setData("code", e.target.value)}
                                className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-blue-500 focus:bg-blue-500/5 outline-none transition-all"
                                placeholder="e.g., HRD, IT, FIN"
                                required
                            />
                            {errors.code && <p className="text-red-500 text-sm mt-1">{errors.code}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                Deskripsi
                            </label>
                            <textarea
                                value={data.description}
                                onChange={(e) => setData("description", e.target.value)}
                                rows="3"
                                className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-blue-500 focus:bg-blue-500/5 outline-none transition-all resize-none"
                                placeholder="Deskripsi singkat tentang departemen..."
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                Ketua Departemen
                            </label>
                            <select
                                value={data.head_id}
                                onChange={(e) => setData("head_id", e.target.value)}
                                className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-blue-500 outline-none transition-all"
                            >
                                <option value="">Pilih Ketua Departemen</option>
                                {users.map((user) => (
                                    <option key={user.id} value={user.id}>
                                        {user.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="flex items-center gap-3">
                            <input
                                type="checkbox"
                                id="is_active"
                                checked={data.is_active}
                                onChange={(e) => setData("is_active", e.target.checked)}
                                className="w-5 h-5 rounded border-[var(--border-line)]"
                            />
                            <label htmlFor="is_active" className="text-sm font-semibold">
                                Departemen Aktif
                            </label>
                        </div>

                        <div className="flex gap-4 pt-4">
                            <button
                                type="submit"
                                disabled={processing}
                                className="bg-gradient-to-r from-blue-500 to-blue-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all disabled:opacity-50"
                            >
                                {processing ? "Menyimpan..." : "Simpan Departemen"}
                            </button>
                            <Link
                                href={route("admin.departments.index")}
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
