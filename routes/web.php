
<?php

use App\Http\Controllers\Admin as AdminCtrl;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User as UserCtrl;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::redirect('/', '/masuk');

Route::middleware('guest')->group(function () {
    Route::get('/daftar', [AuthController::class, 'formDaftar'])->name('daftar');
    Route::post('/daftar', [AuthController::class, 'daftar']);
    Route::get('/masuk', [AuthController::class, 'formMasuk'])->name('masuk');
    Route::post('/masuk', [AuthController::class, 'masuk']);
});

Route::post('/keluar', [AuthController::class, 'keluar'])->middleware('auth')->name('keluar');

Route::middleware(['auth', 'peran:user'])->group(function () {
    Route::get('/beranda', [UserCtrl\BerandaController::class, 'index'])->name('beranda');
    Route::get('/jelajah/hilang', [UserCtrl\BerandaController::class, 'jelajahHilang'])->name('jelajah.hilang');
    Route::get('/jelajah/hilang/{laporanHilang}', [UserCtrl\BerandaController::class, 'detailHilang'])->name('jelajah.hilang.detail');
    Route::get('/jelajah/temuan', [UserCtrl\BerandaController::class, 'jelajahTemuan'])->name('jelajah.temuan');
    Route::get('/jelajah/temuan/{laporanTemuan}', [UserCtrl\BerandaController::class, 'detailTemuan'])->name('jelajah.temuan.detail');

    Route::resource('laporan-hilang', UserCtrl\LaporanHilangController::class)
        ->parameters(['laporan-hilang' => 'laporanHilang']);
    Route::resource('laporan-temuan', UserCtrl\LaporanTemuanController::class)
        ->parameters(['laporan-temuan' => 'laporanTemuan']);
    Route::patch('/laporan-temuan/{laporanTemuan}/klaim/{klaim}/konfirmasi', [UserCtrl\LaporanTemuanController::class, 'konfirmasiKecocokan'])->name('laporan-temuan.klaim.kepemilikan');
    Route::patch('/laporan-temuan/{laporanTemuan}/klaim/{klaim}/penyerahan', [UserCtrl\LaporanTemuanController::class, 'catatPenyerahan'])->name('laporan-temuan.klaim.penyerahan');

    Route::get('/profil', [UserCtrl\ProfilController::class, 'index'])->name('profil');
    Route::put('/profil', [UserCtrl\ProfilController::class, 'perbarui'])->name('profil.perbarui');
    Route::put('/profil/kata-sandi', [UserCtrl\ProfilController::class, 'ubahKataSandi'])->name('profil.kata-sandi');

   
});

    Route::prefix('admin')->name('admin.')->middleware(['auth', 'peran:admin'])->group(function () {

    
    });
