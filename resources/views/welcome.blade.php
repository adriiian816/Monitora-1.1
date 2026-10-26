<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MONITORA | Bidang Perekonomian dan Perdagangan Bappeda Maluku Utara</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Google Fonts: Inter & Plus Jakarta Sans & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Vite Assets (Tailwind CSS & JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- BEGIN: Custom Styles & ZenABM Visual Aesthetics -->
    <style>
        :root {
            --zen-dark: #091A14;
            --zen-light: #F8F9F5;
            --zen-accent: #4ADE80;
            --zen-text-dark: #FFFFFF;
            --zen-muted: #A3B8AD;
            --zen-slate: #0F172A;
            --zen-border-dark: rgba(255, 255, 255, 0.1);
            --zen-border-light: rgba(0, 0, 0, 0.1);
        }

        body {
            background-color: var(--zen-dark);
            color: #FFFFFF;
            font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .font-mono-tag {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Bracketed Section Tag */
        .bracket-tag {
            font-size: 11px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Aurora Glow Effect */
        .aurora-glow-hero {
            position: absolute;
            width: 580px;
            height: 580px;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(74, 222, 128, 0.18) 0%, rgba(16, 185, 129, 0.08) 40%, transparent 70%);
            filter: blur(60px);
            pointer-events: none;
        }

        .aurora-glow-footer {
            position: absolute;
            width: 650px;
            height: 650px;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(74, 222, 128, 0.14) 0%, rgba(6, 78, 59, 0.12) 50%, transparent 75%);
            filter: blur(70px);
            pointer-events: none;
        }

        /* Sharp Buttons */
        .btn-zen-primary {
            background-color: #FFFFFF;
            color: #091A14;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 13px 22px;
            border-radius: 0px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid #FFFFFF;
        }
        .btn-zen-primary:hover {
            background-color: #F0FDF4;
            color: #042F2E;
            box-shadow: 0 0 24px rgba(74, 222, 128, 0.35);
            transform: translateY(-1px);
        }
        .btn-zen-primary:active {
            transform: translateY(0);
        }

        .btn-zen-secondary {
            background-color: transparent;
            color: #FFFFFF;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 13px 22px;
            border-radius: 0px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.35);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-zen-secondary:hover {
            border-color: #FFFFFF;
            background-color: rgba(255, 255, 255, 0.06);
            transform: translateY(-1px);
        }
        .btn-zen-secondary:active {
            transform: translateY(0);
        }

        /* Light Mode Buttons */
        .btn-zen-light-primary {
            background-color: #0F172A;
            color: #FFFFFF;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 13px 22px;
            border-radius: 0px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid #0F172A;
        }
        .btn-zen-light-primary:hover {
            background-color: #1E293B;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.2);
            transform: translateY(-1px);
        }

        .btn-zen-light-secondary {
            background-color: transparent;
            color: #0F172A;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 13px 22px;
            border-radius: 0px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid rgba(15, 23, 42, 0.3);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-zen-light-secondary:hover {
            border-color: #0F172A;
            background-color: rgba(15, 23, 42, 0.04);
            transform: translateY(-1px);
        }

        /* Continuous Ambient Float for 3D Logo */
        @keyframes floatSlow {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-8px) rotate(1.2deg);
            }
        }

        @keyframes monitoraGlowPulse {
            0% {
                opacity: 0.45;
                transform: scale(0.92);
            }
            100% {
                opacity: 0.85;
                transform: scale(1.1);
            }
        }

        .logo-float-anim {
            animation: floatSlow 5s ease-in-out infinite;
        }

        .logo-pulse-anim {
            animation: monitoraGlowPulse 3.8s ease-in-out infinite alternate;
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
                transparent 42%,
                rgba(74, 222, 128, 0.12) 50%,
                transparent 58%,
                transparent 100%
            );
            transform: translateX(-100%);
            transition: transform 0.8s ease;
            pointer-events: none;
        }
        #hero-3d-card-interactive:hover .shimmer-sweep {
            transform: translateX(100%);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #091A14;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.18);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(74, 222, 128, 0.5);
        }

        /* Reduced Motion Accessibility */
        @media (prefers-reduced-motion: reduce) {
            .logo-float-anim,
            .logo-pulse-anim,
            .shimmer-sweep {
                animation: none !important;
                transform: none !important;
                transition: none !important;
            }
        }
    </style>
    <!-- END: Custom Styles & ZenABM Visual Aesthetics -->
