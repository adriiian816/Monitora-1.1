<x-guest-layout>
    <style>
        @keyframes fadeInBlur {
            0% { opacity: 0; backdrop-filter: blur(0px); background-color: rgba(248, 250, 252, 0); }
            100% { opacity: 1; backdrop-filter: blur(8px); background-color: rgba(248, 250, 252, 0.6); }
        }
        @keyframes slideUp {
            0% { transform: translateY(80px) scale(0.95); opacity: 0; }
            100% { transform: translateY(0) scale(1); opacity: 1; }
        }
        .backdrop-anim {
            animation: fadeInBlur 0.5s ease-out forwards;
        }
        .modal-anim {
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0; /* Mulai dalam keadaan tersembunyi */
        }
    </style>

    <!-- Overlay penuh layar yang memberikan efek blur pada background guest layout -->
    <div class="fixed inset-0 z-50 backdrop-anim flex flex-col justify-center items-center px-4 py-12">
        
        <!-- Kontainer Modal dengan animasi muncul dari bawah -->
        <div class="w-full sm:max-w-md modal-anim">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-500 text-white font-black shadow-lg shadow-amber-500/25 mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h1 class="text-xl font-black tracking-tight text-slate-900">Konfirmasi Login</h1>
            </div>

            <div class="bg-white border border-slate-200 shadow-2xl shadow-slate-200/50 rounded-2xl p-6 sm:p-8 text-center">
                <div class="mb-6 p-4 bg-amber-50 rounded-xl border border-amber-200 text-amber-800 text-sm">
                    <p class="font-semibold mb-1">Akun ini sedang aktif di perangkat lain.</p>
                    <p>Hanya satu perangkat yang diizinkan untuk login dalam satu waktu. Apakah Anda ingin mengeluarkan perangkat lain tersebut agar bisa login di sini?</p>
                </div>

                <form method="POST" action="{{ route('login.force') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full mb-3 py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm shadow-md shadow-amber-500/20 active:scale-[0.99] transition">
                        Keluarkan Perangkat Lain & Login
                    </button>
                </form>

                <a href="{{ route('login') }}" 
                   class="block w-full py-3 px-4 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-sm transition">
                    Batal & Gunakan Akun Lain
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
