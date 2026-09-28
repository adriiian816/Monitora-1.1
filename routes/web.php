<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LoginLogController as AdminLoginLogController;
use App\Http\Controllers\Admin\OpdController as AdminOpdController;
use App\Http\Controllers\Admin\SubmissionController as AdminSubmissionController;
use App\Http\Controllers\Admin\TugasController as AdminTugasController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\WhatsappLogController as AdminWhatsappLogController;
use App\Http\Controllers\Opd\DashboardController as OpdDashboardController;
use App\Http\Controllers\Opd\SubmissionController as OpdSubmissionController;
use App\Http\Controllers\Opd\TugasController as OpdTugasController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Central dashboard route that redirects based on role
Route::get('/dashboard', function (Request $request) {
    $user = $request->user();
    if ($user->isAdminOrSuperAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('opd.dashboard');
})->middleware(['auth'])->name('dashboard');

// Routes accessible by Super Admin & Admin Bappeda (Tugas, Verifikasi, Monitoring & Logs)
Route::middleware(['auth', 'role:super_admin,admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Tugas Management
    Route::resource('tugas', AdminTugasController::class)->parameters(['tugas' => 'tugas']);
    Route::post('/tugas/{tugas}/broadcast-reminder', [AdminTugasController::class, 'broadcastReminder'])->name('tugas.broadcast-reminder');
    Route::get('/tugas/{tugas}/ics', [AdminTugasController::class, 'downloadIcs'])->name('tugas.ics');

    // Submissions Verification & Download
    Route::get('/submissions/{submission}', [AdminSubmissionController::class, 'show'])->name('submissions.show');
    Route::post('/submissions/{submission}/verify', [AdminSubmissionController::class, 'verify'])->name('submissions.verify');
    Route::get('/submissions/{submission}/download', [AdminSubmissionController::class, 'downloadFile'])->name('submissions.download');

    // WhatsApp Notification Logs
    Route::get('/whatsapp-logs', [AdminWhatsappLogController::class, 'index'])->name('whatsapp-logs.index');
    Route::post('/whatsapp-logs/{log}/resend', [AdminWhatsappLogController::class, 'resend'])->name('whatsapp-logs.resend');
    Route::delete('/whatsapp-logs/{log}', [AdminWhatsappLogController::class, 'destroy'])->name('whatsapp-logs.destroy');
});

// Routes EXCLUSIVE to Super Admin (OPD Data Master & User Accounts Management)
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    // OPD Management
    Route::resource('opd', AdminOpdController::class)->except(['show']);

    // User Management (Admin Bappeda & Staf OPD accounts)
    Route::resource('users', AdminUserController::class)->except(['show']);

    // OPD Session & Activity Logs (Login, Logout, Device, Location)
    Route::get('/login-logs', [AdminLoginLogController::class, 'index'])->name('login-logs.index');
    Route::delete('/login-logs/purge', [AdminLoginLogController::class, 'purge'])->name('login-logs.purge');
    Route::delete('/login-logs/{loginLog}', [AdminLoginLogController::class, 'destroy'])->name('login-logs.destroy');
});

// OPD Routes
Route::middleware(['auth', 'role:opd'])->prefix('opd')->name('opd.')->group(function () {
    Route::get('/dashboard', [OpdDashboardController::class, 'index'])->name('dashboard');

    // Tugas OPD
    Route::get('/tugas', [OpdTugasController::class, 'index'])->name('tugas.index');
    Route::get('/tugas/{tugas}', [OpdTugasController::class, 'show'])->name('tugas.show');
    Route::get('/tugas/{tugas}/ics', [OpdTugasController::class, 'downloadIcs'])->name('tugas.ics');

    // Submission
    Route::post('/tugas/{tugas}/submit', [OpdSubmissionController::class, 'store'])->name('submission.store');
});

// Profile Management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Auto-polling session endpoint
    Route::get('/ping-session', function () {
        return response()->json(['status' => 'ok']);
    })->name('ping.session');
});

require __DIR__.'/auth.php';
