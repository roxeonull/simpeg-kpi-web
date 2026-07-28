<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'aksi', 'model', 'model_id', 'keterangan', 'created_at'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function catat(string $aksi, ?string $model = null, ?int $modelId = null, ?string $keterangan = null): void
    {
        static::create([
            'user_id' => auth()->id(),
            'aksi' => $aksi,
            'model' => $model,
            'model_id' => $modelId,
            'keterangan' => $keterangan,
            'created_at' => now(),
        ]);
    }
}
