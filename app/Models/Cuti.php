<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Pegawai;
use App\Models\User;
use App\Models\JenisCuti;

class Cuti extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id', 'jenis_cuti', 'jenis_cuti_id', 'tanggal_mulai', 'tanggal_selesai', 'jumlah_hari',
        'alasan', 'alamat_cuti', 'lampiran', 'status_atasan', 'catatan_atasan', 'atasan_pemroses_id',
        'atasan_diproses_pada', 'status_hr', 'catatan_hr', 'hr_pemroses_id',
        'hr_diproses_pada', 'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'atasan_diproses_pada' => 'datetime',
            'hr_diproses_pada' => 'datetime',
        ];
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function atasanPemroses(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atasan_pemroses_id');
    }

    public function hrPemroses(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_pemroses_id');
    }

    public function jenisCuti(): BelongsTo
    {
        return $this->belongsTo(JenisCuti::class, 'jenis_cuti_id');
    }

    public function setujuiAtasan(User $user, ?string $catatan = null): void
    {
        $this->update([
            'status_atasan' => 'disetujui',
            'catatan_atasan' => $catatan,
            'atasan_pemroses_id' => $user->id,
            'atasan_diproses_pada' => now(),
            'status' => 'menunggu_hr',
        ]);

        if ($this->pegawai?->user) {
            $jenisStr = $this->jenisCuti?->nama ?? ucfirst($this->jenis_cuti);
            $tglStr = $this->tanggal_mulai->format('d M') . ' s.d. ' . $this->tanggal_selesai->format('d M Y');
            \App\Services\NotificationService::sendToUser(
                $this->pegawai->user,
                'Cuti Disetujui Atasan',
                "Pengajuan cuti {$jenisStr} ({$tglStr}) telah disetujui atasan, menunggu persetujuan HR.",
                ['type' => 'cuti', 'id' => (string) $this->id]
            );
        }
    }

    public function tolakAtasan(User $user, ?string $catatan = null): void
    {
        $this->update([
            'status_atasan' => 'ditolak',
            'catatan_atasan' => $catatan,
            'atasan_pemroses_id' => $user->id,
            'atasan_diproses_pada' => now(),
            'status' => 'ditolak',
        ]);

        if ($this->pegawai?->user) {
            $jenisStr = $this->jenisCuti?->nama ?? ucfirst($this->jenis_cuti);
            \App\Services\NotificationService::sendToUser(
                $this->pegawai->user,
                'Cuti Ditolak Atasan',
                "Pengajuan cuti {$jenisStr} Anda ditolak oleh atasan.",
                ['type' => 'cuti', 'id' => (string) $this->id]
            );
        }
    }

    public function setujuiHr(User $user, ?string $catatan = null): void
    {
        $this->update([
            'status_hr' => 'disetujui',
            'catatan_hr' => $catatan,
            'hr_pemroses_id' => $user->id,
            'hr_diproses_pada' => now(),
            'status' => 'disetujui',
        ]);

        $saldo = $this->pegawai->saldoCutiTahunIni();
        $potong = $this->jenisCuti ? $this->jenisCuti->potong_saldo_cuti : ($this->jenis_cuti === 'tahunan');
        if ($potong) {
            $saldo->decrement('sisa_saldo', $this->jumlah_hari);
        }

        if ($this->pegawai?->user) {
            $jenisStr = $this->jenisCuti?->nama ?? ucfirst($this->jenis_cuti);
            \App\Services\NotificationService::sendToUser(
                $this->pegawai->user,
                'Cuti Disetujui (Final)',
                "Pengajuan cuti {$jenisStr} Anda telah disetujui secara final oleh HR.",
                ['type' => 'cuti', 'id' => (string) $this->id]
            );
        }
    }

    public function tolakHr(User $user, ?string $catatan = null): void
    {
        $this->update([
            'status_hr' => 'ditolak',
            'catatan_hr' => $catatan,
            'hr_pemroses_id' => $user->id,
            'hr_diproses_pada' => now(),
            'status' => 'ditolak',
        ]);

        if ($this->pegawai?->user) {
            $jenisStr = $this->jenisCuti?->nama ?? ucfirst($this->jenis_cuti);
            \App\Services\NotificationService::sendToUser(
                $this->pegawai->user,
                'Cuti Ditolak HR',
                "Pengajuan cuti {$jenisStr} Anda ditolak oleh HR.",
                ['type' => 'cuti', 'id' => (string) $this->id]
            );
        }
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'menunggu_atasan' => 'Menunggu Atasan',
            'menunggu_hr' => 'Menunggu HR',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            default => $this->status,
        };
    }
}
