<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekapitulasi Kinerja Anggota Bidang – 1 Bulan Terakhir | MONITORA</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --kop-emerald: #064e3b;
            --kop-border: #047857;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .font-mono-tag {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                color: #0f172a !important;
            }
            .print-sheet {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 10mm 12mm !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            .page-break {
                page-break-after: always;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
        }
    </style>
</head>
<body class="bg-[#0b1e17] text-slate-900 min-h-screen">

    <!-- ========== TOP INTERACTIVE CONTROL BAR (NO-PRINT) ========== -->
    <header class="no-print sticky top-0 z-50 bg-[#091A14]/95 backdrop-blur-md border-b border-[#4ADE80]/20 px-4 py-3 shadow-xl">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-3">
            
            <!-- Left: Back Button & Title -->
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-[#A3B8AD] hover:text-white text-xs font-semibold border border-white/10 transition cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    <span>Kembali ke Dashboard</span>
                </a>
                <div class="h-6 w-px bg-white/20 hidden sm:block"></div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-white text-sm font-bold tracking-tight">Cetak Rekapitulasi Kinerja Anggota Bidang</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-indigo-500/20 text-indigo-300 border border-indigo-400/30 font-mono-tag">
                            Bidang Ekop
                        </span>
                    </div>
                    <p class="text-[11px] text-[#A3B8AD]">Evaluasi Kinerja, Konfirmasi Kehadiran &amp; Pelaporan Staf (1 Bulan Terakhir)</p>
                </div>
            </div>

            <!-- Center: Filter Presets -->
            <form method="GET" action="{{ route('admin.rekapitulasi.bidang') }}" class="flex flex-wrap items-center gap-1.5">
                <input type="hidden" name="period" id="periodInput" value="{{ $period }}">

                <div class="bg-white/5 border border-white/10 p-1 rounded-xl flex items-center gap-1">
                    <a href="{{ route('admin.rekapitulasi.bidang', ['period' => '30_days']) }}"
                       class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $period === '30_days' ? 'bg-[#4ADE80] text-[#091A14] font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        1 Bulan Terakhir
                    </a>
                    <a href="{{ route('admin.rekapitulasi.bidang', ['period' => 'this_month']) }}"
                       class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $period === 'this_month' ? 'bg-[#4ADE80] text-[#091A14] font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        Bulan Ini
                    </a>
                    <a href="{{ route('admin.rekapitulasi.bidang', ['period' => 'prev_month']) }}"
                       class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $period === 'prev_month' ? 'bg-[#4ADE80] text-[#091A14] font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        Bulan Lalu
                    </a>
                </div>

                <!-- Custom Range inputs -->
                <div class="hidden lg:flex items-center gap-1.5 bg-white/5 border border-white/10 px-2 py-1 rounded-xl text-xs text-slate-300">
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                           class="bg-transparent border-none text-xs text-white p-0 focus:ring-0 cursor-pointer">
                    <span class="text-slate-500">s/d</span>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                           class="bg-transparent border-none text-xs text-white p-0 focus:ring-0 cursor-pointer">
                    <button type="submit" onclick="document.getElementById('periodInput').value='custom';"
                            class="px-2 py-0.5 rounded bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] transition cursor-pointer">
                        Filter
                    </button>
                </div>
            </form>

            <!-- Right: Print Action -->
            <div class="flex items-center gap-2">
                <button onclick="window.print()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-[#4ADE80] hover:bg-[#38c86d] text-[#091A14] font-bold text-xs shadow-[0_0_20px_rgba(74,222,128,0.3)] transition transform hover:scale-[1.02] cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    <span>Cetak Rekapitulasi</span>
                    <span class="hidden sm:inline-block px-1.5 py-0.5 rounded bg-[#091A14]/20 text-[10px] font-mono-tag">Ctrl+P</span>
                </button>
            </div>
        </div>
    </header>

    <!-- ========== MAIN PRINT CANVAS (Official Paper Sheet Simulation) ========== -->
    <main class="py-6 sm:py-8 px-2 sm:px-4 flex justify-center">
        <div class="print-sheet w-full max-w-5xl bg-white shadow-2xl rounded-2xl p-6 sm:p-10 md:p-12 border border-slate-200 text-slate-800">

            <!-- ===== 1. KOP SURAT RESMI PEMERINTAH PROVINSI MALUKU UTARA ===== -->
            <div class="border-b-4 border-double border-[#064e3b] pb-4 mb-6">
                <div class="flex items-center justify-between gap-4">
                    <!-- Left: Logo MONITORA / Lambang Daerah -->
                    <div class="w-20 h-20 shrink-0 flex items-center justify-center">
                        <img src="{{ asset('logo.png') }}" alt="Logo MONITORA" class="w-18 h-18 object-contain">
                    </div>

                    <!-- Center: Text Kop Surat -->
                    <div class="flex-1 text-center">
                        <h3 class="text-[13px] sm:text-[14px] font-extrabold tracking-wider text-slate-800 uppercase">
                            PEMERINTAH PROVINSI MALUKU UTARA
                        </h3>
                        <h1 class="text-[16px] sm:text-[20px] font-black tracking-tight text-[#064e3b] uppercase leading-tight my-0.5">
                            BADAN PERENCANAAN PEMBANGUNAN DAERAH (BAPPEDA)
                        </h1>
                        <h2 class="text-[12px] sm:text-[14px] font-bold text-slate-700 uppercase tracking-wide">
                            BIDANG PEREKONOMIAN DAN PERDAGANGAN (EKOP)
                        </h2>
                        <p class="text-[10px] sm:text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Jalan Gosale Puncak No. 1, Kota Sofifi, Maluku Utara • Kode Pos 97827<br>
                            Sistem Monitoring Kinerja Internal: <span class="font-bold text-[#064e3b]">MONITORA DRIVE</span> • Penugasan &amp; Pelaporan Kegiatan Bidang
                        </p>
                    </div>

                    <!-- Right: Official Badge / Logo Sekunder -->
                    <div class="w-20 h-20 shrink-0 hidden sm:flex flex-col items-center justify-center p-2 rounded-xl bg-indigo-50/60 border border-indigo-200/80 text-center">
                        <span class="text-[8.5px] font-extrabold text-indigo-900 tracking-wider uppercase font-mono-tag">INTERNAL BIDANG</span>
                        <span class="material-symbols-outlined text-[26px] text-indigo-700 my-0.5">assignment_ind</span>
                        <span class="text-[7.5px] font-bold text-slate-500 uppercase">E-KINERJA</span>
                    </div>
                </div>
            </div>

            <!-- ===== 2. JUDUL DOKUMEN & METADATA EVALUASI ===== -->
            <div class="text-center mb-6">
                <h2 class="text-[16px] sm:text-[18px] font-black uppercase text-[#064e3b] tracking-wide underline underline-offset-4">
                    REKAPITULASI EVALUASI KINERJA &amp; PRODUKTIVITAS ANGGOTA BIDANG
                </h2>
                <div class="text-[12px] sm:text-[13px] font-bold text-slate-700 mt-1">
                    Periode Penilaian: <span class="text-indigo-800 bg-indigo-50 px-2.5 py-0.5 rounded border border-indigo-200">{{ $periodLabel }}</span>
                </div>
            </div>

            <!-- Metadata Dokumen Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 border border-slate-200 rounded-xl p-3 mb-6 text-[11px]">
                <div>
                    <span class="text-slate-400 block uppercase font-bold text-[9.5px]">Nomor Dokumen</span>
                    <span class="font-bold font-mono-tag text-slate-800">REKAP-BIDANG/{{ date('Ym') }}/{{ str_pad((string) $totalAnggota, 3, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block uppercase font-bold text-[9.5px]">Waktu Cetak Dokumen</span>
                    <span class="font-bold text-slate-800">{{ now()->translatedFormat('d F Y, H:i') }} WIT</span>
                </div>
                <div>
                    <span class="text-slate-400 block uppercase font-bold text-[9.5px]">Pencetak / Penanggung Jawab</span>
                    <span class="font-bold text-[#064e3b]">{{ Auth::user()->name }} (Super Admin)</span>
                </div>
                <div>
                    <span class="text-slate-400 block uppercase font-bold text-[9.5px]">Status Validasi</span>
                    <span class="inline-flex items-center gap-1 font-bold text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Data Terverifikasi Resmi
                    </span>
                </div>
            </div>

            <!-- ===== 3. EXECUTIVE SUMMARY / RINGKASAN CAPAIAN KINERJA (KPI CARDS) ===== -->
            <div class="mb-6">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2.5 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-indigo-700">insights</span>
                    <span>Ringkasan Eksekutif Penugasan &amp; Kinerja Anggota Bidang (1 Bulan)</span>
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-6 gap-2.5">
                    <!-- Total Anggota -->
                    <div class="bg-indigo-50/70 border border-indigo-200 rounded-xl p-3 text-center">
                        <div class="text-[10px] font-bold text-indigo-800 uppercase tracking-wider">Total Personel</div>
                        <div class="text-2xl font-black text-indigo-950 my-0.5 leading-none">{{ $totalAnggota }}</div>
                        <div class="text-[9.5px] text-indigo-600 font-semibold">Staf Bidang Ekop</div>
                    </div>

                    <!-- Total Agenda Penugasan -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-center">
                        <div class="text-[10px] font-bold text-slate-600 uppercase tracking-wider">Agenda Kegiatan</div>
                        <div class="text-2xl font-black text-slate-800 my-0.5 leading-none">{{ $totalKegiatan }}</div>
                        <div class="text-[9.5px] text-slate-500 font-semibold">Diterbitkan 1 Bulan</div>
                    </div>

                    <!-- Total Disposisi Personel -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-center">
                        <div class="text-[10px] font-bold text-slate-600 uppercase tracking-wider">Disposisi Tugas</div>
                        <div class="text-2xl font-black text-slate-800 my-0.5 leading-none">{{ $totalDisposisiPersonel }}</div>
                        <div class="text-[9.5px] text-slate-500 font-semibold">Total Tugas x Staf</div>
                    </div>

                    <!-- Kegiatan Selesai -->
                    <div class="bg-emerald-50/70 border border-emerald-200 rounded-xl p-3 text-center">
                        <div class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Kegiatan Selesai</div>
                        <div class="text-2xl font-black text-emerald-700 my-0.5 leading-none">{{ $totalSelesai }}</div>
                        <div class="text-[9.5px] text-emerald-600 font-semibold">{{ $overallPenyelesaianRate }}% Terlaksana</div>
                    </div>

                    <!-- Laporan Masuk -->
                    <div class="bg-teal-50/70 border border-teal-200 rounded-xl p-3 text-center">
                        <div class="text-[10px] font-bold text-teal-800 uppercase tracking-wider">Laporan Masuk</div>
                        <div class="text-2xl font-black text-teal-700 my-0.5 leading-none">{{ $totalLaporanSudah }}</div>
                        <div class="text-[9.5px] text-teal-600 font-semibold">{{ $overallPelaporanRate }}% Dilaporkan</div>
                    </div>

                    <!-- Rata-Rata Produktivitas -->
                    <div class="bg-[#064e3b] text-white rounded-xl p-3 text-center col-span-2 sm:col-span-1 shadow-xs">
                        <div class="text-[10px] font-bold text-[#4ADE80] uppercase tracking-wider">Produktivitas</div>
                        <div class="text-2xl font-black text-white my-0.5 leading-none">{{ $overallProductivityRate }}%</div>
                        <div class="text-[9.5px] text-emerald-200 font-semibold">Skor Gabungan</div>
                    </div>
                </div>

                <!-- Breakdown Status Kegiatan & Laporan -->
                <div class="mt-3 bg-white border border-slate-200 rounded-xl p-3">
                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-700 mb-1.5">
                        <span>Distribusi Pelaksanaan &amp; Pelaporan Tugas Bidang:</span>
                        <span class="text-slate-500 font-mono-tag text-[10px]">Total: {{ $totalDisposisiPersonel }} Disposisi Personel</span>
                    </div>

                    <div class="w-full h-3 rounded-full bg-slate-100 overflow-hidden flex shadow-inner">
                        @php
                            $pctSelesaiLapor = $totalDisposisiPersonel > 0 ? ($totalLaporanSudah / $totalDisposisiPersonel) * 100 : 0;
                            $pctSelesaiBelum = $totalDisposisiPersonel > 0 ? (max(0, $totalSelesai - $totalLaporanSudah) / $totalDisposisiPersonel) * 100 : 0;
                            $pctSedang = $totalDisposisiPersonel > 0 ? ($totalSedang / $totalDisposisiPersonel) * 100 : 0;
                            $pctBelum = $totalDisposisiPersonel > 0 ? ($totalBelum / $totalDisposisiPersonel) * 100 : 0;
                            $pctDitolak = $totalDisposisiPersonel > 0 ? ($totalDitolak / $totalDisposisiPersonel) * 100 : 0;
                        @endphp
                        @if($pctSelesaiLapor > 0)<div style="width: {{ $pctSelesaiLapor }}%" class="bg-emerald-500" title="Selesai & Dilaporkan: {{ $totalLaporanSudah }}"></div>@endif
                        @if($pctSelesaiBelum > 0)<div style="width: {{ $pctSelesaiBelum }}%" class="bg-teal-400" title="Selesai (Belum Lapor)"></div>@endif
                        @if($pctSedang > 0)<div style="width: {{ $pctSedang }}%" class="bg-indigo-500" title="Sedang Dikerjakan: {{ $totalSedang }}"></div>@endif
                        @if($pctBelum > 0)<div style="width: {{ $pctBelum }}%" class="bg-amber-400" title="Belum Dimulai: {{ $totalBelum }}"></div>@endif
                        @if($pctDitolak > 0)<div style="width: {{ $pctDitolak }}%" class="bg-rose-500" title="Berhalangan/Ditolak: {{ $totalDitolak }}"></div>@endif
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2 mt-2 text-[10px] text-slate-600 font-medium">
                        <div class="flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                            <span>Selesai &amp; Ada Laporan: <strong>{{ $totalLaporanSudah }}</strong></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-teal-400 inline-block"></span>
                            <span>Selesai (Menunggu Laporan): <strong>{{ max(0, $totalSelesai - $totalLaporanSudah) }}</strong></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 inline-block"></span>
                            <span>Sedang Dikerjakan: <strong>{{ $totalSedang }}</strong></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span>
                            <span>Belum Dimulai: <strong>{{ $totalBelum }}</strong></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
                            <span>Berhalangan / Ditolak: <strong>{{ $totalDitolak }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== 4. TABEL 1: REKAPITULASI PRODUKTIVITAS PER ANGGOTA BIDANG ===== -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-indigo-700">group</span>
                        <span>Tabel 1: Rekapitulasi Produktivitas &amp; Disiplin Anggota Bidang</span>
                    </h3>
                    <span class="text-[10px] text-slate-500">Urutan: Skor Produktivitas Tertinggi</span>
                </div>

                <div class="overflow-x-auto border border-slate-300 rounded-xl">
                    <table class="w-full text-left text-[11px] border-collapse">
                        <thead>
                            <tr class="bg-[#064e3b] text-white font-bold text-[10px] uppercase tracking-wider">
                                <th class="py-2.5 px-2 text-center border-r border-emerald-800 w-8">No</th>
                                <th class="py-2.5 px-3 border-r border-emerald-800 min-w-[180px]">Nama Anggota Bidang</th>
                                <th class="py-2.5 px-2.5 border-r border-emerald-800 min-w-[110px]">Kontak WhatsApp</th>
                                <th class="py-2.5 px-2 text-center border-r border-emerald-800 w-14" title="Total Agenda Diberikan">Ditugaskan</th>
                                <th class="py-2.5 px-2 text-center border-r border-emerald-800 w-14 text-emerald-200" title="Konfirmasi Siap/Hadir">Siap</th>
                                <th class="py-2.5 px-2 text-center border-r border-emerald-800 w-14 text-rose-200" title="Berhalangan/Ditolak">Ditolak</th>
                                <th class="py-2.5 px-2 text-center border-r border-emerald-800 w-14 text-emerald-200" title="Kegiatan Selesai">Selesai</th>
                                <th class="py-2.5 px-2 text-center border-r border-emerald-800 w-14 text-indigo-200" title="Sedang Dikerjakan">Sedang</th>
                                <th class="py-2.5 px-2 text-center border-r border-emerald-800 w-14 text-teal-200" title="Laporan Sudah Masuk">Laporan</th>
                                <th class="py-2.5 px-2 text-center border-r border-emerald-800 w-14 text-amber-200" title="Laporan Belum Masuk">Tunda Lapor</th>
                                <th class="py-2.5 px-2.5 text-center border-r border-emerald-800 w-20">Skor</th>
                                <th class="py-2.5 px-3 text-center w-28">Predikat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($bidangPerformance as $idx => $row)
                                <tr class="{{ $idx % 2 === 0 ? 'bg-white' : 'bg-slate-50/60' }} hover:bg-emerald-50/40 transition">
                                    <td class="py-2 px-2 text-center font-bold text-slate-500 border-r border-slate-200">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-2 px-3 border-r border-slate-200">
                                        <div class="font-bold text-slate-900 leading-snug">{{ $row['user']->name }}</div>
                                        <div class="text-[9.5px] text-slate-500">
                                            Role: {{ $row['user']->role_label }} • Bidang: {{ strtoupper($row['user']->bidang ?? 'EKOP') }}
                                        </div>
                                    </td>
                                    <td class="py-2 px-2.5 border-r border-slate-200 text-[10px] font-mono-tag text-slate-600">
                                        {{ $row['user']->no_hp ?: '-' }}
                                    </td>
                                    <td class="py-2 px-2 text-center font-bold text-slate-800 border-r border-slate-200">
                                        {{ $row['total_kegiatan'] }}
                                    </td>
                                    <td class="py-2 px-2 text-center font-bold text-emerald-700 bg-emerald-50/30 border-r border-slate-200">
                                        {{ $row['dikonfirmasi'] }}
                                    </td>
                                    <td class="py-2 px-2 text-center font-semibold text-rose-700 border-r border-slate-200">
                                        {{ $row['ditolak'] }}
                                    </td>
                                    <td class="py-2 px-2 text-center font-bold text-emerald-700 bg-emerald-50/30 border-r border-slate-200">
                                        {{ $row['selesai'] }}
                                    </td>
                                    <td class="py-2 px-2 text-center font-semibold text-indigo-700 border-r border-slate-200">
                                        {{ $row['sedang'] }}
                                    </td>
                                    <td class="py-2 px-2 text-center font-bold text-teal-700 bg-teal-50/30 border-r border-slate-200">
                                        {{ $row['laporan_sudah'] }}
                                    </td>
                                    <td class="py-2 px-2 text-center font-semibold text-amber-700 border-r border-slate-200">
                                        {{ $row['laporan_belum'] }}
                                    </td>
                                    <td class="py-2 px-2.5 text-center font-black border-r border-slate-200 text-slate-800">
                                        @if($row['skor_produktivitas'] !== null)
                                            <span class="{{ $row['skor_produktivitas'] >= 80 ? 'text-emerald-700' : ($row['skor_produktivitas'] >= 60 ? 'text-indigo-700' : 'text-amber-700') }}">
                                                {{ $row['skor_produktivitas'] }}%
                                            </span>
                                        @else
                                            <span class="text-slate-400 font-normal">-</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded text-[9.5px] font-bold border {{ $row['badge_class'] }}">
                                            {{ $row['predikat'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="py-6 text-center text-slate-400 font-medium">
                                        Tidak ada data anggota bidang untuk periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-slate-100 border-t-2 border-slate-300 font-bold text-[11px] text-slate-800">
                            <tr>
                                <td colspan="3" class="py-2.5 px-3 text-right uppercase border-r border-slate-300">
                                    Total Disposisi ({{ $totalAnggota }} Staf):
                                </td>
                                <td class="py-2.5 px-2 text-center border-r border-slate-300">{{ $totalDisposisiPersonel }}</td>
                                <td class="py-2.5 px-2 text-center text-emerald-800 bg-emerald-100/50 border-r border-slate-300">{{ $totalDikonfirmasi }}</td>
                                <td class="py-2.5 px-2 text-center text-rose-800 border-r border-slate-300">{{ $totalDitolak }}</td>
                                <td class="py-2.5 px-2 text-center text-emerald-800 bg-emerald-100/50 border-r border-slate-300">{{ $totalSelesai }}</td>
                                <td class="py-2.5 px-2 text-center text-indigo-800 border-r border-slate-300">{{ $totalSedang }}</td>
                                <td class="py-2.5 px-2 text-center text-teal-800 bg-teal-100/50 border-r border-slate-300">{{ $totalLaporanSudah }}</td>
                                <td class="py-2.5 px-2 text-center text-amber-800 border-r border-slate-300">{{ $totalLaporanBelum }}</td>
                                <td class="py-2.5 px-2.5 text-center text-[#064e3b] font-black border-r border-slate-300">{{ $overallProductivityRate }}%</td>
                                <td class="py-2.5 px-3 text-center text-[10px] text-slate-600">
                                    {{ $overallProductivityRate >= 80 ? 'Sangat Baik' : ($overallProductivityRate >= 60 ? 'Baik' : 'Perlu Peningkatan') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- ===== 5. TABEL 2: RINCIAN RIWAYAT PENUGASAN & LAPORAN 1 BULAN TERAKHIR ===== -->
            <div class="mb-10 page-break">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-[#064e3b]">fact_check</span>
                        <span>Tabel 2: Rincian Kegiatan &amp; Laporan Anggota Bidang Periode {{ $periodLabel }}</span>
                    </h3>
                    <span class="text-[10px] text-slate-500 font-mono-tag">Total: {{ $detailedEntries->count() }} Penugasan</span>
                </div>

                <div class="overflow-x-auto border border-slate-300 rounded-xl">
                    <table class="w-full text-left text-[10.5px] border-collapse">
                        <thead>
                            <tr class="bg-slate-800 text-white font-bold text-[10px] uppercase tracking-wider">
                                <th class="py-2 px-2 text-center border-r border-slate-700 w-8">No</th>
                                <th class="py-2 px-3 border-r border-slate-700 min-w-[150px]">Nama Staf Bidang</th>
                                <th class="py-2 px-3 border-r border-slate-700 min-w-[190px]">Judul Agenda Kegiatan</th>
                                <th class="py-2 px-2.5 border-r border-slate-700 w-24">Tanggal Kegiatan</th>
                                <th class="py-2 px-2.5 border-r border-slate-700 w-24">Batas Laporan</th>
                                <th class="py-2 px-2 text-center border-r border-slate-700 w-24">Konfirmasi</th>
                                <th class="py-2 px-2 text-center border-r border-slate-700 w-24">Status Tugas</th>
                                <th class="py-2 px-3 min-w-[160px]">Status &amp; Bukti Laporan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($detailedEntries as $idx => $entry)
                                <tr class="{{ $idx % 2 === 0 ? 'bg-white' : 'bg-slate-50/60' }}">
                                    <td class="py-2 px-2 text-center font-bold text-slate-400 border-r border-slate-200">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-2 px-3 border-r border-slate-200 font-bold text-slate-800">
                                        {{ $entry->user->name ?? '-' }}
                                    </td>
                                    <td class="py-2 px-3 border-r border-slate-200">
                                        <div class="font-bold text-slate-900">{{ $entry->penugasan->judul ?? '-' }}</div>
                                        <div class="text-[9.5px] text-slate-500">
                                            Diberikan oleh: {{ $entry->penugasan->pemberi->name ?? 'Admin Bappeda' }}
                                        </div>
                                    </td>
                                    <td class="py-2 px-2.5 border-r border-slate-200 text-slate-700 font-mono-tag text-[10px]">
                                        {{ $entry->penugasan->tanggal_kegiatan ? $entry->penugasan->tanggal_kegiatan->translatedFormat('d M Y') : '-' }}
                                    </td>
                                    <td class="py-2 px-2.5 border-r border-slate-200 font-mono-tag text-[10px] text-slate-600">
                                        {{ $entry->penugasan->batas_laporan ? $entry->penugasan->batas_laporan->translatedFormat('d M Y') : '-' }}
                                    </td>
                                    <td class="py-2 px-2 text-center border-r border-slate-200">
                                        @if($entry->status_konfirmasi === 'dikonfirmasi')
                                            <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-300">
                                                Dikonfirmasi
                                            </span>
                                        @elseif($entry->status_konfirmasi === 'ditolak')
                                            <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-300">
                                                Berhalangan
                                            </span>
                                        @else
                                            <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-300">
                                                Menunggu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-2 text-center border-r border-slate-200">
                                        @if($entry->status_penugasan === 'selesai')
                                            <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-300">
                                                Selesai
                                            </span>
                                        @elseif($entry->status_penugasan === 'sedang_dikerjakan')
                                            <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-300">
                                                Sedang Jalan
                                            </span>
                                        @else
                                            <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-300">
                                                Belum Mulai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3 text-[10px]">
                                        @if($entry->status_laporan === 'sudah_dilaporkan')
                                            <div class="flex items-center gap-1 font-bold text-teal-700">
                                                <span class="material-symbols-outlined text-[13px]">check_circle</span>
                                                <span>Laporan Telah Diserahkan</span>
                                            </div>
                                            @if($entry->dilaporkan_pada)
                                                <div class="text-[9px] text-slate-500 font-mono-tag">
                                                    Waktu: {{ $entry->dilaporkan_pada->translatedFormat('d M Y, H:i') }} WIT
                                                </div>
                                            @endif
                                            @if($entry->file_laporan_name)
                                                <div class="truncate text-[9.5px] text-slate-600 mt-0.5">
                                                    File: <span class="font-semibold">{{ $entry->file_laporan_name }}</span> ({{ $entry->formatted_file_size }})
                                                </div>
                                            @endif
                                            @if($entry->link_dokumen)
                                                <div class="truncate text-[9.5px] text-indigo-600 mt-0.5 font-mono-tag">
                                                    Link: {{ $entry->link_dokumen }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-amber-700 font-semibold italic text-[9.5px]">
                                                Belum menyerahkan laporan
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-6 text-center text-slate-400 font-medium">
                                        Tidak ada penugasan internal bidang untuk periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ===== 6. LEMBAR PENGESAHAN DOKUMEN (TANDA TANGAN RESMI) ===== -->
            <div class="pt-6 border-t-2 border-slate-300 text-xs text-slate-800">
                <div class="flex justify-between items-start gap-8">
                    <!-- Kolom Kiri: Verifikasi Sistem MONITORA -->
                    <div class="w-1/2 text-left">
                        <p class="font-bold text-slate-600 uppercase text-[10px] tracking-wider">Verifikasi Kinerja Bidang:</p>
                        <p class="mt-1 text-slate-700">
                            Dokumen evaluasi kinerja ini diterbitkan otomatis melalui sub-sistem Penugasan Internal <strong>MONITORA DRIVE</strong> Bidang Perekonomian &amp; Perdagangan Bappeda Provinsi Maluku Utara sebagai tolak ukur disiplin dan capaian tugas staf.
                        </p>
                        <div class="mt-4 p-2.5 rounded-lg bg-slate-50 border border-slate-200 inline-block text-[10px]">
                            <span class="font-bold text-slate-700 block">ID Rekapitulasi: MONITORA-BIDANG-{{ $startDate->format('Ymd') }}-{{ $endDate->format('Ymd') }}</span>
                            <span class="text-slate-500 font-mono-tag">Waktu Cetak: {{ now()->format('Y-m-d H:i:s') }} WIT</span>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Tanda Tangan Pejabat Bappeda -->
                    <div class="w-1/2 text-center flex flex-col items-center">
                        <p class="text-slate-800">
                            Sofifi, {{ now()->translatedFormat('d F Y') }}
                        </p>
                        <p class="font-bold text-slate-900 mt-1 uppercase text-[11px]">
                            Mengetahui,<br>
                            Kepala Bidang Perekonomian dan Perdagangan<br>
                            Bappeda Provinsi Maluku Utara
                        </p>

                        <!-- Ruang Tanda Tangan & Cap -->
                        <div class="h-20 my-2 flex items-center justify-center">
                            <span class="text-slate-300 italic text-[11px]">[ Tanda Tangan &amp; Cap Basah ]</span>
                        </div>

                        <div class="border-b border-slate-800 w-52 pb-0.5">
                            <span class="font-extrabold text-slate-900 text-[12px] uppercase">MUHAMMAD RIDWAN, SE., M.Si</span>
                        </div>
                        <p class="text-[10.5px] text-slate-600 mt-0.5 font-mono-tag">
                            NIP. 19780512 200501 1 008
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer Small print info -->
            <div class="mt-8 pt-3 border-t border-slate-200 flex items-center justify-between text-[9px] text-slate-400 font-medium">
                <span>MONITORA DRIVE v3.4.2 • Sistem Monitoring Evaluasi Penugasan Bappeda Provinsi Maluku Utara</span>
                <span>Halaman 1 dari 1 • Dokumen Resmi Kedinasan</span>
            </div>

        </div>
    </main>

</body>
</html>
