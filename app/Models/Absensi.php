<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Pegawai;
use App\Models\JenisKetidakhadiran;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id', 'tanggal', 'jam_masuk', 'jam_keluar',
        'jam_pulang_diizinkan', 'menit_pengurangan_jam_kerja',
        'latitude_masuk', 'longitude_masuk', 'latitude_keluar', 'longitude_keluar',
        'status', 'keterangan', 'jenis_ketidakhadiran_id',
        // Kolom deteksi indikasi GPS spoofing (diisi dari data mobile, bersifat observatif)
        'is_mock_location', 'gps_accuracy', 'flag_review', 'catatan_flag',
        // Foto selfie presensi masuk (path relatif storage/app/public)
        'foto_masuk',
    ];

    protected function casts(): array
    {
        return [
            'tanggal'                     => 'date',
            'menit_pengurangan_jam_kerja' => 'integer',
            // GPS flag columns
            'is_mock_location'            => 'boolean',
            'gps_accuracy'                => 'float',
            'flag_review'                 => 'boolean',
        ];
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jenisKetidakhadiran(): BelongsTo
    {
        return $this->belongsTo(JenisKetidakhadiran::class, 'jenis_ketidakhadiran_id');
    }

    public function statusBadgeColor(): string
    {
        return match ($this->status) {
            'hadir' => 'success',
            'telat' => 'warning',
            'izin', 'sakit' => 'info',
            'alpa' => 'danger',
            default => 'default',
        };
    }

    /**
     * Kembalikan URL publik foto selfie presensi masuk, atau null jika belum ada.
     * Dipakai di view untuk menampilkan gambar langsung tanpa parsing teks keterangan.
     */
    public function getFotoMasukUrl(): ?string
    {
        if (!$this->foto_masuk) {
            return null;
        }
        return asset('storage/' . $this->foto_masuk);
    }
}
