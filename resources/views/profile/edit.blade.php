<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pengaturan Profil Akun</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola informasi identitas akun, perbarui kata sandi, dan atur keamanan akses sistem</p>
        </div>

        <div class="space-y-6">
            <!-- Informasi Profil -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Perbarui Kata Sandi -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Hapus Akun -->
            <div class="bg-white rounded-2xl border border-rose-200 shadow-sm p-6 sm:p-8 bg-rose-50/20">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

