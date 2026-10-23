<x-app-layout pageTitle="Detail & Pengumpulan Penugasan">
@php
    $sub = $tugasOpd->submission;
    $isOverdue = $tugas->isPastDeadline();
    $format = $tugas->format_pengumpulan;
@endphp

{{-- ===== TOP CONTROL BAR ===== --}}
<div class="px-6 md:px-8 py-4 bg-[#f7faf8] border-b border-emerald-100/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
    <div class="flex items-center gap-3">
        <a href="{{ route('opd.tugas.index') }}" 
           class="w-9 h-9 rounded-xl bg-white text-slate-600 hover:text-[#059669] hover:bg-emerald-50 flex items-center justify-center border border-emerald-200 shadow-xs transition">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">Detail &amp; Pengumpulan Tugas</h1>
                <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full font-mono">
                    {{ $tugas->format_label }}
                </span>
                <span class="text-[11px] font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-full">
                    TA {{ $tugas->tahun_anggaran }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Unggah berkas atau konfirmasi penyerahan dokumen fisik untuk Bidang Perekonomian dan Perdagangan</p>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('opd.tugas.ics', $tugas) }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-emerald-200 hover:bg-emerald-50 text-[#064e3b] text-xs font-bold shadow-xs transition">
            <span class="material-symbols-outlined text-[16px] text-[#059669]">calendar_month</span>
            <span>Simpan ke Kalender (.ics)</span>
        </a>
        <a href="{{ route('opd.tugas.index') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#064e3b] text-white text-xs font-bold shadow-xs hover:bg-[#043d2e] transition">
            <span class="material-symbols-outlined text-[16px]">folder_shared</span>
            <span>Daftar Tugas</span>
        </a>
    </div>
</div>

{{-- ===== MAIN SPLIT-PANEL WORKSPACE ===== --}}
<div class="flex-1 flex flex-col lg:flex-row min-w-0">

    {{-- ===== LEFT PANEL: Instructions & Submission Form (65%) ===== --}}
    <section class="flex-1 lg:w-[65%] p-6 md:p-8 flex flex-col gap-6">

        {{-- Task Header Card --}}
        <div class="bg-white rounded-3xl border border-emerald-100/80 shadow-xs p-6 sm:p-7">
            <div class="flex items-center gap-2.5 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    {{ $tugas->format_label }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                    Tahun Anggaran {{ $tugas->tahun_anggaran }}
                </span>
            </div>
            <h2 class="text-2xl font-bold text-[#064e3b] tracking-tight">
                {{ $tugas->judul }}
            </h2>
            <div class="mt-4 p-4 rounded-2xl bg-[#f8fbf9] border border-emerald-100/80">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="material-symbols-outlined text-[18px] text-[#059669]">info</span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Petunjuk &amp; Instruksi dari Bidang Perekonomian dan Perdagangan</h3>
                </div>
                <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                    {{ $tugas->deskripsi ?: 'Tidak ada rincian instruksi tambahan dari Admin Bidang Perekonomian dan Perdagangan.' }}
                </p>
            </div>

            @if($tugas->file_path)
                <div class="mt-4 p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-3 truncate">
                        <div class="w-10 h-10 rounded-xl bg-white text-[#059669] flex items-center justify-center border border-emerald-200 shrink-0 shadow-2xs">
                            <span class="material-symbols-outlined text-[22px]">attachment</span>
                        </div>
                        <div class="truncate text-xs">
                            <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Dokumen / Template Lampiran dari PIC Bidang:</span>
                            <span class="font-bold text-[#064e3b] text-sm block truncate">{{ $tugas->file_original_name ?: basename($tugas->file_path) }}</span>
                            <span class="text-slate-500 text-[11px]">Ukuran Berkas: {{ $tugas->formatted_file_size }}</span>
                        </div>
                    </div>
                    <a href="{{ route('opd.tugas.download', $tugas) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white text-xs font-bold shadow-md shadow-emerald-900/10 transition-all hover:scale-[1.01] active:scale-[0.99] shrink-0">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span>Unduh Dokumen / Template</span>
                    </a>
                </div>
            @endif
        </div>

        {{-- Submission Form Card --}}
        <div class="bg-white rounded-3xl border border-emerald-100/80 shadow-xs p-6 sm:p-8">
            <div class="mb-6 pb-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="text-base font-bold text-[#064e3b]">Formulir Pengumpulan Dokumen</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Ketentuan penyerahan berkas: <strong class="text-slate-800">{{ $tugas->format_label }}</strong>
                    </p>
                </div>
                @if($sub)
                    <span class="text-[11px] text-slate-400 font-medium">
                        Terakhir diupdate: {{ $sub->updated_at->diffForHumans() }}
                    </span>
                @endif
            </div>

            <form method="POST" action="{{ route('opd.submission.store', $tugas) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- 1. Digital File Upload Section (If Digital or Hybrid) --}}
                @if($format === 'digital' || $format === 'hybrid')
                    <div class="p-5 rounded-2xl bg-[#f8fbf9] border border-emerald-100 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#059669]"></span>
                                Unggah Berkas Digital (Softcopy)
                            </h4>
                            <span class="text-[11px] text-slate-400 font-medium">Format: PDF, Word, Excel, ZIP (Maks. 25 MB)</span>
                        </div>

                        @if($sub && $sub->file_path)
                            <div class="p-3.5 rounded-2xl bg-white border border-emerald-200 flex items-center justify-between gap-3 shadow-2xs">
                                <div class="flex items-center gap-3 truncate">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-[#059669] flex items-center justify-center shrink-0 border border-emerald-200">
                                        <span class="material-symbols-outlined text-[18px]">description</span>
                                    </div>
                                    <div class="truncate text-xs">
                                        <span class="font-bold text-slate-800 block truncate">{{ $sub->file_original_name }}</span>
                                        <span class="text-slate-400 text-[11px]">Ukuran berkas: {{ $sub->formatted_file_size }}</span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-[#064e3b] border border-emerald-200 shrink-0">
                                    ✓ Sudah Terunggah
                                </span>
                            </div>
                        @endif

                        <div>
                            <label for="berkas_dokumen" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ ($sub && $sub->file_path) ? 'Unggah Berkas Baru / Ganti File:' : 'Pilih Berkas yang Akan Diunggah:' }}
                            </label>
                            <input type="file" 
                                   id="berkas_dokumen" 
                                   name="berkas_dokumen" 
                                   {{ ($sub && $sub->file_path) ? '' : 'required' }}
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-[#059669] file:text-white hover:file:bg-[#047857] cursor-pointer border border-emerald-100 rounded-2xl bg-white p-2 focus:ring-2 focus:ring-[#059669]/20 transition">
                            <span class="text-[11px] text-slate-400 mt-1 block">Pastikan berkas telah ditandatangani dan berstempel resmi bila diperlukan.</span>
                        </div>
                    </div>
                @endif

                {{-- 2. Physical Handover Section (If Fisik or Hybrid) --}}
                @if($format === 'fisik' || $format === 'hybrid')
                    <div class="p-5 rounded-2xl bg-amber-50/40 border border-amber-200 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-amber-950 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                Konfirmasi Penyerahan Berkas Fisik (Hardcopy Asli)
                            </h4>
                            <span class="text-[11px] text-amber-800 font-bold bg-amber-100/80 px-2 py-0.5 rounded-full">Bidang Perekonomian</span>
                        </div>

                        <div class="flex items-start gap-2.5 pt-1">
                            <input type="checkbox" 
                                   id="konfirmasi_fisik" 
                                   name="konfirmasi_fisik" 
                                   value="1" 
                                   {{ old('konfirmasi_fisik', $sub?->konfirmasi_fisik) ? 'checked' : '' }}
                                   required 
                                   class="mt-1 text-[#059669] rounded-md border-amber-300 focus:ring-[#059669]">
                            <label for="konfirmasi_fisik" class="text-xs font-bold text-amber-950 cursor-pointer leading-relaxed">
                                Saya mengonfirmasi bahwa dokumen fisik asli bertanda tangan/berstempel telah diserahkan langsung ke Ruang Bidang Perekonomian dan Perdagangan Bappeda Provinsi Maluku Utara.
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div>
                                <label for="nama_pengantar" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Nama Staf Pengantar Berkas <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="nama_pengantar" 
                                       name="nama_pengantar" 
                                       value="{{ old('nama_pengantar', $sub?->nama_pengantar) }}" 
                                       required 
                                       placeholder="Contoh: Muhammad Ali (Staf Perencanaan)" 
                                       class="w-full text-xs font-semibold rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                            </div>

                            <div>
                                <label for="nomor_surat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Nomor Surat Pengantar / Berita Acara <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="nomor_surat" 
                                       name="nomor_surat" 
                                       value="{{ old('nomor_surat', $sub?->nomor_surat) }}" 
                                       required 
                                       placeholder="Contoh: 050/124/DISPERINDAG/IX/2026" 
                                       class="w-full text-xs font-semibold rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="tanggal_penyerahan_fisik" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Tanggal Penyerahan Fisik ke Bidang Perekonomian &amp; Perdagangan <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" 
                                       id="tanggal_penyerahan_fisik" 
                                       name="tanggal_penyerahan_fisik" 
                                       value="{{ old('tanggal_penyerahan_fisik', $sub?->tanggal_penyerahan_fisik ? $sub->tanggal_penyerahan_fisik->format('Y-m-d') : date('Y-m-d')) }}" 
                                       required 
                                       class="w-full text-xs font-semibold rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] px-4 py-2.5 focus:ring-2 focus:ring-[#059669]/20 transition-all text-slate-700">
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Catatan Tambahan OPD --}}
                <div>
                    <label for="catatan_opd" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Catatan Tambahan untuk PIC Bidang Perekonomian dan Perdagangan (Opsional)
                    </label>
                    <textarea id="catatan_opd" 
                              name="catatan_opd" 
                              rows="3" 
                              placeholder="Ketik keterangan atau pesan yang ingin disampaikan kepada tim verifikator Bidang Perekonomian dan Perdagangan..." 
                              class="w-full text-xs rounded-2xl bg-[#f0f5f2] focus:bg-white border border-transparent focus:border-[#059669] p-4 focus:ring-2 focus:ring-[#059669]/20 transition-all leading-relaxed">{{ old('catatan_opd', $sub?->catatan_opd) }}</textarea>
                </div>

                {{-- Submit Button --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('opd.tugas.index') }}" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition transform active:scale-95">
                        <span class="material-symbols-outlined text-[16px]">send</span>
                        <span>{{ $sub ? 'Kirim Pembaruan / Revisi Dokumen' : 'Kirim Pengumpulan Dokumen' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- ===== RIGHT PANEL: Status, Deadline, & WA Logs (35%) ===== --}}
    <aside class="w-full lg:w-[35%] shrink-0 border-t lg:border-t-0 lg:border-l border-emerald-100 bg-[#fcfdfc] p-6 flex flex-col gap-6">

        {{-- Status Alert Card --}}
        <div class="p-5 rounded-3xl border shadow-xs
            {{ $tugasOpd->status === 'diterima' ? 'bg-emerald-50/80 border-emerald-200 text-[#064e3b]' : '' }}
            {{ $tugasOpd->status === 'sudah_dikumpulkan' ? 'bg-teal-50/80 border-teal-200 text-teal-950' : '' }}
            {{ $tugasOpd->status === 'perlu_revisi' ? 'bg-amber-50/80 border-amber-200 text-amber-950' : '' }}
            {{ $tugasOpd->status === 'terlambat' ? 'bg-rose-50/80 border-rose-200 text-rose-950' : '' }}
            {{ $tugasOpd->status === 'belum_dikumpulkan' ? 'bg-[#f0f5f2] border-emerald-100 text-slate-800' : '' }}">
            
            <div class="flex items-center gap-3.5 mb-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-lg shrink-0 shadow-2xs border
                    {{ $tugasOpd->status === 'diterima' ? 'bg-emerald-100 text-[#059669] border-emerald-300' : '' }}
                    {{ $tugasOpd->status === 'sudah_dikumpulkan' ? 'bg-teal-100 text-teal-700 border-teal-300' : '' }}
                    {{ $tugasOpd->status === 'perlu_revisi' ? 'bg-amber-100 text-amber-700 border-amber-300' : '' }}
                    {{ $tugasOpd->status === 'terlambat' ? 'bg-rose-100 text-rose-700 border-rose-300' : '' }}
                    {{ $tugasOpd->status === 'belum_dikumpulkan' ? 'bg-white text-slate-600 border-slate-200' : '' }}">
                    <span class="material-symbols-outlined text-[22px]">
                        @if($tugasOpd->status === 'diterima') check_circle 
                        @elseif($tugasOpd->status === 'sudah_dikumpulkan') hourglass_top 
                        @elseif($tugasOpd->status === 'perlu_revisi') error 
                        @elseif($tugasOpd->status === 'terlambat') timer_off 
                        @else pending_actions @endif
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider block opacity-75">Status Pengumpulan:</span>
                    <h3 class="text-sm font-black">
                        {{ $tugasOpd->status_label }}
                    </h3>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-200/60 text-xs flex flex-col gap-1">
                <span class="text-[10px] font-bold uppercase tracking-wider opacity-75">Batas Waktu Deadline:</span>
                <span class="text-xs font-bold">{{ $tugas->deadline->translatedFormat('l, d F Y - H:i') }} WIT</span>
                <span class="text-[11px] font-bold mt-0.5 {{ $isOverdue ? 'text-rose-600' : 'text-[#059669]' }}">
                    {{ $tugas->sisa_waktu_label }}
                </span>
            </div>

            @if($sub && $sub->catatan_admin)
                <div class="mt-3 pt-3 border-t border-amber-200/70 text-xs">
                    <div class="flex items-center gap-1.5 font-bold text-amber-950 mb-1">
                        <span class="material-symbols-outlined text-[15px] text-amber-700">feedback</span>
                        <span>Catatan Masukan PIC:</span>
                    </div>
                    <p class="italic bg-white/90 p-3 rounded-xl border border-amber-200 text-amber-950 text-[11px] leading-relaxed">
                        "{{ $sub->catatan_admin }}"
                    </p>
                </div>
            @endif
        </div>

        {{-- WhatsApp Reminder Logs --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-[#059669]">chat</span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">HISTORI WHATSAPP</h3>
                </div>
            </div>

            <div class="flex flex-col gap-2.5">
                @forelse($waLogs as $log)
                    <div class="p-3.5 rounded-2xl bg-[#f0f5f2] border border-emerald-100 text-xs flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-[#064e3b]">{{ $log->tipe_label }}</span>
                            <span class="text-[10px] text-slate-400">{{ $log->sent_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-slate-700 text-[11px] bg-white p-2.5 rounded-xl border border-slate-100 leading-relaxed italic">
                            "{{ $log->isi_pesan }}"
                        </p>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs rounded-2xl bg-[#f0f5f2] border border-emerald-100">
                        Belum ada notifikasi WhatsApp terkait penugasan ini.
                    </div>
                @endforelse
            </div>
        </div>
    </aside>
</div>
</x-app-layout>
