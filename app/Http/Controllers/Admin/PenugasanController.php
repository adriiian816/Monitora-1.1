<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penugasan;
use App\Models\PenugasanPenerima;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PenugasanController extends Controller
{
    /**
     * Display a listing of all penugasan.
     */
    public function index(Request $request): View
    {
        $query = Penugasan::with(['pemberi', 'penerima'])
            ->withCount('penerima');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhereHas('penerima', function ($pQ) use ($search) {
                        $pQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $statusFilter = $request->status;
            $query->whereHas('penerima', function ($q) use ($statusFilter) {
                $q->where('penugasan_penerima.status_penugasan', $statusFilter);
            });
        }

        if ($request->filled('status_laporan')) {
            $laporanFilter = $request->status_laporan;
            $query->whereHas('penerima', function ($q) use ($laporanFilter) {
                $q->where('penugasan_penerima.status_laporan', $laporanFilter);
            });
        }

        $penugasanList = $query->latest('tanggal_kegiatan')->paginate(15)->withQueryString();

        // Metrics for summary cards
        $totalPenugasan = Penugasan::count();
        $totalSelesai = DB::table('penugasan_penerima')->where('status_penugasan', 'selesai')->count();
        $totalSedang = DB::table('penugasan_penerima')->where('status_penugasan', 'sedang_dikerjakan')->count();
        $totalLaporanSudah = DB::table('penugasan_penerima')->where('status_laporan', 'sudah_dilaporkan')->count();
        $totalLaporanBelum = DB::table('penugasan_penerima')->where('status_laporan', 'belum_dilaporkan')->count();

        return view('admin.penugasan.index', compact(
            'penugasanList',
            'totalPenugasan',
            'totalSelesai',
            'totalSedang',
            'totalLaporanSudah',
            'totalLaporanBelum'
        ));
    }

    /**
     * Show the form for creating a new penugasan.
     */
    public function create(Request $request): View
    {
        if (! $request->user()->isAdminOrSuperAdmin()) {
            abort(403, 'Akses tidak diizinkan. Hanya Admin dan Super Admin yang dapat membuat penugasan bidang.');
        }

        $anggotaList = User::where(function ($q) {
            $q->where('bidang', 'ekop')
                ->orWhere(function ($sq) {
                    $sq->whereIn('role', ['admin', 'super_admin'])
                        ->whereNull('opd_id');
                });
        })->orderBy('name')->get();

        return view('admin.penugasan.create', compact('anggotaList'));
    }

    /**
     * Store a newly created penugasan in storage.
     */
    public function store(Request $request, WhatsAppService $whatsAppService): RedirectResponse
    {
        if (! $request->user()->isAdminOrSuperAdmin()) {
            abort(403, 'Akses tidak diizinkan. Hanya Admin dan Super Admin yang dapat membuat penugasan bidang.');
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'tanggal_kegiatan' => ['required', 'date'],
            'batas_laporan' => ['nullable', 'date', 'after_or_equal:tanggal_kegiatan'],
            'catatan' => ['nullable', 'string'],
            'penerima_ids' => ['required', 'array', 'min:1'],
            'penerima_ids.*' => ['exists:users,id'],
        ]);

        $penugasan = Penugasan::create([
            'pemberi_id' => $request->user()->id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
            'batas_laporan' => $validated['batas_laporan'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Validate penerima are internal Bappeda / Bidang Ekop members
        $penerimaUsers = User::whereIn('id', $validated['penerima_ids'])
            ->where(function ($q) {
                $q->where('bidang', 'ekop')
                    ->orWhereIn('role', ['admin', 'super_admin']);
            })
            ->get();

        foreach ($penerimaUsers as $anggota) {
            $penugasan->penerima()->attach($anggota->id, [
                'status_penugasan' => 'belum_dikerjakan',
                'status_laporan' => 'belum_dilaporkan',
            ]);
        }

        // Send WA notifications
        $penugasan->load('pemberi');
        $waCount = 0;
        foreach ($penerimaUsers as $anggota) {
            $log = $whatsAppService->sendPenugasanToAnggota($penugasan, $anggota);
            if ($log) {
                $waCount++;
            }
        }

        $msg = "Penugasan '{$penugasan->judul}' berhasil dibuat untuk {$penerimaUsers->count()} anggota.";
        if ($waCount > 0) {
            $msg .= " ({$waCount} notifikasi WhatsApp terkirim).";
        }

        return redirect()->route('admin.penugasan.show', $penugasan)
            ->with('success', $msg);
    }

    /**
     * Display the specified penugasan with per-anggota status table.
     */
    public function show(Request $request, Penugasan $penugasan): View
    {
        $penugasan->load(['pemberi', 'penerima']);
        $currentUser = $request->user();
        $myAssignment = $penugasan->penerima->firstWhere('id', $currentUser->id);

        return view('admin.penugasan.show', compact('penugasan', 'myAssignment'));
    }

    /**
     * Confirm or decline penugasan by assigned anggota bidang.
     */
    public function konfirmasi(Request $request, Penugasan $penugasan, WhatsAppService $whatsAppService): RedirectResponse
    {
        $user = $request->user();

        // If user is anggota bidang, they can only confirm for themselves
        $penerimaId = $user->isAnggotaBidang() ? $user->id : ($request->input('penerima_id') ?? $user->id);

        $validated = $request->validate([
            'penerima_id' => ['nullable', 'exists:users,id'],
            'status_konfirmasi' => ['required', 'in:dikonfirmasi,ditolak'],
            'catatan_konfirmasi' => ['nullable', 'string', 'max:1000'],
        ]);

        $anggota = $penugasan->penerima()->where('users.id', $penerimaId)->first();
        if (! $anggota) {
            abort(404, 'Data penerima penugasan tidak ditemukan.');
        }

        $currentStatusPenugasan = $anggota->pivot->status_penugasan;
        $newStatusPenugasan = $currentStatusPenugasan;

        // If confirmed and currently belum_dikerjakan, advance to sedang_dikerjakan
        if ($validated['status_konfirmasi'] === 'dikonfirmasi' && $currentStatusPenugasan === 'belum_dikerjakan') {
            $newStatusPenugasan = 'sedang_dikerjakan';
        }

        $penugasan->penerima()->updateExistingPivot($penerimaId, [
            'status_konfirmasi' => $validated['status_konfirmasi'],
            'catatan_konfirmasi' => $validated['catatan_konfirmasi'] ?? null,
            'dikonfirmasi_pada' => now(),
            'status_penugasan' => $newStatusPenugasan,
        ]);

        // WhatsApp notification to admin pemberi tugas
        $penugasan->load('pemberi');
        $whatsAppService->sendKonfirmasiPenugasanToAdmin(
            $penugasan,
            $anggota,
            $validated['status_konfirmasi'],
            $validated['catatan_konfirmasi'] ?? null
        );

        $msg = $validated['status_konfirmasi'] === 'dikonfirmasi'
            ? 'Penugasan berhasil dikonfirmasi! Status kini Sedang Dikerjakan.'
            : 'Respon penolakan/berhalangan penugasan telah dicatat.';

        return redirect()->route('admin.penugasan.show', $penugasan)
            ->with('success', $msg);
    }

    /**
     * Submit report (berkas & ringkasan) for penugasan by assigned anggota bidang.
     */
    public function submitLaporan(Request $request, Penugasan $penugasan, WhatsAppService $whatsAppService): RedirectResponse
    {
        $user = $request->user();
        $penerimaId = $user->isAnggotaBidang() ? $user->id : ($request->input('penerima_id') ?? $user->id);

        $anggota = $penugasan->penerima()->where('users.id', $penerimaId)->first();
        if (! $anggota) {
            abort(404, 'Data penerima penugasan tidak ditemukan.');
        }

        $existingPivot = $anggota->pivot;

        // If no file was actually selected (browser sends empty file input),
        // remove it from the request so the 'nullable' rule passes cleanly.
        if ($request->hasFile('file_laporan')) {
            $uploadedFile = $request->file('file_laporan');
            if (! $uploadedFile->isValid()) {
                $request->files->remove('file_laporan');
            }
        }

        $validated = $request->validate([
            'penerima_id' => ['nullable', 'exists:users,id'],
            'isi_laporan' => ['required', 'string', 'max:5000'],
            'link_dokumen' => ['nullable', 'url', 'max:500'],
            'file_laporan' => [
                'nullable',
                'file',
                'max:25600', // 25 MB
                'mimes:pdf,doc,docx,xls,xlsx,zip,rar,ppt,pptx,jpg,jpeg,png',
            ],
            'tandai_selesai' => ['nullable', 'boolean'],
        ], [
            'isi_laporan.required' => 'Ringkasan / uraian laporan wajib diisi.',
            'isi_laporan.max' => 'Uraian laporan terlalu panjang (maks 5000 karakter).',
            'link_dokumen.url' => 'Format link dokumen tidak valid. Pastikan diawali dengan https://',
            'file_laporan.file' => 'Berkas laporan gagal diunggah. Pastikan file tidak rusak dan coba lagi.',
            'file_laporan.uploaded' => 'Berkas gagal diunggah ke server. Pastikan ukuran file tidak melebihi 25 MB dan coba lagi.',
            'file_laporan.max' => 'Ukuran berkas terlalu besar. Maksimal yang diizinkan adalah 25 MB.',
            'file_laporan.mimes' => 'Format berkas tidak didukung. Gunakan: PDF, Word (doc/docx), Excel (xls/xlsx), PowerPoint, ZIP, RAR, JPG, atau PNG.',
        ]);

        $filePath = $existingPivot?->file_laporan;
        $fileName = $existingPivot?->file_laporan_name;
        $fileSize = $existingPivot?->file_laporan_size;

        if ($request->hasFile('file_laporan')) {
            $file = $request->file('file_laporan');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();

            if ($existingPivot && $existingPivot->file_laporan && Storage::disk('public')->exists($existingPivot->file_laporan)) {
                Storage::disk('public')->delete($existingPivot->file_laporan);
            }

            $filePath = $file->store('penugasan-laporan', 'public');
        }

        $statusPenugasan = $request->boolean('tandai_selesai', true) ? 'selesai' : ($existingPivot->status_penugasan ?? 'sedang_dikerjakan');
        if ($existingPivot->status_penugasan === 'belum_dikerjakan' && ! $request->boolean('tandai_selesai')) {
            $statusPenugasan = 'sedang_dikerjakan';
        }

        $penugasan->penerima()->updateExistingPivot($penerimaId, [
            'status_laporan' => 'sudah_dilaporkan',
            'file_laporan' => $filePath,
            'file_laporan_name' => $fileName,
            'file_laporan_size' => $fileSize,
            'isi_laporan' => $validated['isi_laporan'],
            'link_dokumen' => $validated['link_dokumen'] ?? null,
            'dilaporkan_pada' => now(),
            'status_penugasan' => $statusPenugasan,
            // If was not confirmed yet, mark as dikonfirmasi
            'status_konfirmasi' => ($existingPivot->status_konfirmasi === 'menunggu_konfirmasi' || empty($existingPivot->status_konfirmasi)) ? 'dikonfirmasi' : $existingPivot->status_konfirmasi,
            'dikonfirmasi_pada' => $existingPivot->dikonfirmasi_pada ?: now(),
        ]);

        // Reload fresh pivot for WhatsApp notifications
        $updatedAnggota = $penugasan->penerima()->where('users.id', $penerimaId)->first();
        $penugasan->load('pemberi');

        if ($updatedAnggota && $updatedAnggota->pivot instanceof PenugasanPenerima) {
            $whatsAppService->sendLaporanPenugasanToAdmin($penugasan, $updatedAnggota, $updatedAnggota->pivot);
            $whatsAppService->sendLaporanConfirmationToAnggota($penugasan, $updatedAnggota, $updatedAnggota->pivot);
        }

        return redirect()->route('admin.penugasan.show', $penugasan)
            ->with('success', 'Laporan penugasan berhasil dikumpulkan!');
    }

    /**
     * Download the submitted report file.
     */
    public function downloadLaporan(Request $request, Penugasan $penugasan, User $user)
    {
        $currentUser = $request->user();

        // Allow super admin, admin, the uploader themselves, or other members of the same assignment
        if (! $currentUser->isAdminOrSuperAdmin() && $currentUser->id !== $user->id) {
            $isSamePenugasan = $penugasan->penerima()->where('users.id', $currentUser->id)->exists();
            if (! $isSamePenugasan) {
                abort(403, 'Akses tidak diizinkan untuk mengunduh berkas laporan ini.');
            }
        }

        $anggota = $penugasan->penerima()->where('users.id', $user->id)->firstOrFail();
        $pivot = $anggota->pivot;

        if (! $pivot->file_laporan || ! Storage::disk('public')->exists($pivot->file_laporan)) {
            abort(404, 'Berkas laporan tidak ditemukan di server.');
        }

        return Storage::disk('public')->download(
            $pivot->file_laporan,
            $pivot->file_laporan_name ?: basename($pivot->file_laporan)
        );
    }

    /**
     * Update the status of a specific penerima in a penugasan.
     */
    public function updateStatus(Request $request, Penugasan $penugasan): RedirectResponse
    {
        $validated = $request->validate([
            'penerima_id' => ['required', 'exists:users,id'],
            'status_penugasan' => ['required', 'in:belum_dikerjakan,sedang_dikerjakan,selesai'],
            'status_laporan' => ['required', 'in:belum_dilaporkan,sudah_dilaporkan'],
            'status_konfirmasi' => ['nullable', 'in:menunggu_konfirmasi,dikonfirmasi,ditolak'],
        ]);

        $user = $request->user();
        if ($user->isAnggotaBidang() && (int) $validated['penerima_id'] !== (int) $user->id) {
            abort(403, 'Anda hanya memiliki izin untuk memperbarui status penugasan Anda sendiri.');
        }

        $updateData = [
            'status_penugasan' => $validated['status_penugasan'],
            'status_laporan' => $validated['status_laporan'],
        ];

        if (! empty($validated['status_konfirmasi'])) {
            $updateData['status_konfirmasi'] = $validated['status_konfirmasi'];
            if ($validated['status_konfirmasi'] !== 'menunggu_konfirmasi') {
                $updateData['dikonfirmasi_pada'] = now();
            }
        }

        $penugasan->penerima()->updateExistingPivot($validated['penerima_id'], $updateData);

        return redirect()->route('admin.penugasan.show', $penugasan)
            ->with('success', 'Status anggota berhasil diperbarui.');
    }

    /**
     * Remove the specified penugasan.
     */
    public function destroy(Request $request, Penugasan $penugasan): RedirectResponse
    {
        if (! $request->user()->isAdminOrSuperAdmin()) {
            abort(403, 'Akses tidak diizinkan. Hanya Admin dan Super Admin yang dapat menghapus penugasan bidang.');
        }

        $judul = $penugasan->judul;
        $penugasan->delete();

        return redirect()->route('admin.penugasan.index')
            ->with('success', "Penugasan '{$judul}' berhasil dihapus.");
    }
}
