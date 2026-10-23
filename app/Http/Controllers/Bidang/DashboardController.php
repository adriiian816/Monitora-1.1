<?php

namespace App\Http\Controllers\Bidang;

use App\Http\Controllers\Controller;
use App\Models\Penugasan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Anggota Bidang (Staf Bidang Ekop) dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Get penugasan assigned specifically to this anggota bidang
        $penugasanSaya = Penugasan::whereHas('penerima', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })
            ->with(['pemberi', 'penerima' => function ($q) use ($user) {
                $q->where('users.id', $user->id);
            }])
            ->latest('tanggal_kegiatan')
            ->get();

        // Summary counts
        $totalPenugasan = $penugasanSaya->count();
        $totalSelesai = $penugasanSaya->filter(function ($p) {
            return $p->penerima->first()?->pivot?->status_penugasan === 'selesai';
        })->count();
        $totalSedang = $penugasanSaya->filter(function ($p) {
            return $p->penerima->first()?->pivot?->status_penugasan === 'sedang_dikerjakan';
        })->count();
        $totalBelum = $penugasanSaya->filter(function ($p) {
            return $p->penerima->first()?->pivot?->status_penugasan === 'belum_dikerjakan';
        })->count();
        $totalLaporanSudah = $penugasanSaya->filter(function ($p) {
            return $p->penerima->first()?->pivot?->status_laporan === 'sudah_dilaporkan';
        })->count();
        $totalLaporanBelum = $penugasanSaya->filter(function ($p) {
            return $p->penerima->first()?->pivot?->status_laporan === 'belum_dilaporkan';
        })->count();
        $totalDikonfirmasi = $penugasanSaya->filter(function ($p) {
            return $p->penerima->first()?->pivot?->status_konfirmasi === 'dikonfirmasi';
        })->count();
        $totalMenungguKonfirmasi = $penugasanSaya->filter(function ($p) {
            $status = $p->penerima->first()?->pivot?->status_konfirmasi;

            return empty($status) || $status === 'menunggu_konfirmasi';
        })->count();

        // Urgent upcoming activities
        $tugasMendatang = $penugasanSaya->filter(function ($p) {
            return $p->penerima->first()?->pivot?->status_penugasan !== 'selesai';
        })->take(5);

        // Calendar events: internal bidang activities & report deadlines
        $calendarEvents = $penugasanSaya->map(function ($p) {
            return [
                'id' => $p->id,
                'title' => $p->judul,
                'date' => $p->tanggal_kegiatan->format('Y-m-d'),
                'batas_laporan' => $p->batas_laporan ? $p->batas_laporan->format('Y-m-d') : null,
                'status_penugasan' => $p->penerima->first()?->pivot?->status_penugasan,
                'status_laporan' => $p->penerima->first()?->pivot?->status_laporan,
                'status_konfirmasi' => $p->penerima->first()?->pivot?->status_konfirmasi,
                'url' => route('admin.penugasan.show', $p->id),
            ];
        });

        return view('bidang.dashboard', compact(
            'user',
            'penugasanSaya',
            'totalPenugasan',
            'totalSelesai',
            'totalSedang',
            'totalBelum',
            'totalLaporanSudah',
            'totalLaporanBelum',
            'totalDikonfirmasi',
            'totalMenungguKonfirmasi',
            'tugasMendatang',
            'calendarEvents'
        ));
    }
}
