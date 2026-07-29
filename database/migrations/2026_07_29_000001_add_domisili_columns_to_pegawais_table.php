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
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('koordinat_domisili')->nullable()->after('kode_pos');
            $table->decimal('lat_domisili', 10, 8)->nullable()->after('koordinat_domisili');
            $table->decimal('lng_domisili', 11, 8)->nullable()->after('lat_domisili');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn(['koordinat_domisili', 'lat_domisili', 'lng_domisili']);
        });
    }
};
