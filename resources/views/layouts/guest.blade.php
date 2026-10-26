<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Masuk – MONITORA Bidang Perekonomian Dan Perdagangan</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Google Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Material Symbols -->
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

        <!-- Vite Assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #061510 inset !important;
            -webkit-text-fill-color: #FFFFFF !important;
            caret-color: #4ADE80 !important;
            transition: background-color 5000s ease-in-out 0s !important;
        }
        input {
            background-color: #061510 !important;
            color: #FFFFFF !important;
        }
    </style>

    </head>
    <body class="min-h-screen flex flex-col font-sans text-slate-800 antialiased"
          style="background-color: #091A14; min-height: 100vh;">

        <!-- Top Navbar -->
        
        <!-- Aurora Glow Effect (ZenABM) -->
        <div style="position: fixed; top: -150px; left: 50%; transform: translateX(-50%); width: 700px; height: 700px; border-radius: 9999px; background: radial-gradient(circle, rgba(74, 222, 128, 0.16) 0%, rgba(16, 185, 129, 0.08) 40%, transparent 70%); filter: blur(75px); pointer-events: none; z-index: 0;"></div>
        <header class="w-full pt-3 sm:pt-4 px-3 sm:px-6 lg:px-8 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto bg-[#061510]/80 backdrop-blur-xl rounded-full border border-white/10 shadow-2xl px-4 sm:px-6 h-14 sm:h-16 flex items-center justify-between">
                <a class="flex items-center gap-2 sm:gap-3 group" href="{{ route('welcome') }}">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-xl overflow-hidden border border-emerald-500/20 bg-emerald-950/40 p-1 flex items-center justify-center shrink-0">
                        <img alt="MONITORA Logo" class="w-full h-full object-contain rounded-lg transition-transform group-hover:scale-105" src="{{ asset('logo.png') }}">
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-base sm:text-lg font-extrabold tracking-tight text-white leading-none">MONITORA</span>
                            <span class="hidden sm:inline px-2 py-0.5 rounded-full bg-emerald-400/20 border border-emerald-400/30 text-emerald-300 text-[10px] font-extrabold tracking-wider uppercase">DRIVE</span>
                        </div>
                        <p class="text-[10px] sm:text-[11px] text-emerald-200/80 font-medium mt-0.5 hidden sm:block">Bidang Perekonomian &amp; Perdagangan Bappeda Malut</p>
                    </div>
                </a>
                <div class="flex items-center gap-2">
                    <!-- Server Status - hidden on small screens -->
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-950/60 border border-emerald-500/30 text-xs font-medium text-emerald-200">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        </span>
                        <span>Sofifi Server WIT Sync</span>
                    </div>
                    <a href="{{ route('welcome') }}" class="inline-flex items-center gap-1 sm:gap-1.5 px-3 sm:px-3.5 py-1.5 text-xs font-semibold text-emerald-100 hover:text-white bg-emerald-800/40 hover:bg-emerald-800/70 border border-emerald-500/30 rounded-full transition-all shadow-sm">
                        <span class="material-symbols-outlined text-[15px] sm:text-[17px] text-emerald-300">arrow_back</span>
                        <span class="hidden xs:inline sm:inline">Kembali</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow flex items-center justify-center px-4 py-6 sm:py-12">
            <div class="w-full max-w-[500px] relative">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full bg-[#061510]/90 backdrop-blur-md border-t border-white/10 py-3 sm:py-4 px-4 sm:px-8 mt-auto shrink-0">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-1.5 sm:gap-3 text-emerald-100/70 text-[10px] sm:text-[11px]">
                <div class="text-center sm:text-left leading-relaxed">
                    &copy; {{ date('Y') }} MONITORA &bull; Bidang Perekonomian dan Perdagangan, Bappeda Provinsi Maluku Utara
                </div>
                <div class="hidden sm:flex items-center gap-3 shrink-0 text-emerald-200/80">
                    <span>Jalan Gosale Puncak No. 1, Kota Sofifi, Maluku Utara</span>
                    <span class="w-1 h-1 rounded-full bg-emerald-400/50"></span>
                    <span class="font-bold text-emerald-300">Versi 3.4.2-WIT</span>
                </div>
            </div>
        </footer>
    </body>
</html>
