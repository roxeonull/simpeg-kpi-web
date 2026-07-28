<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Jam pulang yang diizinkan (hasil kalkulasi flexible work hours)
            // null = belum presensi masuk / tidak relevan
            $table->time('jam_pulang_diizinkan')->nullable()->after('jam_keluar');

            // Menit pengurangan jam kerja akibat pulang cepat (sebelum jam_pulang_diizinkan)
            // null = tidak pulang cepat / belum presensi keluar
            $table->unsignedSmallInteger('menit_pengurangan_jam_kerja')->nullable()->after('jam_pulang_diizinkan');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['jam_pulang_diizinkan', 'menit_pengurangan_jam_kerja']);
        });
    }
};
