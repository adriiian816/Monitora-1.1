<section class="space-y-6">
    <header>
        <h2 class="text-base font-bold text-rose-900">
            Hapus Akun
        </h2>

        <p class="mt-1 text-xs text-rose-700/80 leading-relaxed">
            Setelah akun Anda dihapus, semua data dan riwayat terkait akun ini akan dihapus secara permanen. Sebelum menghapus akun Anda, harap unduh berkas atau informasi penting yang ingin Anda simpan.
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm transition"
    >Hapus Akun</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-base font-bold text-slate-900">
                Apakah Anda yakin ingin menghapus akun Anda?
            </h2>

            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                Setelah akun Anda dihapus, semua data dan riwayat akan dihapus secara permanen. Silakan masukkan kata sandi akun Anda untuk mengonfirmasi bahwa Anda benar-benar ingin menghapus akun ini secara permanen.
            </p>

            <div class="mt-5">
                <x-input-label for="password" value="Kata Sandi Akun Anda" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full rounded-xl text-sm"
                    placeholder="Masukkan kata sandi Anda untuk konfirmasi"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                    Batal
                </button>

                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition">
                    Ya, Hapus Akun Ini
                </button>
            </div>
        </form>
    </x-modal>
</section>
