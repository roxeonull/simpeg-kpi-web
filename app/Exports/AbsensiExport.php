<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\JadwalShift;
use App\Models\Pegawai;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbsensiExport implements FromArray, WithHeadings
{
    public string $bulan;
    public ?string $type;
    public ?array $pegawaiIds;

    public function __construct(
        string $bulan,
        ?string $type = null,
        ?array $pegawaiIds = null
    ) {
        $this->bulan = $bulan;
        $this->type = $type;
        $this->pegawaiIds = $pegawaiIds;
    }

    public function headings(): array
    {
        if ($this->type === 'shift') {
            $daysInMonth = Carbon::parse($this->bulan . '-01')->daysInMonth;
            $headings = ['NIP', 'Nama Pegawai', 'Unit Kerja'];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $headings[] = (string) $d;
            }
            return $headings;
        }

        if ($this->type === 'pengurangan') {
            return ['Tanggal', 'NIP', 'Nama Pegawai', 'Unit Kerja', 'Jam Masuk', 'Jam Keluar', 'Potongan Menit'];
        }

        return [
            'Tanggal',
            'NIP',
            'Nama Pegawai',
            'Unit Kerja',
            'Jam Masuk',
            'Jam Keluar',
            'Status',
            'Menit Potongan Jam Kerja',
            'Keterangan',
        ];
    }

    public function array(): array
    {
        $year = (int) substr($this->bulan, 0, 4);
        $month = (int) substr($this->bulan, 5, 2);

        if ($this->type === 'shift') {
            $daysInMonth = Carbon::parse($this->bulan . '-01')->daysInMonth;
            $pegawais = Pegawai::with(['unit'])
                ->when($this->pegawaiIds, fn ($q) => $q->whereIn('id', $this->pegawaiIds))
                ->where('status_aktif', 'aktif')
                ->orderBy('nama')
                ->get();

            $shiftsRaw = JadwalShift::with(['statusShift'])
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->when($this->pegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $this->pegawaiIds))
                ->get();

            $shiftMap = [];
            foreach ($shiftsRaw as $s) {
                $dayNum = (int) $s->tanggal->format('j');
                $code = $s->statusShift ? substr($s->statusShift->nama, 0, 4) : ($s->shift ? "S{$s->shift}" : '—');
                $shiftMap[$s->pegawai_id][$dayNum] = $code;
            }

            $rows = [];
            foreach ($pegawais as $p) {
                $row = [
                    $p->nip ?? '—',
                    $p->nama,
                    optional($p->unit)->nama_unit ?? '—',
                ];
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $row[] = $shiftMap[$p->id][$d] ?? '—';
                }
                $rows[] = $row;
            }
            return $rows;
        }

        if ($this->type === 'pengurangan') {
            $absensis = Absensi::with(['pegawai.unit'])
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->where('menit_pengurangan_jam_kerja', '>', 0)
                ->when($this->pegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $this->pegawaiIds))
                ->orderByDesc('menit_pengurangan_jam_kerja')
                ->get();

            $rows = [];
            foreach ($absensis as $a) {
                $rows[] = [
                    $a->tanggal ? $a->tanggal->format('d-m-Y') : '',
                    optional($a->pegawai)->nip,
                    optional($a->pegawai)->nama,
                    optional(optional($a->pegawai)->unit)->nama_unit,
                    $a->jam_masuk ? substr($a->jam_masuk, 0, 5) : '',
                    $a->jam_keluar ? substr($a->jam_keluar, 0, 5) : '',
                    $a->menit_pengurangan_jam_kerja ?? 0,
                ];
            }
            return $rows;
        }

        // Default & Detail Absensi Individu
        $absensis = Absensi::with(['pegawai.unit'])
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->when($this->pegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $this->pegawaiIds))
            ->orderBy('tanggal')
            ->get();

        $rows = [];
        foreach ($absensis as $a) {
            $rows[] = [
                $a->tanggal ? $a->tanggal->format('d-m-Y') : '',
                optional($a->pegawai)->nip,
                optional($a->pegawai)->nama,
                optional(optional($a->pegawai)->unit)->nama_unit,
                $a->jam_masuk ? substr($a->jam_masuk, 0, 5) : '',
                $a->jam_keluar ? substr($a->jam_keluar, 0, 5) : '',
                ucfirst($a->status),
                $a->menit_pengurangan_jam_kerja ?? '',
                $a->keterangan,
            ];
        }
        return $rows;
    }
}
