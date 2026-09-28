<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\Opd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginLogController extends Controller
{
    /**
     * Display a listing of login and logout activity logs.
     */
    public function index(Request $request): View
    {
        $query = LoginLog::with(['user', 'opd'])->latest('logged_at');

        // Filter by OPD
        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->input('opd_id'));
        }

        // Filter by Action (login / logout)
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        // Filter by Device Type (desktop / mobile / tablet)
        if ($request->filled('device_type')) {
            $query->where('device_type', $request->input('device_type'));
        }

        // Filter by Specific Date
        if ($request->filled('date')) {
            $query->whereDate('logged_at', $request->input('date'));
        }

        // Search across user name, email, OPD, IP, and location
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        $logs = $query->paginate(15)->withQueryString();

        // Summary metrics
        $today = now()->toDateString();
        $totalLoginHariIni = LoginLog::where('action', 'login')->whereDate('logged_at', $today)->count();
        $totalLogoutHariIni = LoginLog::where('action', 'logout')->whereDate('logged_at', $today)->count();
        $totalOpdAktifHariIni = LoginLog::whereNotNull('opd_id')->whereDate('logged_at', $today)->distinct('opd_id')->count('opd_id');
        $totalMobileToday = LoginLog::where('device_type', 'mobile')->whereDate('logged_at', $today)->count();
        $totalDesktopToday = LoginLog::where('device_type', 'desktop')->whereDate('logged_at', $today)->count();

        $opdList = Opd::orderBy('nama_opd')->get(['id', 'nama_opd', 'kode_opd']);

        return view('admin.login-logs.index', compact(
            'logs',
            'opdList',
            'totalLoginHariIni',
            'totalLogoutHariIni',
            'totalOpdAktifHariIni',
            'totalMobileToday',
            'totalDesktopToday'
        ));
    }

    /**
     * Remove the specified log from storage.
     */
    public function destroy(LoginLog $loginLog): RedirectResponse
    {
        $loginLog->delete();

        return back()->with('success', 'Catatan log sesi berhasil dihapus.');
    }

    /**
     * Purge logs older than 30 days.
     */
    public function purge(Request $request): RedirectResponse
    {
        $days = (int) $request->input('days', 30);
        $deleted = LoginLog::where('logged_at', '<', now()->subDays($days))->delete();

        return back()->with('success', "Sebanyak {$deleted} rekaman log sesi yang lebih lama dari {$days} hari berhasil dibersihkan.");
    }
}
