<section>
    <header>
        <h2 class="text-base font-bold text-slate-900">
            Informasi Profil Akun
        </h2>

        <p class="mt-1 text-xs text-slate-500">
            Perbarui informasi identitas akun, nama lengkap pengguna, dan alamat email login Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl text-sm" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Alamat Email Login" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl text-sm" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
                    <p class="font-medium">
                        Alamat email Anda belum terverifikasi.

                        <button form="send-verification" class="underline text-amber-800 hover:text-amber-950 font-bold ml-1">
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1.5 font-bold text-emerald-700">
                            Tautan verifikasi baru telah dikirim ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="no_hp" value="Nomor WhatsApp / HP Aktif" />
            <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full font-mono rounded-xl text-sm" :value="old('no_hp', $user->no_hp)" placeholder="Contoh: 081234567890" />
            <p class="mt-1.5 text-xs text-slate-500">Nomor ini digunakan untuk menerima notifikasi penugasan baru kegiatan dan pengingat via WhatsApp.</p>
            <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs shadow-sm transition">
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs font-semibold text-emerald-700 flex items-center gap-1.5 bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-200"
                >
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Perubahan berhasil disimpan.
                </p>
            @endif
        </div>
    </form>
</section>
