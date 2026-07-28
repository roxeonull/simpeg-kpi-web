<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->string('warna', 20)->default('#e5e7eb');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_shifts');
    }
};
