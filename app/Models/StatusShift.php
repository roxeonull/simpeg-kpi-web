<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusShift extends Model
{
    protected $fillable = ['kode', 'nama', 'warna'];

    public function jadwalShifts(): HasMany
    {
        return $this->hasMany(JadwalShift::class);
    }
}
