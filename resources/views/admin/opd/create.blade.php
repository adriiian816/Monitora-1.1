<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.opd.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Data OPD
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tambah Instansi OPD Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Daftarkan instansi Organisasi Perangkat Daerah (OPD) Pemerintah Provinsi Maluku Utara</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.opd.store') }}" class="space-y-6">
                @csrf

                <!-- Section: Informasi Instansi -->
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        Informasi Instansi OPD
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="kode_opd" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Kode OPD <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   id="kode_opd" 
                                   name="kode_opd" 
                                   value="{{ old('kode_opd') }}" 
                                   required 
                                   placeholder="Contoh: DISPERINDAG" 
                                   class="w-full text-sm font-mono uppercase rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 @error('kode_opd') border-rose-500 @enderror">
                            <span class="text-[11px] text-slate-400 mt-1 block">Kode singkat unik instansi (misal: BPKAD, DKP, DISPAR)</span>
                            @error('kode_opd')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nama_opd" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Nama Lengkap Instansi OPD <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nama_opd" 
                                   name="nama_opd" 
                                   value="{{ old('nama_opd') }}" 
                                   required 
                                   placeholder="Contoh: Dinas Perindustrian dan Perdagangan" 
                                   class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 @error('nama_opd') border-rose-500 @enderror">
                            @error('nama_opd')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="alamat" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Alamat Kantor
                            </label>
                            <textarea id="alamat" 
                                      name="alamat" 
                                      rows="3" 
                                      placeholder="Contoh: Jl. Trans Halmahera, Gosale, Sofifi" 
                                      class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2 flex items-center pt-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="ms-2 text-sm font-semibold text-slate-800">Status OPD Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Info Box: Akun Staf OPD & Kontak WhatsApp -->
                <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-200 text-xs text-blue-900 flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <span class="font-bold block mb-0.5">Pengelolaan Akun & Nomor WhatsApp Staf:</span>
                        Setelah mendaftarkan instansi OPD, Anda dapat membuat akun pengguna staf beserta nomor WhatsApp penerima notifikasi melalui menu <a href="{{ route('admin.users.create') }}" class="font-bold underline text-blue-700 hover:text-blue-900">Kelola Pengguna &raquo; Tambah Pengguna Baru</a>.
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.opd.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm shadow-sm transition">
                        Simpan Data OPD
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
