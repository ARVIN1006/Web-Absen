import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";

export default function OrgChart({ departments }) {
    return (
        <AuthenticatedLayout>
            <Head title="Struktur Organisasi" />

            <div className="max-w-[1200px] mx-auto p-6">
                {/* Header */}
                <div className="glass rounded-3xl p-6 mb-8 border border-[var(--border-glass)] backdrop-blur-xl">
                    <h1 className="text-2xl font-bold text-[var(--text-main)]">Struktur Organisasi</h1>
                    <p className="text-[var(--text-muted)] mt-1">Visualisasi hierarki perusahaan</p>
                </div>

                {/* Org Chart */}
                <div className="space-y-8">
                    {departments.map((dept) => (
                        <div key={dept.id} className="glass rounded-3xl p-6 border border-[var(--border-glass)]">
                            {/* Department Header */}
                            <div className="flex items-center gap-4 mb-6 pb-4 border-b border-[var(--border-glass)]">
                                <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-500 text-white flex items-center justify-center font-bold text-xl">
                                    {dept.name.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <h2 className="text-xl font-bold text-[var(--text-main)]">{dept.name}</h2>
                                    <p className="text-sm text-[var(--text-muted)]">
                                        {dept.users?.length || 0} Anggota
                                    </p>
                                </div>
                                {dept.head && (
                                    <div className="ml-auto flex items-center gap-2 px-4 py-2 bg-yellow-500/10 border border-yellow-500/20 rounded-xl">
                                        <span className="text-xs text-yellow-500 font-bold uppercase">Kepala Dept</span>
                                        <span className="text-sm font-semibold text-[var(--text-main)]">{dept.head.name}</span>
                                    </div>
                                )}
                            </div>

                            {/* Members Grid */}
                            {dept.users?.length > 0 ? (
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    {dept.users.map((user) => (
                                        <div
                                            key={user.id}
                                            className="bg-black/5 rounded-2xl p-4 flex items-center gap-3 hover:bg-black/10 transition-colors"
                                        >
                                            <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-blue-500 text-white flex items-center justify-center font-bold text-lg">
                                                {user.name.charAt(0).toUpperCase()}
                                            </div>
                                            <div>
                                                <div className="font-semibold text-[var(--text-main)]">{user.name}</div>
                                                <div className="text-xs text-[var(--text-muted)]">
                                                    {user.position?.name || "Staff"}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-8 text-[var(--text-muted)]">
                                    Belum ada anggota dalam departemen ini.
                                </div>
                            )}
                        </div>
                    ))}
                </div>

                {departments.length === 0 && (
                    <div className="text-center py-20 glass rounded-3xl border border-dashed border-[var(--border-glass)]">
                        <div className="text-5xl mb-6 opacity-40">🏢</div>
                        <h3 className="text-xl font-bold text-[var(--text-main)] mb-2">Belum Ada Departemen</h3>
                        <p className="text-[var(--text-muted)]">
                            Tambahkan departemen terlebih dahulu untuk melihat struktur organisasi.
                        </p>
                        <Link
                            href={route("admin.departments.index")}
                            className="inline-block mt-4 text-blue-500 font-semibold hover:underline"
                        >
                            Kelola Departemen →
                        </Link>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
