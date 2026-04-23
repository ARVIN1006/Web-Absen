import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";

export default function Directory({ employees, departments, filters }) {
    const { data, setData, get, processing } = useForm({
        search: filters.search || "",
        dept: filters.dept || "",
    });

    const handleSearch = (e) => {
        e.preventDefault();
        get(route("company-directory"), { preserveState: true });
    };

    const directoryHref = (dept = "") => {
        const params = new URLSearchParams();

        if (dept) params.set("dept", dept);
        if (data.search) params.set("search", data.search);

        const query = params.toString();

        return query ? `${route("company-directory")}?${query}` : route("company-directory");
    };

    return (
        <AuthenticatedLayout>
            <Head title="Direktori Karyawan" />

            <div className="mx-auto max-w-[1200px] space-y-6 p-4 sm:p-6">
                <section className="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                    <div className="bg-gradient-to-br from-blue-600 via-indigo-600 to-slate-900 px-5 py-6 text-white sm:px-7">
                        <div className="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                            <div>
                                <p className="text-xs font-semibold uppercase tracking-[0.22em] text-blue-100">Company Directory</p>
                                <h1 className="mt-2 font-heading text-2xl font-bold sm:text-3xl">Direktori Karyawan</h1>
                                <p className="mt-2 max-w-2xl text-sm leading-6 text-blue-50">
                                    Cari rekan kerja berdasarkan nama, jabatan, atau departemen dengan tampilan yang lebih ringkas dan nyaman.
                                </p>
                            </div>

                            <div className="grid grid-cols-2 gap-3 sm:min-w-[260px]">
                                <div className="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 backdrop-blur">
                                    <div className="text-2xl font-bold">{employees.length}</div>
                                    <div className="text-xs font-medium text-blue-100">Karyawan tampil</div>
                                </div>
                                <div className="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 backdrop-blur">
                                    <div className="text-2xl font-bold">{departments.length}</div>
                                    <div className="text-xs font-medium text-blue-100">Departemen</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="space-y-4 px-5 py-5 sm:px-7">
                        <form onSubmit={handleSearch} className="flex flex-col gap-3 md:flex-row">
                            <div className="relative flex-1">
                                <span className="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
                                    </svg>
                                </span>
                                <input
                                    type="text"
                                    value={data.search}
                                    onChange={(e) => setData("search", e.target.value)}
                                    placeholder="Cari nama, email, atau jabatan..."
                                    className="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"
                                />
                            </div>
                            <button
                                type="submit"
                                disabled={processing}
                                className="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                {processing ? "Mencari..." : "Cari Karyawan"}
                            </button>
                        </form>

                        <div className="-mx-1 flex gap-2 overflow-x-auto px-1 pb-1">
                            <Link
                                href={directoryHref()}
                                className={`shrink-0 rounded-full border px-4 py-2 text-sm font-semibold transition ${
                                    !filters.dept
                                        ? "border-blue-600 bg-blue-600 text-white shadow-sm"
                                        : "border-slate-200 bg-slate-50 text-slate-600 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                                }`}
                            >
                                Semua Divisi
                            </Link>
                            {departments.map((dept) => (
                                <Link
                                    key={dept.id}
                                    href={directoryHref(dept.id)}
                                    className={`shrink-0 rounded-full border px-4 py-2 text-sm font-semibold transition ${
                                        filters.dept == dept.id
                                            ? "border-blue-600 bg-blue-600 text-white shadow-sm"
                                            : "border-slate-200 bg-slate-50 text-slate-600 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                                    }`}
                                >
                                    {dept.name}
                                </Link>
                            ))}
                        </div>
                    </div>
                </section>

                <section className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    {employees.length === 0 ? (
                        <div className="col-span-full rounded-[28px] border border-dashed border-slate-300 bg-white px-6 py-14 text-center shadow-sm">
                            <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m8-4a4 4 0 100-8 4 4 0 000 8zM9 10a4 4 0 100-8 4 4 0 000 8z" />
                                </svg>
                            </div>
                            <h2 className="mt-4 text-lg font-semibold text-slate-800">Tidak ada karyawan ditemukan</h2>
                            <p className="mt-1 text-sm text-slate-500">Coba ubah kata kunci atau pilih divisi lain.</p>
                        </div>
                    ) : (
                        employees.map((emp) => (
                            <div
                                key={emp.id}
                                className="group rounded-[26px] border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl hover:shadow-blue-950/5"
                            >
                                <div className="flex items-start gap-4">
                                    <div className="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-700 text-xl font-bold text-white shadow-lg shadow-blue-900/15">
                                        {emp.name.charAt(0).toUpperCase()}
                                    </div>
                                    <div className="min-w-0 flex-1">
                                        <div className="truncate text-base font-bold text-slate-900">{emp.name}</div>
                                        <div className="mt-1 flex flex-wrap gap-2">
                                            <span className="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                                {emp.position?.name || "Tanpa jabatan"}
                                            </span>
                                            <span className="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                                {emp.department?.name || "Tanpa departemen"}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div className="mt-5 space-y-2 rounded-2xl bg-slate-50 p-3">
                                    <div className="flex items-center gap-2 text-sm text-slate-600">
                                        <svg className="shrink-0 text-slate-400" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span className="truncate">{emp.email}</span>
                                    </div>
                                    <div className="flex items-center gap-2 text-sm text-slate-600">
                                        <svg className="shrink-0 text-slate-400" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <span className="truncate">{emp.phone_number || "Nomor belum tersedia"}</span>
                                    </div>
                                </div>

                                <div className="mt-4 grid grid-cols-2 gap-2">
                                    <a
                                        href={`mailto:${emp.email}`}
                                        className="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                                    >
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Email
                                    </a>
                                    {emp.phone_number ? (
                                        <a
                                            href={`https://wa.me/${emp.phone_number.replace(/\D/g, "")}`}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
                                        >
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            WhatsApp
                                        </a>
                                    ) : (
                                        <button
                                            type="button"
                                            disabled
                                            className="inline-flex cursor-not-allowed items-center justify-center rounded-2xl bg-slate-100 px-3 py-2.5 text-sm font-semibold text-slate-400"
                                        >
                                            No Phone
                                        </button>
                                    )}
                                </div>
                            </div>
                        ))
                    )}
                </section>
            </div>
        </AuthenticatedLayout>
    );
}
