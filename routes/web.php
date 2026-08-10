<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\CutiAnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PengajuanPerubahanDataController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RiwayatPelatihanController;
use App\Http\Controllers\RiwayatPendidikanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JadwalShiftController;
use App\Http\Controllers\CutiWorkflowController;
use App\Http\Controllers\DinasLuarWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Modul Data Pegawai — Admin/HR only
    Route::middleware('role:admin')->group(function () {
        // Kelola User
        Route::resource('user', UserController::class)->names('user');
        Route::post('/user/{user}/reset-password', [UserController::class, 'resetPassword'])->name('user.reset-password');
        Route::patch('/user/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('user.toggle-active');

        Route::resource('pegawai', PegawaiController::class)->except(['show'])->names('pegawai');
        Route::post('/pegawai/{pegawai}/pendidikan', [RiwayatPendidikanController::class, 'store'])->name('pendidikan.store');
        Route::delete('/pendidikan/{riwayatPendidikan}', [RiwayatPendidikanController::class, 'destroy'])->name('pendidikan.destroy');

        Route::post('/pegawai/{pegawai}/pelatihan', [RiwayatPelatihanController::class, 'store'])->name('pelatihan.store');
        Route::patch('/pelatihan/{riwayatPelatihan}/verifikasi', [RiwayatPelatihanController::class, 'verifikasi'])->name('pelatihan.verifikasi');
        Route::delete('/pelatihan/{riwayatPelatihan}', [RiwayatPelatihanController::class, 'destroy'])->name('pelatihan.destroy');

        Route::get('/absensi/tambah', [AbsensiController::class, 'create'])->name('absensi.create');
        Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');

        Route::patch('/cuti/{cuti}/setujui-hr', [CutiController::class, 'approveHr'])->name('cuti.approve-hr');
        Route::patch('/cuti/{cuti}/tolak-hr', [CutiController::class, 'rejectHr'])->name('cuti.reject-hr');

        Route::get('/cuti/workflows', [CutiWorkflowController::class, 'index'])->name('cuti.workflows');
        Route::post('/cuti/workflows', [CutiWorkflowController::class, 'store'])->name('cuti.workflows.store');
        Route::put('/cuti/workflows/{workflow}', [CutiWorkflowController::class, 'update'])->name('cuti.workflows.update');
        Route::delete('/cuti/workflows/{workflow}', [CutiWorkflowController::class, 'destroy'])->name('cuti.workflows.destroy');

        Route::get('/pengajuan-perubahan', [PengajuanPerubahanDataController::class, 'index'])->name('pengajuan-perubahan.index');
        Route::patch('/pengajuan-perubahan/{pengajuan}/setujui', [PengajuanPerubahanDataController::class, 'setujui'])->name('pengajuan-perubahan.setujui');
        Route::patch('/pengajuan-perubahan/{pengajuan}/tolak', [PengajuanPerubahanDataController::class, 'tolak'])->name('pengajuan-perubahan.tolak');

        Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/pegawai/excel', [ReportController::class, 'pegawaiExcel'])->name('laporan.pegawai.excel');
        Route::get('/laporan/pegawai/pdf', [ReportController::class, 'pegawaiPdf'])->name('laporan.pegawai.pdf');
        Route::get('/laporan/absensi/excel', [ReportController::class, 'absensiExcel'])->name('laporan.absensi.excel');
        Route::get('/laporan/absensi/pdf', [ReportController::class, 'absensiPdf'])->name('laporan.absensi.pdf');
        Route::get('/laporan/cuti/excel', [ReportController::class, 'cutiExcel'])->name('laporan.cuti.excel');
        Route::get('/laporan/cuti/pdf', [ReportController::class, 'cutiPdf'])->name('laporan.cuti.pdf');
        Route::get('/laporan/ketidakhadiran/excel', [ReportController::class, 'ketidakhadiranExcel'])->name('laporan.ketidakhadiran.excel');
        Route::get('/laporan/ketidakhadiran/pdf', [ReportController::class, 'ketidakhadiranPdf'])->name('laporan.ketidakhadiran.pdf');

        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
        Route::post('/pengaturan/unit', [PengaturanController::class, 'storeUnit'])->name('pengaturan.unit.store');
        Route::delete('/pengaturan/unit/{unit}', [PengaturanController::class, 'destroyUnit'])->name('pengaturan.unit.destroy');
        Route::post('/pengaturan/jabatan', [PengaturanController::class, 'storeJabatan'])->name('pengaturan.jabatan.store');
        Route::delete('/pengaturan/jabatan/{jabatan}', [PengaturanController::class, 'destroyJabatan'])->name('pengaturan.jabatan.destroy');

        Route::post('/pengaturan/bentuk-pelatihan', [PengaturanController::class, 'storeBentukPelatihan'])->name('pengaturan.bentuk-pelatihan.store');
        Route::delete('/pengaturan/bentuk-pelatihan/{bentuk}', [PengaturanController::class, 'destroyBentukPelatihan'])->name('pengaturan.bentuk-pelatihan.destroy');
        Route::post('/pengaturan/tipe-kursus', [PengaturanController::class, 'storeTipeKursus'])->name('pengaturan.tipe-kursus.store');
        Route::delete('/pengaturan/tipe-kursus/{tipe}', [PengaturanController::class, 'destroyTipeKursus'])->name('pengaturan.tipe-kursus.destroy');
        Route::post('/pengaturan/jenis-kursus', [PengaturanController::class, 'storeJenisKursus'])->name('pengaturan.jenis-kursus.store');
        Route::delete('/pengaturan/jenis-kursus/{jenis}', [PengaturanController::class, 'destroyJenisKursus'])->name('pengaturan.jenis-kursus.destroy');
        Route::post('/pengaturan/instansi', [PengaturanController::class, 'storeInstansi'])->name('pengaturan.instansi.store');
        Route::delete('/pengaturan/instansi/{instansi}', [PengaturanController::class, 'destroyInstansi'])->name('pengaturan.instansi.destroy');

        Route::post('/pengaturan/jenis-cuti', [PengaturanController::class, 'storeJenisCuti'])->name('pengaturan.jenis-cuti.store');
        Route::delete('/pengaturan/jenis-cuti/{jenisCuti}', [PengaturanController::class, 'destroyJenisCuti'])->name('pengaturan.jenis-cuti.destroy');
        Route::post('/pengaturan/jenis-ketidakhadiran', [PengaturanController::class, 'storeJenisKetidakhadiran'])->name('pengaturan.jenis-ketidakhadiran.store');
        Route::delete('/pengaturan/jenis-ketidakhadiran/{jenisKetidakhadiran}', [PengaturanController::class, 'destroyJenisKetidakhadiran'])->name('pengaturan.jenis-ketidakhadiran.destroy');

        Route::post('/pengaturan/status-shift', [PengaturanController::class, 'storeStatusShift'])->name('pengaturan.status-shift.store');
        Route::delete('/pengaturan/status-shift/{statusShift}', [PengaturanController::class, 'destroyStatusShift'])->name('pengaturan.status-shift.destroy');

        Route::get('/absensi/shift/import', [JadwalShiftController::class, 'importForm'])->name('absensi.shift.import-form');
        Route::post('/absensi/shift/import/parse', [JadwalShiftController::class, 'importParse'])->name('absensi.shift.import-parse');
        Route::get('/absensi/shift/import/preview', [JadwalShiftController::class, 'importPreview'])->name('absensi.shift.import-preview');
        Route::post('/absensi/shift/import/commit', [JadwalShiftController::class, 'importCommit'])->name('absensi.shift.import-commit');
        Route::get('/absensi/shift/{shift}', [JadwalShiftController::class, 'index'])->name('absensi.shift.index');
        Route::patch('/absensi/shift/update-cell', [JadwalShiftController::class, 'updateCell'])->name('absensi.shift.update-cell');
        Route::post('/absensi/shift/tambah-pegawai', [JadwalShiftController::class, 'tambahPegawai'])->name('absensi.shift.tambah-pegawai');

        // Hapus Data Periode: AJAX hitung + DELETE bulk
        Route::get('/absensi/shift/{shift}/hitung-entri', [JadwalShiftController::class, 'hitungEntri'])->name('absensi.shift.hitung-entri');
        Route::delete('/absensi/shift/{shift}/hapus-periode', [JadwalShiftController::class, 'hapusPeriode'])->name('absensi.shift.hapus-periode');
    });

    // Diakses bersama Admin & Atasan (dengan scoping tim pada controller)
    Route::middleware('role:admin,atasan')->group(function () {
        Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');

        Route::get('/pelatihan', [RiwayatPelatihanController::class, 'index'])->name('pelatihan.index');
        Route::get('/pelatihan/pegawai/{pegawai}', [RiwayatPelatihanController::class, 'pegawai'])->name('pelatihan.pegawai');
        Route::get('/pelatihan/{riwayatPelatihan}', [RiwayatPelatihanController::class, 'show'])->name('pelatihan.show');

        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');

        Route::get('/cuti', [CutiController::class, 'index'])->name('cuti.index');
        Route::get('/cuti/kalender', [CutiAnalyticsController::class, 'kalender'])->name('cuti.kalender');
        Route::get('/cuti/analitik', [CutiAnalyticsController::class, 'analitik'])->name('cuti.analitik');
        Route::get('/cuti/rekomendasi', [CutiAnalyticsController::class, 'rekomendasi'])->name('cuti.rekomendasi');
        Route::get('/cuti/tambah', [CutiController::class, 'create'])->name('cuti.create');
        Route::post('/cuti', [CutiController::class, 'store'])->name('cuti.store');
        Route::get('/cuti/{cuti}', [CutiController::class, 'show'])->name('cuti.show');
        Route::patch('/cuti/{cuti}/setujui-atasan', [CutiController::class, 'approveAtasan'])->name('cuti.approve-atasan');
        Route::patch('/cuti/{cuti}/tolak-atasan', [CutiController::class, 'rejectAtasan'])->name('cuti.reject-atasan');
        Route::patch('/cuti/{cuti}/setujui-step', [CutiController::class, 'approveStep'])->name('cuti.approve-step');
        Route::patch('/cuti/{cuti}/tolak-step', [CutiController::class, 'rejectStep'])->name('cuti.reject-step');

        Route::get('/dinas-luar', [DinasLuarWebController::class, 'index'])->name('dinas-luar.index');
        Route::patch('/dinas-luar/{id}/setujui', [DinasLuarWebController::class, 'setujui'])->name('dinas-luar.setujui');
        Route::patch('/dinas-luar/{id}/tolak', [DinasLuarWebController::class, 'tolak'])->name('dinas-luar.tolak');
    });
});
