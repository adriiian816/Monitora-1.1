<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Penugasan Dokumen OPD</h1>
            <p class="text-xs text-slate-500 mt-1">Buat instruksi tugas, pilih format pengumpulan, dan pantau respons OPD secara terpusat</p>
        </div>
        <a href="{{ route('admin.tugas.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm shadow-sm transition shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Buat Tugas Baru
        </a>
    </div>

    <!-- Filters Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.tugas.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari judul tugas atau tahun anggaran..." 
                       class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-100 focus:border-blue-600">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div>
                <select name="format" class="w-full py-2 px-3 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-100 focus:border-blue-600">
                    <option value="">Semua Format</option>
                    <option value="digital" {{ request('format') == 'digital' ? 'selected' : '' }}>Digital (Upload File)</option>
                    <option value="fisik" {{ request('format') == 'fisik' ? 'selected' : '' }}>Fisik (Manual)</option>
                    <option value="hybrid" {{ request('format') == 'hybrid' ? 'selected' : '' }}>Hybrid (Keduanya)</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-blue-900 text-white font-semibold text-sm hover:bg-blue-950 transition text-center">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'format', 'status']))
                    <a href="{{ route('admin.tugas.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 text-slate-600 font-medium text-sm hover:bg-slate-200 transition text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Task List -->
    <div class="space-y-4">
        @forelse($tugasList as $tugas)
            @php
                $totalOpd = $tugas->total_opd ?: 1;
                $pct = round(($tugas->total_terkumpul / $totalOpd) * 100);
                $isOverdue = $tugas->isPastDeadline();
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 hover:shadow-md transition">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="space-y-2 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $tugas->status === 'aktif' ? 'bg-blue-100 text-blue-900' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($tugas->status) }}
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                                {{ $tugas->format_label }}
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                                TA {{ $tugas->tahun_anggaran }}
                            </span>
                        </div>

                        <h2 class="text-base font-bold text-slate-900">
                            <a href="{{ route('admin.tugas.show', $tugas) }}" class="hover:text-blue-700 transition">
                                {{ $tugas->judul }}
                            </a>
                        </h2>

                        @if($tugas->deskripsi)
                            <p class="text-xs text-slate-500 line-clamp-2">{{ $tugas->deskripsi }}</p>
                        @endif

                        <!-- Progress indicator -->
                        <div class="pt-2 max-w-md">
                            <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                                <span>Terkumpul: <strong class="text-slate-800">{{ $tugas->total_terkumpul }} dari {{ $tugas->total_opd }} OPD</strong></span>
                                <span class="font-bold text-slate-800">{{ $pct }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-700 to-sky-500 h-2.5 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Deadline info & Actions -->
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
                            <a href="{{ route('admin.tugas.ics', $tugas) }}" 
                               class="p-2 rounded-xl bg-slate-100 hover:bg-blue-100 text-slate-700 hover:text-blue-800 text-xs font-semibold transition" 
                               title="Unduh Kalender .ics">
                                📅
                            </a>
                            <a href="{{ route('admin.tugas.show', $tugas) }}" 
                               class="px-3.5 py-2 rounded-xl bg-blue-900 hover:bg-blue-950 text-white text-xs font-bold transition shadow-sm">
                                Monitoring OPD
                            </a>
                            <a href="{{ route('admin.tugas.edit', $tugas) }}" 
                               class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition" 
                               title="Edit Tugas">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if(auth()->user()->isAdminOrSuperAdmin())
                            <div x-data="{ showConfirm: false }" class="inline">
                                <form action="{{ route('admin.tugas.destroy', $tugas) }}" method="POST" class="inline" @submit.prevent="showConfirm = true" x-ref="deleteForm">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl bg-slate-100 hover:bg-rose-100 text-slate-600 hover:text-rose-600 transition" title="Hapus Tugas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                
                                <template x-teleport="body">
                                    <div x-show="showConfirm" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-transition.opacity>
                                        <div @click.outside="showConfirm = false" class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl" x-transition.scale.origin.bottom>
                                            <div class="flex items-center gap-3 mb-4">
                                                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                </div>
                                                <h3 class="text-lg font-bold text-slate-900">Hapus Tugas?</h3>
                                            </div>
                                            <p class="text-sm text-slate-600 mb-6">Apakah Anda yakin ingin menghapus tugas ini? Data yang terhubung akan ikut terhapus permanen.</p>
                                            <div class="flex gap-2 justify-end">
                                                <button @click="showConfirm = false" type="button" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">Batal</button>
                                                <button @click="$refs.deleteForm.submit()" type="button" class="px-4 py-2 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-sm">Ya, Hapus</button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
                Belum ada data tugas. Silakan buat tugas baru.
            </div>
        @endforelse

        @if($tugasList->hasPages())
            <div class="mt-4">
                {{ $tugasList->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
