<?php

use App\Http\Controllers\Admin\BiayaHarianController;
use App\Http\Controllers\Admin\HargaProdukMitraBulananController;
use App\Http\Controllers\Admin\KalenderOperasionalController;
use App\Http\Controllers\Admin\LaporanPenjualanMitraController;
use App\Http\Controllers\Admin\MitraController;
use App\Http\Controllers\Admin\OperasiHarianController;
use App\Http\Controllers\Admin\PembayaranMitraHarianController;
use App\Http\Controllers\Admin\PengirimanMitraController;
use App\Http\Controllers\Admin\ProduksiHarianController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('password', [App\Http\Controllers\Auth\PasswordController::class, 'showForm'])->name('password.form');
    Route::put('password', [App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');
    Route::get('/', [OperasiHarianController::class, 'index']);
    Route::get('operasi', [OperasiHarianController::class, 'index'])->name('operasi.index');

    Route::resource('mitra', MitraController::class)->except(['show']);
    Route::resource('produk', ProdukController::class)->except(['show']);
    Route::resource('harga', HargaProdukMitraBulananController::class)->except(['show']);
    Route::resource('kalender', KalenderOperasionalController::class)->except(['show']);

    Route::resource('produksi', ProduksiHarianController::class)->except(['show']);
    Route::post('produksi/{produksi}/detail', [ProduksiHarianController::class, 'storeDetail'])->name('produksi.detail.store');
    Route::get('produksi/{produksi}/detail/{detail}/edit', [ProduksiHarianController::class, 'editDetail'])->name('produksi.detail.edit');
    Route::put('produksi/{produksi}/detail/{detail}', [ProduksiHarianController::class, 'updateDetail'])->name('produksi.detail.update');
    Route::delete('produksi/{produksi}/detail/{detail}', [ProduksiHarianController::class, 'destroyDetail'])->name('produksi.detail.destroy');

    Route::get('pengiriman/matriks', [PengirimanMitraController::class, 'matriks'])->name('pengiriman.matriks');
    Route::post('pengiriman/matriks', [PengirimanMitraController::class, 'matriksStore'])->name('pengiriman.matriks.store');
    Route::get('pengiriman/harian', [PengirimanMitraController::class, 'harian'])->name('pengiriman.harian');
    Route::post('pengiriman/harian', [PengirimanMitraController::class, 'harianStore'])->name('pengiriman.harian.store');
    Route::resource('pengiriman', PengirimanMitraController::class)->except(['show']);
    Route::post('pengiriman/{pengiriman}/detail', [PengirimanMitraController::class, 'storeDetail'])->name('pengiriman.detail.store');
    Route::get('pengiriman/{pengiriman}/detail/{detail}/edit', [PengirimanMitraController::class, 'editDetail'])->name('pengiriman.detail.edit');
    Route::put('pengiriman/{pengiriman}/detail/{detail}', [PengirimanMitraController::class, 'updateDetail'])->name('pengiriman.detail.update');
    Route::delete('pengiriman/{pengiriman}/detail/{detail}', [PengirimanMitraController::class, 'destroyDetail'])->name('pengiriman.detail.destroy');

    Route::get('laporan/matriks', [LaporanPenjualanMitraController::class, 'matriks'])->name('laporan.matriks');
    Route::post('laporan/matriks', [LaporanPenjualanMitraController::class, 'matriksStore'])->name('laporan.matriks.store');
    Route::resource('laporan', LaporanPenjualanMitraController::class)->except(['show']);
    Route::post('laporan/{laporan}/detail', [LaporanPenjualanMitraController::class, 'storeDetail'])->name('laporan.detail.store');
    Route::get('laporan/{laporan}/detail/{detail}/edit', [LaporanPenjualanMitraController::class, 'editDetail'])->name('laporan.detail.edit');
    Route::put('laporan/{laporan}/detail/{detail}', [LaporanPenjualanMitraController::class, 'updateDetail'])->name('laporan.detail.update');
    Route::delete('laporan/{laporan}/detail/{detail}', [LaporanPenjualanMitraController::class, 'destroyDetail'])->name('laporan.detail.destroy');

    Route::resource('biaya', BiayaHarianController::class)->except(['show']);
    Route::post('biaya/{biaya}/detail', [BiayaHarianController::class, 'storeDetail'])->name('biaya.detail.store');
    Route::get('biaya/{biaya}/detail/{detail}/edit', [BiayaHarianController::class, 'editDetail'])->name('biaya.detail.edit');
    Route::put('biaya/{biaya}/detail/{detail}', [BiayaHarianController::class, 'updateDetail'])->name('biaya.detail.update');
    Route::delete('biaya/{biaya}/detail/{detail}', [BiayaHarianController::class, 'destroyDetail'])->name('biaya.detail.destroy');

    Route::resource('pembayaran', PembayaranMitraHarianController::class)->except(['show']);

    Route::get('rekap', [RekapController::class, 'dashboard'])->name('rekap.dashboard');
    Route::get('rekap/harian', [RekapController::class, 'index'])->name('rekap.harian');
    Route::get('rekap/piutang', [RekapController::class, 'piutang'])->name('rekap.piutang');
    Route::get('rekap/produk', [RekapController::class, 'produk'])->name('rekap.produk');
    Route::get('rekap/waste', [RekapController::class, 'waste'])->name('rekap.waste');
    Route::get('rekap/laba-rugi', [RekapController::class, 'labaRugi'])->name('rekap.laba-rugi');
    Route::get('rekap/mitra', [RekapController::class, 'mitra'])->name('rekap.mitra');

    Route::middleware('role:owner')->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);
        Route::put('users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
    });
});
