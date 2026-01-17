<?php

use Illuminate\Support\Facades\Route;
use Mews\Captcha\CaptchaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminFaskesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApotekDashboardController;
use App\Http\Controllers\ApotekLaporanController;
use App\Http\Controllers\FktpPatientController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PrbController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\FktpDashboardController;

Route::get('captcha/{config?}', [CaptchaController::class, 'getCaptcha'])->name('captcha');

Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/refresh-captcha', [AuthController::class, 'refreshCaptcha'])->name('refresh.captcha');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['multiauth:admin,rumah_sakit', 'role:admin,rumah_sakit'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

    Route::prefix('admin')->name('admin.')->middleware(['auth:admin', 'role:admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/faskes', [AdminFaskesController::class, 'index'])->name('faskes.index');
        Route::post('/faskes', [AdminFaskesController::class, 'store'])->name('faskes.store');
        Route::get('/faskes/{id}/detail', [AdminFaskesController::class, 'showDetail'])->name('faskes.detail');
        Route::get('/faskes/{id}', [AdminFaskesController::class, 'show'])->name('faskes.show');
        Route::put('/faskes/{id}', [AdminFaskesController::class, 'update'])->name('faskes.update');
        Route::delete('/faskes/{id}', [AdminFaskesController::class, 'destroy'])->name('faskes.destroy');
    });

    Route::prefix('pasien')->name('pasien.')->group(function () {
        Route::get('/', [PasienController::class, 'index'])->name('index');
        Route::get('/create', [PasienController::class, 'create'])->name('create');
        Route::post('/', [PasienController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PasienController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PasienController::class, 'update'])->name('update');
        Route::get('/{id}', [PasienController::class, 'show'])->name('show');
        Route::get('/{id}/add-diagnosis', [PasienController::class, 'addDiagnosis'])->name('addDiagnosis');
        Route::post('/{id}/store-diagnosis', [PasienController::class, 'storeDiagnosis'])->name('storeDiagnosis');
        Route::delete('/{id}', [PasienController::class, 'destroy'])->name('destroy');

        Route::get('/diagnosa/file/{id}', [PasienController::class, 'showFile'])->name('diagnosa.file');
        Route::post('/diagnosa/store', [PasienController::class, 'storeDiagnosa'])->name('diagnosa.store');
        Route::put('/diagnosa/{id_diagnosa}/update', [PasienController::class, 'updateDiagnosa'])->name('diagnosa.update');

        Route::post('/obat/store', [PasienController::class, 'storeObat'])->name('obat.store');
        Route::put('/obat/{id_obat}/update', [PasienController::class, 'updateObat'])->name('obat.update');

   // Route::get('/search-fktp', [RelasiFktpApotekController::class, 'search'])->name('search.fktp');

    });
});

Route::middleware(['auth:fktp', 'role:fktp'])->group(function () {

    Route::get('/fktp/dashboard', [FktpDashboardController::class, 'index'])->name('fktp.dashboard');

    Route::prefix('fktp')->name('fktp.')->group(function () {
        Route::get('/patients', [FktpPatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/{id}', [FktpPatientController::class, 'show'])->name('patients.show');
        Route::get('/patients/{id}/edit', [FktpPatientController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{id}', [FktpPatientController::class, 'update'])->name('patients.update');

        Route::get('/pasien/tambah', [FktpPatientController::class, 'create'])->name('pasien.create');
        Route::post('/pasien/simpan', [FktpPatientController::class, 'store'])->name('pasien.store');

   // Route::get('/search-fktp', [RelasiFktpApotekController::class, 'search'])->name('search.fktp');

        Route::get('/notifications', [FktpPatientController::class, 'fktpNotifications'])->name('fktp.notifications');
        Route::get('/export-reminder', [FktpDashboardController::class, 'exportReminder'])->name('export.reminder');

    });
});

Route::middleware(['auth:apotek', 'role:apotek', 'EnsureApotekGuard'])->group(function () {

    Route::get('/apotek/dashboard', [ApotekDashboardController::class, 'index'])->name('apotek.dashboard');
    Route::prefix('apotek')->name('apotek.')->group(function () {
        Route::get('/patients', [FktpPatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/{id}', [FktpPatientController::class, 'show'])->name('patients.show');
        Route::get('/patients/{id}/edit', [FktpPatientController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{id}', [FktpPatientController::class, 'update'])->name('patients.update');

        Route::get('/pasien/tambah', [FktpPatientController::class, 'create'])->name('pasien.create');
        Route::post('/pasien/simpan', [FktpPatientController::class, 'store'])->name('pasien.store');
        // Route::get('/search-fktp', [RelasiFktpApotekController::class, 'search'])->name('search.fktp');
        
        Route::post('/diagnosa/{idDiagnosa}/klaim', [FktpPatientController::class, 'klaimDiagnosa'])->name('diagnosa.klaim');
        Route::get('/obat/riwayat-klaim', [FktpPatientController::class, 'riwayatObatKlaim'])->name('obat.riwayat-klaim');
        Route::get('/laporan-obat-keluar', [ApotekLaporanController::class, 'laporanObatKeluar'])->name('laporan-obat-keluar');
        Route::get('/laporan-obat-keluar/export', [ApotekLaporanController::class, 'exportLaporanObatKeluar'])->name('laporan-obat-keluar.export');
        Route::get('/notifications', [ApotekDashboardController::class, 'notifications'])->name('apotek.notifications');
        Route::get('/export-reminder', [ApotekDashboardController::class, 'exportReminder'])->name('export.reminder');
        Route::post('/diagnosa/{idDiagnosa}/upload-pdf', [FktpPatientController::class, 'uploadKlaimPdf'])->name('diagnosa.upload_pdf');
    });
});

Route::middleware(['multiauth:admin,rumah_sakit,fktp,apotek'])->group(function () {
    Route::get('/exports/laporan', [LaporanController::class, 'index'])->name('exports.laporan');
    Route::get('/exports/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');
    Route::get('/exports/download-pdf/{filename}', [LaporanController::class, 'downloadPdf'])->name('laporan.download.pdf');

    // Setting Account
    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::put('/setting/update-profile', [SettingController::class, 'updateProfile'])->name('setting.updateProfile');
    Route::put('/setting/update-password', [SettingController::class, 'updatePassword'])->name('setting.updatePassword');
});


