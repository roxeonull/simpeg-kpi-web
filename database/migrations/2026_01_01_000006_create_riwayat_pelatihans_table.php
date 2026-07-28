<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('nama_pelatihan');
            $table->string('penyelenggara')->nullable();
            $table->date('tanggal');
            $table->unsignedInteger('durasi_jp')->default(0);
            $table->enum('kategori', ['struktural', 'fungsional', 'teknis', 'latsar', 'lainnya'])->default('lainnya');
            $table->string('sertifikat')->nullable();
            $table->enum('status_verifikasi', ['menunggu', 'terverifikasi', 'ditolak'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_pelatihans');
    }
};
