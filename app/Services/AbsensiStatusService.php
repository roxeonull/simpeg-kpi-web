<?php

namespace App\Services;

use App\Models\Cuti;
use App\Models\JadwalShift;
use App\Models\Pegawai;
use App\Models\Pengaturan;
use Carbon\Carbon;

/**
 * AbsensiStatusService — Satu-satunya sumber logic perhitungan status kehadiran.
 *
 * Digunakan bersama oleh:
 *   - AbsensiApiController  (presensi via mobile)
 *   - AbsensiController     (tampilan web + absensi manual)
 *   - CatatAlpaOtomatis     (scheduled command)
 *
 * Aturan Normal (non-shift):
 *   - Jendela buka absen    : jam_awal_absen (default 05:00)
 *   - Hadir tepat waktu     : jam_awal_absen s/d jam_masuk_standar (default 08:30)
 *   - Telat                 : jam_masuk_standar+1 s/d jam_batas_telat (default 10:00)
 *   - Alpa otomatis         : belum presensi masuk sampai jam_batas_alpa (default 16:00)
 *   - Jam pulang standar    : jam_pulang_standar (default 16:00)
 *   - Flexible work hours   : jika datang sebelum jam_masuk_standar, boleh pulang
 *     lebih awal dengan selisih yang sama, TAPI tidak boleh lebih awal dari
 *     jam_pulang_minimal_flexibel (default 15:30) — berapapun jam masuknya.
 *
 * Aturan Shift:
 *   - Jendela buka absen    : jam_mulai_shift - toleransi_awal_shift_menit (default 60 menit)
 *   - Hadir                 : s/d jam_mulai_shift + toleransi_telat_shift_menit (default 30 menit)
 *   - Telat                 : setelah batas hadir di atas
 *   - Alpa otomatis         : belum presensi masuk sampai jam_selesai_shift
 */
class AbsensiStatusService
{
    /** Pengaturan di-cache untuk satu siklus request agar tidak multi-query DB */
    private ?array $cache = null;

    // ------------------------------------------------------------------
    //  Public API
    // ------------------------------------------------------------------

    /**
     * Validasi apakah jendela presensi masuk sudah terbuka.
     *
     * @param  string           $jamMasukAktual  Format 'H:i' atau 'H:i:s'
     * @param  JadwalShift|null $jadwalShift     null = pegawai Normal
     * @return string|null                      Null jika valid (terbuka), atau string pesan error jika belum terbuka
     */
    public function validasiJendelaPresensiMasuk(string $jamMasukAktual, ?JadwalShift $jadwalShift): ?string
    {
        $p = $this->getPengaturan();
        $aktual = $this->parseTime($jamMasukAktual);

        if ($jadwalShift) {
            $jamMulaiShiftStr = JadwalShift::getJamMulai($jadwalShift->shift);
            $jamMulaiShift    = $this->parseTime($jamMulaiShiftStr);

            $toleransiAwal = (int) ($p['toleransi_awal_shift_menit'] ?? 60);
            $jamBuka       = $jamMulaiShift->copy()->subMinutes($toleransiAwal);

            if ($aktual->lt($jamBuka)) {
                $jamBukaStr = $jamBuka->format('H:i');
                return "Presensi untuk Shift {$jadwalShift->shift} baru bisa dilakukan mulai pukul {$jamBukaStr}";
            }

            return null;
        }

        // Pegawai Normal: jendela presensi terbuka jam_awal_absen (05:00)
        $jamAwalAbsen = $this->parseTime($p['jam_awal_absen']);
        if ($aktual->lt($jamAwalAbsen)) {
            $jamAwalAbsenStr = $jamAwalAbsen->format('H:i');
            return "Presensi masuk baru bisa dilakukan mulai pukul {$jamAwalAbsenStr}";
        }

        return null;
    }

    /**
     * Hitung status kehadiran berdasarkan jam masuk aktual.
     *
     * @param  string           $jamMasukAktual  Format 'H:i' atau 'H:i:s'
     * @param  JadwalShift|null $jadwalShift     null = pegawai Normal
     * @return string 'hadir' | 'telat'
     */
    public function hitungStatusMasuk(string $jamMasukAktual, ?JadwalShift $jadwalShift): string
    {
        $aktual = $this->parseTime($jamMasukAktual);

        if ($jadwalShift) {
            return $this->statusMasukShift($aktual, $jadwalShift);
        }

        return $this->statusMasukNormal($aktual);
    }

