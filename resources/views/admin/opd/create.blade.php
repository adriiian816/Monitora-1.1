<x-app-layout pageTitle="Tambah Instansi OPD">
<main class="flex-1 p-6 md:p-8 overflow-y-auto bg-[#fafcfb]">
    <div class="max-w-4xl mx-auto flex flex-col gap-6">

        {{-- ===== PAGE HEADER ===== --}}
        <div>
            <a href="{{ route('admin.opd.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#059669] transition mb-2">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali ke Data OPD</span>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white text-[#059669] flex items-center justify-center border border-emerald-200 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">domain_add</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">Tambah Instansi OPD Baru</h1>
                    <p class="text-[13px] text-slate-500 mt-0.5">Daftarkan instansi Organisasi Perangkat Daerah Pemerintah Provinsi Maluku Utara</p>
                </div>
            </div>
        </div>

        {{-- ===== FORM CARD ===== --}}
        <div class="bg-white rounded-3xl border border-emerald-100/80 shadow-xs p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.opd.store') }}" class="space-y-6">
                @csrf

                <!-- Section: Informasi Instansi -->
                <div>
                    <h2 class="text-xs font-bold text-[#064e3b] uppercase tracking-wider mb-4 pb-2 border-b border-emerald-100/60 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#059669]"></span>
                        Informasi Identitas OPD
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="kode_opd" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Kode OPD <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   id="kode_opd" 
                                   name="kode_opd" 
                                   value="{{ old('kode_opd') }}" 
                                   required 
                                   placeholder="Contoh: DISPERINDAG" 
                                   class="w-full text-sm font-mono uppercase rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all @error('kode_opd') border-rose-500 @enderror">
                            <span class="text-[11px] text-slate-400 mt-1 block">Kode singkat unik instansi (misal: BPKAD, DKP, DISPAR)</span>
                            @error('kode_opd')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nama_opd" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nama Lengkap Instansi OPD <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nama_opd" 
                                   name="nama_opd" 
                                   value="{{ old('nama_opd') }}" 
                                   required 
                                   placeholder="Contoh: Dinas Perindustrian dan Perdagangan" 
                                   class="w-full text-sm font-semibold rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all @error('nama_opd') border-rose-500 @enderror">
                            @error('nama_opd')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="alamat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Kantor
                            </label>
                            <textarea id="alamat" 
                                      name="alamat" 
                                      rows="3" 
                                      placeholder="Contoh: Jl. Trans Halmahera, Gosale, Sofifi" 
                                      class="w-full text-sm rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all leading-relaxed">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2 flex items-center pt-2">
                            <label class="inline-flex items-center cursor-pointer gap-2.5">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300 text-[#059669] focus:ring-[#059669] w-4 h-4">
                                <span class="text-xs font-bold text-slate-700">Status OPD Aktif &amp; Operasional</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Info Box: Akun Staf OPD & Kontak WhatsApp -->
                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 text-emerald-950 text-xs flex items-start gap-3 shadow-xs">
                    <span class="material-symbols-outlined text-[#059669] text-[20px] shrink-0 mt-0.5">help</span>
                    <div>
                        <span class="font-bold block text-sm text-[#064e3b] mb-0.5">Pengelolaan Akun Staf &amp; WhatsApp:</span>
                        <span class="text-slate-600 leading-relaxed">Setelah mendaftarkan instansi OPD, Anda dapat membuat akun pengguna staf beserta nomor WhatsApp penerima notifikasi melalui menu <a href="{{ route('admin.users.create') }}" class="font-bold underline text-[#059669] hover:text-emerald-800">Kelola Pengguna &raquo; Tambah Pengguna Baru</a>.</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.opd.index') }}" class="px-5 py-2.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white font-bold text-xs shadow-md shadow-emerald-900/10 transition-all hover:scale-[1.01] active:scale-[0.99]">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>Simpan Data OPD</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
</x-app-layout>
