<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

/* ============================================================
   AUTH ROUTES (guest only)
   ============================================================ */
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/* ============================================================
   PROTECTED DASHBOARD ROUTES (auth required)
   ============================================================ */
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard.index');
    });

    Route::get('/countries', function () {
        return view('dashboard.countries');
    });

    Route::get('/weather', function () {
        return view('dashboard.weather');
    });

    Route::get('/ports', function () {
        return view('dashboard.ports');
    });

    Route::get('/news', function () {
        return view('dashboard.news');
    });

    Route::get('/settings', function () {
        return view('dashboard.settings');
    });

    Route::get('/currency', function () {
        return view('dashboard.currency');
    });

    Route::get('/compare', function () {
        return view('dashboard.compare');
    });

    Route::get('/analytics', function () {
        return view('dashboard.analytics');
    });

    // Admin only route
    Route::get('/admin', function () {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Admin.');
        }
        return view('dashboard.admin');
    });
});