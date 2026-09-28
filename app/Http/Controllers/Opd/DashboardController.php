<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Models\TugasOpd;
use App\Models\WhatsappLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the PIC OPD dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $opd = $user->opd;

        if (! $opd) {
            abort(403, 'Akun Anda belum dihubungkan dengan data OPD manapun. Silakan hubungi Admin Bappeda.');
        }

        $baseQuery = TugasOpd::where('opd_id', $opd->id);

        $totalTugas = (clone $baseQuery)->count();
        $totalPerluTindakan = (clone $baseQuery)->whereIn('status', ['belum_dikumpulkan', 'perlu_revisi'])->count();
        $totalMenungguVerifikasi = (clone $baseQuery)->where('status', 'sudah_dikumpulkan')->count();
        $totalSelesai = (clone $baseQuery)->where('status', 'diterima')->count();
        $totalTerlambat = (clone $baseQuery)->where('status', 'terlambat')->count();

        // Tasks that need urgent action (nearest deadline)
        $tugasMendesak = TugasOpd::with('tugas')
            ->join('tugas', 'tugas_opd.tugas_id', '=', 'tugas.id')
            ->where('tugas_opd.opd_id', $opd->id)
            ->whereIn('tugas_opd.status', ['belum_dikumpulkan', 'perlu_revisi', 'terlambat'])
            ->where('tugas.status', 'aktif')
            ->orderBy('tugas.deadline', 'asc')
            ->select('tugas_opd.*')
            ->take(5)
            ->get();

        // Recent completed submissions
        $riwayatTerbaru = TugasOpd::with(['tugas', 'submission'])
            ->where('opd_id', $opd->id)
            ->whereIn('status', ['sudah_dikumpulkan', 'diterima'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Recent WhatsApp logs for this OPD
        $recentWaLogs = WhatsappLog::where('opd_id', $opd->id)
            ->latest('sent_at')
            ->take(5)
            ->get();

        return view('opd.dashboard', compact(
            'opd',
            'totalTugas',
            'totalPerluTindakan',
            'totalMenungguVerifikasi',
            'totalSelesai',
            'totalTerlambat',
            'tugasMendesak',
            'riwayatTerbaru',
            'recentWaLogs'
        ));
    }
}
