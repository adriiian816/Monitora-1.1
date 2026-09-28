<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of all user accounts.
     */
    public function index(Request $request): View
    {
        $query = User::with(['opd', 'handledOpds']);

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search by name, email, or OPD name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('opd', function ($opdQ) use ($search) {
                        $opdQ->where('nama_opd', 'like', "%{$search}%")
                            ->orWhere('kode_opd', 'like', "%{$search}%");
                    });
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        // Metrics for summary cards
        $totalUsers = User::count();
        $totalSuperAdmin = User::where('role', 'super_admin')->count();
        $totalAdmin = User::where('role', 'admin')->count();
        $totalOpd = User::where('role', 'opd')->count();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'totalSuperAdmin',
            'totalAdmin',
            'totalOpd'
        ));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $opds = Opd::active()->orderBy('nama_opd')->get();

        return view('admin.users.create', compact('opds'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'in:super_admin,admin,opd'],
            'opd_id' => [
                'nullable',
                Rule::requiredIf(fn () => $request->role === 'opd'),
                'exists:opds,id',
            ],
            'handled_opd_ids' => ['nullable', 'array'],
            'handled_opd_ids.*' => ['exists:opds,id'],
        ], [
            'opd_id.required_if' => 'Instansi OPD wajib dipilih untuk akun dengan peran Staf OPD.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_hp' => $validated['role'] === 'opd' ? ($validated['no_hp'] ?? null) : null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'opd_id' => $validated['role'] === 'opd' ? $validated['opd_id'] : null,
            'email_verified_at' => now(),
        ]);

        if ($user->role === 'admin' && ! empty($validated['handled_opd_ids'])) {
            $user->handledOpds()->sync($validated['handled_opd_ids']);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Akun '{$user->name}' ({$user->role_label}) berhasil dibuat.");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $user->load('handledOpds');
        $opds = Opd::active()->orderBy('nama_opd')->get();

        return view('admin.users.edit', compact('user', 'opds'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'role' => ['required', 'string', 'in:super_admin,admin,opd'],
            'opd_id' => [
                'nullable',
                Rule::requiredIf(fn () => $request->role === 'opd'),
                'exists:opds,id',
            ],
            'handled_opd_ids' => ['nullable', 'array'],
            'handled_opd_ids.*' => ['exists:opds,id'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ], [
            'opd_id.required_if' => 'Instansi OPD wajib dipilih untuk akun dengan peran Staf OPD.',
        ]);

        // Guard against removing the last super admin or self-demotion
        if ($user->id === Auth::id() && $user->role === 'super_admin' && $validated['role'] !== 'super_admin') {
            return back()->withErrors(['role' => 'Anda tidak dapat menurunkan peran akun Admin yang sedang aktif digunakan.']);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->opd_id = $validated['role'] === 'opd' ? $validated['opd_id'] : null;
        $user->no_hp = $validated['role'] === 'opd' ? ($validated['no_hp'] ?? null) : null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($user->role === 'admin') {
            $user->handledOpds()->sync($validated['handled_opd_ids'] ?? []);
        } else {
            $user->handledOpds()->detach();
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Data akun '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang masuk.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Akun '{$name}' berhasil dihapus.");
    }
}
