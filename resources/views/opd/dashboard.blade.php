<x-app-layout pageTitle="Dashboard OPD">
{{-- ===== TOP CONTROL BAR ===== --}}
<div class="px-6 md:px-8 py-4 bg-[#f7faf8] border-b border-emerald-100/70 flex flex-col xl:flex-row xl:items-center justify-between gap-3 shrink-0">
    {{-- Left: OPD Identity --}}
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-white text-[#059669] flex items-center justify-center border border-emerald-200 shadow-xs">
            <span class="material-symbols-outlined text-[22px]">domain</span>
        </div>
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">{{ $opd->nama_opd }}</h1>
                <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full font-mono">
                    {{ $opd->kode_opd }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Portal Pelaporan &amp; Monitoring Tugas Instansi Perangkat Daerah
                @if(auth()->user()->no_hp)
                    • <span class="text-[#059669] font-medium font-mono">WA: {{ auth()->user()->no_hp }}</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Right: Exec Stat Ticker --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-1 xl:pb-0">
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-white border border-emerald-100 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Total Tugas</span>
                <span class="text-base font-extrabold text-slate-900 leading-tight">{{ $totalTugas }}</span>
            </div>
            <span class="text-[10px] text-slate-400 font-medium">Kegiatan</span>
        </div>
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-amber-700">Perlu Tindakan</span>
                <span class="text-base font-extrabold text-amber-800 leading-tight">{{ $totalPerluTindakan }}</span>
            </div>
            <span class="text-[10px] text-amber-700 font-bold bg-white px-1.5 py-0.5 rounded">Revisi/Belum</span>
        </div>
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-white border border-emerald-100 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Verifikasi</span>
                <span class="text-base font-extrabold text-teal-700 leading-tight">{{ $totalMenungguVerifikasi }}</span>
            </div>
            <span class="text-[10px] text-teal-700 font-semibold bg-teal-50 px-1.5 py-0.5 rounded">Menunggu</span>
        </div>
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-white border border-emerald-100 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Diterima</span>
                <span class="text-base font-extrabold text-emerald-600 leading-tight">{{ $totalSelesai }}</span>
            </div>
            <span class="text-[10px] text-emerald-600 font-semibold bg-emerald-50 px-1.5 py-0.5 rounded">Valid &amp; Final</span>
        </div>
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-rose-600">Terlambat</span>
                <span class="text-base font-extrabold text-rose-600 leading-tight">{{ $totalTerlambat }}</span>
            </div>
            <span class="text-[10px] text-rose-600 font-bold bg-white px-1.5 py-0.5 rounded">Lewat Deadline</span>
        </div>
        <a href="{{ route('opd.tugas.index') }}"
           class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#064e3b] text-white text-[12px] font-bold shadow-sm hover:bg-[#043d2e] transition whitespace-nowrap ml-1">
            <span class="material-symbols-outlined text-[16px]">folder_shared</span>
            <span>Semua Tugas</span>
        </a>
    </div>
</div>

{{-- ===== MAIN SPLIT-PANEL WORKSPACE ===== --}}
<div class="flex-1 flex flex-col lg:flex-row min-w-0">

    {{-- ===== LEFT PANEL: Urgent Tasks (65%) ===== --}}
    <section class="flex-1 lg:w-[65%] p-6 md:p-8 flex flex-col gap-6">

        {{-- Welcome Banner Card --}}
        <div class="p-6 rounded-3xl bg-gradient-to-r from-[#064e3b] via-[#065f46] to-[#047857] text-white shadow-xs border border-emerald-700/40 relative overflow-hidden">
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/15 border border-white/20 text-emerald-100 text-[10px] font-bold uppercase tracking-wider mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                        Portal Pelaporan Instansi
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                        {{ $opd->nama_opd }}
                    </h2>
                    <p class="text-xs text-emerald-100/80 mt-1">
                        Pastikan seluruh laporan dan dokumen kerja diunggah sebelum batas waktu berakhir.
                    </p>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('opd.tugas.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white hover:bg-emerald-50 text-[#064e3b] font-bold text-xs shadow-sm transition">
                        <span class="material-symbols-outlined text-[16px] text-[#059669]">assignment</span>
                        <span>Lihat Tugas ({{ $totalTugas }})</span>
                    </a>
                </div>
            </div>
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-emerald-300/10 rounded-full blur-2xl pointer-events-none"></div>
        </div>

        {{-- Urgent Tasks Container --}}
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Tugas yang Memerlukan Tindakan</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Daftar penugasan dengan tenggat waktu terdekat yang perlu diselesaikan</p>
                </div>
                <a href="{{ route('opd.tugas.index', ['status' => 'perlu_tindakan']) }}" class="text-xs font-bold text-[#059669] hover:underline">
                    Filter Perlu Tindakan
                </a>
            </div>

            <div class="border border-emerald-100/80 rounded-3xl overflow-hidden bg-white shadow-xs divide-y divide-slate-100">
                @forelse($tugasMendesak as $to)
                    @php
                        $t = $to->tugas;
                        $isOverdue = $t->isPastDeadline();
                    @endphp
                    <div class="p-5 sm:p-6 hover:bg-[#f8fbf9] transition flex flex-col gap-3">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($to->status === 'perlu_revisi')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                        ⚠️ Perlu Revisi
                                    </span>
                                @elseif($to->status === 'terlambat' || $isOverdue)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-900 border border-rose-200">
                                        ⚠️ Terlambat
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                        Belum Dikumpulkan
                                    </span>
                                @endif

                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    {{ $t->format_label }}
                                </span>
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                    TA {{ $t->tahun_anggaran }}
                                </span>
                            </div>

                            <div class="text-left sm:text-right">
                                <div class="text-xs font-bold {{ $isOverdue ? 'text-rose-600' : 'text-slate-600' }}">
                                    {{ $t->deadline->translatedFormat('d M Y H:i') }} WIT
                                </div>
                                <div class="text-[10px] font-bold {{ $isOverdue ? 'text-rose-600' : 'text-[#059669]' }}">
                                    {{ $t->sisa_waktu_label }}
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-slate-900 hover:text-[#059669] transition">
                                <a href="{{ route('opd.tugas.show', $t) }}">
                                    {{ $t->judul }}
                                </a>
                            </h4>
                            @if($t->deskripsi)
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $t->deskripsi }}</p>
                            @endif
                        </div>

                        @if($to->submission && $to->submission->catatan_admin)
                            <div class="p-3 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs text-amber-900">
                                <strong class="block mb-0.5 text-amber-950 font-bold">Catatan Masukan PIC:</strong>
                                <p class="italic">"{{ $to->submission->catatan_admin }}"</p>
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 flex-wrap gap-2">
                            <a href="{{ route('opd.tugas.ics', $t) }}" 
                               class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-[#059669] font-medium transition">
                                <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                                <span>Simpan ke Kalender (.ics)</span>
                            </a>
                            <a href="{{ route('opd.tugas.show', $t) }}" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#059669] hover:bg-[#047857] text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition transform active:scale-95">
                                <span>Unggah / Serahkan Dokumen</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-400 text-xs">
                        <span class="material-symbols-outlined text-[42px] text-emerald-200 block mb-2">task_alt</span>
                        <div class="text-sm font-bold text-slate-700">Semua Penugasan Selesai!</div>
                        <p class="mt-1 text-slate-400">Tidak ada penugasan yang memerlukan tindakan saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ===== RIGHT PANEL: WhatsApp Logs & Recent Activity (35%) ===== --}}
    <aside class="w-full lg:w-[35%] shrink-0 border-t lg:border-t-0 lg:border-l border-emerald-100 bg-[#fcfdfc] p-6 flex flex-col gap-6">

        {{-- WhatsApp Activity Section --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-[#059669]">chat</span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">NOTIFIKASI WHATSAPP</h3>
                </div>
                @if(auth()->user()->no_hp)
                    <span class="text-[10px] font-mono font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                        {{ auth()->user()->no_hp }}
                    </span>
                @endif
            </div>

            <div class="flex flex-col gap-2.5">
                @forelse($recentWaLogs as $log)
                    <div class="p-3.5 rounded-2xl bg-[#f0f5f2] border border-emerald-100/80 flex flex-col gap-1.5">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-bold text-[#064e3b]">{{ $log->tipe_label }}</span>
                            <span class="text-[10px] text-slate-400">{{ $log->sent_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-slate-600 italic line-clamp-3 bg-white p-2.5 rounded-xl border border-slate-100">
                            "{{ $log->isi_pesan }}"
                        </p>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs rounded-2xl bg-[#f0f5f2] border border-emerald-100">
                        Belum ada notifikasi WhatsApp yang dikirimkan ke akun Anda.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Submissions Activity --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-[#059669]">history_edu</span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">PENGUMPULAN TERBARU</h3>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                @forelse($riwayatTerbaru as $to)
                    <div class="p-3.5 rounded-2xl bg-white border border-emerald-100 shadow-2xs flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-800 block truncate">{{ $to->tugas->judul }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Diperbarui {{ $to->updated_at->diffForHumans() }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0
                            {{ $to->status === 'diterima' ? 'bg-emerald-50 text-[#059669] border border-emerald-200' : 'bg-teal-50 text-teal-700 border border-teal-200' }}">
                            {{ $to->status_label }}
                        </span>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs rounded-2xl bg-[#f0f5f2] border border-emerald-100">
                        Belum ada riwayat dokumen yang dikumpulkan.
                    </div>
                @endforelse
            </div>
        </div>
    </aside>
</div>
</x-app-layout>
