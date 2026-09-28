<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappLog;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsappLogController extends Controller
{
    /**
     * Display a listing of WhatsApp logs.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isPic = $user->isAdmin();
        $handledOpdIds = $user->handledOpdIds();

        $query = WhatsappLog::with(['opd', 'tugas'])->latest('sent_at');

        if ($isPic) {
            $query->whereIn('opd_id', $handledOpdIds);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe_pengingat', $request->tipe);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_tujuan', 'like', "%{$search}%")
                    ->orWhere('isi_pesan', 'like', "%{$search}%")
                    ->orWhereHas('opd', function ($oq) use ($search) {
                        $oq->where('nama_opd', 'like', "%{$search}%");
                    });
            });
        }

        $logs = $query->paginate(15)->withQueryString();

        // Metric counts
        $metricQuery = WhatsappLog::query();
        if ($isPic) {
            $metricQuery->whereIn('opd_id', $handledOpdIds);
        }

        $totalTerkirim = (clone $metricQuery)->where('status', 'terkirim')->count();
        $totalSimulasi = (clone $metricQuery)->where('status', 'simulasi')->count();
        $totalGagal = (clone $metricQuery)->where('status', 'gagal')->count();

        return view('admin.whatsapp-logs.index', compact('logs', 'totalTerkirim', 'totalSimulasi', 'totalGagal'));
    }

    /**
     * Resend/retry WhatsApp message.
     */
    public function resend(Request $request, WhatsappLog $log, WhatsAppService $whatsAppService): RedirectResponse
    {
        $user = $request->user();
        if ($user && $user->isAdmin() && ! $user->handlesOpd($log->opd_id)) {
            abort(403, 'Anda tidak berwenang mengirim ulang notifikasi untuk OPD ini.');
        }

        $newLog = $whatsAppService->send(
            $log->no_tujuan,
            $log->isi_pesan,
            $log->tugas_id,
            $log->opd_id,
            $log->tipe_pengingat
        );

        return redirect()->back()
            ->with('success', "Pesan telah dikirim ulang (ID Log baru: #{$newLog->id}, Status: {$newLog->status}).");
    }

    /**
     * Remove the specified WhatsApp log.
     */
    public function destroy(Request $request, WhatsappLog $log): RedirectResponse
    {
        $user = $request->user();
        if ($user && $user->isAdmin() && ! $user->handlesOpd($log->opd_id)) {
            abort(403, 'Anda tidak berwenang menghapus log notifikasi untuk OPD ini.');
        }

        $log->delete();

        return redirect()->back()
            ->with('success', 'Log notifikasi WhatsApp berhasil dihapus.');
    }
}
