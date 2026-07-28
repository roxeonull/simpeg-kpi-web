<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('shift', ['1', '2', '3']);
            $table->string('stasiun_tv', 100)->nullable();
            $table->foreignId('status_shift_id')->nullable()->constrained('status_shifts')->onDelete('set null');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['pegawai_id', 'tanggal', 'shift']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_shifts');
    }
};
