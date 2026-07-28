<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create jenis_cutis
        Schema::create('jenis_cutis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->boolean('potong_saldo_cuti')->default(false);
            $table->timestamps();
        });

        // Seed jenis_cutis
        $now = now();
        DB::table('jenis_cutis')->insert([
            ['nama' => 'Cuti Tahunan', 'potong_saldo_cuti' => true, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Cuti Tahunan Pengecualian', 'potong_saldo_cuti' => false, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Cuti Alasan Penting', 'potong_saldo_cuti' => false, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Cuti Besar', 'potong_saldo_cuti' => false, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Cuti Bersalin Anak Ke-1 s.d 2', 'potong_saldo_cuti' => false, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Cuti Bersalin Anak Ke-3 dst.', 'potong_saldo_cuti' => false, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Cuti di Luar Tanggungan Negara', 'potong_saldo_cuti' => false, 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Sakit/Cuti Sakit', 'potong_saldo_cuti' => false, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 2. Create jenis_ketidakhadirans
        Schema::create('jenis_ketidakhadirans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        // Seed jenis_ketidakhadirans
        $ketidakhadirans = [
            'Dinas Luar',
            'Rapat, Seminar, Konferensi',
            'Pendidikan, Pelatihan',
            'Rawat Inap',
            'Rawat Jalan',
            'Datang Terlambat dan Pulang Cepat',
            'Force Majeur',
            'Libur Berdasarkan SE Sekjen',
            'Tanpa Keterangan',
            'Tugas Belajar Alih Ke Izin Belajar',
            'Tugas Belajar Dengan Tunjangan Hidup',
            'Tugas Belajar Tanpa Tunjangan Hidup'
        ];

        $insertData = array_map(function ($nama) use ($now) {
            return ['nama' => $nama, 'created_at' => $now, 'updated_at' => $now];
        }, $ketidakhadirans);

        DB::table('jenis_ketidakhadirans')->insert($insertData);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_ketidakhadirans');
        Schema::dropIfExists('jenis_cutis');
    }
};
