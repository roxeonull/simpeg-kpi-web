<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CutiAnalyticsController extends Controller
{
    /**
     * Tampilan Kalender Tim Bulanan.
     */
    public function kalender(Request $request)
    {
        $user = $request->user();

        // Date navigation (default ke bulan/tahun sekarang)
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Mengambil grid Senin-Minggu dari bulan terpilih
        $startOfWeek = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $days = [];
        $currentDay = $startOfWeek->copy();
        while ($currentDay->lte($endOfWeek)) {
            $days[] = $currentDay->copy();
            $currentDay->addDay();
        }

        // Query cuti yang beririsan dengan grid kalender, kecuali yang ditolak
        $cutiQuery = Cuti::with(['pegawai.unit', 'jenisCuti'])
            ->where('status', '!=', 'ditolak')
            ->where(function ($q) use ($startOfWeek, $endOfWeek) {
                $q->whereBetween('tanggal_mulai', [$startOfWeek, $endOfWeek])
                  ->orWhereBetween('tanggal_selesai', [$startOfWeek, $endOfWeek])
                  ->orWhere(function ($q2) use ($startOfWeek, $endOfWeek) {
                      $q2->where('tanggal_mulai', '<=', $startOfWeek)
                         ->where('tanggal_selesai', '>=', $endOfWeek);
                  });
            });

        // Batasi scope jika pengguna adalah Atasan
        if ($user->role === 'atasan' && $user->pegawai) {
            $ids = $user->pegawai->anggotaTim()->pluck('id')->toArray();
            $cutiQuery->whereIn('pegawai_id', $ids);
        }

        // Filter dari dropdown
        if ($unitId = $request->input('unit_id')) {
            $cutiQuery->whereHas('pegawai', fn ($q) => $q->where('unit_id', $unitId));
        }

        if ($jenisCuti = $request->input('jenis_cuti')) {
            $cutiQuery->where(function ($q) use ($jenisCuti) {
                $q->where('jenis_cuti_id', $jenisCuti)
                  ->orWhere('jenis_cuti', $jenisCuti);
            });
        }

        $cutis = $cutiQuery->get();
        $units = UnitKerja::all();

        return view('cuti.kalender', [
            'days' => $days,
            'month' => $month,
            'year' => $year,
            'cutis' => $cutis,
            'units' => $units,
            'jenisCutis' => \App\Models\JenisCuti::orderBy('nama')->get(),
            'filters' => $request->only(['unit_id', 'jenis_cuti']),
            'currentMonthLabel' => $startOfMonth->translatedFormat('F Y'),
        ]);
    }

    /**
     * Tampilan Analitik & Visualisasi Grafik Cuti.
     */
    public function analitik(Request $request)
    {
        $user = $request->user();

        // Base Query scoped to role
        $queryBase = Cuti::query();
        if ($user->role === 'atasan' && $user->pegawai) {
            $ids = $user->pegawai->anggotaTim()->pluck('id')->toArray();
            $queryBase->whereIn('pegawai_id', $ids);
        }

        $year = (int) $request->input('year', now()->year);
        $queryYear = (clone $queryBase)->whereYear('tanggal_mulai', $year);

        // 1. Total Pengajuan (tahun berjalan)
        $totalPengajuan = $queryYear->count();

        // 2. Tingkat Persetujuan (%)
        $disetujuiCount = (clone $queryYear)->where('status', 'disetujui')->count();
        $ditolakCount = (clone $queryYear)->where('status', 'ditolak')->count();
        $decidedCount = $disetujuiCount + $ditolakCount;
        $tingkatPersetujuan = $decidedCount > 0 ? round(($disetujuiCount / $decidedCount) * 100) : 0;

        // 3. Rata-rata Hari per Pengajuan
        $rataRataHari = round((clone $queryYear)->avg('jumlah_hari') ?? 0, 1);

        // 4. Total Hari Cuti Terpakai (status = disetujui)
        $totalHariTerpakai = (clone $queryYear)->where('status', 'disetujui')->sum('jumlah_hari');

        // -- Tren Perubahan dari Bulan/Periode Sebelumnya --
        $now = now();
        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfMonth();

        $prevMonthStart = $now->copy()->subMonth()->startOfMonth();
        $prevMonthEnd = $now->copy()->subMonth()->endOfMonth();

        // Current Month
        $currMonthQuery = (clone $queryBase)->whereBetween('tanggal_mulai', [$currentMonthStart, $currentMonthEnd]);
        $currTotal = $currMonthQuery->count();
        $currDisetujui = (clone $currMonthQuery)->where('status', 'disetujui')->sum('jumlah_hari');

        // Previous Month
        $prevMonthQuery = (clone $queryBase)->whereBetween('tanggal_mulai', [$prevMonthStart, $prevMonthEnd]);
        $prevTotal = $prevMonthQuery->count();
        $prevDisetujui = (clone $prevMonthQuery)->where('status', 'disetujui')->sum('jumlah_hari');

        // Perubahan persentase
        $changeTotal = $prevTotal > 0 ? round((($currTotal - $prevTotal) / $prevTotal) * 100) : null;
        $changeHari = $prevDisetujui > 0 ? round((($currDisetujui - $prevDisetujui) / $prevDisetujui) * 100) : null;

        // -- Chart 1: Tren Bulanan (Line Chart) --
        $monthlyTren = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthQuery = (clone $queryBase)->whereYear('tanggal_mulai', $year)->whereMonth('tanggal_mulai', $m);
            $monthlyTren[] = [
                'label' => Carbon::create()->month($m)->translatedFormat('M'),
                'total' => $monthQuery->count(),
                'disetujui' => (clone $monthQuery)->where('status', 'disetujui')->count(),
                'ditolak' => (clone $monthQuery)->where('status', 'ditolak')->count(),
            ];
        }

        // -- Chart 2: Perbandingan Unit Kerja (Bar Chart) --
        $units = UnitKerja::all();
        $unitComparison = [];
        foreach ($units as $unit) {
            $unitQuery = (clone $queryYear)->whereHas('pegawai', fn ($q) => $q->where('unit_id', $unit->id));
            $days = $unitQuery->where('status', 'disetujui')->sum('jumlah_hari');
            $count = $unitQuery->count();
            if ($count > 0 || $days > 0) {
                $unitComparison[] = [
                    'label' => $unit->nama_unit,
                    'total_hari' => (int) $days,
                    'total_pengajuan' => (int) $count,
                ];
            }
        }

        // -- Chart 3: Jenis Cuti (Breakdown) --
        $jenisCutiBreakdown = [];
        $allJenisCutis = \App\Models\JenisCuti::all();
        foreach ($allJenisCutis as $jc) {
            $legacyKeys = [];
            if ($jc->nama === 'Cuti Tahunan') $legacyKeys[] = 'tahunan';
            if ($jc->nama === 'Sakit/Cuti Sakit') $legacyKeys[] = 'sakit';
            if ($jc->nama === 'Cuti Bersalin Anak Ke-1 s.d 2') $legacyKeys[] = 'melahirkan';
            if ($jc->nama === 'Cuti Alasan Penting') $legacyKeys[] = 'lainnya';

            $jenisQuery = (clone $queryYear)->where(function ($q) use ($jc, $legacyKeys) {
                $q->where('jenis_cuti_id', $jc->id);
                if (!empty($legacyKeys)) {
                    $q->orWhereIn('jenis_cuti', $legacyKeys);
                }
            });

            $jenisCutiBreakdown[] = [
                'label' => $jc->nama,
                'total_pengajuan' => $jenisQuery->count(),
                'total_hari' => (int) (clone $jenisQuery)->where('status', 'disetujui')->sum('jumlah_hari'),
            ];
        }

        return view('cuti.analitik', [
            'totalPengajuan' => $totalPengajuan,
            'tingkatPersetujuan' => $tingkatPersetujuan,
            'rataRataHari' => $rataRataHari,
            'totalHariTerpakai' => $totalHariTerpakai,
            'changeTotal' => $changeTotal,
            'changeHari' => $changeHari,
            'year' => $year,
            'chartData' => [
                'labels' => collect($monthlyTren)->pluck('label'),
                'total' => collect($monthlyTren)->pluck('total'),
                'disetujui' => collect($monthlyTren)->pluck('disetujui'),
                'ditolak' => collect($monthlyTren)->pluck('ditolak'),
                
                'unitLabels' => collect($unitComparison)->pluck('label'),
                'unitHari' => collect($unitComparison)->pluck('total_hari'),
                'unitPengajuan' => collect($unitComparison)->pluck('total_pengajuan'),
                
                'jenisLabels' => collect($jenisCutiBreakdown)->pluck('label'),
                'jenisHari' => collect($jenisCutiBreakdown)->pluck('total_hari'),
                'jenisPengajuan' => collect($jenisCutiBreakdown)->pluck('total_pengajuan'),
            ]
        ]);
    }

    /**
     * Tampilan Rekomendasi Cerdas (Rule-Based dari Data Historis).
     */
    public function rekomendasi(Request $request)
    {
        $user = $request->user();

        // Base Query scoped to role
        $queryBase = Cuti::query();
        if ($user->role === 'atasan' && $user->pegawai) {
            $ids = $user->pegawai->anggotaTim()->pluck('id')->toArray();
            $queryBase->whereIn('pegawai_id', $ids);
        }

        $recommendations = [];

        // 1. Logika Volume Pending Tinggi
        $pendingCount = (clone $queryBase)->whereIn('status', ['menunggu_atasan', 'menunggu_hr'])->count();
        if ($pendingCount > 0) {
            $level = $pendingCount > 5 ? 'Tinggi' : 'Sedang';
            $recommendations[] = [
                'id' => 'volume-pending',
                'level' => $level,
                'kategori' => 'Kapasitas Approval',
                'judul' => 'Volume Pengajuan Tertunda (Pending) Tinggi',
                'deskripsi' => "Terdapat <strong>$pendingCount pengajuan cuti</strong> yang saat ini berstatus menunggu persetujuan. Disarankan untuk segera meninjau kapasitas approval atau mempercepat proses verifikasi.",
                'aksi_nama' => 'Tinjau Pengajuan',
                'aksi_link' => route('cuti.index', ['status' => 'menunggu']),
            ];
        }

        // 2. Logika Deteksi Konflik (Overlapping Leave within Same Unit)
        // Hanya melihat cuti aktif atau cuti mendatang
        $activeLeaves = (clone $queryBase)
            ->where('tanggal_selesai', '>=', now()->toDateString())
            ->whereIn('status', ['disetujui', 'menunggu_atasan', 'menunggu_hr'])
            ->with(['pegawai.unit'])
            ->get();

        $leavesByUnit = $activeLeaves->groupBy(function($c) {
            return $c->pegawai->unit_id ?? 0;
        });

        $konflikAktifCount = 0;
        foreach ($leavesByUnit as $unitId => $leaves) {
            if ($unitId == 0 || $leaves->count() < 2) continue;

            $unitName = $leaves->first()->pegawai->unit->nama_unit ?? 'Unit';
            $overlaps = [];

            // Bandingkan setiap pasang cuti untuk mengecek overlap tanggal
            for ($i = 0; $i < $leaves->count(); $i++) {
                for ($j = $i + 1; $j < $leaves->count(); $j++) {
                    $a = $leaves[$i];
                    $b = $leaves[$j];

                    if ($a->pegawai_id !== $b->pegawai_id && 
                        $a->tanggal_mulai->lte($b->tanggal_selesai) && 
                        $a->tanggal_selesai->gte($b->tanggal_mulai)) {
                        
                        $key = min($a->id, $b->id) . '-' . max($a->id, $b->id);
                        $overlaps[$key] = [$a, $b];
                    }
                }
            }

            if (count($overlaps) > 0) {
                $konflikAktifCount += count($overlaps);

                $names = [];
                foreach ($overlaps as $pair) {
                    $names[$pair[0]->pegawai->nama] = true;
                    $names[$pair[1]->pegawai->nama] = true;
                }

                $namesList = implode(', ', array_keys($names));

                $recommendations[] = [
                    'id' => 'conflict-' . $unitId,
                    'level' => 'Tinggi',
                    'kategori' => 'Konflik Jadwal',
                    'judul' => 'Bentrok Jadwal Cuti di ' . $unitName,
                    'deskripsi' => "Terdeteksi overlapping cuti antara pegawai di unit <strong>$unitName</strong> ($namesList). Harap tinjau ulang pembagian tugas atau koordinasikan dengan pegawai bersangkutan sebelum memberikan persetujuan.",
                    'aksi_nama' => 'Lihat Kalender Unit',
                    'aksi_link' => route('cuti.kalender', ['unit_id' => $unitId]),
                ];
            }
        }

        // 3. Logika Prediksi Lonjakan Musiman
        // Cari bulan historis tersibuk di tahun-tahun sebelumnya
        $historicalLeaves = Cuti::whereYear('tanggal_mulai', '<', now()->year)
            ->selectRaw('MONTH(tanggal_mulai) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderByDesc('count')
            ->get();

        $peakMonths = $historicalLeaves->take(2)->pluck('month')->toArray();
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $upcomingSurgeMonth = null;
        $currentMonthVal = now()->month;
        $nextMonthVal = now()->copy()->addMonth()->month;

        foreach ($peakMonths as $m) {
            if ($m == $currentMonthVal || $m == $nextMonthVal) {
                $upcomingSurgeMonth = $m;
                break;
            }
        }

        if ($upcomingSurgeMonth) {
            $recommendations[] = [
                'id' => 'surge-' . $upcomingSurgeMonth,
                'level' => 'Sedang',
                'kategori' => 'Tren Musiman',
                'judul' => 'Perkiraan Lonjakan Pengajuan Cuti: ' . $monthNames[$upcomingSurgeMonth],
                'deskripsi' => "Analisis pola historis menunjukkan bahwa bulan <strong>" . $monthNames[$upcomingSurgeMonth] . "</strong> memiliki volume pengajuan cuti yang relatif tinggi. Disarankan bagi atasan untuk berkoordinasi lebih awal mengenai jadwal piket.",
                'aksi_nama' => 'Tinjau Pengajuan',
                'aksi_link' => route('cuti.index'),
            ];
        }

        // 4. Logika Unit Berisiko Tinggi (rasio pegawai cuti di atas 25% dalam 14 hari ke depan)
        $unitsWithCounts = UnitKerja::withCount('pegawais')->get();
        $highRiskUnitName = 'Tidak Ada';
        $maxRatio = 0;

        foreach ($unitsWithCounts as $unit) {
            if ($unit->pegawais_count == 0) continue;

            // Jika Atasan, batasi hanya unit kerjanya sendiri
            if ($user->role === 'atasan' && $user->pegawai && $user->pegawai->unit_id !== $unit->id) {
                continue;
            }

            $employeesOnLeaveCount = Pegawai::where('unit_id', $unit->id)
                ->whereHas('cuti', function($q) {
                    $q->where('tanggal_selesai', '>=', now()->toDateString())
                      ->where('tanggal_mulai', '<=', now()->addDays(14)->toDateString())
                      ->whereIn('status', ['disetujui', 'menunggu_atasan', 'menunggu_hr']);
                })
                ->count();

            $ratio = $employeesOnLeaveCount / $unit->pegawais_count;

            if ($ratio > $maxRatio) {
                $maxRatio = $ratio;
                $highRiskUnitName = $unit->nama_unit . ' (' . round($ratio * 100) . '%)';
            }

            if ($ratio > 0.25) { // Ambang batas 25%
                $recommendations[] = [
                    'id' => 'risk-' . $unit->id,
                    'level' => 'Tinggi',
                    'kategori' => 'Kapasitas Operasional',
                    'judul' => 'Kapasitas Kritis: ' . $unit->nama_unit,
                    'deskripsi' => "Sebanyak <strong>" . round($ratio * 100) . "%</strong> pegawai (" . $employeesOnLeaveCount . " dari " . $unit->pegawais_count . " orang) di unit <strong>" . $unit->nama_unit . "</strong> sedang atau akan cuti dalam 14 hari ke depan. Hal ini berpotensi mengganggu kelancaran layanan.",
                    'aksi_nama' => 'Lihat Detail Kalender',
                    'aksi_link' => route('cuti.kalender', ['unit_id' => $unit->id]),
                ];
            }
        }

        return view('cuti.rekomendasi', [
            'highRiskUnit' => $highRiskUnitName,
            'konflikAktifCount' => $konflikAktifCount,
            'surgePred' => $upcomingSurgeMonth ? $monthNames[$upcomingSurgeMonth] : 'Tidak Ada',
            'rekomendasiCount' => count($recommendations),
            'recommendations' => $recommendations,
        ]);
    }
}
