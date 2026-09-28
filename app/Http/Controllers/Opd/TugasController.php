<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\TugasOpd;
use App\Models\WhatsappLog;
use App\Services\CalendarService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TugasController extends Controller
{
    /**
     * Display a listing of tasks assigned to the user's OPD.
     */
    public function index(Request $request): View
    {
        $opd = $request->user()->opd;
        if (! $opd) {
            abort(403, 'Akun Anda belum terhubung ke instansi OPD.');
        }

        $query = TugasOpd::with(['tugas', 'submission'])
            ->join('tugas', 'tugas_opd.tugas_id', '=', 'tugas.id')
            ->where('tugas_opd.opd_id', $opd->id)
            ->select('tugas_opd.*');

        if ($request->filled('status')) {
            if ($request->status === 'perlu_tindakan') {
                $query->whereIn('tugas_opd.status', ['belum_dikumpulkan', 'perlu_revisi', 'terlambat']);
            } elseif ($request->status === 'selesai') {
                $query->where('tugas_opd.status', 'diterima');
            } else {
                $query->where('tugas_opd.status', $request->status);
            }
        }

        if ($request->filled('format')) {
            $query->where('tugas.format_pengumpulan', $request->format);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('tugas.judul', 'like', "%{$search}%");
        }

        $tugasOpdList = $query->orderBy('tugas.deadline', 'asc')->paginate(10)->withQueryString();

        return view('opd.tugas.index', compact('tugasOpdList', 'opd'));
    }

    /**
     * Display the specified task and its submission form/history.
     */
    public function show(Request $request, Tugas $tugas): View
    {
        $opd = $request->user()->opd;
        if (! $opd) {
            abort(403, 'Akun Anda belum terhubung ke instansi OPD.');
        }

        $tugasOpd = TugasOpd::with(['submission.verifier', 'tugas'])
            ->where('tugas_id', $tugas->id)
            ->where('opd_id', $opd->id)
            ->firstOrFail();

        // Logs of notifications received for this task & OPD
        $waLogs = WhatsappLog::where('tugas_id', $tugas->id)
            ->where('opd_id', $opd->id)
            ->latest('sent_at')
            ->get();

        return view('opd.tugas.show', compact('tugas', 'tugasOpd', 'opd', 'waLogs'));
    }

    /**
     * Download .ics calendar event for this task.
     */
    public function downloadIcs(Request $request, Tugas $tugas, CalendarService $calendarService): Response
    {
        $opd = $request->user()->opd;
        $icsContent = $calendarService->generateIcs($tugas, $opd);
        $fileName = 'tugas-bappeda-'.$tugas->id.'.ics';

        return response($icsContent, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }
}
