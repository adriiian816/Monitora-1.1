<x-app-layout>
    @php
        $tugasOpd = $submission->tugasOpd;
        $tugas = $tugasOpd->tugas;
        $opd = $tugasOpd->opd;
    @endphp

    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.tugas.show', $tugas) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Monitoring Tugas: {{ $tugas->judul }}
            </a>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold font-mono px-2.5 py-0.5 rounded bg-slate-200 text-slate-800">{{ $opd->kode_opd }}</span>
                <span class="text-xs font-semibold text-slate-500">{{ $tugas->format_label }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                Verifikasi Berkas Pengumpulan: {{ $opd->nama_opd }}
            </h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Submission Details (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Digital Document Card -->
                @if($tugas->format_pengumpulan === 'digital' || $tugas->format_pengumpulan === 'hybrid')
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                Dokumen Digital yang Diunggah
                            </h2>
                            <span class="text-xs text-slate-400">Softcopy</span>
                        </div>

                        @if($submission->file_path)
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-lg shrink-0">
                                        📄
                                    </div>
                                    <div class="truncate max-w-sm">
                                        <span class="text-sm font-bold text-slate-900 block truncate">
                                            {{ $submission->file_original_name }}
                                        </span>
                                        <span class="text-xs text-slate-500">
                                            Ukuran: {{ $submission->formatted_file_size }}
                                        </span>
                                    </div>
                                </div>
                                <a href="{{ route('admin.submissions.download', $submission) }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow transition shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Unduh Dokumen
                                </a>
                            </div>
                        @else
                            <div class="p-6 text-center text-slate-400 text-xs italic bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                Belum ada dokumen digital yang diunggah oleh OPD ini.
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Physical Delivery Confirmation Card -->
                @if($tugas->format_pengumpulan === 'fisik' || $tugas->format_pengumpulan === 'hybrid')
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                                Bukti Penyerahan Dokumen Fisik
                            </h2>
                            <span class="text-xs text-slate-400">Hardcopy</span>
                        </div>

                        @if($submission->konfirmasi_fisik)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-500 font-semibold block uppercase tracking-wider text-[10px]">Nama Staf Pengantar Berkas:</span>
                                    <span class="text-sm font-bold text-slate-900 mt-1 block">
                                        {{ $submission->nama_pengantar ?: '-' }}
                                    </span>
                                </div>

                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-500 font-semibold block uppercase tracking-wider text-[10px]">Nomor Surat Pengantar / Berita Acara:</span>
                                    <span class="text-sm font-bold text-slate-900 mt-1 block">
                                        {{ $submission->nomor_surat ?: '-' }}
                                    </span>
                                </div>

                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 sm:col-span-2">
                                    <span class="text-slate-500 font-semibold block uppercase tracking-wider text-[10px]">Tanggal Penyerahan Fisik ke Bappeda:</span>
                                    <span class="text-sm font-bold text-slate-900 mt-1 block">
                                        {{ $submission->tanggal_penyerahan_fisik ? $submission->tanggal_penyerahan_fisik->translatedFormat('l, d F Y') : '-' }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="p-6 text-center text-slate-400 text-xs italic bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                OPD belum mengonfirmasi penyerahan berkas fisik.
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Catatan dari PIC OPD -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Catatan dari PIC OPD</h2>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-800 leading-relaxed italic">
                        "{{ $submission->catatan_opd ?: 'Tidak ada catatan khusus dari OPD.' }}"
                    </div>
                    <span class="text-[10px] text-slate-500 block mt-2">
                        Waktu submit: {{ $submission->created_at->translatedFormat('d F Y - H:i') }} WIT
                    </span>
                </div>
            </div>

            <!-- Right: Verification Action Form (1 col) -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sticky top-20">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 pb-2 border-b border-slate-100">
                        Formulir Verifikasi Bappeda
                    </h2>

                    <!-- Current Status Indicator -->
                    <div class="mb-4">
                        <span class="text-[11px] text-slate-500 block mb-1">Status Verifikasi Saat Ini:</span>
                        @if($submission->status_verifikasi === 'diterima')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-900 border border-blue-200">
                                ✓ Diterima & Disetujui
                            </span>
                        @elseif($submission->status_verifikasi === 'perlu_revisi')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                ⚠️ Perlu Revisi / Perbaikan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                ⏳ Menunggu Verifikasi
                            </span>
                        @endif
                    </div>

                    @if($submission->verified_at)
                        <div class="mb-4 p-3 rounded-xl bg-slate-50 text-[11px] text-slate-600 border border-slate-100">
                            Diverifikasi oleh <strong>{{ $submission->verifier?->name ?? 'Admin' }}</strong> pada {{ $submission->verified_at->translatedFormat('d M Y H:i') }} WIT.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.submissions.verify', $submission) }}" class="space-y-4">
                        @csrf

                        <!-- Decision options -->
                        <div>
                            <span class="block text-xs font-bold text-slate-800 mb-2">Keputusan Verifikasi:</span>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-500 hover:bg-blue-50/40 cursor-pointer transition">
                                    <input type="radio" name="status_verifikasi" value="diterima" 
                                           {{ old('status_verifikasi', $submission->status_verifikasi) === 'diterima' ? 'checked' : '' }}
                                           class="text-blue-700 focus:ring-blue-600">
                                    <div>
                                        <span class="block text-xs font-bold text-blue-950">Diterima (Disetujui)</span>
                                        <span class="block text-[11px] text-blue-800">Dokumen telah lengkap dan sesuai ketentuan.</span>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-amber-500 hover:bg-amber-50/40 cursor-pointer transition">
                                    <input type="radio" name="status_verifikasi" value="perlu_revisi"
                                           {{ old('status_verifikasi', $submission->status_verifikasi) === 'perlu_revisi' ? 'checked' : '' }}
                                           class="text-amber-600 focus:ring-amber-500">
                                    <div>
                                        <span class="block text-xs font-bold text-amber-900">Perlu Revisi</span>
                                        <span class="block text-[11px] text-amber-700">Terdapat dokumen kurang atau format belum sesuai.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Catatan Admin -->
                        <div>
                            <label for="catatan_admin" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Catatan / Masukan untuk OPD
                            </label>
                            <textarea id="catatan_admin" 
                                      name="catatan_admin" 
                                      rows="3" 
                                      placeholder="Contoh: Dokumen telah sesuai dan ditandatangani, atau jelaskan bagian apa yang harus diperbaiki..." 
                                      class="w-full text-xs rounded-xl border border-slate-300 p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-600">{{ old('catatan_admin', $submission->catatan_admin) }}</textarea>
                        </div>

                        <!-- Kirim notif WA hasil verifikasi -->
                        <div class="flex items-start gap-2 pt-1">
                            <input type="checkbox" id="send_wa_notification" name="send_wa_notification" value="1" checked class="mt-0.5 text-blue-700 rounded border-slate-300 focus:ring-blue-600">
                            <label for="send_wa_notification" class="text-xs text-slate-700 cursor-pointer">
                                Kirim hasil verifikasi ini via WhatsApp ke Staf OPD
                            </label>
                        </div>

                        <button type="submit" 
                                class="w-full py-2.5 px-4 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs shadow-sm transition">
                            Simpan Keputusan Verifikasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
