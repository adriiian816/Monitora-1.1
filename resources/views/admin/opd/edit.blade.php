<x-app-layout pageTitle="Edit Instansi OPD">
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
                    <span class="material-symbols-outlined text-[22px]">edit_note</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">Edit Data Instansi: {{ $opd->nama_opd }}</h1>
                    <p class="text-[13px] text-slate-500 mt-0.5">Perbarui rincian identitas instansi OPD Pemerintah Provinsi Maluku Utara</p>
                </div>
            </div>
        </div>

        {{-- ===== FORM CARD ===== --}}
        <div class="bg-white rounded-3xl border border-emerald-100/80 shadow-xs p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.opd.update', $opd) }}" class="space-y-6">
                @csrf
                @method('PUT')

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
                                   value="{{ old('kode_opd', $opd->kode_opd) }}" 
                                   required 
                                   class="w-full text-sm font-mono uppercase rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all @error('kode_opd') border-rose-500 @enderror">
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
                                   value="{{ old('nama_opd', $opd->nama_opd) }}" 
                                   required 
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
                                      class="w-full text-sm rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all leading-relaxed">{{ old('alamat', $opd->alamat) }}</textarea>
                            @error('alamat')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2 flex items-center pt-2">
                            <label class="inline-flex items-center cursor-pointer gap-2.5">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $opd->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-[#059669] focus:ring-[#059669] w-4 h-4">
                                <span class="text-xs font-bold text-slate-700">Status OPD Aktif &amp; Operasional</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Section: Daftar Staf Terdaftar di OPD ini -->
                <div class="pt-6 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-3.5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#064e3b] flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#059669]"></span>
                            Staf Terdaftar pada Instansi Ini ({{ $opd->users->count() }} orang)
                        </h3>
                        <a href="{{ route('admin.users.create') }}" class="text-xs font-bold text-[#059669] hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">person_add</span>
                            <span>+ Tambah Staf Baru</span>
                        </a>
                    </div>

                    @if($opd->users->count() > 0)
                        <div class="divide-y divide-slate-100 rounded-2xl border border-emerald-100/80 overflow-hidden bg-[#fafcfb]">
                            @foreach($opd->users as $staff)
                                <div class="p-4 flex items-center justify-between text-xs hover:bg-emerald-50/20 transition-colors">
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $staff->name }}</span>
                                        <span class="text-slate-400 font-mono text-[11px]">{{ $staff->email }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if($staff->no_hp)
                                            <span class="font-mono text-[#059669] font-semibold bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200 flex items-center gap-1 text-[11px]">
                                                <span class="material-symbols-outlined text-[12px]">chat</span>
                                                {{ $staff->no_hp }}
                                            </span>
                                        @else
                                            <span class="text-amber-600 italic text-[11px]">Belum ada no. WA</span>
                                        @endif
                                        <a href="{{ route('admin.users.edit', $staff) }}" class="text-[#059669] hover:underline font-bold">
                                            Edit Akun
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-5 rounded-2xl bg-[#f7faf8] border border-emerald-100/70 text-center text-xs text-slate-500">
                            Belum ada akun staf OPD yang terhubung dengan instansi ini. Buat akun staf di menu <a href="{{ route('admin.users.create') }}" class="text-[#059669] font-bold underline">Kelola Pengguna</a>.
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.opd.index') }}" class="px-5 py-2.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white font-bold text-xs shadow-md shadow-emerald-900/10 transition-all hover:scale-[1.01] active:scale-[0.99]">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>Perbarui Data OPD</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
</x-app-layout>
