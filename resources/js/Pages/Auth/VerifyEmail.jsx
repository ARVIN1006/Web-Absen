import { Head, Link, useForm } from "@inertiajs/react";

export default function VerifyEmail({ status }) {
    const { post, processing } = useForm();

    const submit = (event) => {
        event.preventDefault();
        post(route("verification.send"));
    };

    return (
        <>
            <Head title="Verifikasi Email" />

            <main className="flex min-h-screen items-center justify-center bg-slate-950 px-4 py-10 text-white">
                <section className="w-full max-w-md rounded-xl border border-white/10 bg-white/5 p-6 shadow-2xl">
                    <div className="text-xs font-semibold uppercase tracking-[0.18em] text-blue-200">Account Verification</div>
                    <h1 className="mt-3 font-heading text-2xl font-bold">Verifikasi email Anda</h1>
                    <p className="mt-3 text-sm leading-6 text-slate-300">
                        Link verifikasi sudah dikirim ke email akun. Jika belum masuk, kirim ulang link verifikasi dari tombol di bawah.
                    </p>

                    {status === "verification-link-sent" && (
                        <div className="mt-5 rounded-lg border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                            Link verifikasi baru sudah dikirim.
                        </div>
                    )}

                    <form onSubmit={submit} className="mt-6 space-y-3">
                        <button type="submit" disabled={processing} className="w-full rounded-md bg-blue-500 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-600 disabled:opacity-60">
                            {processing ? "Mengirim..." : "Kirim Ulang Link"}
                        </button>
                        <Link href={route("logout")} method="post" as="button" className="w-full rounded-md border border-white/15 px-4 py-3 text-sm font-semibold text-slate-200 transition hover:bg-white/10">
                            Keluar
                        </Link>
                    </form>
                </section>
            </main>
        </>
    );
}
