<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipeKursus extends Model
{
    use HasFactory;

    protected $table = 'tipe_kursuses';

    protected $fillable = ['bentuk_pelatihan_id', 'nama_tipe'];

    public function bentukPelatihan(): BelongsTo
    {
        return $this->belongsTo(BentukPelatihan::class, 'bentuk_pelatihan_id');
    }

    public function riwayatPelatihan(): HasMany
    {
        return $this->hasMany(RiwayatPelatihan::class, 'tipe_kursus_id');
    }
}
