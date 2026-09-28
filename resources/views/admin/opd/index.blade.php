<x-app-layout>
    <div x-data="{ showDeleteModal: false, deleteActionUrl: '', opdName: '' }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Master Data OPD</h1>
                <p class="text-xs text-slate-500 mt-1">Kelola daftar Organisasi Perangkat Daerah beserta nomor WhatsApp PIC penerima notifikasi</p>
            </div>
            <a href="{{ route('admin.opd.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm shadow-sm transition shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Tambah OPD Baru
            </a>
        </div>

        <!-- Search & Filter Card -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6">
            <form method="GET" action="{{ route('admin.opd.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama instansi OPD atau kode (mis: DISPERINDAG)..." 
                           class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-900 text-white font-semibold text-sm hover:bg-blue-800 transition">
                    Filter Data
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.opd.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-medium text-sm hover:bg-slate-200 transition text-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-700">
                        <tr>
                            <th scope="col" class="px-6 py-4">Kode & Nama Instansi OPD</th>
                            <th scope="col" class="px-6 py-4">Staf OPD & Nomor WhatsApp</th>
                            <th scope="col" class="px-6 py-4 text-center">Statistik Tugas</th>
                            <th scope="col" class="px-6 py-4 text-center">Status</th>
                            <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($opds as $opd)
                            <tr class="hover:bg-slate-50/60 transition">
                                <!-- OPD info -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-800 font-mono text-xs border border-blue-100 font-bold">
                                            {{ $opd->kode_opd }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-slate-800 font-semibold mt-1">{{ $opd->nama_opd }}</div>
                                    @if($opd->alamat)
                                        <div class="text-[11px] text-slate-500 mt-0.5">{{ Str::limit($opd->alamat, 55) }}</div>
                                    @endif
                                </td>

                                <!-- Staf OPD & Kontak WA -->
                                <td class="px-6 py-4">
                                    @if($opd->users->count() > 0)
                                        <div class="space-y-1.5">
                                            @foreach($opd->users->take(2) as $staff)
                                                <div class="text-xs flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-semibold text-slate-900">{{ $staff->name }}</span>
                                                    @if($staff->no_hp)
                                                        @php
                                                            $cleanPhone = preg_replace('/[^0-9]/', '', $staff->no_hp);
                                                            if (str_starts_with($cleanPhone, '0')) {
                                                                $cleanPhone = '62'.substr($cleanPhone, 1);
                                                            }
                                                        @endphp
                                                        <a href="https://wa.me/{{ $cleanPhone }}" 
                                                           target="_blank" 
                                                           class="inline-flex items-center gap-1 text-[11px] font-mono text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 hover:bg-emerald-100">
                                                            <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                            {{ $staff->no_hp }}
                                                        </a>
                                                    @else
                                                        <span class="text-[10px] text-slate-400 italic">tanpa WA</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @if($opd->users->count() > 2)
                                                <span class="text-[10px] text-blue-700 font-bold block">+{{ $opd->users->count() - 2 }} staf lainnya</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[11px] text-amber-700 font-medium italic">Belum ada akun staf</span>
                                    @endif
                                </td>

                                <!-- Stats -->
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center gap-2 text-xs font-semibold">
                                        <span class="text-slate-700" title="Total Tugas">{{ $opd->total_tugas }} tugas</span>
                                        <span class="text-blue-700 font-bold" title="Selesai">({{ $opd->total_selesai }} selesai)</span>
                                        @if($opd->total_terlambat > 0)
                                            <span class="text-rose-600 font-bold" title="Terlambat">• {{ $opd->total_terlambat }} telat</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 text-center">
                                    @if($opd->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-900 border border-blue-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.opd.edit', $opd) }}" 
                                           class="p-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition" 
                                           title="Edit Data OPD">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        <button type="button" 
                                                @click="deleteActionUrl = '{{ route('admin.opd.destroy', $opd) }}'; opdName = '{{ addslashes($opd->nama_opd) }}'; if ($refs.deleteModalForm) { $refs.deleteModalForm.action = '{{ route('admin.opd.destroy', $opd) }}'; } showDeleteModal = true;"
                                                class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition" 
                                                title="Hapus Data OPD">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    Tidak ada data OPD yang sesuai pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($opds->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $opds->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Konfirmasi Hapus OPD -->
        <div x-show="showDeleteModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative text-left"
                 @click.away="showDeleteModal = false">
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="w-11 h-11 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Konfirmasi Hapus OPD</h3>
                        <p class="text-xs text-slate-500">Data OPD dan riwayat terkait akan dihapus permanen</p>
                    </div>
                </div>

                <p class="text-sm text-slate-700 leading-relaxed mb-6">
                    Apakah Anda yakin ingin menghapus data instansi OPD berikut: <br>
                    <strong class="text-slate-900 font-bold block mt-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200" x-text="opdName"></strong>
                </p>

                <form x-ref="deleteModalForm" method="POST" :action="deleteActionUrl" class="flex items-center justify-end gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" 
                            @click="showDeleteModal = false"
                            class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
