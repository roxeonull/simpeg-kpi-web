<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Absensi;
use App\Models\JadwalShift;
use App\Models\Pengaturan;
use App\Services\AbsensiStatusService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiApiController extends Controller
{
    public function __construct(private AbsensiStatusService $statusService) {}

    public function index(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404);

        $bulan = $request->get('bulan', now()->format('Y-m'));

        $absensis = $pegawai->absensi()
            ->whereYear('tanggal', substr($bulan, 0, 4))
            ->whereMonth('tanggal', substr($bulan, 5, 2))
            ->orderByDesc('tanggal')
            ->get()
            ->map(fn ($a) => $this->format($a));

        return response()->json(['data' => $absensis]);
    }

    public function hariIni(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404);

        $absensi  = $pegawai->absensi()->whereDate('tanggal', now()->toDateString())->first();
        $hariIni  = now()->toDateString();
        $shiftToday = $pegawai->jadwalShift()->whereDate('tanggal', $hariIni)->first();

        $p = $this->statusService->getPengaturan();

        // Informasikan jam referensi sesuai shift atau normal
        $jamMasukKantor  = $shiftToday
            ? JadwalShift::getJamMulai($shiftToday->shift)
            : $p['jam_masuk_standar'];

        $jamPulangKantor = $shiftToday
            ? JadwalShift::getJamSelesai($shiftToday->shift)
            : $p['jam_pulang_standar'];

        // Jika sudah presensi masuk, hitung jam pulang yang diizinkan
        $jamPulangDiizinkan = null;
        if ($absensi && $absensi->jam_masuk) {
            $jamPulangDiizinkan = $absensi->jam_pulang_diizinkan
                ?? $this->statusService->hitungJamPulangDiizinkan($absensi->jam_masuk, $shiftToday);
        }

        return response()->json([
            'data'                   => $absensi ? $this->format($absensi) : null,
            'jam_masuk_kantor'       => $jamMasukKantor,
            'jam_pulang_kantor'      => $jamPulangKantor,
            'jam_pulang_diizinkan'   => $jamPulangDiizinkan,
            'flexible_work_hours'    => $p['flexible_work_hours_enabled'],
            'office_lat'             => (float) Pengaturan::get('kantor_lat', -6.167034493339591),
            'office_lng'             => (float) Pengaturan::get('kantor_lng', 106.82246468208389),
            'office_radius_meters'   => (float) Pengaturan::get('radius_gps', 100),
        ]);
    }

    public function masuk(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404);

        $data = $request->validate([
            'latitude'         => ['required', 'numeric'],
            'longitude'        => ['required', 'numeric'],
            'foto'             => ['required', 'image', 'max:4096'],
            // Data deteksi GPS dari mobile (opsional — app lama tidak mengirim ini)
            'is_mock_location' => ['sometimes', 'nullable', 'boolean'],
            'accuracy'         => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ]);

        $hariIni    = now()->toDateString();
        $shiftToday = $pegawai->jadwalShift()->whereDate('tanggal', $hariIni)->first();

        abort_if(
            now()->isWeekend() && !$shiftToday,
            422,
            'Hari ini adalah hari libur (akhir pekan), presensi tidak diperlukan.'
        );

        $existing = $pegawai->absensi()->whereDate('tanggal', $hariIni)->first();
        abort_if($existing && $existing->jam_masuk, 422, 'Anda sudah presensi masuk hari ini.');

        $wfhEnabled = (bool) Pengaturan::get('wfh_enabled', '1');
        $wfhDays = json_decode(Pengaturan::get('wfh_days', '["friday"]'), true) ?? ['friday'];
        $dayNameLower = strtolower(now()->englishDayOfWeek);
        $isHariWfh = $wfhEnabled && in_array($dayNameLower, $wfhDays);

        $radius = (float) Pengaturan::get('radius_gps', 100);
        $keteranganMasuk = 'Presensi masuk via mobile';
        $wfhFlagNote = null;

        if ($isHariWfh && $pegawai->lat_domisili && $pegawai->lng_domisili) {
            $jarakDomisili = $this->hitungJarakTitik((float)$data['latitude'], (float)$data['longitude'], (float)$pegawai->lat_domisili, (float)$pegawai->lng_domisili);
            if ($jarakDomisili <= $radius) {
                $keteranganMasuk = 'Presensi masuk WFH (Di Domisili)';
            } else {
                $keteranganMasuk = "Presensi masuk WFH (Di Luar Domisili - {$jarakDomisili}m dari rumah)";
                $wfhFlagNote = "Presensi WFH di luar radius domisili ({$jarakDomisili}m)";
            }
        } else {
            // Validasi GPS radius kantor standar
            $jarak = $this->hitungJarak($data['latitude'], $data['longitude']);
            abort_if($jarak > $radius, 422, "Anda berada di luar radius kantor ({$jarak} m dari kantor, maksimal {$radius} m).");
        }

        $jamMasukStr = now()->format('H:i:s');

        // Validasi jendela waktu presensi masuk
        $errorJendela = $this->statusService->validasiJendelaPresensiMasuk($jamMasukStr, $shiftToday, now());
        abort_if($errorJendela !== null, 422, $errorJendela);

        // Hitung status & jam pulang yang diizinkan menggunakan service
        $status              = $this->statusService->hitungStatusMasuk($jamMasukStr, $shiftToday);
        $jamPulangDiizinkan  = $this->statusService->hitungJamPulangDiizinkan($jamMasukStr, $shiftToday);

        $fotoPath = $request->file('foto')->store('absensi', 'public');

        // ---------------------------------------------------------------
        // Evaluasi indikasi GPS spoofing & WFH flag (observatif — tidak memblokir)
        // ---------------------------------------------------------------
        $isMock   = isset($data['is_mock_location']) ? (bool) $data['is_mock_location'] : null;
        $accuracy = isset($data['accuracy']) ? (float) $data['accuracy'] : null;

        [$flagReview, $catatanFlag] = $this->evaluasiGpsFlag(
            isMock   : $isMock,
            accuracy : $accuracy,
            pegawai  : $pegawai,
            lat      : (float) $data['latitude'],
            lng      : (float) $data['longitude'],
        );

        if ($wfhFlagNote) {
            $flagReview = true;
            $catatanFlag = trim(($catatanFlag ? $catatanFlag . '; ' : '') . $wfhFlagNote);
        }
        // ---------------------------------------------------------------

        $absensi = Absensi::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'tanggal' => $hariIni],
            [
                'jam_masuk'            => $jamMasukStr,
                'jam_pulang_diizinkan' => $jamPulangDiizinkan,
                'latitude_masuk'       => $data['latitude'],
                'longitude_masuk'      => $data['longitude'],
                'status'               => $status,
                'keterangan'           => $keteranganMasuk,
                'foto_masuk'           => $fotoPath,
                'is_mock_location'     => $isMock,
                'gps_accuracy'         => $accuracy,
                'flag_review'          => $flagReview,
                'catatan_flag'         => $catatanFlag ?: null,
            ]
        );

        AuditLog::catat('presensi masuk (mobile)', 'Absensi', $absensi->id, $pegawai->nama);

        return response()->json([
            'message'              => 'Presensi masuk berhasil dicatat.',
            'data'                 => $this->format($absensi),
            'jam_pulang_diizinkan' => $jamPulangDiizinkan,
        ]);
    }

    public function keluar(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404);

        $data = $request->validate([
            'latitude'         => ['required', 'numeric'],
            'longitude'        => ['required', 'numeric'],
            // Data deteksi GPS dari mobile (opsional)
            'is_mock_location' => ['sometimes', 'nullable', 'boolean'],
            'accuracy'         => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ]);

        $hariIni    = now()->toDateString();
        $shiftToday = $pegawai->jadwalShift()->whereDate('tanggal', $hariIni)->first();

        abort_if(
            now()->isWeekend() && !$shiftToday,
            422,
            'Hari ini adalah hari libur (akhir pekan), presensi tidak diperlukan.'
        );

        $absensi = $pegawai->absensi()->whereDate('tanggal', $hariIni)->first();
        abort_unless($absensi && $absensi->jam_masuk, 422, 'Anda belum presensi masuk hari ini.');
        abort_if($absensi->jam_keluar, 422, 'Anda sudah presensi keluar hari ini.');

        $jamKeluarStr       = now()->format('H:i:s');
        $jamPulangDiizinkan = $absensi->jam_pulang_diizinkan;

        // Hitung total menit pengurangan jam kerja (menit telat + menit pulang cepat)
        $hasilPengurangan = $this->statusService->hitungTotalMenitPengurangan(
            $absensi->jam_masuk,
            $jamKeluarStr,
            $shiftToday,
            $jamPulangDiizinkan
        );

        $updateData = [
            'jam_keluar'                  => $jamKeluarStr,
            'latitude_keluar'             => $data['latitude'],
            'longitude_keluar'            => $data['longitude'],
            'menit_pengurangan_jam_kerja' => $hasilPengurangan['total_menit_pengurangan'] ?: null,
        ];

        // ---------------------------------------------------------------
        // Evaluasi indikasi GPS spoofing saat keluar (observatif)
        // Hanya update flag jika ada indikasi baru; jangan hapus flag masuk
        // ---------------------------------------------------------------
        $isMockKeluar   = isset($data['is_mock_location']) ? (bool) $data['is_mock_location'] : null;
        $accuracyKeluar = isset($data['accuracy']) ? (float) $data['accuracy'] : null;

        if ($isMockKeluar !== null || $accuracyKeluar !== null) {
            $flagsKeluar = [];
            if ($isMockKeluar === true) {
                $flagsKeluar[] = 'Saat keluar: Mock location terdeteksi pada perangkat';
            }
            if ($accuracyKeluar !== null && $accuracyKeluar < 1.0) {
                $flagsKeluar[] = 'Saat keluar: Akurasi GPS tidak biasa (' . number_format($accuracyKeluar, 2) . 'm)';
            }

            if (!empty($flagsKeluar)) {
                // Gabungkan dengan catatan flag masuk yang sudah ada
                $catatanLama     = $absensi->catatan_flag ?? '';
                $catatanBaru     = trim($catatanLama . ($catatanLama ? '; ' : '') . implode('; ', $flagsKeluar));
                $updateData['flag_review']  = true;
                $updateData['catatan_flag'] = $catatanBaru;
            }
        }
        // ---------------------------------------------------------------

        $absensi->update($updateData);

        AuditLog::catat('presensi keluar (mobile)', 'Absensi', $absensi->id, $pegawai->nama);

        $pesan = 'Presensi keluar berhasil dicatat.';
        if ($hasilPengurangan['total_menit_pengurangan'] > 0) {
            $menit  = $hasilPengurangan['total_menit_pengurangan'];
            $pesan .= " Catatan: Pengurangan jam kerja {$menit} menit.";
        }

        return response()->json([
            'message'          => $pesan,
            'data'             => $this->format($absensi->fresh()),
            'pulang_cepat'     => $hasilPengurangan['menit_pulang_cepat'] > 0,
            'menit_pengurangan'=> $hasilPengurangan['total_menit_pengurangan'],
        ]);
    }

    private function hitungJarak(float $lat, float $lng): float
    {
        $kantorLat = (float) Pengaturan::get('kantor_lat', -6.211544);
        $kantorLng = (float) Pengaturan::get('kantor_lng', 106.845172);

        return $this->hitungJarakTitik($lat, $lng, $kantorLat, $kantorLng);
    }

    private function hitungJarakTitik(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat1 - $lat2);
        $dLng = deg2rad($lng1 - $lng2);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat2)) * cos(deg2rad($lat1)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c);
    }

    /**
     * Evaluasi seluruh indikasi GPS spoofing yang tersedia dan kembalikan
     * [bool $flagReview, string $catatanFlag].
     *
     * PRINSIP: Observatif saja — tidak ada penolakan presensi.
     * Bahasa catatan dibuat netral (tidak menuduh).
     */
    private function evaluasiGpsFlag(
        ?bool   $isMock,
        ?float  $accuracy,
        $pegawai,
        float   $lat,
        float   $lng,
    ): array {
        $flags = [];

        // ── Sinyal 1: Mock Location Provider (kepercayaan TINGGI) ──────────
        if ($isMock === true) {
            $flags[] = 'Mock location terdeteksi pada perangkat (kepercayaan tinggi)';
        }

        // ── Sinyal 2: Akurasi GPS tidak biasa (kepercayaan SEDANG) ─────────
        // < 1.0m dianggap tidak wajar untuk GPS biasa; 0.0 hampir pasti tidak nyata
        if ($accuracy !== null && $accuracy < 1.0) {
            $flags[] = 'Akurasi GPS tidak biasa: ' . number_format($accuracy, 2) . 'm (kepercayaan sedang)';
        }

        // ── Sinyal 3: Lompatan lokasi tidak wajar (kepercayaan SEDANG) ─────
        // Bandingkan hanya dengan presensi masuk hari KEMARIN (max 1 hari ke belakang)
        // untuk mengurangi false positive dari hari-hari non-kerja atau WFH
        $lompatanFlag = $this->hitungLompatanLokasi($pegawai, $lat, $lng);
        if ($lompatanFlag) {
            $flags[] = $lompatanFlag;
        }

        $flagReview  = !empty($flags);
        $catatanFlag = implode('; ', $flags);

        return [$flagReview, $catatanFlag];
    }

    /**
     * Cek lompatan lokasi antara presensi masuk hari ini vs. kemarin.
     * Mengembalikan string deskripsi flag jika ada indikasi, null jika normal.
     *
     * Ambang batas: kecepatan implisit > 1000 km/jam dianggap tidak wajar.
     * Toleransi ini sengaja sangat longgar untuk menghindari false positive.
     */
    private function hitungLompatanLokasi($pegawai, float $latHariIni, float $lngHariIni): ?string
    {
        $kemarin = now()->subDay()->toDateString();

        $absensiKemarin = $pegawai->absensi()
            ->whereDate('tanggal', $kemarin)
            ->whereNotNull('latitude_masuk')
            ->whereNotNull('longitude_masuk')
            ->first();

        if (!$absensiKemarin) {
            return null; // Tidak ada data pembanding, tidak bisa menilai
        }

        // Hitung jarak antara dua titik (meter)
        $latLama = (float) $absensiKemarin->latitude_masuk;
        $lngLama = (float) $absensiKemarin->longitude_masuk;

        $earthRadius = 6371000;
        $dLat = deg2rad($latHariIni - $latLama);
        $dLng = deg2rad($lngHariIni - $lngLama);
        $a    = sin($dLat / 2) ** 2 + cos(deg2rad($latLama)) * cos(deg2rad($latHariIni)) * sin($dLng / 2) ** 2;
        $c    = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $jarakMeter = $earthRadius * $c;

        // Selisih waktu antara jam masuk kemarin dan sekarang (detik)
        $jamMasukKemarin = $absensiKemarin->jam_masuk; // format H:i:s
        if (!$jamMasukKemarin) {
            return null;
        }

        $datetimeKemarin = \Carbon\Carbon::parse($kemarin . ' ' . $jamMasukKemarin);
        $selisihDetik    = abs(now()->diffInSeconds($datetimeKemarin));

        if ($selisihDetik < 60) {
            return null; // Terlalu dekat waktunya, skip
        }

        // Kecepatan implisit dalam km/jam
        $kecepatanKmJam = ($jarakMeter / 1000) / ($selisihDetik / 3600);

        // Hanya flag jika kecepatan implisit > 1000 km/jam (mustahil secara fisik tanpa pesawat supersonik)
        if ($kecepatanKmJam > 1000) {
            $jarakKm = round($jarakMeter / 1000, 1);
            return "Lompatan lokasi tidak wajar: {$jarakKm} km dari presensi kemarin dalam "
                . round($selisihDetik / 3600, 1) . ' jam (kepercayaan sedang)';
        }

        return null;
    }

    private function format(Absensi $a): array
    {
        return [
            'id'                          => $a->id,
            'tanggal'                     => $a->tanggal->toDateString(),
            'jam_masuk'                   => $a->jam_masuk,
            'jam_keluar'                  => $a->jam_keluar,
            'jam_pulang_diizinkan'        => $a->jam_pulang_diizinkan,
            'menit_pengurangan_jam_kerja' => $a->menit_pengurangan_jam_kerja,
            'status'                      => $a->status,
            'keterangan'                  => $a->keterangan,
        ];
    }
}
