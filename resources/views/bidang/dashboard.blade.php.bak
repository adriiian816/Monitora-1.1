<x-app-layout pageTitle="Dashboard Staf Bidang">
{{-- ===== TOP CONTROL BAR ===== --}}
<div class="px-4 sm:px-6 md:px-8 py-3.5 bg-[#f7faf8] border-b border-emerald-100/70 flex flex-col 2xl:flex-row 2xl:items-center justify-between gap-3 shrink-0">
    {{-- Left: User & Bidang Identity --}}
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-white text-[#059669] flex items-center justify-center border border-emerald-200 shadow-xs">
            <span class="material-symbols-outlined text-[22px]">badge</span>
        </div>
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">{{ $user->name }}</h1>
                <span class="text-[11px] font-semibold text-indigo-800 bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 rounded-full">
                    Anggota Bidang
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Bidang Perekonomian &amp; Perdagangan Bappeda Malut
                @if($user->no_hp)
                    • <span class="text-[#059669] font-medium font-mono">WA: {{ $user->no_hp }}</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Right: Stat Ticker --}}
    <div class="flex items-center flex-wrap gap-2">
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-white border border-emerald-100 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Total Tugas</span>
                <span class="text-base font-extrabold text-slate-900 leading-tight">{{ $totalPenugasan }}</span>
            </div>
            <span class="text-[10px] text-slate-400 font-medium">Kegiatan</span>
        </div>
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-200 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-indigo-700">Dikonfirmasi</span>
                <span class="text-base font-extrabold text-indigo-800 leading-tight">{{ $totalDikonfirmasi }}</span>
            </div>
            <span class="text-[10px] text-indigo-700 font-bold bg-white px-1.5 py-0.5 rounded">Siap</span>
        </div>
        @if($totalMenungguKonfirmasi > 0)
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-300 shadow-xs whitespace-nowrap animate-pulse">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-amber-800">Perlu Konfirmasi</span>
                <span class="text-base font-extrabold text-amber-900 leading-tight">{{ $totalMenungguKonfirmasi }}</span>
            </div>
            <span class="text-[10px] text-amber-800 font-bold bg-white px-1.5 py-0.5 rounded">Baru</span>
        </div>
        @endif
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-teal-50 border border-teal-200 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-teal-700">Sedang Dikerjakan</span>
                <span class="text-base font-extrabold text-teal-800 leading-tight">{{ $totalSedang }}</span>
            </div>
            <span class="text-[10px] text-teal-700 font-bold bg-white px-1.5 py-0.5 rounded">Proses</span>
        </div>
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-amber-700">Belum Lapor</span>
                <span class="text-base font-extrabold text-amber-800 leading-tight">{{ $totalLaporanBelum }}</span>
            </div>
            <span class="text-[10px] text-amber-700 font-bold bg-white px-1.5 py-0.5 rounded">Wajib Lapor</span>
        </div>
        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 shadow-xs whitespace-nowrap">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-wider text-[#064e3b]">Tugas Selesai</span>
                <span class="text-base font-extrabold text-emerald-700 leading-tight">{{ $totalSelesai }}</span>
            </div>
            <span class="text-[10px] text-emerald-700 font-bold bg-white px-1.5 py-0.5 rounded">Tuntas</span>
        </div>
        <a href="{{ route('admin.penugasan.index') }}"
           class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#064e3b] text-white text-[12px] font-bold shadow-sm hover:bg-[#043d2e] transition whitespace-nowrap ml-1">
            <span class="material-symbols-outlined text-[16px]">account_tree</span>
            <span>Semua Penugasan</span>
        </a>
    </div>
</div>

