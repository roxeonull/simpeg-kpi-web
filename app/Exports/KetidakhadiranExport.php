<?php

namespace App\Exports;

use App\Models\Pegawai;
use App\Models\JenisKetidakhadiran;
use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KetidakhadiranExport implements FromArray, WithHeadings
{
    public function __construct(private string $bulan) {}

    public function headings(): array
    {
        $headings = ['NIP', 'Nama Pegawai', 'Unit Kerja'];
        $categories = JenisKetidakhadiran::orderBy('id')->pluck('nama')->toArray();
        foreach ($categories as $cat) {
            $headings[] = $cat;
        }
        $headings[] = 'Total';
        return $headings;
    }

    public function array(): array
    {
        $year = (int) substr($this->bulan, 0, 4);
        $month = (int) substr($this->bulan, 5, 2);

        $categories = JenisKetidakhadiran::orderBy('id')->get();
        $pegawais = Pegawai::with(['unit'])->where('status_aktif', 'aktif')->orderBy('nama')->get();

        $absensis = Absensi::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->whereNotNull('jenis_ketidakhadiran_id')
            ->get()
            ->groupBy('pegawai_id');

        $rows = [];
        foreach ($pegawais as $pegawai) {
            $row = [
                $pegawai->nip,
                $pegawai->nama,
                $pegawai->unit?->nama_unit ?? '—',
            ];

            $pegawaiAbs = $absensis->get($pegawai->id) ?? collect();
            $totalPegawai = 0;

            foreach ($categories as $cat) {
                $count = $pegawaiAbs->where('jenis_ketidakhadiran_id', $cat->id)->count();
                $row[] = $count;
                $totalPegawai += $count;
            }

            $row[] = $totalPegawai;
            $rows[] = $row;
        }

        return $rows;
    }
}
