<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DinasLuar;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class DinasLuarWebController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = DinasLuar::with(['pegawai.unit', 'pegawai.jabatan', 'jenisKetidakhadiran', 'approver'])
            ->orderByDesc('id');

        // Scope by role: if Atasan, limit to direct team subordinates unless admin
        if ($user->role === 'atasan' && $user->pegawai) {
            $bawahanIds = Pegawai::where('atasan_id', $user->pegawai->id)->pluck('id');
            $query->whereIn('pegawai_id', $bawahanIds);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pegawai', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $dinasLuars = $query->paginate(15)->withQueryString();

        // Stat counters
        $baseQuery = DinasLuar::query();
        if ($user->role === 'atasan' && $user->pegawai) {
            $bawahanIds = Pegawai::where('atasan_id', $user->pegawai->id)->pluck('id');
            $baseQuery->whereIn('pegawai_id', $bawahanIds);
        }

        $stats = [
            'total'    => (clone $baseQuery)->count(),
            'pending'  => (clone $baseQuery)->where('status', 'pending')->count(),
            'disetujui'=> (clone $baseQuery)->where('status', 'disetujui')->count(),
            'ditolak'  => (clone $baseQuery)->where('status', 'ditolak')->count(),
        ];

        return view('dinas-luar.index', compact('dinasLuars', 'stats'));
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

        $namaPegawai = $dinasLuar->pegawai->nama ?? '-';
        AuditLog::catat('setujui', 'DinasLuar', $dinasLuar->id, "Menyetujui Dinas Luar ID #{$dinasLuar->id} pegawai {$namaPegawai}");
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

        return redirect()->back()->with('status', 'Pengajuan Dinas Luar / WFA berhasil disetujui!');
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

        $namaPegawai = $dinasLuar->pegawai->nama ?? '-';
        AuditLog::catat('tolak', 'DinasLuar', $dinasLuar->id, "Menolak Dinas Luar ID #{$dinasLuar->id} pegawai {$namaPegawai}");

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

        return redirect()->back()->with('status', 'Pengajuan Dinas Luar / WFA telah ditolak.');
    }
}
