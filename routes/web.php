<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AjuanCutiController;

// Rute untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Keluar dari aplikasi
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rute yang butuh login
Route::middleware('auth')->group(function () {
    Route::get('/', [AjuanCutiController::class, 'index'])->name('dashboard');
    Route::post('/ajuan-cuti', [AjuanCutiController::class, 'store'])->name('ajuan-cuti.store');
    Route::put('/ajuan-cuti/{id}', [AjuanCutiController::class, 'update'])->name('ajuan-cuti.update');
    Route::patch('/ajuan-cuti/{id}/status', [AjuanCutiController::class, 'updateStatus'])->name('ajuan-cuti.status');
    Route::delete('/ajuan-cuti/{id}', [AjuanCutiController::class, 'destroy'])->name('ajuan-cuti.destroy');
});
