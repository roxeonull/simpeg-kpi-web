<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Cuti;
use Illuminate\Http\Request;

class AtasanApiController extends Controller
{
    public function cutiTim(Request $request)
    {
        $user = $request->user();
        abort_unless($user->role === 'atasan' && $user->pegawai, 403, 'Akses khusus untuk atasan.');

        $timIds = $user->pegawai->anggotaTim()->pluck('id')->toArray();
        $query = Cuti::with(['pegawai.unit', 'pegawai.jabatan', 'jenisCuti'])
            ->whereIn('pegawai_id', $timIds);

        $status = $request->get('status', 'menunggu');

        if ($status === 'menunggu') {
            $query->where('status_atasan', 'menunggu');
        } elseif ($status === 'disetujui') {
            $query->where('status_atasan', 'disetujui');
        } elseif ($status === 'ditolak') {
            $query->where('status_atasan', 'ditolak');
        }
        // If status === 'semua', do not apply status filter

        $cutis = $query->latest('created_at')->get()->map(fn ($c) => $this->format($c));

        return response()->json([
            'data' => $cutis,
            'counts' => [
                'menunggu' => Cuti::whereIn('pegawai_id', $timIds)->where('status_atasan', 'menunggu')->count(),
                'disetujui' => Cuti::whereIn('pegawai_id', $timIds)->where('status_atasan', 'disetujui')->count(),
                'ditolak' => Cuti::whereIn('pegawai_id', $timIds)->where('status_atasan', 'ditolak')->count(),
                'semua' => Cuti::whereIn('pegawai_id', $timIds)->count(),
            ],
        ]);
    }

    public function setujuiCuti(Request $request, Cuti $cuti)
    {
        $this->authorizeAtasan($request, $cuti);

        abort_unless($cuti->canUserApproveActiveStep($request->user()), 422, 'Pengajuan cuti ini tidak sedang menunggu persetujuan Anda.');

        $data = $request->validate([
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $cuti->setujuiAtasan($request->user(), $data['catatan'] ?? null);

        $pegawaiNama = $cuti->pegawai ? $cuti->pegawai->nama : null;
        AuditLog::catat('menyetujui cuti (atasan mobile)', 'Cuti', $cuti->id, $pegawaiNama);

        return response()->json([
            'message' => 'Pengajuan cuti berhasil disetujui.',
            'data' => $this->format($cuti->fresh(['pegawai.unit', 'pegawai.jabatan', 'jenisCuti'])),
        ]);
    }

    public function tolakCuti(Request $request, Cuti $cuti)
    {
        $this->authorizeAtasan($request, $cuti);

        abort_unless($cuti->canUserApproveActiveStep($request->user()), 422, 'Pengajuan cuti ini tidak sedang menunggu persetujuan Anda.');

        $data = $request->validate([
            'catatan' => ['required', 'string', 'max:500'],
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $cuti->tolakAtasan($request->user(), $data['catatan']);

        $pegawaiNama = $cuti->pegawai ? $cuti->pegawai->nama : null;
        AuditLog::catat('menolak cuti (atasan mobile)', 'Cuti', $cuti->id, $pegawaiNama);

        return response()->json([
            'message' => 'Pengajuan cuti berhasil ditolak.',
            'data' => $this->format($cuti->fresh(['pegawai.unit', 'pegawai.jabatan', 'jenisCuti'])),
        ]);
    }

    private function authorizeAtasan(Request $request, Cuti $cuti): void
    {
        $user = $request->user();
        abort_unless($user->role === 'atasan' && $user->pegawai, 403, 'Akses khusus untuk atasan.');

        $timIds = $user->pegawai->anggotaTim()->pluck('id')->toArray();
        abort_unless(in_array($cuti->pegawai_id, $timIds, true), 403, 'Pengajuan ini bukan dari anggota tim Anda.');
    }

    private function format(Cuti $c): array
    {
        $pegawai = $c->pegawai;

        return [
            'id' => $c->id,
            'pegawai_id' => $c->pegawai_id,
            'nama_pegawai' => $pegawai ? $pegawai->nama : null,
            'nip_pegawai' => $pegawai ? $pegawai->nip : null,
            'foto_pegawai' => ($pegawai && $pegawai->foto) ? asset('storage/' . $pegawai->foto) : null,
            'unit_pegawai' => ($pegawai && $pegawai->unit) ? $pegawai->unit->nama_unit : null,
            'jabatan_pegawai' => ($pegawai && $pegawai->jabatan) ? $pegawai->jabatan->nama_jabatan : null,
            'jenis_cuti' => $c->jenis_cuti,
            'tanggal_mulai' => $c->tanggal_mulai->toDateString(),
            'tanggal_selesai' => $c->tanggal_selesai->toDateString(),
            'jumlah_hari' => $c->jumlah_hari,
            'alasan' => $c->alasan,
            'alamat_cuti' => $c->alamat_cuti,
            'lampiran' => $c->lampiran ? asset('storage/' . $c->lampiran) : null,
            'status' => $c->status,
            'status_atasan' => $c->status_atasan,
            'catatan_atasan' => $c->catatan_atasan,
            'status_hr' => $c->status_hr,
            'catatan_hr' => $c->catatan_hr,
            'status_label' => $c->statusLabel(),
            'can_approve' => request()->user() ? $c->canUserApproveActiveStep(request()->user()) : false,
            'created_at' => $c->created_at->toIso8601String(),
        ];
    }
}
