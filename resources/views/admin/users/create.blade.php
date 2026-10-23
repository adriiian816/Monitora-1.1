<x-app-layout pageTitle="Tambah Pengguna">
<main class="flex-1 p-6 md:p-8 overflow-y-auto bg-[#fafcfb]">
    <div class="max-w-4xl mx-auto flex flex-col gap-6" x-data="{ role: '{{ old('role', 'admin') }}' }">

        {{-- ===== PAGE HEADER ===== --}}
        <div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#059669] transition mb-2">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali ke Manajemen Pengguna</span>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white text-[#059669] flex items-center justify-center border border-emerald-200 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">person_add</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">Tambah Pengguna Baru</h1>
                    <p class="text-[13px] text-slate-500 mt-0.5">Daftarkan akun pengelola sistem (Admin / PIC) atau akun perwakilan Staf OPD</p>
                </div>
            </div>
        </div>

        {{-- ===== FORM CARD ===== --}}
        <div class="bg-white rounded-3xl border border-emerald-100/80 shadow-xs p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                @csrf

                <!-- Basic Information -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Lengkap Pengguna <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required 
                               placeholder="Contoh: Budi Santoso, S.STP" 
                               class="w-full text-sm font-semibold rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all @error('name') border-rose-500 @enderror">
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Email Login <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               placeholder="email@malutprov.go.id" 
                               class="w-full text-sm rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all @error('email') border-rose-500 @enderror">
                        @error('email')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_hp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nomor WhatsApp / HP Aktif
                        </label>
                        <input type="text" 
                               id="no_hp" 
                               name="no_hp" 
                               value="{{ old('no_hp') }}" 
                               placeholder="Contoh: 081234567890" 
                               class="w-full text-sm font-mono rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all @error('no_hp') border-rose-500 @enderror">
                        <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk menerima notifikasi penugasan dan pengingat via WhatsApp.</p>
                        @error('no_hp')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bidang" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Bidang Kerja (Internal Bappeda)
                        </label>
                        <select id="bidang" 
                                name="bidang" 
                                class="w-full text-sm rounded-2xl border border-transparent bg-[#f0f5f2] focus:bg-white focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 font-medium transition-all @error('bidang') border-rose-500 @enderror">
                            <option value="">-- Bukan Anggota Bidang / Umum --</option>
                            <option value="ekop" {{ old('bidang') === 'ekop' ? 'selected' : '' }}>
                                Bidang Perekonomian &amp; Perdagangan (Ekop)
                            </option>
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Pilih jika staf/pengguna merupakan anggota internal Bidang Ekop.</p>
                        @error('bidang')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password & Confirmation -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-4 border-t border-slate-100">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Sandi (Password) <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               placeholder="Minimal 8 karakter" 
                               class="w-full text-sm rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all @error('password') border-rose-500 @enderror">
                        @error('password')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required 
                               placeholder="Ulangi kata sandi" 
                               class="w-full text-sm rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                    </div>
                </div>

                <!-- Role Selection -->
                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">
                        Tentukan Peran Akun (Role Hierarki) <span class="text-rose-500">*</span>
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Super Admin -->
                        <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition shadow-xs"
                               :class="role === 'super_admin' ? 'border-purple-500 bg-purple-50/40' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                                    Super Admin
                                </span>
                                <input type="radio" name="role" value="super_admin" x-model="role" class="text-purple-600 focus:ring-purple-500">
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Kontrol penuh: kelola master OPD, kelola seluruh akun pengguna, serta atur sistem.
                            </p>
                        </label>

                        <!-- PIC -->
                        <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition shadow-xs"
                               :class="role === 'admin' ? 'border-teal-500 bg-teal-50/40' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-teal-600"></span>
                                    PIC Bappeda
                                </span>
                                <input type="radio" name="role" value="admin" x-model="role" class="text-teal-600 focus:ring-teal-500">
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Operasional tugas: membuat tugas, memantau progres, dan validasi/verifikasi berkas OPD.
                            </p>
                        </label>

                        <!-- Anggota Bidang -->
                        <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition shadow-xs"
                               :class="role === 'anggota_bidang' ? 'border-indigo-500 bg-indigo-50/40' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                                    Anggota Bidang
                                </span>
                                <input type="radio" name="role" value="anggota_bidang" x-model="role" class="text-indigo-600 focus:ring-indigo-500">
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Internal Bappeda: pelaksana penugasan kegiatan bidang, mengunggah laporan hasil &amp; koordinasi internal.
                            </p>
                        </label>

                        <!-- Staf OPD -->
                        <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition shadow-xs"
                               :class="role === 'opd' ? 'border-[#059669] bg-emerald-50/40' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#059669]"></span>
                                    Staf OPD
                                </span>
                                <input type="radio" name="role" value="opd" x-model="role" class="text-[#059669] focus:ring-[#059669]">
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Pelaksana: melihat tugas yang diberikan ke OPD-nya, mengunggah laporan, &amp; profil pribadi.
                            </p>
                        </label>
                    </div>
                    @error('role')
                        <p class="text-xs text-rose-600 mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PIC Handled OPDs Selection (Visible only when role === 'admin') -->
                <div x-show="role === 'admin'" 
                     x-transition 
                     x-data="{ 
                         searchPicOpd: '',
                         selectAll(checked) {
                             document.querySelectorAll('.pic-opd-checkbox').forEach(el => el.checked = checked);
                         }
                     }"
                     class="p-5 rounded-3xl bg-teal-50/40 border border-teal-200 space-y-4 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2 text-teal-950 font-bold text-sm">
                                <span class="material-symbols-outlined text-[20px] text-teal-700">domain</span>
                                <span>Tentukan Instansi OPD yang Dikelola oleh Akun PIC Ini</span>
                            </div>
                            <p class="text-xs text-teal-800 mt-0.5">
                                Akun PIC hanya akan memiliki akses melihat, membuat tugas, dan memverifikasi berkas untuk OPD yang dipilih.
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" @click="selectAll(true)" class="px-3 py-1 text-xs font-semibold rounded-full bg-teal-100 hover:bg-teal-200 text-teal-900 transition">
                                Pilih Semua
                            </button>
                            <button type="button" @click="selectAll(false)" class="px-3 py-1 text-xs font-semibold rounded-full bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 transition">
                                Batalkan Semua
                            </button>
                        </div>
                    </div>

                    <!-- Filter Search -->
                    <div class="relative flex items-center bg-white px-3.5 py-1.5 rounded-full border border-teal-200">
                        <span class="material-symbols-outlined text-slate-400 text-[18px] mr-2">search</span>
                        <input type="text" 
                               x-model="searchPicOpd"
                               placeholder="Cari kode atau nama OPD..." 
                               class="w-full bg-transparent p-0 text-xs focus:outline-none focus:ring-0">
                    </div>

                    <!-- OPD Checkboxes Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-60 overflow-y-auto p-3 rounded-2xl border border-teal-200/80 bg-white">
                        @foreach($opds as $opd)
                            @php
                                $opdSearchText = strtolower($opd->kode_opd . ' ' . $opd->nama_opd);
                                $isChecked = in_array($opd->id, old('handled_opd_ids', []));
                            @endphp
                            <label x-show="!searchPicOpd || '{{ addslashes($opdSearchText) }}'.includes(searchPicOpd.toLowerCase())"
                                   class="flex items-start gap-2.5 p-2 rounded-xl border border-slate-200 hover:border-teal-400 hover:bg-teal-50/30 transition cursor-pointer">
                                <input type="checkbox" 
                                       name="handled_opd_ids[]" 
                                       value="{{ $opd->id }}" 
                                       {{ $isChecked ? 'checked' : '' }}
                                       class="pic-opd-checkbox mt-0.5 text-teal-600 rounded border-slate-300 focus:ring-teal-500">
                                <div class="text-xs leading-tight">
                                    <span class="font-bold text-teal-900 font-mono text-[11px] block">[{{ $opd->kode_opd }}]</span>
                                    <span class="text-slate-700 font-medium line-clamp-1" title="{{ $opd->nama_opd }}">{{ $opd->nama_opd }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('handled_opd_ids')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- OPD Selection (Visible only when role === 'opd') -->
                <div x-show="role === 'opd'" 
                     x-transition 
                     class="p-5 rounded-3xl bg-emerald-50/50 border border-emerald-200 space-y-4 shadow-xs">
                    <div class="flex items-center gap-2 text-emerald-950 font-bold text-sm">
                        <span class="material-symbols-outlined text-[20px] text-[#059669]">domain</span>
                        <span>Hubungkan ke Instansi Organisasi Perangkat Daerah (OPD) <span class="text-rose-500">*</span></span>
                    </div>
                    <p class="text-xs text-emerald-800">
                        Pilih instansi OPD tempat staf ini bertugas. Akun ini hanya dapat melihat dan mengumpulkan tugas yang ditugaskan ke OPD bersangkutan.
                    </p>
                    <div>
                        <select name="opd_id" 
                                id="opd_id" 
                                class="w-full text-sm rounded-2xl border border-emerald-200 bg-white px-4 py-3 focus:border-[#059669] focus:ring-2 focus:ring-[#059669]/20 font-medium @error('opd_id') border-rose-500 @enderror">
                            <option value="">-- Pilih Instansi OPD --</option>
                            @foreach($opds as $opd)
                                <option value="{{ $opd->id }}" {{ old('opd_id') == $opd->id ? 'selected' : '' }}>
                                    [{{ $opd->kode_opd }}] {{ $opd->nama_opd }}
                                </option>
                            @endforeach
                        </select>
                        @error('opd_id')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.users.index') }}" 
                       class="px-5 py-2.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white font-bold text-xs shadow-md shadow-emerald-900/10 transition-all hover:scale-[1.01] active:scale-[0.99]">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>Simpan Akun Pengguna</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
</x-app-layout>
