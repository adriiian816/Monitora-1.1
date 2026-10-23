<x-app-layout pageTitle="Edit Penugasan Dokumen OPD">
<main class="flex-1 p-6 md:p-8 overflow-y-auto bg-[#fafcfb]">
    <div class="max-w-4xl mx-auto flex flex-col gap-6">

        {{-- ===== PAGE HEADER ===== --}}
        <div>
            <a href="{{ route('admin.tugas.show', $tugas) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#059669] transition mb-2">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali ke Detail Tugas</span>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white text-[#059669] flex items-center justify-center border border-emerald-200 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">edit_note</span>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">Edit Data Penugasan Dokumen OPD</h1>
                        <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                            TA {{ $tugas->tahun_anggaran }}
                        </span>
                    </div>
                    <p class="text-[13px] text-slate-500 mt-0.5">Perbarui batas waktu deadline, petunjuk pengumpulan berkas, atau status penugasan</p>
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
            <form method="POST" action="{{ route('admin.tugas.update', $tugas) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Judul & Tahun Anggaran -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
                    <div class="sm:col-span-3">
                        <label for="judul" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Judul Tugas / Nama Laporan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="judul" 
                               name="judul" 
                               value="{{ old('judul', $tugas->judul) }}" 
                               required 
                               placeholder="Contoh: Penyusunan Rencana Kerja (RENJA) OPD TA {{ date('Y') + 1 }}" 
                               class="w-full text-sm font-semibold rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all @error('judul') border-rose-500 @enderror">
                        @error('judul')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tahun_anggaran" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tahun Anggaran <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="tahun_anggaran" 
                               name="tahun_anggaran" 
                               value="{{ old('tahun_anggaran', $tugas->tahun_anggaran) }}" 
                               required 
                               placeholder="2026 / 2027" 
                               class="w-full text-sm font-semibold rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all text-center @error('tahun_anggaran') border-rose-500 @enderror">
                        @error('tahun_anggaran')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Deskripsi & Petunjuk Teknis -->
                <div>
                    <label for="deskripsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Deskripsi &amp; Petunjuk Teknis Pengumpulan
                    </label>
                    <textarea id="deskripsi" 
                              name="deskripsi" 
                              rows="4" 
                              placeholder="Tuliskan petunjuk rinci berkas yang wajib dilampirkan, ketentuan format laporan, kontak narahubung Bidang Perekonomian dan Perdagangan, dsb..." 
                              class="w-full text-sm rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all leading-relaxed @error('deskripsi') border-rose-500 @enderror">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Upload & Kelola Berkas Lampiran Tugas -->
                <div x-data="{
                    fileName: '',
                    fileSize: '',
                    removeOldFile: false,
                    handleFileSelect(e) {
                        const file = e.target.files[0];
                        if (file) {
                            this.fileName = file.name;
                            const sizeInKb = file.size / 1024;
                            if (sizeInKb > 1024) {
                                this.fileSize = (sizeInKb / 1024).toFixed(2) + ' MB';
                            } else {
                                this.fileSize = sizeInKb.toFixed(1) + ' KB';
                            }
                            this.removeOldFile = false;
                        } else {
                            this.fileName = '';
                            this.fileSize = '';
                        }
                    },
                    clearFile() {
                        this.fileName = '';
                        this.fileSize = '';
                        document.getElementById('file_tugas').value = '';
                    }
                }" class="p-5 rounded-2xl bg-[#f8fbf9] border border-emerald-100/90 space-y-3 shadow-2xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <label class="block text-xs font-bold text-[#064e3b] uppercase tracking-wider flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-[#059669]">attach_file</span>
                            <span>Dokumen Lampiran / Template Tugas</span>
                        </label>
                        <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full self-start sm:self-auto">
                            PDF, Excel, Word, PPT, ZIP/RAR (Maks. 25 MB)
                        </span>
                    </div>

                    @if($tugas->file_path)
                        <div class="p-3.5 rounded-2xl bg-white border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                            <div class="flex items-center gap-3 truncate">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-[#059669] flex items-center justify-center shrink-0 border border-emerald-200">
                                    <span class="material-symbols-outlined text-[20px]">description</span>
                                </div>
                                <div class="truncate text-xs">
                                    <span class="font-bold text-slate-800 block truncate">{{ $tugas->file_original_name ?: basename($tugas->file_path) }}</span>
                                    <span class="text-slate-400 text-[11px]">Ukuran berkas: {{ $tugas->formatted_file_size }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('admin.tugas.download', $tugas) }}" 
                                   class="inline-flex items-center gap-1.5 text-xs font-bold text-[#064e3b] hover:text-[#059669] bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-xl border border-emerald-200 transition">
                                    <span class="material-symbols-outlined text-[16px]">download</span>
                                    <span>Unduh Lampiran Saat Ini</span>
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <input type="checkbox" 
                                   id="hapus_file_tugas" 
                                   name="hapus_file_tugas" 
                                   value="1" 
                                   x-model="removeOldFile"
                                   class="rounded border-slate-300 text-rose-600 focus:ring-rose-500 text-xs">
                            <label for="hapus_file_tugas" class="text-xs font-semibold text-rose-700 cursor-pointer">
                                Hapus file lampiran tugas ini (tanpa mengganti file baru)
                            </label>
                        </div>
                    @endif

                    <div>
                        <label for="file_tugas" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            {{ $tugas->file_path ? 'Ganti dengan File Lampiran Baru (Opsional):' : 'Unggah File Lampiran / Template Baru:' }}
                        </label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <input type="file" 
                                   id="file_tugas" 
                                   name="file_tugas" 
                                   @change="handleFileSelect($event)"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-[#059669] file:text-white hover:file:bg-[#047857] cursor-pointer border border-emerald-200 rounded-2xl bg-white p-2 focus:ring-2 focus:ring-[#059669]/20 transition @error('file_tugas') border-rose-500 @enderror">
                            
                            <template x-if="fileName">
                                <button type="button" 
                                        @click="clearFile()" 
                                        class="inline-flex items-center gap-1 text-xs text-rose-600 hover:text-rose-700 font-bold px-3 py-2 rounded-xl bg-rose-50 border border-rose-200 shrink-0 transition">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                    <span>Batal Pilih</span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <template x-if="fileName">
                        <div class="p-3 rounded-xl bg-white border border-emerald-200 flex items-center justify-between text-xs text-slate-700">
                            <div class="flex items-center gap-2 truncate">
                                <span class="material-symbols-outlined text-[#059669] text-[18px]">check_circle</span>
                                <span class="font-bold text-[#064e3b] truncate" x-text="fileName"></span>
                                <span class="text-slate-400 font-medium" x-text="'(' + fileSize + ')'"></span>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full shrink-0">Berkas Baru Dipilih</span>
                        </div>
                    </template>

                    @error('file_tugas')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Batas Waktu Deadline, Status, & Format Pengumpulan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                    <!-- Deadline -->
                    <div>
                        <label for="deadline" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Batas Waktu Deadline (WITA / WIT) <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" 
                               id="deadline" 
                               name="deadline" 
                               value="{{ old('deadline', $tugas->deadline ? $tugas->deadline->format('Y-m-d\TH:i') : '') }}" 
                               required 
                               class="w-full text-sm font-semibold rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all text-slate-700 @error('deadline') border-rose-500 @enderror">
                        @error('deadline')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Penugasan -->
                    <div>
                        <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Status Penugasan <span class="text-rose-500">*</span>
                        </label>
                        <select id="status" name="status" class="w-full text-sm font-semibold rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all text-slate-700 @error('status') border-rose-500 @enderror">
                            <option value="aktif" {{ old('status', $tugas->status) === 'aktif' ? 'selected' : '' }}>🟢 Aktif (Sedang Berjalan)</option>
                            <option value="selesai" {{ old('status', $tugas->status) === 'selesai' ? 'selected' : '' }}>🔵 Selesai (Ditutup)</option>
                            <option value="dibatalkan" {{ old('status', $tugas->status) === 'dibatalkan' ? 'selected' : '' }}>🔴 Dibatalkan</option>
                        </select>
                        @error('status')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Format Pengumpulan -->
                    <div class="sm:col-span-2">
                        <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Format Pengumpulan Dokumen <span class="text-rose-500">*</span>
                        </span>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="relative flex items-start gap-3 p-4 rounded-2xl border border-slate-200 hover:border-[#059669] hover:bg-emerald-50/40 transition cursor-pointer group">
                                <input type="radio" name="format_pengumpulan" value="digital" {{ old('format_pengumpulan', $tugas->format_pengumpulan) === 'digital' ? 'checked' : '' }} class="mt-0.5 text-[#059669] focus:ring-[#059669]">
                                <div>
                                    <span class="text-xs font-bold text-slate-900 group-hover:text-[#064e3b] flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-[#059669]">cloud_upload</span>
                                        Digital (Upload)
                                    </span>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Unggah berkas PDF / XLSX langsung di sistem</p>
                                </div>
                            </label>
                            <label class="relative flex items-start gap-3 p-4 rounded-2xl border border-slate-200 hover:border-[#059669] hover:bg-emerald-50/40 transition cursor-pointer group">
                                <input type="radio" name="format_pengumpulan" value="fisik" {{ old('format_pengumpulan', $tugas->format_pengumpulan) === 'fisik' ? 'checked' : '' }} class="mt-0.5 text-[#059669] focus:ring-[#059669]">
                                <div>
                                    <span class="text-xs font-bold text-slate-900 group-hover:text-[#064e3b] flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-amber-600">inventory_2</span>
                                        Fisik (Hardcopy)
                                    </span>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Penyerahan hardcopy langsung ke loket Bidang</p>
                                </div>
                            </label>
                            <label class="relative flex items-start gap-3 p-4 rounded-2xl border border-slate-200 hover:border-[#059669] hover:bg-emerald-50/40 transition cursor-pointer group">
                                <input type="radio" name="format_pengumpulan" value="hybrid" {{ old('format_pengumpulan', $tugas->format_pengumpulan) === 'hybrid' ? 'checked' : '' }} class="mt-0.5 text-[#059669] focus:ring-[#059669]">
                                <div>
                                    <span class="text-xs font-bold text-slate-900 group-hover:text-[#064e3b] flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-indigo-600">sync_alt</span>
                                        Hybrid (Keduanya)
                                    </span>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Unggah softcopy &amp; serahkan hardcopy</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Target OPD -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <span class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Target Instansi OPD Ditugaskan <span class="text-rose-500">*</span>
                            </span>
                            <p class="text-[11px] text-slate-500">Pilih instansi OPD yang wajib menyelesaikan penugasan ini</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="selectAllOpds(true)" class="text-[11px] font-bold text-[#059669] hover:underline px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200">
                                Pilih Semua
                            </button>
                            <button type="button" onclick="selectAllOpds(false)" class="text-[11px] font-bold text-slate-500 hover:underline px-2.5 py-1 rounded-full bg-slate-100 border border-slate-200">
                                Reset
                            </button>
                        </div>
                    </div>

                    <!-- Search filter OPD -->
                    <div class="mb-2.5">
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-slate-400 pointer-events-none">search</span>
                            <input type="text" id="filterOpdInput" onkeyup="filterOpdList()" placeholder="Cari nama OPD atau kode..." class="w-full pl-10 pr-4 py-2 text-xs rounded-xl bg-[#f0f5f2] border-transparent focus:border-[#059669] focus:bg-white focus:ring-2 focus:ring-[#059669]/20 transition-all">
                        </div>
                    </div>

                    <div id="opdListContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-72 overflow-y-auto p-3 rounded-2xl border border-emerald-100 bg-[#fbfdfc]">
                        @foreach($opds as $opd)
                            <label class="opd-item flex items-start gap-2.5 p-2.5 rounded-xl bg-white border border-slate-200/80 hover:border-[#059669] hover:bg-emerald-50/20 transition cursor-pointer shadow-2xs" data-opd-name="{{ strtolower($opd->nama_opd . ' ' . $opd->kode_opd) }}">
                                <input type="checkbox" 
                                       name="opd_ids[]" 
                                       value="{{ $opd->id }}" 
                                       {{ in_array($opd->id, old('opd_ids', $assignedOpdIds)) ? 'checked' : '' }}
                                       class="opd-checkbox mt-0.5 text-[#059669] rounded-md border-slate-300 focus:ring-[#059669]">
                                <div class="text-xs">
                                    <span class="font-bold text-[#059669] block font-mono text-[10px] tracking-wider">{{ $opd->kode_opd }}</span>
                                    <span class="text-slate-800 font-semibold leading-tight block">{{ $opd->nama_opd }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.tugas.show', $tugas) }}" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition transform active:scale-95">
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        <span>Perbarui Penugasan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    function selectAllOpds(check) {
        document.querySelectorAll('.opd-checkbox').forEach(cb => {
            cb.checked = check;
        });
    }

    function filterOpdList() {
        const query = document.getElementById('filterOpdInput').value.toLowerCase();
        const items = document.querySelectorAll('.opd-item');
        items.forEach(item => {
            const name = item.getAttribute('data-opd-name');
            if (name.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>
</x-app-layout>
