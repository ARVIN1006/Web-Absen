import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function Locations({ locations, branches, flash }) {
    const [showModal, setShowModal] = useState(false);
    const [editingLocation, setEditingLocation] = useState(null);
    const { data, setData, post, put, delete: destroy, processing, errors, reset } = useForm({
        branch_id: "",
        name: "",
        code: "",
        latitude: "",
        longitude: "",
        radius: 100,
        address: "",
        is_active: true,
        enforce_face_verification: true,
    });

    const openCreate = () => {
        setEditingLocation(null);
        reset();
        setData("radius", 100);
        setData("is_active", true);
        setData("enforce_face_verification", true);
        setShowModal(true);
    };

    const openEdit = (location) => {
        setEditingLocation(location);
        setData({
            branch_id: location.branch_id || "",
            name: location.name || "",
            code: location.code || "",
            latitude: location.latitude || "",
            longitude: location.longitude || "",
            radius: location.radius || 100,
            address: location.address || "",
            is_active: location.is_active,
            enforce_face_verification: location.enforce_face_verification ?? true,
        });
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setEditingLocation(null);
        reset();
    };

    const submit = (event) => {
        event.preventDefault();
        if (editingLocation) {
            put(route("admin.locations.update", editingLocation.id), { onSuccess: closeModal });
            return;
        }
        post(route("admin.locations.store"), { onSuccess: closeModal });
    };

    const handleDelete = (id) => {
        if (confirm("Hapus lokasi ini?")) {
            destroy(route("admin.locations.destroy", id));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Master Lokasi" />

            <section className="ui-card overflow-hidden">
                <div className="flex flex-col gap-5 p-6 lg:flex-row lg:items-end lg:justify-between lg:p-8">
                    <div>
                        <div className="ui-section-title">Location Master</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">Lokasi dan geofence absensi</h1>
                        <p className="mt-3 max-w-2xl text-sm leading-6 text-[var(--text-muted)]">Master lokasi kini memakai tabel HRIS baru sehingga setiap titik absensi bisa dikaitkan ke cabang.</p>
                    </div>
                    <button type="button" onClick={openCreate} className="ui-button-primary">Tambah Lokasi</button>
                </div>
            </section>

            {flash?.success && (
                <div className="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {flash.success}
                </div>
            )}

            <section className="ui-card mt-6 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-[var(--border-line)] text-sm">
                        <thead className="bg-[var(--bg-subtle)] text-left text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--text-soft)]">
                            <tr>
                                <th className="px-6 py-4">Lokasi</th>
                                <th className="px-6 py-4">Cabang</th>
                                <th className="px-6 py-4">Koordinat</th>
                                <th className="px-6 py-4">Radius</th>
                                <th className="px-6 py-4">Verifikasi</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[var(--border-line)]">
                            {locations.map((location) => (
                                <tr key={location.id}>
                                    <td className="px-6 py-4">
                                        <div className="font-semibold text-[var(--text-main)]">{location.name}</div>
                                        <div className="mt-1 text-xs text-[var(--text-muted)]">{location.code} • {location.address || "Tanpa alamat"}</div>
                                    </td>
                                    <td className="px-6 py-4 text-[var(--text-muted)]">{location.branch?.name || "-"}</td>
                                    <td className="px-6 py-4 font-data text-[var(--text-main)]">{location.latitude}, {location.longitude}</td>
                                    <td className="px-6 py-4">
                                        <span className="ui-badge bg-indigo-50 text-[var(--primary-color)]">{location.radius} m</span>
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className={`ui-badge ${location.enforce_face_verification ? "bg-emerald-50 text-[var(--success-color)]" : "bg-amber-50 text-[var(--warning-color)]"}`}>
                                            {location.enforce_face_verification ? "Wajah wajib" : "GPS only"}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex justify-end gap-2">
                                            <button type="button" onClick={() => openEdit(location)} className="ui-button-secondary px-3 py-2">Edit</button>
                                            <button type="button" onClick={() => handleDelete(location.id)} className="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </section>

            {showModal && (
                <div className="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                    <div className="w-full max-w-2xl rounded-xl bg-white shadow-[var(--shadow-pop)]">
                        <div className="flex items-center justify-between border-b border-[var(--border-line)] px-6 py-4">
                            <h2 className="font-heading text-xl font-semibold">{editingLocation ? "Edit Lokasi" : "Tambah Lokasi"}</h2>
                            <button type="button" onClick={closeModal} className="text-sm text-[var(--text-muted)]">Tutup</button>
                        </div>
                        <form onSubmit={submit} className="grid gap-5 p-6 md:grid-cols-2">
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Cabang</label>
                                <select value={data.branch_id} onChange={(event) => setData("branch_id", event.target.value)} className="ui-input">
                                    <option value="">Pilih cabang</option>
                                    {branches.map((branch) => <option key={branch.id} value={branch.id}>{branch.name}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Nama lokasi</label>
                                <input value={data.name} onChange={(event) => setData("name", event.target.value)} className="ui-input" />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Kode lokasi</label>
                                <input value={data.code} onChange={(event) => setData("code", event.target.value)} className="ui-input" />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Radius</label>
                                <input type="number" value={data.radius} onChange={(event) => setData("radius", event.target.value)} className="ui-input" />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Latitude</label>
                                <input value={data.latitude} onChange={(event) => setData("latitude", event.target.value)} className="ui-input" />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Longitude</label>
                                <input value={data.longitude} onChange={(event) => setData("longitude", event.target.value)} className="ui-input" />
                            </div>
                            <div className="md:col-span-2">
                                <label className="mb-2 block text-sm font-semibold">Alamat</label>
                                <textarea rows="3" value={data.address} onChange={(event) => setData("address", event.target.value)} className="ui-input" />
                            </div>
                            <label className="md:col-span-2 flex items-center gap-3 rounded-lg border border-[var(--border-line)] bg-[var(--bg-subtle)] px-4 py-3 text-sm font-medium">
                                <input type="checkbox" checked={data.is_active} onChange={(event) => setData("is_active", event.target.checked)} />
                                Lokasi aktif
                            </label>
                            <label className="md:col-span-2 flex items-center gap-3 rounded-lg border border-[var(--border-line)] bg-[var(--bg-subtle)] px-4 py-3 text-sm font-medium">
                                <input
                                    type="checkbox"
                                    checked={data.enforce_face_verification}
                                    onChange={(event) => setData("enforce_face_verification", event.target.checked)}
                                />
                                Wajibkan verifikasi wajah untuk absensi di lokasi ini
                            </label>
                            <div className="md:col-span-2 flex gap-3 pt-2">
                                <button type="submit" disabled={processing} className="ui-button-primary">{processing ? "Menyimpan..." : editingLocation ? "Update Lokasi" : "Simpan Lokasi"}</button>
                                <button type="button" onClick={closeModal} className="ui-button-secondary">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
