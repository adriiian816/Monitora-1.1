<x-app-layout>
    <!-- Top Back Nav -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.tugas.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Tugas
            </a>
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $tugas->status === 'aktif' ? 'bg-blue-100 text-blue-900' : 'bg-slate-100 text-slate-600' }}">
                    {{ ucfirst($tugas->status) }}
                </span>
                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-sky-50 text-sky-800 border border-sky-200">
                    {{ $tugas->format_label }}
                </span>
                <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                    Tahun Anggaran {{ $tugas->tahun_anggaran }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-2">
                {{ $tugas->judul }}
            </h1>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2 flex-wrap shrink-0">
            <a href="{{ route('admin.tugas.ics', $tugas) }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition">
                <span>📅</span> Unduh .ICS
            </a>

            <!-- Open Manual Reminder Modal Button -->
            <button type="button" 
                    onclick="document.getElementById('broadcastModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-700 hover:bg-blue-800 text-white text-xs font-bold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Kirim Pengingat WA
            </button>

            <a href="{{ route('admin.tugas.edit', $tugas) }}" 
               class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                Edit Tugas
            </a>
        </div>
    </div>

    <!-- Description & Deadline Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Petunjuk & Rincian Tugas</h2>
            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                {{ $tugas->deskripsi ?: 'Tidak ada instruksi tambahan.' }}
            </p>
        </div>

        <div class="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 text-white rounded-2xl p-6 shadow-sm flex flex-col justify-between border border-blue-900/60">
            <div>
                <span class="text-[11px] font-semibold tracking-wider text-sky-300 uppercase block mb-1">
                    Batas Waktu Pengumpulan (Deadline)
                </span>
                <div class="text-xl font-black">
                    {{ $tugas->deadline->translatedFormat('l, d F Y') }}
                </div>
                <div class="text-lg font-bold text-slate-300">
                    Pukul {{ $tugas->deadline->format('H:i') }} WIT
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-700/80 flex items-center justify-between text-xs">
                <span class="text-slate-400">Status Waktu:</span>
                @if($tugas->isPastDeadline())
                    <span class="font-bold text-rose-300 bg-rose-500/20 px-2.5 py-0.5 rounded border border-rose-500/30">
                        Deadline Berakhir
                    </span>
                @else
                    <span class="font-bold text-sky-300 bg-sky-500/20 px-2.5 py-0.5 rounded border border-sky-400/30">
                        {{ $tugas->sisa_waktu_label }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Metric Counters Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 text-center">
            <span class="text-[11px] font-semibold text-slate-500 uppercase block">Ditugaskan</span>
            <span class="text-xl font-black text-slate-900">{{ $totalDitugaskan }} OPD</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 text-center">
            <span class="text-[11px] font-semibold text-slate-500 uppercase block">Belum Kumpul</span>
            <span class="text-xl font-black text-slate-600">{{ $totalBelum }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-amber-200 bg-amber-50/40 text-center">
            <span class="text-[11px] font-semibold text-amber-700 uppercase block">Verifikasi</span>
            <span class="text-xl font-black text-amber-800">{{ $totalMenunggu }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-blue-200 bg-blue-50/50 text-center">
            <span class="text-[11px] font-semibold text-blue-800 uppercase block">Diterima</span>
            <span class="text-xl font-black text-blue-800">{{ $totalDiterima }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-rose-200 bg-rose-50/40 text-center">
            <span class="text-[11px] font-semibold text-rose-700 uppercase block">Terlambat</span>
            <span class="text-xl font-black text-rose-700">{{ $totalTerlambat }}</span>
        </div>
    </div>

    <!-- Table of OPD Assignments -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Rincian Status Pengumpulan Seluruh OPD</h2>
                <p class="text-xs text-slate-500">Daftar verifikasi berkas digital dan konfirmasi fisik per instansi</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-700">
                    <tr>
                        <th scope="col" class="px-6 py-4">OPD & Kontak PIC</th>
                        <th scope="col" class="px-6 py-4">Status Pengumpulan</th>
                        <th scope="col" class="px-6 py-4">Dokumen Digital</th>
                        <th scope="col" class="px-6 py-4">Konfirmasi Fisik</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tugas->tugasOpd as $to)
                        @php
                            $sub = $to->submission;
                            $opd = $to->opd;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- OPD info -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $opd->nama_opd }}</div>
                                <div class="text-xs text-slate-500 flex flex-wrap items-center gap-1.5 mt-0.5">
                                    <span class="font-mono text-blue-800 font-bold">{{ $opd->kode_opd }}</span>
                                    @php
                                        $staffList = $opd->users ? $opd->users->where('role', 'opd') : collect();
                                    @endphp
                                    @if($staffList->isNotEmpty())
                                        <span>•</span>
                                        <span class="text-slate-600 font-medium">{{ $staffList->count() }} Staf:</span>
                                        @foreach($staffList as $stf)
                                            @if($stf->no_hp)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $stf->no_hp) }}" 
                                                   target="_blank" 
                                                   title="Hubungi WhatsApp {{ $stf->name }}"
                                                   class="inline-flex items-center gap-1 text-[11px] text-emerald-700 hover:text-emerald-800 font-semibold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                                    📱 {{ $stf->name }}
                                                </a>
                                            @else
                                                <span class="text-[11px] text-slate-500">{{ $stf->name }}</span>
                                            @endif
                                        @endforeach
                                    @elseif(!empty($opd->whatsapp_pic))
                                        <span>•</span>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $opd->whatsapp_pic) }}" 
                                           target="_blank" 
                                           class="text-blue-700 font-semibold hover:underline">
                                            {{ $opd->whatsapp_pic }}
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4">
                                @if($to->status === 'diterima')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-900 border border-blue-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Diterima
                                    </span>
                                @elseif($to->status === 'sudah_dikumpulkan')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Menunggu Verifikasi
                                    </span>
                                @elseif($to->status === 'perlu_revisi')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-900 border border-orange-200">
                                        ⚠️ Perlu Revisi
                                    </span>
                                @elseif($to->status === 'terlambat')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-900 border border-rose-200">
                                        ⚠️ Terlambat
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                        Belum Dikumpulkan
                                    </span>
                                @endif
                            </td>

                            <!-- Digital Document -->
                            <td class="px-6 py-4">
                                @if($sub && $sub->file_path)
                                    <div>
                                        <a href="{{ route('admin.submissions.download', $sub) }}" 
                                           class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-800 hover:text-blue-950 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            Unduh Berkas
                                        </a>
                                        <span class="block text-[10px] text-slate-500 mt-0.5 truncate max-w-[160px]">
                                            {{ $sub->file_original_name }} ({{ $sub->formatted_file_size }})
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Physical Confirmation -->
                            <td class="px-6 py-4">
                                @if($sub && $sub->konfirmasi_fisik)
                                    <div class="text-xs">
                                        <span class="inline-flex items-center gap-1 font-bold text-sky-900 bg-sky-50 px-2 py-0.5 rounded border border-sky-200">
                                            ✓ Penyerahan Fisik
                                        </span>
                                        <span class="block text-[11px] text-slate-700 mt-0.5">
                                            Pengantar: {{ $sub->nama_pengantar ?: '-' }}
                                        </span>
                                        <span class="block text-[10px] text-slate-500">
                                            No Surat: {{ $sub->nomor_surat ?: '-' }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                @if($sub)
                                    <a href="{{ route('admin.submissions.show', $sub) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-900 hover:bg-blue-950 text-white font-semibold text-xs transition">
                                        Verifikasi / Detail
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('admin.tugas.broadcast-reminder', $tugas) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="target_group" value="spesifik">
                                        <input type="hidden" name="spesifik_opd_id" value="{{ $opd->id }}">
                                        <button type="submit" 
                                                class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-800 font-semibold text-xs border border-blue-200 transition">
                                            Ingatkan WA
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section: WhatsApp Logs for this Task -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-base font-bold text-slate-900 mb-1">Riwayat Pengingat WhatsApp Tugas Ini</h2>
        <p class="text-xs text-slate-500 mb-4">Daftar notifikasi tugas baru, pengingat deadline, atau peringatan keterlambatan yang telah dikirim ke PIC OPD</p>

        <div class="divide-y divide-slate-100">
            @forelse($tugas->whatsappLogs as $log)
                <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900">{{ $log->opd ? $log->opd->nama_opd : 'OPD' }}</span>
                            <span class="px-2 py-0.5 rounded-full font-semibold text-[10px] {{ $log->status === 'terkirim' ? 'bg-blue-100 text-blue-900' : 'bg-slate-100 text-slate-700' }}">
                                {{ ucfirst($log->status) }}
                            </span>
                            <span class="text-blue-800 font-semibold">{{ $log->tipe_label }}</span>
                        </div>
                        <p class="text-slate-600 mt-1 italic text-[11px] line-clamp-1">"{{ $log->isi_pesan }}"</p>
                    </div>
                    <span class="text-slate-500 text-[11px] shrink-0">
                        {{ $log->sent_at->translatedFormat('d M Y H:i') }} WIT
                    </span>
                </div>
            @empty
                <div class="py-6 text-center text-slate-400 text-xs">
                    Belum ada riwayat pesan WhatsApp untuk tugas ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Broadcast WhatsApp Reminder -->
    <div id="broadcastModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 relative">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-base font-bold text-slate-900">Kirim Pengingat WhatsApp</h3>
                <button type="button" 
                        onclick="document.getElementById('broadcastModal').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.tugas.broadcast-reminder', $tugas) }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                        Pilih Target Pengingat:
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="target_group" value="belum_kumpul" checked class="text-blue-700 focus:ring-blue-600">
                            <span class="text-xs font-bold text-slate-800">OPD yang Belum Mengumpulkan / Perlu Revisi / Terlambat</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="target_group" value="semua" class="text-blue-700 focus:ring-blue-600">
                            <span class="text-xs font-bold text-slate-800">Semua OPD yang Ditugaskan</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="custom_note" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Catatan Khusus Bappeda (Opsional)
                    </label>
                    <textarea id="custom_note" 
                              name="custom_note" 
                              rows="3" 
                              placeholder="Contoh: Mengingatkan batas penyerahan berkas fisik terakhir hari Jumat di loket bidang perekonomian..." 
                              class="w-full text-xs rounded-xl border border-slate-300 p-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-600"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" 
                            onclick="document.getElementById('broadcastModal').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 rounded-xl bg-blue-700 hover:bg-blue-800 text-white text-xs font-bold shadow-sm transition">
                        Kirim Notifikasi Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
