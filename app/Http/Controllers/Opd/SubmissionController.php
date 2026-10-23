<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Tugas;
use App\Models\TugasOpd;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SubmissionController extends Controller
{
    /**
     * Handle task submission by OPD PIC.
     */
    public function store(Request $request, Tugas $tugas, WhatsAppService $whatsAppService): RedirectResponse
    {
        $opd = $request->user()->opd;
        if (! $opd) {
            abort(403, 'Akun Anda belum terhubung ke instansi OPD.');
        }

        $tugasOpd = TugasOpd::where('tugas_id', $tugas->id)
            ->where('opd_id', $opd->id)
            ->firstOrFail();

        $format = $tugas->format_pengumpulan;

        // Dynamic validation rules based on format
        $rules = [
            'catatan_opd' => ['nullable', 'string', 'max:1000'],
        ];

        $existingSubmission = $tugasOpd->submission;

        $validationData = $request->all();

        // If no file was actually selected (browser sends empty file input),
        // remove it from validation data so the 'nullable' rule passes cleanly when updating.
        $uploadedFile = $request->file('berkas_dokumen');
        if (! $uploadedFile || ! $uploadedFile->isValid()) {
            unset($validationData['berkas_dokumen']);
        }

        if ($format === 'digital' || $format === 'hybrid') {
            $rules['berkas_dokumen'] = [
                $existingSubmission && $existingSubmission->file_path ? 'nullable' : 'required',
                'file',
                'max:25600', // 25 MB
                'mimes:pdf,doc,docx,xls,xlsx,zip,rar,csv,ppt,pptx',
            ];
        }

        if ($format === 'fisik' || $format === 'hybrid') {
            $rules['konfirmasi_fisik'] = ['accepted'];
            $rules['nama_pengantar'] = ['required', 'string', 'max:255'];
            $rules['nomor_surat'] = ['required', 'string', 'max:255'];
            $rules['tanggal_penyerahan_fisik'] = ['required', 'date'];
        }

        $validated = Validator::make($validationData, $rules)->validate();

        $filePath = $existingSubmission?->file_path;
        $fileName = $existingSubmission?->file_original_name;
        $fileSize = $existingSubmission?->file_size;

        // Process file upload if provided
        if ($request->hasFile('berkas_dokumen')) {
            $file = $request->file('berkas_dokumen');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();

            // Delete old file if updating
            if ($existingSubmission && $existingSubmission->file_path && Storage::disk('public')->exists($existingSubmission->file_path)) {
                Storage::disk('public')->delete($existingSubmission->file_path);
            }

            $filePath = $file->store('submissions', 'public');
        }

        $isFisik = ($format === 'fisik' || $format === 'hybrid');

        $submissionData = [
            'tugas_opd_id' => $tugasOpd->id,
            'file_path' => $filePath,
            'file_original_name' => $fileName,
            'file_size' => $fileSize,
            'konfirmasi_fisik' => $isFisik ? $request->boolean('konfirmasi_fisik') : false,
            'nama_pengantar' => $isFisik ? ($validated['nama_pengantar'] ?? null) : null,
            'nomor_surat' => $isFisik ? ($validated['nomor_surat'] ?? null) : null,
            'tanggal_penyerahan_fisik' => $isFisik ? ($validated['tanggal_penyerahan_fisik'] ?? null) : null,
            'catatan_opd' => $validated['catatan_opd'] ?? null,
            'status_verifikasi' => 'menunggu_verifikasi',
            'verified_at' => null,
            'verified_by' => null,
        ];

        if ($existingSubmission) {
            $existingSubmission->update($submissionData);
            $submission = $existingSubmission;
        } else {
            $submission = Submission::create($submissionData);
        }

        // Update TugasOpd status
        $tugasOpd->update([
            'status' => 'sudah_dikumpulkan',
        ]);

        // Send WhatsApp notification to PIC and confirmation receipt to OPD
        $submission->loadMissing(['tugasOpd.tugas', 'tugasOpd.opd']);
        $whatsAppService->sendSubmissionReceived($submission, $request->user());

        return redirect()->route('opd.tugas.show', $tugas)
            ->with('success', 'Pengumpulan dokumen berhasil disimpan dan notifikasi telah dikirimkan ke PIC Bappeda.');
    }
}
