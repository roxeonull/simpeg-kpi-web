<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bentuk_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bentuk');
            $table->timestamps();
        });

        Schema::create('tipe_kursuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bentuk_pelatihan_id')->constrained('bentuk_pelatihans')->onDelete('cascade');
            $table->string('nama_tipe');
            $table->timestamps();
        });

        Schema::create('jenis_kursuses', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis');
            $table->timestamps();
        });

        Schema::create('instansis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instansi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instansis');
        Schema::dropIfExists('jenis_kursuses');
        Schema::dropIfExists('tipe_kursuses');
        Schema::dropIfExists('bentuk_pelatihans');
    }
};