    /**
     * Hitung jam pulang yang diizinkan.
     *
     * Untuk shift: selalu jam_selesai_shift (flexible tidak berlaku).
     * Untuk Normal: jam_pulang_standar dikurangi selisih menit datang awal,
     *               dengan batas bawah jam_pulang_minimal_flexibel.
     *               Jika flexible_work_hours_enabled = 0, selalu jam_pulang_standar.
     *
     * @param  string           $jamMasukAktual Format 'H:i' atau 'H:i:s'
     * @param  JadwalShift|null $jadwalShift
     * @return string Format 'H:i'
     */
    public function hitungJamPulangDiizinkan(string $jamMasukAktual, ?JadwalShift $jadwalShift): string
    {
        if ($jadwalShift) {
            // Shift: jam pulang = jam selesai shift (fixed, tidak ada flexible)
            return JadwalShift::getJamSelesai($jadwalShift->shift);
        }

        $p = $this->getPengaturan();
        $jamPulangStandar = $this->parseTime($p['jam_pulang_standar']);
        $jamMasukStandar  = $this->parseTime($p['jam_masuk_standar']);
        $aktual           = $this->parseTime($jamMasukAktual);

        // Flexible tidak aktif → selalu jam pulang standar
        if (!$p['flexible_work_hours_enabled']) {
            return $jamPulangStandar->format('H:i');
        }

        // Datang tepat waktu atau telat → jam pulang standar
        if ($aktual->gte($jamMasukStandar)) {
            return $jamPulangStandar->format('H:i');
        }

        // Datang lebih awal → kurangi jam pulang dengan selisih menit,
        // tapi tidak boleh lebih awal dari jam_pulang_minimal_flexibel
        $selisihMenit    = $jamMasukStandar->diffInMinutes($aktual); // menit lebih awal
        $jamPulangFleks  = $jamPulangStandar->copy()->subMinutes($selisihMenit);
        $jamPulangMinimal = $this->parseTime($p['jam_pulang_minimal_flexibel']);

        // Ambil yang lebih besar (lebih siang = lebih aman bagi pegawai)
        if ($jamPulangFleks->lt($jamPulangMinimal)) {
            return $jamPulangMinimal->format('H:i');
        }

        return $jamPulangFleks->format('H:i');
    }