</head>
<body class="bg-[#091A14] text-white antialiased selection:bg-[#4ADE80] selection:text-[#091A14]">

    <!-- ========================================================================= -->
    <!-- SECTION 1: HEADER / NAVBAR (Dark Background #091A14)                       -->
    <!-- ========================================================================= -->
    <header class="sticky top-0 z-50 bg-[#091A14]/90 backdrop-blur-md border-b border-white/10" data-purpose="site-navigation-bar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            <!-- Left: Logo & Subtitle -->
            <a class="flex items-center gap-3 group" href="{{ route('welcome') }}" title="MONITORA Portal">
                <div class="w-10 h-10 rounded-sm bg-[#0e271e] border border-white/15 flex items-center justify-center p-1.5 transition-all duration-300 group-hover:border-[#4ADE80]/60 group-hover:shadow-[0_0_15px_rgba(74,222,128,0.25)]">
                    <img alt="MONITORA Logo" class="w-full h-full object-contain" src="{{ asset('logo.png') }}">
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-lg font-black tracking-tight text-white transition-colors group-hover:text-[#4ADE80]">MONITORA</span>
                        <span class="text-[9px] font-mono-tag font-bold px-1.5 py-0.5 rounded-none bg-[#4ADE80]/15 text-[#4ADE80] border border-[#4ADE80]/30 uppercase">BAPPEDA</span>
                    </div>
                    <p class="text-[10px] sm:text-[11px] text-[#A3B8AD] font-medium leading-none tracking-normal">Bid. Perekonomian &amp; Perdagangan Bappeda</p>
                </div>
            </a>

            <!-- Center: Nav Links -->
            <nav class="hidden md:flex items-center gap-6 lg:gap-8 text-xs font-mono-tag uppercase tracking-wider text-[#A3B8AD]">
                <a href="#hero" class="hover:text-[#4ADE80] transition-colors py-1 flex items-center gap-1">
                    <span class="text-[#4ADE80]/60">01</span> Beranda
                </a>
                <a href="#product-experience" class="hover:text-[#4ADE80] transition-colors py-1 flex items-center gap-1">
                    <span class="text-[#4ADE80]/60">02</span> Prinsip
                </a>
                <a href="#the-problem" class="hover:text-[#4ADE80] transition-colors py-1 flex items-center gap-1">
                    <span class="text-[#4ADE80]/60">03</span> Masalah
                </a>
                <a href="#alur-pengumpulan" class="hover:text-[#4ADE80] transition-colors py-1 flex items-center gap-1">
                    <span class="text-[#4ADE80]/60">04</span> Alur
                </a>
                <a href="#metode-pengantaran" class="hover:text-[#4ADE80] transition-colors py-1 flex items-center gap-1">
                    <span class="text-[#4ADE80]/60">05</span> Metode
                </a>
            </nav>

            <!-- Right: Action CTA -->
            <div class="flex items-center gap-3">
                @auth
                    <a class="btn-zen-primary" href="{{ route('dashboard') }}" title="Buka Dashboard">
                        <span>Dashboard</span>
                        <span class="text-sm font-bold leading-none">↗</span>
                    </a>
                @else
                    <a class="hidden sm:inline-block text-xs font-mono-tag font-semibold uppercase tracking-wider text-[#A3B8AD] hover:text-white transition-colors px-2 py-1" href="{{ route('login') }}">
                        Log In
                    </a>
                    <a class="btn-zen-primary" href="{{ route('login') }}" id="login-portal" title="Masuk ke Portal Resmi">
                        <span>Masuk ke Portal</span>
                        <span class="text-sm font-bold leading-none">↗</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- ========================================================================= -->
    <!-- SECTION 2: HERO SECTION (Dark Background #091A14 with Aurora Glow)        -->
    <!-- ========================================================================= -->
    <section class="relative bg-[#091A14] text-white pt-16 pb-20 sm:pt-24 sm:pb-28 lg:pt-28 lg:pb-36 border-b border-white/10 overflow-hidden" id="hero" data-purpose="hero-gatekeeper">
        <!-- Aurora Glowing Green Gradient at Top-Right & Ambient Center -->
        <div class="aurora-glow-hero -top-24 -right-24"></div>
        <div class="aurora-glow-hero -bottom-36 -left-36 opacity-60"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Label Tag: [ NOMOR ] NAMA SECTION -->
            <div class="mb-6">
                <span class="bracket-tag font-mono-tag text-[#A3B8AD]">
                    [ 01 ] SISTEM INFORMASI PERENCANAAN PEMBANGUNAN DAERAH
                </span>
            </div>

            <!-- Big Headline with Accent Highlight -->
            <div class="max-w-5xl">
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight leading-[1.08] text-white mb-8">
                    Your Integrated System for <span class="text-[#4ADE80]">Perencanaan Daerah</span> Maluku Utara
                </h1>
            </div>

            <!-- Bottom Description: Split into Two Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 pt-8 border-t border-white/10 items-start">
                <!-- Kolom Kiri: Label [ BUILT FOR WHAT'S NEXT ] / Institusi -->
                <div class="lg:col-span-4 flex flex-col gap-3">
                    <span class="bracket-tag font-mono-tag text-[#4ADE80]">
                        [ BIDANG PEREKONOMIAN &amp; PERDAGANGAN ]
                    </span>
                    <p class="text-xs sm:text-sm text-[#A3B8AD] leading-relaxed">
                        Bappeda Provinsi Maluku Utara &bull; Jl. Gosale Puncak No. 1, Kota Sofifi. Tata kelola data pembangunan terarah, terpadu, dan terukur.
                    </p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="w-2 h-2 rounded-full bg-[#4ADE80] animate-pulse"></span>
                        <span class="text-[11px] font-mono-tag uppercase text-white/80">Sistem Operasional Aktif</span>
                    </div>
                </div>

                <!-- Kolom Kanan: Teks deskripsi pendek, diikuti baris tombol -->
                <div class="lg:col-span-8 flex flex-col gap-6">
                    <p class="text-base sm:text-lg text-slate-200 leading-relaxed font-normal max-w-2xl">
                        Sistem informasi terpadu pemantauan dan pengelolaan pelaporan dokumen perencanaan pembangunan daerah yang dikembangkan oleh <strong>Bidang Perekonomian dan Perdagangan Bappeda Provinsi Maluku Utara</strong> secara tertib, terarah, dan transparan.
                    </p>

                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        @auth
                            <a class="btn-zen-primary" href="{{ route('dashboard') }}">
                                <span>Buka Dashboard</span>
                                <span class="text-sm font-bold leading-none">↗</span>
                            </a>
                        @else
                            <a class="btn-zen-primary" href="{{ route('login') }}">
                                <span>Masuk ke Portal</span>
                                <span class="text-sm font-bold leading-none">↗</span>
                            </a>
                        @endauth

                        <a class="btn-zen-secondary" href="#alur-pengumpulan">
                            <span>Pelajari Alur Pengumpulan</span>
                            <span class="text-sm font-bold leading-none">↗</span>
                        </a>

                        <a class="btn-zen-secondary" href="#metode-pengantaran">
                            <span>Pilihan Metode</span>
                            <span class="text-sm font-bold leading-none">↗</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hero Interactive 3D Showcase Card (Adapted with ZenABM dark aesthetic) -->
            <div class="mt-14 sm:mt-18 pt-10 border-t border-white/10 flex justify-center">
                <div class="w-full max-w-4xl p-6 sm:p-10 rounded-none bg-[#0c231b]/80 border border-white/15 relative overflow-hidden backdrop-blur-xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] transition-all duration-300 hover:border-[#4ADE80]/40" id="hero-3d-card-interactive" style="perspective: 1000px; transform-style: preserve-3d;">
                    <div class="shimmer-sweep"></div>
                    <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-[#4ADE80]/15 blur-2xl pointer-events-none"></div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                        <!-- Floating Logo Visual -->
                        <div class="lg:col-span-5 flex flex-col items-center justify-center">
                            <div class="relative w-44 h-44 sm:w-52 sm:h-52 flex items-center justify-center">
                                <div class="logo-float-anim relative w-full h-full flex items-center justify-center" id="hero-3d-logo-floating">
                                    <div class="logo-pulse-anim absolute inset-2 rounded-full bg-[#4ADE80]/20 blur-2xl pointer-events-none -z-10"></div>
                                    <img alt="Logo MONITORA" class="w-full h-full object-contain drop-shadow-[0_15px_30px_rgba(0,0,0,0.7)]" id="hero-3d-logo-img" src="{{ asset('logo.png') }}">
                                </div>
                            </div>
                            <div class="mt-2 text-center">
                                <span class="font-mono-tag text-[11px] uppercase tracking-widest text-[#4ADE80]">PORTAL RESMI &bull; VERI-SIGN QR</span>
                            </div>
                        </div>

                        <!-- Card Spec Details -->
                        <div class="lg:col-span-7 flex flex-col justify-center border-t lg:border-t-0 lg:border-l border-white/10 pt-6 lg:pt-0 lg:pl-8">
                            <div class="flex items-center justify-between mb-3">
                                <span class="bracket-tag font-mono-tag text-[#A3B8AD]">[ ARSITEKTUR LAYANAN ]</span>
                                <span class="font-mono-tag text-[10px] text-[#4ADE80] bg-[#4ADE80]/10 px-2 py-0.5 border border-[#4ADE80]/30">REAL-TIME SYNC</span>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold tracking-tight text-white mb-2">Koordinasi Satu Pintu &amp; Otomasi Verifikasi</h3>
                            <p class="text-xs sm:text-sm text-[#A3B8AD] leading-relaxed mb-5">
                                Mengintegrasikan penerbitan surat tugas resmi, pengingat tenggat WhatsApp Gateway, validasi berkas multi-kanal, hingga penerbitan tanda terima elektronik ber-QR Code sah.
                            </p>

                            <!-- Mini Specs Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <div class="p-3 bg-[#091A14]/70 border border-white/10">
                                    <span class="font-mono-tag text-[10px] text-[#A3B8AD] uppercase block">Kanal Pengiriman</span>
                                    <span class="text-xs font-bold text-white mt-1 block">Paperless &amp; Fisik</span>
                                </div>
                                <div class="p-3 bg-[#091A14]/70 border border-white/10">
                                    <span class="font-mono-tag text-[10px] text-[#A3B8AD] uppercase block">Validasi Berkas</span>
                                    <span class="text-xs font-bold text-[#4ADE80] mt-1 block">Tanda Terima QR</span>
                                </div>
                                <div class="p-3 bg-[#091A14]/70 border border-white/10 col-span-2 sm:col-span-1">
                                    <span class="font-mono-tag text-[10px] text-[#A3B8AD] uppercase block">Narahubung</span>
                                    <span class="text-xs font-bold text-white mt-1 block">WhatsApp Gateway</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- SECTION 3: THE PRODUCT EXPERIENCE / DASHBOARD MOCKUP (Dark Background)    -->
    <!-- ========================================================================= -->
    <section class="relative bg-[#091A14] text-white py-20 sm:py-28 border-b border-white/10" id="product-experience" data-purpose="product-experience">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Top Bar: Left "THE PRODUCT EXPERIENCE", Right "SCROLL TO EXPLORE ↓", thin border-top -->
            <div class="flex items-center justify-between pb-6 mb-12 border-b border-white/10">
                <span class="bracket-tag font-mono-tag text-[#A3B8AD]">
                    [ 02 ] THE PRODUCT EXPERIENCE
                </span>
                <span class="font-mono-tag text-xs tracking-wider text-[#A3B8AD] flex items-center gap-2">
                    SCROLL TO EXPLORE <span class="text-[#4ADE80] font-bold text-sm">↓</span>
                </span>
            </div>

            <!-- Visual Mockup: 2 Floating Cards on Dark Background (as requested in spec) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 mb-16 items-stretch">
                <!-- Left Card: Light Background Card ("Why did campaign performance drop?" adapted to planning governance) -->
                <div class="lg:col-span-6 bg-[#F8F9F5] text-[#0F172A] p-6 sm:p-8 rounded-none border border-black/10 shadow-2xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 mb-5 border-b border-black/10">
                            <span class="font-mono-tag text-[11px] font-bold tracking-widest text-slate-500 uppercase">[ SYSTEM INTELLIGENCE ]</span>
                            <span class="font-mono-tag text-[11px] font-bold px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-300">LIVE QUERY</span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#0F172A] mb-3">
                            "Bagaimana progres penyampaian dokumen evaluasi triwulan seluruh OPD?"
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-6">
                            MONITORA mendeteksi secara otomatis batas tenggat penyampaian berkas Renja &amp; LAKIP. Notifikasi WhatsApp Gateway terjadwal diteruskan ke PIC resmi dinas terkait dengan audit trail status verifikasi dokumen tanpa jeda.
                        </p>

                        <!-- Activity Timeline Preview inside Card -->
                        <div class="space-y-2.5 font-mono-tag text-xs">
                            <div class="p-3 bg-white border border-black/10 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span class="font-semibold text-slate-800">Dinas Perindustrian &amp; Perdagangan</span>
                                </div>
                                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5">Tervalidasi QR</span>
                            </div>
                            <div class="p-3 bg-white border border-black/10 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    <span class="font-semibold text-slate-800">Dinas Kelautan dan Perikanan</span>
                                </div>
                                <span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5">Sedang Ditelaah</span>
                            </div>
                            <div class="p-3 bg-white border border-black/10 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                                    <span class="font-semibold text-slate-800">Dinas Pariwisata Daerah</span>
                                </div>
                                <span class="text-[11px] font-bold text-teal-700 bg-teal-50 px-2 py-0.5">Draft Terunggah</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 mt-6 border-t border-black/10 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500">Siklus Koordinasi Otomatis</span>
                        <span class="font-mono-tag text-xs font-bold text-emerald-800">100% TERINTEGRASI</span>
                    </div>
                </div>

                <!-- Right Card: Dark High-Tech Performance Dip / Kepatuhan OPD Graph with Neon Accent -->
                <div class="lg:col-span-6 bg-[#0c231b] text-white p-6 sm:p-8 rounded-none border border-white/15 shadow-2xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 mb-5 border-b border-white/10">
                            <span class="font-mono-tag text-[11px] font-bold tracking-widest text-[#A3B8AD] uppercase">[ TINGKAT KEPATUHAN &amp; INTEGRASI ]</span>
                            <span class="font-mono-tag text-[11px] font-bold px-2 py-0.5 bg-[#4ADE80]/15 text-[#4ADE80] border border-[#4ADE80]/30">+18.4% BULAN INI</span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white mb-2">
                            Akselerasi Pengumpulan Berkas OPD
                        </h3>
                        <p class="text-xs sm:text-sm text-[#A3B8AD] leading-relaxed mb-6">
                            Penurunan drastis keterlambatan koordinasi fisik berkat saluran digital paperless dan monitoring langsung oleh Admin Bappeda Provinsi Maluku Utara.
                        </p>

                        <!-- Bar Graph Mockup with Neon Accent #4ADE80 -->
                        <div class="bg-[#091A14] p-5 border border-white/10 mb-4">
                            <div class="flex items-end justify-between gap-3 h-36 pt-6">
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <span class="font-mono-tag text-[10px] text-[#A3B8AD]">TW I</span>
                                    <div class="w-full bg-white/10 h-16 relative">
                                        <div class="w-full bg-[#A3B8AD] absolute bottom-0 h-14"></div>
                                    </div>
                                    <span class="font-mono-tag text-[10px] text-white/70">58%</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <span class="font-mono-tag text-[10px] text-[#A3B8AD]">TW II</span>
                                    <div class="w-full bg-white/10 h-24 relative">
                                        <div class="w-full bg-[#A3B8AD] absolute bottom-0 h-20"></div>
                                    </div>
                                    <span class="font-mono-tag text-[10px] text-white/70">76%</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <span class="font-mono-tag text-[10px] text-[#A3B8AD]">TW III</span>
                                    <div class="w-full bg-white/10 h-28 relative">
                                        <div class="w-full bg-[#4ADE80] absolute bottom-0 h-28 shadow-[0_0_15px_rgba(74,222,128,0.4)]"></div>
                                    </div>
                                    <span class="font-mono-tag text-[10px] text-[#4ADE80] font-bold">94%</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <span class="font-mono-tag text-[10px] text-[#A3B8AD]">TW IV</span>
                                    <div class="w-full bg-white/10 h-32 relative">
                                        <div class="w-full bg-[#4ADE80] absolute bottom-0 h-32 shadow-[0_0_20px_rgba(74,222,128,0.6)]"></div>
                                    </div>
                                    <span class="font-mono-tag text-[10px] text-[#4ADE80] font-bold">98%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-white/10 flex items-center justify-between font-mono-tag text-xs text-[#A3B8AD]">
                        <span>Tanda Terima QR Instan</span>
                        <span class="text-white font-semibold">Efisiensi Cetak: ~85%</span>
                    </div>
                </div>
            </div>

            <!-- 3 Prinsip Layanan (Preserving the core principles of MONITORA) -->
            <div id="prinsip" class="pt-10 border-t border-white/10">
                <div class="mb-8">
                    <span class="bracket-tag font-mono-tag text-[#A3B8AD]">[ PRINSIP TATA KELOLA LAYANAN ]</span>
                    <h3 class="text-2xl sm:text-3xl font-bold tracking-tight text-white mt-2">Komitmen Pengelolaan Perencanaan Daerah</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Prinsip 01 -->
                    <div class="p-6 bg-[#0c231b] border border-white/10 hover:border-[#4ADE80]/50 transition-all duration-300 group">
                        <div class="flex items-center justify-between mb-4">
                            <span class="font-mono-tag text-xl font-bold text-[#4ADE80]">01</span>
                            <span class="material-symbols-outlined text-[#4ADE80]">inventory_2</span>
                        </div>
                        <h4 class="text-base font-bold text-white tracking-tight mb-2 group-hover:text-[#4ADE80] transition-colors">Pelaporan Terpadu</h4>
                        <p class="text-xs sm:text-sm text-[#A3B8AD] leading-relaxed">
                            Satu pintu resmi pengumpulan seluruh dokumen perencanaan pembangunan daerah lingkup koordinasi Bidang Perekonomian dan Perdagangan.
                        </p>
                    </div>

                    <!-- Prinsip 02 -->
                    <div class="p-6 bg-[#0c231b] border border-white/10 hover:border-[#4ADE80]/50 transition-all duration-300 group">
                        <div class="flex items-center justify-between mb-4">
                            <span class="font-mono-tag text-xl font-bold text-[#4ADE80]">02</span>
                            <span class="material-symbols-outlined text-[#4ADE80]">verified</span>
                        </div>
                        <h4 class="text-base font-bold text-white tracking-tight mb-2 group-hover:text-[#4ADE80] transition-colors">Transparan &amp; Terarah</h4>
                        <p class="text-xs sm:text-sm text-[#A3B8AD] leading-relaxed">
                            Koordinasi terjadwal, akuntabel, dan terukur dengan pembaruan status verifikasi terbuka untuk OPD dan Tim Verifikator Bappeda.
                        </p>
                    </div>

                    <!-- Prinsip 03 -->
                    <div class="p-6 bg-[#0c231b] border border-white/10 hover:border-[#4ADE80]/50 transition-all duration-300 group">
                        <div class="flex items-center justify-between mb-4">
                            <span class="font-mono-tag text-xl font-bold text-[#4ADE80]">03</span>
                            <span class="material-symbols-outlined text-[#4ADE80]">rule</span>
                        </div>
                        <h4 class="text-base font-bold text-white tracking-tight mb-2 group-hover:text-[#4ADE80] transition-colors">Tertib Administrasi</h4>
                        <p class="text-xs sm:text-sm text-[#A3B8AD] leading-relaxed">
                            Mendukung kepatuhan regulasi, konsistensi data makro daerah, serta arsip digital yang rapi dan terverifikasi secara elektronik.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- SECTION 4: THE PROBLEM (Light Background #F8F9F5)                          -->
    <!-- ========================================================================= -->
    <section class="relative bg-[#F8F9F5] text-[#0F172A] py-20 sm:py-28 border-b border-black/10" id="the-problem" data-purpose="the-problem-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16">
                <!-- Label Kiri: [ 03 ] TANTANGAN TATA KELOLA -->
                <div class="lg:col-span-4 flex flex-col items-start">
                    <span class="bracket-tag font-mono-tag text-slate-500 mb-2">
                        [ 03 ] TANTANGAN TATA KELOLA
                    </span>
                    <span class="font-mono-tag text-xs tracking-widest text-[#059669] font-bold uppercase mb-4">
                        TATA KELOLA ADMINISTRASI SEBELUMNYA
                    </span>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-sm">
                        Kendala klasik yang kerap dihadapi perangkat daerah dalam pelaporan dokumen perencanaan pembangunan tahunan tanpa dukungan sistem terintegrasi.
                    </p>
                </div>

                <!-- Konten Kanan: Headline (H2) & Daftar List bergaris bawah tipis -->
                <div class="lg:col-span-8 flex flex-col">
                    <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-[#0F172A] leading-tight mb-10">
                        Stop Mengelola Pelaporan Perencanaan Secara Manual &amp; Tercecer
                    </h2>

                    <!-- Daftar Accordion / List bergaris bawah tipis -->
                    <div class="border-t border-black/10 divide-y divide-black/10">
                        <!-- Item 01 -->
                        <div class="py-5 sm:py-6 flex items-start gap-4 sm:gap-6 group">
                            <span class="font-mono-tag text-base sm:text-lg font-bold text-slate-400 group-hover:text-[#059669] transition-colors shrink-0">
                                01 /
                            </span>
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-[#0F172A] tracking-tight group-hover:text-emerald-800 transition-colors">
                                    Koordinasi penyampaian berkas fisik memakan waktu dan berisiko tercecer.
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mt-1.5">
                                    Jarak antar pulau dan kendala logistik antar dinas di Maluku Utara sering kali menghambat pengantaran fisik sebelum batas akhir siklus perencanaan.
                                </p>
                            </div>
                        </div>

                        <!-- Item 02 -->
                        <div class="py-5 sm:py-6 flex items-start gap-4 sm:gap-6 group">
                            <span class="font-mono-tag text-base sm:text-lg font-bold text-slate-400 group-hover:text-[#059669] transition-colors shrink-0">
                                02 /
                            </span>
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-[#0F172A] tracking-tight group-hover:text-emerald-800 transition-colors">
                                    Data kinerja evaluasi makro tersebar di berbagai saluran tanpa validasi satu pintu.
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mt-1.5">
                                    Pengiriman melalui email personal, WhatsApp chat, atau flashdisk menyulitkan rekonsiliasi data dan pemantauan versi dokumen final.
                                </p>
                            </div>
                        </div>

                        <!-- Item 03 -->
                        <div class="py-5 sm:py-6 flex items-start gap-4 sm:gap-6 group">
                            <span class="font-mono-tag text-base sm:text-lg font-bold text-slate-400 group-hover:text-[#059669] transition-colors shrink-0">
                                03 /
                            </span>
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-[#0F172A] tracking-tight group-hover:text-emerald-800 transition-colors">
                                    Batas tenggat (deadline) siklus perencanaan rawan terlewatkan.
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mt-1.5">
                                    Narahubung teknis OPD kerap berganti tugas tanpa serah terima riwayat koordinasi, menyebabkan ketidaksiapan saat asistensi Bappeda dimulai.
                                </p>
                            </div>
                        </div>

                        <!-- Item 04 -->
                        <div class="py-5 sm:py-6 flex items-start gap-4 sm:gap-6 group">
                            <span class="font-mono-tag text-base sm:text-lg font-bold text-slate-400 group-hover:text-[#059669] transition-colors shrink-0">
                                04 /
                            </span>
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-[#0F172A] tracking-tight group-hover:text-emerald-800 transition-colors">
                                    Verifikasi administrasi lambat tanpa bukti tanda terima digital yang akuntabel.
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mt-1.5">
                                    Lembar disposisi manual mudah hilang, menyulitkan pembuktian kepatuhan pengumpulan dokumen saat audit berkala dilaksanakan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- SECTION 5: FEATURES / 4 TAHAP ALUR PENGUMPULAN (Dark Background #091A14)  -->
    <!-- ========================================================================= -->
    <section class="relative bg-[#091A14] text-white py-20 sm:py-28 border-b border-white/10" id="alur-pengumpulan" data-purpose="features-workflow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 mb-12">
                <!-- Label Kiri: [ 04 ] ALUR KERJA TERPADU -->
                <div class="lg:col-span-4 flex flex-col items-start">
                    <span class="bracket-tag font-mono-tag text-[#A3B8AD] mb-2">
                        [ 04 ] ALUR KERJA TERPADU
                    </span>
                    <span class="font-mono-tag text-xs tracking-widest text-[#4ADE80] font-bold uppercase mb-4">
                        SISTEMATIKA &amp; PROSEDUR RESMI
                    </span>
                    <p class="text-xs sm:text-sm text-[#A3B8AD] leading-relaxed max-w-sm">
                        Panduan tata kelola dan alur kerja penyampaian dokumen rencana pembangunan daerah bagi seluruh Organisasi Perangkat Daerah (OPD) mitra Bappeda Maluku Utara.
                    </p>
                </div>

                <!-- Konten Kanan: Headline -->
                <div class="lg:col-span-8 flex flex-col justify-end">
                    <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
                        4 Tahap Alur Penyampaian <span class="text-[#4ADE80]">Dokumen Perencanaan</span>
                    </h2>
                </div>
            </div>

            <!-- Daftar Fitur (List disusun baris per baris / row layout sesuai Section 5) -->
            <div class="border-t border-white/10 divide-y divide-white/10">
                <!-- Row 01 -->
                <div class="py-7 sm:py-8 grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-8 items-start group hover:bg-white/[0.02] transition-colors">
                    <div class="lg:col-span-5 flex items-start gap-4">
                        <span class="font-mono-tag text-lg sm:text-xl font-bold text-[#4ADE80] shrink-0">01 /</span>
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-white tracking-tight group-hover:text-[#4ADE80] transition-colors">
                                Penerbitan Tugas &amp; Edaran
                            </h3>
                            <span class="font-mono-tag text-[10px] text-[#A3B8AD] uppercase tracking-wider block mt-1">TAHAP AWAL &bull; INSTRUKSI RESMI</span>
                        </div>
                    </div>
                    <div class="lg:col-span-7 flex flex-col gap-2.5">
                        <p class="text-xs sm:text-sm text-[#A3B8AD] leading-relaxed">
                            Mandat resmi instruksi pengumpulan dokumen diterbitkan oleh Bidang Perekonomian dan Perdagangan Bappeda via MONITORA serta notifikasi otomatis WhatsApp Gateway ke narahubung OPD mitra.
                        </p>
                        <div class="flex flex-wrap items-center gap-2 pt-1 font-mono-tag text-[10px]">
                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 text-slate-300">[ SURAT EDARAN ]</span>
                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 text-[#4ADE80]">[ WA GATEWAY OTOMATIS ]</span>
                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 text-slate-300">[ JADWAL SIKLUS ]</span>
                        </div>
                    </div>
                </div>

                <!-- Row 02 -->
                <div class="py-7 sm:py-8 grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-8 items-start group hover:bg-white/[0.02] transition-colors">
                    <div class="lg:col-span-5 flex items-start gap-4">
                        <span class="font-mono-tag text-lg sm:text-xl font-bold text-[#4ADE80] shrink-0">02 /</span>
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-white tracking-tight group-hover:text-[#4ADE80] transition-colors">
                                Validasi Mandiri Internal OPD
                            </h3>
                            <span class="font-mono-tag text-[10px] text-[#A3B8AD] uppercase tracking-wider block mt-1">KONSISTENSI DATA &bull; PENGESAHAN</span>
                        </div>
                    </div>
                    <div class="lg:col-span-7 flex flex-col gap-2.5">
                        <p class="text-xs sm:text-sm text-[#A3B8AD] leading-relaxed">
                            Tim kerja internal OPD menyusun dokumen teknis sesuai indikator kinerja makro daerah, memastikan keselarasan data antar bidang, dan pengesahan kepala dinas/badan.
                        </p>
                        <div class="flex flex-wrap items-center gap-2 pt-1 font-mono-tag text-[10px]">
                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 text-slate-300">[ INDIKATOR KINERJA ]</span>
                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 text-[#4ADE80]">[ TANDA TANGAN KEPALA DINAS ]</span>
                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 text-slate-300">[ REKONSILIASI SUBAG ]</span>
                        </div>
                    </div>
                </div>

                <!-- Row 03 -->
                <div class="py-7 sm:py-8 grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-8 items-start group hover:bg-white/[0.02] transition-colors">
                    <div class="lg:col-span-5 flex items-start gap-4">
                        <span class="font-mono-tag text-lg sm:text-xl font-bold text-[#4ADE80] shrink-0">03 /</span>
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-white tracking-tight group-hover:text-[#4ADE80] transition-colors">
                                Pengunggahan Berkas Multi-Kanal
                            </h3>
                            <span class="font-mono-tag text-[10px] text-[#A3B8AD] uppercase tracking-wider block mt-1">PAPERLESS ATAU LOKET FISIK</span>
                        </div>
                    </div>
                    <div class="lg:col-span-7 flex flex-col gap-2.5">
                        <p class="text-xs sm:text-sm text-[#A3B8AD] leading-relaxed">
                            Unggah berkas digital PDF/Excel melalui formulir daring terpadu atau penyerahan fisik ke loket Bidang Perekonomian dan Perdagangan Bappeda di Sofifi sesuai metode yang dipilih oleh OPD.
                        </p>
                        <div class="flex flex-wrap items-center gap-2 pt-1 font-mono-tag text-[10px]">
                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 text-slate-300">[ DARING 100% ]</span>
                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 text-[#4ADE80]">[ METODE HYBRID ]</span>
                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 text-slate-300">[ LOKET SOFIFI ]</span>
                        </div>
                    </div>
                </div>

                <!-- Row 04 -->
                <div class="py-7 sm:py-8 grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-8 items-start group hover:bg-white/[0.02] transition-colors">
                    <div class="lg:col-span-5 flex items-start gap-4">
                        <span class="font-mono-tag text-lg sm:text-xl font-bold text-[#4ADE80] shrink-0">04 /</span>
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-white tracking-tight group-hover:text-[#4ADE80] transition-colors">
                                Validasi &amp; Tanda Terima QR
                            </h3>
                            <span class="font-mono-tag text-[10px] text-[#A3B8AD] uppercase tracking-wider block mt-1">LEGALITAS DIGITAL &bull; AUDIT TRAIL</span>
                        </div>
                    </div>
                    <div class="lg:col-span-7 flex flex-col gap-2.5">
                        <p class="text-xs sm:text-sm text-[#A3B8AD] leading-relaxed">
                            Tim verifikator Bidang Perekonomian dan Perdagangan Bappeda menelaah berkas. Berkas yang lolos langsung memperoleh Tanda Terima Elektronik ber-QR Code sah dan dapat diunduh seketika.
                        </p>
                        <div class="flex flex-wrap items-center gap-2 pt-1 font-mono-tag text-[10px]">
                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 text-[#4ADE80]">[ TANDA TERIMA ELEKTRONIK ]</span>
                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 text-[#4ADE80]">[ QR CODE VERIFIKASI ]</span>
                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 text-slate-300">[ AKUNTABILITAS TINGGI ]</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- SECTION 6: METODE PENGANTARAN BERKAS (Light Background #F8F9F5)           -->
    <!-- ========================================================================= -->
    <section class="relative bg-[#F8F9F5] text-[#0F172A] py-20 sm:py-28 border-b border-black/10" id="metode-pengantaran" data-purpose="submission-methods">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
                <!-- Label Kiri: [ 05 ] METODE PENGANTARAN -->
                <div class="lg:col-span-4 flex flex-col items-start">
                    <span class="bracket-tag font-mono-tag text-slate-500 mb-2">
                        [ 05 ] METODE PENGANTARAN
                    </span>
                    <span class="font-mono-tag text-xs tracking-widest text-[#059669] font-bold uppercase mb-4">
                        PILIHAN FLEKSIBEL OPD
                    </span>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-sm">
                        OPD dapat memilih metode yang paling efisien sesuai regulasi dokumen dan ketersediaan tanda tangan elektronik resmi.
                    </p>
                </div>

                <!-- Headline Kanan -->
                <div class="lg:col-span-8 flex flex-col justify-end">
                    <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-[#0F172A] leading-tight">
                        Pilihan Fleksibel Penyampaian Dokumen ke Bappeda
                    </h2>
                </div>
            </div>

            <!-- 3 Kolom Kartu (Cards) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1: 01 - Digital Penuh (Paperless) -->
                <div class="p-7 sm:p-8 bg-white border border-black/10 flex flex-col justify-between shadow-sm hover:border-[#059669] hover:shadow-lg transition-all duration-300 relative group">
                    <div>
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-black/10">
                            <span class="font-mono-tag text-xl font-bold text-[#059669]">01</span>
                            <span class="font-mono-tag text-[10px] font-bold px-2 py-0.5 bg-emerald-100 text-emerald-800 uppercase">[ REKOMENDASI UTAMA ]</span>
                        </div>
                        <h3 class="text-lg font-bold text-[#0F172A] tracking-tight mb-2 group-hover:text-emerald-700 transition-colors">
                            Digital Penuh (Paperless)
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-6">
                            Pengunggahan dokumen PDF bertanda tangan digital (TTE BSrE) atau cap basah hasil pindai langsung ke portal MONITORA tanpa berkas fisik. Tanda terima terbit seketika, hemat biaya cetak.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-black/10 flex items-center justify-between font-mono-tag text-xs text-slate-600">
                        <span>Waktu Proses: Cepat</span>
                        <span class="text-emerald-700 font-bold">100% Online</span>
                    </div>
                </div>

                <!-- Card 2: 02 - Metode Hybrid -->
                <div class="p-7 sm:p-8 bg-white border border-black/10 flex flex-col justify-between shadow-sm hover:border-slate-800 hover:shadow-lg transition-all duration-300 relative group">
                    <div>
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-black/10">
                            <span class="font-mono-tag text-xl font-bold text-slate-800">02</span>
                            <span class="font-mono-tag text-[10px] font-bold px-2 py-0.5 bg-slate-100 text-slate-700 uppercase">[ DRAFT &amp; ASISTENSI ]</span>
                        </div>
                        <h3 class="text-lg font-bold text-[#0F172A] tracking-tight mb-2 group-hover:text-slate-900 transition-colors">
                            Metode Hybrid
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-6">
                            Unggah dokumen draft di portal untuk pra-verifikasi oleh tim Bappeda, disusul penyerahan eksemplar jilid resmi saat tahap finalisasi asistensi. Koreksi cepat sebelum cetak jilid.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-black/10 flex items-center justify-between font-mono-tag text-xs text-slate-600">
                        <span>Fleksibel &amp; Bertahap</span>
                        <span class="text-slate-800 font-bold">Pra-Verifikasi</span>
                    </div>
                </div>

                <!-- Card 3: 03 - Loket Terpadu Sofifi -->
                <div id="loket" class="p-7 sm:p-8 bg-white border border-black/10 flex flex-col justify-between shadow-sm hover:border-slate-800 hover:shadow-lg transition-all duration-300 relative group">
                    <div>
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-black/10">
                            <span class="font-mono-tag text-xl font-bold text-slate-800">03</span>
                            <span class="font-mono-tag text-[10px] font-bold px-2 py-0.5 bg-slate-100 text-slate-700 uppercase">[ LAYANAN TATAP MUKA ]</span>
                        </div>
                        <h3 class="text-lg font-bold text-[#0F172A] tracking-tight mb-2 group-hover:text-slate-900 transition-colors">
                            Loket Terpadu Sofifi
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-6">
                            Penyerahan berkas fisik langsung ke Front Office / Ruang Bidang Perekonomian dan Perdagangan Gedung Bappeda Maluku Utara, Jl. Gosale Puncak No. 1, dengan pencatatan tanda terima oleh petugas. Jam layanan Senin-Jumat 08.00-16.00 WIT.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-black/10 flex items-center justify-between font-mono-tag text-xs text-slate-600">
                        <span>Front Office Sofifi</span>
                        <span class="text-slate-800 font-bold">Senin-Jumat</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- SECTION 7: FOOTER CTA & INSTITUTIONAL FOOTER (Dark Background #091A14)    -->
    <!-- ========================================================================= -->
    <footer class="relative bg-[#091A14] text-white pt-20 pb-12 sm:pt-28 sm:pb-16 overflow-hidden" id="footer-tentang" data-purpose="official-footer">
        <!-- Aurora Glowing Green Gradient at Bottom Corners -->
        <div class="aurora-glow-footer -top-32 -left-32"></div>
        <div class="aurora-glow-footer -bottom-32 -right-32"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Label Tag: [ 06 ] AKSES RUANG KERJA -->
            <div class="mb-6">
                <span class="bracket-tag font-mono-tag text-[#A3B8AD]">
                    [ 06 ] AKSES RUANG KERJA
                </span>
            </div>

            <!-- Big Headline -->
            <div class="max-w-4xl mb-10">
                <h2 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                    Optimalisasi Pengelolaan Pelaporan Perencanaan Sekarang
                </h2>
            </div>

            <!-- Dua Kolom Deskripsi Teks & Baris Aksi -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 pb-16 border-b border-white/10 items-end">
                <div class="lg:col-span-4">
                    <p class="text-xs sm:text-sm text-[#A3B8AD] leading-relaxed">
                        MONITORA mempermudah koordinasi seluruh Organisasi Perangkat Daerah (OPD) mitra dalam menyelaraskan dokumen perencanaan tahunan secara tertib, akuntabel, dan transparan.
                    </p>
                </div>
                <div class="lg:col-span-4">
                    <p class="text-xs sm:text-sm text-[#A3B8AD] leading-relaxed">
                        Akses ruang kerja pelaporan menggunakan akun resmi subag perencanaan/evaluasi OPD untuk memulai penyampaian berkas dan pelacakan status verifikasi bersama Bidang Perekonomian dan Perdagangan.
                    </p>
                </div>
                <div class="lg:col-span-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    @auth
                        <a class="btn-zen-primary w-full sm:w-auto" href="{{ route('dashboard') }}">
                            <span>Ke Dashboard</span>
                            <span class="text-sm font-bold leading-none">↗</span>
                        </a>
                    @else
                        <a class="btn-zen-primary w-full sm:w-auto" href="{{ route('login') }}">
                            <span>Masuk ke Portal</span>
                            <span class="text-sm font-bold leading-none">↗</span>
                        </a>
                    @endauth

                    <a class="btn-zen-secondary w-full sm:w-auto" href="#hero">
                        <span>Kembali ke Atas</span>
                        <span class="text-sm font-bold leading-none">↑</span>
                    </a>
                </div>
            </div>

            <!-- Footer Paling Bawah: Logo, Link, Alamat, Copyright -->
            <div class="pt-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 text-xs text-[#A3B8AD]">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-none bg-[#0e271e] border border-white/10 flex items-center justify-center p-1">
                        <img alt="MONITORA" class="w-full h-full object-contain" src="{{ asset('logo.png') }}">
                    </div>
                    <div>
                        <span class="font-bold text-white tracking-tight block">MONITORA</span>
                        <span class="text-[11px] text-[#A3B8AD]">Bidang Perekonomian dan Perdagangan &bull; Bappeda Provinsi Maluku Utara</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-6 font-mono-tag text-[11px]">
                    <span class="flex items-center gap-1.5 text-slate-300">
                        <span class="material-symbols-outlined text-sm text-[#4ADE80]">location_on</span>
                        Jl. Gosale Puncak No. 1, Kota Sofifi, Maluku Utara
                    </span>
                    <span class="text-[#A3B8AD]">&copy; {{ date('Y') }} MONITORA. Seluruh hak cipta dilindungi.</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- ========================================================================= -->
    <!-- INTERACTIVE 3D TILT SCRIPT                                                -->
    <!-- ========================================================================= -->
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

                // Calculate tilt angles (range approx -8 to +8 deg)
                const rotateX = ((y - centerY) / centerY) * -8;
                const rotateY = ((x - centerX) / centerX) * 8;

                card.style.transform = `perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) scale3d(1.01, 1.01, 1.01)`;
            });

            card.addEventListener('mouseleave', () => {
                isHovering = false;
                card.style.transition = 'transform 0.5s ease-out, box-shadow 0.3s ease, border-color 0.3s ease';
                card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
            });
        });
    </script>
</body>
</html>
