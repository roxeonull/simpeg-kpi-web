<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiExport;
use App\Exports\CutiExport;
use App\Exports\KetidakhadiranExport;
use App\Exports\PegawaiExport;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\JadwalShift;
use App\Models\Pegawai;
use App\Models\RiwayatPelatihan;
use App\Models\SaldoCuti;
use App\Models\UnitKerja;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        $pegawais = Pegawai::with('unit')->orderBy('nama')->get();
        $units = UnitKerja::orderBy('nama_unit')->get();

        return view('laporan.index', compact('pegawais', 'units'));
    }

    public function pegawaiExcel(Request $request)
    {
        $type = $request->get('type');
        $pegawaiIds = $request->get('pegawai_ids') ? explode(',', $request->get('pegawai_ids')) : null;
        $tahun = $request->get('tahun', now()->year);

        $filename = 'data-pegawai.xlsx';
        if ($type === 'demografi') {
            $filename = 'demografi-pegawai.xlsx';
        } elseif ($type === 'slip') {
            $filename = 'slip-rekap-pegawai.xlsx';
        } elseif ($type === 'diklat') {
            $filename = "rekap-diklat-{$tahun}.xlsx";
        } elseif ($type === 'target_jp') {
            $filename = "laporan-target-jp-{$tahun}.xlsx";
        }

        return Excel::download(new PegawaiExport($type, $pegawaiIds, $tahun), $filename);
    }

    public function pegawaiPdf(Request $request)
    {
        $type = $request->get('type');
        $pegawaiIds = $request->get('pegawai_ids') ? explode(',', $request->get('pegawai_ids')) : null;
        $tahun = $request->get('tahun', now()->year);

        if ($type === 'demografi') {
            $pegawais = Pegawai::with(['jabatan', 'unit'])
                ->when($pegawaiIds, fn ($q) => $q->whereIn('id', $pegawaiIds))
                ->orderBy('nama')
                ->get();

            $totalPegawai = $pegawais->count();
            $totalLaki = $pegawais->where('jenis_kelamin', 'L')->count();
            $totalPerempuan = $pegawais->where('jenis_kelamin', 'P')->count();
            $rataRataUsia = round($pegawais->filter(fn ($p) => $p->tanggal_lahir)->avg(fn ($p) => Carbon::parse($p->tanggal_lahir)->age) ?? 0, 1);
            $pNSCount = $pegawais->where('status_kepegawaian', 'PNS')->count();
            $nonPNSCount = $totalPegawai - $pNSCount;

            $pdf = Pdf::loadView('laporan.demografi-pdf', [
                'pegawais' => $pegawais,
                'totalPegawai' => $totalPegawai,
                'totalLaki' => $totalLaki,
                'totalPerempuan' => $totalPerempuan,
                'rataRataUsia' => $rataRataUsia,
                'pNSCount' => $pNSCount,
                'nonPNSCount' => $nonPNSCount,
            ])->setPaper('a4', 'landscape');

            return $pdf->download('demografi-pegawai.pdf');
        }

        if ($type === 'slip') {
            $pegawais = Pegawai::with(['jabatan', 'unit'])
                ->when($pegawaiIds, fn ($q) => $q->whereIn('id', $pegawaiIds))
                ->orderBy('nama')
                ->get();

            $dataPegawaiList = [];
            foreach ($pegawais as $p) {
                $saldoCuti = SaldoCuti::where('pegawai_id', $p->id)->where('tahun', $tahun)->first();
                $cutiList = Cuti::where('pegawai_id', $p->id)->whereYear('tanggal_mulai', $tahun)->orderBy('tanggal_mulai')->get();
                $pelatihanList = RiwayatPelatihan::where('pegawai_id', $p->id)->whereYear('tanggal', $tahun)->orderBy('tanggal')->get();
                $totalJp = $pelatihanList->sum('durasi_jp');

                $absensiSummary = [
                    'hadir' => Absensi::where('pegawai_id', $p->id)->whereYear('tanggal', $tahun)->where('status', 'hadir')->count(),
                    'terlambat' => Absensi::where('pegawai_id', $p->id)->whereYear('tanggal', $tahun)->where('status', 'terlambat')->count(),
                    'izin' => Absensi::where('pegawai_id', $p->id)->whereYear('tanggal', $tahun)->whereIn('status', ['izin', 'sakit'])->count(),
                    'alpa' => Absensi::where('pegawai_id', $p->id)->whereYear('tanggal', $tahun)->where('status', 'alpa')->count(),
                    'potongan_menit' => Absensi::where('pegawai_id', $p->id)->whereYear('tanggal', $tahun)->sum('menit_pengurangan_jam_kerja'),
                ];

                $dataPegawaiList[] = [
                    'pegawai' => $p,
                    'saldoCuti' => $saldoCuti,
                    'cutiList' => $cutiList,
                    'pelatihanList' => $pelatihanList,
                    'totalJp' => $totalJp,
                    'absensiSummary' => $absensiSummary,
                ];
            }

            $pdf = Pdf::loadView('laporan.slip-rekap-pdf', ['dataPegawaiList' => $dataPegawaiList])->setPaper('a4', 'portrait');

            return $pdf->download('slip-rekap-pegawai.pdf');
        }

        if ($type === 'diklat') {
            $pelatihans = RiwayatPelatihan::with(['pegawai.unit'])
                ->when($pegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $pegawaiIds))
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal')
                ->get();

            $pdf = Pdf::loadView('laporan.diklat-pdf', [
                'pelatihans' => $pelatihans,
                'tahun' => $tahun,
            ])->setPaper('a4', 'landscape');

            return $pdf->download("rekap-diklat-{$tahun}.pdf");
        }

        if ($type === 'target_jp') {
            $pegawais = Pegawai::with(['jabatan', 'unit'])
                ->when($pegawaiIds, fn ($q) => $q->whereIn('id', $pegawaiIds))
                ->orderBy('nama')
                ->get();

            $targetJpDefault = 20;
            $reportList = [];

            foreach ($pegawais as $p) {
                $capaianJp = (int) RiwayatPelatihan::where('pegawai_id', $p->id)
                    ->whereYear('tanggal', $tahun)
                    ->sum('durasi_jp');

                $kekurangan = max(0, $targetJpDefault - $capaianJp);
                $status = $capaianJp >= $targetJpDefault ? 'Tercapai' : 'Belum Tercapai';

                $reportList[] = [
                    'nip' => $p->nip ?? '—',
                    'nama' => $p->nama,
                    'unit' => optional($p->unit)->nama_unit ?? '—',
                    'target_jp' => $targetJpDefault,
                    'capaian_jp' => $capaianJp,
                    'kekurangan' => $kekurangan,
                    'status' => $status,
                ];
            }

            $pdf = Pdf::loadView('laporan.target-jp-pdf', [
                'reportList' => $reportList,
                'tahun' => $tahun,
                'targetJpDefault' => $targetJpDefault,
            ])->setPaper('a4', 'landscape');

            return $pdf->download("laporan-target-jp-{$tahun}.pdf");
        }

        // Default Data Pegawai
        $pegawais = Pegawai::with(['jabatan', 'unit'])
            ->when($pegawaiIds, fn ($q) => $q->whereIn('id', $pegawaiIds))
            ->orderBy('nama')
            ->get();

        $pdf = Pdf::loadView('laporan.pegawai-pdf', ['pegawais' => $pegawais])->setPaper('a4', 'landscape');

        return $pdf->download('data-pegawai.pdf');
    }

    public function absensiExcel(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));
        $type = $request->get('type');
        $pegawaiIds = $request->get('pegawai_ids') ? explode(',', $request->get('pegawai_ids')) : null;

        $filename = "rekap-absensi-{$bulan}.xlsx";
        if ($type === 'pengurangan') {
            $filename = "pengurangan-jam-{$bulan}.xlsx";
        } elseif ($type === 'shift') {
            $filename = "jadwal-shift-{$bulan}.xlsx";
        }

        return Excel::download(new AbsensiExport($bulan, $type, $pegawaiIds), $filename);
    }

    public function absensiPdf(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));
        $type = $request->get('type');
        $pegawaiIds = $request->get('pegawai_ids') ? explode(',', $request->get('pegawai_ids')) : null;

        $year = (int) substr($bulan, 0, 4);
        $month = (int) substr($bulan, 5, 2);

        if ($type === 'pengurangan') {
            $absensis = Absensi::with(['pegawai.unit'])
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->where('menit_pengurangan_jam_kerja', '>', 0)
                ->when($pegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $pegawaiIds))
                ->orderByDesc('menit_pengurangan_jam_kerja')
                ->get();

            $pdf = Pdf::loadView('laporan.pengurangan-jam-pdf', [
                'absensis' => $absensis,
                'bulan' => $bulan,
            ])->setPaper('a4', 'landscape');

            return $pdf->download("pengurangan-jam-{$bulan}.pdf");
        }

        if ($type === 'shift') {
            $daysInMonth = Carbon::parse($bulan . '-01')->daysInMonth;
            $pegawais = Pegawai::with(['unit'])
                ->when($pegawaiIds, fn ($q) => $q->whereIn('id', $pegawaiIds))
                ->where('status_aktif', 'aktif')
                ->orderBy('nama')
                ->get();

            $shiftsRaw = JadwalShift::with(['statusShift'])
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->when($pegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $pegawaiIds))
                ->get();

            $shiftMap = [];
            foreach ($shiftsRaw as $s) {
                $dayNum = (int) $s->tanggal->format('j');
                $code = $s->statusShift ? substr($s->statusShift->nama, 0, 4) : ($s->shift ? "S{$s->shift}" : '—');
                $shiftMap[$s->pegawai_id][$dayNum] = $code;
            }

            $pdf = Pdf::loadView('laporan.jadwal-shift-pdf', [
                'pegawais' => $pegawais,
                'shiftMap' => $shiftMap,
                'daysInMonth' => $daysInMonth,
                'bulan' => $bulan,
            ])->setPaper('a4', 'landscape');

            return $pdf->download("jadwal-shift-{$bulan}.pdf");
        }

        // Detail Absensi Individu vs Rekap Bulanan Summary
        if ($pegawaiIds && count($pegawaiIds) === 1) {
            $absensis = Absensi::with(['pegawai.unit'])
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->whereIn('pegawai_id', $pegawaiIds)
                ->orderBy('tanggal')
                ->get();

            $pdf = Pdf::loadView('laporan.absensi-pdf', [
                'absensis' => $absensis,
                'bulan' => $bulan,
                'isDetail' => true,
            ])->setPaper('a4', 'landscape');

            return $pdf->download("detail-absensi-{$bulan}.pdf");
        }

        $pegawais = Pegawai::with(['unit'])
            ->when($pegawaiIds, fn ($q) => $q->whereIn('id', $pegawaiIds))
            ->where('status_aktif', 'aktif')
            ->orderBy('nama')
            ->get();

        $summaryList = [];
        foreach ($pegawais as $p) {
            $pAbs = Absensi::where('pegawai_id', $p->id)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->get();

            $summaryList[] = [
                'nip' => $p->nip ?? '—',
                'nama' => $p->nama,
                'unit' => optional($p->unit)->nama_unit ?? '—',
                'hadir' => $pAbs->where('status', 'hadir')->count(),
                'terlambat' => $pAbs->where('status', 'terlambat')->count(),
                'izin' => $pAbs->whereIn('status', ['izin', 'sakit'])->count(),
                'alpa' => $pAbs->where('status', 'alpa')->count(),
                'potongan_menit' => $pAbs->sum('menit_pengurangan_jam_kerja'),
            ];
        }

        $pdf = Pdf::loadView('laporan.absensi-pdf', [
            'summaryList' => $summaryList,
            'bulan' => $bulan,
            'isDetail' => false,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("rekap-absensi-{$bulan}.pdf");
    }

    public function cutiExcel(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);
        $by = $request->get('by');
        $unitIds = $request->get('unit_ids') ? explode(',', $request->get('unit_ids')) : null;
        $pegawaiIds = $request->get('pegawai_ids') ? explode(',', $request->get('pegawai_ids')) : null;

        $filename = "rekap-cuti-{$tahun}.xlsx";
        if ($by === 'unit') {
            $filename = "rekap-cuti-unit-{$tahun}.xlsx";
        }

        return Excel::download(new CutiExport($tahun, $by, $unitIds, $pegawaiIds), $filename);
    }

    public function cutiPdf(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);
        $by = $request->get('by');
        $unitIds = $request->get('unit_ids') ? explode(',', $request->get('unit_ids')) : null;
        $pegawaiIds = $request->get('pegawai_ids') ? explode(',', $request->get('pegawai_ids')) : null;

        if ($by === 'unit') {
            $units = UnitKerja::withCount(['pegawais'])
                ->when($unitIds, fn ($q) => $q->whereIn('id', $unitIds))
                ->orderBy('nama_unit')
                ->get();

            $unitReports = [];
            foreach ($units as $u) {
                $cutis = Cuti::with(['pegawai', 'jenisCuti'])
                    ->whereHas('pegawai', fn ($q) => $q->where('unit_id', $u->id))
                    ->whereYear('tanggal_mulai', $tahun)
                    ->orderBy('tanggal_mulai')
                    ->get();

                $unitReports[] = [
                    'kode_unit' => $u->kode_unit ?? '—',
                    'nama_unit' => $u->nama_unit,
                    'total_pegawai' => $u->pegawais_count,
                    'total_pengajuan' => $cutis->count(),
                    'total_hari' => $cutis->sum('jumlah_hari'),
                    'cutis' => $cutis,
                ];
            }

            $pdf = Pdf::loadView('laporan.cuti-unit-pdf', [
                'unitReports' => $unitReports,
                'tahun' => $tahun,
            ])->setPaper('a4', 'landscape');

            return $pdf->download("rekap-cuti-unit-{$tahun}.pdf");
        }

        $cutis = Cuti::with(['pegawai.unit', 'jenisCuti'])
            ->whereYear('tanggal_mulai', $tahun)
            ->when($pegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $pegawaiIds))
            ->orderBy('tanggal_mulai')
            ->get();

        $pdf = Pdf::loadView('laporan.cuti-pdf', [
            'cutis' => $cutis,
            'tahun' => $tahun,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("rekap-cuti-{$tahun}.pdf");
    }

    public function ketidakhadiranExcel(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));

        return Excel::download(new KetidakhadiranExport($bulan), "rekap-ketidakhadiran-{$bulan}.xlsx");
    }

    public function ketidakhadiranPdf(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));
        $year = (int) substr($bulan, 0, 4);
        $month = (int) substr($bulan, 5, 2);

        $categories = \App\Models\JenisKetidakhadiran::orderBy('id')->get();
        $pegawais = Pegawai::with(['unit'])->where('status_aktif', 'aktif')->orderBy('nama')->get();

        $absensis = Absensi::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->whereNotNull('jenis_ketidakhadiran_id')
            ->get()
            ->groupBy('pegawai_id');

        $pdf = Pdf::loadView('laporan.ketidakhadiran-pdf', [
            'pegawais' => $pegawais,
            'categories' => $categories,
            'absensis' => $absensis,
            'bulan' => $bulan,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("rekap-ketidakhadiran-{$bulan}.pdf");
    }
}
