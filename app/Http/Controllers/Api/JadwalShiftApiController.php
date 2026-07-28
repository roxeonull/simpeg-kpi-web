<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalShift;
use Illuminate\Http\Request;

class JadwalShiftApiController extends Controller
{
    /**
     * Return shift info for logged in employee FOR TODAY ONLY.
     */
    public function hariIni(Request $request)
    {
        $pegawai = $request->user()->pegawai;

        if (!$pegawai) {
            return response()->json([
                'has_jadwal_shift' => false,
                'has_shift' => false,
                'data' => null,
            ]);
        }

        $hasJadwalShift = JadwalShift::where('pegawai_id', $pegawai->id)->exists();

        $entryToday = JadwalShift::with('statusShift')
            ->where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        if (!$entryToday) {
            return response()->json([
                'has_jadwal_shift' => $hasJadwalShift,
                'has_shift' => false,
                'data' => null,
            ]);
        }

        return response()->json([
            'has_jadwal_shift' => $hasJadwalShift,
            'has_shift' => true,
            'data' => $this->formatShiftItem($entryToday),
        ]);
    }

    /**
     * Return monthly shift schedule for logged in employee.
     */
    public function index(Request $request)
    {
        $pegawai = $request->user()->pegawai;

        abort_unless($pegawai, 404, 'Profil pegawai belum terhubung ke akun ini.');

        $bulan = $request->get('bulan', now()->format('Y-m'));
        $year = substr($bulan, 0, 4);
        $month = substr($bulan, 5, 2);

        $hasJadwalShift = JadwalShift::where('pegawai_id', $pegawai->id)->exists();

        $entries = JadwalShift::with('statusShift')
            ->where('pegawai_id', $pegawai->id)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->orderBy('tanggal')
            ->get();

        return response()->json([
            'has_jadwal_shift' => $hasJadwalShift,
            'bulan' => $bulan,
            'data' => $entries->map(fn ($item) => $this->formatShiftItem($item)),
        ]);
    }

    public static function formatShiftItem(JadwalShift $item): array
    {
        $statusShift = $item->statusShift;
        $isLibur = $statusShift !== null || (!empty($item->keterangan) && str_contains(strtolower($item->keterangan), 'libur'));

        return [
            'id' => $item->id,
            'tanggal' => $item->tanggal->toDateString(),
            'shift' => $item->shift,
            'jam_mulai' => JadwalShift::getJamMulai($item->shift),
            'jam_selesai' => JadwalShift::getJamSelesai($item->shift),
            'stasiun_tv' => $item->stasiun_tv,
            'is_libur' => $isLibur,
            'status_shift' => $statusShift ? [
                'id' => $statusShift->id,
                'kode' => $statusShift->kode,
                'nama' => $statusShift->nama,
                'warna' => $statusShift->warna,
            ] : null,
            'keterangan' => $item->keterangan,
        ];
    }
}
