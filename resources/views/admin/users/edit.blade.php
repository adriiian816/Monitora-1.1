<x-app-layout>
    <div class="max-w-4xl mx-auto" x-data="{ role: '{{ old('role', $user->role) }}' }">
        <div class="mb-6">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Manajemen Pengguna
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Data Pengguna</h1>
            <p class="text-xs text-slate-500 mt-1">Perbarui informasi akun, peran akses, instansi OPD, atau reset kata sandi</p>
        </div>

        @if($user->id === Auth::id())
            <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <strong>Perhatian:</strong> Anda sedang menyunting akun Anda sendiri yang sedang aktif. Anda tidak dapat menurunkan peran Admin untuk mencegah penguncian akses sistem.
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Lengkap Pengguna <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}" 
                               required 
                               class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 @error('name') border-rose-500 @enderror">
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Email Login <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}" 
                               required 
                               class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 @error('email') border-rose-500 @enderror">
                        @error('email')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password Reset (Optional) -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-4">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Ubah Kata Sandi (Opsional)</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Biarkan kolom password kosong jika tidak ingin mengubah kata sandi akun ini.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Kata Sandi Baru
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Minimal 8 karakter" 
                                   class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 @error('password') border-rose-500 @enderror">
                            @error('password')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Konfirmasi Kata Sandi Baru
                            </label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Ulangi kata sandi baru" 
                                   class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                        </div>
                    </div>
                </div>

                <!-- Role Selection -->
                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-3">
                        Peran Akun (Role) <span class="text-rose-500">*</span>
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Admin -->
                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition"
                               :class="role === 'super_admin' ? 'border-purple-600 bg-purple-50/30' : 'border-slate-200 hover:border-slate-300'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                                    Admin
                                </span>
                                <input type="radio" name="role" value="super_admin" x-model="role" class="text-purple-600 focus:ring-purple-500">
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Kontrol penuh: kelola master OPD, akun pengguna, & sistem.
                            </p>
                        </label>

                        <!-- PIC -->
                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition"
                               :class="role === 'admin' ? 'border-blue-600 bg-blue-50/30' : 'border-slate-200 hover:border-slate-300'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                    PIC
                                </span>
                                <input type="radio" name="role" value="admin" x-model="role" class="text-blue-600 focus:ring-blue-500"
                                       @if($user->id === Auth::id() && $user->role === 'super_admin') disabled @endif>
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Operasional: penugasan dokumen & validasi berkas OPD.
                            </p>
                        </label>

                        <!-- Staf OPD -->
                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition"
                               :class="role === 'opd' ? 'border-emerald-600 bg-emerald-50/30' : 'border-slate-200 hover:border-slate-300'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                    Staf OPD
                                </span>
                                <input type="radio" name="role" value="opd" x-model="role" class="text-emerald-600 focus:ring-emerald-500"
                                       @if($user->id === Auth::id() && $user->role === 'super_admin') disabled @endif>
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Pelaksana: melihat tugas OPD-nya & mengunggah berkas.
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
                     class="p-5 rounded-xl bg-blue-50/40 border border-blue-200 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2 text-blue-900 font-bold text-sm">
                                <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                Tentukan Instansi OPD yang Dikelola oleh Akun PIC Ini
                            </div>
                            <p class="text-xs text-blue-800 mt-0.5">
                                Akun PIC hanya akan memiliki akses melihat, membuat tugas, dan memverifikasi berkas untuk OPD yang dipilih.
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" @click="selectAll(true)" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-800 transition">
                                Pilih Semua
                            </button>
                            <button type="button" @click="selectAll(false)" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 transition">
                                Batalkan Semua
                            </button>
                        </div>
                    </div>

                    <!-- Filter Search -->
                    <div>
                        <input type="text" 
                               x-model="searchPicOpd"
                               placeholder="Cari kode atau nama OPD..." 
                               class="w-full text-xs rounded-xl border border-blue-200 bg-white px-3 py-2 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                    </div>

                    <!-- OPD Checkboxes Grid -->
                    @php
                        $userHandledIds = old('handled_opd_ids', $user->handledOpds->pluck('id')->toArray());
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-60 overflow-y-auto p-2.5 rounded-xl border border-blue-200 bg-white">
                        @foreach($opds as $opd)
                            @php
                                $opdSearchText = strtolower($opd->kode_opd . ' ' . $opd->nama_opd);
                                $isChecked = in_array($opd->id, $userHandledIds);
                            @endphp
                            <label x-show="!searchPicOpd || '{{ addslashes($opdSearchText) }}'.includes(searchPicOpd.toLowerCase())"
                                   class="flex items-start gap-2.5 p-2 rounded-lg border border-slate-200 hover:border-blue-400 hover:bg-blue-50/30 transition cursor-pointer">
                                <input type="checkbox" 
                                       name="handled_opd_ids[]" 
                                       value="{{ $opd->id }}" 
                                       {{ $isChecked ? 'checked' : '' }}
                                       class="pic-opd-checkbox mt-0.5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <div class="text-xs leading-tight">
                                    <span class="font-bold text-blue-900 font-mono text-[11px] block">[{{ $opd->kode_opd }}]</span>
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
                     class="p-5 rounded-xl bg-emerald-50/40 border border-emerald-200 space-y-3">
                    <div class="flex items-center gap-2 text-emerald-900 font-bold text-sm">
                        <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Hubungkan ke Instansi Organisasi Perangkat Daerah (OPD) <span class="text-rose-500">*</span>
                    </div>
                    <div>
                        <select name="opd_id" 
                                id="opd_id" 
                                class="w-full text-sm rounded-xl border border-emerald-300 bg-white px-3.5 py-2.5 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 @error('opd_id') border-rose-500 @enderror">
                            <option value="">-- Pilih Instansi OPD --</option>
                            @foreach($opds as $opd)
                                <option value="{{ $opd->id }}" {{ old('opd_id', $user->opd_id) == $opd->id ? 'selected' : '' }}>
                                    [{{ $opd->kode_opd }}] {{ $opd->nama_opd }}
                                </option>
                            @endforeach
                        </select>
                        @error('opd_id')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor WhatsApp / HP Staf OPD -->
                    <div class="pt-2">
                        <label for="no_hp" class="block text-xs font-semibold text-emerald-950 uppercase tracking-wider mb-1">
                            Nomor WhatsApp / HP Staf OPD
                        </label>
                        <input type="text" 
                               id="no_hp" 
                               name="no_hp" 
                               value="{{ old('no_hp', $user->no_hp) }}" 
                               placeholder="Contoh: 081234567890" 
                               class="w-full text-sm rounded-xl border border-emerald-300 bg-white px-3.5 py-2.5 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 @error('no_hp') border-rose-500 @enderror">
                        <p class="text-[11px] text-emerald-700 mt-1">
                            Nomor ini digunakan untuk menerima notifikasi penugasan dan pengingat deadline berkas dari Bappeda via WhatsApp.
                        </p>
                        @error('no_hp')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.users.index') }}" 
                       class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm shadow-md transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Perbarui Data Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
