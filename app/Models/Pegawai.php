<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nip', 'nama', 'jabatan_id', 'unit_id', 'atasan_id',
        'status_kepegawaian', 'tmt', 'foto', 'email', 'no_hp', 'alamat',
        'no_ktp', 'file_ktp', 'file_sk', 'status_aktif', 'stasiun_tv',

        // Data Personal
        'gelar_depan', 'gelar_belakang', 'nama_panggilan', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'golongan_darah', 'agama', 'status_marital', 'pendidikan_terakhir',
        'jurusan_pendidikan', 'universitas', 'email_pribadi', 'telepon', 'fax',
        'kelurahan', 'kecamatan', 'kota', 'provinsi', 'kode_pos',

        // Data Kepegawaian
        'tipe_pegawai', 'jabatan_plt', 'jabatan_plh', 'tmt_cpns', 'tmt_pns',
        'pangkat_golongan', 'tmt_kepangkatan', 'tmt_pangkat_berikutnya', 'portal_status',
        'simpatik_status', 'mendapat_tunkin',

        // Data Lain-Lain
        'no_karis_karsu', 'file_karis_karsu', 'no_bpjs_kesehatan', 'file_bpjs_kesehatan',
        'no_taspen', 'file_taspen', 'no_npwp', 'file_npwp', 'no_kartu_asn_virtual',
        'file_kartu_asn_virtual', 'bkn_pns_id', 'no_bpjs_ketenagakerjaan', 'file_bpjs_ketenagakerjaan',
        'no_kartu_keluarga', 'file_kartu_keluarga', 'tinggi_badan', 'berat_badan',
        'jenis_rambut', 'bentuk_muka', 'warna_kulit', 'ciri_khas', 'cacat_tubuh', 'hobi',
    ];

    protected function casts(): array
    {
        return [
            'tmt' => 'date',
            'tanggal_lahir' => 'date',
            'tmt_cpns' => 'date',
            'tmt_pns' => 'date',
            'tmt_kepangkatan' => 'date',
            'tmt_pangkat_berikutnya' => 'date',
            'mendapat_tunkin' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class, 'unit_id');
    }

    public function atasan(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'atasan_id');
    }

    public function anggotaTim(): HasMany
    {
        return $this->hasMany(Pegawai::class, 'atasan_id');
    }

    public function riwayatPendidikan(): HasMany
    {
        return $this->hasMany(RiwayatPendidikan::class);
    }

    public function riwayatPelatihan(): HasMany
    {
        return $this->hasMany(RiwayatPelatihan::class);
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function cuti(): HasMany
    {
        return $this->hasMany(Cuti::class);
    }

    public function saldoCuti(): HasMany
    {
        return $this->hasMany(SaldoCuti::class);
    }

    public function jadwalShift(): HasMany
    {
        return $this->hasMany(JadwalShift::class);
    }

    public function pengajuanPerubahanData(): HasMany
    {
        return $this->hasMany(PengajuanPerubahanData::class);
    }

    public function saldoCutiTahunIni()
    {
        return $this->saldoCuti()->firstOrCreate(
            ['tahun' => now()->year],
            ['total_saldo' => 12, 'sisa_saldo' => 12]
        );
    }

    public function totalJpTahunIni(): int
    {
        return (int) $this->riwayatPelatihan()
            ->where('status_verifikasi', 'terverifikasi')
            ->whereYear('tanggal', now()->year)
            ->sum('durasi_jp');
    }

    public function getMasaKerjaKeseluruhanAttribute(): ?string
    {
        if (!$this->tmt_cpns) {
            return null;
        }
        $diff = \Carbon\Carbon::parse($this->tmt_cpns)->diff(now());
        return "{$diff->y} Tahun {$diff->m} Bulan";
    }

    public function getMasaKerjaGolonganAttribute(): ?string
    {
        if (!$this->tmt_kepangkatan) {
            return null;
        }
        $diff = \Carbon\Carbon::parse($this->tmt_kepangkatan)->diff(now());
        return "{$diff->y} Tahun {$diff->m} Bulan";
    }
}
