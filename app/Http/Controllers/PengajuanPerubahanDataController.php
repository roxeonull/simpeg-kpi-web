<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\PengajuanPerubahanData;
use Illuminate\Http\Request;

class PengajuanPerubahanDataController extends Controller
{
    public function index(Request $request)
    {
        $pengajuans = PengajuanPerubahanData::with(['pegawai.unit', 'diprosesOleh'])
            ->latest()
            ->get();

        $counts = [
            'semua' => $pengajuans->count(),
            'menunggu' => $pengajuans->where('status', 'menunggu')->count(),
            'disetujui' => $pengajuans->where('status', 'disetujui')->count(),
            'ditolak' => $pengajuans->where('status', 'ditolak')->count(),
        ];

        return view('pengajuan-perubahan.index', compact('pengajuans', 'counts'));
    }

    public function setujui(Request $request, PengajuanPerubahanData $pengajuan)
    {
        $pengajuan->pegawai->update([$pengajuan->field => $pengajuan->nilai_baru]);
        $pengajuan->update([
            'status' => 'disetujui',
            'diproses_oleh' => $request->user()->id,
        ]);

        if ($pengajuan->pegawai?->user) {
            \App\Services\NotificationService::sendToUser(
                $pengajuan->pegawai->user,
                'Perubahan Data Disetujui',
                "Pengajuan perubahan data {$pengajuan->field} Anda telah disetujui.",
                ['type' => 'perubahan_data', 'id' => (string) $pengajuan->id]
            );
        }

        AuditLog::catat('menyetujui perubahan data pegawai', 'PengajuanPerubahanData', $pengajuan->id, $pengajuan->pegawai->nama);

        return back()->with('status', 'Perubahan data disetujui dan diterapkan.');
    }

    public function tolak(Request $request, PengajuanPerubahanData $pengajuan)
    {
        $data = $request->validate(['catatan_admin' => ['nullable', 'string']]);

        $pengajuan->update([
            'status' => 'ditolak',
            'catatan_admin' => $data['catatan_admin'] ?? null,
            'diproses_oleh' => $request->user()->id,
        ]);

        if ($pengajuan->pegawai?->user) {
            \App\Services\NotificationService::sendToUser(
                $pengajuan->pegawai->user,
                'Perubahan Data Ditolak',
                "Pengajuan perubahan data {$pengajuan->field} Anda ditolak.",
                ['type' => 'perubahan_data', 'id' => (string) $pengajuan->id]
            );
        }

        AuditLog::catat('menolak perubahan data pegawai', 'PengajuanPerubahanData', $pengajuan->id, $pengajuan->pegawai->nama);

        return back()->with('status', 'Pengajuan perubahan data ditolak.');
    }
}