    /**
     * Hitung total menit pengurangan jam kerja (gabungan menit telat + menit pulang cepat).
     *
     * @param  string           $jamMasukAktual     Format 'H:i' atau 'H:i:s'
     * @param  string           $jamKeluarAktual    Format 'H:i' atau 'H:i:s'
     * @param  JadwalShift|null $jadwalShift        null = pegawai Normal
     * @param  string|null      $jamPulangDiizinkan Format 'H:i' (untuk pegawai normal)
     * @return array  ['menit_telat' => int, 'menit_pulang_cepat' => int, 'total_menit_pengurangan' => int]
     */
    public function hitungTotalMenitPengurangan(
        string $jamMasukAktual,
        string $jamKeluarAktual,
        ?JadwalShift $jadwalShift,
        ?string $jamPulangDiizinkan = null
    ): array {
        $masuk  = $this->parseTime($jamMasukAktual);
        $keluar = $this->parseTime($jamKeluarAktual);
        $p      = $this->getPengaturan();

        $menitTelat       = 0;
        $menitPulangCepat = 0;

        if ($jadwalShift) {
            // SHIFT
            $jamMulaiShift = $this->parseTime(JadwalShift::getJamMulai($jadwalShift->shift));

            // 1. Menit Telat: jika masuk setelah jam mulai shift
            if ($masuk->gt($jamMulaiShift)) {
                $menitTelat = (int) $jamMulaiShift->diffInMinutes($masuk);
            }

            // 2. Menit Pulang Cepat: jika keluar sebelum jam selesai shift
            if ($jadwalShift->shift === '3') {
                // Shift 3: 22:00 s/d 06:00 esok hari
                $dtMulai   = Carbon::today()->setHour(22)->setMinute(0)->setSecond(0);
                $dtSelesai = Carbon::today()->addDay()->setHour(6)->setMinute(0)->setSecond(0);

                $dtMasuk = Carbon::today()->setHour((int) $masuk->format('H'))->setMinute((int) $masuk->format('i'))->setSecond(0);
                if ((int) $masuk->format('H') < 12) {
                    $dtMasuk->addDay();
                }

                $dtKeluar = Carbon::today()->setHour((int) $keluar->format('H'))->setMinute((int) $keluar->format('i'))->setSecond(0);
                if ((int) $keluar->format('H') < 12) {
                    $dtKeluar->addDay();
                }

                if ($dtMasuk->gt($dtMulai)) {
                    $menitTelat = (int) $dtMulai->diffInMinutes($dtMasuk);
                } else {
                    $menitTelat = 0;
                }

                if ($dtKeluar->lt($dtSelesai)) {
                    $menitPulangCepat = (int) $dtKeluar->diffInMinutes($dtSelesai);
                }
            } else {
                $jamSelesaiShift = $this->parseTime(JadwalShift::getJamSelesai($jadwalShift->shift));
                if ($keluar->lt($jamSelesaiShift)) {
                    $menitPulangCepat = (int) $keluar->diffInMinutes($jamSelesaiShift);
                }
            }
        } else {
            // NORMAL
            $jamMasukStandar = $this->parseTime($p['jam_masuk_standar']);

            // 1. Menit Telat: jika masuk setelah 08:30 (jam_masuk_standar)
            if ($masuk->gt($jamMasukStandar)) {
                $menitTelat = (int) $jamMasukStandar->diffInMinutes($masuk);
            }

            // 2. Menit Pulang Cepat: jika keluar sebelum jam pulang yang diizinkan
            $targetPulangStr = $jamPulangDiizinkan ?? $this->hitungJamPulangDiizinkan($jamMasukAktual, null);
            $targetPulang    = $this->parseTime($targetPulangStr);

            if ($keluar->lt($targetPulang)) {
                $menitPulangCepat = (int) $keluar->diffInMinutes($targetPulang);
            }
        }

        $total = $menitTelat + $menitPulangCepat;

        return [
            'menit_telat'             => $menitTelat,
            'menit_pulang_cepat'      => $menitPulangCepat,
            'total_menit_pengurangan' => $total,
        ];
    }

    /**
     * Hitung apakah presensi keluar termasuk "pulang cepat" dan berapa menit pengurangannya.
     * (Deprecated / helper sederhana)
     *
     * @param  string $jamKeluarAktual     Format 'H:i' atau 'H:i:s'
     * @param  string $jamPulangDiizinkan  Format 'H:i'
     * @return array  ['pulang_cepat' => bool, 'menit_pengurangan' => int]
     */
    public function hitungPulangCepat(string $jamKeluarAktual, string $jamPulangDiizinkan): array
    {
        $keluar   = $this->parseTime($jamKeluarAktual);
        $diizinkan = $this->parseTime($jamPulangDiizinkan);

        if ($keluar->gte($diizinkan)) {
            return ['pulang_cepat' => false, 'menit_pengurangan' => 0];
        }

        $menit = $keluar->diffInMinutes($diizinkan);

        return ['pulang_cepat' => true, 'menit_pengurangan' => (int) $menit];
    }

    /**
     * Apakah pegawai sudah harus di-alpa-kan pada tanggal tersebut?
     * Dipakai oleh CatatAlpaOtomatis command.
     *
     * @param  Pegawai          $pegawai
     * @param  Carbon           $tanggal    Tanggal yang dicek (tanpa jam)
     * @param  JadwalShift|null $jadwalShift null = pegawai Normal
     * @return bool
     */
    public function apakahHarusAlpa(Pegawai $pegawai, Carbon $tanggal, ?JadwalShift $jadwalShift): bool
    {
        $sekarang = now();

        // Tentukan jam batas alpa berdasarkan tipe pegawai
        if ($jadwalShift) {
            $jamBatasStr = JadwalShift::getJamSelesai($jadwalShift->shift);
        } else {
            $p           = $this->getPengaturan();
            $jamBatasStr = $p['jam_batas_alpa'];
        }

        $jamBatas = Carbon::parse($tanggal->toDateString() . ' ' . $jamBatasStr);

        // Untuk shift malam (shift 3: 22:00–06:00), jam selesai ada di hari berikutnya
        if ($jadwalShift && $jadwalShift->shift === '3') {
            $jamBatas->addDay();
        }

        // Belum melewati jam batas → belum waktunya alpa
        if ($sekarang->lt($jamBatas)) {
            return false;
        }

        // Cek apakah pegawai sedang cuti/izin yang disetujui pada tanggal ini
        if ($this->sedangCutiDiIzinkan($pegawai, $tanggal)) {
            return false;
        }

        return true;
    }

