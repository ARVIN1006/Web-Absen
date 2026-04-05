<section>
    <header>
        <h2>Informasi Pribadi</h2>
        <p>Perbarui profil dan alamat email akun Anda.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="form-group">
            <label class="form-label" for="name">Nama Lengkap</label>
            <input class="form-input" id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @error('name')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="position">Jabatan</label>
            <input class="form-input" id="position" name="position" type="text" value="{{ old('position', $user->position) }}" required autocomplete="organization-title" />
            @error('position')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input class="form-input" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @error('email')<div class="form-error">{{ $message }}</div>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div style="margin-top:8px;">
                    <p style="font-size:12px;color:var(--text-muted-dark);">
                        Email Anda belum terverifikasi.
                        <button form="send-verification" style="background:none;border:none;color:#3b82f6;text-decoration:underline;cursor:pointer;font-size:12px;padding:0;">
                            Kirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p style="font-size:12px;color:#10b981;margin-top:4px;font-weight:500;">
                            Link verifikasi baru telah dikirim ke email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex-actions">
            <button type="submit" class="btn-save">Simpan Perubahan</button>

            @if (session('status') === 'profile-updated')
                <div class="form-status" id="status-update">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Tersimpan
                </div>
                <script>setTimeout(() => document.getElementById('status-update').style.display = 'none', 3000);</script>
            @endif
        </div>
    </form>
</section>
