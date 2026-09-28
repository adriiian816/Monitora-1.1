<x-guest-layout>
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-700 to-sky-500 text-white font-black text-2xl shadow-lg shadow-blue-600/25 mb-3">
            M
        </div>
        <h1 class="text-2xl font-black tracking-tight text-slate-900">MONITORA</h1>
        <p class="text-xs font-bold text-blue-800 tracking-wider uppercase mt-0.5">BAPPEDA Provinsi Maluku Utara</p>
        <p class="text-xs text-slate-500 mt-1">Sistem Monitoring & Pengingat Pengumpulan Dokumen Tugas OPD</p>
    </div>

    <div class="bg-white border border-slate-200 shadow-xl rounded-2xl p-6 sm:p-8">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Alamat Email
                </label>
                <input id="email" 
                       class="block w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition text-sm font-medium" 
                       type="email" 
                       name="email" 
                       :value="old('email')" 
                       required 
                       autofocus 
                       placeholder="nama@malutprov.go.id" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-600 font-semibold" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Kata Sandi
                    </label>
                </div>
                <input id="password" 
                       class="block w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition text-sm font-medium"
                       type="password"
                       name="password"
                       required 
                       autocomplete="current-password" 
                       placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-600 font-semibold" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between text-xs pt-1">
                <label for="remember_me" class="inline-flex items-center text-slate-600 hover:text-slate-900 cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded bg-white border-slate-300 text-blue-600 focus:ring-blue-500" name="remember">
                    <span class="ms-2 font-medium">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <button type="submit" 
                    class="w-full py-3 px-4 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm shadow-md shadow-blue-700/20 active:scale-[0.99] transition">
                Masuk ke Portal
            </button>
        </form>

        <!-- Quick Fill Demo Accounts -->
        <div class="mt-6 pt-5 border-t border-slate-200">
            <p class="text-[11px] font-bold tracking-wider uppercase text-slate-500 mb-2.5 text-center">
                Pilihan Akun Uji Coba Cepat (1-Klik Masuk):
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <!-- Admin -->
                <button type="button" 
                        onclick="fillAndLogin('superadmin@bappeda.malutprov.go.id', 'password')"
                        class="p-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 border border-purple-200 text-xs text-left transition flex flex-col group">
                    <span class="font-bold text-purple-900 flex items-center gap-1">
                        ⚡ Admin
                    </span>
                    <span class="text-[10px] text-purple-700 truncate font-mono mt-0.5">superadmin@bappeda...</span>
                </button>

                <!-- PIC -->
                <button type="button" 
                        onclick="fillAndLogin('admin@bappeda.malutprov.go.id', 'password')"
                        class="p-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 border border-blue-200 text-xs text-left transition flex flex-col group">
                    <span class="font-bold text-blue-900 flex items-center gap-1">
                        👤 PIC
                    </span>
                    <span class="text-[10px] text-blue-700 truncate font-mono mt-0.5">admin@bappeda...</span>
                </button>
            </div>
            <p class="text-[11px] text-slate-500 text-center mt-2.5">
                Klik salah satu akun di atas untuk login instan. Kata sandi default: <code class="px-1.5 py-0.5 rounded bg-slate-100 font-mono text-slate-700 font-bold">password</code>
            </p>
        </div>
    </div>

    <script>
        function fillCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }

        function fillAndLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            document.getElementById('login-form').submit();
        }
    </script>
</x-guest-layout>
