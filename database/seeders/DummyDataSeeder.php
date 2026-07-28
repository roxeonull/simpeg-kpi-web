<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Pegawai;
use App\Models\PengajuanPerubahanData;
use App\Models\RiwayatPelatihan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $pegawais = Pegawai::all();
        if ($pegawais->isEmpty()) {
            $this->command->warn('Tidak ada data pegawai. Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        $adminUser = User::where('role', 'admin')->first() ?? User::first();

        $fields = [
            'no_hp' => [
                'lama' => fn() => '0812' . rand(10000000, 99999999),
                'baru' => fn() => '0857' . rand(10000000, 99999999),
            ],
            'email_pribadi' => [
                'lama' => fn() => 'old.email' . rand(10, 99) . '@gmail.com',
                'baru' => fn() => 'new.email' . rand(100, 999) . '@gmail.com',
            ],
            'alamat' => [
                'lama' => fn() => 'Jl. Kebon Sirih No. ' . rand(1, 50) . ', Jakarta Pusat',
                'baru' => fn() => 'Jl. Sudirman Indah Blok C No. ' . rand(1, 100) . ', Jakarta Selatan',
            ],
            'status_marital' => [
                'lama' => fn() => 'Lajang',
                'baru' => fn() => 'Menikah',
            ],
            'golongan_darah' => [
                'lama' => fn() => '—',
                'baru' => fn() => ['A', 'B', 'AB', 'O'][rand(0, 3)],
            ],
            'agama' => [
                'lama' => fn() => '—',
                'baru' => fn() => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha'][rand(0, 4)],
            ],
            'hobi' => [
                'lama' => fn() => 'Membaca',
                'baru' => fn() => ['Olahraga & Bulutangkis', 'Fotografi', 'Bersepeda & Trail', 'Bermain Musik'][rand(0, 3)],
            ],
            'nama_panggilan' => [
                'lama' => fn() => '—',
                'baru' => fn() => ['Budi', 'Rina', 'Dedi', 'Maya', 'Nisa', 'Afif', 'Sigit', 'Roni'][rand(0, 7)],
            ],
        ];

        $alasanCuti = [
            'Mudik ke kampung halaman keluarga',
            'Acara pernikahan saudara kandung',
            'Kondisi kesehatan kurang baik / demam tinggi',
            'Mendampingi anggota keluarga yang dirawat di rumah sakit',
            'Mengurus pendaftaran sekolah anak',
            'Keperluan keluarga mendesak dan renovasi rumah',
            'Istirahat setelah menyelesaikan proyek penyiaran',
            'Menghadiri wisuda anggota keluarga',
        ];

        $namaDiklat = [
            'Pelatihan Pengawasan Isi Siaran Berbasis Digital',
            'Bimtek Legal Drafting Peraturan Penyiaran',
            'Workshop Literasi Media dan Analisis Konten',
            'Diklat Kepemimpinan dan Manajemen Kepegawaian',
            'Pelatihan Cyber Security dan Keamanan Data',
            'Bimtek Audit Kinerja dan Akuntabilitas Publik',
            'Workshop Pengolahan Data Statistik Penyiaran',
            'Diklat Etika Pelayanan Publik dan Komunikasi Efektif',
        ];

        $penyelenggaraDiklat = [
            'Pusdiklat Kominfo & KPI',
            'Lembaga Administrasi Negara (LAN)',
            'Pusat Studi Komunikasi UI',
            'Badan Kepegawaian Negara (BKN)',
            'Badan Siber dan Sandi Negara (BSSN)',
            'Dewan Pers & KPI Pusat',
        ];

        $statuses = ['menunggu', 'disetujui', 'ditolak'];

        // -------------------------------------------------------------
        // 1. Seed ~30 Data Pengajuan Perubahan Data
        // -------------------------------------------------------------
        $this->command->info('Seeding 30 Pengajuan Perubahan Data...');
        foreach (range(1, 30) as $i) {
            $pegawai = $pegawais->random();
            $fieldKey = array_rand($fields);
            $fieldConfig = $fields[$fieldKey];
            $status = $statuses[rand(0, 2)];

            $createdAt = Carbon::now()->subDays(rand(0, 25))->subHours(rand(1, 12));

            PengajuanPerubahanData::create([
                'pegawai_id' => $pegawai->id,
                'field' => $fieldKey,
                'nilai_lama' => ($fieldConfig['lama'])(),
                'nilai_baru' => ($fieldConfig['baru'])(),
                'status' => $status,
                'catatan_admin' => $status === 'ditolak' ? 'Dokumen pendukung / bukti perubahan belum dilampirkan secara lengkap.' : null,
                'diproses_oleh' => $status !== 'menunggu' ? $adminUser->id : null,
                'created_at' => $createdAt,
                'updated_at' => $status !== 'menunggu' ? $createdAt->copy()->addHours(2) : $createdAt,
            ]);
        }

        // -------------------------------------------------------------
        // 2. Seed ~30 Data Pengajuan Cuti
        // -------------------------------------------------------------
        $this->command->info('Seeding 30 Pengajuan Cuti...');
        $jenisCutiList = ['tahunan', 'sakit', 'melahirkan', 'lainnya'];
        foreach (range(1, 30) as $i) {
            $pegawai = $pegawais->random();
            $jenis = $jenisCutiList[rand(0, count($jenisCutiList) - 1)];
            $durasi = rand(1, 5);
            $start = Carbon::now()->addDays(rand(-20, 15));
            $end = (clone $start)->addDays($durasi - 1);
            $statusChoice = rand(0, 3);

            $statusAtasan = 'disetujui';
            $statusHr = 'disetujui';
            $status = 'disetujui';

            if ($statusChoice === 0) {
                $statusAtasan = 'menunggu';
                $statusHr = 'menunggu';
                $status = 'menunggu_atasan';
            } elseif ($statusChoice === 1) {
                $statusAtasan = 'disetujui';
                $statusHr = 'menunggu';
                $status = 'menunggu_hr';
            } elseif ($statusChoice === 2) {
                $statusAtasan = 'ditolak';
                $statusHr = 'menunggu';
                $status = 'ditolak';
            }

            $createdAt = Carbon::now()->subDays(rand(1, 30));

            Cuti::create([
                'pegawai_id' => $pegawai->id,
                'jenis_cuti' => $jenis,
                'tanggal_mulai' => $start->toDateString(),
                'tanggal_selesai' => $end->toDateString(),
                'jumlah_hari' => $durasi,
                'alasan' => $alasanCuti[rand(0, count($alasanCuti) - 1)],
                'status_atasan' => $statusAtasan,
                'catatan_atasan' => $statusAtasan === 'ditolak' ? 'Jadwal berbenturan dengan agenda utama tim.' : null,
                'status_hr' => $statusHr,
                'status' => $status,
                'created_at' => $createdAt,
            ]);
        }

        // -------------------------------------------------------------
        // 3. Seed ~30 Data Riwayat / Verifikasi Diklat & Pelatihan
        // -------------------------------------------------------------
        $this->command->info('Seeding 30 Data Diklat / Pelatihan...');
        $kategoriList = ['struktural', 'fungsional', 'teknis', 'latsar', 'lainnya'];
        $verifStatusList = ['terverifikasi', 'menunggu', 'ditolak'];

        foreach (range(1, 30) as $i) {
            $pegawai = $pegawais->random();
            $verif = $verifStatusList[rand(0, 2)];

            RiwayatPelatihan::create([
                'pegawai_id' => $pegawai->id,
                'nama_pelatihan' => $namaDiklat[rand(0, count($namaDiklat) - 1)],
                'penyelenggara' => $penyelenggaraDiklat[rand(0, count($penyelenggaraDiklat) - 1)],
                'tanggal' => Carbon::now()->subDays(rand(5, 90)),
                'durasi_jp' => [8, 16, 24, 32, 40][rand(0, 4)],
                'kategori' => $kategoriList[rand(0, count($kategoriList) - 1)],
                'status_verifikasi' => $verif,
                'created_at' => Carbon::now()->subDays(rand(1, 40)),
            ]);
        }

        // -------------------------------------------------------------
        // 4. Seed ~30 Data Absensi Variatif Hari Ini & Kemarin
        // -------------------------------------------------------------
        $this->command->info('Seeding 30 Record Absensi...');
        $absensiStatusList = ['hadir', 'telat', 'izin', 'sakit'];

        foreach ($pegawais->take(30) as $i => $pegawai) {
            $tgl = Carbon::today()->subDays($i % 3);
            $st = $absensiStatusList[rand(0, 3)];

            Absensi::updateOrCreate(
                [
                    'pegawai_id' => $pegawai->id,
                    'tanggal' => $tgl->toDateString(),
                ],
                [
                    'jam_masuk' => $st === 'telat' ? '08:42' : '07:50',
                    'jam_keluar' => '16:30',
                    'status' => $st,
                    'latitude_masuk' => -6.229728,
                    'longitude_masuk' => 106.807444,
                    'is_mock_location' => 0,
                    'gps_accuracy' => 8.5,
                ]
            );
        }

        $this->command->info('DummyDataSeeder berhasil menyemaikan 30+ data pengajuan perubahan, cuti, diklat, dan absensi!');
    }
}
