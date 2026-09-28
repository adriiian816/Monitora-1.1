<nav x-data="{ open: false }" class="bg-blue-950 border-b border-blue-900/80 text-white shadow-md relative z-30">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl 2xl:max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">
            
            <!-- Left: Logo & Navigation Links grouped together -->
            <div class="flex items-center gap-3 lg:gap-5 min-w-0">
                <!-- Logo & Brand -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group focus:outline-none">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-700 to-sky-500 flex items-center justify-center text-white font-black text-base shadow-sm group-hover:scale-105 transition-transform shrink-0">
                            M
                        </div>
                        <div class="leading-tight">
                            <div class="font-extrabold text-sm xl:text-base tracking-wider text-white flex items-center gap-1.5">
                                MONITORA
                                <span class="text-[9px] uppercase font-bold tracking-widest px-1.5 py-0.5 rounded bg-sky-500/20 text-sky-300 border border-sky-400/30">
                                    BAPPEDA
                                </span>
                            </div>
                            <span class="block text-[10px] text-blue-200/80 font-medium">Provinsi Maluku Utara</span>
                        </div>
                    </a>
                </div>

                <!-- Subtle Vertical Divider -->
                <div class="hidden lg:block h-6 w-px bg-blue-800/60 shrink-0"></div>

                <!-- Navigation Links (Left-aligned next to Logo) -->
                <nav class="hidden lg:flex items-center gap-1 xl:gap-1.5">
                    @if(Auth::user()->isAdminOrSuperAdmin())
                        <!-- Dashboard -->
                        <a href="{{ route('admin.dashboard') }}" 
                           class="inline-flex items-center gap-1.5 px-2.5 xl:px-3 py-1.5 rounded-lg text-xs xl:text-[13px] font-medium transition-all duration-150 whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-blue-800/90 text-white font-semibold border border-blue-600/50 shadow-sm' : 'text-blue-200/90 hover:text-white hover:bg-blue-900/60 border border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-sky-300' : 'text-blue-300/70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Dashboard</span>
                        </a>

                        <!-- Tugas Bappeda -->
                        <a href="{{ route('admin.tugas.index') }}" 
                           class="inline-flex items-center gap-1.5 px-2.5 xl:px-3 py-1.5 rounded-lg text-xs xl:text-[13px] font-medium transition-all duration-150 whitespace-nowrap {{ request()->routeIs('admin.tugas.*') ? 'bg-blue-800/90 text-white font-semibold border border-blue-600/50 shadow-sm' : 'text-blue-200/90 hover:text-white hover:bg-blue-900/60 border border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.tugas.*') ? 'text-sky-300' : 'text-blue-300/70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span>Tugas Bappeda</span>
                        </a>

                        @if(Auth::user()->isSuperAdmin())
                            <!-- Data OPD -->
                            <a href="{{ route('admin.opd.index') }}" 
                               class="inline-flex items-center gap-1.5 px-2.5 xl:px-3 py-1.5 rounded-lg text-xs xl:text-[13px] font-medium transition-all duration-150 whitespace-nowrap {{ request()->routeIs('admin.opd.*') ? 'bg-blue-800/90 text-white font-semibold border border-blue-600/50 shadow-sm' : 'text-blue-200/90 hover:text-white hover:bg-blue-900/60 border border-transparent' }}">
                                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.opd.*') ? 'text-sky-300' : 'text-blue-300/70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Data OPD</span>
                            </a>

                            <!-- Kelola Pengguna -->
                            <a href="{{ route('admin.users.index') }}" 
                               class="inline-flex items-center gap-1.5 px-2.5 xl:px-3 py-1.5 rounded-lg text-xs xl:text-[13px] font-medium transition-all duration-150 whitespace-nowrap {{ request()->routeIs('admin.users.*') ? 'bg-blue-800/90 text-white font-semibold border border-blue-600/50 shadow-sm' : 'text-blue-200/90 hover:text-white hover:bg-blue-900/60 border border-transparent' }}">
                                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-sky-300' : 'text-blue-300/70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <span>Kelola Pengguna</span>
                            </a>

                            <!-- Log Sesi -->
                            <a href="{{ route('admin.login-logs.index') }}" 
                               class="inline-flex items-center gap-1.5 px-2.5 xl:px-3 py-1.5 rounded-lg text-xs xl:text-[13px] font-medium transition-all duration-150 whitespace-nowrap {{ request()->routeIs('admin.login-logs.*') ? 'bg-blue-800/90 text-white font-semibold border border-blue-600/50 shadow-sm' : 'text-blue-200/90 hover:text-white hover:bg-blue-900/60 border border-transparent' }}">
                                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.login-logs.*') ? 'text-sky-300' : 'text-blue-300/70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Log Sesi</span>
                            </a>
                        @endif

                        <!-- Log Notifikasi WA -->
                        <a href="{{ route('admin.whatsapp-logs.index') }}" 
                           class="inline-flex items-center gap-1.5 px-2.5 xl:px-3 py-1.5 rounded-lg text-xs xl:text-[13px] font-medium transition-all duration-150 whitespace-nowrap {{ request()->routeIs('admin.whatsapp-logs.*') ? 'bg-blue-800/90 text-white font-semibold border border-blue-600/50 shadow-sm' : 'text-blue-200/90 hover:text-white hover:bg-blue-900/60 border border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.whatsapp-logs.*') ? 'text-sky-300' : 'text-blue-300/70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span>Log Notifikasi WA</span>
                        </a>
                    @else
                        <!-- OPD: Dashboard -->
                        <a href="{{ route('opd.dashboard') }}" 
                           class="inline-flex items-center gap-1.5 px-2.5 xl:px-3 py-1.5 rounded-lg text-xs xl:text-[13px] font-medium transition-all duration-150 whitespace-nowrap {{ request()->routeIs('opd.dashboard') ? 'bg-blue-800/90 text-white font-semibold border border-blue-600/50 shadow-sm' : 'text-blue-200/90 hover:text-white hover:bg-blue-900/60 border border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('opd.dashboard') ? 'text-sky-300' : 'text-blue-300/70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Dashboard</span>
                        </a>

                        <!-- OPD: Tugas Saya -->
                        <a href="{{ route('opd.tugas.index') }}" 
                           class="inline-flex items-center gap-1.5 px-2.5 xl:px-3 py-1.5 rounded-lg text-xs xl:text-[13px] font-medium transition-all duration-150 whitespace-nowrap {{ request()->routeIs('opd.tugas.*') ? 'bg-blue-800/90 text-white font-semibold border border-blue-600/50 shadow-sm' : 'text-blue-200/90 hover:text-white hover:bg-blue-900/60 border border-transparent' }}">
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('opd.tugas.*') ? 'text-sky-300' : 'text-blue-300/70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <span>Tugas Saya</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Right: User Area -->
            <div class="hidden md:flex items-center gap-2 xl:gap-2.5 shrink-0">
                @if(Auth::user()->isSuperAdmin())
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-purple-900/40 border border-purple-600/40 text-purple-200 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                        Admin
                    </span>
                @elseif(Auth::user()->isAdmin())
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-blue-900/60 border border-blue-700/50 text-sky-200 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        PIC
                    </span>
                @elseif(Auth::user()->isOpd() && Auth::user()->opd)
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-950/60 border border-emerald-700/50 text-emerald-200 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        {{ Auth::user()->opd->kode_opd }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-blue-900/60 border border-blue-700/50 text-sky-200 shrink-0">
                        {{ Auth::user()->role_label }}
                    </span>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-1.5 px-2.5 py-1.5 border border-blue-800/80 text-xs xl:text-sm font-medium rounded-lg text-white bg-blue-900/50 hover:bg-blue-800/70 focus:outline-none transition shadow-sm">
                            <span class="truncate max-w-[110px] xl:max-w-[140px]">{{ Auth::user()->name }}</span>
                            <svg class="w-3.5 h-3.5 text-blue-300 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil Akun') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="text-red-600 hover:text-red-700">
                                {{ __('Keluar (Log Out)') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger for Mobile / Tablet (< 1024px) -->
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-blue-200 hover:text-white hover:bg-blue-900 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Mobile Menu (< 1024px) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden bg-blue-950 border-b border-blue-900 px-4 pt-2 pb-4 space-y-1">
        @if(Auth::user()->isAdminOrSuperAdmin())
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-900' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.tugas.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('admin.tugas.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-900' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Tugas Bappeda
            </a>

            @if(Auth::user()->isSuperAdmin())
                <a href="{{ route('admin.opd.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('admin.opd.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Data OPD
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('admin.users.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Kelola Pengguna
                </a>
                <a href="{{ route('admin.login-logs.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('admin.login-logs.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Log Sesi
                </a>
            @endif

            <a href="{{ route('admin.whatsapp-logs.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('admin.whatsapp-logs.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-900' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Log Notifikasi WA
            </a>
        @else
            <a href="{{ route('opd.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('opd.dashboard') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-900' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="{{ route('opd.tugas.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('opd.tugas.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-900' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Tugas Saya
            </a>
        @endif

        <div class="pt-4 pb-2 border-t border-blue-900">
            <div class="px-3">
                <div class="font-bold text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-xs text-blue-200">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-sm text-blue-100 hover:bg-blue-900">Profil Akun</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-sm text-red-300 hover:bg-blue-900">Keluar (Log Out)</button>
                </form>
            </div>
        </div>
    </div>
</nav>
