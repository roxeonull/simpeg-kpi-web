<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\JadwalShift;
use App\Models\Pegawai;
use App\Models\PengajuanPerubahanData;
use App\Models\RiwayatPelatihan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $scopePegawaiIds = $this->scopedPegawaiIds($user);
        $hariIni = now()->toDateString();
        $currentYear = now()->year;

        // 1. Stat Figures
        $totalPegawai = Pegawai::when($scopePegawaiIds, fn ($q) => $q->whereIn('id', $scopePegawaiIds))
            ->where('status_aktif', 'aktif')->count();

        $hadirHariIni = Absensi::when($scopePegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $scopePegawaiIds))
            ->whereDate('tanggal', $hariIni)
            ->whereIn('status', ['hadir', 'telat'])
            ->count();

        $tingkatKehadiran = $totalPegawai > 0 ? round(($hadirHariIni / $totalPegawai) * 100) : 0;

        // 2. Pending Actions ("BUTUH TINDAKAN")
        $cutiMenunggu = Cuti::when($scopePegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $scopePegawaiIds))
            ->whereIn('status', ['menunggu_atasan', 'menunggu_hr'])
            ->count();

        $pelatihanMenunggu = RiwayatPelatihan::when($scopePegawaiIds, fn ($q) => $q->whereHas('pegawai', fn ($p) => $p->whereIn('id', $scopePegawaiIds)))
            ->where('status_verifikasi', 'menunggu')
            ->count();

        $perubahanDataMenunggu = PengajuanPerubahanData::when($scopePegawaiIds, fn ($q) => $q->whereHas('pegawai', fn ($p) => $p->whereIn('id', $scopePegawaiIds)))
            ->where('status', 'menunggu')
            ->count();

        $totalButuhTindakan = $cutiMenunggu + $pelatihanMenunggu + $perubahanDataMenunggu;

        // 3. Ringkasan Cuti Hari Ini
        $cutiHariIniList = Cuti::with(['pegawai.unit', 'jenisCuti'])
            ->when($scopePegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $scopePegawaiIds))
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $hariIni)
            ->whereDate('tanggal_selesai', '>=', $hariIni)
            ->orderBy('tanggal_selesai', 'asc')
            ->limit(5)
            ->get();

        $totalCutiHariIniCount = Cuti::when($scopePegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $scopePegawaiIds))
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $hariIni)
            ->whereDate('tanggal_selesai', '>=', $hariIni)
            ->count();

        // 4. Ringkasan Jadwal Shift Hari Ini
        $jadwalShiftToday = JadwalShift::when($scopePegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $scopePegawaiIds))
            ->whereDate('tanggal', $hariIni)
            ->get();

        $shiftCounts = [
            'shift_1' => $jadwalShiftToday->where('shift', '1')->count(),
            'shift_2' => $jadwalShiftToday->where('shift', '2')->count(),
            'shift_3' => $jadwalShiftToday->where('shift', '3')->count(),
            'total'   => $jadwalShiftToday->count(),
        ];

        // 5. Capaian JP Diklat
        $targetJp = 20;
        $pegawaiCapaianDiklat = Pegawai::when($scopePegawaiIds, fn ($q) => $q->whereIn('id', $scopePegawaiIds))
            ->where('status_aktif', 'aktif')
            ->withSum(['riwayatPelatihan as total_jp' => function ($q) use ($currentYear) {
                $q->where('status_verifikasi', 'terverifikasi')
                  ->whereYear('tanggal', $currentYear);
            }], 'durasi_jp')
            ->get()
            ->filter(fn ($p) => ($p->total_jp ?? 0) >= $targetJp)
            ->count();

        // 6. Grafik Kehadiran (Filter Periode: 7 Hari / 30 Hari / Bulan Ini)
        $periode = $request->get('periode', '7');
        if ($periode === '30') {
            $days = 29;
        } elseif ($periode === 'bulan_ini') {
            $days = max(0, now()->day - 1);
        } else {
            $periode = '7';
            $days = 6;
        }

        $grafikMingguan = collect(range($days, 0))->map(function ($i) use ($scopePegawaiIds, $periode) {
            $tanggal = now()->subDays($i);
            $hadir = Absensi::when($scopePegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $scopePegawaiIds))
                ->whereDate('tanggal', $tanggal->toDateString())
                ->whereIn('status', ['hadir', 'telat'])
                ->count();

            return [
                'label' => in_array($periode, ['30', 'bulan_ini']) ? $tanggal->format('d/m') : $tanggal->translatedFormat('D'),
                'total' => $hadir,
            ];
        });

        if ($request->wantsJson()) {
            return response()->json([
                'labels' => $grafikMingguan->pluck('label'),
                'totals' => $grafikMingguan->pluck('total'),
            ]);
        }

        // 7. Presensi Hari Ini Rincian
        $jamPulang = \App\Models\Pengaturan::get('jam_pulang', '16:30');
        $isPastJamPulang = now()->format('H:i') >= $jamPulang;

        $todayAbsensi = Absensi::when($scopePegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $scopePegawaiIds))
            ->whereDate('tanggal', $hariIni)
            ->get();

        $hadirCount = $todayAbsensi->where('status', 'hadir')->count();
        $telatCount = $todayAbsensi->where('status', 'telat')->count();
        $izinSakitCount = $todayAbsensi->whereIn('status', ['izin', 'sakit'])->count();
        
        $recordedAlpaCount = $todayAbsensi->where('status', 'alpa')->count();
        $unrecordedCount = max(0, $totalPegawai - $todayAbsensi->count());

        if ($isPastJamPulang) {
            $alpaCount = $recordedAlpaCount + $unrecordedCount;
            $belumPresensiCount = 0;
        } else {
            $alpaCount = $recordedAlpaCount;
            $belumPresensiCount = $unrecordedCount;
        }

        // 8. Aktivitas Terbaru
        $aktivitasTerbaru = AuditLog::with('user')->latest('created_at')->limit(8)->get();

        return view('dashboard', [
            'totalPegawai' => $totalPegawai,
            'tingkatKehadiran' => $tingkatKehadiran,
            'cutiMenunggu' => $cutiMenunggu,
            'pelatihanMenunggu' => $pelatihanMenunggu,
            'perubahanDataMenunggu' => $perubahanDataMenunggu,
            'totalButuhTindakan' => $totalButuhTindakan,
            'cutiHariIniList' => $cutiHariIniList,
            'totalCutiHariIniCount' => $totalCutiHariIniCount,
            'shiftCounts' => $shiftCounts,
            'pegawaiCapaianDiklat' => $pegawaiCapaianDiklat,
            'targetJp' => $targetJp,
            'grafikMingguan' => $grafikMingguan,
            'selectedPeriode' => $periode,
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'rincianKehadiran' => [
                'hadir' => $hadirCount,
                'telat' => $telatCount,
                'izin_sakit' => $izinSakitCount,
                'alpa' => $alpaCount,
                'belum_presensi' => $belumPresensiCount,
            ]
        ]);
    }

    /**
     * Atasan only sees their team; Admin/HR sees everyone (returns null = no scope).
     */
    private function scopedPegawaiIds($user): ?array
    {
        if ($user->role === 'atasan' && $user->pegawai) {
            return $user->pegawai->anggotaTim()->pluck('id')->push($user->pegawai->id)->toArray();
        }

        return null;
    }
}
