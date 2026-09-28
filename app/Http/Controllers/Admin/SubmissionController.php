<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    /**
     * Display the specified submission detail.
     */
    public function show(Request $request, Submission $submission): View
    {
        $submission->load(['tugasOpd.tugas', 'tugasOpd.opd', 'verifier']);

        $user = $request->user();
        if ($user && $user->isAdmin() && ! $user->handlesOpd($submission->tugasOpd->opd_id)) {
            abort(403, 'Anda tidak berwenang melihat pengumpulan berkas dari OPD ini.');
        }

        return view('admin.submission.show', compact('submission'));
    }

    /**
     * Verify the submission (Approve or Request Revision).
     */
    public function verify(
        Request $request,
        Submission $submission,
        WhatsAppService $whatsAppService
    ): RedirectResponse {
        $user = $request->user();
        if ($user && $user->isAdmin() && ! $user->handlesOpd($submission->tugasOpd->opd_id)) {
            abort(403, 'Anda tidak berwenang memverifikasi berkas dari OPD ini.');
        }

        $validated = $request->validate([
            'status_verifikasi' => ['required', 'in:diterima,perlu_revisi'],
            'catatan_admin' => ['nullable', 'string', 'max:1000'],
            'send_wa_notification' => ['nullable', 'boolean'],
        ]);

        $submission->update([
            'status_verifikasi' => $validated['status_verifikasi'],
            'catatan_admin' => $validated['catatan_admin'] ?? null,
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
        ]);

        // Synchronize TugasOpd status
        $tugasOpd = $submission->tugasOpd;
        $newStatus = $validated['status_verifikasi'] === 'diterima' ? 'diterima' : 'perlu_revisi';
        $tugasOpd->update(['status' => $newStatus]);

        if ($request->boolean('send_wa_notification', true)) {
            $whatsAppService->sendVerificationResult($submission);
        }

        $label = $validated['status_verifikasi'] === 'diterima' ? 'Diterima' : 'Perlu Revisi';

        return redirect()->route('admin.tugas.show', $tugasOpd->tugas_id)
            ->with('success', "Pengumpulan berkas dari OPD '{$tugasOpd->opd->nama_opd}' telah ditandai {$label}.");
    }

    /**
     * Download the submitted digital document.
     */
    public function downloadFile(Request $request, Submission $submission): StreamedResponse
    {
        $user = $request->user();
        if ($user && $user->isAdmin() && ! $user->handlesOpd($submission->tugasOpd->opd_id)) {
            abort(403, 'Anda tidak berwenang mengunduh berkas pengumpulan dari OPD ini.');
        }

        if (! $submission->file_path || ! Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File lampiran tidak ditemukan di penyimpanan server.');
        }

        $fileName = $submission->file_original_name ?: basename($submission->file_path);

        return Storage::disk('public')->download($submission->file_path, $fileName);
    }
}
