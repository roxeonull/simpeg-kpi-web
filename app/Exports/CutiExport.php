<?php

namespace App\Exports;

use App\Models\Cuti;
use App\Models\UnitKerja;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CutiExport implements FromArray, WithHeadings
{
    public ?string $tahun;
    public ?string $by;
    public ?array $unitIds;
    public ?array $pegawaiIds;

    public function __construct(
        ?string $tahun = null,
        ?string $by = null,
        ?array $unitIds = null,
        ?array $pegawaiIds = null
    ) {
        $this->tahun = $tahun ?: now()->year;
        $this->by = $by;
        $this->unitIds = $unitIds;
        $this->pegawaiIds = $pegawaiIds;
    }

    public function headings(): array
    {
        if ($this->by === 'unit') {
            return ['Kode Unit', 'Nama Unit Kerja', 'Total Pegawai', 'Total Pengajuan Cuti', 'Total Hari Cuti Terpakai'];
        }

        return ['NIP', 'Nama Pegawai', 'Unit Kerja', 'Jenis Cuti', 'Tanggal Mulai', 'Tanggal Selesai', 'Jumlah Hari', 'Status'];
    }

    public function array(): array
    {
        if ($this->by === 'unit') {
            $units = UnitKerja::withCount(['pegawais'])
                ->when($this->unitIds, fn ($q) => $q->whereIn('id', $this->unitIds))
                ->orderBy('nama_unit')
                ->get();

            $rows = [];
            foreach ($units as $u) {
                $cutis = Cuti::whereHas('pegawai', fn ($q) => $q->where('unit_id', $u->id))
                    ->whereYear('tanggal_mulai', $this->tahun)
                    ->get();

                $totalPengajuan = $cutis->count();
                $totalHari = $cutis->sum('jumlah_hari');

                $rows[] = [
                    $u->kode_unit ?? '—',
                    $u->nama_unit,
                    $u->pegawais_count,
                    $totalPengajuan,
                    $totalHari,
                ];
            }
            return $rows;
        }

        // Default & Riwayat Cuti Individu
        $cutis = Cuti::with(['pegawai.unit', 'jenisCuti'])
            ->when($this->tahun, fn ($q) => $q->whereYear('tanggal_mulai', $this->tahun))
            ->when($this->pegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $this->pegawaiIds))
            ->orderBy('tanggal_mulai')
            ->get();

        $rows = [];
        foreach ($cutis as $c) {
            $rows[] = [
                optional($c->pegawai)->nip,
                optional($c->pegawai)->nama,
                optional(optional($c->pegawai)->unit)->nama_unit,
                optional($c->jenisCuti)->nama ?? ucfirst($c->jenis_cuti),
                $c->tanggal_mulai ? $c->tanggal_mulai->format('d-m-Y') : '',
                $c->tanggal_selesai ? $c->tanggal_selesai->format('d-m-Y') : '',
                $c->jumlah_hari,
                $c->statusLabel(),
            ];
        }

        return $rows;
    }
}
