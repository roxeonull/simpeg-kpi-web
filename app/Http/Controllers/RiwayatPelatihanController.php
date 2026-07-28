<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Pegawai;
use App\Models\RiwayatPelatihan;
use App\Models\BentukPelatihan;
use App\Models\TipeKursus;
use App\Models\JenisKursus;
use App\Models\Instansi;
use Illuminate\Http\Request;

class RiwayatPelatihanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $scopePegawaiIds = null;
        if ($user->role === 'atasan' && $user->pegawai) {
            $scopePegawaiIds = $user->pegawai->anggotaTim()->pluck('id')->push($user->pegawai->id)->toArray();
        }

        $query = RiwayatPelatihan::with(['pegawai.unit', 'bentukPelatihan', 'tipeKursus', 'jenisKursus', 'instansi']);

        if ($scopePegawaiIds) {
            $query->whereIn('pegawai_id', $scopePegawaiIds);
        }

        if ($search = $request->get('q')) {
            $query->whereHas('pegawai', fn ($q) => $q->where('nama', 'like', "%{$search}%"));
        }

        if ($status = $request->get('status_verifikasi')) {
            $query->where('status_verifikasi', $status);
        }

        if ($kategori = $request->get('kategori')) {
            $query->where('kategori', $kategori);
        }

        $pelatihans = $query->latest('tanggal')->paginate(15)->withQueryString();

        $targetJp = 20;

        $rekapPegawai = Pegawai::where('status_aktif', 'aktif')
            ->when($scopePegawaiIds, fn ($q) => $q->whereIn('id', $scopePegawaiIds))
            ->with(['unit'])
            ->withCount('riwayatPelatihan as total_pelatihan')
            ->withMax('riwayatPelatihan as pelatihan_terakhir', 'tanggal')
            ->withSum(['riwayatPelatihan as jp_tahun_ini' => function ($query) {
                $query->where('status_verifikasi', 'terverifikasi')
                      ->whereYear('tanggal', now()->year);
            }], 'durasi_jp')
            ->orderBy('nama')
            ->get();

        $totalPegawai = $rekapPegawai->count();
        $capaianDiklatCount = $rekapPegawai->filter(fn ($p) => (int)($p->jp_tahun_ini ?? 0) >= $targetJp)->count();
        $totalJpSum = $rekapPegawai->sum(fn ($p) => (int)($p->jp_tahun_ini ?? 0));
        $rataRataJp = $totalPegawai > 0 ? round($totalJpSum / $totalPegawai, 1) : 0;

        $totalPelatihanTahunIni = RiwayatPelatihan::whereYear('tanggal', now()->year)
            ->when($scopePegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $scopePegawaiIds))
            ->count();
        $menungguVerifikasiCount = RiwayatPelatihan::where('status_verifikasi', 'menunggu')
            ->when($scopePegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $scopePegawaiIds))
            ->count();

        return view('pelatihan.index', [
            'pelatihans' => $pelatihans,
            'filters' => $request->only(['q', 'status_verifikasi', 'kategori']),
            'rekapPegawai' => $rekapPegawai,
            'targetJp' => $targetJp,
            'totalPegawai' => $totalPegawai,
            'capaianDiklatCount' => $capaianDiklatCount,
            'rataRataJp' => $rataRataJp,
            'totalPelatihanTahunIni' => $totalPelatihanTahunIni,
            'menungguVerifikasiCount' => $menungguVerifikasiCount,
        ]);
    }

    public function store(Request $request, Pegawai $pegawai)
    {
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

        if ($request->hasFile('sertifikat')) {
            $data['sertifikat'] = $request->file('sertifikat')->store('pelatihan', 'public');
        }

        $pegawai->riwayatPelatihan()->create($data);

        AuditLog::catat('menambah riwayat pelatihan', 'RiwayatPelatihan', $pegawai->id, $pegawai->nama);

        return back()->with('status', 'Riwayat pelatihan berhasil ditambahkan.');
    }

    public function verifikasi(Request $request, RiwayatPelatihan $riwayatPelatihan)
    {
        $data = $request->validate([
            'keputusan' => ['required', 'in:terverifikasi,ditolak'],
            'catatan' => ['nullable', 'string'],
        ]);

        $riwayatPelatihan->update([
            'status_verifikasi' => $data['keputusan'],
            'catatan' => $data['catatan'] ?? null,
            'diverifikasi_oleh' => $request->user()->id,
        ]);

        if ($riwayatPelatihan->pegawai?->user) {
            $isSetuju = $data['keputusan'] === 'terverifikasi';
            $title = $isSetuju ? 'Pelatihan Terverifikasi' : 'Verifikasi Pelatihan Ditolak';
            $body = $isSetuju
                ? "Usulan pelatihan '{$riwayatPelatihan->nama_pelatihan}' Anda telah diverifikasi."
                : "Usulan pelatihan '{$riwayatPelatihan->nama_pelatihan}' Anda ditolak.";

            \App\Services\NotificationService::sendToUser(
                $riwayatPelatihan->pegawai->user,
                $title,
                $body,
                ['type' => 'pelatihan', 'id' => (string) $riwayatPelatihan->id]
            );
        }

        AuditLog::catat(
            $data['keputusan'] === 'terverifikasi' ? 'memverifikasi pelatihan' : 'menolak verifikasi pelatihan',
            'RiwayatPelatihan',
            $riwayatPelatihan->id,
            $riwayatPelatihan->nama_pelatihan
        );

        return back()->with('status', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy(RiwayatPelatihan $riwayatPelatihan)
    {
        $pegawai = $riwayatPelatihan->pegawai;
        $riwayatPelatihan->delete();

        AuditLog::catat('menghapus riwayat pelatihan', 'RiwayatPelatihan', $pegawai->id, $pegawai->nama);

        return back()->with('status', 'Riwayat pelatihan berhasil dihapus.');
    }

    public function show(Request $request, RiwayatPelatihan $riwayatPelatihan)
    {
        $user = $request->user();
        if ($user->role === 'atasan' && $user->pegawai) {
            $timIds = $user->pegawai->anggotaTim()->pluck('id')->push($user->pegawai->id)->toArray();
            abort_unless(in_array($riwayatPelatihan->pegawai_id, $timIds, true), 403, 'Anda hanya dapat melihat data pelatihan anggota tim Anda.');
        }

        $riwayatPelatihan->load(['pegawai.unit', 'pegawai.jabatan', 'verifikator']);

        $backUrl = route('pelatihan.index');
        $prev = url()->previous();
        if (str_contains($prev, '/pegawai/')) {
            $backUrl = $prev;
        }

        return view('pelatihan.show', [
            'riwayatPelatihan' => $riwayatPelatihan,
            'backUrl' => $backUrl,
        ]);
    }

    public function pegawai(Request $request, Pegawai $pegawai)
    {
        $user = $request->user();
        if ($user->role === 'atasan' && $user->pegawai) {
            $timIds = $user->pegawai->anggotaTim()->pluck('id')->push($user->pegawai->id)->toArray();
            abort_unless(in_array($pegawai->id, $timIds, true), 403, 'Anda hanya dapat melihat data pelatihan anggota tim Anda.');
        }

        $pegawai->load(['jabatan', 'unit', 'riwayatPelatihan' => function ($q) {
            $q->latest('tanggal');
        }]);

        return view('pelatihan.pegawai', [
            'pegawai' => $pegawai,
            'bentukPelatihans' => BentukPelatihan::orderBy('nama_bentuk')->get(),
            'tipeKursuses' => TipeKursus::orderBy('nama_tipe')->get(),
            'jenisKursuses' => JenisKursus::orderBy('nama_jenis')->get(),
            'instansis' => Instansi::orderBy('nama_instansi')->get(),
        ]);
    }
}
