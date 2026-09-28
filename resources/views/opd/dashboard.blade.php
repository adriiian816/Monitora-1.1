<x-app-layout>
    <!-- Welcome Header Banner -->
    <div class="mb-8 p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-blue-950 via-slate-900 to-blue-900 text-white shadow-xl border border-blue-900/60 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-300 text-xs font-semibold uppercase tracking-wider mb-3">
                    <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                    Portal Staf Instansi OPD
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    {{ $opd->nama_opd }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1.5 flex flex-wrap items-center gap-2">
                    <span>Kode OPD: <strong class="font-mono text-white">{{ $opd->kode_opd }}</strong></span>
                    @if(auth()->user()->no_hp)
                        <span>•</span>
                        <span class="font-mono text-sky-300">WhatsApp Akun Anda: {{ auth()->user()->no_hp }}</span>
                    @endif
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('opd.tugas.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white hover:bg-blue-50 text-blue-950 font-bold text-sm shadow-md transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Lihat Semua Tugas
                </a>
            </div>
        </div>

        <div class="absolute -right-16 -top-16 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Stat Metrics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block mb-1">Total Tugas</span>
            <div class="text-2xl font-black text-slate-900">{{ $totalTugas }}</div>
            <span class="text-[11px] text-slate-500">Ditugaskan Bappeda</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-amber-200 bg-amber-50/40 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-amber-700 block mb-1">Perlu Tindakan</span>
            <div class="text-2xl font-black text-amber-800">{{ $totalPerluTindakan }}</div>
            <span class="text-[11px] text-amber-700 font-medium">Belum / Perlu Revisi</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-blue-200 bg-blue-50/40 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-blue-700 block mb-1">Menunggu Verifikasi</span>
            <div class="text-2xl font-black text-blue-800">{{ $totalMenungguVerifikasi }}</div>
            <span class="text-[11px] text-blue-700 font-medium">Sudah dikirim</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-blue-200 bg-blue-50/50 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-blue-800 block mb-1">Selesai & Diterima</span>
            <div class="text-2xl font-black text-blue-800">{{ $totalSelesai }}</div>
            <span class="text-[11px] text-blue-800 font-medium">Tervalidasi Bappeda</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-rose-200 bg-rose-50/40 shadow-sm col-span-2 md:col-span-1">
            <span class="text-xs font-semibold uppercase tracking-wider text-rose-700 block mb-1">Terlambat</span>
            <div class="text-2xl font-black text-rose-700">{{ $totalTerlambat }}</div>
            <span class="text-[11px] text-rose-700 font-medium">Segera selesaikan</span>
        </div>
    </div>

    <!-- Main Grid: Urgent Tasks + WhatsApp Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Urgent Tasks Requiring Action (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Tugas yang Memerlukan Tindakan</h2>
                        <p class="text-xs text-slate-500">Daftar tugas dengan batas waktu terdekat yang belum diselesaikan</p>
                    </div>
                    <a href="{{ route('opd.tugas.index', ['status' => 'perlu_tindakan']) }}" class="text-xs font-bold text-blue-700 hover:underline">
                        Lihat Filter
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($tugasMendesak as $to)
                        @php
                            $t = $to->tugas;
                            $isOverdue = $t->isPastDeadline();
                        @endphp
                        <div class="p-5 hover:bg-slate-50 transition">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        @if($to->status === 'perlu_revisi')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                                ⚠️ Perlu Revisi
                                            </span>
                                        @elseif($to->status === 'terlambat')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-900 border border-rose-200">
                                                ⚠️ Terlambat
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                                Belum Dikumpulkan
                                            </span>
                                        @endif
                                        <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                                            {{ $t->format_label }}
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">
                                        <a href="{{ route('opd.tugas.show', $t) }}" class="hover:text-blue-700 transition">
                                            {{ $t->judul }}
                                        </a>
                                    </h3>
                                    @if($t->deskripsi)
                                        <p class="text-xs text-slate-500 line-clamp-2 mt-1">{{ $t->deskripsi }}</p>
                                    @endif
                                </div>

                                <div class="text-left sm:text-right shrink-0">
                                    <span class="text-xs font-bold block {{ $isOverdue ? 'text-rose-600' : 'text-slate-800' }}">
                                        ⏰ {{ $t->deadline->translatedFormat('d M Y H:i') }} WIT
                                    </span>
                                    <span class="text-[11px] font-semibold block {{ $isOverdue ? 'text-rose-600' : 'text-blue-700' }}">
                                        {{ $t->sisa_waktu_label }}
                                    </span>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                <a href="{{ route('opd.tugas.ics', $t) }}" 
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 hover:text-blue-700">
                                    <span>📅</span> Simpan ke Kalender (.ics)
                                </a>
                                <a href="{{ route('opd.tugas.show', $t) }}" 
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition">
                                    <span>Unggah / Serahkan Dokumen</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center text-slate-400 text-xs">
                            🎉 Luar biasa! Tidak ada tugas tertunda yang memerlukan tindakan.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Completed Submissions -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Riwayat Pengumpulan Dokumen yang Telah Dikirim</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($riwayatTerbaru as $to)
                        @php $sub = $to->submission; @endphp
                        <div class="p-4 flex items-center justify-between gap-4 hover:bg-slate-50 transition text-xs">
                            <div>
                                <span class="font-bold text-slate-900 block">{{ $to->tugas->judul }}</span>
                                <span class="text-slate-500 text-[11px] block mt-0.5">
                                    Status: 
                                    @if($to->status === 'diterima')
                                        <strong class="text-blue-800">✓ Diterima & Disetujui</strong>
                                    @else
                                        <strong class="text-amber-700">Menunggu Verifikasi Admin</strong>
                                    @endif
                                </span>
                            </div>
                            <a href="{{ route('opd.tugas.show', $to->tugas) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition shrink-0">
                                Lihat Berkas
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            Belum ada dokumen yang dikumpulkan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Side: WhatsApp Activity Log for this OPD (1 col) -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="font-bold text-slate-900 text-sm">Notifikasi WhatsApp Diterima</h3>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-900 border border-blue-200">
                        {{ auth()->user()->no_hp ? 'No: ' . auth()->user()->no_hp : 'Histori Notifikasi' }}
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($recentWaLogs as $log)
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-blue-900">{{ $log->tipe_label }}</span>
                                <span class="text-[10px] text-slate-400">{{ $log->sent_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-600 whitespace-pre-line text-[11px] leading-relaxed font-sans line-clamp-3">
                                {{ $log->isi_pesan }}
                            </p>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">
                            Belum ada notifikasi WhatsApp yang tercatat untuk OPD ini.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
