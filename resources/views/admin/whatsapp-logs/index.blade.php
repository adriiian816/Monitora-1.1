<x-app-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Log & Audit Notifikasi WhatsApp</h1>
            <p class="text-xs text-slate-500 mt-1">Audit seluruh rekaman pesan tugas baru, pengingat deadline otomatis (H-7, H-3, H-1, H-0), peringatan keterlambatan, dan hasil verifikasi</p>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Berhasil Terkirim</span>
                <span class="text-2xl font-black text-blue-800 mt-1 block">{{ $totalTerkirim }}</span>
            </div>
            <span class="p-3 rounded-xl bg-blue-50 text-blue-700 font-bold text-lg">✓</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Mode Simulasi (Lokal)</span>
                <span class="text-2xl font-black text-sky-700 mt-1 block">{{ $totalSimulasi }}</span>
            </div>
            <span class="p-3 rounded-xl bg-sky-50 text-sky-700 font-bold text-lg">🧪</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Pesan Gagal</span>
                <span class="text-2xl font-black text-rose-600 mt-1 block">{{ $totalGagal }}</span>
            </div>
            <span class="p-3 rounded-xl bg-rose-50 text-rose-600 font-bold text-lg">⚠️</span>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.whatsapp-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nomor tujuan, OPD, isi pesan..." 
                       class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-100 focus:border-blue-600">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div>
                <select name="tipe" class="w-full py-2 px-3 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-100 focus:border-blue-600">
                    <option value="">Semua Tipe Pengingat</option>
                    <option value="pembuatan_tugas" {{ request('tipe') == 'pembuatan_tugas' ? 'selected' : '' }}>Tugas Baru</option>
                    <option value="h-14" {{ request('tipe') == 'h-14' ? 'selected' : '' }}>Pengingat H-14</option>
                    <option value="h-7" {{ request('tipe') == 'h-7' ? 'selected' : '' }}>Pengingat H-7</option>
                    <option value="h-3" {{ request('tipe') == 'h-3' ? 'selected' : '' }}>Pengingat H-3</option>
                    <option value="h-2" {{ request('tipe') == 'h-2' ? 'selected' : '' }}>Pengingat H-2</option>
                    <option value="h-1" {{ request('tipe') == 'h-1' ? 'selected' : '' }}>Pengingat H-1</option>
                    <option value="h-0" {{ request('tipe') == 'h-0' ? 'selected' : '' }}>Hari-H (H-0 Pagi)</option>
                    <option value="h-0-sore" {{ request('tipe') == 'h-0-sore' ? 'selected' : '' }}>Hari-H (H-0 Sore/Mendesak)</option>
                    <option value="keterlambatan" {{ request('tipe') == 'keterlambatan' ? 'selected' : '' }}>Keterlambatan Awal</option>
                    <option value="eskalasi_1" {{ request('tipe') == 'eskalasi_1' ? 'selected' : '' }}>Eskalasi (+3 Hari)</option>
                    <option value="eskalasi_2" {{ request('tipe') == 'eskalasi_2' ? 'selected' : '' }}>Kritis (+7 Hari)</option>
                    <option value="manual_broadcast" {{ request('tipe') == 'manual_broadcast' ? 'selected' : '' }}>Broadcast Manual</option>
                    <option value="verifikasi" {{ request('tipe') == 'verifikasi' ? 'selected' : '' }}>Hasil Verifikasi</option>
                    <option value="pengumpulan_tugas" {{ request('tipe') == 'pengumpulan_tugas' ? 'selected' : '' }}>Pengumpulan Tugas (Ke PIC)</option>
                    <option value="konfirmasi_pengumpulan" {{ request('tipe') == 'konfirmasi_pengumpulan' ? 'selected' : '' }}>Konfirmasi Pengumpulan (Ke OPD)</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-blue-900 text-white font-semibold text-sm hover:bg-blue-950 transition text-center">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'tipe', 'status']))
                    <a href="{{ route('admin.whatsapp-logs.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 text-slate-600 font-medium text-sm hover:bg-slate-200 transition text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-700">
                    <tr>
                        <th scope="col" class="px-6 py-4">Waktu & Tipe</th>
                        <th scope="col" class="px-6 py-4">OPD & No. WhatsApp</th>
                        <th scope="col" class="px-6 py-4">Isi Pesan WhatsApp</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-slate-900 block text-xs">
                                    {{ $log->sent_at->translatedFormat('d M Y') }}
                                </span>
                                <span class="text-[11px] text-slate-400 block">
                                    {{ $log->sent_at->format('H:i:s') }} WIT
                                </span>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $log->tipe_label }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-xs">
                                    {{ $log->opd ? $log->opd->nama_opd : ($log->tugas ? $log->tugas->judul : 'Umum') }}
                                </div>
                                <a href="https://wa.me/{{ $log->no_tujuan }}" target="_blank" class="inline-flex items-center gap-1 font-mono text-xs text-blue-700 hover:underline mt-0.5">
                                    {{ $log->no_tujuan }}
                                </a>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-xs text-slate-700 font-mono bg-slate-50 p-2.5 rounded-lg border border-slate-100 whitespace-pre-line max-w-md max-h-24 overflow-y-auto leading-relaxed">
                                    {{ $log->isi_pesan }}
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($log->status === 'terkirim')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-900 border border-blue-200">
                                        Terkirim
                                    </span>
                                @elseif($log->status === 'simulasi')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-100 text-sky-900 border border-sky-200">
                                        Simulasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200"
                                          @if(!empty($log->response_payload['message']) || !empty($log->response_payload['error']))
                                              title="{{ $log->response_payload['message'] ?? $log->response_payload['error'] }}"
                                          @endif>
                                        Gagal
                                    </span>
                                    @if(!empty($log->response_payload['message']))
                                        <span class="block text-[10px] text-rose-600 mt-1 max-w-[140px] mx-auto truncate" title="{{ $log->response_payload['message'] }}">
                                            {{ $log->response_payload['message'] }}
                                        </span>
                                    @endif
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.whatsapp-logs.resend', $log) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition"
                                            title="Kirim ulang pesan ini">
                                        Kirim Ulang
                                    </button>
                                </form>
                                @if(auth()->user()->isAdminOrSuperAdmin())
                                <div x-data="{ showConfirm: false }" class="inline ml-2">
                                    <form method="POST" action="{{ route('admin.whatsapp-logs.destroy', $log) }}" class="inline" @submit.prevent="showConfirm = true" x-ref="deleteForm">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-2 py-1.5 rounded-lg bg-slate-100 hover:bg-rose-100 text-slate-600 hover:text-rose-600 text-xs transition"
                                                title="Hapus log">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>

                                    <template x-teleport="body">
                                        <div x-show="showConfirm" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-transition.opacity>
                                            <div @click.outside="showConfirm = false" class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl whitespace-normal text-left" x-transition.scale.origin.bottom>
                                                <div class="flex items-center gap-3 mb-4">
                                                    <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    </div>
                                                    <h3 class="text-lg font-bold text-slate-900">Hapus Log Notifikasi?</h3>
                                                </div>
                                                <p class="text-sm text-slate-600 mb-6">Apakah Anda yakin ingin menghapus catatan pengiriman pesan ini? Data yang terhapus tidak dapat dikembalikan.</p>
                                                <div class="flex gap-2 justify-end">
                                                    <button @click="showConfirm = false" type="button" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">Batal</button>
                                                    <button @click="$refs.deleteForm.submit()" type="button" class="px-4 py-2 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-sm">Ya, Hapus</button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Belum ada log pesan WhatsApp yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
