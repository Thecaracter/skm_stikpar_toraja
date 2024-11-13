<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\JenisPembayaranController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Login Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// User Routes
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::post('/', [UserController::class, 'store'])->name('users.store');
    Route::patch('/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Jenis Pembayaran Routes
Route::prefix('jenis-pembayaran')->group(function () {
    Route::get('/', [JenisPembayaranController::class, 'index'])->name('jenis-pembayaran.index');
    Route::post('/', [JenisPembayaranController::class, 'store'])->name('jenis-pembayaran.store');
    Route::put('/{jenisPembayaran}', [JenisPembayaranController::class, 'update'])->name('jenis-pembayaran.update');
    Route::delete('/{jenisPembayaran}', [JenisPembayaranController::class, 'destroy'])->name('jenis-pembayaran.destroy');
});

// Tagihan Routes
Route::prefix('tagihan')->name('tagihan.')->group(function () {
    Route::get('/', [TagihanController::class, 'index'])->name('index');
    Route::get('/{user}/detail', [TagihanController::class, 'getDetail'])->name('detail');
    Route::get('/jenis-pembayaran/{jenisPembayaran}', [TagihanController::class, 'getJenisPembayaran'])->name('jenis-pembayaran.detail');
    Route::get('/{user}/statistics', [TagihanController::class, 'getStatistics'])->name('statistics');
    Route::post('/{user}', [TagihanController::class, 'store'])->name('store');
    Route::put('/{user}/{tagihan}', [TagihanController::class, 'update'])->name('update');
    Route::delete('/{user}/{tagihan}', [TagihanController::class, 'destroy'])->name('destroy');
});

// Pembayaran Routes
Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
    Route::get('/', [PembayaranController::class, 'index'])->name('index');
    Route::get('/search', [PembayaranController::class, 'search'])->name('search'); // Pindahkan search sebelum route dengan parameter
    Route::get('/{pembayaran}', [PembayaranController::class, 'show'])->name('show');
    Route::get('/bukti/{pembayaran}', [PembayaranController::class, 'showBukti'])->name('bukti');
    Route::post('/{pembayaran}/verifikasi', [PembayaranController::class, 'verifikasi'])->name('verifikasi');
});