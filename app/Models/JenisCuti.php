<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisCuti extends Model
{
    use HasFactory;

    protected $table = 'jenis_cutis';

    protected $fillable = ['nama', 'potong_saldo_cuti'];

    protected $casts = [
        'potong_saldo_cuti' => 'boolean',
    ];

    public function cuti(): HasMany
    {
        return $this->hasMany(Cuti::class, 'jenis_cuti_id');
    }
}
