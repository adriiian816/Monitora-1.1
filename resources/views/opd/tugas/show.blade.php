<x-app-layout>
    @php
        $sub = $tugasOpd->submission;
        $isOverdue = $tugas->isPastDeadline();
        $format = $tugas->format_pengumpulan;
    @endphp

    <div class="max-w-4xl mx-auto">
        <!-- Top Back Nav -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('opd.tugas.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Daftar Tugas Saya
                </a>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-blue-50 text-blue-700">
                        Format: {{ $tugas->format_label }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                        Tahun Anggaran {{ $tugas->tahun_anggaran }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-2">
                    {{ $tugas->judul }}
                </h1>
            </div>

            <!-- Calendar .ics Download Button -->
            <div class="shrink-0">
                <a href="{{ route('opd.tugas.ics', $tugas) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 text-xs font-bold shadow-sm transition">
                    <span>📅</span>
                    Simpan ke Google Calendar / Outlook (.ics)
                </a>
            </div>
        </div>

        <!-- Status Alert Banner -->
        <div class="mb-6 p-5 rounded-2xl border 
            {{ $tugasOpd->status === 'diterima' ? 'bg-blue-50 border-blue-200 text-blue-900' : '' }}
            {{ $tugasOpd->status === 'sudah_dikumpulkan' ? 'bg-sky-50 border-sky-200 text-sky-950' : '' }}
            {{ $tugasOpd->status === 'perlu_revisi' ? 'bg-amber-50 border-amber-200 text-amber-900' : '' }}
            {{ $tugasOpd->status === 'terlambat' ? 'bg-rose-50 border-rose-200 text-rose-900' : '' }}
            {{ $tugasOpd->status === 'belum_dikumpulkan' ? 'bg-slate-100 border-slate-200 text-slate-800' : '' }}">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-lg shrink-0
                        {{ $tugasOpd->status === 'diterima' ? 'bg-blue-100 text-blue-800 border border-blue-200' : '' }}
                        {{ $tugasOpd->status === 'sudah_dikumpulkan' ? 'bg-sky-100 text-sky-800 border border-sky-200' : '' }}
                        {{ $tugasOpd->status === 'perlu_revisi' ? 'bg-amber-100 text-amber-800 border border-amber-200' : '' }}
                        {{ $tugasOpd->status === 'terlambat' ? 'bg-rose-100 text-rose-800 border border-rose-200' : '' }}
                        {{ $tugasOpd->status === 'belum_dikumpulkan' ? 'bg-slate-200 text-slate-700' : '' }}">
                        @if($tugasOpd->status === 'diterima') ✓ @elseif($tugasOpd->status === 'sudah_dikumpulkan') ⏳ @elseif($tugasOpd->status === 'perlu_revisi') ⚠️ @elseif($tugasOpd->status === 'terlambat') ! @else 📋 @endif
                    </span>
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider block opacity-75">Status Pengumpulan Instansi Anda:</span>
                        <h2 class="text-base font-black">
                            {{ $tugasOpd->status_label }}
                        </h2>
                    </div>
                </div>

                <div class="text-left sm:text-right">
                    <span class="text-xs font-semibold opacity-75 block">Batas Waktu Deadline (WIT):</span>
                    <span class="text-sm font-bold block">{{ $tugas->deadline->translatedFormat('l, d F Y - H:i') }} WIT</span>
                    <span class="text-xs font-semibold block mt-0.5 {{ $isOverdue ? 'text-rose-600' : 'text-blue-800' }}">
                        {{ $tugas->sisa_waktu_label }}
                    </span>
                </div>
            </div>

            @if($sub && $sub->catatan_admin)
                <div class="mt-4 pt-3 border-t border-amber-200/80 text-xs">
                    <strong class="text-amber-900 block mb-1">Catatan Evaluasi / Masukan dari PIC:</strong>
                    <p class="italic bg-white/80 p-3 rounded-xl border border-amber-200 text-amber-950 whitespace-pre-line">
                        "{{ $sub->catatan_admin }}"
                    </p>
                </div>
            @endif
        </div>

        <!-- Task Instructions Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Petunjuk & Instruksi dari Bappeda</h2>
            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                {{ $tugas->deskripsi ?: 'Tidak ada rincian tambahan dari Admin.' }}
            </p>
        </div>

        <!-- Submission Form Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-8">
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Formulir Pengumpulan Dokumen</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Format pengumpulan untuk tugas ini adalah: <strong class="text-slate-800">{{ $tugas->format_label }}</strong>
                    </p>
                </div>
                @if($sub)
                    <span class="text-[11px] text-slate-400">
                        Terakhir diperbarui: {{ $sub->updated_at->diffForHumans() }}
                    </span>
                @endif
            </div>

            <form method="POST" action="{{ route('opd.submission.store', $tugas) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- 1. Digital File Upload Section (If Digital or Hybrid) -->
                @if($format === 'digital' || $format === 'hybrid')
                    <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                Unggah Berkas Digital (Softcopy)
                            </h3>
                            <span class="text-[11px] text-slate-400">Format: PDF, Word, Excel, ZIP (Maks. 25 MB)</span>
                        </div>

                        @if($sub && $sub->file_path)
                            <div class="p-3.5 rounded-xl bg-white border border-slate-200 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5 truncate">
                                    <span class="text-lg">📄</span>
                                    <div class="truncate text-xs">
                                        <span class="font-bold text-slate-800 block truncate">{{ $sub->file_original_name }}</span>
                                        <span class="text-slate-400 text-[11px]">Ukuran: {{ $sub->formatted_file_size }}</span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-200 shrink-0">
                                    Sudah Terunggah
                                </span>
                            </div>
                        @endif

                        <div>
                            <label for="berkas_dokumen" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ ($sub && $sub->file_path) ? 'Unggah Berkas Baru / Ganti File:' : 'Pilih Berkas yang Akan Diunggah:' }}
                            </label>
                            <input type="file" 
                                   id="berkas_dokumen" 
                                   name="berkas_dokumen" 
                                   {{ ($sub && $sub->file_path) ? '' : 'required' }}
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-900 file:text-white hover:file:bg-blue-950 cursor-pointer border border-slate-300 rounded-xl bg-white p-2">
                            <span class="text-[11px] text-slate-400 mt-1 block">Pastikan dokumen telah ditandatangani bila diperlukan.</span>
                        </div>
                    </div>
                @endif

                <!-- 2. Physical Handover Section (If Fisik or Hybrid) -->
                @if($format === 'fisik' || $format === 'hybrid')
                    <div class="p-5 rounded-2xl bg-sky-50/50 border border-sky-200 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-sky-950 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                                Konfirmasi Penyerahan Berkas Fisik (Hardcopy Asli)
                            </h3>
                            <span class="text-[11px] text-sky-800 font-medium">BAPPEDA Prov. Malut</span>
                        </div>

                        <div class="flex items-start gap-2.5 pt-1">
                            <input type="checkbox" 
                                   id="konfirmasi_fisik" 
                                   name="konfirmasi_fisik" 
                                   value="1" 
                                   {{ old('konfirmasi_fisik', $sub?->konfirmasi_fisik) ? 'checked' : '' }}
                                   required 
                                   class="mt-1 text-blue-700 rounded border-sky-300 focus:ring-blue-500">
                            <label for="konfirmasi_fisik" class="text-xs font-bold text-sky-950 cursor-pointer leading-relaxed">
                                Saya mengonfirmasi bahwa dokumen fisik asli bertanda tangan/berstempel telah diserahkan langsung ke Kantor Bappeda Provinsi Maluku Utara.
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div>
                                <label for="nama_pengantar" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                    Nama Staf Pengantar Berkas <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="nama_pengantar" 
                                       name="nama_pengantar" 
                                       value="{{ old('nama_pengantar', $sub?->nama_pengantar) }}" 
                                       required 
                                       placeholder="Contoh: Muhammad Ali (Staf Perencanaan)" 
                                       class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                            </div>

                            <div>
                                <label for="nomor_surat" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                    Nomor Surat Pengantar / Berita Acara <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="nomor_surat" 
                                       name="nomor_surat" 
                                       value="{{ old('nomor_surat', $sub?->nomor_surat) }}" 
                                       required 
                                       placeholder="Contoh: 050/124/DISPERINDAG/IX/2026" 
                                       class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="tanggal_penyerahan_fisik" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                    Tanggal Penyerahan Fisik ke Bappeda <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" 
                                       id="tanggal_penyerahan_fisik" 
                                       name="tanggal_penyerahan_fisik" 
                                       value="{{ old('tanggal_penyerahan_fisik', $sub?->tanggal_penyerahan_fisik ? $sub->tanggal_penyerahan_fisik->format('Y-m-d') : date('Y-m-d')) }}" 
                                       required 
                                       class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Catatan OPD -->
                <div>
                    <label for="catatan_opd" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Catatan Tambahan untuk PIC (Opsional)
                    </label>
                    <textarea id="catatan_opd" 
                              name="catatan_opd" 
                              rows="3" 
                              placeholder="Ketik keterangan atau pesan yang ingin disampaikan kepada tim verifikator Bappeda..." 
                              class="w-full text-xs rounded-xl border border-slate-300 p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-600 leading-relaxed">{{ old('catatan_opd', $sub?->catatan_opd) }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('opd.tugas.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs shadow-sm transition">
                        {{ $sub ? 'Kirim Pembaruan / Revisi Dokumen' : 'Kirim Pengumpulan Dokumen' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- WhatsApp Logs for this Task received by this OPD -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-bold text-slate-900 mb-1">Histori Pengingat WhatsApp yang Diterima</h3>
            <p class="text-xs text-slate-500 mb-4">Daftar notifikasi WhatsApp resmi dari Bappeda terkait penugasan ini</p>

            <div class="space-y-3">
                @forelse($waLogs as $log)
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="font-bold text-blue-900">{{ $log->tipe_label }}</span>
                            <span class="text-[11px] text-slate-400">{{ $log->sent_at->translatedFormat('d M Y - H:i') }} WIT</span>
                        </div>
                        <p class="text-slate-700 whitespace-pre-line text-[11px] font-sans leading-relaxed">
                            {{ $log->isi_pesan }}
                        </p>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">
                        Belum ada pesan WhatsApp yang dikirimkan untuk tugas ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
