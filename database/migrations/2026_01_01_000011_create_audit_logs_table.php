<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('aksi'); // e.g. "menyetujui cuti", "verifikasi pelatihan"
            $table->string('model')->nullable(); // e.g. "Cuti", "RiwayatPelatihan"
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
