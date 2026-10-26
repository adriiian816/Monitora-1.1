<!DOCTYPE html>
@props(['pageTitle' => 'Dashboard'])
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>MONITORA – Bidang Perekonomian Dan Perdagangan</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Google Fonts: Inter, Plus Jakarta Sans, JetBrains Mono -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Material Symbols -->
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

        <!-- Vite Assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --zen-dark: #091A14;
                --zen-dark-surface: #0D221B;
                --zen-mint: #4ADE80;
                --zen-mint-hover: #38c86d;
                --zen-muted: #A3B8AD;
                --emerald-primary: #059669;
                --emerald-hover: #047857;
                --app-canvas: #F4F7F5;
                --card-bg: #FFFFFF;
                --card-border: #E2E8F0;
                --text-heading: #0F172A;
                --text-body: #334155;
                --text-muted: #64748B;
            }

            html, body {
                background-color: var(--zen-dark);
                color: var(--text-heading);
                font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
            }

            .font-mono-tag {
                font-family: 'JetBrains Mono', monospace;
            }

            /* Custom slim scrollbars */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            ::-webkit-scrollbar-track {
                background: #F1F5F2;
            }
            ::-webkit-scrollbar-thumb {
                background: #CBD5E1;
                border-radius: 9999px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #94A3B8;
            }

            /* ========================================================
               SIDEBAR: Deep Zen Emerald Theme (Matching welcome & login)
               ======================================================== */
            .zen-sidebar-theme {
                background-color: #091A14 !important;
                border-color: rgba(255, 255, 255, 0.08) !important;
                color: #FFFFFF !important;
            }

            .zen-sidebar-theme .brand-badge-drive {
                background-color: rgba(74, 222, 128, 0.15) !important;
                color: #4ADE80 !important;
                border: 1px solid rgba(74, 222, 128, 0.35) !important;
            }

            .zen-sidebar-theme .btn-mint-cta {
                background-color: #4ADE80 !important;
                color: #091A14 !important;
                font-weight: 800 !important;
                box-shadow: 0 0 20px rgba(74, 222, 128, 0.25) !important;
                transition: all 0.2s ease !important;
            }
            .zen-sidebar-theme .btn-mint-cta:hover {
                background-color: #38c86d !important;
                box-shadow: 0 0 25px rgba(74, 222, 128, 0.45) !important;
                transform: translateY(-1px) !important;
            }

            .zen-sidebar-theme .nav-group-title {
                color: #A3B8AD !important;
                font-family: 'JetBrains Mono', monospace !important;
                font-size: 9.5px !important;
                font-weight: 700 !important;
                letter-spacing: 0.12em !important;
                text-transform: uppercase !important;
            }

            .zen-sidebar-theme .nav-link-active {
                background-color: rgba(74, 222, 128, 0.15) !important;
                color: #4ADE80 !important;
                border: 1px solid rgba(74, 222, 128, 0.35) !important;
                font-weight: 700 !important;
            }
            .zen-sidebar-theme .nav-link-active .material-symbols-outlined {
                color: #4ADE80 !important;
            }

            .zen-sidebar-theme .nav-link-idle {
                color: #A3B8AD !important;
                border: 1px solid transparent !important;
                transition: all 0.15s ease !important;
            }
            .zen-sidebar-theme .nav-link-idle:hover {
                color: #FFFFFF !important;
                background-color: rgba(255, 255, 255, 0.06) !important;
                border-color: rgba(255, 255, 255, 0.1) !important;
            }
            .zen-sidebar-theme .nav-link-idle .material-symbols-outlined {
                color: #7A9A8C !important;
                transition: color 0.15s ease !important;
            }
            .zen-sidebar-theme .nav-link-idle:hover .material-symbols-outlined {
                color: #4ADE80 !important;
            }

            .zen-sidebar-theme .zen-status-card {
                background-color: #0D221B !important;
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
            }

            .zen-sidebar-theme .zen-user-bar {
                background-color: rgba(255, 255, 255, 0.04) !important;
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                color: #FFFFFF !important;
                transition: background-color 0.15s ease !important;
            }
            .zen-sidebar-theme .zen-user-bar:hover {
                background-color: rgba(255, 255, 255, 0.09) !important;
            }

            /* ========================================================
               MAIN APP CANVAS & CARDS (Clean, Legible, High Density)
               ======================================================== */
            main {
                background-color: var(--app-canvas) !important;
                color: var(--text-body) !important;
            }

            /* Top control bar / subheader */
            .top-control-bar,
            main > div:first-child[class*="bg-[#f7faf8]"],
            main > div:first-child[class*="border-b"] {
                background: linear-gradient(135deg, #FFFFFF 0%, #F0FDF4 100%) !important;
                border-bottom: 1px solid #D1FAE5 !important;
            }

            /* Headings in control bar */
            main h1[class*="text-[#064e3b]"],
            main h1[class*="text-slate-900"],
            main h1[class*="text-slate-800"] {
                color: var(--text-heading) !important;
            }
            main p[class*="text-slate-500"],
            main span[class*="text-slate-500"] {
                color: var(--text-muted) !important;
            }

            /* White content cards */
            main .bg-white:not(button):not(input):not(select):not(.dropdown-item) {
                background-color: #FFFFFF !important;
                border: 1px solid var(--card-border) !important;
                color: var(--text-body) !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02) !important;
                border-radius: 16px !important;
            }
            main .bg-white:not(button):not(input):not(select):hover {
                border-color: #CBD5E1 !important;
            }

            /* Card headings */
            main .text-slate-800,
            main .text-slate-900,
            main .text-[#064e3b] {
                color: var(--text-heading) !important;
            }
            main .text-slate-400,
            main .text-slate-500,
            main .text-slate-600 {
                color: var(--text-muted) !important;
            }

            /* Card inner borders */
            main .border-slate-100,
            main .border-slate-200,
            main .border-emerald-100,
            main .border-emerald-100\/70,
            main .border-emerald-100\/80 {
                border-color: var(--card-border) !important;
            }

            /* Soft backgrounds inside cards */
            main .bg-slate-50,
            main .bg-emerald-50\/50,
            main .bg-[#f0f5f2],
            main .bg-[#f8faf9],
            main .bg-[#f7faf8] {
                background-color: #F8FAFC !important;
            }

            /* Table row hover: delicate mint glow */
            main tr:hover,
            main .hover\:bg-slate-50:hover,
            main .hover\:bg-emerald-50\/50:hover,
            main .hover\:bg-\[\#f0f8f4\]:hover {
                background-color: #F0FDF4 !important;
            }

            /* Table Headers */
            main thead tr,
            main th {
                background-color: #F8FAFC !important;
                color: #475569 !important;
                border-color: var(--card-border) !important;
                font-weight: 700 !important;
            }

            /* Form inputs inside main */
            main input[type="text"],
            main input[type="search"],
            main input[type="date"],
            main input[type="datetime-local"],
            main select,
            main textarea {
                background-color: #FFFFFF !important;
                color: var(--text-heading) !important;
                border: 1px solid var(--card-border) !important;
            }
            main input:focus,
            main select:focus,
            main textarea:focus {
                border-color: var(--emerald-primary) !important;
                box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.2) !important;
            }

            /* Action Buttons Explicit Styling */
            .btn-action-emerald {
                background-color: #059669 !important;
                color: #FFFFFF !important;
                font-weight: 700 !important;
                border-radius: 9999px !important;
                transition: all 0.15s ease !important;
            }
            .btn-action-emerald:hover {
                background-color: #047857 !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.25) !important;
            }

            /* Badges & Pills in Light Mode - High Contrast, Harmonious Pastel */
            main .bg-emerald-50 {
                background-color: #ECFDF5 !important;
                color: #047857 !important;
                border: 1px solid #A7F3D0 !important;
            }
            main .bg-teal-50 {
                background-color: #F0FDFA !important;
                color: #0F766E !important;
                border: 1px solid #99F6E4 !important;
            }
            main .bg-amber-50 {
                background-color: #FFFBEB !important;
                color: #B45309 !important;
                border: 1px solid #FDE68A !important;
            }
            main .bg-rose-50 {
                background-color: #FFF1F2 !important;
                color: #BE123C !important;
                border: 1px solid #FECDD3 !important;
            }
            main .bg-indigo-50 {
                background-color: #EEF2FF !important;
                color: #4338CA !important;
                border: 1px solid #C7D2FE !important;
            }
        </style>
    </head>
    <body class="bg-[#091A14] font-sans antialiased text-[#0F172A] min-h-screen lg:h-screen lg:overflow-hidden flex flex-row p-2 sm:p-3 lg:p-4 selection:bg-[#4ADE80] selection:text-[#091A14]"
          x-data="{ sidebarOpen: false }">

        <!-- ========== LEFT SIDEBAR (Deep Zen Emerald matching welcome & login) ========== -->
        <aside class="zen-sidebar-theme w-64 xl:w-68 shrink-0 border shadow-2xl rounded-2xl lg:rounded-3xl flex flex-col justify-between py-4 px-3.5 z-40
                      fixed inset-y-0 left-0 transform transition-transform duration-300 ease-in-out
                      lg:relative lg:translate-x-0 lg:h-full lg:overflow-hidden
                      -translate-x-full"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

            <!-- Top Section: Logo + CTA + Nav -->
            <div class="flex flex-col gap-3 min-h-0 flex-1 overflow-y-auto pr-0.5">

                <!-- Logo & Brand Header -->
                <div class="px-2 py-1 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl overflow-hidden shadow-[0_0_15px_rgba(74,222,128,0.25)] flex items-center justify-center shrink-0 border border-[#4ADE80]/30 bg-[#0e271e] p-1">
                        <img src="{{ asset('logo.png') }}" alt="MONITORA Logo" class="w-full h-full object-contain">
                    </div>
                    <div class="flex flex-col min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[17px] font-extrabold tracking-tight text-white leading-tight">MONITORA</span>
                            <span class="brand-badge-drive px-1.5 py-0.5 rounded text-[8.5px] font-extrabold tracking-wider uppercase font-mono-tag">DRIVE</span>
                        </div>
                        <span class="text-[10px] text-[#A3B8AD] font-medium truncate">Bidang Perekonomian &amp; Perdagangan</span>
                    </div>
                </div>

                <!-- Primary Action Button: Vibrant Mint CTA from welcome & login -->
                @if(Auth::user()->isAdminOrSuperAdmin())
                    <div class="px-1 pt-1">
                        <a href="{{ route('admin.tugas.create') }}"
                           class="btn-mint-cta w-full py-2.5 px-4 rounded-full text-[12.5px] flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                            <span>Buat Penugasan Baru</span>
                        </a>
                    </div>
                @endif

                <!-- Navigation Links -->
                <nav class="flex flex-col gap-1 px-0.5 pt-2">
                    @if(Auth::user()->isAdminOrSuperAdmin())
                        <div class="nav-group-title px-3 pt-1 pb-1">
                            // Monitoring
                        </div>
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13px] transition-all
                                  {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : 'nav-link-idle' }}">
                            <span class="material-symbols-outlined text-[19px]">dashboard</span>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.tugas.index') }}"
                           class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13px] transition-all
                                  {{ request()->routeIs('admin.tugas.*') ? 'nav-link-active' : 'nav-link-idle' }}">
                            <span class="material-symbols-outlined text-[19px]">folder_shared</span>
                            <span>Penugasan OPD</span>
                        </a>
                        <a href="{{ route('admin.penugasan.index') }}"
                           class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13px] transition-all
                                  {{ request()->routeIs('admin.penugasan.*') ? 'nav-link-active' : 'nav-link-idle' }}">
                            <span class="material-symbols-outlined text-[19px]">account_tree</span>
                            <span>Penugasan Bidang</span>
                        </a>

                        @if(Auth::user()->isSuperAdmin())
                            <div class="nav-group-title px-3 pt-3 pb-1">
                                // Manajemen
                            </div>
                            <a href="{{ route('admin.opd.index') }}"
                               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13px] transition-all
                                      {{ request()->routeIs('admin.opd.*') ? 'nav-link-active' : 'nav-link-idle' }}">
                                <span class="material-symbols-outlined text-[19px]">domain</span>
                                <span>Data OPD</span>
                            </a>
                            <a href="{{ route('admin.users.index') }}"
                               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13px] transition-all
                                      {{ request()->routeIs('admin.users.*') ? 'nav-link-active' : 'nav-link-idle' }}">
                                <span class="material-symbols-outlined text-[19px]">group</span>
                                <span>Kelola Pengguna</span>
                            </a>
                        @endif

                        <div class="nav-group-title px-3 pt-3 pb-1">
                            // Aktivitas &amp; Log
                        </div>
                        @if(Auth::user()->isSuperAdmin())
                            <a href="{{ route('admin.login-logs.index') }}"
                               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13px] transition-all
                                      {{ request()->routeIs('admin.login-logs.*') ? 'nav-link-active' : 'nav-link-idle' }}">
                                <span class="material-symbols-outlined text-[19px]">schedule</span>
                                <span>Log Sesi</span>
                            </a>
                        @endif
                        <a href="{{ route('admin.whatsapp-logs.index') }}"
                           class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13px] transition-all
                                  {{ request()->routeIs('admin.whatsapp-logs.*') ? 'nav-link-active' : 'nav-link-idle' }}">
                            <span class="material-symbols-outlined text-[19px]">chat</span>
                            <span>Log Notifikasi WA</span>
                            <span class="ml-auto w-2 h-2 rounded-full bg-[#4ADE80] shadow-[0_0_8px_#4ADE80]"></span>
                        </a>

                    @elseif(Auth::user()->isAnggotaBidang())
                        <div class="nav-group-title px-3 pt-1 pb-1">
                            // Workspace
                        </div>
                        <a href="{{ route('bidang.dashboard') }}"
                           class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13px] transition-all
                                  {{ request()->routeIs('bidang.dashboard') ? 'nav-link-active' : 'nav-link-idle' }}">
                            <span class="material-symbols-outlined text-[19px]">dashboard</span>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.penugasan.index') }}"
                           class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13px] transition-all
                                  {{ request()->routeIs('admin.penugasan.*') ? 'nav-link-active' : 'nav-link-idle' }}">
                            <span class="material-symbols-outlined text-[19px]">account_tree</span>
                            <span>Penugasan Bidang</span>
                        </a>

                    @elseif(Auth::user()->isOpd())
                        <div class="nav-group-title px-3 pt-1 pb-1">
                            // Pelaporan
                        </div>
                        <a href="{{ route('opd.dashboard') }}"
                           class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13px] transition-all
                                  {{ request()->routeIs('opd.dashboard') ? 'nav-link-active' : 'nav-link-idle' }}">
                            <span class="material-symbols-outlined text-[19px]">dashboard</span>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('opd.tugas.index') }}"
                           class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-[13px] transition-all
                                  {{ request()->routeIs('opd.tugas.*') ? 'nav-link-active' : 'nav-link-idle' }}">
                            <span class="material-symbols-outlined text-[19px]">folder_shared</span>
                            <span>Tugas Saya</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Bottom: System Status Info + User Profile -->
            <div class="px-1 pt-3 border-t border-white/10 flex flex-col gap-2 shrink-0">
                <!-- System Info Card -->
                <div class="zen-status-card p-2.5 rounded-xl flex flex-col gap-1.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-white">
                            <div class="w-5 h-5 rounded-md bg-[#4ADE80]/15 flex items-center justify-center text-[#4ADE80] border border-[#4ADE80]/30">
                                <span class="material-symbols-outlined text-[13px]">verified_user</span>
                            </div>
                            <span class="text-[10px] font-bold tracking-wider text-slate-200 uppercase">Ekonomi &amp; Perdagangan</span>
                        </div>
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-[#4ADE80]/15 border border-[#4ADE80]/30 text-[8px] font-extrabold text-[#4ADE80]">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#4ADE80] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-[#4ADE80]"></span>
                            </span>
                            ONLINE
                        </span>
                    </div>

                    <div class="text-[10px] text-[#A3B8AD] flex items-center gap-1">
                        <span class="material-symbols-outlined text-[12px] text-[#7A9A8C]">schedule</span>
                        <span>Zona Waktu WIT (UTC+9) • Sofifi</span>
                    </div>

                    <div class="pt-1.5 border-t border-white/10 flex items-center justify-between text-[10px] text-[#A3B8AD] font-medium">
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[11px] text-[#7A9A8C]">calendar_today</span>
                            T.A. {{ date('Y') }}
                        </span>
                        <span class="text-[#4ADE80] font-bold bg-[#4ADE80]/15 border border-[#4ADE80]/30 px-1.5 py-0.2 rounded text-[9px] font-mono-tag">v3.4.2</span>
                    </div>
                </div>

                <!-- User Profile Bar -->
                <div class="zen-user-bar flex items-center gap-2.5 p-2 rounded-xl cursor-pointer group relative"
                     x-data="{ profileOpen: false }"
                     @click="profileOpen = !profileOpen">
                    <div class="w-8 h-8 rounded-full bg-[#059669] text-white font-extrabold flex items-center justify-center text-xs shadow-xs shrink-0 border border-emerald-400/40">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="flex flex-col min-w-0 flex-1">
                        <span class="text-xs font-bold text-white truncate leading-tight">{{ Auth::user()->name }}</span>
                        <span class="text-[10px] text-[#4ADE80] font-medium truncate leading-tight">{{ Auth::user()->role_label ?? 'Bappeda Malut' }}</span>
                    </div>
                    <span class="material-symbols-outlined text-[18px] text-[#A3B8AD]">expand_more</span>

                    <!-- Dropdown Menu -->
                    <div x-show="profileOpen"
                         x-cloak
                         @click.away="profileOpen = false"
                         class="absolute bottom-full left-0 mb-2 w-full bg-[#0D221B] rounded-2xl shadow-2xl border border-white/15 overflow-hidden z-50 text-white text-xs">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 hover:bg-white/10 font-medium transition-colors">
                            <span class="material-symbols-outlined text-[16px] text-[#4ADE80]">manage_accounts</span>
                            Profil Akun
                        </a>
                        <div class="border-t border-white/10">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 hover:bg-rose-950/40 text-rose-400 font-semibold transition-colors">
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
        <div class="fixed inset-0 bg-black/60 z-30 lg:hidden"
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

        <!-- ========== RIGHT APP SHELL (Container Canvas) ========== -->
        <div class="flex-1 flex flex-col min-w-0 h-full bg-white rounded-2xl md:rounded-3xl shadow-xl overflow-hidden ml-0 lg:ml-3 border border-slate-200/80">

            <!-- Top Header -->
            <header class="h-14 md:h-16 px-4 md:px-6 lg:px-8 border-b border-[#E2E8F0] flex items-center justify-between gap-4 bg-white shrink-0">
                <!-- Mobile Hamburger -->
                <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden w-9 h-9 rounded-xl hover:bg-slate-100 text-slate-700 flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined text-[22px]">menu</span>
                </button>

                <!-- Breadcrumb -->
                <div class="flex items-center gap-2 text-[13px] font-medium">
                    <span class="text-[#059669] font-bold hidden sm:inline">MONITORA</span>
                    <span class="text-slate-300 hidden sm:inline">/</span>
                    <span class="font-extrabold text-[#0F172A]">
                        {{ $pageTitle }}
                    </span>
                </div>

                <!-- Center Search Bar: Pill-shape, Fresh Tint -->
                <div class="flex-1 max-w-xl mx-4 hidden md:block">
                    <div class="relative w-full">
                        <span class="material-symbols-outlined absolute left-3.5 top-2.5 text-[18px] text-slate-400">search</span>
                        <input class="w-full pl-10 pr-4 py-2 text-[12px] bg-[#F4F7F5] hover:bg-[#EBF1ED] border border-transparent focus:border-[#059669] rounded-full text-[#0F172A] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#059669]/20 focus:bg-white transition-all"
                               placeholder="Cari program, kegiatan, OPD, atau berkas..."
                               type="text">
                    </div>
                </div>

                <!-- Right Utility Icons -->
                <div class="flex items-center gap-2 md:gap-3">
                    <a href="{{ route('admin.whatsapp-logs.index') }}"
                       aria-label="Pemberitahuan"
                       class="w-9 h-9 rounded-full hover:bg-slate-100 text-slate-600 flex items-center justify-center transition-colors relative">
                        <span class="material-symbols-outlined text-[20px]">notifications</span>
                        <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                    </a>
                    <div class="flex items-center gap-2.5 pl-2 border-l border-slate-200">
                        <span class="text-[12px] font-bold text-slate-800 hidden lg:inline-block">{{ Auth::user()->name }}</span>
                        <div class="w-8 h-8 rounded-full bg-[#059669] text-white flex items-center justify-center font-bold text-xs shadow-xs border border-emerald-400/40">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <div class="px-4 md:px-6 lg:px-8 pt-3 empty:hidden shrink-0">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show"
                         class="mb-3 flex items-center justify-between p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 shadow-xs transition">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                            <div class="text-sm font-semibold leading-relaxed">{{ session('success') }}</div>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 text-lg leading-none p-1">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show"
                         class="mb-3 flex items-center justify-between p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 shadow-xs transition">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[18px] text-rose-600">error</span>
                            <div class="text-sm font-medium leading-relaxed">{{ session('error') }}</div>
                        </div>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-700 text-lg leading-none p-1">&times;</button>
                    </div>
                @endif

                @if ($errors->any())
                    <div x-data="{ show: true }" x-show="show"
                         class="mb-3 p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 shadow-xs transition">
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

            <!-- Page Content Slot: Fresh Canvas #F4F7F5 -->
            <main class="flex-1 overflow-y-auto min-h-0 bg-[#F4F7F5]">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="w-full bg-white border-t border-slate-200/80 py-3 px-6 lg:px-8 mt-auto flex flex-col md:flex-row items-center justify-between gap-2 text-slate-500 text-[11px] shrink-0">
                <div class="text-left">
                    &copy; {{ date('Y') }} MONITORA &bull; Bidang Perekonomian dan Perdagangan, Bappeda Provinsi Maluku Utara
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <span>Bidang Perekonomian &amp; Perdagangan | Kolaborasi Prodi TI Unkhair</span>
                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                    <span class="font-bold text-[#059669] font-mono-tag">Versi 3.4.2-WIT</span>
                </div>
            </footer>
        </div>

        <!-- Session Ping -->
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