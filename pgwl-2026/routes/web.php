<?php

use App\Http\Controllers\LahanPertanianController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PertanianController;
use App\Http\Controllers\PertanianCreateController;
use Illuminate\Support\Facades\Route;


//Route::get('/', function () {
   // return view('welcome');
//})->name('home');

Route::get('/', [PageController::class, 'landingpage'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/peta', [PageController::class, 'peta'])->name('peta');
});

Route::get('/geojson-kecamatan', [PageController::class, 'geojsonKecamatan'])
    ->name('geojson.kecamatan');

    Route::get(
    '/geojson-kecamatan-tahun/{tahun}',
    [PageController::class, 'geojsonKecamatanTahun']
)->name('geojson.kecamatan.tahun');

/* EDIT */
Route::get('/map-edit-pertanian/{nama}', [PertanianController::class, 'edit'])
    ->name('pertanian.edit');

Route::post('/map-edit-pertanian/{nama}', [PertanianController::class, 'update'])
    ->name('pertanian.update');

/* CREATE */
Route::get('/pertanian/create/{kecamatan}', [PertanianCreateController::class, 'create'])
    ->name('pertanian.create');

Route::post('/pertanian/store', [PertanianCreateController::class, 'store'])
    ->name('pertanian.store');

    Route::get(
    '/geojson-kecamatan/{tahun}',
    [PageController::class, 'geojsonKecamatan']
)->name('geojson.kecamatan');

Route::get('/daftar-tahun',
    [PageController::class, 'daftarTahun']
);

Route::get('/tahun-pertanian',
    [PageController::class,'getTahun']);

    Route::get('/tahun', [PageController::class, 'daftarTahun'])
    ->name('tahun');

Route::get('/tabel', [PageController::class, 'tabel'])->name('tabel');

// Tabel (public read)
Route::get('/tabel', [PageController::class, 'tabel'])->name('tabel');

// API endpoint for table view
Route::get('/api/pertanian-data', [PageController::class, 'pertanianData']);

// ============================================================
// AUTH ROUTES — data management requires login
// ============================================================

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');

    // EDIT
    Route::get('/map-edit-pertanian/{nama}',  [PertanianController::class, 'edit'])
        ->name('pertanian.edit');

    Route::post('/map-edit-pertanian/{nama}', [PertanianController::class, 'update'])
        ->name('pertanian.update');

    // CREATE
    Route::get('/pertanian/create/{kecamatan}', [PertanianCreateController::class, 'create'])
        ->name('pertanian.create');

    Route::post('/pertanian/store', [PertanianCreateController::class, 'store'])
        ->name('pertanian.store');

    // DELETE
    Route::delete('/pertanian/delete/{kecamatan}/{tahun}', [PertanianController::class, 'destroy'])
        ->name('pertanian.delete');
});

Route::get('/layer-check/{tahun}', [PageController::class, 'checkLayer']);

Route::delete('/tabel/{tahun}/{kecamatan}', [PageController::class, 'destroy']);

Route::get(
    '/lahan-pertanian',
    [LahanPertanianController::class,'index']
);

Route::post(
    '/lahan-pertanian',
    [LahanPertanianController::class,'store']
);

Route::post(
    '/lahan-pertanian/{id}',
    [LahanPertanianController::class,'update']
);

Route::get(
    '/map-edit-lahan/{id}',
    [LahanPertanianController::class, 'editMap']
);

Route::get(
    '/geojson-lahan/{id}',
    [LahanPertanianController::class, 'geojson']
)->name('geojson.lahan');

Route::patch(
    '/lahan-pertanian/{id}',
    [LahanPertanianController::class, 'update']
)->name('lahan.update');

Route::delete(
    '/lahan-pertanian/{id}',
    [LahanPertanianController::class,'destroy']
)->name('lahan.destroy');






// ============================================================
// AUTH (Breeze/Jetstream generated)
// ============================================================
require __DIR__ . '/auth.php';


// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

//require __DIR__.'/settings.php';
