<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DinasLuar;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DinasLuarApiController extends Controller
{
    public function index(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        if (!$pegawai) {
            return response()->json(['data' => []]);
        }

        $items = DinasLuar::with(['jenisKetidakhadiran', 'approver'])
            ->where('pegawai_id', $pegawai->id)
            ->orderByDesc('id')
            ->get()
            ->map(fn ($item) => $this->format($item));

        return response()->json(['data' => $items]);
    }

    public function options()
    {
        $items = \App\Models\JenisKetidakhadiran::orderBy('id')->get();
        return response()->json(['data' => $items]);
    }

    public function store(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        if (!$pegawai) {
            return response()->json(['message' => 'Data pegawai tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'jenis_ketidakhadiran_id' => 'required|exists:jenis_ketidakhadirans,id',
            'tanggal_mulai'           => 'required|date',
            'tanggal_selesai'         => 'required|date|after_or_equal:tanggal_mulai',
            'lokasi_tugas'            => 'required|string|max:255',
            'alasan'                  => 'required|string',
            'file_spt'                => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file_spt')) {
            $filePath = $request->file('file_spt')->store('spt', 'public');
        }

        $dinasLuar = DinasLuar::create([
            'pegawai_id'              => $pegawai->id,
            'jenis_ketidakhadiran_id' => $validated['jenis_ketidakhadiran_id'],
            'tanggal_mulai'           => $validated['tanggal_mulai'],
            'tanggal_selesai'         => $validated['tanggal_selesai'],
            'lokasi_tugas'            => $validated['lokasi_tugas'],
            'alasan'                  => $validated['alasan'],
            'file_spt'                => $filePath,
            'status'                  => 'pending',
        ]);

        // Send FCM Notification to Atasan
        if ($pegawai->atasan && $pegawai->atasan->user) {
            $jenisStr = $dinasLuar->jenisKetidakhadiran?->nama ?? 'Dinas Luar';
            $tglStr = \Carbon\Carbon::parse($dinasLuar->tanggal_mulai)->format('d M') . ' s.d. ' . \Carbon\Carbon::parse($dinasLuar->tanggal_selesai)->format('d M Y');
            \App\Services\NotificationService::sendToUser(
                $pegawai->atasan->user,
                '📋 Pengajuan Dinas Luar / WFA Baru',
                "{$pegawai->nama} mengajukan {$jenisStr} ({$tglStr}) di {$dinasLuar->lokasi_tugas} yang memerlukan persetujuan Anda.",
                ['type' => 'dinas_luar', 'id' => (string) $dinasLuar->id]
            );
        }

        return response()->json([
            'message' => 'Pengajuan Dinas Luar / WFA berhasil dikirim',
            'data'    => $this->format($dinasLuar->fresh(['jenisKetidakhadiran'])),
        ], 201);
    }

    public function teamRequests(Request $request)
    {
        $user = $request->user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return response()->json(['data' => []]);
        }

        // Subordinate employee IDs where atasan_id == $pegawai->id
        $bawahanIds = Pegawai::where('atasan_id', $pegawai->id)->pluck('id');

        $items = DinasLuar::with(['pegawai', 'jenisKetidakhadiran', 'approver'])
            ->whereIn('pegawai_id', $bawahanIds)
            ->orderByDesc('id')
            ->get()
            ->map(fn ($item) => $this->format($item));

        return response()->json(['data' => $items]);
    }

    public function setujui(Request $request, $id)
    {
        $user = $request->user();
        $dinasLuar = DinasLuar::findOrFail($id);

        $dinasLuar->update([
            'status'         => 'disetujui',
            'approved_by'     => $user->id,
            'catatan_atasan' => $request->input('catatan_atasan'),
        ]);

        $dinasLuar->syncToAbsensi();

        \App\Models\AuditLog::catat('setujui', 'DinasLuar', $dinasLuar->id, "Menyetujui Dinas Luar ID #{$dinasLuar->id} via Mobile API");

        // Send FCM Notification to Pegawai
        if ($dinasLuar->pegawai && $dinasLuar->pegawai->user) {
            $tglStr = \Carbon\Carbon::parse($dinasLuar->tanggal_mulai)->format('d M') . ' s.d. ' . \Carbon\Carbon::parse($dinasLuar->tanggal_selesai)->format('d M Y');
            \App\Services\NotificationService::sendToUser(
                $dinasLuar->pegawai->user,
                '✅ Pengajuan Dinas Luar Disetujui',
                "Pengajuan Dinas Luar/WFA Anda ({$tglStr}) di {$dinasLuar->lokasi_tugas} telah DISETUJUI.",
                ['type' => 'dinas_luar', 'id' => (string) $dinasLuar->id]
            );
        }

        return response()->json([
            'message' => 'Pengajuan disetujui',
            'data'    => $this->format($dinasLuar->fresh(['jenisKetidakhadiran'])),
        ]);
    }

    public function tolak(Request $request, $id)
    {
        $user = $request->user();
        $dinasLuar = DinasLuar::findOrFail($id);

        $dinasLuar->update([
            'status'         => 'ditolak',
            'approved_by'     => $user->id,
            'catatan_atasan' => $request->input('catatan_atasan'),
        ]);

        \App\Models\AuditLog::catat('tolak', 'DinasLuar', $dinasLuar->id, "Menolak Dinas Luar ID #{$dinasLuar->id} via Mobile API");

        // Send FCM Notification to Pegawai
        if ($dinasLuar->pegawai && $dinasLuar->pegawai->user) {
            $tglStr = \Carbon\Carbon::parse($dinasLuar->tanggal_mulai)->format('d M') . ' s.d. ' . \Carbon\Carbon::parse($dinasLuar->tanggal_selesai)->format('d M Y');
            $catatan = $dinasLuar->catatan_atasan ? " Catatan: {$dinasLuar->catatan_atasan}" : '';
            \App\Services\NotificationService::sendToUser(
                $dinasLuar->pegawai->user,
                '✕ Pengajuan Dinas Luar Ditolak',
                "Pengajuan Dinas Luar/WFA Anda ({$tglStr}) telah DITOLAK.{$catatan}",
                ['type' => 'dinas_luar', 'id' => (string) $dinasLuar->id]
            );
        }

        return response()->json([
            'message' => 'Pengajuan ditolak',
            'data'    => $this->format($dinasLuar->fresh(['jenisKetidakhadiran'])),
        ]);
    }

    private function format(DinasLuar $item): array
    {
        return [
            'id'                        => $item->id,
            'pegawai_id'                => $item->pegawai_id,
            'pegawai_nama'              => $item->pegawai?->nama,
            'jenis_ketidakhadiran_id'   => $item->jenis_ketidakhadiran_id,
            'jenis_ketidakhadiran_nama' => $item->jenisKetidakhadiran?->nama ?? 'Dinas Luar',
            'tanggal_mulai'             => $item->tanggal_mulai?->format('Y-m-d') ?? (string) $item->tanggal_mulai,
            'tanggal_selesai'           => $item->tanggal_selesai?->format('Y-m-d') ?? (string) $item->tanggal_selesai,
            'lokasi_tugas'              => $item->lokasi_tugas,
            'alasan'                    => $item->alasan,
            'file_spt'                  => $item->file_spt,
            'file_spt_url'              => $item->file_spt_url,
            'status'                    => $item->status,
            'approved_by_nama'          => $item->approver?->name,
            'catatan_atasan'            => $item->catatan_atasan,
            'created_at'                => $item->created_at?->toIso8601String(),
        ];
    }
}
