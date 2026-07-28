<?php

namespace App\Exports;

use App\Models\Cuti;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CutiExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private ?string $tahun = null) {}

    public function collection()
    {
        return Cuti::with('pegawai')
            ->when($this->tahun, fn ($q) => $q->whereYear('tanggal_mulai', $this->tahun))
            ->orderBy('tanggal_mulai')
            ->get();
    }

    public function headings(): array
    {
        return ['NIP', 'Nama', 'Jenis Cuti', 'Tanggal Mulai', 'Tanggal Selesai', 'Jumlah Hari', 'Status'];
    }

    public function map($cuti): array
    {
        return [
            $cuti->pegawai?->nip,
            $cuti->pegawai?->nama,
            $cuti->jenis_cuti,
            $cuti->tanggal_mulai->format('d-m-Y'),
            $cuti->tanggal_selesai->format('d-m-Y'),
            $cuti->jumlah_hari,
            $cuti->statusLabel(),
        ];
    }
}
