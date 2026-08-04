<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Pegawai;
use App\Models\User;
use App\Models\JenisCuti;
use App\Models\ApprovalWorkflow;
use App\Models\CutiApprovalStep;

class Cuti extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id', 'jenis_cuti', 'jenis_cuti_id', 'tanggal_mulai', 'tanggal_selesai', 'jumlah_hari',
        'alasan', 'alamat_cuti', 'lampiran', 'status_atasan', 'catatan_atasan', 'atasan_pemroses_id',
        'atasan_diproses_pada', 'status_hr', 'catatan_hr', 'hr_pemroses_id',
        'hr_diproses_pada', 'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'atasan_diproses_pada' => 'datetime',
            'hr_diproses_pada' => 'datetime',
        ];
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function atasanPemroses(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atasan_pemroses_id');
    }

    public function hrPemroses(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_pemroses_id');
    }

    public function jenisCuti(): BelongsTo
    {
        return $this->belongsTo(JenisCuti::class, 'jenis_cuti_id');
    }

    public function approvalSteps(): HasMany
    {
        return $this->hasMany(CutiApprovalStep::class, 'cuti_id')->orderBy('urutan');
    }

    public function activeStep(): ?CutiApprovalStep
    {
        $hasRejected = $this->approvalSteps()->where('status', 'ditolak')->exists();
        if ($hasRejected) {
            return null;
        }

        return $this->approvalSteps()->where('status', 'menunggu')->orderBy('urutan')->first();
    }

    public function generateApprovalSteps(): void
    {
        if ($this->approvalSteps()->count() > 0) {
            return;
        }

        $unitId = $this->pegawai?->unit_id;
        $workflow = null;
        if ($unitId) {
            $workflow = ApprovalWorkflow::with('steps')->where('unit_id', $unitId)->first();
        }

        if ($workflow && $workflow->steps->count() > 0) {
            foreach ($workflow->steps as $step) {
                $this->approvalSteps()->create([
                    'urutan' => $step->urutan,
                    'tipe_step' => $step->tipe_step,
                    'status' => 'menunggu',
                ]);
            }
        } else {
            $this->approvalSteps()->createMany([
                ['urutan' => 1, 'tipe_step' => 'atasan_langsung', 'status' => 'menunggu'],
                ['urutan' => 2, 'tipe_step' => 'hr_admin', 'status' => 'menunggu'],
            ]);
        }

        $firstStep = $this->approvalSteps()->orderBy('urutan')->first();
        if ($firstStep) {
            $initStatus = match ($firstStep->tipe_step) {
                'atasan_langsung' => 'menunggu_atasan',
                'hr_admin' => 'menunggu_hr',
                default => 'menunggu',
            };
            $this->updateQuietly(['status' => $initStatus]);
        }
    }

    public function canUserApproveActiveStep(User $user): bool
    {
        $step = $this->activeStep();
        if (!$step) {
            return false;
        }

        // Pegawai tidak boleh menyetujui / menolak pengajuan cutinya sendiri
        if ($user->pegawai && (int) $this->pegawai_id === (int) $user->pegawai->id) {
            return false;
        }

        if ($step->tipe_step === 'atasan_langsung') {
            if ($user->role === 'admin') return true;
            if ($user->role === 'atasan' && $user->pegawai) {
                $timIds = $user->pegawai->anggotaTim()->pluck('id')->toArray();
                return in_array($this->pegawai_id, $timIds, true) || ($this->pegawai?->atasan_id === $user->pegawai->id);
            }
            return false;
        }

        if ($step->tipe_step === 'hr_admin') {
            return $user->role === 'admin';
        }

        return false;
    }

    public function prosesActiveStep(User $user, string $action, ?string $catatan = null): void
    {
        $step = $this->activeStep();
        if (!$step) {
            // Fallback for legacy calls if no step found
            if ($action === 'disetujui') {
                $this->setujuiAtasanLegacy($user, $catatan);
            } else {
                $this->tolakAtasanLegacy($user, $catatan);
            }
            return;
        }

        $step->update([
            'status' => $action,
            'pemroses_user_id' => $user->id,
            'catatan' => $catatan,
            'diproses_pada' => now(),
        ]);

        if ($step->tipe_step === 'atasan_langsung') {
            $this->status_atasan = $action;
            $this->catatan_atasan = $catatan;
            $this->atasan_pemroses_id = $user->id;
            $this->atasan_diproses_pada = now();
        } elseif ($step->tipe_step === 'hr_admin') {
            $this->status_hr = $action;
            $this->catatan_hr = $catatan;
            $this->hr_pemroses_id = $user->id;
            $this->hr_diproses_pada = now();
        }

        if ($action === 'ditolak') {
            $this->status = 'ditolak';
            $this->save();

            if ($this->pegawai?->user) {
                $jenisStr = $this->jenisCuti?->nama ?? ucfirst($this->jenis_cuti);
                $tipeLabel = $step->tipeStepLabel();
                \App\Services\NotificationService::sendToUser(
                    $this->pegawai->user,
                    "Cuti Ditolak ({$tipeLabel})",
                    "Pengajuan cuti {$jenisStr} Anda ditolak pada tahap {$tipeLabel}.",
                    ['type' => 'cuti', 'id' => (string) $this->id]
                );
            }
        } elseif ($action === 'disetujui') {
            $nextStep = $this->approvalSteps()->where('status', 'menunggu')->orderBy('urutan')->first();
            if ($nextStep) {
                $this->status = match ($nextStep->tipe_step) {
                    'atasan_langsung' => 'menunggu_atasan',
                    'hr_admin' => 'menunggu_hr',
                    default => 'menunggu',
                };
                $this->save();

                if ($this->pegawai?->user) {
                    $jenisStr = $this->jenisCuti?->nama ?? ucfirst($this->jenis_cuti);
                    $tipeLabel = $step->tipeStepLabel();
                    \App\Services\NotificationService::sendToUser(
                        $this->pegawai->user,
                        "Cuti Disetujui Tahap {$step->urutan}",
                        "Pengajuan cuti {$jenisStr} Anda disetujui ({$tipeLabel}) dan melanjut ke tahap berikutnya.",
                        ['type' => 'cuti', 'id' => (string) $this->id]
                    );
                }
            } else {
                $this->status = 'disetujui';
                $this->save();

                $saldo = $this->pegawai?->saldoCutiTahunIni();
                $potong = $this->jenisCuti ? $this->jenisCuti->potong_saldo_cuti : ($this->jenis_cuti === 'tahunan');
                if ($saldo && $potong) {
                    $saldo->decrement('sisa_saldo', $this->jumlah_hari);
                }

                if ($this->pegawai?->user) {
                    $jenisStr = $this->jenisCuti?->nama ?? ucfirst($this->jenis_cuti);
                    \App\Services\NotificationService::sendToUser(
                        $this->pegawai->user,
                        'Cuti Disetujui (Final)',
                        "Pengajuan cuti {$jenisStr} Anda telah disetujui secara final.",
                        ['type' => 'cuti', 'id' => (string) $this->id]
                    );
                }
            }
        }
    }

    protected static function booted(): void
    {
        static::created(function (Cuti $cuti) {
            $cuti->generateApprovalSteps();
        });

        static::saving(function (Cuti $cuti) {
            if (!$cuti->jenis_cuti_id && $cuti->jenis_cuti) {
                $map = [
                    'tahunan' => 'Cuti Tahunan',
                    'sakit' => 'Sakit/Cuti Sakit',
                    'melahirkan' => 'Cuti Bersalin Anak Ke-1 s.d 2',
                    'lainnya' => 'Cuti Alasan Penting',
                ];
                $targetNama = $map[$cuti->jenis_cuti] ?? null;
                if ($targetNama) {
                    $jc = JenisCuti::where('nama', $targetNama)->first();
                    if ($jc) {
                        $cuti->jenis_cuti_id = $jc->id;
                    }
                }
            } elseif ($cuti->jenis_cuti_id && !$cuti->jenis_cuti) {
                $jc = $cuti->jenisCuti ?: JenisCuti::find($cuti->jenis_cuti_id);
                if ($jc) {
                    $nameLower = strtolower($jc->nama);
                    if (str_contains($nameLower, 'sakit')) {
                        $cuti->jenis_cuti = 'sakit';
                    } elseif (str_contains($nameLower, 'melahirkan') || str_contains($nameLower, 'bersalin')) {
                        $cuti->jenis_cuti = 'melahirkan';
                    } elseif (str_contains($nameLower, 'tahunan')) {
                        $cuti->jenis_cuti = 'tahunan';
                    } else {
                        $cuti->jenis_cuti = 'lainnya';
                    }
                }
            }
        });
    }

    public function setujuiAtasan(User $user, ?string $catatan = null): void
    {
        $this->prosesActiveStep($user, 'disetujui', $catatan);
    }

    public function tolakAtasan(User $user, ?string $catatan = null): void
    {
        $this->prosesActiveStep($user, 'ditolak', $catatan);
    }

    public function setujuiHr(User $user, ?string $catatan = null): void
    {
        $this->prosesActiveStep($user, 'disetujui', $catatan);
    }

    public function tolakHr(User $user, ?string $catatan = null): void
    {
        $this->prosesActiveStep($user, 'ditolak', $catatan);
    }

    private function setujuiAtasanLegacy(User $user, ?string $catatan = null): void
    {
        $this->update([
            'status_atasan' => 'disetujui',
            'catatan_atasan' => $catatan,
            'atasan_pemroses_id' => $user->id,
            'atasan_diproses_pada' => now(),
            'status' => 'menunggu_hr',
        ]);
    }

    private function tolakAtasanLegacy(User $user, ?string $catatan = null): void
    {
        $this->update([
            'status_atasan' => 'ditolak',
            'catatan_atasan' => $catatan,
            'atasan_pemroses_id' => $user->id,
            'atasan_diproses_pada' => now(),
            'status' => 'ditolak',
        ]);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'menunggu_atasan' => 'Menunggu Atasan',
            'menunggu_hr' => 'Menunggu HR',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            default => $this->status,
        };
    }
}
