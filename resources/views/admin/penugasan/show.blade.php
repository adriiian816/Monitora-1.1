<x-app-layout pageTitle="Detail Penugasan Bidang">
<main class="flex-1 p-6 md:p-8 overflow-y-auto bg-[#fafcfb]">
    <div class="max-w-5xl mx-auto flex flex-col gap-6">

        {{-- ===== BACK NAVIGATION & ACTIONS ===== --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.penugasan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#059669] transition mb-2">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Kembali ke Daftar Penugasan</span>
                </a>
                <h1 class="text-xl font-bold text-[#064e3b] tracking-tight">{{ $penugasan->judul }}</h1>
                <p class="text-[13px] text-slate-500 mt-0.5">
                    Diberikan oleh <span class="font-bold text-slate-700">{{ $penugasan->pemberi?->name ?? 'Admin' }}</span> &bull; Dibuat pada {{ $penugasan->created_at->translatedFormat('d F Y, H:i') }} WIT
                </p>
            </div>

            @if(Auth::user()->isAdminOrSuperAdmin())
            <div class="flex items-center gap-2 shrink-0">
                <form method="POST" action="{{ route('admin.penugasan.destroy', $penugasan) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus penugasan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold hover:bg-rose-100 transition shadow-xs">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        <span>Hapus Penugasan</span>
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- ===== ALERT NOTIFICATIONS ===== --}}
        @if(session('success'))
            <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl px-5 py-3.5 text-xs font-semibold shadow-xs">
                <span class="material-symbols-outlined text-emerald-600 text-[20px] shrink-0">check_circle</span>
                <div class="mt-0.5">{{ session('success') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="flex items-start gap-3 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl px-5 py-3.5 text-xs font-semibold shadow-xs">
                <span class="material-symbols-outlined text-rose-600 text-[20px] shrink-0">error</span>
                <div class="mt-0.5">
                    <div class="font-bold mb-1">Terjadi kesalahan pada input:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ===== DETAILS CARD ===== --}}
        <div class="bg-white rounded-3xl border border-emerald-100/80 shadow-xs p-6 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-6 border-b border-slate-100">
                <!-- Tanggal Kegiatan -->
                <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#064e3b] block mb-1">Tanggal Kegiatan</span>
                    <div class="text-sm font-bold text-[#064e3b] flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] text-[#059669]">event</span>
                        {{ $penugasan->tanggal_kegiatan->translatedFormat('l, d F Y') }}
                    </div>
                    @if($penugasan->isKegiatanPassed())
                        <span class="inline-block mt-1 text-[10px] font-semibold text-slate-400">Kegiatan telah lewat</span>
                    @else
                        <span class="inline-block mt-1 text-[10px] font-bold text-[#059669]">Mendatang</span>
                    @endif
                </div>

                <!-- Batas Laporan -->
                <div class="p-4 rounded-2xl {{ $penugasan->isBatasLaporanPassed() ? 'bg-rose-50/60 border border-rose-200' : 'bg-[#f0f5f2] border border-emerald-100/60' }}">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600 block mb-1">Batas Akhir Pelaporan</span>
                    <div class="text-sm font-bold {{ $penugasan->isBatasLaporanPassed() ? 'text-rose-950' : 'text-slate-800' }} flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] {{ $penugasan->isBatasLaporanPassed() ? 'text-rose-600' : 'text-slate-500' }}">alarm</span>
                        {{ $penugasan->batas_laporan ? $penugasan->batas_laporan->translatedFormat('l, d F Y') : 'Tidak ditentukan' }}
                    </div>
                    @if($penugasan->isBatasLaporanPassed())
                        <span class="inline-block mt-1 text-[10px] font-bold text-rose-600">Batas waktu telah berakhir</span>
                    @elseif($penugasan->batas_laporan)
                        <span class="inline-block mt-1 text-[10px] font-bold text-[#059669]">Tenggat masih berlaku</span>
                    @endif
                </div>

                <!-- Jumlah Anggota -->
                <div class="p-4 rounded-2xl bg-teal-50/50 border border-teal-100">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-teal-800 block mb-1">Penerima Tugas</span>
                    <div class="text-sm font-bold text-teal-950 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] text-teal-600">groups</span>
                        {{ $penugasan->penerima->count() }} Anggota Internal Bidang
                    </div>
                    <span class="inline-block mt-1 text-[10px] font-semibold text-teal-700">Bidang Ekop</span>
                </div>
            </div>

            <!-- Deskripsi & Catatan -->
            <div class="space-y-4">
                @if($penugasan->deskripsi)
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Deskripsi / Uraian Tugas:</h4>
                        <div class="text-sm text-slate-700 bg-[#f7faf8] p-4 rounded-2xl border border-emerald-100/70 whitespace-pre-line leading-relaxed">
                            {{ $penugasan->deskripsi }}
                        </div>
                    </div>
                @endif

                @if($penugasan->catatan)
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Catatan Tambahan:</h4>
                        <div class="text-xs text-amber-900 bg-amber-50 p-3.5 rounded-2xl border border-amber-200 leading-relaxed">
                            {{ $penugasan->catatan }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ===== PANEL AKSI TUGAS & LAPORAN SAYA (KHUSUS PENERIMA TUGAS) ===== --}}
        @if($myAssignment)
            @php
                $myPivot = $myAssignment->pivot;
                $isKonfirmasi = $myPivot->status_konfirmasi;
                $isLaporan = $myPivot->status_laporan;
            @endphp
            <div class="bg-white rounded-3xl border-2 border-emerald-500/25 shadow-md p-6 sm:p-7 space-y-6 bg-gradient-to-br from-white via-white to-emerald-50/20"
                 x-data="{ showDeclineForm: false, editingReport: {{ $errors->any() ? 'true' : 'false' }} }">
                
                {{-- Header Panel Saya --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-emerald-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-[#059669] flex items-center justify-center font-bold shadow-xs">
                            <span class="material-symbols-outlined text-[24px]">assignment_turned_in</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-extrabold text-[#064e3b]">Status &amp; Pelaporan Tugas Saya</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#059669] text-white">
                                    {{ Auth::user()->name }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Konfirmasikan penerimaan penugasan dan serahkan laporan hasil pelaksanaan kegiatan</p>
                        </div>
                    </div>
                </div>

                {{-- Split: 1. Konfirmasi Tugas & 2. Pengumpulan Laporan --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- ===== LANGKAH 1: KONFIRMASI PENUGASAN ===== --}}
                    <div class="flex flex-col justify-between p-5 rounded-2xl {{ $isKonfirmasi === 'dikonfirmasi' ? 'bg-emerald-50/70 border border-emerald-200' : ($isKonfirmasi === 'ditolak' ? 'bg-rose-50/70 border border-rose-200' : 'bg-amber-50/70 border border-amber-200') }}">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">1. Konfirmasi Kehadiran / Kesiapan</span>
                                @if($isKonfirmasi === 'dikonfirmasi')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-[#059669] text-white shadow-xs">
                                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                        Dikonfirmasi Siap
                                    </span>
                                @elseif($isKonfirmasi === 'ditolak')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-600 text-white shadow-xs">
                                        <span class="material-symbols-outlined text-[14px]">cancel</span>
                                        Berhalangan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-xs">
                                        <span class="material-symbols-outlined text-[14px]">timelapse</span>
                                        Menunggu Respon
                                    </span>
                                @endif
                            </div>

                            @if($isKonfirmasi === 'dikonfirmasi')
                                <p class="text-xs text-emerald-950 font-medium leading-relaxed mt-2">
                                    Terima kasih! Anda telah mengonfirmasi kesiapan melaksanakan tugas ini.
                                </p>
                                @if($myPivot->catatan_konfirmasi)
                                    <div class="mt-2.5 p-3 rounded-xl bg-white/90 border border-emerald-200 text-xs text-slate-700 italic">
                                        "{{ $myPivot->catatan_konfirmasi }}"
                                    </div>
                                @endif
                            @elseif($isKonfirmasi === 'ditolak')
                                <p class="text-xs text-rose-950 font-medium leading-relaxed mt-2">
                                    Anda menyatakan berhalangan / menolak penugasan ini.
                                </p>
                                @if($myPivot->catatan_konfirmasi)
                                    <div class="mt-2.5 p-3 rounded-xl bg-white/90 border border-rose-200 text-xs text-slate-700 italic">
                                        Alasan: "{{ $myPivot->catatan_konfirmasi }}"
                                    </div>
                                @endif
                            @else
                                <p class="text-xs text-amber-950 font-medium leading-relaxed mt-2">
                                    Silakan konfirmasikan kesiapan Anda untuk melaksanakan penugasan ini agar pimpinan dan tim Bappeda mengetahui status pelaksanaan.
                                </p>
                            @endif
                        </div>

                        {{-- Action Buttons for Confirmation --}}
                        <div class="mt-4 pt-3 border-t {{ $isKonfirmasi === 'dikonfirmasi' ? 'border-emerald-200' : ($isKonfirmasi === 'ditolak' ? 'border-rose-200' : 'border-amber-200') }}">
                            @if($isKonfirmasi === 'menunggu_konfirmasi' || empty($isKonfirmasi))
                                <div class="flex flex-col gap-2">
                                    <form method="POST" action="{{ route('admin.penugasan.konfirmasi', $penugasan) }}">
                                        @csrf
                                        <input type="hidden" name="status_konfirmasi" value="dikonfirmasi">
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white text-xs font-bold shadow-md shadow-emerald-700/20 transition transform active:scale-[0.99]">
                                            <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                            <span>Konfirmasi Siap Melaksanakan</span>
                                        </button>
                                    </form>

                                    <button type="button" @click="showDeclineForm = !showDeclineForm" class="w-full inline-flex items-center justify-center gap-1 text-xs text-slate-500 hover:text-rose-600 font-semibold py-1">
                                        <span class="material-symbols-outlined text-[14px]">cancel</span>
                                        <span>Berhalangan / Tidak Bisa Hadir</span>
                                    </button>
                                </div>
                            @else
                                <div class="flex items-center justify-between text-[11px] text-slate-500">
                                    <span>Direspon: {{ $myPivot->dikonfirmasi_pada?->translatedFormat('d M Y, H:i') ?? '-' }} WIT</span>
                                    <button type="button" @click="showDeclineForm = !showDeclineForm" class="text-xs font-bold text-[#059669] hover:underline">
                                        Ubah Konfirmasi
                                    </button>
                                </div>
                            @endif

                            {{-- Collapsible Respon Form (Alasan Berhalangan / Ubah Respon) --}}
                            <div x-show="showDeclineForm" x-cloak class="mt-3 p-3.5 bg-white rounded-2xl border border-slate-200 shadow-xs space-y-2.5">
                                <form method="POST" action="{{ route('admin.penugasan.konfirmasi', $penugasan) }}" class="space-y-2">
                                    @csrf
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Pilih Status Respon:</label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <label class="flex items-center gap-1.5 p-2 rounded-xl border border-slate-200 text-xs cursor-pointer hover:bg-emerald-50">
                                                <input type="radio" name="status_konfirmasi" value="dikonfirmasi" class="text-[#059669] focus:ring-[#059669]" {{ $isKonfirmasi === 'dikonfirmasi' ? 'checked' : '' }}>
                                                <span class="font-semibold text-emerald-800">Siap Laksanakan</span>
                                            </label>
                                            <label class="flex items-center gap-1.5 p-2 rounded-xl border border-slate-200 text-xs cursor-pointer hover:bg-rose-50">
                                                <input type="radio" name="status_konfirmasi" value="ditolak" class="text-rose-600 focus:ring-rose-500" {{ $isKonfirmasi === 'ditolak' ? 'checked' : '' }}>
                                                <span class="font-semibold text-rose-800">Berhalangan</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Catatan / Alasan (Opsional):</label>
                                        <textarea name="catatan_konfirmasi" rows="2" placeholder="Tuliskan keterangan kesiapan atau alasan berhalangan..." class="w-full text-xs p-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#059669]/20">{{ $myPivot->catatan_konfirmasi }}</textarea>
                                    </div>
                                    <div class="flex items-center gap-2 justify-end">
                                        <button type="button" @click="showDeclineForm = false" class="px-3 py-1 rounded-full text-xs font-semibold text-slate-500 hover:bg-slate-100">
                                            Tutup
                                        </button>
                                        <button type="submit" class="px-4 py-1.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white text-xs font-bold shadow-xs">
                                            Kirim Respon
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- ===== LANGKAH 2: PENGUMPULAN LAPORAN HASIL KEGIATAN ===== --}}
                    <div class="flex flex-col justify-between p-5 rounded-2xl bg-[#f8fbf9] border border-emerald-200/80">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">2. Pengumpulan Laporan Kegiatan</span>
                                @if($isLaporan === 'sudah_dilaporkan')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-[#059669] text-white shadow-xs">
                                        <span class="material-symbols-outlined text-[14px]">task</span>
                                        Laporan Sudah Masuk
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="material-symbols-outlined text-[14px]">timelapse</span>
                                        Belum Dilaporkan
                                    </span>
                                @endif
                            </div>

                            {{-- Jika Laporan Sudah Dikumpulkan & Tidak Sedang Diedit --}}
                            @if($isLaporan === 'sudah_dilaporkan')
                                <div x-show="!editingReport" class="space-y-3 mt-3">
                                    <div class="p-3.5 rounded-xl bg-white border border-emerald-100 text-xs space-y-2 shadow-2xs">
                                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ringkasan Pelaksanaan:</div>
                                        <div class="text-slate-800 leading-relaxed whitespace-pre-line">{{ $myPivot->isi_laporan }}</div>
                                    </div>

                                    @if($myPivot->file_laporan)
                                        <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-emerald-100 shadow-2xs">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="material-symbols-outlined text-emerald-600 text-[24px]">description</span>
                                                <div class="truncate">
                                                    <div class="text-xs font-bold text-slate-800 truncate" title="{{ $myPivot->file_laporan_name }}">
                                                        {{ $myPivot->file_laporan_name }}
                                                    </div>
                                                    <div class="text-[10px] text-slate-400">{{ $myPivot->formatted_file_size }}</div>
                                                </div>
                                            </div>
                                            <a href="{{ route('admin.penugasan.download-laporan', [$penugasan, Auth::user()]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white text-xs font-bold shadow-xs shrink-0 transition">
                                                <span class="material-symbols-outlined text-[14px]">download</span>
                                                <span>Unduh</span>
                                            </a>
                                        </div>
                                    @endif

                                    @if($myPivot->link_dokumen)
                                        <div class="p-2.5 rounded-xl bg-white border border-emerald-100 flex items-center justify-between shadow-2xs">
                                            <div class="flex items-center gap-1.5 text-xs text-[#059669] font-semibold truncate">
                                                <span class="material-symbols-outlined text-[16px]">link</span>
                                                <span class="truncate">{{ $myPivot->link_dokumen }}</span>
                                            </div>
                                            <a href="{{ $myPivot->link_dokumen }}" target="_blank" class="text-xs font-bold text-[#059669] hover:underline shrink-0 ml-2">
                                                Buka &rarr;
                                            </a>
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1">
                                        <span>Dikumpulkan pada: {{ $myPivot->dilaporkan_pada?->translatedFormat('d M Y, H:i') ?? '-' }} WIT</span>
                                        <button type="button" @click="editingReport = true" class="text-xs font-bold text-[#059669] hover:underline flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[14px]">edit</span>
                                            <span>Perbarui Laporan</span>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            {{-- Form Input / Update Laporan --}}
                            <div x-show="editingReport || '{{ $isLaporan }}' === 'belum_dilaporkan'" class="mt-3">
                                <form method="POST" action="{{ route('admin.penugasan.submit-laporan', $penugasan) }}" enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="penerima_id" value="{{ $myAssignment->id }}">
                                    <div>
                                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                            Ringkasan / Uraian Pelaksanaan Kegiatan <span class="text-slate-400 font-normal lowercase">(opsional jika melampirkan berkas)</span>
                                        </label>
                                        <textarea name="isi_laporan" rows="3" placeholder="Tuliskan uraian hasil kegiatan, notulensi rapat, kesimpulan, atau rekomendasi..." class="w-full text-xs p-2.5 rounded-xl border border-emerald-200 bg-white focus:ring-2 focus:ring-[#059669]/20 leading-relaxed">{{ old('isi_laporan', $myPivot->isi_laporan) }}</textarea>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                            Unggah Berkas Laporan <span class="text-slate-400 font-normal lowercase">(PDF, Word, Excel, ZIP maks 25MB)</span>
                                        </label>
                                        <input type="file" name="file_laporan" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-[#059669] hover:file:bg-emerald-100 cursor-pointer">
                                        @if($myPivot->file_laporan_name)
                                            <p class="text-[10px] text-slate-500 mt-1">Berkas tersimpan: <strong class="text-slate-700">{{ $myPivot->file_laporan_name }}</strong> (biarkan kosong jika tidak diganti)</p>
                                        @endif
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                            Link Berkas Tambahan / Google Drive <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                                        </label>
                                        <input type="url" name="link_dokumen" value="{{ old('link_dokumen', $myPivot->link_dokumen) }}" placeholder="https://drive.google.com/..." class="w-full text-xs p-2 rounded-xl border border-emerald-200 bg-white focus:ring-2 focus:ring-[#059669]/20">
                                    </div>

                                    <div class="flex items-center gap-2 pt-1">
                                        <input type="checkbox" id="tandai_selesai" name="tandai_selesai" value="1" checked class="rounded text-[#059669] focus:ring-[#059669]/20">
                                        <label for="tandai_selesai" class="text-xs text-slate-600 font-medium cursor-pointer">
                                            Tandai tugas ini sebagai <strong>Selesai</strong> setelah laporan dikirim
                                        </label>
                                    </div>

                                    <div class="flex items-center gap-2 pt-2">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#059669] hover:bg-[#047857] text-white text-xs font-bold shadow-md shadow-emerald-700/20 transition">
                                            <span class="material-symbols-outlined text-[16px]">upload_file</span>
                                            <span>{{ $isLaporan === 'sudah_dilaporkan' ? 'Simpan Perubahan Laporan' : 'Kumpulkan Laporan' }}</span>
                                        </button>
                                        @if($isLaporan === 'sudah_dilaporkan')
                                            <button type="button" @click="editingReport = false" class="px-3 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition">
                                                Batal
                                            </button>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ===== TABEL STATUS PENERIMA ANGGOTA BIDANG ===== --}}
        <div class="bg-white rounded-3xl border border-emerald-100/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-emerald-100/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-[#f7faf8]">
                <div>
                    <h2 class="text-base font-bold text-[#064e3b]">Status Tugas &amp; Laporan Anggota Bidang</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola konfirmasi kehadiran, progres pelaksanaan, dan pengumpulan laporan dari masing-masing anggota</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        {{ $penugasan->penerima->where('pivot.status_konfirmasi', 'dikonfirmasi')->count() }} / {{ $penugasan->penerima->count() }} Dikonfirmasi
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-[#059669] border border-emerald-200">
                        {{ $penugasan->penerima->where('pivot.status_penugasan', 'selesai')->count() }} / {{ $penugasan->penerima->count() }} Selesai
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
                        {{ $penugasan->penerima->where('pivot.status_laporan', 'sudah_dilaporkan')->count() }} / {{ $penugasan->penerima->count() }} Laporan Masuk
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-[#f7faf8] border-b border-emerald-100/70 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <tr>
                            <th scope="col" class="px-5 py-4">Penerima (Staf/Anggota)</th>
                            <th scope="col" class="px-4 py-4">WhatsApp &amp; Kontak</th>
                            <th scope="col" class="px-4 py-4">Status Konfirmasi</th>
                            <th scope="col" class="px-4 py-4">Status Penugasan</th>
                            <th scope="col" class="px-5 py-4">Laporan &amp; Berkas</th>
                            <th scope="col" class="px-4 py-4 text-right">Ubah Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($penugasan->penerima as $anggota)
                            @php
                                $statusKonf = $anggota->pivot->status_konfirmasi;
                                $statusTugas = $anggota->pivot->status_penugasan;
                                $statusLap = $anggota->pivot->status_laporan;
                            @endphp
                            <tr class="hover:bg-[#f0f8f4] transition-colors" x-data="{ showUraian: false, showAdminReportForm: false }">
                                <!-- Penerima -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-[#064e3b] font-bold text-xs flex items-center justify-center shrink-0 border border-emerald-200">
                                            {{ strtoupper(substr($anggota->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 leading-tight">
                                                {{ $anggota->name }}
                                                @if($anggota->id === Auth::id())
                                                    <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">Saya</span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-slate-400">{{ $anggota->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- WhatsApp -->
                                <td class="px-4 py-4">
                                    @if($anggota->no_hp)
                                        @php
                                            $waPhone = preg_replace('/[^0-9]/', '', $anggota->no_hp);
                                            if (str_starts_with($waPhone, '0')) {
                                                $waPhone = '62'.substr($waPhone, 1);
                                            }
                                        @endphp
                                        <a href="https://wa.me/{{ $waPhone }}" 
                                           target="_blank" 
                                           class="inline-flex items-center gap-1 text-[11px] text-[#059669] font-semibold hover:underline bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                            <span class="material-symbols-outlined text-[13px]">chat</span>
                                            {{ $anggota->no_hp }}
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Belum ada No. WA</span>
                                    @endif
                                </td>

                                <!-- Status Konfirmasi -->
                                <td class="px-4 py-4">
                                    @if($statusKonf === 'dikonfirmasi')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-[#059669] border border-emerald-200" title="{{ $anggota->pivot->catatan_konfirmasi ?: 'Telah dikonfirmasi siap' }}">
                                            <span class="material-symbols-outlined text-[14px]">check</span>
                                            Dikonfirmasi
                                        </span>
                                        @if($anggota->pivot->dikonfirmasi_pada)
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                {{ $anggota->pivot->dikonfirmasi_pada->translatedFormat('d/m/y H:i') }}
                                            </div>
                                        @endif
                                    @elseif($statusKonf === 'ditolak')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200" title="{{ $anggota->pivot->catatan_konfirmasi ?: 'Berhalangan' }}">
                                            <span class="material-symbols-outlined text-[14px]">close</span>
                                            Berhalangan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            <span class="material-symbols-outlined text-[14px]">timelapse</span>
                                            Menunggu
                                        </span>
                                    @endif
                                </td>

                                <!-- Status Penugasan -->
                                <td class="px-4 py-4">
                                    @if($statusTugas === 'selesai')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-[#059669] border border-emerald-200">
                                            <span class="material-symbols-outlined text-[14px]">check</span>
                                            Selesai
                                        </span>
                                    @elseif($statusTugas === 'sedang_dikerjakan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                            <span class="material-symbols-outlined text-[14px] animate-spin">sync</span>
                                            Sedang Dikerjakan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Belum Dikerjakan
                                        </span>
                                    @endif
                                </td>

                                <!-- Status Laporan & Berkas -->
                                <td class="px-5 py-4">
                                    @if($statusLap === 'sudah_dilaporkan')
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-[#059669] border border-emerald-200">
                                                    <span class="material-symbols-outlined text-[13px]">task</span>
                                                    Sudah Lapor
                                                </span>

                                                @if($anggota->pivot->file_laporan)
                                                    <a href="{{ route('admin.penugasan.download-laporan', [$penugasan, $anggota]) }}" 
                                                       class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#059669] hover:bg-[#047857] text-white text-[11px] font-bold shadow-xs transition" 
                                                       title="Unduh {{ $anggota->pivot->file_laporan_name }}">
                                                        <span class="material-symbols-outlined text-[13px]">download</span>
                                                        <span>Berkas ({{ $anggota->pivot->formatted_file_size }})</span>
                                                    </a>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-2 text-[11px]">
                                                @if($anggota->pivot->isi_laporan)
                                                    <button type="button" @click="showUraian = !showUraian" class="text-xs text-[#059669] font-bold hover:underline inline-flex items-center gap-0.5">
                                                        <span class="material-symbols-outlined text-[13px]">notes</span>
                                                        <span x-text="showUraian ? 'Tutup Uraian' : 'Lihat Ringkasan'">Lihat Ringkasan</span>
                                                    </button>
                                                @endif

                                                @if($anggota->pivot->link_dokumen)
                                                    <a href="{{ $anggota->pivot->link_dokumen }}" target="_blank" class="text-xs text-indigo-700 font-semibold hover:underline inline-flex items-center gap-0.5">
                                                        <span class="material-symbols-outlined text-[13px]">link</span>
                                                        <span>Link</span>
                                                    </a>
                                                @endif

                                                @if(Auth::user()->isAdminOrSuperAdmin())
                                                    <button type="button" @click="showAdminReportForm = !showAdminReportForm" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-900 inline-flex items-center gap-0.5 hover:underline">
                                                        <span class="material-symbols-outlined text-[13px]">edit_document</span>
                                                        <span x-text="showAdminReportForm ? 'Tutup' : 'Ubah Laporan'"></span>
                                                    </button>
                                                @endif
                                            </div>

                                            {{-- Expandable Ringkasan Laporan --}}
                                            <div x-show="showUraian" x-cloak class="mt-1 p-2.5 bg-white rounded-xl border border-emerald-200 text-xs text-slate-700 whitespace-pre-line shadow-2xs">
                                                <strong class="text-[#064e3b] block mb-1">Ringkasan Laporan:</strong>
                                                {{ $anggota->pivot->isi_laporan }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex flex-col gap-1 items-start">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                <span class="material-symbols-outlined text-[14px]">timelapse</span>
                                                Belum Dilaporkan
                                            </span>
                                            @if(Auth::user()->isAdminOrSuperAdmin())
                                                <button type="button" @click="showAdminReportForm = !showAdminReportForm" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:text-emerald-900 hover:underline mt-0.5">
                                                    <span class="material-symbols-outlined text-[13px]">upload_file</span>
                                                    <span x-text="showAdminReportForm ? 'Tutup Form' : 'Input Laporan'">Input Laporan</span>
                                                </button>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Admin Report Submission / Edit Form in Table --}}
                                    @if(Auth::user()->isAdminOrSuperAdmin())
                                        <div x-show="showAdminReportForm" x-cloak class="mt-2.5 p-3 bg-white rounded-2xl border border-emerald-200 shadow-sm space-y-2 text-left w-72 max-w-sm">
                                            <div class="text-[11px] font-bold text-[#064e3b] flex items-center justify-between pb-1 border-b border-slate-100">
                                                <span>Input Laporan: {{ Str::limit($anggota->name, 18) }}</span>
                                                <button type="button" @click="showAdminReportForm = false" class="text-slate-400 hover:text-slate-600 text-xs font-bold">&times;</button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.penugasan.submit-laporan', $penugasan) }}" enctype="multipart/form-data" class="space-y-2">
                                                @csrf
                                                <input type="hidden" name="penerima_id" value="{{ $anggota->id }}">
                                                <div>
                                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-0.5">Ringkasan / Uraian</label>
                                                    <textarea name="isi_laporan" rows="2" placeholder="Uraian hasil kegiatan..." class="w-full text-xs p-1.5 rounded-xl border border-slate-200 focus:ring-1 focus:ring-[#059669]">{{ $anggota->pivot->isi_laporan }}</textarea>
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-0.5">Unggah Berkas Laporan</label>
                                                    <input type="file" name="file_laporan" class="w-full text-[10px] text-slate-500 file:mr-1.5 file:py-0.5 file:px-2 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-emerald-50 file:text-[#059669]">
                                                    @if($anggota->pivot->file_laporan_name)
                                                        <p class="text-[9px] text-slate-400 mt-0.5 truncate">Tersimpan: {{ $anggota->pivot->file_laporan_name }}</p>
                                                    @endif
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-0.5">Link Dokumen (Opsional)</label>
                                                    <input type="url" name="link_dokumen" value="{{ $anggota->pivot->link_dokumen }}" placeholder="https://drive.google.com/..." class="w-full text-xs p-1 rounded-xl border border-slate-200 focus:ring-1 focus:ring-[#059669]">
                                                </div>
                                                <div class="flex items-center gap-1.5 pt-0.5">
                                                    <input type="checkbox" id="tandai_selesai_{{ $anggota->id }}" name="tandai_selesai" value="1" checked class="rounded text-[#059669] focus:ring-[#059669]/20 text-xs">
                                                    <label for="tandai_selesai_{{ $anggota->id }}" class="text-[10px] text-slate-600 font-medium">Tandai status <strong>Selesai</strong></label>
                                                </div>
                                                <div class="flex items-center gap-1.5 pt-1">
                                                    <button type="submit" class="px-2.5 py-1 rounded-full bg-[#059669] hover:bg-[#047857] text-white text-[10px] font-bold shadow-xs">
                                                        Simpan Laporan
                                                    </button>
                                                    <button type="button" @click="showAdminReportForm = false" class="px-2 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 text-[10px] font-semibold">
                                                        Batal
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </td>

                                <!-- Quick Update Form -->
                                <td class="px-4 py-4 text-right">
                                    @if(Auth::user()->isAdminOrSuperAdmin() || $anggota->id === Auth::id())
                                        <div class="inline-block text-left">
                                            <form method="POST" action="{{ route('admin.penugasan.update-status', $penugasan) }}" class="flex items-center justify-end gap-1.5 flex-wrap">
                                                @csrf
                                                <input type="hidden" name="penerima_id" value="{{ $anggota->id }}">

                                                <select name="status_konfirmasi" class="text-[11px] py-1 px-2 rounded-full border border-emerald-100 bg-[#f0f5f2] font-medium" title="Ubah Konfirmasi">
                                                    <option value="menunggu_konfirmasi" {{ $statusKonf === 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu</option>
                                                    <option value="dikonfirmasi" {{ $statusKonf === 'dikonfirmasi' ? 'selected' : '' }}>Konfirmasi</option>
                                                    <option value="ditolak" {{ $statusKonf === 'ditolak' ? 'selected' : '' }}>Berhalangan</option>
                                                </select>

                                                <select name="status_penugasan" class="text-[11px] py-1 px-2 rounded-full border border-emerald-100 bg-[#f0f5f2] font-medium" title="Ubah Status Tugas">
                                                    <option value="belum_dikerjakan" {{ $statusTugas === 'belum_dikerjakan' ? 'selected' : '' }}>Belum Mulai</option>
                                                    <option value="sedang_dikerjakan" {{ $statusTugas === 'sedang_dikerjakan' ? 'selected' : '' }}>Proses</option>
                                                    <option value="selesai" {{ $statusTugas === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                                </select>

                                                <select name="status_laporan" class="text-[11px] py-1 px-2 rounded-full border border-emerald-100 bg-[#f0f5f2] font-medium" title="Ubah Status Laporan">
                                                    <option value="belum_dilaporkan" {{ $statusLap === 'belum_dilaporkan' ? 'selected' : '' }}>Belum Lapor</option>
                                                    <option value="sudah_dilaporkan" {{ $statusLap === 'sudah_dilaporkan' ? 'selected' : '' }}>Sudah Lapor</option>
                                                </select>

                                                <button type="submit" class="px-2.5 py-1 rounded-full bg-[#059669] hover:bg-[#047857] text-white font-bold text-[11px] transition shadow-xs">
                                                    Simpan
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Hanya dapat diubah oleh ybs / Admin</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                    Belum ada anggota yang ditugaskan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
</x-app-layout>
