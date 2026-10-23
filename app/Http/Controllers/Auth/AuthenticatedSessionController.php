<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = User::where('email', $request->email)->first();

        // 1. Bersihkan seluruh sesi yang telah kadaluarsa dari database
        $lifetimeMinutes = (int) config('session.lifetime', 35);
        $expiredCutoff = now()->subMinutes($lifetimeMinutes)->timestamp;
        DB::table('sessions')->where('last_activity', '<', $expiredCutoff)->delete();

        // 2. Jika ada penanda server baru dihidupkan, bersihkan sesi yang aktif sebelum server dinyalakan
        $serverBootFile = storage_path('framework/server_booted_at');
        if (! app()->environment('testing') && file_exists($serverBootFile)) {
            $bootTime = (int) @file_get_contents($serverBootFile);
            if ($bootTime > 0) {
                DB::table('sessions')->where('last_activity', '<', $bootTime)->delete();
            }
        }

        // 3. Cek apakah pengguna benar-benar memiliki sesi lain yang masih aktif
        $hasOtherSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
            ->where('last_activity', '>=', $expiredCutoff)
            ->exists();

        if ($hasOtherSessions) {
            $request->session()->put('pending_login', [
                'email' => $request->email,
                'password' => $request->password,
                'remember' => $request->boolean('remember'),
            ]);

            return redirect()->route('login.conflict');
        }

        Auth::attempt($request->only('email', 'password'), $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Display the login conflict view.
     */
    public function conflict(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('pending_login')) {
            return redirect()->route('login');
        }

        return view('auth.login-conflict');
    }

    /**
     * Force login and log out other devices.
     */
    public function forceLogin(Request $request): RedirectResponse
    {
        if (! $request->session()->has('pending_login')) {
            return redirect()->route('login');
        }

        $credentials = $request->session()->pull('pending_login');

        Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $credentials['remember']);

        Auth::logoutOtherDevices($credentials['password']);

        // Bersihkan sisa-sisa sesi lama di database agar tidak menjadi ghost session
        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', '!=', session()->getId())
            ->delete();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Pastikan seluruh jejak sesi user dihapus dari database saat logout manual
        if ($userId) {
            DB::table('sessions')->where('user_id', $userId)->delete();
        }

        return redirect('/');
    }
}
