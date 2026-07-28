<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class PegawaiShiftSeeder extends Seeder
{
    public function run(): void
    {
        $unit = UnitKerja::where('nama_unit', 'Bidang Pengawasan Isi Siaran')->first()
            ?? UnitKerja::first();

        $shiftPegawais = [
            // --- Data Pegawai dari Excel / PDF Jadwal Shift Juli 2026 ---
            ['nama' => 'Khoirun Nisa', 'jk' => 'P', 'stasiun_tv' => 'TRANS TV'],
            ['nama' => 'Ofryanti Mairani', 'jk' => 'P', 'stasiun_tv' => 'METRO TV'],
            ['nama' => 'Hanna Puspita', 'jk' => 'P', 'stasiun_tv' => 'SCTV'],
            ['nama' => 'Olivia Rina', 'jk' => 'P', 'stasiun_tv' => 'MNC TV'],
            ['nama' => 'Lia Deviyanti', 'jk' => 'P', 'stasiun_tv' => 'TRANS 7'],
            ['nama' => 'Rini Rahayu', 'jk' => 'P', 'stasiun_tv' => 'TV ONE'],
            ['nama' => 'Resti Desmalia', 'jk' => 'P', 'stasiun_tv' => 'INDOSIAR'],
            ['nama' => 'Irma Fardiyanih', 'jk' => 'P', 'stasiun_tv' => 'TVRI'],
            ['nama' => 'Nurul Azizah', 'jk' => 'P', 'stasiun_tv' => 'KOMPAS TV'],
            ['nama' => 'Afifatuzzahra', 'jk' => 'P', 'stasiun_tv' => 'RTV'],
            ['nama' => 'Sri Mulyani', 'jk' => 'P', 'stasiun_tv' => 'MDTV'],
            ['nama' => 'Brillian Nadiba', 'jk' => 'P', 'stasiun_tv' => 'INEWS TV'],
            ['nama' => 'Diyala Gelarina', 'jk' => 'P', 'stasiun_tv' => 'RCTI'],
            ['nama' => 'Fakhrun Nisa', 'jk' => 'P', 'stasiun_tv' => 'ANTV'],
            ['nama' => 'Intantri Kusmawarni', 'jk' => 'P', 'stasiun_tv' => 'GTV'],
            ['nama' => 'Bilqis Ubaida', 'jk' => 'P', 'stasiun_tv' => 'JPM'],
            ['nama' => 'Jupentus V. Sinaga', 'jk' => 'L', 'stasiun_tv' => 'Moji'],
            ['nama' => 'Eka Kurniawati', 'jk' => 'P', 'stasiun_tv' => 'BTV'],
            ['nama' => 'Dini Rosyana', 'jk' => 'P', 'stasiun_tv' => 'CNN INDONESIA'],
            ['nama' => 'Nurina Afriani', 'jk' => 'P', 'stasiun_tv' => 'MENTARI'],
            ['nama' => 'Luli Faira', 'jk' => 'P', 'stasiun_tv' => 'GARUDA TV'],
            ['nama' => 'Hety Ningrum', 'jk' => 'P', 'stasiun_tv' => 'INSERT'],
            ['nama' => 'Defina Ayu Febriyanti', 'jk' => 'P', 'stasiun_tv' => 'INSERT'],
            ['nama' => 'Haldia Nurvianti', 'jk' => 'P', 'stasiun_tv' => 'INSERT'],
            ['nama' => 'Meri Andani', 'jk' => 'P', 'stasiun_tv' => 'INSERT'],
            ['nama' => 'Ronaa Permata', 'jk' => 'P', 'stasiun_tv' => 'INSERT'],
            ['nama' => 'Chevy Wijayanti. G', 'jk' => 'P', 'stasiun_tv' => 'INSERT'],
            ['nama' => 'Erlin Surlina Febriyanti', 'jk' => 'P', 'stasiun_tv' => 'INSERT'],
            ['nama' => 'Emmy Olivia', 'jk' => 'P', 'stasiun_tv' => 'INSERT'],
            ['nama' => 'Nadya Yossiva', 'jk' => 'P', 'stasiun_tv' => 'M2'],
            ['nama' => 'Tommy Manggala P. P', 'jk' => 'L', 'stasiun_tv' => 'M2a'],
            ['nama' => 'Wawan Harriadi', 'jk' => 'L', 'stasiun_tv' => 'DIGITAL 1'],
            ['nama' => 'Fitri Dewi Sunarti', 'jk' => 'P', 'stasiun_tv' => 'DIGITAL 2'],
            ['nama' => 'Nurul Isyana', 'jk' => 'P', 'stasiun_tv' => 'LPB 1'],
            ['nama' => 'Nur Fitriah', 'jk' => 'P', 'stasiun_tv' => 'RADIO 1'],
            ['nama' => 'Nazwa Ramadhanti', 'jk' => 'P', 'stasiun_tv' => 'INSERT'],
            ['nama' => 'Bendhan Woro', 'jk' => 'L', 'stasiun_tv' => 'INSERT'],
            ['nama' => 'Utri Suciati', 'jk' => 'P', 'stasiun_tv' => 'INSERT'],
            ['nama' => 'Beatrik Harya Dewi. U', 'jk' => 'P', 'stasiun_tv' => 'M2'],
            ['nama' => 'Khoirotul Akyuni', 'jk' => 'P', 'stasiun_tv' => 'M2a'],

            // --- Data Pegawai Shift Laki-Laki Tambahan ---
            ['nama' => 'Sigit Alfian', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Marulitua Gultom', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Hanjasa Partogi', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'M. Yusuf Abdullah', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Abby Bagaskara', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Afif Bustami', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Mustafid', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Faesal', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Virgiawan Arsetya. M', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Fahdiansyah. O', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Adi Priadi', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Yintrosius Bena', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Amir Muhammad', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Mohammad Zamzami', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'M. Ryan Arif Pratama', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Evan Yohanes', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Erwin', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Akbar Junaedi', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Agus Atabik', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Alfa Arlingga', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Arfiansyah', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Robi Sabila', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Yogi Perdana. W', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Syamsul Arif. R', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Anjungan P. Samosir', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Supriadih', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Muhammad Ardja Widjaya', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'M. Syaiful Fahmi', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'M. Ardiansyah', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Egy Pramana Putra', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Fauzan Arya. K', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Muhammad Bahreisy', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'M. Mughny Abdinirio', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Qadarusno', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Marsada. Hr. Napitupulu', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Abdul Badi Darmadi', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Firman Rawul', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Dezan Alfatkhan', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Fawri Nashr', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'M. Aldy Permana', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Irfan Nuruddin', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Salman Alfarisi', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Andra Tranido', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Duto Dwi Prasetyo', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Fachri Dwi Heldiansyah', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Muhamad Kumaidi', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Satria Pratama', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Nanang Supena', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Jevandi Sembi Ranto. P', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Suherman', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Muhammad Ikhsan', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Fajar Ramadhani', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Arif Mansyah', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Bagus Prasojo', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Indrasto', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Rifqi Mahfud', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Soe Suranta Billeam. T', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Achmad Zarkasih', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Defri Syahrizal', 'jk' => 'L', 'stasiun_tv' => null],
            ['nama' => 'Dany Kurniawan', 'jk' => 'L', 'stasiun_tv' => null],
        ];

        foreach ($shiftPegawais as $idx => $item) {
            $nama = $item['nama'];
            $cleanName = strtolower(trim($nama));
            $exists = Pegawai::all()->first(function ($p) use ($cleanName) {
                return strtolower(trim($p->nama)) === $cleanName;
            });

            if (!$exists) {
                Pegawai::create([
                    'nip' => '199501012026' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                    'nama' => $nama,
                    'jenis_kelamin' => $item['jk'],
                    'stasiun_tv' => $item['stasiun_tv'] ?? null,
                    'unit_id' => $unit?->id,
                    'status_aktif' => 'aktif',
                    'status_kepegawaian' => 'Non-ASN',
                ]);
            } else {
                // Update jenis_kelamin dan stasiun_tv jika belum terisi
                $updateData = [];
                if (!$exists->jenis_kelamin && !empty($item['jk'])) {
                    $updateData['jenis_kelamin'] = $item['jk'];
                }
                if (!$exists->stasiun_tv && !empty($item['stasiun_tv'])) {
                    $updateData['stasiun_tv'] = $item['stasiun_tv'];
                }
                if (!empty($updateData)) {
                    $exists->update($updateData);
                }
            }
        }
    }
}
