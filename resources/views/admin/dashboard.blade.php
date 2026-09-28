<x-app-layout>
    <!-- Header Hero Banner -->
    <div class="mb-8 p-6 sm:p-8 rounded-2xl bg-gradient-to-r from-blue-950 via-slate-900 to-blue-900 text-white shadow-lg border border-blue-900 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-200 text-xs font-bold uppercase tracking-wider mb-3">
                    <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                    Portal Monitoring & Pengingat Deadline
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    Dashboard BAPPEDA Maluku Utara
                </h1>
                <p class="text-sm text-blue-100 mt-1.5 max-w-2xl leading-relaxed font-normal">
                    Pantau status kepatuhan pengumpulan tugas, berkas digital, verifikasi fisik, serta jadwal pengingat otomatis WhatsApp untuk 10 OPD di bawah Bidang Ekonomi.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('admin.tugas.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-md transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Buat Tugas Baru
                </a>
                @if(Auth::user()->isSuperAdmin())
                    <a href="{{ route('admin.opd.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-900/90 hover:bg-blue-800 border border-blue-700 text-white font-semibold text-sm transition">
                        <svg class="w-4 h-4 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Tambah OPD
                    </a>
                    <a href="{{ route('admin.users.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-900/80 hover:bg-purple-800 border border-purple-700 text-white font-semibold text-sm transition">
                        <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Kelola Pengguna
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if($isPic && count($handledOpdIds) === 0)
        <div class="mb-8 p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3 shadow-xs">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="text-xs">
                <h4 class="font-bold text-sm text-amber-950 mb-0.5">Penugasan OPD Belum Ditetapkan</h4>
                <p class="leading-relaxed">
                    Akun PIC Anda saat ini belum ditugaskan untuk mengelola perangkat daerah (OPD) manapun. Silakan hubungi Administrator untuk mengatur instansi OPD binaan Anda agar dapat memantau dan memverifikasi berkas tugas.
                </p>
            </div>
        </div>
    @endif

    <!-- Stat Cards Metric Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <!-- Card 1: Total OPD -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ $isPic ? 'Total OPD Dikelola' : 'Total OPD' }}</span>
                <span class="p-2 rounded-lg bg-blue-50 text-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $totalOpd }}</div>
            <span class="text-[11px] text-slate-500 font-medium">{{ $isPic ? 'Binaan Anda' : 'Instansi aktif' }}</span>
        </div>

        <!-- Card 2: Tugas Aktif -->
        <div class="bg-white p-5 rounded-2xl border border-sky-200 bg-sky-50/30 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between text-sky-700 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-sky-800">Tugas Aktif</span>
                <span class="p-2 rounded-lg bg-sky-100 text-sky-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-sky-900">{{ $totalTugasAktif }}</div>
            <span class="text-[11px] text-sky-700 font-semibold">Sedang berjalan</span>
        </div>

        <!-- Card 3: Belum Kumpul -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Belum Kumpul</span>
                <span class="p-2 rounded-lg bg-slate-100 text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-800">{{ $totalBelumKumpul }}</div>
            <span class="text-[11px] text-slate-500 font-medium">Menunggu submission</span>
        </div>

        <!-- Card 4: Menunggu Verifikasi -->
        <div class="bg-white p-5 rounded-2xl border border-amber-200/80 bg-gradient-to-b from-amber-50/40 to-white shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between text-amber-700 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Verifikasi</span>
                <span class="p-2 rounded-lg bg-amber-100 text-amber-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-amber-800">{{ $totalMenungguVerifikasi }}</div>
            <span class="text-[11px] text-amber-700 font-semibold">Perlu ditinjau</span>
        </div>

        <!-- Card 5: Diterima / Selesai -->
        <div class="bg-white p-5 rounded-2xl border border-blue-200 bg-blue-50/40 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between text-blue-700 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-800">Diterima</span>
                <span class="p-2 rounded-lg bg-blue-100 text-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-blue-900">{{ $totalDiterima }}</div>
            <span class="text-[11px] text-blue-700 font-semibold">Tervalidasi Bappeda</span>
        </div>

        <!-- Card 6: Terlambat -->
        <div class="bg-white p-5 rounded-2xl border border-rose-200/80 bg-gradient-to-b from-rose-50/40 to-white shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between text-rose-700 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Terlambat</span>
                <span class="p-2 rounded-lg bg-rose-100 text-rose-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-rose-600">{{ $totalTerlambat }}</div>
            <span class="text-[11px] text-rose-600 font-semibold">Lewati deadline</span>
        </div>
    </div>

    <!-- Main Content Layout: Tasks Table + Side Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Active Tasks Monitoring (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Monitoring Tugas OPD Real-time</h2>
                        <p class="text-xs text-slate-500">Progres pengumpulan dokumen per tugas aktif</p>
                    </div>
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari nama tugas..." 
                               class="px-3 py-1.5 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 w-44">
                        <button type="submit" class="px-3 py-1.5 bg-blue-900 text-white rounded-xl text-xs font-semibold hover:bg-blue-800 transition">
                            Cari
                        </button>
                    </form>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($daftarTugas as $tugas)
                        @php
                            $totalOpd = $tugas->total_ditugaskan ?: 1;
                            $pct = round(($tugas->total_terkumpul / $totalOpd) * 100);
                            $isOverdue = $tugas->isPastDeadline();
                        @endphp
                        <div class="p-5 hover:bg-slate-50/80 transition">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $tugas->status === 'aktif' ? 'bg-sky-100 text-sky-900 border border-sky-200' : 'bg-slate-100 text-slate-700' }}">
                                            {{ ucfirst($tugas->status) }}
                                        </span>
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                            TA {{ $tugas->tahun_anggaran }}
                                        </span>
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $tugas->format_label }}
                                        </span>
                                    </div>
                                    <a href="{{ route('admin.tugas.show', $tugas) }}" class="font-bold text-slate-900 hover:text-blue-700 transition text-sm sm:text-base">
                                        {{ $tugas->judul }}
                                    </a>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-xs font-semibold {{ $isOverdue ? 'text-rose-600' : 'text-slate-600' }} block">
                                        ⏰ {{ $tugas->deadline->translatedFormat('d M Y - H:i') }} WIT
                                    </span>
                                    <span class="text-[10px] font-bold {{ $isOverdue ? 'text-rose-600' : 'text-sky-700' }}">
                                        {{ $tugas->sisa_waktu_label }}
                                    </span>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mt-3">
                                <div class="flex items-center justify-between text-xs text-slate-500 mb-1 font-medium">
                                    <span>Progres Pengumpulan: <strong>{{ $tugas->total_terkumpul }} / {{ $tugas->total_ditugaskan }} OPD</strong></span>
                                    <span class="font-bold text-slate-900">{{ $pct }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden flex">
                                    <div class="bg-gradient-to-r from-blue-600 to-sky-400 h-2.5 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>

                            <!-- Actions Footer -->
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-3 text-xs text-slate-500">
                                    @if($tugas->total_terlambat > 0)
                                        <span class="text-rose-600 font-semibold flex items-center gap-1">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                            {{ $tugas->total_terlambat }} OPD Terlambat
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.tugas.show', $tugas) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-800 font-bold text-xs transition">
                                        Rincian Monitoring & OPD
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-slate-400 text-sm">
                            Belum ada tugas yang dibuat. Klik tombol <strong>"Buat Tugas Baru"</strong> untuk mendistribusikan penugasan ke OPD.
                        </div>
                    @endforelse
                </div>

                @if($daftarTugas->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $daftarTugas->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Side: Submissions To Verify & WA Activity (1 col) -->
        <div class="space-y-6">
            <!-- Submissions awaiting verification card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Menunggu Verifikasi</h3>
                        <p class="text-[11px] text-slate-500">Berkas masuk terbaru dari OPD</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                        {{ $submissionsMenunggu->count() }} berkas
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($submissionsMenunggu as $sub)
                        <div class="p-4 hover:bg-slate-50 transition">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="text-[11px] font-bold text-blue-900 block">
                                        {{ $sub->tugasOpd->opd->nama_opd }}
                                    </span>
                                    <p class="text-xs font-semibold text-slate-900 mt-0.5 line-clamp-1">
                                        {{ $sub->tugasOpd->tugas->judul }}
                                    </p>
                                    <span class="text-[10px] text-slate-500 mt-1 block">
                                        Dikumpulkan {{ $sub->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <a href="{{ route('admin.submissions.show', $sub) }}" 
                                   class="px-3 py-1.5 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-xs font-bold shrink-0 shadow-sm transition">
                                    Periksa
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            Semua pengumpulan telah diverifikasi!
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent WhatsApp Activity Log Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Aktivitas WhatsApp</h3>
                        <p class="text-[11px] text-slate-500">Histori pengiriman notifikasi & reminder</p>
                    </div>
                    <a href="{{ route('admin.whatsapp-logs.index') }}" class="text-xs font-bold text-blue-700 hover:underline">
                        Lihat Semua
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($recentLogs as $log)
                        <div class="p-4 text-xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-slate-800">
                                    {{ $log->opd ? $log->opd->kode_opd : 'Broadcast' }}
                                </span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $log->status === 'terkirim' ? 'bg-blue-100 text-blue-800' : ($log->status === 'simulasi' ? 'bg-sky-100 text-sky-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </div>
                            <span class="text-[10px] font-bold text-blue-800 block">
                                {{ $log->tipe_label }}
                            </span>
                            <p class="text-slate-600 mt-1 line-clamp-2 italic text-[11px]">
                                "{{ Str::limit($log->isi_pesan, 80) }}"
                            </p>
                            <span class="text-[10px] text-slate-400 block mt-1">
                                {{ $log->sent_at->diffForHumans() }} (Ke: {{ $log->no_tujuan }})
                            </span>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            Belum ada aktivitas pengiriman WhatsApp.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
