<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\Penugasan;
use App\Models\PenugasanPenerima;
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

        // ===== BREAKDOWN KEPATUHAN OPD (DONUT CHART) =====
        $opdQuery = TugasOpd::query();
        if ($isPic) {
            $opdQuery->whereIn('opd_id', $handledOpdIds);
        }

        $opdStatusCounts = (clone $opdQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $opdDiterima = (int) $opdStatusCounts->get('diterima', 0);
        $opdVerifikasi = (int) $opdStatusCounts->get('sudah_dikumpulkan', 0);
        $opdBelum = (int) $opdStatusCounts->get('belum_dikumpulkan', 0);
        $opdRevisi = (int) $opdStatusCounts->get('perlu_revisi', 0);
        $opdTerlambat = (int) $opdStatusCounts->get('terlambat', 0);
        $totalTugasOpdSemua = $opdDiterima + $opdVerifikasi + $opdBelum + $opdRevisi + $opdTerlambat;

        $kepatuhanOpdRate = $totalTugasOpdSemua > 0
            ? (int) round((($opdDiterima + $opdVerifikasi) / $totalTugasOpdSemua) * 100)
            : 0;

        $chartKepatuhanOpd = $this->buildDonutChart(
            title: 'Kepatuhan Penugasan OPD',
            subtitle: 'Distribusi status pengumpulan berkas perangkat daerah',
            centerLabel: 'Kepatuhan',
            rate: $kepatuhanOpdRate,
            total: $totalTugasOpdSemua,
            actionUrl: route('admin.tugas.index'),
            rawItems: [
                [
                    'label' => 'Valid & Diterima',
                    'count' => $opdDiterima,
                    'color' => '#059669',
                    'badge_class' => 'bg-emerald-500',
                ],
                [
                    'label' => 'Menunggu Verifikasi',
                    'count' => $opdVerifikasi,
                    'color' => '#0284c7',
                    'badge_class' => 'bg-sky-500',
                ],
                [
                    'label' => 'Belum Mengumpulkan',
                    'count' => $opdBelum,
                    'color' => '#f59e0b',
                    'badge_class' => 'bg-amber-500',
                ],
                [
                    'label' => 'Perlu Perbaikan',
                    'count' => $opdRevisi,
                    'color' => '#ec4899',
                    'badge_class' => 'bg-pink-500',
                ],
                [
                    'label' => 'Lewat Batas Waktu',
                    'count' => $opdTerlambat,
                    'color' => '#f43f5e',
                    'badge_class' => 'bg-rose-500',
                ],
            ]
        );

        // ===== BREAKDOWN KEPATUHAN ANGGOTA BIDANG (DONUT CHART) =====
        $bidangPenerima = PenugasanPenerima::query()
            ->select(['status_penugasan', 'status_laporan', 'status_konfirmasi'])
            ->get();

        $totalBidangSemua = $bidangPenerima->count();
        $bidangSelesaiLapor = $bidangPenerima->where('status_penugasan', 'selesai')->where('status_laporan', 'sudah_dilaporkan')->count();
        $bidangSelesaiBelumLapor = $bidangPenerima->where('status_penugasan', 'selesai')->where('status_laporan', '!=', 'sudah_dilaporkan')->count();
        $bidangSedang = $bidangPenerima->where('status_penugasan', 'sedang_dikerjakan')->count();
        $bidangBelum = $bidangPenerima->filter(function ($item) {
            return (empty($item->status_penugasan) || $item->status_penugasan === 'belum_dikerjakan')
                && $item->status_konfirmasi !== 'ditolak';
        })->count();
        $bidangDitolak = $bidangPenerima->where('status_konfirmasi', 'ditolak')->count();

        $kepatuhanBidangRate = $totalBidangSemua > 0
            ? (int) round(($bidangSelesaiLapor / $totalBidangSemua) * 100)
            : 0;

        $chartKepatuhanBidang = $this->buildDonutChart(
            title: 'Kepatuhan Anggota Bidang',
            subtitle: 'Progres kegiatan & laporan staf Bidang Ekop',
            centerLabel: 'Kepatuhan',
            rate: $kepatuhanBidangRate,
            total: $totalBidangSemua,
            actionUrl: route('admin.penugasan.index'),
            rawItems: [
                [
                    'label' => 'Selesai & Dilaporkan',
                    'count' => $bidangSelesaiLapor,
                    'color' => '#059669',
                    'badge_class' => 'bg-emerald-500',
                ],
                [
                    'label' => 'Selesai (Belum Lapor)',
                    'count' => $bidangSelesaiBelumLapor,
                    'color' => '#0d9488',
                    'badge_class' => 'bg-teal-500',
                ],
                [
                    'label' => 'Sedang Dikerjakan',
                    'count' => $bidangSedang,
                    'color' => '#4f46e5',
                    'badge_class' => 'bg-indigo-600',
                ],
                [
                    'label' => 'Belum Dimulai',
                    'count' => $bidangBelum,
                    'color' => '#f59e0b',
                    'badge_class' => 'bg-amber-500',
                ],
                [
                    'label' => 'Berhalangan / Ditolak',
                    'count' => $bidangDitolak,
                    'color' => '#f43f5e',
                    'badge_class' => 'bg-rose-500',
                ],
            ]
        );

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
            'calendarEvents',
            'chartKepatuhanOpd',
            'chartKepatuhanBidang'
        ));
    }

    /**
     * Build donut chart breakdown dataset.
     *
     * @param  array<int, array{label: string, count: int, color: string, badge_class: string}>  $rawItems
     * @return array{
     *     title: string,
     *     subtitle: string,
     *     rate: int,
     *     total: int,
     *     center_label: string,
     *     action_url: string,
     *     segments: array<int, array{label: string, count: int, percentage: int, color: string, dasharray: string, dashoffset: string, badge_class: string}>
     * }
     */
    private function buildDonutChart(string $title, string $subtitle, string $centerLabel, int $rate, int $total, string $actionUrl, array $rawItems): array
    {
        $circumference = 2 * M_PI * 50;
        $currentOffset = 0;
        $segments = [];

        $activeSegmentsCount = collect($rawItems)->where('count', '>', 0)->count();
        $gap = $activeSegmentsCount > 1 ? 2.5 : 0;

        foreach ($rawItems as $item) {
            $count = $item['count'];
            $pct = $total > 0 ? (int) round(($count / $total) * 100) : 0;
            $arcLen = $total > 0 ? ($count / $total) * $circumference : 0;
            $visibleLen = $count > 0 ? max(0.5, $arcLen - $gap) : 0;

            $segments[] = [
                'label' => $item['label'],
                'count' => $count,
                'percentage' => $pct,
                'color' => $item['color'],
                'badge_class' => $item['badge_class'],
                'dasharray' => sprintf('%.2f %.2f', $visibleLen, $circumference - $visibleLen),
                'dashoffset' => sprintf('%.2f', -$currentOffset),
            ];

            $currentOffset += $arcLen;
        }

        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'rate' => $rate,
            'total' => $total,
            'center_label' => $centerLabel,
            'action_url' => $actionUrl,
            'segments' => $segments,
        ];
    }
}
