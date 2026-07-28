<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalShift extends Model
{
    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'shift',
        'stasiun_tv',
        'status_shift_id',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function statusShift(): BelongsTo
    {
        return $this->belongsTo(StatusShift::class);
    }

    public static function getJamMulai(string $shift): string
    {
        $map = [
            '1' => '06:00',
            '2' => '14:00',
            '3' => '22:00',
        ];
        return $map[$shift] ?? '08:00';
    }

    public static function getJamSelesai(string $shift): string
    {
        $map = [
            '1' => '14:00',
            '2' => '22:00',
            '3' => '06:00',
        ];
        return $map[$shift] ?? '16:30';
    }
}
