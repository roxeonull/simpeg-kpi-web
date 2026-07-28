<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiExport;
use App\Exports\CutiExport;
use App\Exports\PegawaiExport;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Pegawai;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function pegawaiExcel()
    {
        return Excel::download(new PegawaiExport, 'data-pegawai.xlsx');
    }

    public function pegawaiPdf()
    {
        $pegawais = Pegawai::with(['jabatan', 'unit'])->orderBy('nama')->get();
        $pdf = Pdf::loadView('laporan.pegawai-pdf', ['pegawais' => $pegawais])->setPaper('a4', 'landscape');

        return $pdf->download('data-pegawai.pdf');
    }

    public function absensiExcel(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));

        return Excel::download(new AbsensiExport($bulan), "rekap-absensi-{$bulan}.xlsx");
    }

    public function absensiPdf(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));
        $absensis = Absensi::with('pegawai')
            ->whereYear('tanggal', substr($bulan, 0, 4))
            ->whereMonth('tanggal', substr($bulan, 5, 2))
            ->orderBy('tanggal')
            ->get();

        $pdf = Pdf::loadView('laporan.absensi-pdf', ['absensis' => $absensis, 'bulan' => $bulan])->setPaper('a4', 'landscape');

        return $pdf->download("rekap-absensi-{$bulan}.pdf");
    }

    public function cutiExcel(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        return Excel::download(new CutiExport($tahun), "rekap-cuti-{$tahun}.xlsx");
    }

    public function cutiPdf(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);
        $cutis = Cuti::with('pegawai')->whereYear('tanggal_mulai', $tahun)->orderBy('tanggal_mulai')->get();

        $pdf = Pdf::loadView('laporan.cuti-pdf', ['cutis' => $cutis, 'tahun' => $tahun])->setPaper('a4', 'landscape');

        return $pdf->download("rekap-cuti-{$tahun}.pdf");
    }

    public function ketidakhadiranExcel(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));

        return Excel::download(new \App\Exports\KetidakhadiranExport($bulan), "rekap-ketidakhadiran-{$bulan}.xlsx");
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
            'bulan' => $bulan
        ])->setPaper('a4', 'landscape');

        return $pdf->download("rekap-ketidakhadiran-{$bulan}.pdf");
    }
}
