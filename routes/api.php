<?php

use App\Http\Controllers\Api\AbsensiApiController;
use App\Http\Controllers\Api\AtasanApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CutiApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\RiwayatApiController;
use App\Http\Controllers\Api\FcmTokenController;
use Illuminate\Support\Facades\Route;

// ── Auth (tanpa middleware) ──────────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);
Route::post('/lupa-password', [AuthController::class, 'lupaPassword']);
Route::post('/forgot-password/request-otp', [AuthController::class, 'requestOtp']);
Route::post('/forgot-password/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/forgot-password/reset-password', [AuthController::class, 'resetPasswordWithOtp']);

// ── Endpoint yang butuh token Sanctum ───────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth & Device FCM
    Route::post('/logout',    [AuthController::class, 'logout']);
    Route::get('/me',         [AuthController::class, 'me']);
    Route::post('/fcm-token', [FcmTokenController::class, 'store']);

    // Dashboard
    Route::get('/dashboard', [DashboardApiController::class, 'index']);

    // Absensi
    Route::get('/absensi',            [AbsensiApiController::class, 'index']);
    Route::get('/absensi/hari-ini',   [AbsensiApiController::class, 'hariIni']);
    Route::post('/absensi/masuk',     [AbsensiApiController::class, 'masuk']);
    Route::post('/absensi/keluar',    [AbsensiApiController::class, 'keluar']);

    // Cuti
    Route::get('/cuti',              [CutiApiController::class, 'index']);
    Route::get('/cuti/saldo',         [CutiApiController::class, 'saldo']);
    Route::get('/cuti/kalender-tim',  [CutiApiController::class, 'kalenderTim']);
    Route::post('/cuti',              [CutiApiController::class, 'store']);
    Route::get('/cuti/{cuti}',        [CutiApiController::class, 'show']);

    // Approval Cuti Atasan
    Route::get('/atasan/cuti-tim',           [AtasanApiController::class, 'cutiTim']);
    Route::patch('/atasan/cuti/{cuti}/setujui', [AtasanApiController::class, 'setujuiCuti']);
    Route::patch('/atasan/cuti/{cuti}/tolak',   [AtasanApiController::class, 'tolakCuti']);

    // Riwayat
    Route::get('/riwayat/pendidikan',        [RiwayatApiController::class, 'pendidikan']);
    Route::get('/riwayat/pelatihan',         [RiwayatApiController::class, 'pelatihan']);
    Route::get('/riwayat/pelatihan/options', [RiwayatApiController::class, 'pelatihanOptions']);
    Route::post('/riwayat/pelatihan',        [RiwayatApiController::class, 'storePelatihan']);

    // Pengajuan Perubahan Data (Profile)
    Route::get('/profil/lengkap',       [ProfileApiController::class, 'detailPegawai']);
    Route::get('/pengajuan-perubahan',  [ProfileApiController::class, 'pengajuanPerubahan']);
    Route::post('/pengajuan-perubahan', [ProfileApiController::class, 'ajukanPerubahan']);
    Route::post('/ubah-password',       [ProfileApiController::class, 'ubahPassword']);

    // Jadwal Shift
    Route::get('/jadwal-shift/hari-ini', [\App\Http\Controllers\Api\JadwalShiftApiController::class, 'hariIni']);
    Route::get('/jadwal-shift',          [\App\Http\Controllers\Api\JadwalShiftApiController::class, 'index']);
});
