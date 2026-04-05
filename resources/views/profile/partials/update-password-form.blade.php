<section>
    <header>
        <h2>Perbarui Kata Sandi</h2>
        <p>Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="form-group">
            <label class="form-label" for="current_password">Kata Sandi Saat Ini</label>
            <input class="form-input" id="current_password" name="current_password" type="password" autocomplete="current-password" />
            @error('current_password', 'updatePassword')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Kata Sandi Baru</label>
            <input class="form-input" id="password" name="password" type="password" autocomplete="new-password" />
            @error('password', 'updatePassword')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
            <input class="form-input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
            @error('password_confirmation', 'updatePassword')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="flex-actions">
            <button type="submit" class="btn-save">Ubah Sandi</button>

            @if (session('status') === 'password-updated')
                <div class="form-status" id="status-password">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Tersimpan
                </div>
                <script>setTimeout(() => document.getElementById('status-password').style.display = 'none', 3000);</script>
            @endif
        </div>
    </form>
</section>
