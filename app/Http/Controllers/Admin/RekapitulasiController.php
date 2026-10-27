<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\Penugasan;
use App\Models\PenugasanPenerima;
use App\Models\Tugas;
use App\Models\TugasOpd;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RekapitulasiController extends Controller
{
    /**
     * Display a detailed performance recapitulation report for all OPDs.
     */
    public function opd(Request $request): View
    {
        $period = $request->input('period', '30_days');
        $now = Carbon::now();

        if ($period === 'this_month') {
            $startDate = $now->copy()->startOfMonth()->startOfDay();
            $endDate = $now->copy()->endOfMonth()->endOfDay();
            $periodLabel = 'Bulan Ini ('.$now->translatedFormat('F Y').')';
        } elseif ($period === 'prev_month') {
            $startDate = $now->copy()->subMonth()->startOfMonth()->startOfDay();
            $endDate = $now->copy()->subMonth()->endOfMonth()->endOfDay();
            $periodLabel = 'Bulan Lalu ('.$now->copy()->subMonth()->translatedFormat('F Y').')';
        } elseif ($period === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
            $periodLabel = 'Periode '.$startDate->translatedFormat('d M Y').' s/d '.$endDate->translatedFormat('d M Y');
        } else {
            $period = '30_days';
            $startDate = $now->copy()->subDays(30)->startOfDay();
            $endDate = $now->copy()->endOfDay();
            $periodLabel = '1 Bulan Terakhir ('.$startDate->translatedFormat('d M Y').' s/d '.$endDate->translatedFormat('d M Y').')';
        }

        // Fetch all tasks published or due within this period
        $tasksInPeriod = Tugas::with('admin')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('deadline', [$startDate, $endDate])
                    ->orWhereBetween('created_at', [$startDate, $endDate]);
            })
            ->latest('deadline')
            ->get();

        $taskIds = $tasksInPeriod->pluck('id');

        // Fetch all assignments (tugas_opd) within the period with eager loads
        $assignments = TugasOpd::with(['tugas.admin', 'opd.users', 'submission.verifier'])
            ->whereIn('tugas_id', $taskIds)
            ->get();

        $groupedByOpd = $assignments->groupBy('opd_id');

        // Fetch all active OPDs
        $allOpds = Opd::active()->with('users')->orderBy('nama_opd')->get();

        // Compute performance metrics for each OPD
        $opdPerformance = $allOpds->map(function (Opd $opd) use ($groupedByOpd) {
            $opdAssignments = $groupedByOpd->get($opd->id, collect());
            $totalAssigned = $opdAssignments->count();

            $totalDiterima = $opdAssignments->where('status', 'diterima')->count();
            $totalMenunggu = $opdAssignments->where('status', 'sudah_dikumpulkan')->count();
            $totalRevisi = $opdAssignments->where('status', 'perlu_revisi')->count();
            $totalBelum = $opdAssignments->where('status', 'belum_dikumpulkan')->count();
            $totalTerlambat = $opdAssignments->where('status', 'terlambat')->count();

            // Compliance rate: percentage of tasks fulfilled (verified accepted or submitted waiting verification)
            $kepatuhanRate = $totalAssigned > 0
                ? round((($totalDiterima + $totalMenunggu) / $totalAssigned) * 100, 1)
                : null;

            if ($totalAssigned === 0) {
                $predikat = 'Nihil Penugasan';
                $badgeClass = 'bg-slate-100 text-slate-700 border-slate-300';
            } elseif ($kepatuhanRate >= 90) {
                $predikat = 'Sangat Baik';
                $badgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-300';
            } elseif ($kepatuhanRate >= 75) {
                $predikat = 'Baik';
                $badgeClass = 'bg-sky-100 text-sky-800 border-sky-300';
            } elseif ($kepatuhanRate >= 60) {
                $predikat = 'Cukup';
                $badgeClass = 'bg-amber-100 text-amber-800 border-amber-300';
            } else {
                $predikat = 'Kurang / Perlu Pembinaan';
                $badgeClass = 'bg-rose-100 text-rose-800 border-rose-300';
            }

            return [
                'opd' => $opd,
                'total_assigned' => $totalAssigned,
                'total_diterima' => $totalDiterima,
                'total_menunggu' => $totalMenunggu,
                'total_revisi' => $totalRevisi,
                'total_belum' => $totalBelum,
                'total_terlambat' => $totalTerlambat,
                'kepatuhan_rate' => $kepatuhanRate,
                'predikat' => $predikat,
                'badge_class' => $badgeClass,
                'assignments' => $opdAssignments,
            ];
        });

        // Sort: OPDs with tasks first by compliance rate descending, then by name
        $opdPerformance = $opdPerformance->sort(function ($a, $b) {
            if ($a['total_assigned'] > 0 && $b['total_assigned'] === 0) {
                return -1;
            }
            if ($a['total_assigned'] === 0 && $b['total_assigned'] > 0) {
                return 1;
            }
            if ($a['kepatuhan_rate'] !== $b['kepatuhan_rate']) {
                return ($b['kepatuhan_rate'] ?? -1) <=> ($a['kepatuhan_rate'] ?? -1);
            }

            return strcmp($a['opd']->nama_opd, $b['opd']->nama_opd);
        })->values();

        // Global KPI Totals
        $totalOpds = $allOpds->count();
        $totalTugasDiterbitkan = $tasksInPeriod->count();
        $totalDisposisi = $assignments->count();
        $totalDiterima = $assignments->where('status', 'diterima')->count();
        $totalMenunggu = $assignments->where('status', 'sudah_dikumpulkan')->count();
        $totalRevisi = $assignments->where('status', 'perlu_revisi')->count();
        $totalBelum = $assignments->where('status', 'belum_dikumpulkan')->count();
        $totalTerlambat = $assignments->where('status', 'terlambat')->count();

        $overallComplianceRate = $totalDisposisi > 0
            ? round((($totalDiterima + $totalMenunggu) / $totalDisposisi) * 100, 1)
            : 0;

        // Detailed assignment records list sorted by OPD name and deadline
        $detailedAssignments = $assignments->sortBy([
            fn ($a, $b) => strcmp($a->opd->nama_opd ?? '', $b->opd->nama_opd ?? ''),
            fn ($a, $b) => ($b->tugas->deadline ?? 0) <=> ($a->tugas->deadline ?? 0),
        ])->values();

        return view('admin.rekapitulasi.opd', compact(
            'period',
            'periodLabel',
            'startDate',
            'endDate',
            'tasksInPeriod',
            'opdPerformance',
            'detailedAssignments',
            'totalOpds',
            'totalTugasDiterbitkan',
            'totalDisposisi',
            'totalDiterima',
            'totalMenunggu',
            'totalRevisi',
            'totalBelum',
            'totalTerlambat',
            'overallComplianceRate'
        ));
    }

    /**
     * Display a detailed performance recapitulation report for internal Bidang staff.
     */
    public function bidang(Request $request): View
    {
        $period = $request->input('period', '30_days');
        $now = Carbon::now();

        if ($period === 'this_month') {
            $startDate = $now->copy()->startOfMonth()->startOfDay();
            $endDate = $now->copy()->endOfMonth()->endOfDay();
            $periodLabel = 'Bulan Ini ('.$now->translatedFormat('F Y').')';
        } elseif ($period === 'prev_month') {
            $startDate = $now->copy()->subMonth()->startOfMonth()->startOfDay();
            $endDate = $now->copy()->subMonth()->endOfMonth()->endOfDay();
            $periodLabel = 'Bulan Lalu ('.$now->copy()->subMonth()->translatedFormat('F Y').')';
        } elseif ($period === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
            $periodLabel = 'Periode '.$startDate->translatedFormat('d M Y').' s/d '.$endDate->translatedFormat('d M Y');
        } else {
            $period = '30_days';
            $startDate = $now->copy()->subDays(30)->startOfDay();
            $endDate = $now->copy()->endOfDay();
            $periodLabel = '1 Bulan Terakhir ('.$startDate->translatedFormat('d M Y').' s/d '.$endDate->translatedFormat('d M Y').')';
        }

        // Fetch internal penugasan within period
        $penugasanInPeriod = Penugasan::with(['pemberi', 'penerima'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_kegiatan', [$startDate, $endDate])
                    ->orWhereBetween('batas_laporan', [$startDate, $endDate])
                    ->orWhereBetween('created_at', [$startDate, $endDate]);
            })
            ->latest('tanggal_kegiatan')
            ->get();

        $penugasanIds = $penugasanInPeriod->pluck('id');

        // Fetch all assigned recipient pivots in this period
        $penerimaEntries = PenugasanPenerima::with(['penugasan.pemberi', 'user'])
            ->whereIn('penugasan_id', $penugasanIds)
            ->get();

        $groupedByUser = $penerimaEntries->groupBy('user_id');

        // Fetch all Bidang members
        $anggotaBidangList = User::where(function ($q) {
            $q->where('role', 'anggota_bidang')
                ->orWhere('bidang', 'ekop');
        })
            ->where('role', '!=', 'super_admin')
            ->orderBy('name')
            ->get();

        // Calculate performance for each Bidang member
        $bidangPerformance = $anggotaBidangList->map(function (User $member) use ($groupedByUser) {
            $entries = $groupedByUser->get($member->id, collect());
            $totalKegiatan = $entries->count();

            $dikonfirmasi = $entries->where('status_konfirmasi', 'dikonfirmasi')->count();
            $ditolak = $entries->where('status_konfirmasi', 'ditolak')->count();
            $menungguKonfirmasi = $entries->filter(function ($e) {
                return empty($e->status_konfirmasi) || $e->status_konfirmasi === 'menunggu_konfirmasi';
            })->count();

            $selesai = $entries->where('status_penugasan', 'selesai')->count();
            $sedang = $entries->where('status_penugasan', 'sedang_dikerjakan')->count();
            $belum = $entries->where('status_penugasan', 'belum_dikerjakan')->count();

            $laporanSudah = $entries->where('status_laporan', 'sudah_dilaporkan')->count();
            $laporanBelum = $entries->where('status_laporan', '!=', 'sudah_dilaporkan')->count();

            $konfirmasiRate = $totalKegiatan > 0 ? round(($dikonfirmasi / $totalKegiatan) * 100, 1) : null;
            $penyelesaianRate = $totalKegiatan > 0 ? round(($selesai / $totalKegiatan) * 100, 1) : null;
            $pelaporanRate = $totalKegiatan > 0 ? round(($laporanSudah / $totalKegiatan) * 100, 1) : null;

            // Combined productivity score: 50% completion + 50% report submission
            $skorProduktivitas = $totalKegiatan > 0
                ? round((($selesai * 50 + $laporanSudah * 50) / $totalKegiatan), 1)
                : null;

            if ($totalKegiatan === 0) {
                $predikat = 'Belum Ada Penugasan';
                $badgeClass = 'bg-slate-100 text-slate-700 border-slate-300';
            } elseif ($skorProduktivitas >= 90) {
                $predikat = 'Sangat Produktif & Disiplin';
                $badgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-300';
            } elseif ($skorProduktivitas >= 75) {
                $predikat = 'Produktif & Baik';
                $badgeClass = 'bg-indigo-100 text-indigo-800 border-indigo-300';
            } elseif ($skorProduktivitas >= 60) {
                $predikat = 'Cukup';
                $badgeClass = 'bg-amber-100 text-amber-800 border-amber-300';
            } else {
                $predikat = 'Perlu Peningkatan';
                $badgeClass = 'bg-rose-100 text-rose-800 border-rose-300';
            }

            return [
                'user' => $member,
                'total_kegiatan' => $totalKegiatan,
                'dikonfirmasi' => $dikonfirmasi,
                'ditolak' => $ditolak,
                'menunggu_konfirmasi' => $menungguKonfirmasi,
                'selesai' => $selesai,
                'sedang' => $sedang,
                'belum' => $belum,
                'laporan_sudah' => $laporanSudah,
                'laporan_belum' => $laporanBelum,
                'konfirmasi_rate' => $konfirmasiRate,
                'penyelesaian_rate' => $penyelesaianRate,
                'pelaporan_rate' => $pelaporanRate,
                'skor_produktivitas' => $skorProduktivitas,
                'predikat' => $predikat,
                'badge_class' => $badgeClass,
                'entries' => $entries,
            ];
        });

        // Sort: Members with assignments first by productivity score descending
        $bidangPerformance = $bidangPerformance->sort(function ($a, $b) {
            if ($a['total_kegiatan'] > 0 && $b['total_kegiatan'] === 0) {
                return -1;
            }
            if ($a['total_kegiatan'] === 0 && $b['total_kegiatan'] > 0) {
                return 1;
            }
            if ($a['skor_produktivitas'] !== $b['skor_produktivitas']) {
                return ($b['skor_produktivitas'] ?? -1) <=> ($a['skor_produktivitas'] ?? -1);
            }

            return strcmp($a['user']->name, $b['user']->name);
        })->values();

        // Global KPI Totals
        $totalAnggota = $anggotaBidangList->count();
        $totalKegiatan = $penugasanInPeriod->count();
        $totalDisposisiPersonel = $penerimaEntries->count();
        $totalDikonfirmasi = $penerimaEntries->where('status_konfirmasi', 'dikonfirmasi')->count();
        $totalDitolak = $penerimaEntries->where('status_konfirmasi', 'ditolak')->count();
        $totalSelesai = $penerimaEntries->where('status_penugasan', 'selesai')->count();
        $totalSedang = $penerimaEntries->where('status_penugasan', 'sedang_dikerjakan')->count();
        $totalBelum = $penerimaEntries->where('status_penugasan', 'belum_dikerjakan')->count();
        $totalLaporanSudah = $penerimaEntries->where('status_laporan', 'sudah_dilaporkan')->count();
        $totalLaporanBelum = $penerimaEntries->where('status_laporan', '!=', 'sudah_dilaporkan')->count();

        $overallPenyelesaianRate = $totalDisposisiPersonel > 0
            ? round(($totalSelesai / $totalDisposisiPersonel) * 100, 1)
            : 0;

        $overallPelaporanRate = $totalDisposisiPersonel > 0
            ? round(($totalLaporanSudah / $totalDisposisiPersonel) * 100, 1)
            : 0;

        $overallProductivityRate = $totalDisposisiPersonel > 0
            ? round((($totalSelesai * 50 + $totalLaporanSudah * 50) / $totalDisposisiPersonel), 1)
            : 0;

        // Detailed assignment records list sorted by date descending
        $detailedEntries = $penerimaEntries->sortByDesc(function ($entry) {
            return $entry->penugasan->tanggal_kegiatan ?? $entry->created_at;
        })->values();

        return view('admin.rekapitulasi.bidang', compact(
            'period',
            'periodLabel',
            'startDate',
            'endDate',
            'penugasanInPeriod',
            'bidangPerformance',
            'detailedEntries',
            'totalAnggota',
            'totalKegiatan',
            'totalDisposisiPersonel',
            'totalDikonfirmasi',
            'totalDitolak',
            'totalSelesai',
            'totalSedang',
            'totalBelum',
            'totalLaporanSudah',
            'totalLaporanBelum',
            'overallPenyelesaianRate',
            'overallPelaporanRate',
            'overallProductivityRate'
        ));
    }
}
