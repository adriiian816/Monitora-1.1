<x-app-layout pageTitle="Penugasan OPD">
<style>
    .bg-striped-progress {
        background-image: linear-gradient(45deg, rgba(255,255,255,0.2) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.2) 75%, transparent 75%, transparent);
        background-size: 1rem 1rem;
    }
</style>

<main class="flex-1 p-6 md:p-8 overflow-y-auto bg-[#fafcfb]">
    <div class="flex flex-col w-full max-w-7xl mx-auto gap-6">

        {{-- ===== PAGE HEADER ===== --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white text-[#059669] flex items-center justify-center border border-emerald-200 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">assignment</span>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">Manajemen Penugasan Dokumen OPD</h1>
                        <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">T.A. {{ date('Y') }} • Triwulan I</span>
                    </div>
                    <p class="text-[13px] text-slate-500 mt-0.5">Buat instruksi tugas, pilih format pengumpulan, dan pantau respons OPD secara terpusat</p>
                </div>
            </div>
            @if(Auth::user()->isAdminOrSuperAdmin())
                <a href="{{ route('admin.tugas.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#059669] hover:bg-[#047857] text-white text-[13px] font-semibold shadow-md shadow-emerald-900/10 transition-all hover:scale-[1.01] active:scale-[0.99] shrink-0 self-start sm:self-auto rounded-full">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>Buat Tugas Baru</span>
                </a>
            @endif
        </div>

        {{-- ===== FILTER BAR ===== --}}
        <form method="GET" action="{{ route('admin.tugas.index') }}"
              class="bg-white p-2 shadow-xs border border-emerald-100/80 flex flex-col md:flex-row items-stretch gap-2.5 rounded-full">
            {{-- Search --}}
            <div class="relative flex-1 flex items-center bg-[#f0f5f2] px-3.5 py-2 focus-within:bg-white focus-within:ring-2 focus-within:ring-[#059669]/20 transition-all rounded-full">
                <span class="material-symbols-outlined text-slate-400 text-[20px] mr-2.5">search</span>
                <input class="w-full bg-transparent p-0 text-slate-800 placeholder:text-slate-400 text-[13px] focus:outline-none focus:ring-0"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari judul tugas atau tahun anggaran..."
                       type="text">
            </div>

            {{-- Format Filter --}}
            <div class="relative min-w-[200px]">
                <select name="format" class="w-full h-full bg-[#f0f5f2] hover:bg-[#e6efe9] text-slate-700 text-[13px] font-medium py-2.5 pl-4 pr-10 appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#059669]/20 transition-colors rounded-full">
                    <option value="">Semua Format</option>
                    <option value="digital" {{ request('format') == 'digital' ? 'selected' : '' }}>Digital (Upload File)</option>
                    <option value="hybrid" {{ request('format') == 'hybrid' ? 'selected' : '' }}>Hybrid (Digital &amp; Fisik)</option>
                    <option value="fisik" {{ request('format') == 'fisik' ? 'selected' : '' }}>Fisik (Manual)</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-[#059669] hover:bg-[#047857] text-white text-[13px] font-semibold shadow-xs flex items-center justify-center gap-2 transition-colors rounded-full">
                    <span class="material-symbols-outlined text-[18px]">tune</span>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['search', 'format']))
                    <a href="{{ route('admin.tugas.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-medium flex items-center gap-1.5 transition-colors rounded-full">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- Filter status bar --}}
        <div class="flex items-center justify-between px-1">
            <div class="flex items-center gap-2 text-slate-500 text-[12px]">
                <span>Menampilkan <strong class="text-[#064e3b] font-bold text-[13px]">{{ $tugasList->total() }}</strong> penugasan</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="text-[#059669] font-bold">Sinkronisasi Sofifi Cloud Aktif</span>
            </div>
            <div class="flex items-center gap-3 text-[12px] text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> Berjalan</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Berakhir</span>
            </div>
        </div>

        {{-- ===== TASK CARDS ===== --}}
        <div class="flex flex-col gap-4">
            @forelse($tugasList as $tugas)
                @php
                    $totalOpd = $tugas->total_opd ?: 1;
                    $pct = round(($tugas->total_terkumpul / $totalOpd) * 100);
                    $isOverdue = $tugas->isPastDeadline();
                @endphp

                <div class="task-card bg-white rounded-3xl p-6 shadow-xs hover:shadow-md transition-all flex flex-col gap-4 border {{ $isOverdue ? 'border-rose-200 hover:border-rose-300' : 'border-emerald-100/80 hover:border-emerald-200' }}">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                        {{-- Left: Title & Meta --}}
                        <div class="flex-1 flex flex-col gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-bold
                                    {{ $tugas->status === 'aktif' ? 'bg-emerald-50 text-[#059669] border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($tugas->status) }}
                                </span>
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-medium
                                    {{ $tugas->format === 'hybrid' ? 'bg-amber-50 text-amber-800 border border-amber-200/50' : 'bg-[#f0f5f2] text-slate-600' }}">
                                    {{ $tugas->format_label }}
                                </span>
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full bg-[#f0f5f2] text-slate-600 text-[11px] font-medium">
                                    TA {{ $tugas->tahun_anggaran }}
                                </span>
                            </div>

                            <h2 class="text-base font-bold text-[#064e3b] tracking-tight leading-snug hover:text-[#059669] transition-colors">
                                <a href="{{ route('admin.tugas.show', $tugas) }}">{{ $tugas->judul }}</a>
                            </h2>

                            @if($tugas->deskripsi)
                                <p class="text-[13px] text-slate-500 line-clamp-2 leading-relaxed">{{ $tugas->deskripsi }}</p>
                            @endif
                        </div>

                        {{-- Right: Deadline --}}
                        <div class="flex md:flex-col items-end md:items-end justify-between md:justify-start gap-1 shrink-0 bg-[#f7faf8] md:bg-transparent p-2.5 md:p-0 rounded-2xl">
                            <div class="flex items-center gap-1.5 text-slate-700 text-[12px]">
                                <span class="material-symbols-outlined {{ $isOverdue ? 'text-rose-500' : 'text-[#059669]' }} text-[18px]">
                                    {{ $isOverdue ? 'event_busy' : 'alarm' }}
                                </span>
                                <span class="font-semibold">{{ $tugas->deadline->translatedFormat('d F Y') }} - {{ $tugas->deadline->format('H:i') }} WIT</span>
                            </div>
                            <div class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-0.5 rounded-full
                                {{ $isOverdue ? 'text-rose-600 bg-rose-50 border border-rose-200' : 'text-[#059669] bg-emerald-50 border border-emerald-200' }}">
                                @if(!$isOverdue)
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#059669] animate-pulse"></span>
                                @endif
                                <span>{{ $tugas->sisa_waktu_label }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom: Progress + Actions --}}
                    <div class="pt-3 flex flex-col md:flex-row md:items-center justify-between gap-4 border-t border-slate-100">
                        {{-- Progress --}}
                        <div class="flex-1 max-w-xl flex flex-col gap-1.5">
                            <div class="flex items-center justify-between text-[12px]">
                                <span class="text-slate-500 font-medium">
                                    Terkumpul: <strong class="text-slate-900 font-bold">{{ $tugas->total_terkumpul }}</strong> dari {{ $tugas->total_opd }} OPD
                                </span>
                                <span class="{{ $pct >= 80 ? 'text-[#059669]' : ($pct > 0 ? 'text-teal-700' : 'text-slate-400') }} font-bold">{{ $pct }}%</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-striped-progress transition-all duration-700
                                    {{ $isOverdue && $pct < 100 ? 'bg-rose-500' : 'bg-gradient-to-r from-emerald-500 to-teal-400' }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                            <a href="{{ route('admin.tugas.ics', $tugas) }}"
                               class="w-9 h-9 rounded-xl bg-[#f0f5f2] hover:bg-emerald-50 text-slate-600 hover:text-[#064e3b] flex items-center justify-center transition-colors"
                               title="Unduh Kalender .ics">
                                <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                            </a>
                            <a href="{{ route('admin.tugas.show', $tugas) }}"
                               class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-[#059669] hover:bg-[#047857] text-white text-[12px] font-semibold shadow-xs transition-all active:scale-[0.98]">
                                <span class="material-symbols-outlined text-[18px]">fact_check</span>
                                <span>Monitoring OPD</span>
                            </a>
                            @if(Auth::user()->isAdminOrSuperAdmin())
                                <a href="{{ route('admin.tugas.edit', $tugas) }}"
                                   class="w-9 h-9 rounded-xl bg-[#f0f5f2] hover:bg-emerald-50 text-slate-600 hover:text-[#064e3b] flex items-center justify-center transition-colors"
                                   title="Edit Penugasan">
                                    <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                </a>
                                <div x-data="{ showConfirm: false }" class="inline">
                                    <button @click="showConfirm = true"
                                            class="w-9 h-9 rounded-xl bg-[#f0f5f2] hover:bg-rose-50 text-slate-600 hover:text-rose-600 flex items-center justify-center transition-colors"
                                            title="Hapus Tugas"
                                            type="button">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                    <template x-teleport="body">
                                        <div x-show="showConfirm" style="display:none"
                                             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
                                             x-transition.opacity>
                                            <div @click.outside="showConfirm = false"
                                                 class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl"
                                                 x-transition.scale.origin.bottom>
                                                <div class="flex items-center gap-3 mb-4">
                                                    <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                                        <span class="material-symbols-outlined text-[22px]">warning</span>
                                                    </div>
                                                    <h3 class="text-lg font-bold text-slate-900">Hapus Tugas?</h3>
                                                </div>
                                                <p class="text-sm text-slate-600 mb-6">Apakah Anda yakin ingin menghapus tugas ini? Data yang terhubung akan ikut terhapus permanen.</p>
                                                <div class="flex gap-2 justify-end">
                                                    <button @click="showConfirm = false" type="button" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">Batal</button>
                                                    <form action="{{ route('admin.tugas.destroy', $tugas) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-sm">Ya, Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center p-12 bg-white border border-emerald-100/80 rounded-3xl text-center gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-[#f0f5f2] flex items-center justify-center text-slate-400">
                        <span class="material-symbols-outlined text-[32px]">manage_search</span>
                    </div>
                    <h3 class="text-base font-bold text-[#064e3b]">Tidak Ada Tugas yang Ditemukan</h3>
                    <p class="text-[13px] text-slate-500 max-w-sm">Silakan ubah filter kata kunci pencarian atau buat tugas baru.</p>
                    @if(Auth::user()->isAdminOrSuperAdmin())
                        <a href="{{ route('admin.tugas.create') }}"
                           class="mt-2 inline-flex items-center gap-2 px-5 py-2.5 bg-[#059669] hover:bg-[#047857] text-white text-[13px] font-semibold rounded-full transition-all">
                            <span class="material-symbols-outlined text-[18px]">add_circle</span>
                            Buat Tugas Baru
                        </a>
                    @endif
                </div>
            @endforelse

            @if($tugasList->hasPages())
                <div class="mt-2">
                    {{ $tugasList->links() }}
                </div>
            @endif
        </div>

        {{-- ===== INFO BANNER ===== --}}
        <div class="flex items-center gap-3.5 p-4 rounded-2xl bg-[#e6f4ee] border border-emerald-200/70 text-slate-700">
            <div class="w-8 h-8 rounded-full bg-[#059669] text-white flex items-center justify-center shrink-0 shadow-xs">
                <span class="material-symbols-outlined text-[18px]">info</span>
            </div>
            <p class="text-[12px] leading-relaxed text-slate-700">
                <span class="font-bold text-[#064e3b]">Informasi Pengiriman:</span>
                Penugasan dokumen baru akan otomatis terkirim melalui antrean notifikasi WhatsApp Gateway resmi Bidang Perekonomian dan Perdagangan Bappeda Malut ke setiap Kepala Sub Bagian Perencanaan OPD terkait.
            </p>
        </div>
    </div>
</main>
</x-app-layout>
