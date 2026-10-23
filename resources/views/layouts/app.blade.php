<!DOCTYPE html>
@props(['pageTitle' => 'Dashboard'])
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>MONITORA – Bidang Perekonomian Dan Perdagangan</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        

        <!-- Google Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Material Symbols -->
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

        <!-- Vite Assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            ::-webkit-scrollbar { display: none; }
            html {
                background-color: #064e3b;
            }
            @media (min-width: 1024px) {
                body {
                    zoom: 0.9;
                }
            }
        </style>
    </head>
    <body class="bg-[#064e3b] font-sans antialiased text-slate-800 min-h-screen flex flex-row overflow-x-hidden p-2 sm:p-3 md:p-5"
          x-data="{ sidebarOpen: false }">

        <!-- ========== LEFT SIDEBAR ========== -->
        <aside class="w-64 shrink-0 bg-[#064e3b] text-white min-h-screen flex flex-col justify-between py-6 px-4 z-40
                      fixed inset-y-0 left-0 transform transition-transform duration-300 ease-in-out
                      lg:relative lg:translate-x-0
                      -translate-x-full"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <div class="flex flex-col gap-6">

                <!-- Logo & Brand Header -->
                <div class="px-3 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl overflow-hidden shadow-xs flex items-center justify-center shrink-0 border border-white/20 bg-white/10">
                        <img src="{{ asset('logo.png') }}" alt="MONITORA Logo" class="w-full h-full object-cover">
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="text-[18px] font-bold tracking-tight text-white">MONITORA</span>
                            <span class="px-1.5 py-0.5 rounded bg-emerald-400/20 border border-emerald-400/30 text-[9px] font-extrabold tracking-wider uppercase text-emerald-200">Drive</span>
                        </div>
                        <span class="text-[10px] text-emerald-100/80 font-medium">Bidang Perekonomian &amp; Perdagangan</span>
                    </div>
                </div>

                <!-- Primary Action Button -->
                @if(Auth::user()->isAdminOrSuperAdmin())
                    <div class="px-2">
                        <a href="{{ route('admin.tugas.create') }}"
                           class="w-full py-2.5 px-4 bg-white hover:bg-emerald-50 text-[#064e3b] font-bold text-[13px] rounded-full shadow-md flex items-center justify-center gap-2 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            <span class="material-symbols-outlined text-[18px] text-[#059669]">add</span>
                            <span>Buat Penugasan Baru</span>
                        </a>
                    </div>
                @endif

                <!-- Navigation -->
                <nav class="flex flex-col gap-1 px-1">
                    @if(Auth::user()->isAdminOrSuperAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-semibold text-[13px] transition-all
                                  {{ request()->routeIs('admin.dashboard') ? 'bg-white/15 text-white shadow-sm' : 'text-emerald-100/90 hover:text-white hover:bg-white/10 font-medium' }}">
                            <span class="material-symbols-outlined text-[19px] {{ request()->routeIs('admin.dashboard') ? 'text-emerald-300' : 'opacity-80' }}">home</span>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.tugas.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-medium text-[13px] transition-colors
                                  {{ request()->routeIs('admin.tugas.*') ? 'bg-white/15 text-white font-semibold shadow-sm' : 'text-emerald-100/90 hover:text-white hover:bg-white/10' }}">
                            <span class="material-symbols-outlined text-[19px] opacity-80">folder_shared</span>
                            <span>Penugasan OPD</span>
                        </a>
                        <a href="{{ route('admin.penugasan.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-medium text-[13px] transition-colors
                                  {{ request()->routeIs('admin.penugasan.*') ? 'bg-white/15 text-white font-semibold shadow-sm' : 'text-emerald-100/90 hover:text-white hover:bg-white/10' }}">
                            <span class="material-symbols-outlined text-[19px] opacity-80">account_tree</span>
                            <span>Penugasan Bidang</span>
                        </a>
                        @if(Auth::user()->isSuperAdmin())
                            <a href="{{ route('admin.opd.index') }}"
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-medium text-[13px] transition-colors
                                      {{ request()->routeIs('admin.opd.*') ? 'bg-white/15 text-white font-semibold shadow-sm' : 'text-emerald-100/90 hover:text-white hover:bg-white/10' }}">
                                <span class="material-symbols-outlined text-[19px] opacity-80">domain</span>
                                <span>Data OPD</span>
                            </a>
                            <a href="{{ route('admin.users.index') }}"
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-medium text-[13px] transition-colors
                                      {{ request()->routeIs('admin.users.*') ? 'bg-white/15 text-white font-semibold shadow-sm' : 'text-emerald-100/90 hover:text-white hover:bg-white/10' }}">
                                <span class="material-symbols-outlined text-[19px] opacity-80">group</span>
                                <span>Kelola Pengguna</span>
                            </a>
                        @endif

                        <div class="my-2 border-t border-emerald-600/30 mx-2"></div>

                        @if(Auth::user()->isSuperAdmin())
                            <a href="{{ route('admin.login-logs.index') }}"
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-medium text-[13px] transition-colors
                                      {{ request()->routeIs('admin.login-logs.*') ? 'bg-white/15 text-white font-semibold shadow-sm' : 'text-emerald-100/90 hover:text-white hover:bg-white/10' }}">
                                <span class="material-symbols-outlined text-[19px] opacity-80">schedule</span>
                                <span>Log Sesi</span>
                            </a>
                        @endif
                        <a href="{{ route('admin.whatsapp-logs.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-medium text-[13px] transition-colors
                                  {{ request()->routeIs('admin.whatsapp-logs.*') ? 'bg-white/15 text-white font-semibold shadow-sm' : 'text-emerald-100/90 hover:text-white hover:bg-white/10' }}">
                            <span class="material-symbols-outlined text-[19px] opacity-80">chat</span>
                            <span>Log Notifikasi WA</span>
                            <span class="ml-auto w-2 h-2 rounded-full bg-emerald-300"></span>
                        </a>
                    @elseif(Auth::user()->isAnggotaBidang())
                        {{-- Anggota Bidang / Staf Bidang Ekop Nav --}}
                        <a href="{{ route('bidang.dashboard') }}"
                           class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-medium text-[13px] transition-all
                                  {{ request()->routeIs('bidang.dashboard') ? 'bg-white/15 text-white font-semibold shadow-sm' : 'text-emerald-100/90 hover:text-white hover:bg-white/10' }}">
                            <span class="material-symbols-outlined text-[19px] {{ request()->routeIs('bidang.dashboard') ? 'text-emerald-300' : 'opacity-80' }}">home</span>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.penugasan.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-medium text-[13px] transition-colors
                                  {{ request()->routeIs('admin.penugasan.*') ? 'bg-white/15 text-white font-semibold shadow-sm' : 'text-emerald-100/90 hover:text-white hover:bg-white/10' }}">
                            <span class="material-symbols-outlined text-[19px] opacity-80">account_tree</span>
                            <span>Penugasan Bidang</span>
                        </a>
                    @elseif(Auth::user()->isOpd())
                        {{-- OPD User Nav --}}
                        <a href="{{ route('opd.dashboard') }}"
                           class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-medium text-[13px] transition-all
                                  {{ request()->routeIs('opd.dashboard') ? 'bg-white/15 text-white font-semibold shadow-sm' : 'text-emerald-100/90 hover:text-white hover:bg-white/10' }}">
                            <span class="material-symbols-outlined text-[19px] {{ request()->routeIs('opd.dashboard') ? 'text-emerald-300' : 'opacity-80' }}">home</span>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('opd.tugas.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-medium text-[13px] transition-colors
                                  {{ request()->routeIs('opd.tugas.*') ? 'bg-white/15 text-white font-semibold shadow-sm' : 'text-emerald-100/90 hover:text-white hover:bg-white/10' }}">
                            <span class="material-symbols-outlined text-[19px] opacity-80">folder_shared</span>
                            <span>Tugas Saya</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Bottom: System Info Card + User Profile -->
            <div class="px-2 flex flex-col gap-4">
                <!-- Bappeda System & Time Info Card -->
                <div class="p-3.5 rounded-2xl bg-white/10 border border-emerald-500/20 flex flex-col gap-2.5 backdrop-blur-xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-white">
                            <div class="w-6 h-6 rounded-lg bg-emerald-400/20 flex items-center justify-center text-emerald-300">
                                <span class="material-symbols-outlined text-[15px]">verified_user</span>
                            </div>
                            <span class="text-[11px] font-bold tracking-wider text-emerald-100 uppercase">Ekonomi &amp; Perdagangan</span>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-400/20 border border-emerald-400/30 text-[9px] font-extrabold text-emerald-200">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-300"></span>
                            </span>
                            ONLINE
                        </span>
                    </div>
                    
                    <div class="flex flex-col gap-0.5 text-white">
                        <div class="text-[12px] font-bold text-white tracking-tight">Pusat Layanan &amp; Monitoring</div>
                        <div class="text-[10px] text-emerald-200/80 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px] opacity-75">schedule</span>
                            <span>Zona Waktu WIT (UTC+9) • Sofifi</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-emerald-500/20 flex items-center justify-between text-[10px] text-emerald-100/90 font-medium">
                        <span class="flex items-center gap-1 text-emerald-200/90">
                            <span class="material-symbols-outlined text-[12px]">calendar_today</span>
                            T.A. {{ date('Y') }}
                        </span>
                        <span class="text-emerald-300 font-bold bg-white/10 px-2 py-0.5 rounded-md text-[9px]">v1.2.0</span>
                    </div>
                </div>

                <!-- User Profile Bar -->
                <div class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/10 transition-colors cursor-pointer group relative" x-data="{ profileOpen: false }" @click="profileOpen = !profileOpen">
                    <div class="w-8 h-8 rounded-full bg-white text-[#064e3b] font-extrabold flex items-center justify-center text-xs shadow-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="flex flex-col min-w-0 flex-1">
                        <span class="text-xs font-bold truncate leading-tight">{{ Auth::user()->name }}</span>
                        <span class="text-[10px] text-emerald-200/90 truncate leading-tight">{{ Auth::user()->role_label ?? 'Bappeda Malut' }}</span>
                    </div>
                    <span class="material-symbols-outlined text-[16px] text-emerald-200">expand_more</span>

                    <!-- Dropdown Menu -->
                    <div x-show="profileOpen"
                         x-cloak
                         @click.away="profileOpen = false"
                         class="absolute bottom-full left-0 mb-2 w-full bg-white rounded-2xl shadow-xl border border-emerald-100 overflow-hidden z-50 text-slate-700 text-xs">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-3 hover:bg-emerald-50 font-medium transition-colors">
                            <span class="material-symbols-outlined text-[16px] text-emerald-700">manage_accounts</span>
                            Profil Akun
                        </a>
                        <div class="border-t border-emerald-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-3 hover:bg-rose-50 text-rose-600 font-semibold transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">logout</span>
                                    Keluar (Log Out)
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div class="fixed inset-0 bg-black/50 z-30 lg:hidden"
             x-show="sidebarOpen"
             x-cloak
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>

        <!-- ========== RIGHT APP SHELL ========== -->
        <div class="flex-1 flex flex-col min-w-0 bg-white rounded-[32px] md:rounded-[40px] shadow-2xl overflow-hidden ml-0 lg:ml-4 border border-emerald-900/10">

            <!-- Top Header -->
            <header class="h-16 px-4 md:px-8 border-b border-slate-100 flex items-center justify-between gap-4 bg-white shrink-0">
                <!-- Mobile Hamburger -->
                <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden w-9 h-9 rounded-xl hover:bg-emerald-50 text-slate-600 flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined text-[22px]">menu</span>
                </button>

                <!-- Breadcrumb -->
                <div class="flex items-center gap-2 text-[13px] text-slate-500 font-medium">
                    <span class="text-slate-400 hidden sm:inline">MONITORA</span>
                    <span class="text-slate-300 hidden sm:inline">/</span>
                    <span class="font-bold text-[#064e3b]">
                        {{ $pageTitle }}
                    </span>
                </div>

                <!-- Center Search Bar -->
                <div class="flex-1 max-w-xl mx-4 hidden md:block">
                    <div class="relative w-full">
                        <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-[18px] text-slate-400">search</span>
                        <input class="w-full pl-10 pr-4 py-2 text-[12px] bg-[#f0f5f2] hover:bg-[#e6efe9] border border-transparent focus:border-emerald-300 rounded-full text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#059669]/30 transition-all"
                               placeholder="Cari program, kegiatan, OPD, atau berkas..."
                               type="text">
                    </div>
                </div>

                <!-- Right Utility Icons -->
                <div class="flex items-center gap-2 md:gap-3">
                    <a href="{{ route('admin.whatsapp-logs.index') }}"
                       aria-label="Pemberitahuan"
                       class="w-9 h-9 rounded-full hover:bg-emerald-50 text-slate-600 flex items-center justify-center transition-colors relative">
                        <span class="material-symbols-outlined text-[20px]">notifications</span>
                        <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                    </a>
                    <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                        <span class="text-[12px] font-semibold text-slate-700 hidden lg:inline-block">{{ Auth::user()->name }}</span>
                        <div class="w-8 h-8 rounded-full bg-[#064e3b] text-emerald-100 flex items-center justify-center font-bold text-xs shadow-xs">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <div class="px-6 md:px-8 pt-4">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show"
                         class="mb-4 flex items-center justify-between p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 shadow-sm transition">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                            <div class="text-sm font-semibold leading-relaxed">{{ session('success') }}</div>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 text-lg leading-none p-1">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show"
                         class="mb-4 flex items-center justify-between p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm transition">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[18px] text-rose-600">error</span>
                            <div class="text-sm font-medium leading-relaxed">{{ session('error') }}</div>
                        </div>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-700 text-lg leading-none p-1">&times;</button>
                    </div>
                @endif

                @if ($errors->any())
                    <div x-data="{ show: true }" x-show="show"
                         class="mb-4 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 shadow-sm transition">
                        <div class="flex items-center gap-2 font-semibold text-sm mb-1">
                            <span class="material-symbols-outlined text-[16px] text-amber-600">warning</span>
                            <span>Terjadi beberapa kesalahan:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-1 mt-1 text-amber-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Content Slot -->
            <main class="flex-1 overflow-y-auto">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="w-full bg-[#f7faf8] border-t border-emerald-100/80 py-3 px-8 mt-auto flex flex-col md:flex-row items-center justify-between gap-3 text-slate-500 text-[11px] shrink-0">
                <div class="text-left text-slate-500">
                    © {{ date('Y') }} MONITORA • Bidang Perekonomian dan Perdagangan, Bappeda Provinsi Maluku Utara
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <span>Bidang Perekonomian &amp; Perdagangan | Kolaborasi Prodi TI Unkhair</span>
                    <span class="w-1 h-1 rounded-full bg-emerald-200"></span>
                    <span class="font-bold text-[#064e3b]">Versi 3.4.2-WIT</span>
                </div>
            </footer>
        </div>

        <!-- Session Ping (tetap aktif) -->
        <script>
            setInterval(() => {
                fetch('{{ route("ping.session") }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => {
                    if (response.status === 401 || response.status === 419) {
                        window.location.href = '{{ route("login") }}';
                    }
                })
                .catch(err => console.log('Session check failed:', err));
            }, 20000);
        </script>
    </body>
</html>
