<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.tugas.show', $tugas) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Detail Tugas
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Data Tugas: {{ $tugas->judul }}</h1>
            <p class="text-xs text-slate-500 mt-1">Perbarui batas waktu deadline, petunjuk pengumpulan berkas, atau status penugasan</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.tugas.update', $tugas) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Judul & Tahun Anggaran -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div class="sm:col-span-3">
                        <label for="judul" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Judul Tugas / Nama Laporan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="judul" 
                               name="judul" 
                               value="{{ old('judul', $tugas->judul) }}" 
                               required 
                               class="w-full text-sm font-semibold rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                    </div>

                    <div>
                        <label for="tahun_anggaran" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Tahun Anggaran <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="tahun_anggaran" 
                               name="tahun_anggaran" 
                               value="{{ old('tahun_anggaran', $tugas->tahun_anggaran) }}" 
                               required 
                               class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                    </div>
                </div>

                <!-- Deskripsi & Petunjuk Teknis -->
                <div>
                    <label for="deskripsi" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Deskripsi & Petunjuk Teknis Pengumpulan
                    </label>
                    <textarea id="deskripsi" 
                              name="deskripsi" 
                              rows="4" 
                              class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 leading-relaxed">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                </div>

                <!-- Batas Waktu Deadline & Format Pengumpulan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                    <!-- Deadline -->
                    <div>
                        <label for="deadline" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Batas Waktu Deadline (WIT) <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" 
                               id="deadline" 
                               name="deadline" 
                               value="{{ old('deadline', $tugas->deadline->format('Y-m-d\TH:i')) }}" 
                               required 
                               class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                    </div>

                    <!-- Status Tugas -->
                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Status Penugasan <span class="text-rose-500">*</span>
                        </label>
                        <select id="status" name="status" class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                            <option value="aktif" {{ old('status', $tugas->status) === 'aktif' ? 'selected' : '' }}>Aktif (Sedang Berjalan)</option>
                            <option value="selesai" {{ old('status', $tugas->status) === 'selesai' ? 'selected' : '' }}>Selesai (Ditutup)</option>
                            <option value="dibatalkan" {{ old('status', $tugas->status) === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <!-- Format Pengumpulan -->
                    <div class="sm:col-span-2">
                        <span class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Format Pengumpulan Dokumen <span class="text-rose-500">*</span>
                        </span>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-blue-400 hover:bg-blue-50/40 transition cursor-pointer">
                                <input type="radio" name="format_pengumpulan" value="digital" {{ old('format_pengumpulan', $tugas->format_pengumpulan) === 'digital' ? 'checked' : '' }} class="mt-0.5 text-blue-700 focus:ring-blue-600">
                                <span class="text-xs font-bold text-slate-900">Digital (Upload File)</span>
                            </label>
                            <label class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-blue-400 hover:bg-blue-50/40 transition cursor-pointer">
                                <input type="radio" name="format_pengumpulan" value="fisik" {{ old('format_pengumpulan', $tugas->format_pengumpulan) === 'fisik' ? 'checked' : '' }} class="mt-0.5 text-blue-700 focus:ring-blue-600">
                                <span class="text-xs font-bold text-slate-900">Fisik (Penyerahan Langsung)</span>
                            </label>
                            <label class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-blue-400 hover:bg-blue-50/40 transition cursor-pointer">
                                <input type="radio" name="format_pengumpulan" value="hybrid" {{ old('format_pengumpulan', $tugas->format_pengumpulan) === 'hybrid' ? 'checked' : '' }} class="mt-0.5 text-blue-700 focus:ring-blue-600">
                                <span class="text-xs font-bold text-slate-900">Hybrid (Keduanya)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Target OPD -->
                <div class="pt-4 border-t border-slate-100">
                    <span class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-2">
                        Target Instansi OPD Ditugaskan <span class="text-rose-500">*</span>
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-56 overflow-y-auto p-3 rounded-xl border border-slate-200 bg-slate-50/50">
                        @foreach($opds as $opd)
                            <label class="flex items-start gap-2.5 p-2 rounded-lg bg-white border border-slate-200 hover:border-blue-300 transition cursor-pointer">
                                <input type="checkbox" 
                                       name="opd_ids[]" 
                                       value="{{ $opd->id }}" 
                                       {{ in_array($opd->id, old('opd_ids', $assignedOpdIds)) ? 'checked' : '' }}
                                       class="mt-0.5 text-blue-700 rounded border-slate-300 focus:ring-blue-500">
                                <div class="text-xs">
                                    <span class="font-bold text-blue-900 block font-mono text-[11px]">{{ $opd->kode_opd }}</span>
                                    <span class="text-slate-800 leading-tight block">{{ $opd->nama_opd }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.tugas.show', $tugas) }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm shadow-sm transition">
                        Perbarui Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
