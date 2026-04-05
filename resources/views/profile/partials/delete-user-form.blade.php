<section>
    <header>
        <h2 style="color:#ef4444;">Hapus Akun</h2>
        <p>Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Sebelum menghapus akun Anda, harap unduh semua data atau informasi yang ingin Anda simpan.</p>
    </header>

    <button class="btn-danger" onclick="document.getElementById('modal-delete').style.display='flex'">Hapus Akun</button>

    <div id="modal-delete" class="modal-overlay">
        <form method="post" action="{{ route('profile.destroy') }}" class="modal-box" onclick="event.stopPropagation()">
            @csrf
            @method('delete')

            <h2 class="modal-title">Apakah Anda yakin ingin menghapus akun Anda?</h2>
            <p class="modal-text">
                Setelah akun Anda dihapus, semua datanya akan dihapus secara permanen. Silakan masukkan kata sandi Anda untuk mengonfirmasi penghapusan.
            </p>

            <div class="form-group">
                <label for="password_delete" class="form-label sr-only" style="display:none;">Kata Sandi</label>
                <input type="password" id="password_delete" name="password" class="form-input" placeholder="Kata Sandi" />
                @error('password', 'userDeletion')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="flex-actions" style="justify-content:flex-end;margin-top:24px;">
                <button type="button" class="btn-cancel" onclick="document.getElementById('modal-delete').style.display='none'">Batal</button>
                <button type="submit" class="btn-danger">Hapus Akun Secara Permanen</button>
            </div>
        </form>
    </div>
</section>

@if ($errors->userDeletion->isNotEmpty())
    <script> document.getElementById('modal-delete').style.display = 'flex'; </script>
@endif
