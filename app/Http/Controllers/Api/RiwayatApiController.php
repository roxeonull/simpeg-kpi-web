<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AuditLog;
use App\Models\BentukPelatihan;
use App\Models\TipeKursus;
use App\Models\JenisKursus;
use App\Models\Instansi;
use App\Models\RiwayatPelatihan;

class RiwayatApiController extends Controller
{
    public function pendidikan(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404);

        $data = $pegawai->riwayatPendidikan()->orderByDesc('tahun_lulus')->get()->map(fn ($r) => [
            'id' => $r->id,
            'jenjang' => $r->jenjang,
            'institusi' => $r->institusi,
            'jurusan' => $r->jurusan,
            'tahun_lulus' => (int) $r->tahun_lulus,
            'file_ijazah' => $r->file_ijazah ? asset('storage/' . $r->file_ijazah) : null,
        ]);

        return response()->json(['data' => $data]);
    }

    public function pelatihan(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404);

        $data = $pegawai->riwayatPelatihan()
            ->with(['bentukPelatihan', 'tipeKursus', 'jenisKursus', 'instansi'])
            ->orderByDesc('tanggal')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'nama_pelatihan' => $r->nama_pelatihan,
                'penyelenggara' => $r->penyelenggara,
                'tanggal' => $r->tanggal->toDateString(),
                'tanggal_akhir' => $r->tanggal_akhir ? $r->tanggal_akhir->toDateString() : null,
                'durasi_jp' => $r->durasi_jp,
                'kategori' => $r->kategori,
                'status_verifikasi' => $r->status_verifikasi,
                'catatan' => $r->catatan,
                'no_sertifikat' => $r->no_sertifikat,
                'tanggal_sertifikat' => $r->tanggal_sertifikat ? $r->tanggal_sertifikat->toDateString() : null,
                'bidang_sdm_spbe' => $r->bidang_sdm_spbe,
                'sertifikat' => $r->sertifikat ? asset('storage/' . $r->sertifikat) : null,
                'bentuk_pelatihan_id' => $r->bentuk_pelatihan_id,
                'bentuk_pelatihan' => $r->bentukPelatihan?->nama_bentuk,
                'tipe_kursus_id' => $r->tipe_kursus_id,
                'tipe_kursus' => $r->tipeKursus?->nama_tipe,
                'jenis_kursus_id' => $r->jenis_kursus_id,
                'jenis_kursus' => $r->jenisKursus?->nama_jenis,
                'instansi_id' => $r->instansi_id,
                'instansi' => $r->instansi?->nama_instansi,
            ]);

        $totalJp = $pegawai->totalJpTahunIni();

        return response()->json(['data' => $data, 'total_jp_tahun_ini' => $totalJp]);
    }

    public function pelatihanOptions()
    {
        return response()->json([
            'bentuk_pelatihans' => BentukPelatihan::orderBy('nama_bentuk')->get(['id', 'nama_bentuk']),
            'tipe_kursuses' => TipeKursus::orderBy('nama_tipe')->get(['id', 'nama_tipe', 'bentuk_pelatihan_id']),
            'jenis_kursuses' => JenisKursus::orderBy('nama_jenis')->get(['id', 'nama_jenis']),
            'instansis' => Instansi::orderBy('nama_instansi')->get(['id', 'nama_instansi']),
        ]);
    }

    public function storePelatihan(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404);

        $data = $request->validate([
            'nama_pelatihan' => ['required', 'string', 'max:255'],
            'penyelenggara' => ['required', 'string', 'max:255'],
            'tanggal' => ['required', 'date'],
            'tanggal_akhir' => ['required', 'date', 'after_or_equal:tanggal'],
            'durasi_jp' => ['required', 'integer', 'min:1'],
            'bentuk_pelatihan_id' => ['required', 'exists:bentuk_pelatihans,id'],
            'tipe_kursus_id' => ['required', 'exists:tipe_kursuses,id'],
            'jenis_kursus_id' => ['required', 'exists:jenis_kursuses,id'],
            'instansi_id' => ['required', 'exists:instansis,id'],
            'no_sertifikat' => ['required', 'string', 'max:100'],
            'tanggal_sertifikat' => ['required', 'date'],
            'bidang_sdm_spbe' => ['nullable', 'string', 'max:255'],
            'sertifikat' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ]);

        $data['kategori'] = 'lainnya'; // Default non-nullable enum column
        $data['status_verifikasi'] = 'menunggu';

        if ($request->hasFile('sertifikat')) {
            $data['sertifikat'] = $request->file('sertifikat')->store('pelatihan', 'public');
        }

        $pelatihan = $pegawai->riwayatPelatihan()->create($data);

        AuditLog::catat('menambah riwayat pelatihan (mobile)', 'RiwayatPelatihan', $pegawai->id, $pegawai->nama);

        // 1. Notifikasi konfirmasi ke Pegawai (pemohon)
        if ($pegawai->user) {
            \App\Services\NotificationService::sendToUser(
                $pegawai->user,
                'Usulan Diklat Berhasil Dikirim',
                "Usulan diklat '{$pelatihan->nama_pelatihan}' Anda telah terkirim dan sedang menunggu verifikasi.",
                ['type' => 'pelatihan', 'id' => (string) $pelatihan->id]
            );
        }

        // 2. Notifikasi ke Atasan (HANYA jika atasan ada dan BERBEDA akun dengan pemohon)
        if (
            $pegawai->atasan &&
            $pegawai->atasan->user &&
            $pegawai->atasan->user_id !== $pegawai->user_id &&
            $pegawai->atasan->id !== $pegawai->id
        ) {
            \App\Services\NotificationService::sendToUser(
                $pegawai->atasan->user,
                'Usulan Diklat Pegawai Baru',
                "{$pegawai->nama} mengusulkan diklat '{$pelatihan->nama_pelatihan}' yang memerlukan verifikasi.",
                ['type' => 'pelatihan', 'id' => (string) $pelatihan->id]
            );
        }

        return response()->json([
            'message' => 'Riwayat pelatihan berhasil ditambahkan dan menunggu verifikasi.',
            'data' => [
                'id' => $pelatihan->id,
                'nama_pelatihan' => $pelatihan->nama_pelatihan,
            ]
        ], 201);
    }
}
