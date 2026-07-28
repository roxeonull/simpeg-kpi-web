<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Path foto selfie presensi masuk (relatif terhadap storage/app/public)
            // Menggantikan pendekatan lama yang menyisipkan path ke kolom teks keterangan.
            // null = presensi lama (sebelum migration) atau presensi non-mobile (admin manual)
            $table->string('foto_masuk')->nullable()->after('catatan_flag');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn('foto_masuk');
        });
    }
};
