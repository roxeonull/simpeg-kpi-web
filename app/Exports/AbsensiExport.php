<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AbsensiExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private string $bulan) {}

    public function collection()
    {
        return Absensi::with('pegawai')
            ->whereYear('tanggal', substr($this->bulan, 0, 4))
            ->whereMonth('tanggal', substr($this->bulan, 5, 2))
            ->orderBy('tanggal')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'NIP',
            'Nama',
            'Jam Masuk',
            'Jam Keluar',
            'Status',
            'Jam Pulang Diizinkan',
            'Pengurangan Jam Kerja (menit)',
            'Keterangan',
        ];
    }

    public function map($absensi): array
    {
        return [
            $absensi->tanggal->format('d-m-Y'),
            $absensi->pegawai?->nip,
            $absensi->pegawai?->nama,
            $absensi->jam_masuk ? substr($absensi->jam_masuk, 0, 5) : '',
            $absensi->jam_keluar ? substr($absensi->jam_keluar, 0, 5) : '',
            ucfirst($absensi->status),
            $absensi->jam_pulang_diizinkan ?? '',
            $absensi->menit_pengurangan_jam_kerja ?? '',
            $absensi->keterangan,
        ];
    }
}

