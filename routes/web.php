<?php

use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController; // Pastikan ini ada
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'pimpinan') {
            return redirect()->route('pimpinan.dashboard');
        } else {
            return redirect()->route('overtimes.index');
        }
    }
    return redirect()->route('login');
});

// Routes authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Dashboard redirect berdasarkan role
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'pimpinan') {
            return redirect()->route('pimpinan.dashboard');
        } else {
            return redirect()->route('overtimes.index');
        }
    })->name('dashboard');

    // Routes untuk semua user
    Route::resource('overtimes', OvertimeController::class);
    
    Route::post('/overtimes/{overtime}/approve', [OvertimeController::class, 'approve'])
        ->name('overtimes.approve');
        
    Route::post('/overtimes/{overtime}/reject', [OvertimeController::class, 'reject'])
        ->name('overtimes.reject');
        
    Route::get('/overtimes-report', [OvertimeController::class, 'report'])
        ->name('overtimes.report');

    // Routes untuk download report
    Route::get('/overtimes/download/pdf', [OvertimeController::class, 'downloadReport'])
        ->name('overtimes.download.pdf');
        
    Route::get('/overtimes/download/excel', [OvertimeController::class, 'downloadExcel'])
        ->name('overtimes.download.excel');

    // Routes khusus admin
    Route::get('/admin/dashboard', [OvertimeController::class, 'adminDashboard'])
        ->name('admin.dashboard');

    // Routes khusus pimpinan
    Route::get('/pimpinan/dashboard', [OvertimeController::class, 'pimpinanDashboard'])
        ->name('pimpinan.dashboard');

    // Routes management user (hanya untuk admin)
    Route::resource('users', UserController::class);
});