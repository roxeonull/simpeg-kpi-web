<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('riwayat_pelatihans', function (Blueprint $table) {
            $table->date('tanggal_akhir')->nullable()->after('tanggal');
            $table->foreignId('instansi_id')->nullable()->after('penyelenggara')->constrained('instansis')->nullOnDelete();
            $table->string('bidang_sdm_spbe')->nullable()->after('durasi_jp');
            $table->string('no_sertifikat')->nullable()->after('sertifikat');
            $table->date('tanggal_sertifikat')->nullable()->after('no_sertifikat');
            
            $table->foreignId('bentuk_pelatihan_id')->nullable()->after('kategori')->constrained('bentuk_pelatihans')->nullOnDelete();
            $table->foreignId('tipe_kursus_id')->nullable()->after('bentuk_pelatihan_id')->constrained('tipe_kursuses')->nullOnDelete();
            $table->foreignId('jenis_kursus_id')->nullable()->after('tipe_kursus_id')->constrained('jenis_kursuses')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_pelatihans', function (Blueprint $table) {
            $table->dropForeign(['jenis_kursus_id']);
            $table->dropForeign(['tipe_kursus_id']);
            $table->dropForeign(['bentuk_pelatihan_id']);
            $table->dropForeign(['instansi_id']);
            
            $table->dropColumn([
                'tanggal_akhir',
                'instansi_id',
                'bidang_sdm_spbe',
                'no_sertifikat',
                'tanggal_sertifikat',
                'bentuk_pelatihan_id',
                'tipe_kursus_id',
                'jenis_kursus_id'
            ]);
        });
    }
};
