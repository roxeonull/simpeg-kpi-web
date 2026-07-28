<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisKursus extends Model
{
    use HasFactory;

    protected $table = 'jenis_kursuses';

    protected $fillable = ['nama_jenis'];

    public function riwayatPelatihan(): HasMany
    {
        return $this->hasMany(RiwayatPelatihan::class, 'jenis_kursus_id');
    }
}