{{-- ===== MAIN SPLIT-PANEL WORKSPACE ===== --}}
<div class="flex-1 flex flex-col lg:flex-row min-w-0">

    {{-- ===== LEFT PANEL: Daftar Tugas Saya (65%) ===== --}}
    <section class="flex-1 lg:w-[65%] p-4 sm:p-5 lg:p-6 flex flex-col gap-6">

        {{-- Welcome Banner Card --}}
        <div class="p-6 rounded-3xl bg-gradient-to-r from-[#064e3b] via-[#065f46] to-[#047857] text-white shadow-xs border border-emerald-700/40 relative overflow-hidden">
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/15 border border-white/20 text-emerald-100 text-[10px] font-bold uppercase tracking-wider mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                        Internal Bidang Perekonomian &amp; Perdagangan
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                        Selamat Datang, {{ $user->name }}
                    </h2>
                    <p class="text-xs text-emerald-100/80 mt-1">
                        Pantau instruksi penugasan kegiatan bidang dan perbarui status pelaksanaan serta pelaporan secara berkala.
                    </p>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('admin.penugasan.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white hover:bg-emerald-50 text-[#064e3b] font-bold text-xs shadow-sm transition">
                        <span class="material-symbols-outlined text-[16px] text-[#059669]">list_alt</span>
                        <span>Daftar Kegiatan ({{ $totalPenugasan }})</span>
                    </a>
                </div>
            </div>
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-emerald-300/10 rounded-full blur-2xl pointer-events-none"></div>
        </div>

        {{-- Assigned Tasks Container --}}
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Tugas &amp; Kegiatan yang Ditugaskan ke Saya</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Daftar agenda kegiatan bidang di mana Anda didelegasikan sebagai pelaksana</p>
                </div>
            </div>

            <div class="border border-emerald-100/80 rounded-3xl overflow-hidden bg-white shadow-xs divide-y divide-slate-100">
                @forelse($penugasanSaya as $p)
                    @php
                        $myPivot = $p->penerima->first()?->pivot;
                        $statusKonf = $myPivot?->status_konfirmasi ?? 'menunggu_konfirmasi';
                        $statusTugas = $myPivot?->status_penugasan ?? 'belum_dikerjakan';
                        $statusLap = $myPivot?->status_laporan ?? 'belum_dilaporkan';
                        $isBatasPassed = $p->isBatasLaporanPassed();
                    @endphp
                    <div class="p-5 sm:p-6 hover:bg-[#f8fbf9] transition flex flex-col gap-3">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                {{-- Status Konfirmasi Badge --}}
                                @if($statusKonf === 'dikonfirmasi')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">verified</span>
                                        Siap Laksanakan
                                    </span>
                                @elseif($statusKonf === 'ditolak')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">cancel</span>
                                        Berhalangan
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-300 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">timelapse</span>
                                        Perlu Konfirmasi
                                    </span>
                                @endif

                                {{-- Status Pengerjaan Badge --}}
                                @if($statusTugas === 'selesai')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 border border-emerald-200 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">check</span>
                                        Tugas Selesai
                                    </span>
                                @elseif($statusTugas === 'sedang_dikerjakan')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-900 border border-teal-200 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px] animate-spin">sync</span>
                                        Sedang Dikerjakan
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        Belum Dikerjakan
                                    </span>
                                @endif

                                {{-- Status Laporan Badge --}}
                                @if($statusLap === 'sudah_dilaporkan')
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">task</span>
                                        Laporan Sudah Masuk
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $isBatasPassed ? 'bg-rose-50 text-rose-700 border border-rose-200 font-bold' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                        {{ $isBatasPassed ? '⚠️ Terlambat Lapor' : 'Laporan Belum Diserahkan' }}
                                    </span>
                                @endif
                            </div>

                            <div class="text-left sm:text-right">
                                <div class="text-xs font-bold text-slate-800 flex items-center sm:justify-end gap-1">
                                    <span class="material-symbols-outlined text-[16px] text-[#059669]">event</span>
                                    <span>{{ $p->tanggal_kegiatan->translatedFormat('d M Y') }}</span>
                                </div>
                                @if($p->batas_laporan)
                                    <div class="text-[10px] {{ $isBatasPassed ? 'text-rose-600 font-bold' : 'text-slate-500' }}">
                                        Batas lapor: {{ $p->batas_laporan->translatedFormat('d M Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-slate-900 hover:text-[#059669] transition">
                                <a href="{{ route('admin.penugasan.show', $p) }}">
                                    {{ $p->judul }}
                                </a>
                            </h4>
                            @if($p->deskripsi)
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $p->deskripsi }}</p>
                            @endif
                        </div>

                        @if($p->catatan)
                            <div class="p-3 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs text-amber-900">
                                <strong class="block mb-0.5 text-amber-950 font-bold">Instruksi / Catatan:</strong>
                                <p class="italic">"{{ $p->catatan }}"</p>
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 flex-wrap gap-2">
                            <span class="text-xs text-slate-400">
                                Pemberi Tugas: <span class="font-semibold text-slate-700">{{ $p->pemberi?->name ?? 'Admin' }}</span>
                            </span>
                            @if($statusKonf === 'menunggu_konfirmasi')
                                <a href="{{ route('admin.penugasan.show', $p) }}" 
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-md shadow-amber-700/20 transition transform active:scale-95">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                    <span>Konfirmasi &amp; Laporan</span>
                                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                </a>
                            @elseif($statusLap === 'belum_dilaporkan')
                                <a href="{{ route('admin.penugasan.show', $p) }}" 
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#059669] hover:bg-[#047857] text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition transform active:scale-95">
                                    <span class="material-symbols-outlined text-[16px]">upload_file</span>
                                    <span>Kumpulkan Laporan</span>
                                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                </a>
                            @else
                                <a href="{{ route('admin.penugasan.show', $p) }}" 
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white hover:bg-emerald-50 text-[#064e3b] font-bold text-xs border border-emerald-200 shadow-xs transition">
                                    <span class="material-symbols-outlined text-[16px] text-[#059669]">visibility</span>
                                    <span>Lihat Detail &amp; Laporan</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-400 text-xs">
                        <span class="material-symbols-outlined text-[42px] text-emerald-200 block mb-2">task_alt</span>
                        <div class="text-sm font-bold text-slate-700">Belum Ada Penugasan</div>
                        <p class="mt-1 text-slate-400">Anda belum menerima penugasan kegiatan dari pimpinan / admin bidang.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ===== RIGHT PANEL: Agenda & Informasi Koordinasi (35%) ===== --}}
    <aside class="w-full lg:w-[35%] shrink-0 border-t lg:border-t-0 lg:border-l border-emerald-100 bg-[#fcfdfc] p-4 sm:p-5 lg:p-6 flex flex-col gap-6">

        {{-- Agenda Internal Bidang --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-[#059669]">calendar_month</span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">AGENDA KEGIATAN BIDANG</h3>
                </div>
                <span class="text-[10px] font-bold text-[#064e3b] bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                    {{ $totalPenugasan }} Agenda
                </span>
            </div>

            <div class="flex flex-col gap-2.5">
                @forelse($penugasanSaya->take(5) as $p)
                    <div class="p-3.5 rounded-2xl bg-[#f0f5f2] border border-emerald-100/80 flex flex-col gap-1.5">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-bold text-[#064e3b] truncate max-w-[180px]">{{ $p->judul }}</span>
                            <span class="text-[10px] text-slate-500 font-semibold">{{ $p->tanggal_kegiatan->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1 border-t border-emerald-200/50">
                            <span>Deadline Lapor: {{ $p->batas_laporan ? $p->batas_laporan->translatedFormat('d M Y') : '-' }}</span>
                            <a href="{{ route('admin.penugasan.show', $p) }}" class="text-[#059669] font-bold hover:underline">
                                Buka &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs rounded-2xl bg-[#f0f5f2] border border-emerald-100">
                        Tidak ada agenda terjadwal saat ini.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pedoman Kerja & Koordinasi --}}
        <div class="p-5 rounded-2xl bg-white border border-emerald-100 shadow-2xs space-y-3">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-[#059669]">verified_user</span>
                <h4 class="text-xs font-bold uppercase tracking-wider text-[#064e3b]">TATA TERTIB ANGGOTA BIDANG</h4>
            </div>
            <ul class="text-xs text-slate-600 space-y-2 list-disc list-inside leading-relaxed">
                <li>Melaksanakan kegiatan sesuai jadwal tanggal pelaksanaan yang telah ditetapkan pimpinan.</li>
                <li>Memperbarui status pengerjaan (<em>Sedang Dikerjakan</em> / <em>Selesai</em>) pada sistem MONITORA.</li>
                <li>Menyerahkan laporan hasil pelaksanaan kegiatan sebelum batas akhir pelaporan berakhir.</li>
            </ul>
        </div>
    </aside>
</div>
</x-app-layout>
