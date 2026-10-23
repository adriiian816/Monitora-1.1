<x-app-layout pageTitle="Penugasan Bidang Ekop">
<main class="flex-1 p-6 md:p-8 overflow-y-auto bg-[#fafcfb]">
    <div class="flex flex-col w-full max-w-7xl mx-auto gap-6">

        {{-- ===== PAGE HEADER ===== --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white text-[#059669] flex items-center justify-center border border-emerald-200 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">assignment_ind</span>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">Penugasan Internal Bidang</h1>
                        <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                            Bidang Perekonomian &amp; Perdagangan
                        </span>
                    </div>
                    <p class="text-[13px] text-slate-500 mt-0.5">Daftar instruksi tugas, delegasi kegiatan anggota bidang, serta pemantauan laporan hasil</p>
                </div>
            </div>
            
            @if(Auth::user()->isAdminOrSuperAdmin())
                <a href="{{ route('admin.penugasan.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#059669] hover:bg-[#047857] text-white text-[13px] font-semibold shadow-md shadow-emerald-900/10 transition-all hover:scale-[1.01] active:scale-[0.99] shrink-0 self-start sm:self-auto rounded-full">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span> Buat Penugasan Baru</span>
                </a>
            @endif
        </div>

        {{-- ===== ALERT NOTIFICATIONS ===== --}}
        @if(session('success'))
            <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl px-5 py-3.5 text-xs font-semibold shadow-xs">
                <span class="material-symbols-outlined text-emerald-600 text-[20px] shrink-0">check_circle</span>
                <div class="mt-0.5">{{ session('success') }}</div>
            </div>
        @endif

        {{-- ===== SUMMARY KPI CARDS ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Total Penugasan --}}
            <div class="bg-white p-5 rounded-3xl border border-emerald-100/80 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-[#059669] border border-emerald-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">assignment</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Penugasan</div>
                    <div class="text-2xl font-black text-slate-900 leading-tight">{{ $totalPenugasan }}</div>
                    <div class="text-[10px] text-slate-400 font-medium">Kegiatan internal</div>
                </div>
            </div>

            {{-- Card 2: Tugas Selesai --}}
            <div class="bg-white p-5 rounded-3xl border border-emerald-100/80 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-[#059669] border border-emerald-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">task_alt</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tugas Selesai</div>
                    <div class="text-2xl font-black text-emerald-700 leading-tight">{{ $totalSelesai }}</div>
                    <div class="text-[10px] text-[#059669] font-semibold">Tuntas terlaksana</div>
                </div>
            </div>

            {{-- Card 3: Sedang Dikerjakan --}}
            <div class="bg-white p-5 rounded-3xl border border-emerald-100/80 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-700 border border-teal-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">pending_actions</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Sedang Dikerjakan</div>
                    <div class="text-2xl font-black text-teal-700 leading-tight">{{ $totalSedang }}</div>
                    <div class="text-[10px] text-teal-700 font-semibold">Dalam proses</div>
                </div>
            </div>

            {{-- Card 4: Laporan Masuk --}}
            <div class="bg-white p-5 rounded-3xl border border-emerald-100/80 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">description</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Laporan Masuk</div>
                    <div class="text-2xl font-black text-amber-700 leading-tight">
                        {{ $totalLaporanSudah }} <span class="text-xs font-semibold text-slate-400">/ {{ $totalLaporanSudah + $totalLaporanBelum }}</span>
                    </div>
                    <div class="text-[10px] text-amber-700 font-semibold">Telah dilampirkan</div>
                </div>
            </div>
        </div>

        {{-- ===== FILTER & SEARCH BAR ===== --}}
        <form method="GET" action="{{ route('admin.penugasan.index') }}"
              class="bg-white p-2 shadow-xs border border-emerald-100/80 flex flex-col md:flex-row items-stretch gap-2.5 rounded-full">
            
            {{-- Search Input --}}
            <div class="relative flex-1 flex items-center bg-[#f0f5f2] px-3.5 py-2 focus-within:bg-white focus-within:ring-2 focus-within:ring-[#059669]/20 transition-all rounded-full">
                <span class="material-symbols-outlined text-slate-400 text-[20px] mr-2.5">search</span>
                <input class="w-full bg-transparent p-0 text-slate-800 placeholder:text-slate-400 text-[13px] focus:outline-none focus:ring-0"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari tugas atau nama anggota bidang..."
                       type="text">
            </div>

            {{-- Status Tugas Select --}}
            <div class="relative min-w-[190px]">
                <select name="status" class="w-full h-full bg-[#f0f5f2] hover:bg-[#e6efe9] text-slate-700 text-[13px] font-medium py-2.5 pl-4 pr-10 appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#059669]/20 transition-colors rounded-full">
                    <option value="">Semua Status Tugas</option>
                    <option value="belum_dikerjakan" {{ request('status') == 'belum_dikerjakan' ? 'selected' : '' }}>Belum Dikerjakan</option>
                    <option value="sedang_dikerjakan" {{ request('status') == 'sedang_dikerjakan' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
            </div>

            {{-- Status Laporan Select --}}
            <div class="relative min-w-[190px]">
                <select name="status_laporan" class="w-full h-full bg-[#f0f5f2] hover:bg-[#e6efe9] text-slate-700 text-[13px] font-medium py-2.5 pl-4 pr-10 appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#059669]/20 transition-colors rounded-full">
                    <option value="">Semua Status Laporan</option>
                    <option value="belum_dilaporkan" {{ request('status_laporan') == 'belum_dilaporkan' ? 'selected' : '' }}>Belum Lapor</option>
                    <option value="sudah_dilaporkan" {{ request('status_laporan') == 'sudah_dilaporkan' ? 'selected' : '' }}>Sudah Lapor</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-[#059669] hover:bg-[#047857] text-white text-[13px] font-semibold shadow-xs flex items-center justify-center gap-2 transition-colors rounded-full">
                    <span class="material-symbols-outlined text-[18px]">tune</span>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['search', 'status', 'status_laporan']))
                    <a href="{{ route('admin.penugasan.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-medium flex items-center gap-1.5 transition-colors rounded-full">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </form>

        {{-- Status Bar --}}
        <div class="flex items-center justify-between px-1">
            <div class="flex items-center gap-2 text-slate-500 text-[12px]">
                <span>Menampilkan <strong class="text-[#064e3b] font-bold text-[13px]">{{ $penugasanList->total() }}</strong> penugasan bidang</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="text-[#059669] font-bold">Sinkronisasi Internal Bidang Ekop</span>
            </div>
            <div class="flex items-center gap-3 text-[12px] text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Selesai</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-teal-500"></span> Proses</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Belum Lapor</span>
            </div>
        </div>

        {{-- ===== TABEL PENUGASAN INTERNAL BIDANG ===== --}}
        <div class="bg-white rounded-3xl border border-emerald-100/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-[#f7faf8] border-b border-emerald-100/70 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <tr>
                            <th scope="col" class="px-6 py-4">Tugas / Kegiatan</th>
                            <th scope="col" class="px-6 py-4">Penerima (Anggota Bidang)</th>
                            <th scope="col" class="px-6 py-4">Tanggal Kegiatan</th>
                            <th scope="col" class="px-6 py-4">Status Penugasan</th>
                            <th scope="col" class="px-6 py-4">Status Laporan</th>
                            <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($penugasanList as $penugasan)
                            @php
                                $totalPenerima = $penugasan->penerima->count();
                                $selesaiCount = $penugasan->penerima->where('pivot.status_penugasan', 'selesai')->count();
                                $sedangCount = $penugasan->penerima->where('pivot.status_penugasan', 'sedang_dikerjakan')->count();
                                $belumCount = $penugasan->penerima->where('pivot.status_penugasan', 'belum_dikerjakan')->count();
                                $laporanSudah = $penugasan->penerima->where('pivot.status_laporan', 'sudah_dilaporkan')->count();
                                $pct = $totalPenerima > 0 ? round(($selesaiCount / $totalPenerima) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-[#f0f8f4] transition-colors group">
                                {{-- Tugas / Kegiatan --}}
                                <td class="px-6 py-4 max-w-sm">
                                    <a href="{{ route('admin.penugasan.show', $penugasan) }}" 
                                       class="font-bold text-[#064e3b] text-sm leading-snug line-clamp-2 group-hover:text-[#059669] transition-colors" 
                                       title="{{ $penugasan->judul }}">
                                        {{ $penugasan->judul }}
                                    </a>
                                    <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1.5 flex-wrap">
                                        <span>Oleh: <strong class="text-slate-600">{{ $penugasan->pemberi?->name ?? 'Admin' }}</strong></span>
                                        @if($penugasan->deskripsi)
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="truncate max-w-[200px]" title="{{ $penugasan->deskripsi }}">{{ $penugasan->deskripsi }}</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Penerima (Anggota Bidang) --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1.5">
                                        @foreach($penugasan->penerima->take(3) as $anggota)
                                            <div class="flex items-center gap-2">
                                                <span class="w-6 h-6 rounded-full bg-emerald-100 text-[#064e3b] font-bold text-[10px] flex items-center justify-center shrink-0 border border-emerald-200">
                                                    {{ strtoupper(substr($anggota->name, 0, 1)) }}
                                                </span>
                                                <div class="leading-tight">
                                                    <div class="font-semibold text-slate-800 text-xs">{{ $anggota->name }}</div>
                                                    @if($anggota->no_hp)
                                                        @php
                                                            $waPhone = preg_replace('/[^0-9]/', '', $anggota->no_hp);
                                                            if (str_starts_with($waPhone, '0')) {
                                                                $waPhone = '62'.substr($waPhone, 1);
                                                            }
                                                        @endphp
                                                        <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="text-[10px] text-[#059669] font-medium hover:underline inline-flex items-center gap-0.5">
                                                            <span class="material-symbols-outlined text-[12px]">chat</span>
                                                            {{ $anggota->no_hp }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($totalPenerima > 3)
                                            <span class="text-[11px] text-[#059669] font-bold ml-8">
                                                +{{ $totalPenerima - 3 }} anggota lainnya
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Tanggal Kegiatan --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-[#059669]">event</span>
                                        {{ $penugasan->tanggal_kegiatan->translatedFormat('d M Y') }}
                                    </div>
                                    @if($penugasan->batas_laporan)
                                        <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                                            <span>Deadline lapor:</span>
                                            <span class="font-medium {{ $penugasan->isBatasLaporanPassed() ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                                {{ $penugasan->batas_laporan->translatedFormat('d M Y') }}
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                {{-- Status Penugasan --}}
                                <td class="px-6 py-4">
                                    @php
                                        $konfCount = $penugasan->penerima->where('pivot.status_konfirmasi', 'dikonfirmasi')->count();
                                    @endphp
                                    @if($pct === 100)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-[#059669] border border-emerald-200">
                                            <span class="material-symbols-outlined text-[14px]">check</span>
                                            Selesai ({{ $selesaiCount }}/{{ $totalPenerima }})
                                        </span>
                                    @elseif($selesaiCount > 0 || $sedangCount > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                            <span class="material-symbols-outlined text-[14px] animate-spin">sync</span>
                                            Sedang Dikerjakan ({{ $selesaiCount }}/{{ $totalPenerima }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Belum Dikerjakan
                                        </span>
                                    @endif

                                    {{-- Progress Bar --}}
                                    <div class="w-28 mt-2 h-1.5 rounded-full bg-[#f0f5f2] overflow-hidden">
                                        <div class="h-full rounded-full {{ $pct === 100 ? 'bg-[#059669]' : 'bg-teal-600' }} transition-all" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        Konfirmasi: <span class="font-bold text-indigo-700">{{ $konfCount }}/{{ $totalPenerima }}</span>
                                    </div>
                                </td>

                                {{-- Status Laporan --}}
                                <td class="px-6 py-4">
                                    @if($laporanSudah === $totalPenerima && $totalPenerima > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-[#059669] border border-emerald-200">
                                            <span class="material-symbols-outlined text-[14px]">task</span>
                                            Sudah Dilaporkan
                                        </span>
                                    @elseif($laporanSudah > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            <span class="material-symbols-outlined text-[14px]">timelapse</span>
                                            Sebagian ({{ $laporanSudah }}/{{ $totalPenerima }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $penugasan->isBatasLaporanPassed() ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                            @if($penugasan->isBatasLaporanPassed())
                                                <span class="material-symbols-outlined text-[14px] text-rose-600">warning</span>
                                                Terlambat Lapor
                                            @else
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Belum Dilaporkan
                                            @endif
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.penugasan.show', $penugasan) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-white hover:bg-emerald-50 text-[#064e3b] hover:text-[#059669] font-bold text-xs border border-emerald-200 shadow-xs transition"
                                           title="Lihat Rincian & Status">
                                            <span class="material-symbols-outlined text-[15px]">visibility</span>
                                            <span>Detail</span>
                                        </a>

                                        @if(Auth::user()->isAdminOrSuperAdmin())
                                            <form method="POST" action="{{ route('admin.penugasan.destroy', $penugasan) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus penugasan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-1.5 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" 
                                                        title="Hapus Penugasan">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 mx-auto rounded-2xl bg-[#f0f5f2] text-slate-400 flex items-center justify-center mb-3">
                                        <span class="material-symbols-outlined text-[26px]">assignment_late</span>
                                    </div>
                                    <p class="font-bold text-slate-700 text-sm">Belum ada data penugasan internal bidang.</p>
                                    @if(Auth::user()->isAdminOrSuperAdmin())
                                        <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Buat Penugasan Baru" untuk menugaskan anggota Bidang Ekop.</p>
                                        <a href="{{ route('admin.penugasan.create') }}" class="mt-4 inline-flex items-center gap-1.5 px-5 py-2 rounded-full bg-[#059669] hover:bg-[#047857] text-white font-bold text-xs shadow-xs transition">
                                            <span class="material-symbols-outlined text-[16px]">add</span>
                                            <span>Buat Penugasan Pertama</span>
                                        </a>
                                    @else
                                        <p class="text-xs text-slate-400 mt-1">Anda belum memiliki penugasan internal yang didelegasikan saat ini.</p>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($penugasanList->hasPages())
            <div class="mt-2">
                {{ $penugasanList->links() }}
            </div>
        @endif
    </div>
</main>
</x-app-layout>
