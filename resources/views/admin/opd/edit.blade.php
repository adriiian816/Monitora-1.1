<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.opd.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Data OPD
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Data Instansi: {{ $opd->nama_opd }}</h1>
            <p class="text-xs text-slate-500 mt-1">Perbarui rincian identitas instansi OPD Pemerintah Provinsi Maluku Utara</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.opd.update', $opd) }}" class="space-y-6">
                @csrf
                @method('PUT')

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
                                   value="{{ old('kode_opd', $opd->kode_opd) }}" 
                                   required 
                                   class="w-full text-sm font-mono uppercase rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 @error('kode_opd') border-rose-500 @enderror">
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
                                   value="{{ old('nama_opd', $opd->nama_opd) }}" 
                                   required 
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
                                      class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20">{{ old('alamat', $opd->alamat) }}</textarea>
                            @error('alamat')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2 flex items-center pt-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $opd->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="ms-2 text-sm font-semibold text-slate-800">Status OPD Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Section: Daftar Staf Terdaftar di OPD ini -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Staf Terdaftar pada Instansi Ini ({{ $opd->users->count() }} orang)
                        </h3>
                        <a href="{{ route('admin.users.create') }}" class="text-xs font-bold text-blue-700 hover:underline">
                            + Tambah Staf Baru
                        </a>
                    </div>

                    @if($opd->users->count() > 0)
                        <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 overflow-hidden bg-slate-50/50">
                            @foreach($opd->users as $staff)
                                <div class="p-3 flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $staff->name }}</span>
                                        <span class="text-slate-500 font-mono text-[11px]">{{ $staff->email }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if($staff->no_hp)
                                            <span class="font-mono text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                WA: {{ $staff->no_hp }}
                                            </span>
                                        @else
                                            <span class="text-amber-600 italic text-[11px]">Belum ada no. WA</span>
                                        @endif
                                        <a href="{{ route('admin.users.edit', $staff) }}" class="text-blue-700 hover:underline font-semibold">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center text-xs text-slate-500">
                            Belum ada akun staf OPD yang terhubung dengan instansi ini. Buat akun staf di menu <a href="{{ route('admin.users.create') }}" class="text-blue-700 font-bold underline">Kelola Pengguna</a>.
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.opd.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm shadow-sm transition">
                        Perbarui Data OPD
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
