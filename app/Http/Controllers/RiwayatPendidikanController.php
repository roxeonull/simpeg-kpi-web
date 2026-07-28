<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Pegawai;
use App\Models\RiwayatPendidikan;
use Illuminate\Http\Request;

class RiwayatPendidikanController extends Controller
{
    public function store(Request $request, Pegawai $pegawai)
    {
        $data = $request->validate([
            'jenjang' => ['required', 'in:SD,SMP,SMA/SMK,D3,D4,S1,S2,S3'],
            'institusi' => ['required', 'string', 'max:255'],
            'jurusan' => ['nullable', 'string', 'max:255'],
            'tahun_lulus' => ['required', 'digits:4', 'integer', 'min:1950', 'max:' . (date('Y') + 1)],
            'file_ijazah' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ]);

        if ($request->hasFile('file_ijazah')) {
            $data['file_ijazah'] = $request->file('file_ijazah')->store('pendidikan', 'public');
        }

        $pegawai->riwayatPendidikan()->create($data);

        AuditLog::catat('menambah riwayat pendidikan', 'RiwayatPendidikan', $pegawai->id, $pegawai->nama);

        return back()->with('status', 'Riwayat pendidikan berhasil ditambahkan.');
    }

    public function destroy(RiwayatPendidikan $riwayatPendidikan)
    {
        $pegawai = $riwayatPendidikan->pegawai;
        $riwayatPendidikan->delete();

        AuditLog::catat('menghapus riwayat pendidikan', 'RiwayatPendidikan', $pegawai->id, $pegawai->nama);

        return back()->with('status', 'Riwayat pendidikan berhasil dihapus.');
    }
}
