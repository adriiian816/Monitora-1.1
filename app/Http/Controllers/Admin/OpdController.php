<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\TugasOpd;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OpdController extends Controller
{
    /**
     * Display a listing of OPDs.
     */
    public function index(Request $request): View
    {
        $query = Opd::with('users')
            ->withCount([
                'tugasOpd as total_tugas',
                'tugasOpd as total_selesai' => function ($q) {
                    $q->where('status', 'diterima');
                },
                'tugasOpd as total_terlambat' => function ($q) {
                    $q->where('status', 'terlambat');
                },
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_opd', 'like', "%{$search}%")
                    ->orWhere('kode_opd', 'like', "%{$search}%");
            });
        }

        $opds = $query->orderBy('nama_opd')->paginate(10)->withQueryString();

        $totalOpds = Opd::count();
        $totalActiveOpds = Opd::where('is_active', true)->count();
        $totalStaff = User::where('role', 'opd')->count();
        $totalTugas = TugasOpd::count();

        return view('admin.opd.index', compact(
            'opds',
            'totalOpds',
            'totalActiveOpds',
            'totalStaff',
            'totalTugas'
        ));
    }

    /**
     * Show the form for creating a new OPD.
     */
    public function create(): View
    {
        return view('admin.opd.create');
    }

    /**
     * Store a newly created OPD in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_opd' => ['required', 'string', 'max:50', 'unique:opds,kode_opd'],
            'nama_opd' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $opd = Opd::create([
            'kode_opd' => strtoupper($validated['kode_opd']),
            'nama_opd' => $validated['nama_opd'],
            'alamat' => $validated['alamat'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.opd.index')
            ->with('success', "Data instansi OPD '{$opd->nama_opd}' berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the specified OPD.
     */
    public function edit(Opd $opd): View
    {
        $opd->load('users');

        return view('admin.opd.edit', compact('opd'));
    }

    /**
     * Update the specified OPD in storage.
     */
    public function update(Request $request, Opd $opd): RedirectResponse
    {
        $validated = $request->validate([
            'kode_opd' => ['required', 'string', 'max:50', Rule::unique('opds', 'kode_opd')->ignore($opd->id)],
            'nama_opd' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $opd->update([
            'kode_opd' => strtoupper($validated['kode_opd']),
            'nama_opd' => $validated['nama_opd'],
            'alamat' => $validated['alamat'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.opd.index')
            ->with('success', "Data OPD '{$opd->nama_opd}' berhasil diperbarui.");
    }

    /**
     * Remove the specified OPD from storage.
     */
    public function destroy(Opd $opd): RedirectResponse
    {
        $namaOpd = $opd->nama_opd;

        // Clean up file submissions on disk if any
        foreach ($opd->tugasOpd as $tugasOpd) {
            if ($tugasOpd->submission && $tugasOpd->submission->file_path) {
                if (Storage::disk('public')->exists($tugasOpd->submission->file_path)) {
                    Storage::disk('public')->delete($tugasOpd->submission->file_path);
                }
            }
        }

        // Delete associated user accounts for this OPD
        $opd->users()->delete();

        // Delete OPD (cascading deletes tugas_opd and submissions)
        $opd->delete();

        return redirect()->route('admin.opd.index')
            ->with('success', "Data OPD '{$namaOpd}' berhasil dihapus.");
    }

    /**
     * Remove multiple OPDs from storage.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'opd_ids' => ['required', 'array', 'min:1'],
            'opd_ids.*' => ['exists:opds,id'],
        ]);

        $opds = Opd::with(['tugasOpd.submission', 'users'])->whereIn('id', $validated['opd_ids'])->get();

        foreach ($opds as $opd) {
            foreach ($opd->tugasOpd as $tugasOpd) {
                if ($tugasOpd->submission && $tugasOpd->submission->file_path) {
                    if (Storage::disk('public')->exists($tugasOpd->submission->file_path)) {
                        Storage::disk('public')->delete($tugasOpd->submission->file_path);
                    }
                }
            }
            $opd->users()->delete();
            $opd->delete();
        }

        $count = $opds->count();

        return redirect()->route('admin.opd.index')
            ->with('success', "Berhasil menghapus {$count} data instansi OPD.");
    }
}
