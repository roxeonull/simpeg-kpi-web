<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalWorkflowStep extends Model
{
    use HasFactory;

    protected $fillable = ['approval_workflow_id', 'urutan', 'tipe_step'];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(ApprovalWorkflow::class, 'approval_workflow_id');
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
