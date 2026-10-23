<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\Penugasan;
use App\Models\Submission;
use App\Models\Tugas;
use App\Models\TugasOpd;
use App\Models\WhatsappLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin monitoring dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isPic = $user->isAdmin();
        $handledOpdIds = $user->handledOpdIds();

        if ($isPic) {
            $totalOpd = Opd::active()->whereIn('id', $handledOpdIds)->count();
            $totalTugasAktif = Tugas::where('status', 'aktif')
                ->whereHas('tugasOpd', fn ($q) => $q->whereIn('opd_id', $handledOpdIds))
                ->count();

            // Assignment metrics for handled OPDs
            $totalBelumKumpul = TugasOpd::whereIn('opd_id', $handledOpdIds)
                ->where('status', 'belum_dikumpulkan')
                ->count();
            $totalMenungguVerifikasi = Submission::whereHas('tugasOpd', fn ($q) => $q->whereIn('opd_id', $handledOpdIds))
                ->where('status_verifikasi', 'menunggu_verifikasi')
                ->count();
            $totalDiterima = TugasOpd::whereIn('opd_id', $handledOpdIds)
                ->where('status', 'diterima')
                ->count();
            $totalTerlambat = TugasOpd::whereIn('opd_id', $handledOpdIds)
                ->where('status', 'terlambat')
                ->count();
        } else {
            $totalOpd = Opd::active()->count();
            $totalTugasAktif = Tugas::where('status', 'aktif')->count();

            // Assignment metrics across all OPDs
            $totalBelumKumpul = TugasOpd::where('status', 'belum_dikumpulkan')->count();
            $totalMenungguVerifikasi = Submission::where('status_verifikasi', 'menunggu_verifikasi')->count();
            $totalDiterima = TugasOpd::where('status', 'diterima')->count();
            $totalTerlambat = TugasOpd::where('status', 'terlambat')->count();
        }

        // Tasks with completion stats
        $tugasQuery = Tugas::with([
            'tugasOpd' => function ($q) use ($isPic, $handledOpdIds) {
                if ($isPic) {
                    $q->whereIn('opd_id', $handledOpdIds);
                }
                $q->with(['opd', 'submission']);
            },
        ])
            ->withCount([
                'tugasOpd as total_ditugaskan' => function ($query) use ($isPic, $handledOpdIds) {
                    if ($isPic) {
                        $query->whereIn('opd_id', $handledOpdIds);
                    }
                },
                'tugasOpd as total_terkumpul' => function ($query) use ($isPic, $handledOpdIds) {
                    if ($isPic) {
                        $query->whereIn('opd_id', $handledOpdIds);
                    }
                    $query->whereIn('status', ['sudah_dikumpulkan', 'diterima']);
                },
                'tugasOpd as total_terlambat' => function ($query) use ($isPic, $handledOpdIds) {
                    if ($isPic) {
                        $query->whereIn('opd_id', $handledOpdIds);
                    }
                    $query->where('status', 'terlambat');
                },
            ])
            ->latest();

        if ($isPic) {
            $tugasQuery->whereHas('tugasOpd', fn ($q) => $q->whereIn('opd_id', $handledOpdIds));
        }

        if ($request->filled('status')) {
            $tugasQuery->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $tugasQuery->where('judul', 'like', "%{$search}%");
        }

        $daftarTugas = $tugasQuery->paginate(6)->withQueryString();

        // Submissions awaiting verification
        $submissionsMenungguQuery = Submission::with(['tugasOpd.tugas', 'tugasOpd.opd'])
            ->where('status_verifikasi', 'menunggu_verifikasi');

        if ($isPic) {
            $submissionsMenungguQuery->whereHas('tugasOpd', fn ($q) => $q->whereIn('opd_id', $handledOpdIds));
        }

        $submissionsMenunggu = $submissionsMenungguQuery->latest()
            ->take(5)
            ->get();

        // Recent WhatsApp logs
        $recentLogsQuery = WhatsappLog::with(['opd', 'tugas']);

        if ($isPic) {
            $recentLogsQuery->whereIn('opd_id', $handledOpdIds);
        }

        $recentLogs = $recentLogsQuery->latest('sent_at')
            ->take(5)
            ->get();

        // Calendar Events: Penugasan OPD (deadlines) & Penugasan Bidang (kegiatan & batas laporan)
        $tugasCalendarQuery = Tugas::query()->where('status', '!=', 'dibatalkan');
        if ($isPic) {
            $tugasCalendarQuery->whereHas('tugasOpd', fn ($q) => $q->whereIn('opd_id', $handledOpdIds));
        }
        $tugasEvents = $tugasCalendarQuery->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'type' => 'tugas_opd',
                    'type_label' => 'Penugasan OPD',
                    'title' => $t->judul,
                    'date' => $t->deadline ? $t->deadline->format('Y-m-d') : null,
                    'time' => $t->deadline ? $t->deadline->format('H:i').' WIT' : null,
                    'url' => route('admin.tugas.show', $t->id),
                    'badge_color' => 'emerald',
                    'status' => $t->status,
                    'detail' => 'Format: '.($t->format_label ?? 'Digital').' • TA '.$t->tahun_anggaran,
                ];
            })->filter(fn ($e) => ! empty($e['date']));

        $penugasanEvents = Penugasan::with('penerima')
            ->get()
            ->flatMap(function ($p) {
                $items = [];
                if ($p->tanggal_kegiatan) {
                    $items[] = [
                        'id' => $p->id,
                        'type' => 'penugasan_bidang',
                        'type_label' => 'Kegiatan Bidang',
                        'title' => $p->judul,
                        'date' => $p->tanggal_kegiatan->format('Y-m-d'),
                        'time' => 'Kegiatan',
                        'url' => route('admin.penugasan.show', $p->id),
                        'badge_color' => 'indigo',
                        'status' => 'aktif',
                        'detail' => 'Tim: '.($p->penerima->pluck('name')->take(2)->join(', ') ?: 'Anggota Bidang').($p->penerima->count() > 2 ? ' +'.($p->penerima->count() - 2) : ''),
                    ];
                }
                if ($p->batas_laporan && (! $p->tanggal_kegiatan || $p->batas_laporan->format('Y-m-d') !== $p->tanggal_kegiatan->format('Y-m-d'))) {
                    $items[] = [
                        'id' => $p->id,
                        'type' => 'penugasan_laporan',
                        'type_label' => 'Batas Laporan Bidang',
                        'title' => '[Laporan] '.$p->judul,
                        'date' => $p->batas_laporan->format('Y-m-d'),
                        'time' => 'Batas Laporan',
                        'url' => route('admin.penugasan.show', $p->id),
                        'badge_color' => 'amber',
                        'status' => 'aktif',
                        'detail' => 'Batas penyerahan laporan kegiatan',
                    ];
                }

                return $items;
            });

        $calendarEvents = $tugasEvents->concat($penugasanEvents)->values();

        return view('admin.dashboard', compact(
            'totalOpd',
            'totalTugasAktif',
            'totalBelumKumpul',
            'totalMenungguVerifikasi',
            'totalDiterima',
            'totalTerlambat',
            'daftarTugas',
            'submissionsMenunggu',
            'recentLogs',
            'isPic',
            'handledOpdIds',
            'calendarEvents'
        ));
    }
}