    /**
     * Ambil semua pengaturan jam kerja (di-cache dalam satu request).
     *
     * @return array
     */
    public function getPengaturan(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $this->cache = [
            'jam_awal_absen'              => Pengaturan::get('jam_awal_absen', '05:00'),
            'jam_masuk_standar'           => Pengaturan::get('jam_masuk_standar', Pengaturan::get('jam_masuk', '08:30')),
            'jam_batas_telat'             => Pengaturan::get('jam_batas_telat', '10:00'),
            'jam_batas_alpa'              => Pengaturan::get('jam_batas_alpa', '16:00'),
            'jam_pulang_standar'          => Pengaturan::get('jam_pulang_standar', Pengaturan::get('jam_pulang', '16:00')),
            'jam_pulang_minimal_flexibel' => Pengaturan::get('jam_pulang_minimal_flexibel', '15:30'),
            'flexible_work_hours_enabled' => (bool) Pengaturan::get('flexible_work_hours_enabled', '1'),
            'toleransi_awal_shift_menit'  => (int) Pengaturan::get('toleransi_awal_shift_menit', 60),
            'toleransi_telat_shift_menit' => (int) Pengaturan::get('toleransi_telat_shift_menit', 30),
        ];

        return $this->cache;
    }

    /**
     * Reset cache (berguna di unit test atau command yang berjalan lama).
     */
    public function resetCache(): void
    {
        $this->cache = null;
    }

    // ------------------------------------------------------------------
    //  Private Helpers
    // ------------------------------------------------------------------

    private function statusMasukNormal(Carbon $aktual): string
    {
        $p = $this->getPengaturan();
        $jamMasukStandar = $this->parseTime($p['jam_masuk_standar']);

        // Datang sebelum atau tepat jam masuk standar → HADIR
        if ($aktual->lte($jamMasukStandar)) {
            return 'hadir';
        }

        // Datang setelah jam masuk standar tapi sebelum/tepat batas telat → TELAT
        return 'telat';
    }

    private function statusMasukShift(Carbon $aktual, JadwalShift $jadwalShift): string
    {
        $p = $this->getPengaturan();
        $jamMulai = $this->parseTime(JadwalShift::getJamMulai($jadwalShift->shift));

        // Batas hadir = jam mulai shift + toleransi telat
        $batasHadir = $jamMulai->copy()->addMinutes($p['toleransi_telat_shift_menit']);

        if ($aktual->lte($batasHadir)) {
            return 'hadir';
        }

        return 'telat';
    }

    /**
     * Cek apakah pegawai punya cuti/izin yang disetujui (status='disetujui') pada tanggal tersebut.
     */
    private function sedangCutiDiIzinkan(Pegawai $pegawai, Carbon $tanggal): bool
    {
        return Cuti::where('pegawai_id', $pegawai->id)
            ->where('status', 'disetujui')
            ->where('tanggal_mulai', '<=', $tanggal->toDateString())
            ->where('tanggal_selesai', '>=', $tanggal->toDateString())
            ->exists();
    }

    /**
     * Parse string waktu 'H:i' atau 'H:i:s' menjadi Carbon hari ini.
     */
    private function parseTime(string $time): Carbon
    {
        // Normalisasi ke H:i saja untuk perbandingan
        $parts = explode(':', $time);
        $h = (int) ($parts[0] ?? 0);
        $m = (int) ($parts[1] ?? 0);

        return Carbon::today()->setHour($h)->setMinute($m)->setSecond(0);
    }
}
