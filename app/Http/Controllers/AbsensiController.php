<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Absensi;
use App\Models\JadwalShift;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Services\AbsensiStatusService;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function __construct(private AbsensiStatusService $statusService) {}

    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', now()->toDateString());
        $p       = $this->statusService->getPengaturan();

        // Jam batas alpa untuk pegawai normal (dipakai label di view)
        $jamBatasAlpa = $p['jam_batas_alpa'];

        // Tentukan apakah tanggal ini sudah melewati jam batas alpa normal
        // (untuk keperluan label "Alpa" vs "Belum Presensi" di header section)
        $today           = now()->toDateString();
        $isPastJamBatasAlpa = false;
        if ($tanggal < $today) {
            $isPastJamBatasAlpa = true;
        } elseif ($tanggal === $today) {
            $isPastJamBatasAlpa = now()->format('H:i') >= $jamBatasAlpa;
        }

        // Ambil jadwal shift untuk tanggal terpilih
        $shiftsOnDay = JadwalShift::whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('pegawai_id');

        // Scoping berdasarkan role
        $scopePegawaiIds = null;
        if ($request->user()->role === 'atasan' && $request->user()->pegawai) {
            $scopePegawaiIds = $request->user()->pegawai->anggotaTim()
                ->pluck('id')
                ->push($request->user()->pegawai->id)
                ->toArray();
        }

        // Semua pegawai aktif di lingkup
        $pegawaiQuery = Pegawai::with('unit')
            ->where('status_aktif', 'aktif')
            ->when($scopePegawaiIds, fn ($q) => $q->whereIn('id', $scopePegawaiIds));

        if ($unit = $request->get('unit_id')) {
            $pegawaiQuery->where('unit_id', $unit);
        }
        $allPegawais = $pegawaiQuery->get();

        // ID pegawai yang sudah punya record absensi
        $recordedPegawaiIds = Absensi::whereDate('tanggal', $tanggal)
            ->when($scopePegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $scopePegawaiIds))
            ->pluck('pegawai_id')
            ->toArray();

        // Pegawai yang belum presensi
        $belumPresensiPegawais = $allPegawais->reject(fn ($p) => in_array($p->id, $recordedPegawaiIds));

        // Query absensi dari DB
        $query = Absensi::with(['pegawai.unit', 'jenisKetidakhadiran'])->whereDate('tanggal', $tanggal);
        if ($scopePegawaiIds) {
            $query->whereIn('pegawai_id', $scopePegawaiIds);
        }
        if ($unit) {
            $query->whereHas('pegawai', fn ($q) => $q->where('unit_id', $unit));
        }
        if ($jenisKetidakhadiran = $request->get('jenis_ketidakhadiran_id')) {
            $query->where('jenis_ketidakhadiran_id', $jenisKetidakhadiran);
        }

        $status = $request->get('status');
        if ($status) {
            if ($status === 'belum_presensi') {
                $query->whereRaw('1 = 0');
            } else {
                $query->where('status', $status);
            }
        }

        // Filter khusus: perlu ditinjau (flag GPS spoofing)
        if ($request->boolean('perlu_ditinjau')) {
            $query->where('flag_review', true);
        }

        $absensis = $query->orderBy('pegawai_id')->paginate(20)->withQueryString();

        // Saring $belumPresensiPegawais sesuai filter status
        if ($status) {
            if ($status === 'belum_presensi') {
                // tampilkan semua yang belum presensi (tidak difilter lebih lanjut)
            } elseif ($status === 'alpa') {
                // Hanya tampilkan yang sudah melewati jam batas alpa MASING-MASING
                $belumPresensiPegawais = $belumPresensiPegawais->filter(
                    fn ($pegawai) => $this->sudahMelampauiBatasAlpa($pegawai, $tanggal, $shiftsOnDay)
                );
            } else {
                $belumPresensiPegawais = collect();
            }
        }

        // Hitung statistik rekap
        $hadir = Absensi::whereDate('tanggal', $tanggal)
            ->when($scopePegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $scopePegawaiIds))
            ->where('status', 'hadir')->count();

        $telat = Absensi::whereDate('tanggal', $tanggal)
            ->when($scopePegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $scopePegawaiIds))
            ->where('status', 'telat')->count();

        $izinSakit = Absensi::whereDate('tanggal', $tanggal)
            ->when($scopePegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $scopePegawaiIds))
            ->whereIn('status', ['izin', 'sakit'])->count();

        $recordedAlpa = Absensi::whereDate('tanggal', $tanggal)
            ->when($scopePegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $scopePegawaiIds))
            ->where('status', 'alpa')->count();

        // Hitung alpa & belum presensi dari pegawai yang belum punya record
        $unrecordedAlpa          = 0;
        $unrecordedBelumPresensi = 0;

        foreach ($belumPresensiPegawais as $pegawai) {
            if ($this->sudahMelampauiBatasAlpa($pegawai, $tanggal, $shiftsOnDay)) {
                $unrecordedAlpa++;
            } else {
                $unrecordedBelumPresensi++;
            }
        }

        $rekapHariIni = [
            'hadir'          => $hadir,
            'telat'          => $telat,
            'izin_sakit'     => $izinSakit,
            'alpa'           => $recordedAlpa + $unrecordedAlpa,
            'belum_presensi' => $unrecordedBelumPresensi,
        ];

        // Breakdown jenis ketidakhadiran
        $breakdownKetidakhadiran = Absensi::whereDate('tanggal', $tanggal)
            ->when($scopePegawaiIds, fn ($q) => $q->whereIn('pegawai_id', $scopePegawaiIds))
            ->whereNotNull('jenis_ketidakhadiran_id')
            ->join('jenis_ketidakhadirans', 'absensis.jenis_ketidakhadiran_id', '=', 'jenis_ketidakhadirans.id')
            ->select('jenis_ketidakhadirans.nama', \DB::raw('count(absensis.id) as total'))
            ->groupBy('jenis_ketidakhadirans.id', 'jenis_ketidakhadirans.nama')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => ['nama' => $row->nama, 'total' => $row->total]);

        return view('absensi.index', [
            'absensis'               => $absensis,
            'tanggal'                => $tanggal,
            'units'                  => UnitKerja::orderBy('nama_unit')->get(),
            'jenisKetidakhadirans'   => \App\Models\JenisKetidakhadiran::orderBy('nama')->get(),
            'filters'                => $request->only(['unit_id', 'status', 'jenis_ketidakhadiran_id', 'perlu_ditinjau']),
            'rekapHariIni'           => $rekapHariIni,
            'breakdownKetidakhadiran'=> $breakdownKetidakhadiran,
            'belumPresensiPegawais'  => $belumPresensiPegawais,
            'isPastJamBatasAlpa'     => $isPastJamBatasAlpa,
            'jamBatasAlpa'           => $jamBatasAlpa,
            'shiftsOnDay'            => $shiftsOnDay,
        ]);
    }

    public function create()
    {
        return view('absensi.create', [
            'pegawais'            => Pegawai::where('status_aktif', 'aktif')->orderBy('nama')->get(),
            'jenisKetidakhadirans'=> \App\Models\JenisKetidakhadiran::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $statusInput  = $request->input('status');
        $requiresJenis = in_array($statusInput, ['izin', 'sakit']);

        $data = $request->validate([
            'pegawai_id'              => ['required', 'exists:pegawais,id'],
            'tanggal'                 => ['required', 'date'],
            'jam_masuk'               => ['nullable', 'date_format:H:i'],
            'jam_keluar'              => ['nullable', 'date_format:H:i'],
            'status'                  => ['required', 'in:hadir,telat,izin,sakit,alpa'],
            'jenis_ketidakhadiran_id' => [
                $requiresJenis ? 'required' : 'nullable',
                'exists:jenis_ketidakhadirans,id',
            ],
            'keterangan' => ['nullable', 'string'],
        ]);

        if (!$requiresJenis) {
            $data['jenis_ketidakhadiran_id'] = null;
        }

        // Jika admin mencatat jam masuk, hitung jam_pulang_diizinkan secara otomatis
        if (!empty($data['jam_masuk'])) {
            $shiftToday = JadwalShift::where('pegawai_id', $data['pegawai_id'])
                ->whereDate('tanggal', $data['tanggal'])
                ->first();

            $data['jam_pulang_diizinkan'] = $this->statusService
                ->hitungJamPulangDiizinkan($data['jam_masuk'], $shiftToday);

            // Jika ada jam keluar, hitung total menit pengurangan jam kerja (telat + pulang cepat)
            if (!empty($data['jam_keluar'])) {
                $hasilPengurangan = $this->statusService->hitungTotalMenitPengurangan(
                    $data['jam_masuk'],
                    $data['jam_keluar'],
                    $shiftToday,
                    $data['jam_pulang_diizinkan']
                );
                $data['menit_pengurangan_jam_kerja'] = $hasilPengurangan['total_menit_pengurangan'] ?: null;
            }
        }

        $absensi = Absensi::updateOrCreate(
            ['pegawai_id' => $data['pegawai_id'], 'tanggal' => $data['tanggal']],
            $data
        );

        AuditLog::catat('mencatat absensi manual', 'Absensi', $absensi->id, $absensi->pegawai->nama ?? null);

        return redirect()->route('absensi.index', ['tanggal' => $data['tanggal']])
            ->with('status', 'Absensi manual berhasil dicatat.');
    }

    // ------------------------------------------------------------------
    //  Helpers
    // ------------------------------------------------------------------

    /**
     * Cek apakah seorang pegawai sudah melewati jam batas alpa mereka pada tanggal tertentu.
     * Untuk shift: batas = jam selesai shift.
     * Untuk normal: batas = jam_batas_alpa dari pengaturan.
     */
    private function sudahMelampauiBatasAlpa(Pegawai $pegawai, string $tanggal, $shiftsOnDay): bool
    {
        $shift = $shiftsOnDay->get($pegawai->id);
        $p     = $this->statusService->getPengaturan();

        if ($shift) {
            $jamBatasStr = JadwalShift::getJamSelesai($shift->shift);
        } else {
            $jamBatasStr = $p['jam_batas_alpa'];
        }

        $today = now()->toDateString();

        if ($tanggal < $today) {
            return true;
        }

        if ($tanggal === $today) {
            // Shift malam (shift 3) selesai di hari berikutnya jam 06:00
            if ($shift && $shift->shift === '3') {
                return false; // Shift malam belum selesai pada hari yang sama
            }
            return now()->format('H:i') >= $jamBatasStr;
        }

        return false;
    }

    private function applyScope($query, $user): void
    {
        if ($user->role === 'atasan' && $user->pegawai) {
            $ids = $user->pegawai->anggotaTim()->pluck('id')->push($user->pegawai->id)->toArray();
            $query->whereIn('pegawai_id', $ids);
        }
    }
}
