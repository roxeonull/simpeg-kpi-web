<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function index(Request $request)
    {
        $pegawai = $request->user()->pegawai;

        abort_unless($pegawai, 404, 'Profil pegawai belum terhubung ke akun ini.');

        $absensiHariIni = $pegawai->absensi()->whereDate('tanggal', now()->toDateString())->first();
        $saldoCuti = $pegawai->saldoCutiTahunIni();

        $cutiTerbaru = $pegawai->cuti()->latest('tanggal_mulai')->limit(3)->get()->map(fn ($c) => [
            'id' => $c->id,
            'jenis_cuti' => $c->jenis_cuti,
            'tanggal_mulai' => $c->tanggal_mulai->toDateString(),
            'tanggal_selesai' => $c->tanggal_selesai->toDateString(),
            'jumlah_hari' => $c->jumlah_hari,
            'status' => $c->status,
            'status_label' => $c->statusLabel(),
        ]);

        $absensiBulanIni = $pegawai->absensi()->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);

        $hasJadwalShift = \App\Models\JadwalShift::where('pegawai_id', $pegawai->id)->exists();
        $entryToday = \App\Models\JadwalShift::with('statusShift')
            ->where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        $todayStr = now()->toDateString();
        $dinasLuarAktif = \App\Models\DinasLuar::with('jenisKetidakhadiran')
            ->where('pegawai_id', $pegawai->id)
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $todayStr)
            ->whereDate('tanggal_selesai', '>=', $todayStr)
            ->first();

        $dinasLuarTerbaru = \App\Models\DinasLuar::with('jenisKetidakhadiran')
            ->where('pegawai_id', $pegawai->id)
            ->orderByDesc('id')
            ->limit(3)
            ->get()
            ->map(fn ($dl) => [
                'id' => $dl->id,
                'jenis_ketidakhadiran_nama' => ($dl->jenisKetidakhadiran && $dl->jenisKetidakhadiran->nama) ? $dl->jenisKetidakhadiran->nama : 'Dinas Luar',
                'tanggal_mulai' => is_object($dl->tanggal_mulai) ? $dl->tanggal_mulai->format('Y-m-d') : (string) $dl->tanggal_mulai,
                'tanggal_selesai' => is_object($dl->tanggal_selesai) ? $dl->tanggal_selesai->format('Y-m-d') : (string) $dl->tanggal_selesai,
                'lokasi_tugas' => $dl->lokasi_tugas,
                'alasan' => $dl->alasan,
                'status' => $dl->status,
            ]);

        return response()->json([
            'absensi_hari_ini' => $absensiHariIni ? [
                'jam_masuk' => $absensiHariIni->jam_masuk,
                'jam_keluar' => $absensiHariIni->jam_keluar,
                'status' => $absensiHariIni->status,
            ] : null,
            'has_jadwal_shift' => $hasJadwalShift,
            'jadwal_shift_hari_ini' => $entryToday ? \App\Http\Controllers\Api\JadwalShiftApiController::formatShiftItem($entryToday) : null,
            'saldo_cuti' => [
                'total' => $saldoCuti->total_saldo,
                'sisa' => $saldoCuti->sisa_saldo,
                'tahun' => $saldoCuti->tahun,
            ],
            'total_jp_tahun_ini' => $pegawai->totalJpTahunIni(),
            'rekap_absensi_bulan_ini' => [
                'hadir' => (clone $absensiBulanIni)->whereIn('status', ['hadir', 'telat'])->count(),
                'izin_sakit' => (clone $absensiBulanIni)->whereIn('status', ['izin', 'sakit'])->count(),
                'alpa' => (clone $absensiBulanIni)->where('status', 'alpa')->count(),
            ],
            'cuti_terbaru' => $cutiTerbaru,
            'dinas_luar_aktif' => $dinasLuarAktif ? [
                'id' => $dinasLuarAktif->id,
                'jenis_ketidakhadiran_nama' => ($dinasLuarAktif->jenisKetidakhadiran && $dinasLuarAktif->jenisKetidakhadiran->nama) ? $dinasLuarAktif->jenisKetidakhadiran->nama : 'Dinas Luar',
                'tanggal_mulai' => is_object($dinasLuarAktif->tanggal_mulai) ? $dinasLuarAktif->tanggal_mulai->format('Y-m-d') : (string) $dinasLuarAktif->tanggal_mulai,
                'tanggal_selesai' => is_object($dinasLuarAktif->tanggal_selesai) ? $dinasLuarAktif->tanggal_selesai->format('Y-m-d') : (string) $dinasLuarAktif->tanggal_selesai,
                'lokasi_tugas' => $dinasLuarAktif->lokasi_tugas,
                'alasan' => $dinasLuarAktif->alasan,
            ] : null,
            'dinas_luar_terbaru' => $dinasLuarTerbaru,
        ]);
    }
}
