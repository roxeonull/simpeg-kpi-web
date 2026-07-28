<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Cuti;
use Illuminate\Http\Request;

class CutiApiController extends Controller
{
    public function index(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404);

        $query = $pegawai->cuti()->latest('tanggal_mulai');

        if ($status = $request->get('status')) {
            if ($status === 'menunggu') {
                $query->whereIn('status', ['menunggu_atasan', 'menunggu_hr']);
            } else {
                $query->where('status', $status);
            }
        }

        $cutis = $query->get()->map(fn ($c) => $this->format($c));

        return response()->json(['data' => $cutis]);
    }

    public function saldo(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404);

        $saldo = $pegawai->saldoCutiTahunIni();
        $terpakai = $pegawai->cuti()
            ->where('jenis_cuti', 'tahunan')
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', now()->year)
            ->sum('jumlah_hari');

        return response()->json([
            'tahun' => $saldo->tahun,
            'total' => $saldo->total_saldo,
            'terpakai' => (int) $terpakai,
            'sisa' => $saldo->sisa_saldo,
        ]);
    }

    public function kalenderTim(Request $request)
    {
        $user = $request->user();
        $pegawai = $user->pegawai;
        abort_unless($pegawai, 404);

        $bulan = $request->get('bulan', now()->format('Y-m'));
        try {
            $startOfMonth = \Carbon\Carbon::parse($bulan)->startOfMonth();
            $endOfMonth = \Carbon\Carbon::parse($bulan)->endOfMonth();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Format bulan tidak valid. Gunakan Y-m.'], 400);
        }

        if ($user->role === 'atasan') {
            $teamPegawaiIds = $pegawai->anggotaTim()->pluck('id')->push($pegawai->id);
        } else {
            if ($pegawai->atasan_id) {
                $teamPegawaiIds = \App\Models\Pegawai::where('atasan_id', $pegawai->atasan_id)
                    ->pluck('id')
                    ->push($pegawai->atasan_id);
            } else {
                $teamPegawaiIds = collect([$pegawai->id]);
            }
        }

        $cutis = Cuti::with('pegawai')
            ->whereIn('pegawai_id', $teamPegawaiIds)
            ->where('status', '!=', 'ditolak')
            ->where('tanggal_mulai', '<=', $endOfMonth)
            ->where('tanggal_selesai', '>=', $startOfMonth)
            ->get();

        $data = $cutis->map(fn ($c) => [
            'id' => $c->id,
            'pegawai_id' => $c->pegawai_id,
            'nama_pegawai' => $c->pegawai?->nama ?? 'Pegawai',
            'jenis_cuti' => $c->jenis_cuti,
            'tanggal_mulai' => $c->tanggal_mulai->toDateString(),
            'tanggal_selesai' => $c->tanggal_selesai->toDateString(),
            'status' => $c->status,
            'alasan' => $c->alasan,
        ]);

        return response()->json(['data' => $data]);
    }

    public function show(Request $request, Cuti $cuti)
    {
        $this->authorizeOwnerOrAtasan($request, $cuti);

        return response()->json(['data' => $this->format($cuti, detail: true)]);
    }

    public function store(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404);

        $data = $request->validate([
            'jenis_cuti' => ['required', 'in:tahunan,sakit,melahirkan,lainnya'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'alasan' => ['required', 'string', 'max:1000'],
            'alamat_cuti' => ['nullable', 'string'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ]);

        $mulai = \Carbon\Carbon::parse($data['tanggal_mulai']);
        $selesai = \Carbon\Carbon::parse($data['tanggal_selesai']);
        $jumlahHari = max(1, $mulai->diffInDaysFiltered(fn ($date) => ! $date->isWeekend(), $selesai->copy()->addDay()));

        if ($data['jenis_cuti'] === 'tahunan') {
            $saldo = $pegawai->saldoCutiTahunIni();
            abort_if($jumlahHari > $saldo->sisa_saldo, 422, "Saldo cuti tidak mencukupi. Sisa saldo Anda: {$saldo->sisa_saldo} hari.");
        }

        if ($request->hasFile('lampiran')) {
            $data['lampiran'] = $request->file('lampiran')->store('cuti', 'public');
        }

        $cuti = $pegawai->cuti()->create([
            ...$data,
            'jumlah_hari' => $jumlahHari,
            'status_atasan' => 'menunggu',
            'status_hr' => 'menunggu',
            'status' => 'menunggu_atasan',
        ]);

        AuditLog::catat('mengajukan cuti (mobile)', 'Cuti', $cuti->id, $pegawai->nama);

        // Send FCM Notification to Atasan if exists
        if ($pegawai->atasan && $pegawai->atasan->user) {
            $jenisStr = ($cuti->jenisCuti && $cuti->jenisCuti->nama) ? $cuti->jenisCuti->nama : ucfirst($cuti->jenis_cuti);
            $tglStr = $cuti->tanggal_mulai->format('d M') . ' s.d. ' . $cuti->tanggal_selesai->format('d M Y');
            \App\Services\NotificationService::sendToUser(
                $pegawai->atasan->user,
                'Pengajuan Cuti Tim Baru',
                "{$pegawai->nama} mengajukan cuti {$jenisStr} ({$tglStr}) yang memerlukan persetujuan Anda.",
                ['type' => 'cuti', 'id' => (string) $cuti->id]
            );
        }

        return response()->json(['message' => 'Pengajuan cuti berhasil dikirim.', 'data' => $this->format($cuti)], 201);
    }

    private function authorizeOwnerOrAtasan(Request $request, Cuti $cuti): void
    {
        $user = $request->user();
        if ($user->pegawai && $cuti->pegawai_id === $user->pegawai->id) {
            return;
        }

        if ($user->role === 'atasan' && $user->pegawai) {
            $timIds = $user->pegawai->anggotaTim()->pluck('id')->toArray();
            if (in_array($cuti->pegawai_id, $timIds, true)) {
                return;
            }
        }

        abort(403, 'Anda tidak berhak melihat pengajuan cuti ini.');
    }

    private function format(Cuti $c, bool $detail = false): array
    {
        $c->loadMissing(['pegawai.unit', 'pegawai.jabatan', 'jenisCuti']);
        $pegawai = $c->pegawai;

        $base = [
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
            'created_at' => $c->created_at->toIso8601String(),
        ];

        if ($detail) {
            $base['timeline'] = [
                [
                    'tahap' => 'Diajukan',
                    'status' => 'selesai',
                    'waktu' => $c->created_at->toIso8601String(),
                    'catatan' => null,
                ],
                [
                    'tahap' => 'Persetujuan Atasan',
                    'status' => $c->status_atasan,
                    'waktu' => $c->atasan_diproses_pada?->toIso8601String(),
                    'catatan' => $c->catatan_atasan,
                ],
                [
                    'tahap' => 'Persetujuan HR',
                    'status' => $c->status_hr,
                    'waktu' => $c->hr_diproses_pada?->toIso8601String(),
                    'catatan' => $c->catatan_hr,
                ],
            ];
        }

        return $base;
    }
}
