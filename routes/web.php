<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KategoriKendaraanController;
use App\Http\Controllers\Admin\KendaraanController;
use App\Http\Controllers\Admin\TarifParkirController;
use App\Http\Controllers\Admin\AreaParkirController;
use App\Http\Controllers\Admin\LogAktivitasController;

// Petugas Controllers
use App\Http\Controllers\Petugas\TransaksiController as PetugasTransaksiController;

// Owner Controllers
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;

// =============================================
// Redirect Home ke Login
// =============================================
Route::get('/', function () {
    return redirect()->route('login');
});

// =============================================
// Autentikasi (Terbuka)
// =============================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Lupa Password / Reset Password
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.forgot');
Route::post('/forgot-password', [AuthController::class, 'resetPassword'])->name('password.reset.post');

// =============================================
// Grup ADMIN
// =============================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // CRUD User
    Route::resource('users', UserController::class);

    // CRUD Kategori Kendaraan
    Route::resource('kategori', KategoriKendaraanController::class);

    // CRUD Kendaraan
    Route::resource('kendaraan', KendaraanController::class);

    // CRUD Tarif Parkir
    Route::resource('tarif', TarifParkirController::class);

    // CRUD Area Parkir
    Route::resource('area', AreaParkirController::class);

    // Log Aktivitas (Read Only)
    Route::get('/log', [LogAktivitasController::class, 'index'])->name('log.index');
});

// =============================================
// Grup PETUGAS
// =============================================
Route::middleware(['auth', 'role:petugas'])
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {

    // Transaksi Kendaraan Masuk
    Route::get('/masuk', [PetugasTransaksiController::class, 'masukForm'])->name('transaksi.masuk');
    Route::post('/masuk', [PetugasTransaksiController::class, 'masukStore'])->name('transaksi.masuk.store');

    // Transaksi Kendaraan Keluar
    Route::get('/keluar', [PetugasTransaksiController::class, 'keluarForm'])->name('transaksi.keluar');
    Route::post('/keluar/{transaksi}', [PetugasTransaksiController::class, 'keluarProcess'])->name('transaksi.keluar.process');

    // Cetak Struk
    Route::get('/struk/masuk/{transaksi}', [PetugasTransaksiController::class, 'strukMasuk'])->name('struk.masuk');
    Route::get('/struk/keluar/{transaksi}', [PetugasTransaksiController::class, 'strukKeluar'])->name('struk.keluar');
});

// =============================================
// Grup OWNER
// =============================================
Route::middleware(['auth', 'role:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {

    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/rekap/print', [OwnerDashboardController::class, 'printRekap'])->name('rekap.print');
});
