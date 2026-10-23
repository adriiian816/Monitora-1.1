<x-app-layout pageTitle="Tugas Saya">
{{-- ===== TOP CONTROL BAR ===== --}}
<div class="px-6 md:px-8 py-4 bg-[#f7faf8] border-b border-emerald-100/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-white text-[#059669] flex items-center justify-center border border-emerald-200 shadow-xs">
            <span class="material-symbols-outlined text-[22px]">folder_shared</span>
        </div>
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">Daftar Penugasan Dokumen OPD</h1>
                <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full font-mono">
                    {{ $opd->kode_opd }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Seluruh instruksi tugas dari Bidang Perekonomian dan Perdagangan Bappeda yang wajib diselesaikan oleh {{ $opd->nama_opd }}</p>
        </div>
    </div>
    <div>
        <a href="{{ route('opd.dashboard') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-emerald-200 text-slate-700 text-[12px] font-bold shadow-xs hover:bg-emerald-50 transition">
            <span class="material-symbols-outlined text-[16px] text-[#059669]">dashboard</span>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>
</div>

{{-- ===== WORKSPACE CONTENT ===== --}}
<div class="p-6 md:p-8 flex flex-col gap-6 w-full">

    {{-- ===== FILTER & SEARCH BAR ===== --}}
    <div class="bg-white p-2 sm:p-2.5 shadow-xs border border-emerald-100/80 rounded-full">
        <form method="GET" action="{{ route('opd.tugas.index') }}" class="flex flex-col sm:flex-row items-stretch gap-2.5">
            {{-- Search Input --}}
            <div class="relative flex-1 flex items-center bg-[#f0f5f2] px-3.5 py-2 focus-within:bg-white focus-within:ring-2 focus-within:ring-[#059669]/20 transition-all rounded-full">
                <span class="material-symbols-outlined text-slate-400 text-[20px] mr-2.5">search</span>
                <input class="w-full bg-transparent p-0 text-slate-800 placeholder:text-slate-400 text-[13px] focus:outline-none focus:ring-0 font-medium"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari judul tugas atau dokumen..."
                       type="text">
            </div>

            {{-- Status Filter --}}
            <div class="relative min-w-[220px]">
                <select name="status" class="w-full h-full bg-[#f0f5f2] hover:bg-[#e6efe9] text-slate-700 text-[13px] font-semibold py-2.5 pl-4 pr-10 appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#059669]/20 transition-colors rounded-full">
                    <option value="">Semua Status Pengumpulan</option>
                    <option value="perlu_tindakan" {{ request('status') == 'perlu_tindakan' ? 'selected' : '' }}>⚡ Perlu Tindakan</option>
                    <option value="belum_dikumpulkan" {{ request('status') == 'belum_dikumpulkan' ? 'selected' : '' }}>⏳ Belum Dikumpulkan</option>
                    <option value="sudah_dikumpulkan" {{ request('status') == 'sudah_dikumpulkan' ? 'selected' : '' }}>🔍 Menunggu Verifikasi</option>
                    <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>✅ Diterima (Disetujui)</option>
                    <option value="perlu_revisi" {{ request('status') == 'perlu_revisi' ? 'selected' : '' }}>⚠️ Perlu Revisi</option>
                    <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>❌ Terlambat</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="px-6 py-2.5 bg-[#059669] hover:bg-[#047857] text-white text-[13px] font-bold shadow-md shadow-emerald-900/10 transition rounded-full flex items-center justify-center gap-1.5 shrink-0">
                <span>Filter</span>
            </button>

            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('opd.tugas.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[13px] font-bold transition rounded-full flex items-center justify-center shrink-0">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- ===== TASKS LIST ===== --}}
    <div class="flex flex-col gap-4">
        @forelse($tugasOpdList as $to)
            @php
                $tugas = $to->tugas;
                $sub = $to->submission;
                $isOverdue = $tugas->isPastDeadline();
            @endphp
            <div class="bg-white rounded-3xl border border-emerald-100/80 shadow-xs p-5 sm:p-6 hover:shadow-md transition flex flex-col gap-3 group">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="space-y-2 flex-1 min-w-0">
                        {{-- Badges row --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($to->status === 'diterima')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-[#064e3b] border border-emerald-200">
                                    ✓ Diterima (Disetujui)
                                </span>
                            @elseif($to->status === 'sudah_dikumpulkan')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-900 border border-teal-200">
                                    ⏳ Menunggu Verifikasi
                                </span>
                            @elseif($to->status === 'perlu_revisi')
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

                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                {{ $tugas->format_label }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                TA {{ $tugas->tahun_anggaran }}
                            </span>
                        </div>

                        {{-- Task Title --}}
                        <h2 class="text-base font-bold text-slate-900 group-hover:text-[#059669] transition">
                            <a href="{{ route('opd.tugas.show', $tugas) }}">
                                {{ $tugas->judul }}
                            </a>
                        </h2>

                        @if($tugas->deskripsi)
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ $tugas->deskripsi }}</p>
                        @endif

                        @if($sub && $sub->catatan_admin)
                            <div class="mt-2 p-3 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs text-amber-900">
                                <strong class="text-amber-950 block mb-0.5">Catatan Masukan PIC:</strong>
                                <span class="italic">"{{ $sub->catatan_admin }}"</span>
                            </div>
                        @endif
                    </div>

                    {{-- Deadline & Action Button --}}
                    <div class="flex flex-col sm:flex-row lg:flex-col items-start lg:items-end justify-between gap-3 shrink-0 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                        <div class="text-left lg:text-right">
                            <div class="text-xs font-bold {{ $isOverdue ? 'text-rose-600' : 'text-slate-800' }}">
                                {{ $tugas->deadline->translatedFormat('d F Y - H:i') }} WIT
                            </div>
                            <div class="text-[11px] font-bold {{ $isOverdue ? 'text-rose-500' : 'text-[#059669]' }}">
                                {{ $tugas->sisa_waktu_label }}
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('opd.tugas.ics', $tugas) }}" 
                               class="p-2 rounded-full bg-slate-100 hover:bg-emerald-50 hover:text-[#059669] text-slate-600 text-xs font-semibold transition border border-slate-200" 
                               title="Unduh Pengingat Kalender .ics">
                                <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                            </a>
                            <a href="{{ route('opd.tugas.show', $tugas) }}" 
                               class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white text-xs font-bold transition shadow-md shadow-emerald-700/20 transform active:scale-95">
                                <span>{{ $to->status === 'belum_dikumpulkan' ? 'Unggah Dokumen' : 'Detail / Revisi' }}</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-emerald-100/80 p-12 text-center text-slate-400">
                <span class="material-symbols-outlined text-[48px] text-emerald-200 block mb-2">folder_open</span>
                <div class="text-sm font-bold text-slate-700">Tidak ada penugasan yang ditemukan</div>
                <p class="text-xs text-slate-400 mt-1">Coba ubah kata kunci pencarian atau reset filter status.</p>
            </div>
        @endforelse

        @if($tugasOpdList->hasPages())
            <div class="mt-2">
                {{ $tugasOpdList->links() }}
            </div>
        @endif
    </div>
</div>
</x-app-layout>
