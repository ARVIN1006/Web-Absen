import { Head, useForm } from "@inertiajs/react";

export default function ConfirmPassword() {
    const { data, setData, post, processing, errors, reset } = useForm({
        password: "",
    });

    const submit = (event) => {
        event.preventDefault();
        post(route("password.confirm"), {
            onFinish: () => reset("password"),
        });
    };

    return (
        <>
            <Head title="Konfirmasi Password" />

            <main className="flex min-h-screen items-center justify-center bg-slate-950 px-4 py-10 text-white">
                <section className="w-full max-w-md rounded-xl border border-white/10 bg-white/5 p-6 shadow-2xl">
                    <div className="text-xs font-semibold uppercase tracking-[0.18em] text-blue-200">Security Check</div>
                    <h1 className="mt-3 font-heading text-2xl font-bold">Konfirmasi password</h1>
                    <p className="mt-3 text-sm leading-6 text-slate-300">
                        Masukkan password akun untuk melanjutkan ke area yang dilindungi.
                    </p>

                    <form onSubmit={submit} className="mt-6 space-y-4">
                        <div>
                            <input
                                type="password"
                                value={data.password}
                                onChange={(event) => setData("password", event.target.value)}
                                className="w-full rounded-md border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/30 focus:border-blue-400"
                                placeholder="Password"
                                autoFocus
                            />
                            {errors.password && <p className="mt-2 text-sm text-red-300">{errors.password}</p>}
                        </div>
                        <button type="submit" disabled={processing} className="w-full rounded-md bg-blue-500 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-600 disabled:opacity-60">
                            {processing ? "Memeriksa..." : "Konfirmasi"}
                        </button>
                    </form>
                </section>
            </main>
        </>
    );
}
