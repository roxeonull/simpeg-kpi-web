<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPendidikan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id', 'jenjang', 'institusi', 'jurusan', 'tahun_lulus', 'file_ijazah',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    protected static function booted(): void
    {
        static::saved(function (RiwayatPendidikan $riwayat) {
            $riwayat->pegawai?->syncPendidikanTerakhir();
        });

        static::deleted(function (RiwayatPendidikan $riwayat) {
            $riwayat->pegawai?->syncPendidikanTerakhir();
        });
    }
}

