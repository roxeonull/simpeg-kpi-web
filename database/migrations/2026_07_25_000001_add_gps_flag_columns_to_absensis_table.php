<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Apakah Position.isMocked = true saat presensi masuk (dari geolocator)
            // null = data tidak tersedia (presensi manual oleh admin / versi app lama)
            $table->boolean('is_mock_location')->nullable()->after('longitude_keluar');

            // Nilai accuracy GPS dalam meter saat presensi masuk (Position.accuracy)
            // null = data tidak tersedia
            $table->decimal('gps_accuracy', 8, 2)->nullable()->after('is_mock_location');

            // true jika salah satu indikasi mencurigakan terdeteksi — perlu review Admin
            // false (default) = tidak ada indikasi; null = data tidak tersedia (presensi lama)
            $table->boolean('flag_review')->default(false)->after('gps_accuracy');

            // Deskripsi flag-flag yang terdeteksi, diisi backend, ditampilkan sebagai tooltip
            // Format: teks yang dipisah newline atau koma
            $table->text('catatan_flag')->nullable()->after('flag_review');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['is_mock_location', 'gps_accuracy', 'flag_review', 'catatan_flag']);
        });
    }
};
