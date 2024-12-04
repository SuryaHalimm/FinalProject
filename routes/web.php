<?php

use App\Http\Controllers\KotaController;
use App\Http\Controllers\NegaraController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WisatawanController;
use App\Models\Negara;
use Illuminate\Support\Facades\Route;


Route::get('/', [KotaController::class, 'viewLandingPage'])->name('welcome');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/kota', [KotaController::class, 'view'])->name('kota.view');
    Route::get('/kota/kota-info/{id_enc}', [KotaController::class, 'kotaInfo'])->name('kota.info');

    Route::prefix('admin')->name('admin.')->group(function (){
        Route::get('/dashboard', [WisatawanController::class, 'dashBoard'])->name('dashboard.index');
        Route::get('/dashboard/country/{id}', [WisatawanController::class, 'getCountry'])->name('dashboard.country');
        Route::get('/upload-csv', [WisatawanController::class, 'showUploadForm'])->name('upload.form');
        Route::post('/upload-csv', [WisatawanController::class, 'importCsv'])->name('upload.csv');
        Route::get('/report', [WisatawanController::class, 'report'])->name('report.index');
        Route::post('/report/search', [WisatawanController::class, 'search'])->name('report.search');
        Route::post('/report/download', [WisatawanController::class, 'downloadPDF'])->name('report.download');
        // Route untuk menyimpan data kota baru (store)
        Route::post('/kota/store', [KotaController::class, 'store'])->name('kota.store');
        // Route untuk mengedit data kota (edit & update)
        Route::get('/kota/{id}/edit', [KotaController::class, 'edit'])->name('kota.edit'); // Optional, karena edit menggunakan modal
        Route::put('/kota/update/{id}', [KotaController::class, 'update'])->name('kota.update');
        // Route untuk menghapus data kota (destroy)
        Route::delete('/kota/{id}', [KotaController::class, 'destroy'])->name('kota.destroy');
    });
});

require __DIR__.'/auth.php';
