<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekapitulasi Kinerja Kepatuhan OPD – 1 Bulan Terakhir | MONITORA</title>
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
                        <span class="text-white text-sm font-bold tracking-tight">Cetak Rekapitulasi Kinerja OPD</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-[#4ADE80]/20 text-[#4ADE80] border border-[#4ADE80]/30 font-mono-tag">
                            Super Admin
                        </span>
                    </div>
                    <p class="text-[11px] text-[#A3B8AD]">Evaluasi Kinerja Pengumpulan Berkas Perangkat Daerah (1 Bulan Terakhir)</p>
                </div>
            </div>

            <!-- Center: Filter Presets -->
            <form method="GET" action="{{ route('admin.rekapitulasi.opd') }}" class="flex flex-wrap items-center gap-1.5">
                <input type="hidden" name="period" id="periodInput" value="{{ $period }}">

                <div class="bg-white/5 border border-white/10 p-1 rounded-xl flex items-center gap-1">
                    <a href="{{ route('admin.rekapitulasi.opd', ['period' => '30_days']) }}"
                       class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $period === '30_days' ? 'bg-[#4ADE80] text-[#091A14] font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        1 Bulan Terakhir
                    </a>
                    <a href="{{ route('admin.rekapitulasi.opd', ['period' => 'this_month']) }}"
                       class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $period === 'this_month' ? 'bg-[#4ADE80] text-[#091A14] font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        Bulan Ini
                    </a>
                    <a href="{{ route('admin.rekapitulasi.opd', ['period' => 'prev_month']) }}"
                       class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $period === 'prev_month' ? 'bg-[#4ADE80] text-[#091A14] font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        Bulan Lalu
                    </a>
                </div>

                <!-- Custom Range inputs if needed -->
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
                            BIDANG PEREKONOMIAN DAN PERDAGANGAN
                        </h2>
                        <p class="text-[10px] sm:text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Jalan Gosale Puncak No. 1, Kota Sofifi, Maluku Utara • Kode Pos 97827<br>
                            Sistem Monitoring Kinerja Terpadu: <span class="font-bold text-[#064e3b]">MONITORA DRIVE</span> • Laman: https://monitora.malutprov.go.id
                        </p>
                    </div>

                    <!-- Right: Official Badge / Logo Sekunder -->
                    <div class="w-20 h-20 shrink-0 hidden sm:flex flex-col items-center justify-center p-2 rounded-xl bg-emerald-50/60 border border-emerald-200/80 text-center">
                        <span class="text-[8.5px] font-extrabold text-[#064e3b] tracking-wider uppercase font-mono-tag">DOKUMEN RESMI</span>
                        <span class="material-symbols-outlined text-[26px] text-[#059669] my-0.5">verified</span>
                        <span class="text-[7.5px] font-bold text-slate-500 uppercase">E-MONITORING</span>
                    </div>
                </div>
            </div>

            <!-- ===== 2. JUDUL DOKUMEN & METADATA EVALUASI ===== -->
            <div class="text-center mb-6">
                <h2 class="text-[16px] sm:text-[18px] font-black uppercase text-[#064e3b] tracking-wide underline underline-offset-4">
                    REKAPITULASI EVALUASI KINERJA KEPATUHAN PERANGKAT DAERAH (OPD)
                </h2>
                <div class="text-[12px] sm:text-[13px] font-bold text-slate-700 mt-1">
                    Periode Evaluasi: <span class="text-emerald-800 bg-emerald-50 px-2.5 py-0.5 rounded border border-emerald-200">{{ $periodLabel }}</span>
                </div>
            </div>

            <!-- Metadata Dokumen Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 border border-slate-200 rounded-xl p-3 mb-6 text-[11px]">
                <div>
                    <span class="text-slate-400 block uppercase font-bold text-[9.5px]">Nomor Dokumen</span>
                    <span class="font-bold font-mono-tag text-slate-800">REKAP-OPD/{{ date('Ym') }}/{{ str_pad((string) $totalOpds, 3, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block uppercase font-bold text-[9.5px]">Waktu Cetak Dokumen</span>
                    <span class="font-bold text-slate-800">{{ now()->translatedFormat('d F Y, H:i') }} WIT</span>
                </div>
                <div>
                    <span class="text-slate-400 block uppercase font-bold text-[9.5px]">Verifikator / Administrator</span>
                    <span class="font-bold text-[#064e3b]">{{ Auth::user()->name }} (Super Admin)</span>
                </div>
                <div>
                    <span class="text-slate-400 block uppercase font-bold text-[9.5px]">Status Validitas</span>
                    <span class="inline-flex items-center gap-1 font-bold text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Data Terverifikasi Digital
                    </span>
                </div>
            </div>

            <!-- ===== 3. EXECUTIVE SUMMARY / RINGKASAN CAPAIAN KINERJA (KPI CARDS) ===== -->
            <div class="mb-6">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2.5 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-emerald-700">analytics</span>
                    <span>Ringkasan Eksekutif Kinerja 1 Bulan Terakhir</span>
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                    <!-- Total OPD -->
                    <div class="bg-emerald-50/70 border border-emerald-200 rounded-xl p-3 text-center">
                        <div class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Total OPD Dinilai</div>
                        <div class="text-2xl font-black text-[#064e3b] my-0.5 leading-none">{{ $totalOpds }}</div>
                        <div class="text-[9.5px] text-emerald-700 font-semibold">Perangkat Daerah Aktif</div>
                    </div>

                    <!-- Total Penugasan Diterbitkan -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-center">
                        <div class="text-[10px] font-bold text-slate-600 uppercase tracking-wider">Penugasan Diterbitkan</div>
                        <div class="text-2xl font-black text-slate-800 my-0.5 leading-none">{{ $totalTugasDiterbitkan }}</div>
                        <div class="text-[9.5px] text-slate-500 font-semibold">Dalam Periode 1 Bulan</div>
                    </div>

                    <!-- Total Disposisi Berkas -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-center">
                        <div class="text-[10px] font-bold text-slate-600 uppercase tracking-wider">Total Disposisi Tugas</div>
                        <div class="text-2xl font-black text-slate-800 my-0.5 leading-none">{{ $totalDisposisi }}</div>
                        <div class="text-[9.5px] text-slate-500 font-semibold">Berkas Wajib Dikumpul</div>
                    </div>

                    <!-- Total Selesai / Diterima -->
                    <div class="bg-emerald-50/70 border border-emerald-200 rounded-xl p-3 text-center">
                        <div class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Berkas Diterima</div>
                        <div class="text-2xl font-black text-emerald-700 my-0.5 leading-none">{{ $totalDiterima }}</div>
                        <div class="text-[9.5px] text-emerald-600 font-semibold">Valid &amp; Terverifikasi</div>
                    </div>

                    <!-- Tingkat Kepatuhan Rata-Rata -->
                    <div class="bg-[#064e3b] text-white rounded-xl p-3 text-center col-span-2 sm:col-span-1 shadow-xs">
                        <div class="text-[10px] font-bold text-[#4ADE80] uppercase tracking-wider">Tingkat Kepatuhan</div>
                        <div class="text-2xl font-black text-white my-0.5 leading-none">{{ $overallComplianceRate }}%</div>
                        <div class="text-[9.5px] text-emerald-200 font-semibold">Rata-Rata Seluruh OPD</div>
                    </div>
                </div>

                <!-- Breakdown Status Pengumpulan Bar -->
                <div class="mt-3 bg-white border border-slate-200 rounded-xl p-3">
                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-700 mb-1.5">
                        <span>Distribusi Status Berkas Penugasan:</span>
                        <span class="text-slate-500 font-mono-tag text-[10px]">Total: {{ $totalDisposisi }} Disposisi</span>
                    </div>

                    <div class="w-full h-3 rounded-full bg-slate-100 overflow-hidden flex shadow-inner">
                        @php
                            $pctDiterima = $totalDisposisi > 0 ? ($totalDiterima / $totalDisposisi) * 100 : 0;
                            $pctMenunggu = $totalDisposisi > 0 ? ($totalMenunggu / $totalDisposisi) * 100 : 0;
                            $pctRevisi = $totalDisposisi > 0 ? ($totalRevisi / $totalDisposisi) * 100 : 0;
                            $pctBelum = $totalDisposisi > 0 ? ($totalBelum / $totalDisposisi) * 100 : 0;
                            $pctTerlambat = $totalDisposisi > 0 ? ($totalTerlambat / $totalDisposisi) * 100 : 0;
                        @endphp
                        @if($pctDiterima > 0)<div style="width: {{ $pctDiterima }}%" class="bg-emerald-500" title="Diterima: {{ $totalDiterima }}"></div>@endif
                        @if($pctMenunggu > 0)<div style="width: {{ $pctMenunggu }}%" class="bg-sky-500" title="Menunggu Verifikasi: {{ $totalMenunggu }}"></div>@endif
                        @if($pctRevisi > 0)<div style="width: {{ $pctRevisi }}%" class="bg-pink-500" title="Perlu Revisi: {{ $totalRevisi }}"></div>@endif
                        @if($pctBelum > 0)<div style="width: {{ $pctBelum }}%" class="bg-amber-400" title="Belum Dikumpulkan: {{ $totalBelum }}"></div>@endif
                        @if($pctTerlambat > 0)<div style="width: {{ $pctTerlambat }}%" class="bg-rose-500" title="Terlambat: {{ $totalTerlambat }}"></div>@endif
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2 mt-2 text-[10px] text-slate-600 font-medium">
                        <div class="flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                            <span>Diterima: <strong>{{ $totalDiterima }}</strong> ({{ round($pctDiterima, 1) }}%)</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-500 inline-block"></span>
                            <span>Menunggu Verifikasi: <strong>{{ $totalMenunggu }}</strong></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-pink-500 inline-block"></span>
                            <span>Perlu Revisi: <strong>{{ $totalRevisi }}</strong></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span>
                            <span>Belum Kumpul: <strong>{{ $totalBelum }}</strong></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
                            <span>Lewat Batas Waktu: <strong>{{ $totalTerlambat }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== 4. TABEL 1: REKAPITULASI & PERINGKAT KEPATUHAN SELURUH OPD ===== -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-[#064e3b]">format_list_numbered</span>
                        <span>Tabel 1: Rekapitulasi Tingkat Kepatuhan Perangkat Daerah (OPD)</span>
                    </h3>
                    <span class="text-[10px] text-slate-500">Urutan: Capaian Kepatuhan Tertinggi ke Terendah</span>
                </div>

                <div class="overflow-x-auto border border-slate-300 rounded-xl">
                    <table class="w-full text-left text-[11px] border-collapse">
                        <thead>
                            <tr class="bg-[#064e3b] text-white font-bold text-[10.5px] uppercase tracking-wider">
                                <th class="py-2.5 px-2 text-center border-r border-emerald-800 w-10">No</th>
                                <th class="py-2.5 px-3 border-r border-emerald-800 min-w-[200px]">Nama Perangkat Daerah (OPD)</th>
                                <th class="py-2.5 px-2.5 border-r border-emerald-800 min-w-[120px]">PIC &amp; WhatsApp</th>
                                <th class="py-2.5 px-2 text-center border-r border-emerald-800 w-14" title="Total Tugas Diterima">Ditugaskan</th>
                                <th class="py-2.5 px-2 text-center border-r border-emerald-800 w-14 text-emerald-200" title="Valid & Diterima">Diterima</th>
                                <th class="py-2.5 px-2 text-center border-r border-emerald-800 w-14 text-sky-200" title="Menunggu Verifikasi">Menunggu</th>
                                <th class="py-2.5 px-2 text-center border-r border-emerald-800 w-14 text-pink-200" title="Perlu Revisi">Revisi</th>
                                <th class="py-2.5 px-2 text-center border-r border-emerald-800 w-14 text-amber-200" title="Belum Dikumpulkan">Belum</th>
                                <th class="py-2.5 px-2 text-center border-r border-emerald-800 w-14 text-rose-200" title="Terlambat">Terlambat</th>
                                <th class="py-2.5 px-2.5 text-center border-r border-emerald-800 w-20">Kepatuhan</th>
                                <th class="py-2.5 px-3 text-center w-28">Predikat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($opdPerformance as $idx => $row)
                                <tr class="{{ $idx % 2 === 0 ? 'bg-white' : 'bg-slate-50/60' }} hover:bg-emerald-50/40 transition">
                                    <td class="py-2 px-2 text-center font-bold text-slate-500 border-r border-slate-200">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-2 px-3 border-r border-slate-200">
                                        <div class="font-bold text-slate-900 leading-snug">{{ $row['opd']->nama_opd }}</div>
                                        <div class="text-[9.5px] font-mono-tag text-slate-500">Kode: {{ $row['opd']->kode_opd }}</div>
                                    </td>
                                    <td class="py-2 px-2.5 border-r border-slate-200 text-[10px]">
                                        <div class="font-semibold text-slate-800">{{ $row['opd']->nama_pic ?: '-' }}</div>
                                        <div class="text-slate-500 font-mono-tag">{{ $row['opd']->whatsapp_pic ?: '-' }}</div>
                                    </td>
                                    <td class="py-2 px-2 text-center font-bold text-slate-800 border-r border-slate-200">
                                        {{ $row['total_assigned'] }}
                                    </td>
                                    <td class="py-2 px-2 text-center font-bold text-emerald-700 bg-emerald-50/40 border-r border-slate-200">
                                        {{ $row['total_diterima'] }}
                                    </td>
                                    <td class="py-2 px-2 text-center font-semibold text-sky-700 border-r border-slate-200">
                                        {{ $row['total_menunggu'] }}
                                    </td>
                                    <td class="py-2 px-2 text-center font-semibold text-pink-700 border-r border-slate-200">
                                        {{ $row['total_revisi'] }}
                                    </td>
                                    <td class="py-2 px-2 text-center font-semibold text-amber-700 border-r border-slate-200">
                                        {{ $row['total_belum'] }}
                                    </td>
                                    <td class="py-2 px-2 text-center font-bold text-rose-700 bg-rose-50/40 border-r border-slate-200">
                                        {{ $row['total_terlambat'] }}
                                    </td>
                                    <td class="py-2 px-2.5 text-center font-black border-r border-slate-200 text-slate-800">
                                        @if($row['kepatuhan_rate'] !== null)
                                            <span class="{{ $row['kepatuhan_rate'] >= 80 ? 'text-emerald-700' : ($row['kepatuhan_rate'] >= 60 ? 'text-amber-700' : 'text-rose-700') }}">
                                                {{ $row['kepatuhan_rate'] }}%
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
                                    <td colspan="11" class="py-6 text-center text-slate-400 font-medium">
                                        Tidak ada data perangkat daerah untuk periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-slate-100 border-t-2 border-slate-300 font-bold text-[11px] text-slate-800">
                            <tr>
                                <td colspan="3" class="py-2.5 px-3 text-right uppercase border-r border-slate-300">
                                    Total Keseluruhan ({{ $totalOpds }} OPD):
                                </td>
                                <td class="py-2.5 px-2 text-center border-r border-slate-300">{{ $totalDisposisi }}</td>
                                <td class="py-2.5 px-2 text-center text-emerald-800 bg-emerald-100/50 border-r border-slate-300">{{ $totalDiterima }}</td>
                                <td class="py-2.5 px-2 text-center text-sky-800 border-r border-slate-300">{{ $totalMenunggu }}</td>
                                <td class="py-2.5 px-2 text-center text-pink-800 border-r border-slate-300">{{ $totalRevisi }}</td>
                                <td class="py-2.5 px-2 text-center text-amber-800 border-r border-slate-300">{{ $totalBelum }}</td>
                                <td class="py-2.5 px-2 text-center text-rose-800 bg-rose-100/50 border-r border-slate-300">{{ $totalTerlambat }}</td>
                                <td class="py-2.5 px-2.5 text-center text-[#064e3b] font-black border-r border-slate-300">{{ $overallComplianceRate }}%</td>
                                <td class="py-2.5 px-3 text-center text-[10px] text-slate-600">
                                    {{ $overallComplianceRate >= 80 ? 'Sangat Baik' : ($overallComplianceRate >= 60 ? 'Cukup' : 'Perlu Evaluasi') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- ===== 5. TABEL 2: RINCIAN BERKAS & PENUGASAN 1 BULAN TERAKHIR ===== -->
            <div class="mb-10 page-break">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-[#064e3b]">description</span>
                        <span>Tabel 2: Rincian Riwayat Berkas Penugasan Periode {{ $periodLabel }}</span>
                    </h3>
                    <span class="text-[10px] text-slate-500 font-mono-tag">Total: {{ $detailedAssignments->count() }} Data</span>
                </div>

                <div class="overflow-x-auto border border-slate-300 rounded-xl">
                    <table class="w-full text-left text-[10.5px] border-collapse">
                        <thead>
                            <tr class="bg-slate-800 text-white font-bold text-[10px] uppercase tracking-wider">
                                <th class="py-2 px-2 text-center border-r border-slate-700 w-8">No</th>
                                <th class="py-2 px-3 border-r border-slate-700 min-w-[170px]">Perangkat Daerah</th>
                                <th class="py-2 px-3 border-r border-slate-700 min-w-[200px]">Judul Penugasan</th>
                                <th class="py-2 px-2.5 border-r border-slate-700 w-28">Batas Waktu (Deadline)</th>
                                <th class="py-2 px-2.5 border-r border-slate-700 w-28">Waktu Pengumpulan</th>
                                <th class="py-2 px-2.5 text-center border-r border-slate-700 w-28">Status Berkas</th>
                                <th class="py-2 px-3 min-w-[160px]">Detail Bukti / Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($detailedAssignments as $idx => $assignment)
                                <tr class="{{ $idx % 2 === 0 ? 'bg-white' : 'bg-slate-50/60' }}">
                                    <td class="py-2 px-2 text-center font-bold text-slate-400 border-r border-slate-200">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-2 px-3 border-r border-slate-200 font-bold text-slate-800">
                                        {{ $assignment->opd->nama_opd ?? '-' }}
                                    </td>
                                    <td class="py-2 px-3 border-r border-slate-200">
                                        <div class="font-bold text-slate-900">{{ $assignment->tugas->judul ?? '-' }}</div>
                                        <div class="text-[9.5px] text-slate-500">
                                            Format: {{ $assignment->tugas->format_label ?? 'Digital' }} • TA {{ $assignment->tugas->tahun_anggaran ?? date('Y') }}
                                        </div>
                                    </td>
                                    <td class="py-2 px-2.5 border-r border-slate-200 text-slate-700 font-mono-tag text-[10px]">
                                        {{ $assignment->tugas->deadline ? $assignment->tugas->deadline->translatedFormat('d M Y, H:i') : '-' }} WIT
                                    </td>
                                    <td class="py-2 px-2.5 border-r border-slate-200 font-mono-tag text-[10px]">
                                        @if($assignment->submission)
                                            <span class="text-slate-800 font-semibold">
                                                {{ $assignment->submission->created_at->translatedFormat('d M Y, H:i') }} WIT
                                            </span>
                                            @if($assignment->tugas && $assignment->submission->created_at > $assignment->tugas->deadline)
                                                <span class="block text-[9px] text-rose-600 font-bold">(Terlambat)</span>
                                            @else
                                                <span class="block text-[9px] text-emerald-600 font-bold">(Tepat Waktu)</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400 italic">Belum Mengumpulkan</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-2.5 text-center border-r border-slate-200">
                                        @php
                                            $st = $assignment->status;
                                            $badgeSt = match($st) {
                                                'diterima' => 'bg-emerald-50 text-emerald-700 border-emerald-300',
                                                'sudah_dikumpulkan' => 'bg-sky-50 text-sky-700 border-sky-300',
                                                'perlu_revisi' => 'bg-pink-50 text-pink-700 border-pink-300',
                                                'terlambat' => 'bg-rose-50 text-rose-700 border-rose-300',
                                                default => 'bg-amber-50 text-amber-700 border-amber-300'
                                            };
                                        @endphp
                                        <span class="inline-block px-2 py-0.5 rounded text-[9.5px] font-bold border {{ $badgeSt }}">
                                            {{ $assignment->status_label }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 text-[10px] text-slate-600">
                                        @if($assignment->submission)
                                            @if($assignment->submission->nomor_surat)
                                                <div>No. Surat: <span class="font-bold text-slate-800">{{ $assignment->submission->nomor_surat }}</span></div>
                                            @endif
                                            @if($assignment->submission->file_original_name)
                                                <div class="truncate text-slate-500">File: {{ $assignment->submission->file_original_name }}</div>
                                            @endif
                                            @if($assignment->submission->verifier)
                                                <div class="text-[9px] text-emerald-700 font-medium">Verifikator: {{ $assignment->submission->verifier->name }}</div>
                                            @endif
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-slate-400 font-medium">
                                        Tidak ada penugasan atau berkas dalam rentang waktu ini.
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
                        <p class="font-bold text-slate-600 uppercase text-[10px] tracking-wider">Verifikasi Digital Sistem:</p>
                        <p class="mt-1 text-slate-700">
                            Dokumen rekapitulasi ini dihasilkan secara otomatis oleh sistem <strong>MONITORA DRIVE</strong> berdasarkan data aktivitas, pengumpulan berkas, dan log verifikasi penugasan OPD Bappeda Provinsi Maluku Utara.
                        </p>
                        <div class="mt-4 p-2.5 rounded-lg bg-slate-50 border border-slate-200 inline-block text-[10px]">
                            <span class="font-bold text-slate-700 block">ID Dokumen: MONITORA-OPD-{{ $startDate->format('Ymd') }}-{{ $endDate->format('Ymd') }}</span>
                            <span class="text-slate-500 font-mono-tag">Waktu Ekstraksi: {{ now()->format('Y-m-d H:i:s') }} WIT</span>
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
