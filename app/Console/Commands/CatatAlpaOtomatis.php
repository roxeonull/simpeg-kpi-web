<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\JadwalShift;
use App\Models\Pegawai;
use App\Services\AbsensiStatusService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CatatAlpaOtomatis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absensi:catat-alpa
                            {--tanggal= : Tanggal yang diproses (Y-m-d), default hari ini}
                            {--dry-run  : Tampilkan pegawai yang akan di-alpa tanpa menyimpan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Catat otomatis status Alpa bagi pegawai yang belum presensi masuk setelah jam batas alpa berlalu';

    public function __construct(private AbsensiStatusService $statusService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $explicitTanggal = $this->option('tanggal');
        $isDryRun        = $this->option('dry-run');

        $tanggalsToProcess = [];
        if ($explicitTanggal) {
            $tanggalsToProcess[] = $explicitTanggal;
        } else {
            $tanggalsToProcess[] = now()->toDateString();
            // Pagi hari (00:00 - 12:00): sertakan juga tanggal kemarin untuk mengecek Shift 3 (22:00-06:00) yang berakhir jam 06:00
            if (now()->hour < 12) {
                $tanggalsToProcess[] = now()->subDay()->toDateString();
            }
        }

        $jumlahDiproses = 0;
        $jumlahDibuat   = 0;

        foreach ($tanggalsToProcess as $tanggalStr) {
            $tanggal = Carbon::parse($tanggalStr);
            $this->info("Memproses Alpa Otomatis untuk tanggal: {$tanggalStr}" . ($isDryRun ? ' [DRY-RUN]' : ''));

            // Ambil semua jadwal shift pada tanggal tersebut (keyed by pegawai_id)
            $shiftsOnDay = JadwalShift::whereDate('tanggal', $tanggalStr)
                ->get()
                ->keyBy('pegawai_id');

            // Ambil semua pegawai aktif
            $pegawais = Pegawai::where('status_aktif', 'aktif')->get();

            // Ambil ID pegawai yang sudah punya record absensi tanggal itu
            $sudahAbsenIds = Absensi::whereDate('tanggal', $tanggalStr)
                ->pluck('pegawai_id')
                ->toArray();

            foreach ($pegawais as $pegawai) {
                // Lewati jika sudah punya record absensi (apapun statusnya)
                if (in_array($pegawai->id, $sudahAbsenIds)) {
                    continue;
                }

                $shift = $shiftsOnDay->get($pegawai->id);

                // Cek apakah sudah harus di-alpa (waktu batas sudah lewat & tidak sedang cuti)
                if (!$this->statusService->apakahHarusAlpa($pegawai, $tanggal, $shift)) {
                    continue;
                }

                $jumlahDiproses++;

                if ($isDryRun) {
                    $tipe = $shift ? "Shift {$shift->shift}" : 'Normal';
                    $this->line("  [DRY] {$pegawai->nama} ({$tipe}) → akan dicatat ALPA ({$tanggalStr})");
                    continue;
                }

                // Buat record absensi dengan status alpa
                Absensi::create([
                    'pegawai_id' => $pegawai->id,
                    'tanggal'    => $tanggalStr,
                    'jam_masuk'  => null,
                    'jam_keluar' => null,
                    'status'     => 'alpa',
                    'keterangan' => 'Alpa otomatis — tidak presensi masuk' . ($shift ? " (Shift {$shift->shift})" : ''),
                ]);

                $jumlahDibuat++;
            }
        }

        if ($isDryRun) {
            $this->info("Total pegawai yang akan di-alpa: {$jumlahDiproses}");
        } else {
            $this->info("Selesai. Total record alpa dibuat: {$jumlahDibuat}");
        }

        return self::SUCCESS;
    }
}
