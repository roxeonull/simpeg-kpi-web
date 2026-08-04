<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('unit_id')->nullable()->unique()->constrained('unit_kerjas')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('approval_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_workflow_id')->constrained('approval_workflows')->cascadeOnDelete();
            $table->integer('urutan')->default(1);
            $table->enum('tipe_step', ['atasan_langsung', 'hr_admin'])->default('atasan_langsung');
            $table->timestamps();
        });

        Schema::create('cuti_approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuti_id')->constrained('cutis')->cascadeOnDelete();
            $table->integer('urutan')->default(1);
            $table->enum('tipe_step', ['atasan_langsung', 'hr_admin'])->default('atasan_langsung');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->foreignId('pemroses_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamp('diproses_pada')->nullable();
            $table->timestamps();
        });

        // Retroactive migration for existing cutis
        $cutis = DB::table('cutis')->get();
        foreach ($cutis as $cuti) {
            // Step 1: Atasan Langsung
            DB::table('cuti_approval_steps')->insert([
                'cuti_id' => $cuti->id,
                'urutan' => 1,
                'tipe_step' => 'atasan_langsung',
                'status' => in_array($cuti->status_atasan, ['disetujui', 'ditolak', 'menunggu']) ? $cuti->status_atasan : 'menunggu',
                'pemroses_user_id' => $cuti->atasan_pemroses_id,
                'catatan' => $cuti->catatan_atasan,
                'diproses_pada' => $cuti->atasan_diproses_pada,
                'created_at' => $cuti->created_at ?? now(),
                'updated_at' => $cuti->updated_at ?? now(),
            ]);

            // Step 2: HR / Admin
            $statusHr = $cuti->status_hr;
            if (!in_array($statusHr, ['disetujui', 'ditolak', 'menunggu'])) {
                $statusHr = 'menunggu';
            }
            DB::table('cuti_approval_steps')->insert([
                'cuti_id' => $cuti->id,
                'urutan' => 2,
                'tipe_step' => 'hr_admin',
                'status' => $statusHr,
                'pemroses_user_id' => $cuti->hr_pemroses_id,
                'catatan' => $cuti->catatan_hr,
                'diproses_pada' => $cuti->hr_diproses_pada,
                'created_at' => $cuti->created_at ?? now(),
                'updated_at' => $cuti->updated_at ?? now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cuti_approval_steps');
        Schema::dropIfExists('approval_workflow_steps');
        Schema::dropIfExists('approval_workflows');
    }
};
