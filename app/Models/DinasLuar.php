<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DinasLuar extends Model
{
    use HasFactory;

    protected $table = 'dinas_luars';

    protected $fillable = [
        'pegawai_id',
        'jenis_ketidakhadiran_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi_tugas',
        'alasan',
        'file_spt',
        'status',
        'approved_by',
        'catatan_atasan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date:Y-m-d',
            'tanggal_selesai' => 'date:Y-m-d',
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getFileSptUrlAttribute(): ?string
    {
        if (!$this->file_spt) {
            return null;
        }
        return asset('storage/' . $this->file_spt);
    }

    public function syncToAbsensi(): void
    {
        if ($this->status !== 'disetujui') {
            return;
        }

        $start = \Carbon\Carbon::parse($this->tanggal_mulai);
        $end   = \Carbon\Carbon::parse($this->tanggal_selesai);

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dateStr = $date->toDateString();

            Absensi::updateOrCreate(
                [
                    'pegawai_id' => $this->pegawai_id,
                    'tanggal'    => $dateStr,
                ],
                [
                    'status'                  => 'izin',
                    'jenis_ketidakhadiran_id' => $this->jenis_ketidakhadiran_id,
                    'keterangan'              => '[Dinas Luar / WFA] ' . $this->lokasi_tugas . ($this->alasan ? ' - ' . $this->alasan : ''),
                ]
            );
        }
    }
}
