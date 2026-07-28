<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Pengaturan;
use App\Models\RiwayatPelatihan;
use App\Models\RiwayatPendidikan;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Pengaturan sistem default
        Pengaturan::set('jam_masuk', '08:00');
        Pengaturan::set('jam_pulang', '16:00');
        Pengaturan::set('radius_gps', '100');
        Pengaturan::set('kuota_cuti_tahunan', '12');
        Pengaturan::set('target_jp_tahunan', '20');

        // Master data unit kerja
        $units = collect(['Bagian Umum & SDM', 'Bidang Pengawasan Isi Siaran', 'Bidang Kelembagaan', 'Bidang Perizinan', 'Sekretariat'])
            ->map(fn ($nama, $i) => UnitKerja::create(['nama_unit' => $nama, 'kode_unit' => 'U' . str_pad($i + 1, 2, '0', STR_PAD_LEFT)]));

        $this->call(PegawaiShiftSeeder::class);

        // Master data jabatan
        $jabatans = collect(['Kepala Bagian', 'Kepala Bidang', 'Staf Pelaksana', 'Analis Kepegawaian', 'Pranata Komputer'])
            ->map(fn ($nama) => Jabatan::create(['nama_jabatan' => $nama]));

        // Admin HR
        $admin = User::create([
            'name' => 'Admin HR KPI',
            'email' => 'admin@kpi.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Atasan (Kepala Bagian) + pegawainya
        $atasanUser = User::create([
            'name' => 'Budi Santoso',
            'email' => 'atasan@kpi.go.id',
            'password' => Hash::make('password'),
            'role' => 'atasan',
        ]);

        $atasanPegawai = Pegawai::create([
            'user_id' => $atasanUser->id,
            'nip' => '198501012010011001',
            'nama' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'jabatan_id' => $jabatans[0]->id,
            'unit_id' => $units[0]->id,
            'status_kepegawaian' => 'PNS',
            'tmt' => '2010-01-01',
            'email' => 'atasan@kpi.go.id',
            'no_hp' => '081234567890',
            'status_aktif' => 'aktif',
        ]);

        // Anggota tim (Termasuk Pegawai Perempuan: Siti Aminah, Rina Kusuma, Maya Sari)
        $namaPegawai = [
            ['nama' => 'Siti Aminah', 'jk' => 'P'],
            ['nama' => 'Andi Wijaya', 'jk' => 'L'],
            ['nama' => 'Rina Kusuma', 'jk' => 'P'],
            ['nama' => 'Dedi Rahman', 'jk' => 'L'],
            ['nama' => 'Maya Sari', 'jk' => 'P'],
            ['nama' => 'Fajar Nugroho', 'jk' => 'L'],
        ];
        $anggotaTim = collect($namaPegawai)->map(function ($item, $i) use ($jabatans, $units, $atasanPegawai) {
            $nama = $item['nama'];
            $pegawai = Pegawai::create([
                'nip' => '19900101201001' . str_pad($i + 2, 4, '0', STR_PAD_LEFT),
                'nama' => $nama,
                'jenis_kelamin' => $item['jk'],
                'jabatan_id' => $jabatans[($i % 3) + 2]->id,
                'unit_id' => $units[0]->id,
                'atasan_id' => $atasanPegawai->id,
                'status_kepegawaian' => $i % 3 === 0 ? 'PPPK' : 'PNS',
                'tmt' => now()->subYears(3 + $i)->startOfYear(),
                'email' => strtolower(str_replace(' ', '.', $nama)) . '@kpi.go.id',
                'no_hp' => '08123456' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'status_aktif' => 'aktif',
            ]);

            RiwayatPendidikan::create([
                'pegawai_id' => $pegawai->id,
                'jenjang' => 'S1',
                'institusi' => 'Universitas Indonesia',
                'jurusan' => 'Ilmu Komunikasi',
                'tahun_lulus' => 2008 + $i,
            ]);

            RiwayatPelatihan::create([
                'pegawai_id' => $pegawai->id,
                'nama_pelatihan' => 'Diklat Fungsional Penyiaran',
                'penyelenggara' => 'Pusdiklat KPI',
                'tanggal' => now()->subMonths($i + 1),
                'durasi_jp' => 16 + $i * 2,
                'kategori' => 'fungsional',
                'status_verifikasi' => $i % 2 === 0 ? 'terverifikasi' : 'menunggu',
            ]);

            // Absensi 5 hari terakhir
            foreach (range(0, 4) as $d) {
                Absensi::create([
                    'pegawai_id' => $pegawai->id,
                    'tanggal' => now()->subDays($d),
                    'jam_masuk' => $d === 1 ? '08:15' : '07:55',
                    'jam_keluar' => '16:35',
                    'status' => $d === 1 ? 'telat' : 'hadir',
                ]);
            }

            return $pegawai;
        });

        // Contoh data Cuti
        // 1. Data Historis (2025) untuk demo analitik & rekomendasi musiman (lonjakan Mei & Desember)
        Cuti::create([
            'pegawai_id' => $anggotaTim[0]->id, // Siti Aminah
            'jenis_cuti' => 'tahunan',
            'tanggal_mulai' => '2025-05-10',
            'tanggal_selesai' => '2025-05-12',
            'jumlah_hari' => 3,
            'alasan' => 'Mudik Lebaran lebih awal',
            'status_atasan' => 'disetujui',
            'status_hr' => 'disetujui',
            'status' => 'disetujui',
            'created_at' => '2025-05-01 09:00:00',
        ]);
        Cuti::create([
            'pegawai_id' => $anggotaTim[1]->id, // Andi Wijaya
            'jenis_cuti' => 'sakit',
            'tanggal_mulai' => '2025-05-11',
            'tanggal_selesai' => '2025-05-12',
            'jumlah_hari' => 2,
            'alasan' => 'Demam berdarah',
            'status_atasan' => 'disetujui',
            'status_hr' => 'disetujui',
            'status' => 'disetujui',
            'created_at' => '2025-05-10 08:30:00',
        ]);
        Cuti::create([
            'pegawai_id' => $anggotaTim[2]->id, // Rina Kusuma
            'jenis_cuti' => 'tahunan',
            'tanggal_mulai' => '2025-12-20',
            'tanggal_selesai' => '2025-12-24',
            'jumlah_hari' => 5,
            'alasan' => 'Liburan akhir tahun keluarga',
            'status_atasan' => 'disetujui',
            'status_hr' => 'disetujui',
            'status' => 'disetujui',
            'created_at' => '2025-12-10 10:00:00',
        ]);
        Cuti::create([
            'pegawai_id' => $anggotaTim[3]->id, // Dedi Rahman
            'jenis_cuti' => 'tahunan',
            'tanggal_mulai' => '2025-12-22',
            'tanggal_selesai' => '2025-12-25',
            'jumlah_hari' => 4,
            'alasan' => 'Natal & Tahun Baru',
            'status_atasan' => 'disetujui',
            'status_hr' => 'disetujui',
            'status' => 'disetujui',
            'created_at' => '2025-12-15 11:00:00',
        ]);

        // 2. Data Cuti Aktif & Konflik Overlap di Bulan Ini (Juli 2026)
        // Overlap di unit yang sama (Unit 0: Bagian Umum & SDM)
        Cuti::create([
            'pegawai_id' => $anggotaTim[0]->id, // Siti Aminah
            'jenis_cuti' => 'tahunan',
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => '2026-07-14',
            'jumlah_hari' => 5,
            'alasan' => 'Acara pernikahan keluarga',
            'status_atasan' => 'disetujui',
            'status_hr' => 'disetujui',
            'status' => 'disetujui',
            'created_at' => '2026-07-01 09:00:00',
        ]);
        Cuti::create([
            'pegawai_id' => $anggotaTim[1]->id, // Andi Wijaya
            'jenis_cuti' => 'tahunan',
            'tanggal_mulai' => '2026-07-12',
            'tanggal_selesai' => '2026-07-15',
            'jumlah_hari' => 4,
            'alasan' => 'Kebutuhan keluarga mendesak',
            'status_atasan' => 'disetujui',
            'status_hr' => 'disetujui',
            'status' => 'disetujui',
            'created_at' => '2026-07-02 10:00:00',
        ]);
        Cuti::create([
            'pegawai_id' => $anggotaTim[2]->id, // Rina Kusuma
            'jenis_cuti' => 'tahunan',
            'tanggal_mulai' => '2026-07-13',
            'tanggal_selesai' => '2026-07-15',
            'jumlah_hari' => 3,
            'alasan' => 'Pindah rumah',
            'status_atasan' => 'disetujui',
            'status_hr' => 'disetujui',
            'status' => 'disetujui',
            'created_at' => '2026-07-05 11:30:00',
        ]);
        Cuti::create([
            'pegawai_id' => $anggotaTim[3]->id, // Dedi Rahman
            'jenis_cuti' => 'sakit',
            'tanggal_mulai' => '2026-07-20',
            'tanggal_selesai' => '2026-07-20',
            'jumlah_hari' => 1,
            'alasan' => 'Sakit gigi',
            'status_atasan' => 'disetujui',
            'status_hr' => 'disetujui',
            'status' => 'disetujui',
            'created_at' => '2026-07-19 08:00:00',
        ]);
        Cuti::create([
            'pegawai_id' => $anggotaTim[4]->id, // Maya Sari
            'jenis_cuti' => 'melahirkan',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-08-09',
            'jumlah_hari' => 40,
            'alasan' => 'Melahirkan anak pertama',
            'status_atasan' => 'disetujui',
            'status_hr' => 'disetujui',
            'status' => 'disetujui',
            'created_at' => '2026-06-15 08:30:00',
        ]);

        // Cuti Menunggu (Total 2 pending)
        Cuti::create([
            'pegawai_id' => $anggotaTim[5]->id, // Fajar Nugroho
            'jenis_cuti' => 'tahunan',
            'tanggal_mulai' => '2026-07-25',
            'tanggal_selesai' => '2026-07-27',
            'jumlah_hari' => 3,
            'alasan' => 'Keperluan keluarga',
            'status_atasan' => 'menunggu',
            'status_hr' => 'menunggu',
            'status' => 'menunggu_atasan',
            'created_at' => '2026-07-14 10:00:00',
        ]);
        Cuti::create([
            'pegawai_id' => $atasanPegawai->id, // Budi Santoso (Atasan)
            'jenis_cuti' => 'tahunan',
            'tanggal_mulai' => '2026-07-28',
            'tanggal_selesai' => '2026-07-29',
            'jumlah_hari' => 2,
            'alasan' => 'Cek kesehatan tahunan',
            'status_atasan' => 'disetujui',
            'status_hr' => 'menunggu',
            'status' => 'menunggu_hr',
            'created_at' => '2026-07-15 09:00:00',
        ]);

        // Cuti Ditolak
        Cuti::create([
            'pegawai_id' => $anggotaTim[0]->id, // Siti Aminah
            'jenis_cuti' => 'lainnya',
            'tanggal_mulai' => '2026-07-30',
            'tanggal_selesai' => '2026-07-31',
            'jumlah_hari' => 2,
            'alasan' => 'Menghadiri reuni akbar',
            'status_atasan' => 'ditolak',
            'catatan_atasan' => 'Reuni tidak memenuhi kualifikasi mendesak saat volume unit tinggi.',
            'status_hr' => 'menunggu',
            'status' => 'ditolak',
            'created_at' => '2026-07-14 15:00:00',
        ]);

        // Contoh pegawai role login mandiri (untuk pengujian, mobile menyusul)
        User::create([
            'name' => $namaPegawai[0]['nama'],
            'email' => 'pegawai@kpi.go.id',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
        ]);

        $this->call(StatusShiftSeeder::class);
        $this->call(TrainingMasterSeeder::class);

        $this->command->info('Seeder selesai. Akun demo:');
        $this->command->info('Admin  : admin@kpi.go.id / password');
        $this->command->info('Atasan : atasan@kpi.go.id / password');
        $this->command->info('Pegawai: pegawai@kpi.go.id / password');
    }
}
