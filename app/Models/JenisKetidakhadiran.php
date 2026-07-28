<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisKetidakhadiran extends Model
{
    use HasFactory;

    protected $table = 'jenis_ketidakhadirans';

    protected $fillable = ['nama'];

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'jenis_ketidakhadiran_id');
    }
}
