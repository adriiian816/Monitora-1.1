<x-guest-layout>
    <div class="bg-[#0D221B]/95 backdrop-blur-2xl rounded-3xl border border-emerald-500/25 shadow-[0_25px_60px_rgba(0,0,0,0.6)] p-7 sm:p-10 relative overflow-hidden text-white">

        {{-- Header Branding --}}
        <div class="text-center mb-6">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-400/30 flex items-center justify-center p-2 shadow-[0_0_25px_rgba(74,222,128,0.2)] mb-4">
                <img alt="MONITORA Logo" class="w-full h-full object-contain rounded-xl" src="{{ asset('logo.png') }}">
            </div>
            <div class="inline-flex items-center gap-2 mb-2 px-3 py-1 rounded-full bg-emerald-500/15 border border-emerald-500/30 font-mono">
                <span class="text-[10.5px] font-extrabold uppercase tracking-widest text-[#4ADE80]">MONITORA DRIVE</span>
                <span class="w-1 h-1 rounded-full bg-emerald-400"></span>
                <span class="text-[10.5px] font-medium text-emerald-200">Bidang Perekonomian &amp; Perdagangan</span>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-white">Masuk ke Portal MONITORA</h1>
            <p class="text-xs text-[#A3B8AD] mt-1.5 leading-relaxed">Bidang Perekonomian dan Perdagangan — Bappeda Provinsi Maluku Utara</p>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status class="mb-4" :status="session('status')" />

        {{-- Login Form --}}
        <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            {{-- Email --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-[#A3B8AD] mb-1.5" for="email">
                    Alamat Email Resmi / NIP ASN
                </label>
                <div class="relative rounded-2xl shadow-xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#4ADE80]">
                        <span class="material-symbols-outlined text-[18px]">alternate_email</span>
                    </div>
                    <input class="zen-dark-input block w-full pl-10 pr-3.5 py-3 text-sm rounded-2xl transition-all" style="background-color: #061510 !important; color: #FFFFFF !important; border: 1px solid rgba(255, 255, 255, 0.15) !important;" id="email"
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
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#A3B8AD]" for="password">
                        Kata Sandi Akun
                    </label>
                </div>
                <div class="relative rounded-2xl shadow-xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#4ADE80]">
                        <span class="material-symbols-outlined text-[18px]">lock</span>
                    </div>
                    <input class="zen-dark-input block w-full pl-10 pr-10 py-3 text-sm rounded-2xl transition-all" style="background-color: #061510 !important; color: #FFFFFF !important; border: 1px solid rgba(255, 255, 255, 0.15) !important;" id="password"
                           name="password"
                           type="password"
                           required
                           autocomplete="current-password"
                           placeholder="••••••••••••">
                    <button aria-label="Tampilkan sandi"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-[#4ADE80] focus:outline-none"
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
                    <span class="text-[#A3B8AD] font-medium">Ingat saya di perangkat ini</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="font-semibold text-[#4ADE80] hover:text-emerald-300 transition hover:underline" href="{{ route('password.request') }}">
                        Lupa sandi?
                    </a>
                @endif
            </div>

            {{-- Submit Button --}}
            <div class="pt-2">
                <button class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-extrabold text-sm text-[#091A14] bg-[#4ADE80] hover:bg-[#38c86d] shadow-[0_0_25px_rgba(74,222,128,0.3)] hover:shadow-[0_0_35px_rgba(74,222,128,0.5)] transition-all hover:scale-[1.01] active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4ADE80] cursor-pointer"
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

<style>
    /* Autofill & Input Contrast Fix (Brave, Chrome, Edge) */
    .zen-dark-input {
        background-color: #061510 !important;
        color: #FFFFFF !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
    }
    .zen-dark-input:focus {
        background-color: #081E15 !important;
        color: #FFFFFF !important;
        border-color: #4ADE80 !important;
        box-shadow: 0 0 0 2px rgba(74, 222, 128, 0.3) !important;
        outline: none !important;
    }
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus, 
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 1000px #061510 inset !important;
        -webkit-text-fill-color: #FFFFFF !important;
        caret-color: #4ADE80 !important;
        transition: background-color 5000s ease-in-out 0s !important;
    }
    input::placeholder {
        color: #64748B !important;
        opacity: 0.9 !important;
    }
</style>

</x-guest-layout>
