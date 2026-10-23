<x-app-layout pageTitle="Buat Penugasan Bidang">
<main class="flex-1 p-6 md:p-8 overflow-y-auto bg-[#fafcfb]">
    <div class="max-w-4xl mx-auto flex flex-col gap-6" x-data="{ searchAnggota: '', selectAll(checked) { document.querySelectorAll('.anggota-checkbox').forEach(el => el.checked = checked); } }">
        
        {{-- ===== PAGE HEADER ===== --}}
        <div>
            <a href="{{ route('admin.penugasan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#059669] transition mb-2">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali ke Daftar Penugasan</span>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white text-[#059669] flex items-center justify-center border border-emerald-200 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">assignment_add</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">Buat Penugasan Anggota Bidang</h1>
                    <p class="text-[13px] text-slate-500 mt-0.5">Berikan penugasan kegiatan internal kepada staf/anggota Bidang Perekonomian &amp; Perdagangan</p>
                </div>
            </div>
        </div>

        {{-- ===== VALIDATION ERRORS ===== --}}
        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <span class="material-symbols-outlined text-rose-600 text-[18px]">error</span>
                    <span>Terdapat kesalahan pada isian formulir:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 ml-6">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ===== FORM CARD ===== --}}
        <div class="bg-white rounded-3xl border border-emerald-100/80 shadow-xs p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.penugasan.store') }}" class="space-y-6">
                @csrf

                <!-- Judul Penugasan -->
                <div>
                    <label for="judul" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Judul Tugas / Kegiatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="judul" 
                           name="judul" 
                           value="{{ old('judul') }}" 
                           required 
                           placeholder="Contoh: Rapat Koordinasi Pengendalian Inflasi Daerah di Ruang Rapat Bappeda" 
                           class="w-full text-sm font-semibold rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all @error('judul') border-rose-500 @enderror">
                    @error('judul')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Deskripsi &amp; Rincian Tugas Kegiatan
                    </label>
                    <textarea id="deskripsi" 
                              name="deskripsi" 
                              rows="4" 
                              placeholder="Jelaskan detail kegiatan, lokasi/tempat pelaksanaan, agenda pembahasan, dokumen yang perlu disiapkan anggota..." 
                              class="w-full text-sm rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all leading-relaxed">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Kegiatan & Batas Laporan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-4 border-t border-slate-100">
                    <div>
                        <label for="tanggal_kegiatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tanggal Kegiatan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" 
                               id="tanggal_kegiatan" 
                               name="tanggal_kegiatan" 
                               value="{{ old('tanggal_kegiatan', date('Y-m-d')) }}" 
                               required 
                               class="w-full text-sm rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all @error('tanggal_kegiatan') border-rose-500 @enderror">
                        <p class="text-[11px] text-slate-400 mt-1">Tanggal pelaksanaan kegiatan internal atau dinas luar.</p>
                        @error('tanggal_kegiatan')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="batas_laporan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Batas Akhir Pelaporan Hasil
                        </label>
                        <input type="date" 
                               id="batas_laporan" 
                               name="batas_laporan" 
                               value="{{ old('batas_laporan') }}" 
                               class="w-full text-sm rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all @error('batas_laporan') border-rose-500 @enderror">
                        <p class="text-[11px] text-slate-400 mt-1">Batas waktu anggota menyampaikan laporan hasil (opsional).</p>
                        @error('batas_laporan')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Catatan Tambahan -->
                <div>
                    <label for="catatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Catatan Tambahan / Arahan
                    </label>
                    <input type="text" 
                           id="catatan" 
                           name="catatan" 
                           value="{{ old('catatan') }}" 
                           placeholder="Contoh: Berpakaian dinas rapi dan membawa notulen kegiatan" 
                           class="w-full text-sm rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                </div>

                <!-- Penerima Penugasan (Anggota Bidang Ekop) -->
                <div class="pt-4 border-t border-slate-100 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Penerima Penugasan (Staf / Anggota Bidang) <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Pilih satu atau lebih staf yang ditugaskan untuk kegiatan ini.
                            </p>
                        </div>
                        @if($anggotaList->isNotEmpty())
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="selectAll(true)" class="px-3 py-1.5 text-xs font-semibold rounded-full bg-emerald-50 hover:bg-emerald-100 text-[#064e3b] transition border border-emerald-200">
                                    Pilih Semua
                                </button>
                                <button type="button" @click="selectAll(false)" class="px-3 py-1.5 text-xs font-semibold rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                                    Batalkan Semua
                                </button>
                            </div>
                        @endif
                    </div>

                    @if($anggotaList->isEmpty())
                        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs shadow-xs">
                            <div class="font-bold flex items-center gap-2 mb-1">
                                <span class="material-symbols-outlined text-amber-600 text-[18px]">warning</span>
                                <span>Belum ada staf/anggota internal yang terdaftar</span>
                            </div>
                            <p class="mb-2 leading-relaxed">Silakan daftarkan anggota bidang atau tandai akun internal sebagai Anggota Bidang Ekop melalui menu Kelola Pengguna.</p>
                            @if(Auth::user()->isSuperAdmin())
                                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-1 font-bold text-[#059669] hover:underline">
                                    <span>Tambah Akun Anggota di Kelola Pengguna</span>
                                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                </a>
                            @endif
                        </div>
                    @else
                        <!-- Search Box for Anggota -->
                        <div class="relative flex items-center bg-[#f0f5f2] px-3.5 py-1.5 rounded-full border border-emerald-100/60 focus-within:bg-white focus-within:ring-2 focus-within:ring-[#059669]/20 transition-all">
                            <span class="material-symbols-outlined text-slate-400 text-[18px] mr-2">search</span>
                            <input type="text" 
                                   x-model="searchAnggota" 
                                   placeholder="Ketik nama atau email staf untuk mencari..." 
                                   class="w-full bg-transparent p-0 text-slate-800 placeholder:text-slate-400 text-xs focus:outline-none focus:ring-0">
                        </div>

                        <!-- Grid Checkbox Anggota -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-72 overflow-y-auto p-3 rounded-2xl border border-emerald-100/70 bg-[#fafcfb]">
                            @foreach($anggotaList as $anggota)
                                @php
                                    $searchText = strtolower($anggota->name . ' ' . $anggota->email . ' ' . $anggota->no_hp);
                                    $isSelected = is_array(old('penerima_ids')) && in_array($anggota->id, old('penerima_ids'));
                                @endphp
                                <label x-show="!searchAnggota || '{{ addslashes($searchText) }}'.includes(searchAnggota.toLowerCase())"
                                       class="flex items-start gap-3 p-3 rounded-2xl border border-slate-200/80 bg-white hover:border-[#059669]/50 hover:bg-emerald-50/20 transition cursor-pointer shadow-xs">
                                    <input type="checkbox" 
                                           name="penerima_ids[]" 
                                           value="{{ $anggota->id }}" 
                                           {{ $isSelected ? 'checked' : '' }}
                                           class="anggota-checkbox mt-1 text-[#059669] rounded border-slate-300 focus:ring-[#059669]">
                                    <div class="text-xs leading-tight flex-1 min-w-0">
                                        <div class="font-bold text-slate-900 truncate">{{ $anggota->name }}</div>
                                        <div class="text-slate-400 text-[11px] truncate mt-0.5">{{ $anggota->email }}</div>
                                        <div class="mt-1 flex items-center gap-2 flex-wrap">
                                            @if($anggota->no_hp)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-[#059669] border border-emerald-200">
                                                    <span class="material-symbols-outlined text-[12px]">chat</span>
                                                    {{ $anggota->no_hp }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-500">
                                                    Belum ada No. WA
                                                </span>
                                            @endif
                                            @if($anggota->bidang === 'ekop')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                    Bidang Ekop
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                    @error('penerima_ids')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror

                    <!-- Info WhatsApp Notification -->
                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 text-emerald-950 text-xs flex items-start gap-3 shadow-xs">
                        <span class="material-symbols-outlined text-[#059669] text-[20px] shrink-0 mt-0.5">mark_chat_read</span>
                        <div>
                            <span class="font-bold block text-sm text-[#064e3b] mb-0.5">Notifikasi Otomatis WhatsApp Gateway:</span>
                            <span class="text-slate-600 leading-relaxed">Setiap anggota terpilih yang memiliki nomor WhatsApp aktif akan langsung menerima pesan rincian penugasan ini ke nomor WhatsApp masing-masing.</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button Row -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.penugasan.index') }}" 
                       class="px-5 py-2.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white font-bold text-xs shadow-md shadow-emerald-900/10 transition-all hover:scale-[1.01] active:scale-[0.99]">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        <span>Simpan &amp; Kirim Penugasan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
</x-app-layout>
