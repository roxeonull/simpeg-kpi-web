<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add column to cutis
        Schema::table('cutis', function (Blueprint $table) {
            $table->foreignId('jenis_cuti_id')->nullable()->after('jenis_cuti')->constrained('jenis_cutis')->nullOnDelete();
        });

        // 2. Add column to absensis
        Schema::table('absensis', function (Blueprint $table) {
            $table->foreignId('jenis_ketidakhadiran_id')->nullable()->after('status')->constrained('jenis_ketidakhadirans')->nullOnDelete();
        });

        // 3. Migrate legacy cuti data
        $jenisCutiMap = [
            'tahunan' => 'Cuti Tahunan',
            'sakit' => 'Sakit/Cuti Sakit',
            'melahirkan' => 'Cuti Bersalin Anak Ke-1 s.d 2',
            'lainnya' => 'Cuti Alasan Penting'
        ];

        foreach ($jenisCutiMap as $oldKey => $newNama) {
            $jenisCuti = DB::table('jenis_cutis')->where('nama', $newNama)->first();
            if ($jenisCuti) {
                DB::table('cutis')
                    ->where('jenis_cuti', $oldKey)
                    ->update(['jenis_cuti_id' => $jenisCuti->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropForeign(['jenis_ketidakhadiran_id']);
            $table->dropColumn('jenis_ketidakhadiran_id');
        });

        Schema::table('cutis', function (Blueprint $table) {
            $table->dropForeign(['jenis_cuti_id']);
            $table->dropColumn('jenis_cuti_id');
        });
    }
};
