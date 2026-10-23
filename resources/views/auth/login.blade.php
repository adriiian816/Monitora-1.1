<x-guest-layout>
    <div class="bg-white rounded-3xl border border-emerald-100 shadow-2xl p-7 sm:p-10 relative overflow-hidden">

        {{-- Header Branding --}}
        <div class="text-center mb-6">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-[#064e3b] border border-emerald-400/30 flex items-center justify-center p-2 shadow-lg mb-4">
                <img alt="MONITORA Logo" class="w-full h-full object-contain rounded-xl" src="{{ asset('logo.png') }}">
            </div>
            <div class="inline-flex items-center gap-2 mb-2 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200/80">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#064e3b]">MONITORA DRIVE</span>
                <span class="w-1 h-1 rounded-full bg-emerald-400"></span>
                <span class="text-[11px] font-medium text-emerald-800">Bidang Perekonomian &amp; Perdagangan</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Masuk ke Portal MONITORA</h1>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Bidang Perekonomian dan Perdagangan — Bappeda Provinsi Maluku Utara</p>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status class="mb-4" :status="session('status')" />

        {{-- Login Form --}}
        <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            {{-- Email --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5" for="email">
                    Alamat Email Resmi / NIP ASN
                </label>
                <div class="relative rounded-2xl shadow-xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-700">
                        <span class="material-symbols-outlined text-[18px]">alternate_email</span>
                    </div>
                    <input class="block w-full pl-10 pr-3.5 py-3 text-sm text-slate-800 bg-[#f0f5f2] border border-emerald-100 rounded-2xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#059669]/40 focus:border-[#059669] transition-all @error('email') border-rose-400 bg-rose-50 @enderror"
                           id="email"
                           name="email"
                           type="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           placeholder="nama@malutprov.go.id">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-600 font-semibold" />
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700" for="password">
                        Kata Sandi Akun
                    </label>
                </div>
                <div class="relative rounded-2xl shadow-xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-700">
                        <span class="material-symbols-outlined text-[18px]">lock</span>
                    </div>
                    <input class="block w-full pl-10 pr-10 py-3 text-sm text-slate-800 bg-[#f0f5f2] border border-emerald-100 rounded-2xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#059669]/40 focus:border-[#059669] transition-all @error('password') border-rose-400 bg-rose-50 @enderror"
                           id="password"
                           name="password"
                           type="password"
                           required
                           autocomplete="current-password"
                           placeholder="••••••••••••">
                    <button aria-label="Tampilkan sandi"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-[#059669] focus:outline-none"
                            id="toggle-password-btn"
                            type="button">
                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-600 font-semibold" />
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox"
                           id="remember_me"
                           name="remember"
                           class="w-4 h-4 rounded text-[#059669] border-slate-300 focus:ring-[#059669] transition">
                    <span class="text-slate-600 font-medium">Ingat saya di perangkat ini</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="font-semibold text-[#059669] hover:text-[#064e3b] transition hover:underline" href="{{ route('password.request') }}">
                        Lupa sandi?
                    </a>
                @endif
            </div>

            {{-- Submit Button --}}
            <div class="pt-2">
                <button class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-full font-bold text-sm text-white bg-[#059669] hover:bg-[#047857] active:bg-[#064e3b] shadow-lg shadow-emerald-800/25 transition-all hover:scale-[1.01] active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#059669] cursor-pointer"
                        type="submit">
                    <span>Masuk ke Portal</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </button>
            </div>
        </form>
    </div>

    {{-- SSL Trust Badge --}}
    <div class="mt-5 text-center text-xs text-emerald-200/90 flex items-center justify-center gap-1.5">
        <span class="material-symbols-outlined text-[16px] text-emerald-300">verified_user</span>
        <span>Koneksi Aman SSL 256-bit • Bidang Perekonomian &amp; Perdagangan Bappeda Sofifi</span>
    </div>

    <script>

        const togglePasswordBtn = document.getElementById('toggle-password-btn');
        const passwordInput = document.getElementById('password');
        if (togglePasswordBtn && passwordInput) {
            togglePasswordBtn.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                const icon = togglePasswordBtn.querySelector('.material-symbols-outlined');
                if (icon) icon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
            });
        }
    </script>
</x-guest-layout>
