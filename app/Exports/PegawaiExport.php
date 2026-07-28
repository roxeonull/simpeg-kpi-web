<?php

namespace App\Exports;

use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PegawaiExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Pegawai::with(['jabatan', 'unit'])->orderBy('nama')->get();
    }

    public function headings(): array
    {
        return ['NIP', 'Nama', 'Jabatan', 'Unit Kerja', 'Status Kepegawaian', 'TMT', 'Status Aktif'];
    }

    public function map($pegawai): array
    {
        return [
            $pegawai->nip,
            $pegawai->nama,
            $pegawai->jabatan?->nama_jabatan,
            $pegawai->unit?->nama_unit,
            $pegawai->status_kepegawaian,
            optional($pegawai->tmt)->format('d-m-Y'),
            $pegawai->status_aktif,
        ];
    }
}
