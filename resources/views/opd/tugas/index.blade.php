<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Daftar Penugasan Dokumen OPD</h1>
            <p class="text-xs text-slate-500 mt-1">Seluruh instruksi tugas dari Bappeda yang wajib diselesaikan oleh {{ $opd->nama_opd }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6">
        <form method="GET" action="{{ route('opd.tugas.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari judul tugas..." 
                       class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-100 focus:border-blue-600">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div>
                <select name="status" class="w-full py-2 px-3 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-100 focus:border-blue-600">
                    <option value="">Semua Status</option>
                    <option value="perlu_tindakan" {{ request('status') == 'perlu_tindakan' ? 'selected' : '' }}>Perlu Tindakan (Belum/Revisi/Telat)</option>
                    <option value="belum_dikumpulkan" {{ request('status') == 'belum_dikumpulkan' ? 'selected' : '' }}>Belum Dikumpulkan</option>
                    <option value="sudah_dikumpulkan" {{ request('status') == 'sudah_dikumpulkan' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima (Selesai)</option>
                    <option value="perlu_revisi" {{ request('status') == 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                    <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-blue-900 text-white font-semibold text-sm hover:bg-blue-950 transition text-center">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'status', 'format']))
                    <a href="{{ route('opd.tugas.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 text-slate-600 font-medium text-sm hover:bg-slate-200 transition text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tasks List -->
    <div class="space-y-4">
        @forelse($tugasOpdList as $to)
            @php
                $tugas = $to->tugas;
                $sub = $to->submission;
                $isOverdue = $tugas->isPastDeadline();
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 hover:shadow-md transition">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="space-y-2 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($to->status === 'diterima')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-900 border border-blue-200">
                                    ✓ Diterima (Disetujui)
                                </span>
                            @elseif($to->status === 'sudah_dikumpulkan')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                    ⏳ Menunggu Verifikasi
                                </span>
                            @elseif($to->status === 'perlu_revisi')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-900 border border-orange-200">
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
                                {{ $tugas->format_label }}
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                                TA {{ $tugas->tahun_anggaran }}
                            </span>
                        </div>

                        <h2 class="text-base font-bold text-slate-900">
                            <a href="{{ route('opd.tugas.show', $tugas) }}" class="hover:text-blue-700 transition">
                                {{ $tugas->judul }}
                            </a>
                        </h2>

                        @if($tugas->deskripsi)
                            <p class="text-xs text-slate-500 line-clamp-2">{{ $tugas->deskripsi }}</p>
                        @endif

                        @if($sub && $sub->catatan_admin)
                            <div class="mt-2 p-2.5 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-900">
                                <strong>Catatan PIC:</strong> "{{ $sub->catatan_admin }}"
                            </div>
                        @endif
                    </div>

                    <!-- Right Column: Deadline & Action Buttons -->
                    <div class="flex flex-col sm:flex-row lg:flex-col items-start lg:items-end justify-between gap-3 shrink-0 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                        <div class="text-left lg:text-right">
                            <span class="text-xs font-bold block {{ $isOverdue ? 'text-rose-600' : 'text-slate-800' }}">
                                ⏰ {{ $tugas->deadline->translatedFormat('d F Y - H:i') }} WIT
                            </span>
                            <span class="text-[11px] font-semibold block {{ $isOverdue ? 'text-rose-500' : 'text-blue-700' }}">
                                {{ $tugas->sisa_waktu_label }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('opd.tugas.ics', $tugas) }}" 
                               class="p-2 rounded-xl bg-slate-100 hover:bg-blue-100 text-slate-700 hover:text-blue-800 text-xs font-semibold transition" 
                               title="Unduh Kalender .ics">
                                📅
                            </a>
                            <a href="{{ route('opd.tugas.show', $tugas) }}" 
                               class="px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white text-xs font-bold transition shadow-sm">
                                {{ $to->status === 'belum_dikumpulkan' ? 'Unggah Dokumen' : 'Detail / Revisi' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>           </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
                Tidak ada penugasan yang ditemukan.
            </div>
        @endforelse

        @if($tugasOpdList->hasPages())
            <div class="mt-4">
                {{ $tugasOpdList->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
