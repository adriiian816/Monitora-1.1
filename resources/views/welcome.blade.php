<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MONITORA | Bidang Perekonomian dan Perdagangan Bappeda Maluku Utara</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Vite Assets (Tailwind CSS & JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- BEGIN: Custom Styles & Animations -->
    <style>
        body {
            background-color: #064e3b;
            background-image: 
                radial-gradient(at 15% 15%, rgba(16, 185, 129, 0.15) 0px, transparent 45%),
                radial-gradient(at 85% 25%, rgba(52, 211, 153, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 90%, rgba(4, 47, 46, 0.6) 0px, transparent 60%);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .dark-glow-card {
            background: linear-gradient(135deg, #042f2e 0%, #064e3b 60%, #0f172a 100%);
            box-shadow: 0 20px 40px -15px rgba(4, 47, 46, 0.6);
        }

        .glass-pill {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        /* Ambient background oscillating orbs */
        @keyframes floatOrb1 {
            0%, 100% { transform: translate(0px, 0px) scale(1); }
            50% { transform: translate(30px, 20px) scale(1.08); }
        }
        @keyframes floatOrb2 {
            0%, 100% { transform: translate(0px, 0px) scale(1); }
            50% { transform: translate(-25px, -30px) scale(1.12); }
        }
        .ambient-orb-1 {
            animation: floatOrb1 16s ease-in-out infinite alternate;
        }
        .ambient-orb-2 {
            animation: floatOrb2 20s ease-in-out infinite alternate;
        }

        /* 3D Logo Continuous Ambient Float & Pulse */
        @keyframes floatSlow {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-9px) rotate(1.2deg);
            }
        }

        @keyframes monitoraGlowPulse {
            0% {
                opacity: 0.45;
                transform: scale(0.92);
            }
            100% {
                opacity: 0.95;
                transform: scale(1.12);
            }
        }

        .logo-float-anim {
            animation: floatSlow 5.5s ease-in-out infinite;
        }

        .logo-pulse-anim {
            animation: monitoraGlowPulse 4s ease-in-out infinite alternate;
        }

        /* Shimmer light sweep across 3D logo container */
        .shimmer-sweep {
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                115deg,
                transparent 0%,
                transparent 40%,
                rgba(255, 255, 255, 0.16) 50%,
                transparent 60%,
                transparent 100%
            );
            transform: translateX(-100%);
            transition: transform 0.8s ease;
            pointer-events: none;
        }
        #hero-3d-card-interactive:hover .shimmer-sweep {
            transform: translateX(100%);
        }

        /* Staggered entrance animation for principle cards */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-stagger-1 {
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both;
        }
        .card-stagger-2 {
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.22s both;
        }
        .card-stagger-3 {
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.34s both;
        }

        /* Reduced Motion Accessibility */
        @media (prefers-reduced-motion: reduce) {
            .ambient-orb-1,
            .ambient-orb-2,
            .logo-float-anim,
            .logo-pulse-anim,
            .card-stagger-1,
            .card-stagger-2,
            .card-stagger-3,
            .shimmer-sweep {
                animation: none !important;
                transform: none !important;
                transition: none !important;
            }
        }
    </style>
    <!-- END: Custom Styles & Animations -->
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col justify-between selection:bg-emerald-100 selection:text-emerald-900 p-3 sm:p-5 lg:p-6 pb-6 sm:pb-8">

    <!-- BEGIN: MainHeader (Floating Pill Capsule) -->
    <header class="glass-pill border border-emerald-100/80 sticky top-3 z-50 shadow-pill rounded-full max-w-7xl mx-auto w-full transition-all duration-300 hover:shadow-lg hover:border-emerald-200" data-purpose="site-navigation-bar">
        <div class="px-4 sm:px-8 py-3 sm:py-3.5 flex items-center justify-between gap-3">
            <a class="flex items-center gap-2 sm:gap-3 group" href="{{ route('welcome') }}">
                <div class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center shrink-0 transition-transform duration-300 group-hover:scale-105">
                    <img alt="MONITORA Logo" class="w-full h-full object-contain" src="{{ asset('logo.png') }}">
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-base sm:text-lg font-extrabold tracking-tight text-[#064e3b] transition-colors group-hover:text-emerald-800">MONITORA</span>
                    </div>
                    <p class="text-[10px] sm:text-xs text-slate-500 font-medium leading-none hidden xs:block sm:block">Bid. Perekonomian &amp; Perdagangan Bappeda Malut</p>
                </div>
            </a>
            <div class="flex items-center gap-2">
                @auth
                    <a class="inline-flex items-center justify-center gap-1.5 px-4 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs font-bold text-white bg-[#064e3b] hover:bg-[#042f2e] shadow-sm transition-all duration-200 active:scale-95 group" href="{{ route('dashboard') }}">
                        <span>Dashboard</span>
                        <span class="material-symbols-outlined text-[15px] hidden sm:inline group-hover:translate-x-1 transition-transform duration-200">dashboard</span>
                    </a>
                @else
                    <a class="inline-flex items-center justify-center gap-1.5 px-4 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs font-bold text-white bg-[#064e3b] hover:bg-[#042f2e] shadow-sm transition-all duration-200 active:scale-95 group" href="{{ route('login') }}">
                        <span>Masuk</span>
                        <span class="material-symbols-outlined text-[15px] group-hover:translate-x-1 transition-transform duration-200">arrow_forward</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>
    <!-- END: MainHeader -->

    <!-- BEGIN: HeroSection (Centered Bento Showcase) -->
    <section class="relative bg-white text-[#0f172a] mt-4 rounded-3xl lg:rounded-[36px] border border-emerald-100 shadow-bento overflow-hidden max-w-7xl mx-auto w-full pt-10 pb-12 sm:pt-14 sm:pb-20 lg:pt-20 lg:pb-28 px-5 sm:px-10" data-purpose="hero-gatekeeper" id="hero">
        <div class="ambient-orb-1 absolute -right-28 -top-28 w-96 h-96 rounded-full bg-emerald-100/60 blur-3xl pointer-events-none"></div>
        <div class="ambient-orb-2 absolute -left-24 top-20 w-80 h-80 rounded-full bg-teal-100/50 blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 py-4 sm:py-6 lg:py-10 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center text-left">
            <!-- Left Info Area -->
            <div class="lg:col-span-7 flex flex-col items-start justify-center pr-0 lg:pr-4">
                <div class="inline-flex items-center gap-2 px-3 sm:px-4 py-1 sm:py-1.5 rounded-full bg-emerald-50 text-[#064e3b] border border-emerald-200 text-[10px] sm:text-xs font-bold tracking-wide shadow-xs mb-4 sm:mb-5 transition-transform duration-300 hover:scale-105">
                    <span class="w-2 h-2 rounded-full bg-[#059669] animate-ping shrink-0"></span>
                    <span class="sm:hidden">PORTAL RESMI BIDANG EKOP &amp; PERDAGANGAN</span>
                    <span class="hidden sm:inline">PORTAL RESMI BIDANG PEREKONOMIAN &amp; PERDAGANGAN</span>
                </div>
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-[#064e3b] tracking-tight leading-[1.15] mb-2 sm:mb-3">MONITORA</h1>
                <p class="text-sm sm:text-lg lg:text-xl font-semibold text-[#059669] mb-3 sm:mb-4 tracking-tight leading-snug">Bidang Perekonomian dan Perdagangan — Bappeda Provinsi Maluku Utara</p>
                <p class="text-xs sm:text-sm lg:text-base text-slate-600 font-normal leading-relaxed mb-5 sm:mb-7 max-w-xl">
                    Sistem informasi terpadu pemantauan dan pengelolaan pelaporan dokumen perencanaan pembangunan daerah yang dikembangkan oleh <strong>Bidang Perekonomian dan Perdagangan Bappeda Provinsi Maluku Utara</strong> secara tertib, terarah, dan transparan.
                </p>
                
                <div class="flex flex-col xs:flex-row sm:flex-row items-stretch xs:items-center gap-3 sm:gap-4 w-full sm:w-auto mb-6 sm:mb-8">
                    @auth
                        <a class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3.5 rounded-full font-bold text-sm text-white bg-[#059669] hover:bg-[#047857] shadow-md hover:shadow-[0_10px_25px_-5px_rgba(5,150,105,0.45)] transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95 group" href="{{ route('dashboard') }}">
                            <span>Buka Dashboard</span>
                            <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1.5 transition-transform duration-200">dashboard</span>
                        </a>
                    @else
                        <a class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3.5 rounded-full font-bold text-sm text-white bg-[#059669] hover:bg-[#047857] shadow-md hover:shadow-[0_10px_25px_-5px_rgba(5,150,105,0.45)] transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95 group" href="{{ route('login') }}" id="login-portal">
                            <span>Masuk ke Portal</span>
                            <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1.5 transition-transform duration-200">arrow_forward</span>
                        </a>
                    @endauth

                    <a class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full font-semibold text-xs text-slate-700 bg-white hover:bg-emerald-50/70 border border-slate-200 hover:border-emerald-300 hover:text-emerald-900 hover:ring-2 hover:ring-emerald-100 hover:-translate-y-0.5 transition-all duration-200 shadow-xs active:scale-95 group" href="#alur-pengumpulan">
                        <span class="material-symbols-outlined text-[16px] text-slate-500 group-hover:text-emerald-600 transition-colors">menu_book</span>
                        <span>Pelajari Alur Pengumpulan</span>
                    </a>
                </div>

                <!-- Prinsip Layanan Cards -->
                <div class="w-full pt-6 border-t border-emerald-100">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#f0f5f2] text-[#064e3b] text-xs font-bold uppercase tracking-wider border border-emerald-200 w-fit mb-3.5">
                        <span class="material-symbols-outlined text-[16px] text-[#059669]">verified_user</span>
                        <span>PRINSIP LAYANAN</span>
                    </div>
                    <div class="grid grid-cols-1 xs:grid-cols-3 sm:grid-cols-3 gap-2 sm:gap-3 w-full">
                        <div class="card-stagger-1 p-3.5 rounded-2xl bg-[#f8faf9] border border-emerald-100 flex flex-col gap-2 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[0_12px_24px_-8px_rgba(6,78,59,0.12)] hover:border-emerald-400/50 hover:bg-white group cursor-default">
                            <div class="w-8 h-8 rounded-lg bg-white text-[#059669] border border-emerald-200 flex items-center justify-center font-bold shadow-xs transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3 group-hover:bg-emerald-50">
                                <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-hover:scale-105">inventory_2</span>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-[#064e3b] tracking-tight mb-0.5 group-hover:text-emerald-700 transition-colors">Pelaporan Terpadu</h4>
                                <p class="text-slate-600 text-[11px] leading-snug">Satu pintu resmi pengumpulan dokumen perencanaan.</p>
                            </div>
                        </div>

                        <div class="card-stagger-2 p-3.5 rounded-2xl bg-[#f8faf9] border border-emerald-100 flex flex-col gap-2 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[0_12px_24px_-8px_rgba(6,78,59,0.12)] hover:border-emerald-400/50 hover:bg-white group cursor-default">
                            <div class="w-8 h-8 rounded-lg bg-white text-[#059669] border border-emerald-200 flex items-center justify-center font-bold shadow-xs transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3 group-hover:bg-emerald-50">
                                <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-hover:scale-105">verified</span>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-[#064e3b] tracking-tight mb-0.5 group-hover:text-emerald-700 transition-colors">Transparan &amp; Terarah</h4>
                                <p class="text-slate-600 text-[11px] leading-snug">Koordinasi terjadwal, akuntabel, dan terukur.</p>
                            </div>
                        </div>

                        <div class="card-stagger-3 p-3.5 rounded-2xl bg-[#f8faf9] border border-emerald-100 flex flex-col gap-2 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[0_12px_24px_-8px_rgba(6,78,59,0.12)] hover:border-emerald-400/50 hover:bg-white group cursor-default">
                            <div class="w-8 h-8 rounded-lg bg-white text-[#059669] border border-emerald-200 flex items-center justify-center font-bold shadow-xs transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3 group-hover:bg-emerald-50">
                                <span class="material-symbols-outlined text-[18px] transition-transform duration-300 group-hover:scale-105">rule</span>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-[#064e3b] tracking-tight mb-0.5 group-hover:text-emerald-700 transition-colors">Tertib Administrasi</h4>
                                <p class="text-slate-600 text-[11px] leading-snug">Kepatuhan dan konsistensi data pembangunan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right 3D Interactive Showcase Card – hidden on mobile to keep page light -->
            <div class="hidden lg:flex lg:col-span-5 flex-col items-center justify-center relative">
                <div class="w-full max-w-md p-6 sm:p-8 rounded-3xl bg-gradient-to-b from-[#064e3b] via-[#042f2e] to-[#021b1a] text-white shadow-2xl border border-emerald-500/20 relative overflow-hidden flex flex-col items-center text-center cursor-pointer transition-all duration-300 ease-out hover:border-emerald-400/40 hover:shadow-[0_30px_60px_-15px_rgba(4,47,46,0.8)]" id="hero-3d-card-interactive" style="perspective: 1000px; transform-style: preserve-3d;">
                    <div class="shimmer-sweep"></div>
                    <div class="ambient-orb-1 absolute -top-16 -right-16 w-48 h-48 rounded-full bg-emerald-400/20 blur-2xl pointer-events-none"></div>
                    <div class="ambient-orb-2 absolute -bottom-16 -left-16 w-48 h-48 rounded-full bg-teal-400/15 blur-2xl pointer-events-none"></div>
                    
                    <div class="relative z-10 w-64 h-64 sm:w-72 sm:h-72 flex items-center justify-center mb-4">
                        <div class="logo-float-anim relative w-full h-full flex items-center justify-center transition-transform duration-200 ease-out" id="hero-3d-logo-floating" style="transform-style: preserve-3d;">
                            <div class="logo-pulse-anim absolute inset-4 rounded-full bg-emerald-400/30 blur-3xl pointer-events-none -z-10"></div>
                            <img alt="Logo MONITORA" class="w-full h-full object-contain drop-shadow-[0_25px_35px_rgba(0,0,0,0.55)] transition-transform duration-300 group-hover:scale-105" id="hero-3d-logo-img" src="{{ asset('logo.png') }}" style="background: transparent;">
                        </div>
                    </div>
                    
                    <div class="relative z-10 flex flex-col items-center">
                        <span class="text-2xl sm:text-3xl font-extrabold tracking-wider text-white drop-shadow-sm transition-transform duration-300">MONITORA</span>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-xs text-emerald-200 font-medium tracking-wide uppercase">Bidang Perekonomian &amp; Perdagangan</span>
                        </div>
                        <p class="text-[11px] text-emerald-100/70 mt-3 max-w-xs font-normal leading-relaxed text-center">Bappeda Provinsi Maluku Utara</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END: HeroSection -->

    <!-- BEGIN: ProcessWorkflowSection ("Alur Penyampaian Dokumen") -->
    <section class="relative bg-white text-[#0f172a] mt-4 mb-4 rounded-3xl lg:rounded-[36px] border border-emerald-100 shadow-bento overflow-hidden max-w-7xl mx-auto w-full py-12 lg:py-16 px-5 sm:px-10" id="alur-pengumpulan" data-purpose="process-workflow-section">
        <div class="ambient-orb-1 absolute -right-24 -top-24 w-80 h-80 rounded-full bg-emerald-100/50 blur-3xl pointer-events-none"></div>
        <div class="ambient-orb-2 absolute -left-20 bottom-10 w-72 h-72 rounded-full bg-teal-100/40 blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10">
            <!-- Header Alur Section -->
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-8 border-b border-emerald-100/80 mb-10">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 text-[#064e3b] border border-emerald-200 text-xs font-bold tracking-wide shadow-xs mb-3.5">
                        <span class="w-2 h-2 rounded-full bg-[#059669]"></span>
                        <span>SISTEMATIKA &amp; PROSEDUR PENGUMPULAN</span>
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-[#064e3b] tracking-tight leading-tight">4 Tahap Alur Pengumpulan Dokumen Perencanaan</h2>
                    <p class="text-sm sm:text-base text-slate-600 font-normal leading-relaxed mt-2">
                        Panduan tata kelola dan alur kerja penyampaian dokumen rencana pembangunan daerah bagi seluruh Organisasi Perangkat Daerah (OPD) di lingkungan Pemerintah Provinsi Maluku Utara.
                    </p>
                </div>
                <div class="p-4 rounded-2xl bg-[#f8faf9] border border-emerald-100/80 max-w-md">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[#059669] text-xl shrink-0 mt-0.5">info</span>
                        <div class="text-xs text-slate-600 leading-relaxed">
                            <span class="font-bold text-[#064e3b]">Pedoman Koordinasi:</span> Pastikan setiap tahap diselesaikan sesuai jadwal siklus perencanaan daerah demi validitas data makro Provinsi.
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4 Tahap Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5 mb-14">
                <!-- Step 01 -->
                <div class="p-5 rounded-2xl bg-[#f8faf9] border border-emerald-100 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:bg-white hover:border-emerald-300 group">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-black text-[#059669]/60 group-hover:text-[#059669] transition-colors">01</span>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-[#064e3b] border border-emerald-200">Notifikasi Terjadwal</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-white text-[#059669] border border-emerald-200 flex items-center justify-center font-bold shadow-xs transition-transform duration-300 group-hover:scale-105">
                            <span class="material-symbols-outlined text-[20px]">campaign</span>
                        </div>
                        <h3 class="text-base font-bold text-[#064e3b] tracking-tight group-hover:text-emerald-700 transition-colors">Penerbitan Tugas &amp; Edaran</h3>
                        <p class="text-slate-600 text-xs leading-relaxed">Mandat resmi instruksi pengumpulan dokumen diterbitkan oleh Bidang Perekonomian dan Perdagangan Bappeda via MONITORA serta notifikasi otomatis WhatsApp Gateway ke narahubung OPD mitra.</p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-emerald-100/60 flex items-center gap-1.5 text-[11px] font-semibold text-[#059669]">
                        <span class="material-symbols-outlined text-sm">schedule</span>
                        <span>Jadwal Surat Edaran</span>
                    </div>
                </div>

                <!-- Step 02 -->
                <div class="p-5 rounded-2xl bg-[#f8faf9] border border-emerald-100 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:bg-white hover:border-emerald-300 group">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-black text-[#059669]/60 group-hover:text-[#059669] transition-colors">02</span>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-[#064e3b] border border-emerald-200">Validasi Mandiri OPD</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-white text-[#059669] border border-emerald-200 flex items-center justify-center font-bold shadow-xs transition-transform duration-300 group-hover:scale-105">
                            <span class="material-symbols-outlined text-[20px]">fact_check</span>
                        </div>
                        <h3 class="text-base font-bold text-[#064e3b] tracking-tight group-hover:text-emerald-700 transition-colors">Penyusunan &amp; Verifikasi OPD</h3>
                        <p class="text-slate-600 text-xs leading-relaxed">Tim kerja internal OPD menyusun dokumen teknis sesuai indikator kinerja, memastikan keselarasan data, dan pengesahan kepala dinas/badan.</p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-emerald-100/60 flex items-center gap-1.5 text-[11px] font-semibold text-[#059669]">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        <span>Pemeriksaan Internal</span>
                    </div>
                </div>

                <!-- Step 03 -->
                <div class="p-5 rounded-2xl bg-[#f8faf9] border border-emerald-100 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:bg-white hover:border-emerald-300 group">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-black text-[#059669]/60 group-hover:text-[#059669] transition-colors">03</span>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-[#064e3b] border border-emerald-200">Dukungan Multi-Kanal</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-white text-[#059669] border border-emerald-200 flex items-center justify-center font-bold shadow-xs transition-transform duration-300 group-hover:scale-105">
                            <span class="material-symbols-outlined text-[20px]">cloud_upload</span>
                        </div>
                        <h3 class="text-base font-bold text-[#064e3b] tracking-tight group-hover:text-emerald-700 transition-colors">Pengunggahan Berkas</h3>
                        <p class="text-slate-600 text-xs leading-relaxed">Unggah berkas digital PDF/Excel melalui formulir daring terpadu atau penyerahan fisik ke loket Bidang Perekonomian dan Perdagangan Bappeda di Sofifi sesuai metode terpilih.</p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-emerald-100/60 flex items-center gap-1.5 text-[11px] font-semibold text-[#059669]">
                        <span class="material-symbols-outlined text-sm">sync_alt</span>
                        <span>Sinkronisasi Otomatis</span>
                    </div>
                </div>

                <!-- Step 04 -->
                <div class="p-5 rounded-2xl bg-[#f8faf9] border border-emerald-100 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:bg-white hover:border-emerald-300 group">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-black text-[#059669]/60 group-hover:text-[#059669] transition-colors">04</span>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-[#064e3b] border border-emerald-200">Sertifikasi Tanda Terima</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-white text-[#059669] border border-emerald-200 flex items-center justify-center font-bold shadow-xs transition-transform duration-300 group-hover:scale-105">
                            <span class="material-symbols-outlined text-[20px]">qr_code_2</span>
                        </div>
                        <h3 class="text-base font-bold text-[#064e3b] tracking-tight group-hover:text-emerald-700 transition-colors">Validasi &amp; Tanda Terima QR</h3>
                        <p class="text-slate-600 text-xs leading-relaxed">Tim verifikator Bidang Perekonomian dan Perdagangan Bappeda menelaah berkas. Berkas yang lolos langsung memperoleh Tanda Terima Elektronik ber-QR Code sah.</p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-emerald-100/60 flex items-center gap-1.5 text-[11px] font-semibold text-[#059669]">
                        <span class="material-symbols-outlined text-sm">verified</span>
                        <span>Legalitas Akuntabel</span>
                    </div>
                </div>
            </div>

            <!-- Pilihan Metode Pengantaran Berkas -->
            <div class="pt-6 border-t border-emerald-100/80 mb-10">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-xl sm:text-2xl font-bold text-[#064e3b] tracking-tight">Pilihan Metode Pengantaran Berkas</h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-2xl">OPD dapat memilih metode yang paling efisien sesuai regulasi dokumen dan ketersediaan tanda tangan elektronik.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    <!-- Metode 1 -->
                    <div class="p-6 rounded-2xl bg-gradient-to-b from-white to-[#f8faf9] border-2 border-[#059669] relative shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-lg flex flex-col justify-between">
                        <div class="absolute -top-3 right-6 px-3 py-0.5 rounded-full bg-[#059669] text-white text-[11px] font-bold tracking-wider uppercase shadow-xs">Rekomendasi</div>
                        <div class="flex flex-col gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#059669] border border-emerald-200 flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl">cloud_done</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-base font-bold text-[#064e3b]">Digital Penuh (Paperless)</h4>
                                <span class="text-[11px] font-semibold text-[#059669] bg-emerald-100/60 px-2 py-0.5 rounded-md">100% Online</span>
                            </div>
                            <p class="text-slate-600 text-xs leading-relaxed">Pengunggahan dokumen PDF bertanda tangan digital (TTE BSrE) atau cap basah hasil pindai langsung ke portal MONITORA tanpa berkas fisik. Tanda terima terbit seketika, hemat biaya cetak.</p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-emerald-100/80 flex items-center justify-between text-xs font-semibold text-[#064e3b]">
                            <span>Waktu Proses: Cepat</span>
                            <span class="material-symbols-outlined text-sm text-[#059669]">bolt</span>
                        </div>
                    </div>

                    <!-- Metode 2 -->
                    <div class="p-6 rounded-2xl bg-white border border-emerald-100 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-emerald-300 flex flex-col justify-between">
                        <div class="flex flex-col gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#f0f5f2] text-[#064e3b] border border-emerald-200 flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl">swap_horiz</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-base font-bold text-[#064e3b]">Metode Hybrid</h4>
                                <span class="text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">Upload &amp; Fisik</span>
                            </div>
                            <p class="text-slate-600 text-xs leading-relaxed">Unggah dokumen draft di portal untuk pra-verifikasi, disusul penyerahan eksemplar jilid resmi saat tahap finalisasi asistensi. Koreksi cepat sebelum cetak jilid.</p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-emerald-100/80 flex items-center justify-between text-xs font-semibold text-[#064e3b]">
                            <span>Fleksibel &amp; Bertahap</span>
                            <span class="material-symbols-outlined text-sm text-slate-500">check</span>
                        </div>
                    </div>

                    <!-- Metode 3 -->
                    <div class="p-6 rounded-2xl bg-white border border-emerald-100 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-emerald-300 flex flex-col justify-between">
                        <div class="flex flex-col gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#f0f5f2] text-[#064e3b] border border-emerald-200 flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl">storefront</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-base font-bold text-[#064e3b]">Loket Terpadu Sofifi</h4>
                                <span class="text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">Loket Sofifi</span>
                            </div>
                            <p class="text-slate-600 text-xs leading-relaxed">Penyerahan berkas fisik langsung ke Front Office / Ruang Bidang Perekonomian dan Perdagangan Gedung Bappeda Maluku Utara, Jl. Gosale Puncak No. 1, dengan pencatatan tanda terima oleh petugas. Jam layanan Senin-Jumat 08.00-16.00 WIT.</p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-emerald-100/80 flex items-center justify-between text-xs font-semibold text-[#064e3b]">
                            <span>Layanan Tatap Muka</span>
                            <span class="material-symbols-outlined text-sm text-slate-500">badge</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick CTA Banner -->
            <div class="p-6 sm:p-8 rounded-2xl sm:rounded-3xl bg-gradient-to-r from-[#064e3b] via-[#042f2e] to-[#021b1a] text-white flex flex-col sm:flex-row items-center justify-between gap-6 shadow-lg">
                <div class="text-center sm:text-left">
                    <h3 class="text-lg sm:text-xl font-bold tracking-tight">Akses Ruang Kerja Pelaporan OPD Sekarang</h3>
                    <p class="text-xs sm:text-sm text-emerald-100/80 mt-1 max-w-lg">Gunakan akun resmi subag perencanaan/evaluasi OPD mitra untuk memulai penyampaian dan pemantauan status berkas bersama Bidang Perekonomian dan Perdagangan.</p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-3 w-full sm:w-auto">
                    @auth
                        <a class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full font-bold text-xs text-[#064e3b] bg-white hover:bg-emerald-50 transition-all duration-200 active:scale-95 shadow-sm group" href="{{ route('dashboard') }}">
                            <span>Ke Dashboard</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">dashboard</span>
                        </a>
                    @else
                        <a class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full font-bold text-xs text-[#064e3b] bg-white hover:bg-emerald-50 transition-all duration-200 active:scale-95 shadow-sm group" href="{{ route('login') }}">
                            <span>Masuk ke Portal</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    @endauth

                    <a class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full font-semibold text-xs text-white/90 hover:text-white border border-white/20 hover:border-white/40 transition-all duration-200 active:scale-95" href="#hero">
                        <span class="material-symbols-outlined text-base">arrow_upward</span>
                        <span>Kembali ke Atas</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <!-- END: ProcessWorkflowSection -->

    <!-- BEGIN: MainFooter (Institutional Footer) -->
    <footer class="w-full max-w-7xl mx-auto px-5 sm:px-8 py-5 rounded-2xl sm:rounded-full bg-white border border-emerald-100 text-slate-500 text-xs shadow-sm mt-2 transition-all duration-300 hover:shadow-md hover:border-emerald-200" data-purpose="official-footer" id="footer-tentang">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-600">
            <div class="font-medium text-center sm:text-left">
                Bidang Perekonomian dan Perdagangan &bull; Bappeda Provinsi Maluku Utara &bull; &copy; {{ date('Y') }} MONITORA
            </div>
            <div class="flex items-center gap-2 text-slate-500">
                <span class="material-symbols-outlined text-[16px] text-[#059669]">location_on</span>
                <span>Jl. Gosale Puncak No. 1, Kota Sofifi, Maluku Utara</span>
            </div>
        </div>
    </footer>
    <!-- END: MainFooter -->

    <!-- BEGIN: Interactive 3D Tilt Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const card = document.getElementById('hero-3d-card-interactive');
            const floatingLogo = document.getElementById('hero-3d-logo-floating');

            if (!card || !floatingLogo) return;

            // Check if user prefers reduced motion
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (prefersReducedMotion) return;

            let isHovering = false;

            card.addEventListener('mouseenter', () => {
                isHovering = true;
                card.style.transition = 'transform 0.15s ease-out, box-shadow 0.3s ease, border-color 0.3s ease';
            });

            card.addEventListener('mousemove', (e) => {
                if (!isHovering) return;
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                // Calculate tilt angles (range approx -10 to +10 deg)
                const rotateX = ((y - centerY) / centerY) * -10;
                const rotateY = ((x - centerX) / centerX) * 10;

                card.style.transform = `perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) scale3d(1.02, 1.02, 1.02)`;
            });

            card.addEventListener('mouseleave', () => {
                isHovering = false;
                card.style.transition = 'transform 0.5s ease-out, box-shadow 0.3s ease, border-color 0.3s ease';
                card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
            });
        });
    </script>
    <!-- END: Interactive 3D Tilt Script -->
</body>
</html>
