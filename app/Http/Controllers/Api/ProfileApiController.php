<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileApiController extends Controller
{
    public function pengajuanPerubahan(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404);

        $data = $pegawai->pengajuanPerubahanData()->latest()->get()->map(fn ($p) => [
            'id' => $p->id,
            'field' => $p->field,
            'nilai_lama' => $p->nilai_lama,
            'nilai_baru' => $p->nilai_baru,
            'status' => $p->status,
            'catatan_admin' => $p->catatan_admin,
            'created_at' => $p->created_at->toIso8601String(),
        ]);

        return response()->json(['data' => $data]);
    }

    public function ajukanPerubahan(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404);

        $data = $request->validate([
            'field' => ['required', 'in:no_hp,alamat,email,email_pribadi,nama_panggilan,status_marital,golongan_darah,agama,hobi'],
            'nilai_baru' => ['required', 'string', 'max:500'],
        ]);

        $pengajuan = $pegawai->pengajuanPerubahanData()->create([
            'field' => $data['field'],
            'nilai_lama' => $pegawai->{$data['field']},
            'nilai_baru' => $data['nilai_baru'],
            'status' => 'menunggu',
        ]);

        AuditLog::catat('mengajukan perubahan data (mobile)', 'PengajuanPerubahanData', $pengajuan->id, $pegawai->nama);

        return response()->json(['message' => 'Pengajuan perubahan data terkirim, menunggu persetujuan Admin.'], 201);
    }

    public function detailPegawai(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404);

        $pegawai->loadMissing(['jabatan', 'unit', 'atasan']);

        $data = [
            'id' => $pegawai->id,
            // Personal
            'nama' => $pegawai->nama,
            'gelar_depan' => $pegawai->gelar_depan,
            'gelar_belakang' => $pegawai->gelar_belakang,
            'nama_panggilan' => $pegawai->nama_panggilan,
            'tempat_lahir' => $pegawai->tempat_lahir,
            'tanggal_lahir' => $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->toDateString() : null,
            'jenis_kelamin' => $pegawai->jenis_kelamin,
            'golongan_darah' => $pegawai->golongan_darah,
            'agama' => $pegawai->agama,
            'status_marital' => $pegawai->status_marital,
            'pendidikan_terakhir' => $pegawai->pendidikan_terakhir,
            'jurusan_pendidikan' => $pegawai->jurusan_pendidikan,
            'universitas' => $pegawai->universitas,
            'email' => $pegawai->email,
            'email_pribadi' => $pegawai->email_pribadi,
            'no_hp' => $pegawai->no_hp,
            'telepon' => $pegawai->telepon,
            'fax' => $pegawai->fax,
            'alamat' => $pegawai->alamat,
            'kelurahan' => $pegawai->kelurahan,
            'kecamatan' => $pegawai->kecamatan,
            'kota' => $pegawai->kota,
            'provinsi' => $pegawai->provinsi,
            'kode_pos' => $pegawai->kode_pos,

            // Kepegawaian
            'nip' => $pegawai->nip,
            'tipe_pegawai' => $pegawai->tipe_pegawai,
            'status_kepegawaian' => $pegawai->status_kepegawaian,
            'status_aktif' => $pegawai->status_aktif,
            'jabatan' => $pegawai->jabatan?->nama_jabatan,
            'unit' => $pegawai->unit?->nama_unit,
            'atasan' => $pegawai->atasan?->nama,
            'jabatan_plt' => $pegawai->jabatan_plt,
            'jabatan_plh' => $pegawai->jabatan_plh,
            'pangkat_golongan' => $pegawai->pangkat_golongan,
            'tmt_kepangkatan' => $pegawai->tmt_kepangkatan ? $pegawai->tmt_kepangkatan->toDateString() : null,
            'tmt_cpns' => $pegawai->tmt_cpns ? $pegawai->tmt_cpns->toDateString() : null,
            'tmt_pns' => $pegawai->tmt_pns ? $pegawai->tmt_pns->toDateString() : null,
            'tmt' => $pegawai->tmt ? $pegawai->tmt->toDateString() : null,
            'tmt_pangkat_berikutnya' => $pegawai->tmt_pangkat_berikutnya ? $pegawai->tmt_pangkat_berikutnya->toDateString() : null,
            'portal_status' => $pegawai->portal_status,
            'simpatik_status' => $pegawai->simpatik_status,
            'mendapat_tunkin' => (bool)$pegawai->mendapat_tunkin,
            'masa_kerja_keseluruhan' => $pegawai->masa_kerja_keseluruhan,
            'masa_kerja_golongan' => $pegawai->masa_kerja_golongan,

            // Lain-Lain & Dokumen
            'no_ktp' => $pegawai->no_ktp,
            'no_kartu_keluarga' => $pegawai->no_kartu_keluarga,
            'bkn_pns_id' => $pegawai->bkn_pns_id,
            'tinggi_badan' => $pegawai->tinggi_badan,
            'berat_badan' => $pegawai->berat_badan,
            'jenis_rambut' => $pegawai->jenis_rambut,
            'bentuk_muka' => $pegawai->bentuk_muka,
            'warna_kulit' => $pegawai->warna_kulit,
            'ciri_khas' => $pegawai->ciri_khas,
            'cacat_tubuh' => $pegawai->cacat_tubuh,
            'hobi' => $pegawai->hobi,
            'no_karis_karsu' => $pegawai->no_karis_karsu,
            'no_bpjs_kesehatan' => $pegawai->no_bpjs_kesehatan,
            'no_bpjs_ketenagakerjaan' => $pegawai->no_bpjs_ketenagakerjaan,
            'no_taspen' => $pegawai->no_taspen,
            'no_npwp' => $pegawai->no_npwp,
            'no_kartu_asn_virtual' => $pegawai->no_kartu_asn_virtual,

            // Dokumen File URLs
            'foto' => (!empty($pegawai->foto)) ? asset('storage/' . $pegawai->foto) : null,
            'foto_url' => (!empty($pegawai->foto)) ? asset('storage/' . $pegawai->foto) : null,
            'file_ktp_url' => $pegawai->file_ktp ? asset('storage/' . $pegawai->file_ktp) : null,
            'file_sk_url' => $pegawai->file_sk ? asset('storage/' . $pegawai->file_sk) : null,
            'file_kartu_keluarga_url' => $pegawai->file_kartu_keluarga ? asset('storage/' . $pegawai->file_kartu_keluarga) : null,
            'file_karis_karsu_url' => $pegawai->file_karis_karsu ? asset('storage/' . $pegawai->file_karis_karsu) : null,
            'file_bpjs_kesehatan_url' => $pegawai->file_bpjs_kesehatan ? asset('storage/' . $pegawai->file_bpjs_kesehatan) : null,
            'file_bpjs_ketenagakerjaan_url' => $pegawai->file_bpjs_ketenagakerjaan ? asset('storage/' . $pegawai->file_bpjs_ketenagakerjaan) : null,
            'file_taspen_url' => $pegawai->file_taspen ? asset('storage/' . $pegawai->file_taspen) : null,
            'file_npwp_url' => $pegawai->file_npwp ? asset('storage/' . $pegawai->file_npwp) : null,
            'file_kartu_asn_virtual_url' => $pegawai->file_kartu_asn_virtual ? asset('storage/' . $pegawai->file_kartu_asn_virtual) : null,
        ];

        return response()->json(['data' => $data]);
    }

    public function ubahPassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password lama tidak sesuai.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        AuditLog::catat('mengubah password (mobile)', 'User', $user->id, $user->name);

        return response()->json([
            'message' => 'Kata sandi berhasil diperbarui.',
        ]);
    }
}
