<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Pegawai;
use App\Models\RiwayatPelatihan;
use App\Models\SaldoCuti;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PegawaiExport implements FromArray, WithHeadings
{
    public ?string $type;
    public ?array $pegawaiIds;
    public ?string $tahun;

    public function __construct(
        ?string $type = null,
        ?array $pegawaiIds = null,
        ?string $tahun = null
    ) {
        $this->type = $type;
        $this->pegawaiIds = $pegawaiIds;
        $this->tahun = $tahun ?: now()->year;
    }

    public function headings(): array
    {
        if ($this->type === 'demografi') {
            return [
                'NIP', 'Nama Pegawai', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir',
                'Usia', 'Pendidikan Terakhir', 'Jurusan Pendidikan', 'Universitas',
                'Status Marital', 'Pangkat / Golongan', 'Unit Kerja', 'Status Kepegawaian'
            ];
        }

        if ($this->type === 'slip') {
            return [
                'NIP', 'Nama Pegawai', 'Unit Kerja', 'Jabatan', 'Status Kepegawaian', 'TMT',
                'Total Hadir', 'Terlambat', 'Izin/Sakit', 'Alpa', 'Potongan Menit',
                'Saldo Cuti Total', 'Sisa Saldo Cuti', 'Total Pelatihan (JP)'
            ];
        }

        if ($this->type === 'diklat') {
            return [
                'NIP', 'Nama Pegawai', 'Unit Kerja', 'Nama Pelatihan / Diklat',
                'Penyelenggara', 'Tanggal', 'Durasi (JP)', 'Status Verifikasi'
            ];
        }

        if ($this->type === 'target_jp') {
            return [
                'NIP', 'Nama Pegawai', 'Unit Kerja', 'Target JP', 'Capaian Realisasi JP',
                'Selisih / Kekurangan JP', 'Status Target'
            ];
        }

        // Default Data Pegawai
        return ['NIP', 'Nama', 'Jabatan', 'Unit Kerja', 'Status Kepegawaian', 'TMT', 'Status Aktif'];
    }

    public function array(): array
    {
        $query = Pegawai::with(['jabatan', 'unit'])
            ->when($this->pegawaiIds, fn ($q) => $q->whereIn('id', $this->pegawaiIds))
            ->orderBy('nama');

        if ($this->type === 'demografi') {
            $pegawais = $query->get();
            $rows = [];
            foreach ($pegawais as $p) {
                $usia = $p->tanggal_lahir ? Carbon::parse($p->tanggal_lahir)->age : '';
                $tglLahir = $p->tanggal_lahir ? Carbon::parse($p->tanggal_lahir)->format('d-m-Y') : '';

                $rows[] = [
                    $p->nip,
                    $p->nama,
                    strtoupper($p->jenis_kelamin ?? ''),
                    $p->tempat_lahir,
                    $tglLahir,
                    $usia ? "{$usia} thn" : '',
                    $p->pendidikan_terakhir,
                    $p->jurusan_pendidikan,
                    $p->universitas,
                    $p->status_marital,
                    $p->pangkat_golongan,
                    optional($p->unit)->nama_unit,
                    $p->status_kepegawaian,
                ];
            }
            return $rows;
        }

        if ($this->type === 'slip') {
            $pegawais = $query->get();
            $rows = [];
            foreach ($pegawais as $p) {
                $saldoCuti = SaldoCuti::where('pegawai_id', $p->id)->where('tahun', $this->tahun)->first();
                $absensiSummary = [
                    'hadir' => Absensi::where('pegawai_id', $p->id)->whereYear('tanggal', $this->tahun)->where('status', 'hadir')->count(),
                    'terlambat' => Absensi::where('pegawai_id', $p->id)->whereYear('tanggal', $this->tahun)->where('status', 'terlambat')->count(),
                    'izin' => Absensi::where('pegawai_id', $p->id)->whereYear('tanggal', $this->tahun)->whereIn('status', ['izin', 'sakit'])->count(),
                    'alpa' => Absensi::where('pegawai_id', $p->id)->whereYear('tanggal', $this->tahun)->where('status', 'alpa')->count(),
                    'potongan_menit' => Absensi::where('pegawai_id', $p->id)->whereYear('tanggal', $this->tahun)->sum('menit_pengurangan_jam_kerja'),
                ];
                $totalJp = RiwayatPelatihan::where('pegawai_id', $p->id)->whereYear('tanggal', $this->tahun)->sum('durasi_jp');

                $rows[] = [
                    $p->nip,
                    $p->nama,
                    optional($p->unit)->nama_unit,
                    optional($p->jabatan)->nama_jabatan,
                    $p->status_kepegawaian,
                    optional($p->tmt)->format('d-m-Y'),
                    $absensiSummary['hadir'],
                    $absensiSummary['terlambat'],
                    $absensiSummary['izin'],
                    $absensiSummary['alpa'],
                    $absensiSummary['potongan_menit'],
                    $saldoCuti ? $saldoCuti->total_saldo : 12,
                    $saldoCuti ? $saldoCuti->sisa_saldo : 12,
                    $totalJp,
                ];
            }
            return $rows;
        }

        if ($this->type === 'diklat') {
            $pelatihans = RiwayatPelatihan::with(['pegawai.unit'])
                ->when($this->pegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $this->pegawaiIds))
                ->whereYear('tanggal', $this->tahun)
                ->orderBy('tanggal')
                ->get();

            $rows = [];
            foreach ($pelatihans as $p) {
                $rows[] = [
                    optional($p->pegawai)->nip,
                    optional($p->pegawai)->nama,
                    optional(optional($p->pegawai)->unit)->nama_unit,
                    $p->nama_pelatihan,
                    $p->penyelenggara,
                    $p->tanggal ? $p->tanggal->format('d-m-Y') : '',
                    $p->durasi_jp ?? 0,
                    ucfirst($p->status_verifikasi ?? 'menunggu'),
                ];
            }
            return $rows;
        }

        if ($this->type === 'target_jp') {
            $pegawais = $query->get();
            $targetDefault = 20;
            $rows = [];

            foreach ($pegawais as $p) {
                $capaianJp = (int) RiwayatPelatihan::where('pegawai_id', $p->id)
                    ->whereYear('tanggal', $this->tahun)
                    ->sum('durasi_jp');

                $kekurangan = max(0, $targetDefault - $capaianJp);
                $status = $capaianJp >= $targetDefault ? 'Tercapai' : 'Belum Tercapai';

                $rows[] = [
                    $p->nip,
                    $p->nama,
                    optional($p->unit)->nama_unit,
                    $targetDefault,
                    $capaianJp,
                    $kekurangan,
                    $status,
                ];
            }
            return $rows;
        }

        // Default Data Pegawai
        $pegawais = $query->get();
        $rows = [];
        foreach ($pegawais as $p) {
            $rows[] = [
                $p->nip,
                $p->nama,
                optional($p->jabatan)->nama_jabatan,
                optional($p->unit)->nama_unit,
                $p->status_kepegawaian,
                optional($p->tmt)->format('d-m-Y'),
                ucfirst($p->status_aktif),
            ];
        }
        return $rows;
    }
}
