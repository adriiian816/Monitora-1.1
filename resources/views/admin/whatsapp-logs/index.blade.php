<x-app-layout pageTitle="Log Notifikasi WhatsApp">
<main class="flex-1 p-6 md:p-8 overflow-y-auto bg-[#fafcfb]">
    <div class="flex flex-col w-full max-w-7xl mx-auto gap-6">

        {{-- ===== PAGE HEADER ===== --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white text-[#059669] flex items-center justify-center border border-emerald-200 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">mark_chat_read</span>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">Log &amp; Audit Notifikasi WhatsApp</h1>
                        <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                            WhatsApp Gateway
                        </span>
                    </div>
                    <p class="text-[13px] text-slate-500 mt-0.5">
                        Audit seluruh rekaman pesan tugas baru, pengingat deadline otomatis (H-7, H-3, H-1, H-0), eskalasi, dan hasil verifikasi
                    </p>
                </div>
            </div>
        </div>

        {{-- ===== ALERT NOTIFICATIONS ===== --}}
        @if(session('success'))
            <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl px-5 py-3.5 text-xs font-semibold shadow-xs">
                <span class="material-symbols-outlined text-emerald-600 text-[20px] shrink-0">check_circle</span>
                <div class="mt-0.5">{{ session('success') }}</div>
            </div>
        @endif

        {{-- ===== SUMMARY KPI CARDS ===== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Total Berhasil Terkirim --}}
            <div class="bg-white p-5 rounded-3xl border border-emerald-100/80 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-[#059669] border border-emerald-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">done_all</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Berhasil Terkirim</div>
                    <div class="text-2xl font-black text-[#064e3b] leading-tight">{{ $totalTerkirim }}</div>
                    <div class="text-[10px] text-[#059669] font-semibold">Tersampaikan ke penerima</div>
                </div>
            </div>

            {{-- Card 2: Mode Simulasi --}}
            <div class="bg-white p-5 rounded-3xl border border-emerald-100/80 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-700 border border-teal-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">science</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Mode Simulasi (Lokal)</div>
                    <div class="text-2xl font-black text-teal-800 leading-tight">{{ $totalSimulasi }}</div>
                    <div class="text-[10px] text-teal-700 font-semibold">Uji coba log lokal</div>
                </div>
            </div>

            {{-- Card 3: Dibatasi (Rate Limit 30/jam) --}}
            <div class="bg-white p-5 rounded-3xl border border-amber-100/80 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">speed</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Dibatasi (Rate Limit)</div>
                    <div class="text-2xl font-black text-amber-800 leading-tight">{{ $totalDibatasi }}</div>
                    <div class="text-[10px] text-amber-700 font-semibold">Maks 30 pesan / jam / nomor</div>
                </div>
            </div>

            {{-- Card 4: Pesan Gagal --}}
            <div class="bg-white p-5 rounded-3xl border border-emerald-100/80 shadow-xs hover:shadow-md transition-all flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">error</span>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pesan Gagal</div>
                    <div class="text-2xl font-black text-rose-600 leading-tight">{{ $totalGagal }}</div>
                    <div class="text-[10px] text-rose-600 font-semibold">Memerlukan pengiriman ulang</div>
                </div>
            </div>
        </div>

        {{-- ===== FILTER & SEARCH BAR ===== --}}
        <form method="GET" action="{{ route('admin.whatsapp-logs.index') }}"
              class="bg-white p-2 shadow-xs border border-emerald-100/80 flex flex-col md:flex-row items-stretch gap-2.5 rounded-full">
            
            {{-- Search Input --}}
            <div class="relative flex-1 flex items-center bg-[#f0f5f2] px-3.5 py-2 focus-within:bg-white focus-within:ring-2 focus-within:ring-[#059669]/20 transition-all rounded-full">
                <span class="material-symbols-outlined text-slate-400 text-[20px] mr-2.5">search</span>
                <input class="w-full bg-transparent p-0 text-slate-800 placeholder:text-slate-400 text-[13px] focus:outline-none focus:ring-0"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nomor tujuan, OPD, isi pesan..."
                       type="text">
            </div>

            {{-- Status Select --}}
            <div class="relative min-w-[170px]">
                <select name="status" class="w-full h-full bg-[#f0f5f2] hover:bg-[#e6efe9] text-slate-700 text-[13px] font-medium py-2.5 pl-4 pr-10 appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#059669]/20 transition-colors rounded-full">
                    <option value="">Semua Status</option>
                    <option value="terkirim" {{ request('status') == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                    <option value="simulasi" {{ request('status') == 'simulasi' ? 'selected' : '' }}>Simulasi</option>
                    <option value="dibatasi" {{ request('status') == 'dibatasi' ? 'selected' : '' }}>Dibatasi (Rate Limit)</option>
                    <option value="gagal" {{ request('status') == 'gagal' ? 'selected' : '' }}>Gagal</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
            </div>

            {{-- Tipe Pengingat Select --}}
            <div class="relative min-w-[200px]">
                <select name="tipe" class="w-full h-full bg-[#f0f5f2] hover:bg-[#e6efe9] text-slate-700 text-[13px] font-medium py-2.5 pl-4 pr-10 appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#059669]/20 transition-colors rounded-full">
                    <option value="">Semua Tipe Pengingat</option>
                    <option value="pembuatan_tugas" {{ request('tipe') == 'pembuatan_tugas' ? 'selected' : '' }}>Tugas Baru</option>
                    <option value="penugasan_ekop" {{ request('tipe') == 'penugasan_ekop' ? 'selected' : '' }}>Penugasan Bidang</option>
                    <option value="konfirmasi_penugasan" {{ request('tipe') == 'konfirmasi_penugasan' ? 'selected' : '' }}>Respon Penugasan</option>
                    <option value="laporan_penugasan" {{ request('tipe') == 'laporan_penugasan' ? 'selected' : '' }}>Laporan Penugasan</option>
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
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-[#059669] hover:bg-[#047857] text-white text-[13px] font-semibold shadow-xs flex items-center justify-center gap-2 transition-colors rounded-full">
                    <span class="material-symbols-outlined text-[18px]">tune</span>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['search', 'tipe', 'status']))
                    <a href="{{ route('admin.whatsapp-logs.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-medium flex items-center gap-1.5 transition-colors rounded-full">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </form>

        {{-- Status Info Bar --}}
        <div class="flex items-center justify-between px-1">
            <div class="flex items-center gap-2 text-slate-500 text-[12px]">
                <span>Menampilkan <strong class="text-[#064e3b] font-bold text-[13px]">{{ $logs->total() }}</strong> log notifikasi</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="text-[#059669] font-bold">WhatsApp Gateway Engine Terhubung</span>
            </div>
            <div class="flex items-center gap-3 text-[12px] text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Terkirim</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-teal-500"></span> Simulasi</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Dibatasi</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Gagal</span>
            </div>
        </div>

        {{-- ===== TABEL LOG WHATSAPP ===== --}}
        <div class="bg-white rounded-3xl border border-emerald-100/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-[#f7faf8] border-b border-emerald-100/70 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <tr>
                            <th scope="col" class="px-6 py-4">Waktu &amp; Tipe</th>
                            <th scope="col" class="px-6 py-4">OPD &amp; No. WhatsApp</th>
                            <th scope="col" class="px-6 py-4">Isi Pesan WhatsApp</th>
                            <th scope="col" class="px-6 py-4 text-center">Status</th>
                            <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-[#f0f8f4] transition-colors group">
                                <!-- Waktu & Tipe -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold text-slate-900 block text-xs">
                                        {{ $log->sent_at->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">
                                        {{ $log->sent_at->format('H:i:s') }} WIT
                                    </span>
                                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#f0f5f2] text-[#064e3b] border border-emerald-200">
                                        {{ $log->tipe_label }}
                                    </span>
                                </td>

                                <!-- OPD & No WhatsApp -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 text-xs">
                                        {{ $log->opd ? $log->opd->nama_opd : ($log->tugas ? $log->tugas->judul : 'Umum') }}
                                    </div>
                                    <a href="https://wa.me/{{ $log->no_tujuan }}" target="_blank" class="inline-flex items-center gap-1 font-mono text-xs text-[#059669] hover:underline mt-0.5 font-semibold">
                                        <span class="material-symbols-outlined text-[12px]">chat</span>
                                        <span>{{ $log->no_tujuan }}</span>
                                    </a>
                                </td>

                                <!-- Isi Pesan WhatsApp -->
                                <td class="px-6 py-4">
                                    <div class="text-xs text-slate-700 font-mono bg-[#f7faf8] p-3 rounded-2xl border border-emerald-100/70 whitespace-pre-line max-w-md max-h-24 overflow-y-auto leading-relaxed">
                                        {{ $log->isi_pesan }}
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if($log->status === 'terkirim')
                                        <span class="inline-flex items-center gap-1 px-3 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-[#059669] border border-emerald-200">
                                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                            <span>Terkirim</span>
                                        </span>
                                    @elseif($log->status === 'simulasi')
                                        <span class="inline-flex items-center gap-1 px-3 py-0.5 rounded-full text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200">
                                            <span class="material-symbols-outlined text-[14px]">science</span>
                                            <span>Simulasi</span>
                                        </span>
                                    @elseif($log->status === 'dibatasi')
                                        <span class="inline-flex items-center gap-1 px-3 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200"
                                              title="{{ $log->response_payload['message'] ?? 'Dibatasi: Maksimal 30 pesan per jam tercapai' }}">
                                            <span class="material-symbols-outlined text-[14px]">speed</span>
                                            <span>Dibatasi</span>
                                        </span>
                                        <span class="block text-[10px] text-amber-700 mt-1 max-w-[140px] mx-auto truncate" title="Maks 30 pesan/jam">
                                            Maks 30 pesan/jam
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200"
                                              @if(!empty($log->response_payload['message']) || !empty($log->response_payload['error']))
                                                  title="{{ $log->response_payload['message'] ?? $log->response_payload['error'] }}"
                                              @endif>
                                            <span class="material-symbols-outlined text-[14px]">error</span>
                                            <span>Gagal</span>
                                        </span>
                                        @if(!empty($log->response_payload['message']))
                                            <span class="block text-[10px] text-rose-600 mt-1 max-w-[140px] mx-auto truncate" title="{{ $log->response_payload['message'] }}">
                                                {{ $log->response_payload['message'] }}
                                            </span>
                                        @endif
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form method="POST" action="{{ route('admin.whatsapp-logs.resend', $log) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-white hover:bg-emerald-50 text-[#064e3b] hover:text-[#059669] font-bold text-xs border border-emerald-200 shadow-xs transition cursor-pointer"
                                                    title="Kirim ulang pesan ini">
                                                <span class="material-symbols-outlined text-[14px]">send</span>
                                                <span>Kirim Ulang</span>
                                            </button>
                                        </form>

                                        @if(auth()->user()->isAdminOrSuperAdmin())
                                        <div x-data="{ showConfirm: false }" class="inline">
                                            <form method="POST" action="{{ route('admin.whatsapp-logs.destroy', $log) }}" class="inline" @submit.prevent="showConfirm = true" x-ref="deleteForm">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-full transition cursor-pointer"
                                                        title="Hapus log">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>

                                            <template x-teleport="body">
                                                <div x-show="showConfirm" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-transition.opacity>
                                                    <div @click.outside="showConfirm = false" class="bg-white rounded-3xl p-6 sm:p-7 max-w-sm w-full shadow-2xl border border-slate-200 whitespace-normal text-left" x-transition.scale.origin.bottom>
                                                        <div class="flex items-center gap-3 mb-4">
                                                            <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                                                <span class="material-symbols-outlined text-[24px]">warning</span>
                                                            </div>
                                                            <h3 class="text-lg font-bold text-slate-900">Hapus Log Notifikasi?</h3>
                                                        </div>
                                                        <p class="text-xs text-slate-600 mb-6 leading-relaxed">Apakah Anda yakin ingin menghapus catatan pengiriman pesan ini? Data yang terhapus tidak dapat dikembalikan.</p>
                                                        <div class="flex gap-2 justify-end pt-4 border-t border-slate-100">
                                                            <button @click="showConfirm = false" type="button" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-full transition cursor-pointer">Batal</button>
                                                            <button @click="$refs.deleteForm.submit()" type="button" class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-full transition shadow-sm cursor-pointer">Ya, Hapus</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 mx-auto rounded-2xl bg-[#f0f5f2] text-slate-400 flex items-center justify-center mb-3">
                                        <span class="material-symbols-outlined text-[26px]">chat_bubble</span>
                                    </div>
                                    <span class="font-bold text-slate-700 text-sm block">Belum ada log pesan WhatsApp yang tercatat.</span>
                                    <span class="text-xs text-slate-400 mt-1 block">Seluruh riwayat broadcast dan notifikasi otomatis WhatsApp akan tersimpan di sini.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</main>
</x-app-layout>
