<?php

namespace Database\Seeders;

use App\Models\BentukPelatihan;
use App\Models\TipeKursus;
use App\Models\JenisKursus;
use App\Models\Instansi;
use Illuminate\Database\Seeder;

class TrainingMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Bentuk Pelatihan
        $bp1 = BentukPelatihan::firstOrCreate(['nama_bentuk' => 'Pendidikan']);
        $bp2 = BentukPelatihan::firstOrCreate(['nama_bentuk' => 'Pelatihan']);
        $bp3 = BentukPelatihan::firstOrCreate(['nama_bentuk' => 'Kursus']);
        $bp4 = BentukPelatihan::firstOrCreate(['nama_bentuk' => 'Bimbingan Teknis']);
        $bp5 = BentukPelatihan::firstOrCreate(['nama_bentuk' => 'Sosialisasi']);
        $bp6 = BentukPelatihan::firstOrCreate(['nama_bentuk' => 'Seminar / Webinar']);

        // 2. Tipe Kursus
        TipeKursus::firstOrCreate(['bentuk_pelatihan_id' => $bp1->id, 'nama_tipe' => 'Tugas Belajar']);
        TipeKursus::firstOrCreate(['bentuk_pelatihan_id' => $bp1->id, 'nama_tipe' => 'Izin Belajar']);

        TipeKursus::firstOrCreate(['bentuk_pelatihan_id' => $bp2->id, 'nama_tipe' => 'Pelatihan Struktural']);
        TipeKursus::firstOrCreate(['bentuk_pelatihan_id' => $bp2->id, 'nama_tipe' => 'Pelatihan Fungsional']);
        TipeKursus::firstOrCreate(['bentuk_pelatihan_id' => $bp2->id, 'nama_tipe' => 'Pelatihan Teknis']);
        TipeKursus::firstOrCreate(['bentuk_pelatihan_id' => $bp2->id, 'nama_tipe' => 'Pelatihan Sosio-Kultural']);

        TipeKursus::firstOrCreate(['bentuk_pelatihan_id' => $bp3->id, 'nama_tipe' => 'Kursus Singkat (Short Course)']);
        TipeKursus::firstOrCreate(['bentuk_pelatihan_id' => $bp3->id, 'nama_tipe' => 'Kursus Panjang (Long Course)']);

        TipeKursus::firstOrCreate(['bentuk_pelatihan_id' => $bp4->id, 'nama_tipe' => 'Bimtek Teknis']);
        TipeKursus::firstOrCreate(['bentuk_pelatihan_id' => $bp4->id, 'nama_tipe' => 'Bimtek Administratif']);

        // 3. Jenis Kursus
        JenisKursus::firstOrCreate(['nama_jenis' => 'Kepemimpinan']);
        JenisKursus::firstOrCreate(['nama_jenis' => 'Fungsional']);
        JenisKursus::firstOrCreate(['nama_jenis' => 'Teknis Kepegawaian']);
        JenisKursus::firstOrCreate(['nama_jenis' => 'Teknis Penyiaran']);
        JenisKursus::firstOrCreate(['nama_jenis' => 'SPBE / Teknologi Informasi']);
        JenisKursus::firstOrCreate(['nama_jenis' => 'Manajerial']);
        JenisKursus::firstOrCreate(['nama_jenis' => 'Sosio-Kultural']);

        // 4. Instansi
        Instansi::firstOrCreate(['nama_instansi' => 'Komisi Penyiaran Indonesia']);
        Instansi::firstOrCreate(['nama_instansi' => 'Lembaga Administrasi Negara']);
        Instansi::firstOrCreate(['nama_instansi' => 'Kementerian Komunikasi dan Digital']);
        Instansi::firstOrCreate(['nama_instansi' => 'Badan Kepegawaian Negara']);
        Instansi::firstOrCreate(['nama_instansi' => 'Kementerian Keuangan']);
        Instansi::firstOrCreate(['nama_instansi' => 'Pusdiklat Instansi Pemerintah']);
    }
}
