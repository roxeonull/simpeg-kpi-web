<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CutiApprovalStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'cuti_id', 'urutan', 'tipe_step', 'status',
        'pemroses_user_id', 'catatan', 'diproses_pada',
    ];

    protected function casts(): array
    {
        return [
            'diproses_pada' => 'datetime',
        ];
    }

    public function cuti(): BelongsTo
    {
        return $this->belongsTo(Cuti::class, 'cuti_id');
    }

    public function pemrosesUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemroses_user_id');
    }

    public function tipeStepLabel(): string
    {
        return match ($this->tipe_step) {
            'atasan_langsung' => 'Atasan Langsung',
            'hr_admin' => 'HR / Admin',
            default => ucfirst(str_replace('_', ' ', $this->tipe_step)),
        };
    }
}
