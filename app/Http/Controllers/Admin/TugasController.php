<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\Tugas;
use App\Models\TugasOpd;
use App\Models\User;
use App\Services\CalendarService;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TugasController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isPic = $user->isAdmin();
        $handledOpdIds = $user->handledOpdIds();

        $query = Tugas::withCount([
            'tugasOpd as total_opd' => function ($q) use ($isPic, $handledOpdIds) {
                if ($isPic) {
                    $q->whereIn('opd_id', $handledOpdIds);
                }
            },
            'tugasOpd as total_terkumpul' => function ($q) use ($isPic, $handledOpdIds) {
                if ($isPic) {
                    $q->whereIn('opd_id', $handledOpdIds);
                }
                $q->whereIn('status', ['sudah_dikumpulkan', 'diterima']);
            },
            'tugasOpd as total_terlambat' => function ($q) use ($isPic, $handledOpdIds) {
                if ($isPic) {
                    $q->whereIn('opd_id', $handledOpdIds);
                }
                $q->where('status', 'terlambat');
            },
        ]);

        if ($isPic) {
            $query->whereHas('tugasOpd', fn ($q) => $q->whereIn('opd_id', $handledOpdIds));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('format')) {
            $query->where('format_pengumpulan', $request->format);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('tahun_anggaran', 'like', "%{$search}%");
            });
        }

        $tugasList = $query->latest('deadline')->paginate(10)->withQueryString();

        return view('admin.tugas.index', compact('tugasList'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $isPic = $user->isAdmin();
        $handledOpdIds = $user->handledOpdIds();

        $opdsQuery = Opd::active()
            ->with(['users' => fn ($query) => $query->where('role', 'opd')->orderBy('name')])
            ->orderBy('nama_opd');

        if ($isPic) {
            $opdsQuery->whereIn('id', $handledOpdIds);
        }

        $opds = $opdsQuery->get();

        return view('admin.tugas.create', compact('opds', 'isPic'));
    }

    /**
     * Store a newly created task and distribute to OPDs.
     */
    public function store(Request $request, WhatsAppService $whatsAppService): RedirectResponse
    {
        $user = $request->user();
        $isPic = $user->isAdmin();
        $handledOpdIds = $user->handledOpdIds();

        $opdItemRules = ['exists:opds,id'];
        if ($isPic) {
            $opdItemRules[] = Rule::in($handledOpdIds);
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'file_tugas' => ['nullable', 'file', 'max:25600', 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/zip,application/x-zip-compressed,application/x-rar-compressed,application/vnd.rar,text/csv,application/csv,application/octet-stream'],
            'format_pengumpulan' => ['required', 'in:digital,fisik,hybrid'],
            'deadline' => ['required', 'date', 'after:now'],
            'tahun_anggaran' => ['required', 'string', 'max:10'],
            'opd_ids' => ['required', 'array', 'min:1'],
            'opd_ids.*' => $opdItemRules,
            'staff_ids' => ['nullable', 'array'],
            'staff_ids.*' => ['exists:users,id'],
            'send_wa' => ['nullable', 'boolean'],
        ], [
            'opd_ids.*.in' => 'Anda hanya berwenang menugaskan OPD yang berada di bawah pengelolaan Anda.',
        ]);

        $filePath = null;
        $fileName = null;
        $fileSize = null;

        if ($request->hasFile('file_tugas')) {
            $file = $request->file('file_tugas');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $filePath = $file->store('tugas', 'public');
        }

        $tugas = Tugas::create([
            'admin_id' => $user->id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'file_path' => $filePath,
            'file_original_name' => $fileName,
            'file_size' => $fileSize,
            'format_pengumpulan' => $validated['format_pengumpulan'],
            'deadline' => $validated['deadline'],
            'tahun_anggaran' => $validated['tahun_anggaran'],
            'status' => 'aktif',
        ]);

        // Attach OPDs to task
        $selectedOpds = Opd::whereIn('id', $validated['opd_ids'])->get();
        foreach ($selectedOpds as $opd) {
            TugasOpd::create([
                'tugas_id' => $tugas->id,
                'opd_id' => $opd->id,
                'status' => 'belum_dikumpulkan',
            ]);
        }

        $notifSentCount = 0;

        // WhatsApp notification handling: send to selected staff members
        if ($request->boolean('send_wa', true)) {
            if ($request->has('staff_ids') && is_array($request->input('staff_ids'))) {
                $submittedStaffIds = $request->input('staff_ids');
                $targetStaff = User::whereIn('id', $submittedStaffIds)
                    ->where('role', 'opd')
                    ->whereIn('opd_id', $validated['opd_ids'])
                    ->whereNotNull('no_hp')
                    ->get();

                foreach ($targetStaff as $staff) {
                    $log = $whatsAppService->sendTaskCreatedToStaff($tugas, $staff);
                    if ($log) {
                        $notifSentCount++;
                    }
                }
            } else {
                // If staff_ids was not passed (e.g. tests or automated calls), notify all staff of selected OPDs
                foreach ($selectedOpds as $opd) {
                    $log = $whatsAppService->sendTaskCreated($tugas, $opd);
                    if ($log) {
                        $notifSentCount++;
                    }
                }
            }
        }

        $msg = "Tugas '{$tugas->judul}' berhasil dibuat dan didistribusikan ke {$selectedOpds->count()} OPD.";
        if ($notifSentCount > 0) {
            $msg .= " ({$notifSentCount} notifikasi WhatsApp staf tercatat).";
        }

        return redirect()->route('admin.tugas.show', $tugas)
            ->with('success', $msg);
    }

    /**
     * Display the specified task and monitoring table per OPD.
     */
    public function show(Request $request, Tugas $tugas): View
    {
        $user = $request->user();
        $isPic = $user->isAdmin();
        $handledOpdIds = $user->handledOpdIds();

        if ($isPic) {
            $hasAccess = $tugas->tugasOpd()->whereIn('opd_id', $handledOpdIds)->exists();
            if (! $hasAccess) {
                abort(403, 'Anda tidak memiliki hak akses untuk melihat tugas ini karena tidak menargetkan OPD yang Anda kelola.');
            }
        }

        $tugas->load([
            'admin',
            'tugasOpd' => function ($q) use ($isPic, $handledOpdIds) {
                if ($isPic) {
                    $q->whereIn('opd_id', $handledOpdIds);
                }
                $q->with(['opd.users', 'submission.verifier']);
            },
            'whatsappLogs' => function ($q) use ($isPic, $handledOpdIds) {
                if ($isPic) {
                    $q->whereIn('opd_id', $handledOpdIds);
                }
                $q->latest('sent_at')->take(20);
            },
        ]);

        $totalDitugaskan = $tugas->tugasOpd->count();
        $totalBelum = $tugas->tugasOpd->where('status', 'belum_dikumpulkan')->count();
        $totalMenunggu = $tugas->tugasOpd->filter(function ($to) {
            return $to->submission && $to->submission->status_verifikasi === 'menunggu_verifikasi';
        })->count();
        $totalDiterima = $tugas->tugasOpd->where('status', 'diterima')->count();
        $totalTerlambat = $tugas->tugasOpd->where('status', 'terlambat')->count();

        return view('admin.tugas.show', compact(
            'tugas',
            'totalDitugaskan',
            'totalBelum',
            'totalMenunggu',
            'totalDiterima',
            'totalTerlambat'
        ));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Request $request, Tugas $tugas): View
    {
        $user = $request->user();
        $isPic = $user->isAdmin();
        $handledOpdIds = $user->handledOpdIds();

        if ($isPic) {
            $hasAccess = $tugas->tugasOpd()->whereIn('opd_id', $handledOpdIds)->exists();
            if (! $hasAccess) {
                abort(403, 'Anda tidak memiliki hak akses untuk mengedit tugas ini.');
            }
        }

        $opdsQuery = Opd::active()->orderBy('nama_opd');
        if ($isPic) {
            $opdsQuery->whereIn('id', $handledOpdIds);
        }
        $opds = $opdsQuery->get();

        $assignedOpdIds = $tugas->tugasOpd()->pluck('opd_id')->toArray();

        return view('admin.tugas.edit', compact('tugas', 'opds', 'assignedOpdIds'));
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, Tugas $tugas): RedirectResponse
    {
        $user = $request->user();
        $isPic = $user->isAdmin();
        $handledOpdIds = $user->handledOpdIds();

        if ($isPic) {
            $hasAccess = $tugas->tugasOpd()->whereIn('opd_id', $handledOpdIds)->exists();
            if (! $hasAccess) {
                abort(403, 'Anda tidak memiliki hak akses untuk memperbarui tugas ini.');
            }
        }

        $opdItemRules = ['exists:opds,id'];
        if ($isPic) {
            $opdItemRules[] = Rule::in($handledOpdIds);
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'file_tugas' => ['nullable', 'file', 'max:25600', 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/zip,application/x-zip-compressed,application/x-rar-compressed,application/vnd.rar,text/csv,application/csv,application/octet-stream'],
            'hapus_file_tugas' => ['nullable', 'boolean'],
            'format_pengumpulan' => ['required', 'in:digital,fisik,hybrid'],
            'deadline' => ['required', 'date'],
            'tahun_anggaran' => ['required', 'string', 'max:10'],
            'status' => ['required', 'in:aktif,selesai,dibatalkan'],
            'opd_ids' => ['required', 'array', 'min:1'],
            'opd_ids.*' => $opdItemRules,
        ], [
            'opd_ids.*.in' => 'Anda hanya berwenang memilih OPD yang berada di bawah pengelolaan Anda.',
        ]);

        $filePath = $tugas->file_path;
        $fileName = $tugas->file_original_name;
        $fileSize = $tugas->file_size;

        if ($request->boolean('hapus_file_tugas')) {
            if ($tugas->file_path && Storage::disk('public')->exists($tugas->file_path)) {
                Storage::disk('public')->delete($tugas->file_path);
            }
            $filePath = null;
            $fileName = null;
            $fileSize = null;
        }

        if ($request->hasFile('file_tugas')) {
            if ($tugas->file_path && Storage::disk('public')->exists($tugas->file_path)) {
                Storage::disk('public')->delete($tugas->file_path);
            }
            $file = $request->file('file_tugas');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $filePath = $file->store('tugas', 'public');
        }

        $tugas->update([
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'file_path' => $filePath,
            'file_original_name' => $fileName,
            'file_size' => $fileSize,
            'format_pengumpulan' => $validated['format_pengumpulan'],
            'deadline' => $validated['deadline'],
            'tahun_anggaran' => $validated['tahun_anggaran'],
            'status' => $validated['status'],
        ]);

        // Sync OPDs (only add new ones, preserve existing submissions)
        $existingOpdIds = $tugas->tugasOpd()->pluck('opd_id')->toArray();
        $newOpdIds = array_diff($validated['opd_ids'], $existingOpdIds);

        foreach ($newOpdIds as $newId) {
            TugasOpd::create([
                'tugas_id' => $tugas->id,
                'opd_id' => $newId,
                'status' => 'belum_dikumpulkan',
            ]);
        }

        return redirect()->route('admin.tugas.show', $tugas)
            ->with('success', "Tugas '{$tugas->judul}' berhasil diperbarui.");
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Request $request, Tugas $tugas): RedirectResponse
    {
        $user = $request->user();
        if ($user->isAdmin()) {
            $hasAccess = $tugas->tugasOpd()->whereIn('opd_id', $user->handledOpdIds())->exists();
            if (! $hasAccess) {
                abort(403, 'Anda tidak memiliki hak akses untuk menghapus tugas ini.');
            }
        }

        $tugas->delete();

        return redirect()->route('admin.tugas.index')
            ->with('success', "Tugas '{$tugas->judul}' berhasil dihapus.");
    }

    /**
     * Manual broadcast reminder to unsubmitted OPDs.
     */
    public function broadcastReminder(
        Request $request,
        Tugas $tugas,
        WhatsAppService $whatsAppService
    ): RedirectResponse {
        $user = $request->user();
        $isPic = $user->isAdmin();
        $handledOpdIds = $user->handledOpdIds();

        if ($isPic) {
            $hasAccess = $tugas->tugasOpd()->whereIn('opd_id', $handledOpdIds)->exists();
            if (! $hasAccess) {
                abort(403, 'Anda tidak memiliki hak akses untuk mengirim pengingat pada tugas ini.');
            }
        }

        $spesifikRules = ['nullable', 'required_if:target_group,spesifik', 'exists:opds,id'];
        if ($isPic) {
            $spesifikRules[] = Rule::in($handledOpdIds);
        }

        $validated = $request->validate([
            'target_group' => ['required', 'in:belum_kumpul,semua,spesifik'],
            'spesifik_opd_id' => $spesifikRules,
            'custom_note' => ['nullable', 'string', 'max:500'],
        ], [
            'spesifik_opd_id.in' => 'OPD yang dipilih tidak berada di bawah wewenang Anda.',
        ]);

        $tugasOpdQuery = $tugas->tugasOpd()->with('opd');

        if ($isPic) {
            $tugasOpdQuery->whereIn('opd_id', $handledOpdIds);
        }

        if ($validated['target_group'] === 'belum_kumpul') {
            $tugasOpdQuery->whereIn('status', ['belum_dikumpulkan', 'perlu_revisi', 'terlambat']);
        } elseif ($validated['target_group'] === 'spesifik') {
            $tugasOpdQuery->where('opd_id', $validated['spesifik_opd_id']);
        }

        $targets = $tugasOpdQuery->get();
        $sentCount = 0;

        foreach ($targets as $to) {
            if ($to->opd && $to->opd->is_active) {
                $whatsAppService->sendManualBroadcast($tugas, $to->opd, $validated['custom_note'] ?? null);
                $sentCount++;
            }
        }

        return redirect()->back()
            ->with('success', "Broadcast pengingat WhatsApp berhasil dikirim ke {$sentCount} OPD.");
    }

    /**
     * Download .ics calendar event for this task.
     */
    public function downloadIcs(Request $request, Tugas $tugas, CalendarService $calendarService): Response
    {
        $user = $request->user();
        if ($user && $user->isAdmin()) {
            $hasAccess = $tugas->admin_id === $user->id || $tugas->tugasOpd()->whereIn('opd_id', $user->handledOpdIds())->exists();
            if (! $hasAccess) {
                abort(403, 'Anda tidak memiliki hak akses untuk mengunduh kalender tugas ini.');
            }
        }

        $icsContent = $calendarService->generateIcs($tugas);
        $fileName = 'deadline-tugas-'.$tugas->id.'.ics';

        return response($icsContent, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Download the task attachment/template file.
     */
    public function downloadFile(Request $request, Tugas $tugas): StreamedResponse
    {
        $user = $request->user();
        if ($user && $user->isAdmin()) {
            $hasAccess = $tugas->admin_id === $user->id || $tugas->tugasOpd()->whereIn('opd_id', $user->handledOpdIds())->exists();
            if (! $hasAccess) {
                abort(403, 'Anda tidak memiliki hak akses untuk mengunduh lampiran tugas ini.');
            }
        }

        if (! $tugas->file_path || ! Storage::disk('public')->exists($tugas->file_path)) {
            abort(404, 'File lampiran tugas tidak ditemukan di penyimpanan server.');
        }

        $fileName = $tugas->file_original_name ?: basename($tugas->file_path);

        return Storage::disk('public')->download($tugas->file_path, $fileName);
    }
}
