<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Cuti;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\PengajuanPerubahanData;
use App\Models\RiwayatPelatihan;
use App\Models\UnitKerja;
use App\Models\BentukPelatihan;
use App\Models\TipeKursus;
use App\Models\JenisKursus;
use App\Models\Instansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::with(['jabatan', 'unit', 'atasan']);

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($unit = $request->get('unit_id')) {
            $query->where('unit_id', $unit);
        }

        if ($status = $request->get('status_kepegawaian')) {
            $query->where('status_kepegawaian', $status);
        }

        if ($statusAktif = $request->get('status_aktif')) {
            $query->where('status_aktif', $statusAktif);
        }

        $perPage = in_array((int) $request->get('per_page'), [10, 15, 25, 50]) ? (int) $request->get('per_page') : 15;
        $pegawais = $query->orderBy('nama')->paginate($perPage)->withQueryString();

        // 1. Total Pegawai
        $totalPegawai = Pegawai::count();

        // 2. Keaktifan
        $aktifCount = Pegawai::where('status_aktif', 'aktif')->count();
        $nonaktifCount = Pegawai::where('status_aktif', 'nonaktif')->count();

        // 3. Breakdown Status Kepegawaian
        $pnsCount = Pegawai::where('status_kepegawaian', 'PNS')->count();
        $pppkCount = Pegawai::where('status_kepegawaian', 'PPPK')->count();
        $nonAsnCount = Pegawai::where('status_kepegawaian', 'Non-ASN')->count();

        // 4. Distribusi Pegawai per Unit Kerja (untuk Chart)
        $distribusiUnit = UnitKerja::withCount('pegawais')
            ->orderBy('pegawais_count', 'desc')
            ->get();

        // 5. Pegawai Baru Bergabung
        $pegawaiBaru = Pegawai::with(['jabatan'])
            ->whereNotNull('tmt')
            ->orderBy('tmt', 'desc')
            ->limit(5)
            ->get();

        // 6. Butuh Tindakan — hitungan pending per kategori
        $pendingCuti = Cuti::where(function ($q) {
            $q->where('status_atasan', 'menunggu')
              ->orWhere('status_hr', 'menunggu');
        })->count();

        $pendingPelatihan = RiwayatPelatihan::where('status_verifikasi', 'menunggu')->count();

        $pendingPerubahan = PengajuanPerubahanData::where('status', 'menunggu')->count();

        $totalButuhTindakan = $pendingCuti + $pendingPelatihan + $pendingPerubahan;

        return view('pegawai.index', [
            'pegawais'           => $pegawais,
            'units'              => UnitKerja::orderBy('nama_unit')->get(),
            'filters'            => $request->only(['q', 'unit_id', 'status_kepegawaian', 'status_aktif', 'per_page']),
            'totalPegawai'       => $totalPegawai,
            'aktifCount'         => $aktifCount,
            'nonaktifCount'      => $nonaktifCount,
            'pnsCount'           => $pnsCount,
            'pppkCount'          => $pppkCount,
            'nonAsnCount'        => $nonAsnCount,
            'distribusiUnit'     => $distribusiUnit,
            'pegawaiBaru'        => $pegawaiBaru,
            // Butuh Tindakan
            'pendingCuti'        => $pendingCuti,
            'pendingPelatihan'   => $pendingPelatihan,
            'pendingPerubahan'   => $pendingPerubahan,
            'totalButuhTindakan' => $totalButuhTindakan,
        ]);
    }

    public function create()
    {
        return view('pegawai.form', [
            'pegawai' => new Pegawai(),
            'units' => UnitKerja::orderBy('nama_unit')->get(),
            'jabatans' => Jabatan::orderBy('nama_jabatan')->get(),
            'atasanList' => Pegawai::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // Handle boolean checkbox mendapat_tunkin
        $data['mendapat_tunkin'] = $request->boolean('mendapat_tunkin');

        // Handle default cacat_tubuh
        if ($request->has('cacat_tubuh') && empty($data['cacat_tubuh'])) {
            $data['cacat_tubuh'] = 'Tidak ada';
        }

        // Handle file uploads
        $fileFields = [
            'foto' => 'pegawai/foto',
            'file_ktp' => 'pegawai/dokumen',
            'file_sk' => 'pegawai/dokumen',
            'file_karis_karsu' => 'pegawai/dokumen',
            'file_bpjs_kesehatan' => 'pegawai/dokumen',
            'file_taspen' => 'pegawai/dokumen',
            'file_npwp' => 'pegawai/dokumen',
            'file_kartu_asn_virtual' => 'pegawai/dokumen',
            'file_bpjs_ketenagakerjaan' => 'pegawai/dokumen',
            'file_kartu_keluarga' => 'pegawai/dokumen',
        ];

        foreach ($fileFields as $field => $path) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store($path, 'public');
            }
        }

        $pegawai = Pegawai::create($data);

        AuditLog::catat('menambah data pegawai', 'Pegawai', $pegawai->id, $pegawai->nama);

        return redirect()->route('pegawai.index')->with('status', 'Data pegawai berhasil ditambahkan.');
    }

    public function show(Request $request, Pegawai $pegawai)
    {
        $user = $request->user();
        if ($user->role === 'atasan' && $user->pegawai) {
            $timIds = $user->pegawai->anggotaTim()->pluck('id')->push($user->pegawai->id)->toArray();
            abort_unless(in_array($pegawai->id, $timIds, true), 403, 'Anda hanya dapat melihat data anggota tim Anda.');
        }

        $pegawai->load(['jabatan', 'unit', 'atasan', 'riwayatPendidikan', 'riwayatPelatihan', 'cuti' => function ($q) {
            $q->latest('tanggal_mulai')->limit(10);
        }]);

        $bulanShift = $request->get('bulan_shift', now()->format('Y-m'));
        $yearShift  = substr($bulanShift, 0, 4);
        $monthShift = substr($bulanShift, 5, 2);

        $riwayatShift = $pegawai->jadwalShift()
            ->with('statusShift')
            ->whereYear('tanggal', $yearShift)
            ->whereMonth('tanggal', $monthShift)
            ->orderByDesc('tanggal')
            ->get();

        $bulanAbsensi = $request->get('bulan_absensi', now()->format('Y-m'));
        $yearAbsensi  = substr($bulanAbsensi, 0, 4);
        $monthAbsensi = substr($bulanAbsensi, 5, 2);

        $riwayatAbsensi = $pegawai->absensi()
            ->whereYear('tanggal', $yearAbsensi)
            ->whereMonth('tanggal', $monthAbsensi)
            ->orderByDesc('tanggal')
            ->get();

        $totalMenitPenguranganBulanIni = (int) $riwayatAbsensi->sum('menit_pengurangan_jam_kerja');

        return view('pegawai.show', [
            'pegawai'                       => $pegawai,
            'saldoCuti'                     => $pegawai->saldoCutiTahunIni(),
            'totalJp'                       => $pegawai->totalJpTahunIni(),
            'bentukPelatihans'              => BentukPelatihan::orderBy('nama_bentuk')->get(),
            'tipeKursuses'                  => TipeKursus::with('bentukPelatihan')->orderBy('nama_tipe')->get(),
            'jenisKursuses'                 => JenisKursus::orderBy('nama_jenis')->get(),
            'instansis'                     => Instansi::orderBy('nama_instansi')->get(),
            'riwayatShift'                  => $riwayatShift,
            'riwayatAbsensi'                => $riwayatAbsensi,
            'totalMenitPenguranganBulanIni' => $totalMenitPenguranganBulanIni,
            'bulanAbsensi'                  => $bulanAbsensi,
        ]);
    }

    public function edit(Pegawai $pegawai)
    {
        return view('pegawai.form', [
            'pegawai' => $pegawai,
            'units' => UnitKerja::orderBy('nama_unit')->get(),
            'jabatans' => Jabatan::orderBy('nama_jabatan')->get(),
            'atasanList' => Pegawai::where('id', '!=', $pegawai->id)->orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $data = $this->validated($request, $pegawai->id);

        // Handle boolean checkbox mendapat_tunkin
        $data['mendapat_tunkin'] = $request->boolean('mendapat_tunkin');

        // Handle default cacat_tubuh
        if ($request->has('cacat_tubuh') && empty($data['cacat_tubuh'])) {
            $data['cacat_tubuh'] = 'Tidak ada';
        }

        // Handle file uploads
        $fileFields = [
            'foto' => 'pegawai/foto',
            'file_ktp' => 'pegawai/dokumen',
            'file_sk' => 'pegawai/dokumen',
            'file_karis_karsu' => 'pegawai/dokumen',
            'file_bpjs_kesehatan' => 'pegawai/dokumen',
            'file_taspen' => 'pegawai/dokumen',
            'file_npwp' => 'pegawai/dokumen',
            'file_kartu_asn_virtual' => 'pegawai/dokumen',
            'file_bpjs_ketenagakerjaan' => 'pegawai/dokumen',
            'file_kartu_keluarga' => 'pegawai/dokumen',
        ];

        foreach ($fileFields as $field => $path) {
            if ($request->hasFile($field)) {
                // Delete old file if exists
                if ($pegawai->$field) {
                    Storage::disk('public')->delete($pegawai->$field);
                }
                $data[$field] = $request->file($field)->store($path, 'public');
            }
        }

        $pegawai->update($data);

        AuditLog::catat('memperbarui data pegawai', 'Pegawai', $pegawai->id, $pegawai->nama);

        return redirect()->route('pegawai.index')->with('status', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        AuditLog::catat('menghapus data pegawai', 'Pegawai', $pegawai->id, $pegawai->nama);
        $pegawai->delete();

        return redirect()->route('pegawai.index')->with('status', 'Data pegawai berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nip' => ['required', 'string', 'max:30', 'unique:pegawais,nip' . ($ignoreId ? ",{$ignoreId}" : '')],
            'nama' => ['required', 'string', 'max:255'],
            'jabatan_id' => ['nullable', 'exists:jabatans,id'],
            'unit_id' => ['nullable', 'exists:unit_kerjas,id'],
            'atasan_id' => ['nullable', 'exists:pegawais,id'],
            'status_kepegawaian' => ['required', 'in:PNS,PPPK,Non-ASN'],
            'tmt' => ['nullable', 'date'],
            'email' => ['nullable', 'email', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'no_ktp' => ['nullable', 'string', 'max:20'],
            'status_aktif' => ['required', 'in:aktif,nonaktif'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'file_ktp' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'file_sk' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],

            // Data Personal
            'gelar_depan' => ['nullable', 'string', 'max:50'],
            'gelar_belakang' => ['nullable', 'string', 'max:50'],
            'nama_panggilan' => ['nullable', 'string', 'max:100'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'golongan_darah' => ['nullable', 'string', 'max:5'],
            'agama' => ['nullable', 'string', 'max:50'],
            'status_marital' => ['nullable', 'string', 'max:50'],
            'email_pribadi' => ['nullable', 'email', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'fax' => ['nullable', 'string', 'max:50'],
            'kelurahan' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'kota' => ['nullable', 'string', 'max:100'],
            'provinsi' => ['nullable', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],

            // Data Kepegawaian
            'tipe_pegawai' => ['nullable', 'string', 'max:100'],
            'jabatan_plt' => ['nullable', 'string', 'max:255'],
            'jabatan_plh' => ['nullable', 'string', 'max:255'],
            'tmt_cpns' => ['nullable', 'date'],
            'tmt_pns' => ['nullable', 'date'],
            'pangkat_golongan' => ['nullable', 'string', 'max:50'],
            'tmt_kepangkatan' => ['nullable', 'date'],
            'tmt_pangkat_berikutnya' => ['nullable', 'date'],
            'portal_status' => ['nullable', 'in:Aktif,Nonaktif'],
            'simpatik_status' => ['nullable', 'in:Aktif,Nonaktif'],
            'mendapat_tunkin' => ['nullable', 'boolean'],

            // Data Lain-Lain
            'no_karis_karsu' => ['nullable', 'string', 'max:50'],
            'file_karis_karsu' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'no_bpjs_kesehatan' => ['nullable', 'string', 'max:50'],
            'file_bpjs_kesehatan' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'no_taspen' => ['nullable', 'string', 'max:50'],
            'file_taspen' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'no_npwp' => ['nullable', 'string', 'max:50'],
            'file_npwp' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'no_kartu_asn_virtual' => ['nullable', 'string', 'max:50'],
            'file_kartu_asn_virtual' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'bkn_pns_id' => ['nullable', 'string', 'max:100'],
            'no_bpjs_ketenagakerjaan' => ['nullable', 'string', 'max:50'],
            'file_bpjs_ketenagakerjaan' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'no_kartu_keluarga' => ['nullable', 'string', 'max:50'],
            'file_kartu_keluarga' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'tinggi_badan' => ['nullable', 'integer', 'min:0'],
            'berat_badan' => ['nullable', 'integer', 'min:0'],
            'jenis_rambut' => ['nullable', 'string', 'max:100'],
            'bentuk_muka' => ['nullable', 'string', 'max:100'],
            'warna_kulit' => ['nullable', 'string', 'max:100'],
            'ciri_khas' => ['nullable', 'string', 'max:255'],
            'cacat_tubuh' => ['nullable', 'string', 'max:255'],
            'hobi' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
