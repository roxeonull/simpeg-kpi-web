<?php

use App\Console\Commands\CatatAlpaOtomatis;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Catat Alpa Otomatis — dijalankan setiap jam mulai pukul 14:00 hingga 23:59.
 *
 * Mengapa range ini:
 *   - Shift 1 (06:00–14:00): batas alpa di jam 14:00
 *   - Normal   (08:30–16:00): batas alpa di jam 16:00
 *   - Shift 2  (14:00–22:00): batas alpa di jam 22:00
 *   - Shift 3  (22:00–06:00): batas alpa hari BERIKUTNYA jam 06:00 → ditangani
 *                              oleh run pagi hari (00:30 dan 06:30)
 *
 * Command idempotent: jika record sudah ada, pegawai di-skip.
 */
Schedule::command('absensi:catat-alpa')
    ->hourlyAt(5)           // Tiap jam, 5 menit setelah jam bulat (mis. 14:05, 15:05, ...)
    ->between('14:00', '23:59')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/alpa-otomatis.log'));

// Run tambahan di pagi hari untuk menangkap shift 3 yang berakhir jam 06:00
Schedule::command('absensi:catat-alpa')
    ->dailyAt('06:30')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/alpa-otomatis.log'));

