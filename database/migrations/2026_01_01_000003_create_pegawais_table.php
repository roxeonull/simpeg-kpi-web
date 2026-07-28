<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nip', 30)->unique();
            $table->string('nama');
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatans')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('unit_kerjas')->nullOnDelete();
            $table->foreignId('atasan_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->enum('status_kepegawaian', ['PNS', 'PPPK', 'Non-ASN'])->default('PNS');
            $table->date('tmt')->nullable();
            $table->string('foto')->nullable();
            $table->string('email')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_ktp', 20)->nullable();
            $table->string('file_ktp')->nullable();
            $table->string('file_sk')->nullable();
            $table->enum('status_aktif', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
