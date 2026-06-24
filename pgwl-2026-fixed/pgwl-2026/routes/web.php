<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PertanianController;
use App\Http\Controllers\PertanianCreateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Publik
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'landingpage'])->name('home');
Route::get('/peta', [PageController::class, 'peta'])->name('peta');
Route::get('/tabel', [PageController::class, 'tabel'])->name('tabel');

/*
|--------------------------------------------------------------------------
| API GeoJSON & Data Pendukung Peta
|--------------------------------------------------------------------------
*/
Route::get('/geojson-kecamatan', [PageController::class, 'geojsonKecamatan'])
    ->name('geojson.kecamatan');

Route::get('/geojson-kecamatan-tahun/{tahun}', [PageController::class, 'geojsonKecamatanTahun'])
    ->name('geojson.kecamatan.tahun');

Route::get('/tahun-pertanian', [PageController::class, 'daftarTahun'])
    ->name('tahun');

/*
|--------------------------------------------------------------------------
| CRUD Data Pertanian (wajib login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/pertanian/create/{kecamatan}', [PertanianCreateController::class, 'create'])
        ->name('pertanian.create');

    Route::post('/pertanian/store', [PertanianCreateController::class, 'store'])
        ->name('pertanian.store');

    Route::get('/map-edit-pertanian/{nama}', [PertanianController::class, 'edit'])
        ->name('pertanian.edit');

    Route::post('/map-edit-pertanian/{nama}', [PertanianController::class, 'update'])
        ->name('pertanian.update');
});

/*
|--------------------------------------------------------------------------
| Dashboard (wajib login)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
