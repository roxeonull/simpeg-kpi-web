<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cutis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->enum('jenis_cuti', ['tahunan', 'sakit', 'melahirkan', 'lainnya'])->default('tahunan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->unsignedInteger('jumlah_hari');
            $table->text('alasan')->nullable();
            $table->string('lampiran')->nullable();
            $table->enum('status_atasan', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_atasan')->nullable();
            $table->foreignId('atasan_pemroses_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('atasan_diproses_pada')->nullable();
            $table->enum('status_hr', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_hr')->nullable();
            $table->foreignId('hr_pemroses_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hr_diproses_pada')->nullable();
            $table->enum('status', ['menunggu_atasan', 'menunggu_hr', 'disetujui', 'ditolak'])->default('menunggu_atasan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cutis');
    }
};
