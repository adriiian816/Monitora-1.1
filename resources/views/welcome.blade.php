<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MONITORA - Sistem Monitoring & Pengingat Pengumpulan Tugas OPD | BAPPEDA Maluku Utara</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    </style>
</head>
<body class="min-h-full bg-slate-50 text-slate-800 antialiased flex flex-col selection:bg-blue-600 selection:text-white">
    <!-- Top Navigation Header -->
    <header class="bg-blue-950 text-white border-b border-blue-900 shadow-md relative z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 to-sky-500 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-blue-600/30">
                    M
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-black text-lg tracking-wider text-white">MONITORA</span>
                        <span class="text-[10px] uppercase font-bold tracking-widest px-2 py-0.5 rounded bg-sky-500/20 text-sky-300 border border-sky-400/30">
                            BAPPEDA
                        </span>
                    </div>
                    <span class="text-[11px] text-blue-200 font-medium hidden sm:block">Pemerintah Provinsi Maluku Utara</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md transition">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-400 text-blue-950 font-black text-xs shadow-md transition">
                        Masuk ke Portal
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-b from-blue-950 via-blue-900 to-slate-900 text-white py-16 sm:py-20 px-4 sm:px-6 lg:px-8 text-center relative overflow-hidden">
        <div class="max-w-4xl mx-auto relative z-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-500/10 border border-sky-400/30 text-sky-300 text-xs font-bold uppercase tracking-wider mb-6">
                <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                Bidang Perekonomian dan Perdagangan
            </div>

            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                Monitoring & Pengingat Pengumpulan <span class="bg-gradient-to-r from-sky-400 to-blue-200 bg-clip-text text-transparent">Tugas OPD</span>
            </h1>

            <p class="mt-5 text-base sm:text-lg text-blue-100/90 max-w-2xl mx-auto leading-relaxed font-medium">
                Sistem informasi terpadu Badan Perencanaan dan Pembangunan Daerah Provinsi Maluku Utara untuk memantau kepatuhan pengumpulan dokumen, integrasi berkas fisik & digital, serta otomasi notifikasi pengingat WhatsApp.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('login') }}" 
                   class="px-8 py-3.5 rounded-2xl bg-sky-400 hover:bg-sky-300 text-blue-950 font-black text-sm shadow-lg shadow-sky-500/25 active:scale-95 transition">
                    Masuk ke Portal MONITORA
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <main class="flex-1 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-20 pb-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
            <!-- Feature 1: Fleksibilitas 3 Format -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-md hover:shadow-lg transition">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl mb-4 font-bold">
                    📁
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2">3 Format Pengumpulan Fleksibel</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-normal">
                    Mendukung upload berkas digital (PDF, Word, Excel, ZIP), konfirmasi penyerahan fisik langsung ke loket Bappeda, maupun kombinasi keduanya (hybrid).
                </p>
            </div>

            <!-- Feature 2: WhatsApp Reminder -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-md hover:shadow-lg transition">
                <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center text-xl mb-4 font-bold">
                    💬
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2">Pengingat WhatsApp Otomatis</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-normal">
                    Pengingat berkala H-7, H-3, H-1, Hari-H (H-0), hingga peringatan keterlambatan langsung ke nomor WhatsApp PIC OPD dengan catatan audit transparan.
                </p>
            </div>

            <!-- Feature 3: Integrasi Kalender .ICS -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-md hover:shadow-lg transition">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-800 flex items-center justify-center text-xl mb-4 font-bold">
                    📅
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2">Sinkronisasi Kalender (.ics)</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-normal">
                    Tiap penugasan dapat diunduh dalam format standar iCalendar (.ics) untuk disimpan langsung ke Google Calendar atau Microsoft Outlook PIC OPD.
                </p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-auto bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} MONITORA - BAPPEDA Provinsi Maluku Utara | Bidang Perekonomian dan Perdagangan | Kolaborasi Prodi Teknik Informatika Universitas Khairun</p>
    </footer>
</body>
</html>
