<x-app-layout pageTitle="Buat Penugasan Dokumen OPD">
<main class="flex-1 p-6 md:p-8 overflow-y-auto bg-[#fafcfb]">
    <div class="max-w-4xl mx-auto flex flex-col gap-6">

        {{-- ===== PAGE HEADER ===== --}}
        <div>
            <a href="{{ route('admin.tugas.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#059669] transition mb-2">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali ke Manajemen Tugas</span>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white text-[#059669] flex items-center justify-center border border-emerald-200 shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">assignment_add</span>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">Buat Penugasan Dokumen OPD</h1>
                        <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                            T.A. {{ date('Y') }} • Triwulan I
                        </span>
                    </div>
                    <p class="text-[13px] text-slate-500 mt-0.5">Tentukan instruksi, batas deadline pengumpulan, format penyerahan berkas, dan target instansi OPD</p>
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
            <form method="POST" action="{{ route('admin.tugas.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Judul & Tahun Anggaran -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
                    <div class="sm:col-span-3">
                        <label for="judul" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Judul Tugas / Nama Laporan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="judul" 
                               name="judul" 
                               value="{{ old('judul') }}" 
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
                               value="{{ old('tahun_anggaran', date('Y')) }}" 
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
                              class="w-full text-sm rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all leading-relaxed @error('deskripsi') border-rose-500 @enderror">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Upload Berkas Lampiran / Template Tugas -->
                <div x-data="{
                    fileName: '',
                    fileSize: '',
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
                        <label for="file_tugas" class="block text-xs font-bold text-[#064e3b] uppercase tracking-wider flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-[#059669]">attach_file</span>
                            <span>Upload Lampiran / Template Dokumen Tugas (Opsional)</span>
                        </label>
                        <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full self-start sm:self-auto">
                            PDF, Excel, Word, PPT, ZIP/RAR (Maks. 25 MB)
                        </span>
                    </div>

                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Sertakan dokumen panduan teknis, formulir template isian (Excel/Word), atau surat penugasan resmi agar staf OPD dapat mengunduhnya secara langsung.
                    </p>

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
                                <span>Hapus Berkas</span>
                            </button>
                        </template>
                    </div>

                    <template x-if="fileName">
                        <div class="p-3 rounded-xl bg-white border border-emerald-200 flex items-center justify-between text-xs text-slate-700">
                            <div class="flex items-center gap-2 truncate">
                                <span class="material-symbols-outlined text-[#059669] text-[18px]">check_circle</span>
                                <span class="font-bold text-[#064e3b] truncate" x-text="fileName"></span>
                                <span class="text-slate-400 font-medium" x-text="'(' + fileSize + ')'"></span>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full shrink-0">Siap Diunggah</span>
                        </div>
                    </template>

                    @error('file_tugas')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Batas Waktu Deadline & Format Pengumpulan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                    <!-- Deadline -->
                    <div>
                        <label for="deadline" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Batas Waktu Deadline (WIT) <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" 
                               id="deadline" 
                               name="deadline" 
                               value="{{ old('deadline', now()->addDays(7)->setTime(23, 59)->format('Y-m-d\TH:i')) }}" 
                               required 
                               class="w-full text-sm font-semibold rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-3 focus:ring-2 focus:ring-[#059669]/20 transition-all @error('deadline') border-rose-500 @enderror">
                        <span class="text-[11px] text-slate-400 mt-1.5 block">Sistem otomatis mengirimkan pengingat WhatsApp pada H-7, H-3, H-1, dan H-0.</span>
                        @error('deadline')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Format Pengumpulan (3 Format Sesuai SKPL) -->
                    <div>
                        <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Format Pengumpulan Dokumen <span class="text-rose-500">*</span>
                        </span>
                        <div class="space-y-2.5">
                            <label class="flex items-start gap-3 p-3 rounded-2xl border border-slate-200 hover:border-[#059669] hover:bg-emerald-50/20 transition cursor-pointer shadow-xs">
                                <input type="radio" name="format_pengumpulan" value="digital" {{ old('format_pengumpulan', 'digital') === 'digital' ? 'checked' : '' }} class="mt-0.5 text-[#059669] focus:ring-[#059669]">
                                <div>
                                    <span class="block text-xs font-bold text-slate-900">1. Digital (Upload File)</span>
                                    <span class="block text-[11px] text-slate-500">OPD cukup mengunggah file softcopy (PDF, Excel, Word, ZIP).</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3 rounded-2xl border border-slate-200 hover:border-[#059669] hover:bg-emerald-50/20 transition cursor-pointer shadow-xs">
                                <input type="radio" name="format_pengumpulan" value="fisik" {{ old('format_pengumpulan') === 'fisik' ? 'checked' : '' }} class="mt-0.5 text-[#059669] focus:ring-[#059669]">
                                <div>
                                    <span class="block text-xs font-bold text-slate-900">2. Fisik (Penyerahan Langsung)</span>
                                    <span class="block text-[11px] text-slate-500">OPD mengonfirmasi penyerahan berkas fisik asli basah ke loket Bidang Perekonomian &amp; Perdagangan.</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3 rounded-2xl border border-slate-200 hover:border-[#059669] hover:bg-emerald-50/20 transition cursor-pointer shadow-xs">
                                <input type="radio" name="format_pengumpulan" value="hybrid" {{ old('format_pengumpulan') === 'hybrid' ? 'checked' : '' }} class="mt-0.5 text-[#059669] focus:ring-[#059669]">
                                <div>
                                    <span class="block text-xs font-bold text-slate-900">3. Hybrid (Digital &amp; Fisik)</span>
                                    <span class="block text-[11px] text-slate-500">Kombinasi unggah file softcopy sekaligus konfirmasi fisik berkas basah.</span>
                                </div>
                            </label>
                        </div>
                        @error('format_pengumpulan')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Pemilihan Target OPD & Staf Penerima Notifikasi -->
                <div x-data="{
                    searchOpd: '',
                    selectAllOpd(checked) {
                        document.querySelectorAll('.opd-checkbox').forEach(el => {
                            el.checked = checked;
                        });
                        if (!checked) {
                            document.querySelectorAll('.staff-checkbox').forEach(el => el.checked = false);
                            document.querySelectorAll('.all-staff-toggle').forEach(el => el.checked = false);
                        }
                    },
                    selectAllStaff(checked) {
                        document.querySelectorAll('.staff-checkbox').forEach(el => el.checked = checked);
                        document.querySelectorAll('.all-staff-toggle').forEach(el => el.checked = checked);
                    },
                    toggleAllStaffForOpd(opdId, checked) {
                        document.querySelectorAll('.staff-opd-' + opdId).forEach(el => el.checked = checked);
                        if (checked) {
                            const opdCb = document.getElementById('opd_checkbox_' + opdId);
                            if (opdCb) opdCb.checked = true;
                        }
                    },
                    onStaffChange(opdId) {
                        const staffCheckboxes = document.querySelectorAll('.staff-opd-' + opdId);
                        const allToggle = document.getElementById('toggle_all_staff_' + opdId);
                        if (staffCheckboxes.length > 0 && allToggle) {
                            const allChecked = Array.from(staffCheckboxes).every(el => el.checked);
                            allToggle.checked = allChecked;
                        }
                        const anyChecked = Array.from(staffCheckboxes).some(el => el.checked);
                        if (anyChecked) {
                            const opdCb = document.getElementById('opd_checkbox_' + opdId);
                            if (opdCb) opdCb.checked = true;
                        }
                    },
                    onOpdChange(opdId, checked) {
                        if (!checked) {
                            document.querySelectorAll('.staff-opd-' + opdId).forEach(el => el.checked = false);
                            const allToggle = document.getElementById('toggle_all_staff_' + opdId);
                            if (allToggle) allToggle.checked = false;
                        } else {
                            document.querySelectorAll('.staff-opd-' + opdId).forEach(el => el.checked = true);
                            const allToggle = document.getElementById('toggle_all_staff_' + opdId);
                            if (allToggle) allToggle.checked = true;
                        }
                    }
                }" class="pt-4 border-t border-slate-100 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Target Instansi OPD &amp; Staf Penerima Notifikasi <span class="text-rose-500">*</span>
                            </span>
                            <span class="text-[11px] text-slate-400">Pilih instansi OPD yang ditugaskan serta tentukan staf penerima notifikasi WhatsApp</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <button type="button" @click="selectAllOpd(true)" class="px-3 py-1.5 rounded-full bg-emerald-50 hover:bg-emerald-100 text-[#064e3b] text-xs font-bold transition border border-emerald-200">
                                Pilih Semua OPD
                            </button>
                            <button type="button" @click="selectAllOpd(false)" class="px-3 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                                Hapus Pilihan
                            </button>
                            <button type="button" @click="selectAllStaff(true)" class="px-3 py-1.5 rounded-full bg-teal-50 hover:bg-teal-100 text-teal-800 text-xs font-bold transition border border-teal-200">
                                Centang Semua Staf
                            </button>
                        </div>
                    </div>

                    <!-- Search Box OPD -->
                    <div class="relative flex items-center bg-[#f0f5f2] px-3.5 py-1.5 rounded-full border border-emerald-100/60 focus-within:bg-white focus-within:ring-2 focus-within:ring-[#059669]/20 transition-all">
                        <span class="material-symbols-outlined text-slate-400 text-[18px] mr-2">search</span>
                        <input type="text" 
                               x-model="searchOpd" 
                               placeholder="Cari kode atau nama instansi OPD..." 
                               class="w-full bg-transparent p-0 text-slate-800 placeholder:text-slate-400 text-xs focus:outline-none focus:ring-0">
                    </div>

                    <!-- OPD & Staff List Container -->
                    <div class="space-y-3 max-h-96 overflow-y-auto p-3.5 rounded-2xl border border-emerald-100/80 bg-[#fafcfb]">
                        @foreach($opds as $opd)
                            @php
                                $isOpdChecked = in_array($opd->id, old('opd_ids', $opds->pluck('id')->toArray()));
                                $staffCount = $opd->users->count();
                                $opdSearchContent = strtolower($opd->kode_opd . ' ' . $opd->nama_opd);
                            @endphp
                            <div x-show="!searchOpd || '{{ addslashes($opdSearchContent) }}'.includes(searchOpd.toLowerCase())"
                                 class="p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-[#059669]/50 transition shadow-xs">
                                <div class="flex items-start justify-between gap-3">
                                    <label class="flex items-start gap-3 cursor-pointer flex-1">
                                        <input type="checkbox" 
                                               id="opd_checkbox_{{ $opd->id }}"
                                               name="opd_ids[]" 
                                               value="{{ $opd->id }}" 
                                               {{ $isOpdChecked ? 'checked' : '' }}
                                               @change="onOpdChange({{ $opd->id }}, $event.target.checked)"
                                               class="opd-checkbox mt-0.5 text-[#059669] rounded border-slate-300 focus:ring-[#059669]">
                                        <div class="text-xs">
                                            <span class="font-bold text-[#064e3b] font-mono text-[11px] px-2 py-0.5 rounded bg-[#f0f5f2] border border-emerald-200">{{ $opd->kode_opd }}</span>
                                            <span class="font-bold text-slate-800 ml-1.5">{{ $opd->nama_opd }}</span>
                                        </div>
                                    </label>

                                    @if($staffCount > 0)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 bg-slate-100 px-2.5 py-0.5 rounded-full">
                                            <span class="material-symbols-outlined text-[14px] text-slate-500">group</span>
                                            {{ $staffCount }} Staf
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-[10px] font-medium text-slate-400 bg-slate-50 px-2.5 py-0.5 rounded-full border border-slate-100">
                                            Belum ada staf
                                        </span>
                                    @endif
                                </div>

                                @if($staffCount > 0)
                                    <div class="mt-3 pt-3 border-t border-slate-100 pl-6 space-y-2">
                                        <!-- Toggle All Staff per OPD -->
                                        <div class="flex items-center justify-between pb-1.5 border-b border-dashed border-slate-200">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox"
                                                       id="toggle_all_staff_{{ $opd->id }}"
                                                       {{ $isOpdChecked ? 'checked' : '' }}
                                                       @change="toggleAllStaffForOpd({{ $opd->id }}, $event.target.checked)"
                                                       class="all-staff-toggle all-staff-toggle-{{ $opd->id }} text-[#059669] rounded border-slate-300 focus:ring-[#059669] text-xs">
                                                <span class="text-[11px] font-bold text-slate-700">
                                                    Centang Semua Staf OPD Ini ({{ $staffCount }} orang)
                                                </span>
                                            </label>
                                            <span class="text-[10px] text-slate-400">Atau pilih staf tertentu:</span>
                                        </div>

                                        <!-- Pilihan Spesifik per Staf -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                            @foreach($opd->users as $staff)
                                                @php
                                                    $isStaffChecked = old('staff_ids') !== null 
                                                        ? in_array($staff->id, old('staff_ids', []))
                                                        : $isOpdChecked;
                                                @endphp
                                                <label class="flex items-start gap-2 p-2 rounded-xl border border-slate-100 bg-[#fafcfb] hover:bg-emerald-50/30 hover:border-emerald-200 transition cursor-pointer">
                                                    <input type="checkbox"
                                                           name="staff_ids[]"
                                                           value="{{ $staff->id }}"
                                                           {{ $isStaffChecked ? 'checked' : '' }}
                                                           @change="onStaffChange({{ $opd->id }})"
                                                           class="staff-checkbox staff-opd-{{ $opd->id }} mt-0.5 text-[#059669] rounded border-slate-300 focus:ring-[#059669]">
                                                    <div class="text-[11px] leading-tight min-w-0 flex-1">
                                                        <span class="font-semibold text-slate-800 block truncate">{{ $staff->name }}</span>
                                                        @if($staff->no_hp)
                                                            <span class="inline-flex items-center gap-1 text-[10px] text-[#059669] font-mono mt-0.5 font-medium">
                                                                <span class="material-symbols-outlined text-[12px]">chat</span>
                                                                {{ $staff->no_hp }}
                                                            </span>
                                                        @else
                                                            <span class="text-[10px] text-amber-600 italic block mt-0.5">
                                                                (Belum ada No HP)
                                                            </span>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-2 pl-6 text-[11px] text-slate-400 italic">
                                        Belum ada staf terdaftar pada OPD ini. Tambahkan staf di menu <a href="{{ route('admin.users.create') }}" class="text-[#059669] hover:underline font-semibold">Kelola Pengguna</a>.
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Notifikasi WhatsApp Otomatis -->
                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 flex items-start gap-3 shadow-xs">
                    <input type="checkbox" 
                           id="send_wa" 
                           name="send_wa" 
                           value="1" 
                           {{ old('send_wa', 1) ? 'checked' : '' }}
                           class="mt-1 text-[#059669] rounded border-slate-300 focus:ring-[#059669]">
                    <div>
                        <label for="send_wa" class="text-xs font-bold text-[#064e3b] block cursor-pointer">
                            Kirim Notifikasi Tugas Baru Otomatis via WhatsApp Sekarang
                        </label>
                        <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">
                            Sistem akan secara instan mencatat dan mengirimkan pesan instruksi tugas baru kepada seluruh nomor WhatsApp Staf OPD yang dipilih di atas via WhatsApp Gateway.
                        </p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.tugas.index') }}" class="px-5 py-2.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white font-bold text-xs shadow-md shadow-emerald-900/10 transition-all hover:scale-[1.01] active:scale-[0.99]">
                        <span class="material-symbols-outlined text-[18px]">rocket_launch</span>
                        <span>Terbitkan &amp; Distribusikan Tugas</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
</x-app-layout>
