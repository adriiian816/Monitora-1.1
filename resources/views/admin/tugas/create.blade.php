<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.tugas.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Manajemen Tugas
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Buat Penugasan Dokumen OPD Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Tentukan instruksi, batas deadline pengumpulan, format penyerahan berkas, dan target instansi OPD</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.tugas.store') }}" class="space-y-6">
                @csrf

                <!-- Judul & Tahun Anggaran -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div class="sm:col-span-3">
                        <label for="judul" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Judul Tugas / Nama Laporan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="judul" 
                               name="judul" 
                               value="{{ old('judul') }}" 
                               required 
                               placeholder="Contoh: Penyusunan Rencana Kerja (RENJA) OPD TA 2027" 
                               class="w-full text-sm font-semibold rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                    </div>

                    <div>
                        <label for="tahun_anggaran" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Tahun Anggaran <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="tahun_anggaran" 
                               name="tahun_anggaran" 
                               value="{{ old('tahun_anggaran', date('Y')) }}" 
                               required 
                               placeholder="2026 / 2027" 
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
                              placeholder="Tuliskan petunjuk rinci berkas yang wajib dilampirkan, ketentuan format laporan, kontak narahubung Bappeda, dsb..." 
                              class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 leading-relaxed">{{ old('deskripsi') }}</textarea>
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
                               value="{{ old('deadline', now()->addDays(7)->setTime(23, 59)->format('Y-m-d\TH:i')) }}" 
                               required 
                               class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                        <span class="text-[11px] text-slate-500 mt-1 block">Sistem otomatis mengirimkan pengingat WhatsApp pada H-7, H-3, H-1, dan H-0</span>
                    </div>

                    <!-- Format Pengumpulan (3 Format Sesuai SKPL) -->
                    <div>
                        <span class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Format Pengumpulan Dokumen <span class="text-rose-500">*</span>
                        </span>
                        <div class="space-y-2">
                            <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-400 hover:bg-blue-50/40 transition cursor-pointer">
                                <input type="radio" name="format_pengumpulan" value="digital" {{ old('format_pengumpulan', 'digital') === 'digital' ? 'checked' : '' }} class="mt-0.5 text-blue-700 focus:ring-blue-600">
                                <div>
                                    <span class="block text-xs font-bold text-slate-900">1. Digital (Upload Berkas)</span>
                                    <span class="block text-[11px] text-slate-600">OPD cukup mengunggah file softcopy (PDF, Excel, Word, ZIP).</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-400 hover:bg-blue-50/40 transition cursor-pointer">
                                <input type="radio" name="format_pengumpulan" value="fisik" {{ old('format_pengumpulan') === 'fisik' ? 'checked' : '' }} class="mt-0.5 text-blue-700 focus:ring-blue-600">
                                <div>
                                    <span class="block text-xs font-bold text-slate-900">2. Fisik (Penyerahan Langsung)</span>
                                    <span class="block text-[11px] text-slate-600">OPD mengonfirmasi penyerahan berkas fisik asli basah ke kantor Bappeda.</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-400 hover:bg-blue-50/40 transition cursor-pointer">
                                <input type="radio" name="format_pengumpulan" value="hybrid" {{ old('format_pengumpulan') === 'hybrid' ? 'checked' : '' }} class="mt-0.5 text-blue-700 focus:ring-blue-600">
                                <div>
                                    <span class="block text-xs font-bold text-slate-900">3. Hybrid (Digital & Fisik)</span>
                                    <span class="block text-[11px] text-slate-600">Kombinasi unggah file softcopy sekaligus konfirmasi fisik berkas basah.</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Pemilihan Target OPD & Staf Penerima Notifikasi -->
                <div x-data="{
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
                }" class="pt-4 border-t border-slate-100">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                        <div>
                            <span class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Target Instansi OPD & Staf Penerima Notifikasi <span class="text-rose-500">*</span>
                            </span>
                            <span class="text-[11px] text-slate-500">Pilih instansi OPD yang ditugaskan serta tentukan staf penerima notifikasi WhatsApp</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" @click="selectAllOpd(true)" class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-800 text-xs font-semibold transition border border-blue-200">
                                Pilih Semua OPD
                            </button>
                            <button type="button" @click="selectAllOpd(false)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                                Hapus Pilihan
                            </button>
                            <button type="button" @click="selectAllStaff(true)" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-semibold transition border border-emerald-200">
                                Centang Semua Staf
                            </button>
                        </div>
                    </div>

                    <div class="space-y-3 max-h-96 overflow-y-auto p-3 rounded-xl border border-slate-200 bg-slate-50/50">
                        @foreach($opds as $opd)
                            @php
                                $isOpdChecked = in_array($opd->id, old('opd_ids', $opds->pluck('id')->toArray()));
                                $staffCount = $opd->users->count();
                            @endphp
                            <div class="p-3 rounded-xl bg-white border border-slate-200 hover:border-blue-300 transition shadow-xs">
                                <div class="flex items-start justify-between gap-3">
                                    <label class="flex items-start gap-2.5 cursor-pointer flex-1">
                                        <input type="checkbox" 
                                               id="opd_checkbox_{{ $opd->id }}"
                                               name="opd_ids[]" 
                                               value="{{ $opd->id }}" 
                                               {{ $isOpdChecked ? 'checked' : '' }}
                                               @change="onOpdChange({{ $opd->id }}, $event.target.checked)"
                                               class="opd-checkbox mt-0.5 text-blue-700 rounded border-slate-300 focus:ring-blue-500">
                                        <div class="text-xs">
                                            <span class="font-bold text-blue-900 font-mono text-[11px]">{{ $opd->kode_opd }}</span>
                                            <span class="font-bold text-slate-800 ml-1.5">{{ $opd->nama_opd }}</span>
                                        </div>
                                    </label>

                                    @if($staffCount > 0)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            {{ $staffCount }} Staf
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-[10px] font-medium text-slate-400 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100">
                                            Belum ada staf
                                        </span>
                                    @endif
                                </div>

                                @if($staffCount > 0)
                                    <div class="mt-2.5 pt-2.5 border-t border-slate-100 pl-6 space-y-2">
                                        <!-- Opsi 1: Centang Semua Staf pada OPD Tersebut -->
                                        <div class="flex items-center justify-between pb-1.5 border-b border-dashed border-slate-200">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox"
                                                       id="toggle_all_staff_{{ $opd->id }}"
                                                       {{ $isOpdChecked ? 'checked' : '' }}
                                                       @change="toggleAllStaffForOpd({{ $opd->id }}, $event.target.checked)"
                                                       class="all-staff-toggle all-staff-toggle-{{ $opd->id }} text-blue-600 rounded border-slate-300 focus:ring-blue-500 text-xs">
                                                <span class="text-[11px] font-bold text-slate-700">
                                                    Centang Semua Staf OPD Ini ({{ $staffCount }} orang)
                                                </span>
                                            </label>
                                            <span class="text-[10px] text-slate-400">Atau pilih staf tertentu:</span>
                                        </div>

                                        <!-- Opsi 2: Pilihan Spesifik per Staf -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                            @foreach($opd->users as $staff)
                                                @php
                                                    $isStaffChecked = old('staff_ids') !== null 
                                                        ? in_array($staff->id, old('staff_ids', []))
                                                        : $isOpdChecked;
                                                @endphp
                                                <label class="flex items-start gap-2 p-2 rounded-lg border border-slate-100 bg-slate-50/60 hover:bg-blue-50/40 hover:border-blue-200 transition cursor-pointer">
                                                    <input type="checkbox"
                                                           name="staff_ids[]"
                                                           value="{{ $staff->id }}"
                                                           {{ $isStaffChecked ? 'checked' : '' }}
                                                           @change="onStaffChange({{ $opd->id }})"
                                                           class="staff-checkbox staff-opd-{{ $opd->id }} mt-0.5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                                    <div class="text-[11px] leading-tight min-w-0 flex-1">
                                                        <span class="font-semibold text-slate-800 block truncate">{{ $staff->name }}</span>
                                                        @if($staff->no_hp)
                                                            <span class="inline-flex items-center gap-1 text-[10px] text-emerald-700 font-mono mt-0.5 font-medium">
                                                                <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
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
                                        Belum ada staf terdaftar pada OPD ini. Tambahkan staf di menu <a href="{{ route('admin.users.create') }}" class="text-blue-600 hover:underline">Kelola Pengguna</a>.
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Notifikasi WhatsApp Otomatis -->
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 flex items-start gap-3">
                    <input type="checkbox" 
                           id="send_wa" 
                           name="send_wa" 
                           value="1" 
                           {{ old('send_wa', 1) ? 'checked' : '' }}
                           class="mt-1 text-blue-700 rounded border-blue-300 focus:ring-blue-500">
                    <div>
                        <label for="send_wa" class="text-xs font-bold text-blue-950 block cursor-pointer">
                            Kirim Notifikasi Tugas Baru Otomatis via WhatsApp Sekarang
                        </label>
                        <p class="text-[11px] text-blue-800 mt-0.5 leading-relaxed">
                            Sistem akan secara instan mencatat dan mengirimkan pesan notifikasi penugasan baru kepada seluruh nomor WhatsApp Staf OPD yang dipilih di atas (nomor telepon dikelola di Kelola Pengguna).
                        </p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.tugas.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm shadow-sm transition">
                        Terbitkan & Distribusikan Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
