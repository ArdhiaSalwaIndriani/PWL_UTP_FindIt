
<?php

use App\Http\Controllers\Admin as AdminCtrl;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User as UserCtrl;
use Illuminate\Support\Facades\Route;

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

    Route::get('/klaim-saya', [UserCtrl\KlaimController::class, 'index'])->name('klaim.index');
    Route::get('/klaim-saya/barang-tersedia', [UserCtrl\KlaimController::class, 'tersedia'])->name('klaim.tersedia');
    Route::get('/klaim-saya/ajukan/{laporanTemuan}', [UserCtrl\KlaimController::class, 'create'])->name('klaim.create');
    Route::post('/klaim-saya/ajukan/{laporanTemuan}', [UserCtrl\KlaimController::class, 'store'])->name('klaim.store');
    Route::get('/klaim-saya/{klaim}', [UserCtrl\KlaimController::class, 'show'])->name('klaim.show');
    Route::get('/notifikasi', [UserCtrl\NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::patch('/notifikasi/baca-semua', [UserCtrl\NotifikasiController::class, 'tandaiSemuaDibaca'])->name('notifikasi.baca-semua');
    Route::patch('/notifikasi/{notifikasi}/baca', [UserCtrl\NotifikasiController::class, 'tandaiDibaca'])->name('notifikasi.baca');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'peran:admin'])->group(function () {
    Route::get('/dashboard', [AdminCtrl\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('laporan-hilang', AdminCtrl\LaporanHilangController::class)
        ->only(['index', 'show', 'edit', 'update', 'destroy'])
        ->parameters(['laporan-hilang' => 'laporanHilang']);
    Route::patch('laporan-hilang/{laporanHilang}/setujui', [AdminCtrl\LaporanHilangController::class, 'setujui'])->name('laporan-hilang.setujui');
    Route::patch('laporan-hilang/{laporanHilang}/tolak', [AdminCtrl\LaporanHilangController::class, 'tolak'])->name('laporan-hilang.tolak');
    Route::patch('laporan-hilang/{laporanHilang}/status', [AdminCtrl\LaporanHilangController::class, 'ubahStatus'])->name('laporan-hilang.status');

    Route::resource('laporan-temuan', AdminCtrl\LaporanTemuanController::class)
        ->only(['index', 'show', 'edit', 'update', 'destroy'])
        ->parameters(['laporan-temuan' => 'laporanTemuan']);
    Route::patch('laporan-temuan/{laporanTemuan}/setujui', [AdminCtrl\LaporanTemuanController::class, 'setujui'])->name('laporan-temuan.setujui');
    Route::patch('laporan-temuan/{laporanTemuan}/tolak', [AdminCtrl\LaporanTemuanController::class, 'tolak'])->name('laporan-temuan.tolak');
    Route::patch('laporan-temuan/{laporanTemuan}/status', [AdminCtrl\LaporanTemuanController::class, 'ubahStatus'])->name('laporan-temuan.status');

    Route::resource('kategori', AdminCtrl\KategoriController::class)->except('show');
    Route::resource('pengguna', AdminCtrl\PenggunaController::class)->only(['index', 'show', 'edit', 'update']);
    Route::patch('pengguna/{pengguna}/aktif', [AdminCtrl\PenggunaController::class, 'ubahStatusAktif'])->name('pengguna.aktif');

    Route::get('/profil', [UserCtrl\ProfilController::class, 'index'])->name('profil');
    Route::put('/profil', [UserCtrl\ProfilController::class, 'perbarui'])->name('profil.perbarui');
    Route::put('/profil/kata-sandi', [UserCtrl\ProfilController::class, 'ubahKataSandi'])->name('profil.kata-sandi');

    Route::get('klaim', [AdminCtrl\KlaimController::class, 'index'])->name('klaim.index');
    Route::get('klaim/{klaim}', [AdminCtrl\KlaimController::class, 'show'])->name('klaim.show');
    Route::patch('klaim/{klaim}/setujui', [AdminCtrl\KlaimController::class, 'setujui'])->name('klaim.setujui');
    Route::patch('klaim/{klaim}/tolak', [AdminCtrl\KlaimController::class, 'tolak'])->name('klaim.tolak');
    Route::patch('klaim/{klaim}/pengembalian', [AdminCtrl\KlaimController::class, 'konfirmasiPengembalian'])->name('klaim.pengembalian');
});
