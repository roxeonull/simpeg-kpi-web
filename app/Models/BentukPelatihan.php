<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BentukPelatihan extends Model
{
    use HasFactory;

    protected $fillable = ['nama_bentuk'];

    public function tipeKursus(): HasMany
    {
        return $this->hasMany(TipeKursus::class, 'bentuk_pelatihan_id');
    }

    public function riwayatPelatihan(): HasMany
    {
        return $this->hasMany(RiwayatPelatihan::class, 'bentuk_pelatihan_id');
    }
}
