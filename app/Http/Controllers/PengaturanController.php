<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Jabatan;
use App\Models\Pengaturan;
use App\Models\UnitKerja;
use App\Models\BentukPelatihan;
use App\Models\TipeKursus;
use App\Models\JenisKursus;
use App\Models\Instansi;
use App\Models\JenisCuti;
use App\Models\JenisKetidakhadiran;
use App\Models\StatusShift;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $p = fn (string $key, string $default) => Pengaturan::get($key, $default);

        return view('pengaturan.index', [
            // GPS & Lokasi
            'radiusGps'  => $p('radius_gps', '100'),
            'kantorLat'  => $p('kantor_lat', '-6.211544'),
            'kantorLng'  => $p('kantor_lng', '106.845172'),

            // Jam Kerja Normal
            'jamAwalAbsen'            => $p('jam_awal_absen', '05:00'),
            'jamMasukStandar'         => $p('jam_masuk_standar', $p('jam_masuk', '08:30')),
            'jamBatasTelat'           => $p('jam_batas_telat', '10:00'),
            'jamBatasAlpa'            => $p('jam_batas_alpa', '16:00'),
            'jamPulangStandar'        => $p('jam_pulang_standar', $p('jam_pulang', '16:00')),
            'jamPulangMinimalFleks'   => $p('jam_pulang_minimal_flexibel', '15:30'),
            'flexibleWorkHoursEnabled'=> (bool) $p('flexible_work_hours_enabled', '1'),

            // Jam Kerja Shift
            'toleransiAwalShiftMenit'  => $p('toleransi_awal_shift_menit', '60'),
            'toleransiTelatShiftMenit' => $p('toleransi_telat_shift_menit', '30'),

            // Cuti & Diklat
            'kuotaCuti' => $p('kuota_cuti_tahunan', '12'),
            'targetJp'  => $p('target_jp_tahunan', '20'),

            // Master data
            'units'               => UnitKerja::orderBy('nama_unit')->get(),
            'jabatans'            => Jabatan::orderBy('nama_jabatan')->get(),
            'bentukPelatihans'    => BentukPelatihan::orderBy('nama_bentuk')->get(),
            'tipeKursuses'        => TipeKursus::with('bentukPelatihan')->orderBy('nama_tipe')->get(),
            'jenisKursuses'       => JenisKursus::orderBy('nama_jenis')->get(),
            'instansis'           => Instansi::orderBy('nama_instansi')->get(),
            'jenisCutis'          => JenisCuti::orderBy('nama')->get(),
            'jenisKetidakhadirans'=> JenisKetidakhadiran::orderBy('nama')->get(),
            'statusShifts'        => StatusShift::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            // GPS & Lokasi
            'radius_gps'  => ['required', 'integer', 'min:10'],
            'kantor_lat'  => ['required', 'numeric'],
            'kantor_lng'  => ['required', 'numeric'],

            // Jam Kerja Normal
            'jam_awal_absen'              => ['required', 'date_format:H:i'],
            'jam_masuk_standar'           => ['required', 'date_format:H:i'],
            'jam_batas_telat'             => ['required', 'date_format:H:i'],
            'jam_batas_alpa'              => ['required', 'date_format:H:i'],
            'jam_pulang_standar'          => ['required', 'date_format:H:i'],
            'jam_pulang_minimal_flexibel' => ['required', 'date_format:H:i'],
            'flexible_work_hours_enabled' => ['nullable', 'boolean'],

            // Jam Kerja Shift
            'toleransi_awal_shift_menit'  => ['required', 'integer', 'min:0', 'max:180'],
            'toleransi_telat_shift_menit' => ['required', 'integer', 'min:0', 'max:120'],

            // Cuti & Diklat
            'kuota_cuti_tahunan' => ['required', 'integer', 'min:1'],
            'target_jp_tahunan'  => ['required', 'integer', 'min:1'],
        ]);

        // Checkbox boolean — jika tidak dikirim, berarti false
        $data['flexible_work_hours_enabled'] = $request->has('flexible_work_hours_enabled') ? '1' : '0';

        // Simpan semua key ke tabel pengaturans
        foreach ($data as $key => $value) {
            Pengaturan::set($key, $value);
        }

        // Sinkronisasi key lama agar backward-compat (kode lama yang masih baca jam_masuk / jam_pulang)
        Pengaturan::set('jam_masuk', $data['jam_masuk_standar']);
        Pengaturan::set('jam_pulang', $data['jam_pulang_standar']);

        AuditLog::catat('memperbarui pengaturan sistem', 'Pengaturan');

        return back()->with('status', 'Pengaturan berhasil disimpan.');
    }

    public function storeUnit(Request $request)
    {
        $data = $request->validate([
            'nama_unit' => ['required', 'string', 'max:255'],
            'kode_unit' => ['nullable', 'string', 'max:20', 'unique:unit_kerjas,kode_unit'],
        ]);

        UnitKerja::create($data);

        return back()->with('status', 'Unit kerja berhasil ditambahkan.');
    }

    public function destroyUnit(UnitKerja $unit)
    {
        $unit->delete();

        return back()->with('status', 'Unit kerja berhasil dihapus.');
    }

    public function storeJabatan(Request $request)
    {
        $data = $request->validate([
            'nama_jabatan' => ['required', 'string', 'max:255'],
        ]);

        Jabatan::create($data);

        return back()->with('status', 'Jabatan berhasil ditambahkan.');
    }

    public function destroyJabatan(Jabatan $jabatan)
    {
        $jabatan->delete();

        return back()->with('status', 'Jabatan berhasil dihapus.');
    }

    public function storeBentukPelatihan(Request $request)
    {
        $data = $request->validate([
            'nama_bentuk' => ['required', 'string', 'max:255'],
        ]);

        BentukPelatihan::create($data);

        return back()->with('status', 'Bentuk Pelatihan berhasil ditambahkan.');
    }

    public function destroyBentukPelatihan(BentukPelatihan $bentuk)
    {
        $bentuk->delete();

        return back()->with('status', 'Bentuk Pelatihan berhasil dihapus.');
    }

    public function storeTipeKursus(Request $request)
    {
        $data = $request->validate([
            'bentuk_pelatihan_id' => ['required', 'exists:bentuk_pelatihans,id'],
            'nama_tipe' => ['required', 'string', 'max:255'],
        ]);

        TipeKursus::create($data);

        return back()->with('status', 'Tipe Kursus berhasil ditambahkan.');
    }

    public function destroyTipeKursus(TipeKursus $tipe)
    {
        $tipe->delete();

        return back()->with('status', 'Tipe Kursus berhasil dihapus.');
    }

    public function storeJenisKursus(Request $request)
    {
        $data = $request->validate([
            'nama_jenis' => ['required', 'string', 'max:255'],
        ]);

        JenisKursus::create($data);

        return back()->with('status', 'Jenis Kursus berhasil ditambahkan.');
    }

    public function destroyJenisKursus(JenisKursus $jenis)
    {
        $jenis->delete();

        return back()->with('status', 'Jenis Kursus berhasil dihapus.');
    }

    public function storeInstansi(Request $request)
    {
        $data = $request->validate([
            'nama_instansi' => ['required', 'string', 'max:255'],
        ]);

        Instansi::create($data);

        return back()->with('status', 'Instansi berhasil ditambahkan.');
    }

    public function destroyInstansi(Instansi $instansi)
    {
        $instansi->delete();

        return back()->with('status', 'Instansi berhasil dihapus.');
    }

    public function storeJenisCuti(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'potong_saldo_cuti' => ['nullable', 'boolean'],
        ]);

        $data['potong_saldo_cuti'] = $request->has('potong_saldo_cuti');

        JenisCuti::create($data);

        return back()->with('status', 'Jenis Cuti berhasil ditambahkan.');
    }

    public function destroyJenisCuti(JenisCuti $jenisCuti)
    {
        $jenisCuti->delete();

        return back()->with('status', 'Jenis Cuti berhasil dihapus.');
    }

    public function storeJenisKetidakhadiran(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        JenisKetidakhadiran::create($data);

        return back()->with('status', 'Jenis Ketidakhadiran berhasil ditambahkan.');
    }

    public function destroyJenisKetidakhadiran(JenisKetidakhadiran $jenisKetidakhadiran)
    {
        $jenisKetidakhadiran->delete();

        return back()->with('status', 'Jenis Ketidakhadiran berhasil dihapus.');
    }

    public function storeStatusShift(Request $request)
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:status_shifts,kode'],
            'nama' => ['required', 'string', 'max:100'],
            'warna' => ['required', 'string', 'max:20'],
        ]);

        StatusShift::create($data);

        return back()->with('status', 'Status shift berhasil ditambahkan.');
    }

    public function destroyStatusShift(StatusShift $statusShift)
    {
        $statusShift->delete();

        return back()->with('status', 'Status shift berhasil dihapus.');
    }
}
