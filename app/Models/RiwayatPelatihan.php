<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPelatihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id', 'nama_pelatihan', 'penyelenggara', 'tanggal', 'durasi_jp',
        'kategori', 'sertifikat', 'status_verifikasi', 'catatan', 'diverifikasi_oleh',
        
        'tanggal_akhir', 'instansi_id', 'bidang_sdm_spbe', 'no_sertifikat', 'tanggal_sertifikat',
        'bentuk_pelatihan_id', 'tipe_kursus_id', 'jenis_kursus_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'tanggal_akhir' => 'date',
            'tanggal_sertifikat' => 'date',
        ];
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function bentukPelatihan(): BelongsTo
    {
        return $this->belongsTo(BentukPelatihan::class, 'bentuk_pelatihan_id');
    }

    public function tipeKursus(): BelongsTo
    {
        return $this->belongsTo(TipeKursus::class, 'tipe_kursus_id');
    }

    public function jenisKursus(): BelongsTo
    {
        return $this->belongsTo(JenisKursus::class, 'jenis_kursus_id');
    }

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class, 'instansi_id');
    }

    public function getTahunDiklatAttribute(): ?int
    {
        return $this->tanggal ? $this->tanggal->year : null;
    }
}
