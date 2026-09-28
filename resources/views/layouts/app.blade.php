<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MONITORA') }} - BAPPEDA Maluku Utara</title>

        <!-- Google Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                font-family: 'Inter', system-ui, -apple-system, sans-serif;
            }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-800 bg-slate-50 min-h-full flex flex-col">
        @include('layouts.navigation')

        <!-- Flash Messages -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-4">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 shadow-sm transition">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-700 font-bold shrink-0">
                            ✓
                        </span>
                        <div class="text-sm font-semibold leading-relaxed">
                            {{ session('success') }}
                        </div>
                    </div>
                    <button @click="show = false" class="text-blue-500 hover:text-blue-700 text-lg leading-none p-1">
                        &times;
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm transition">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center text-rose-600 font-bold shrink-0">
                            !
                        </span>
                        <div class="text-sm font-medium leading-relaxed">
                            {{ session('error') }}
                        </div>
                    </div>
                    <button @click="show = false" class="text-rose-500 hover:text-rose-700 text-lg leading-none p-1">
                        &times;
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div x-data="{ show: true }" x-show="show" class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 shadow-sm transition">
                    <div class="flex items-center gap-2 font-semibold text-sm mb-1">
                        <span>⚠️ Terjadi beberapa kesalahan pengisian form:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 mt-1 text-amber-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white border-b border-slate-200 shadow-sm">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Main Content Slot -->
        <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="mt-auto bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p>&copy; {{ date('Y') }} <strong>MONITORA</strong> - Badan Perencanaan Pembangunan Daerah (BAPPEDA) Provinsi Maluku Utara</p>
                <p class="text-slate-400">Bidang Perekonomian & Perdagangan | Kolaborasi Prodi Teknik Informatika Universitas Khairun</p>
            </div>
        </footer>
        <script>
            // Auto-polling untuk mengecek apakah sesi masih aktif.
            // Jika login di perangkat lain, session akan invalid, dan fetch mengembalikan 401.
            // Saat mendapat 401, kita me-reload halaman agar notifikasi logout muncul.
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
            }, 20000); // Cek setiap 20 detik
        </script>
    </body>
</html>
