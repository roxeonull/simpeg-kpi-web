<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Seed nilai default pengaturan jam kerja baru.
     * Menggunakan INSERT IGNORE agar tidak menimpa nilai yang sudah dikustomisasi Admin.
     */
    public function up(): void
    {
        $defaults = [
            // Grup Jam Kerja Normal
            ['key' => 'jam_awal_absen',             'value' => '05:00'],
            ['key' => 'jam_masuk_standar',           'value' => '08:30'],
            ['key' => 'jam_batas_telat',             'value' => '10:00'],
            ['key' => 'jam_batas_alpa',              'value' => '16:00'],
            ['key' => 'jam_pulang_standar',          'value' => '16:00'],
            ['key' => 'jam_pulang_minimal_flexibel', 'value' => '15:30'], // batas bawah flexible
            ['key' => 'flexible_work_hours_enabled', 'value' => '1'],

            // Grup Jam Kerja Shift
            ['key' => 'toleransi_awal_shift_menit',  'value' => '60'],
            ['key' => 'toleransi_telat_shift_menit', 'value' => '30'],
        ];

        foreach ($defaults as $item) {
            DB::table('pengaturans')->insertOrIgnore([
                'key'        => $item['key'],
                'value'      => $item['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        $keys = [
            'jam_awal_absen',
            'jam_masuk_standar',
            'jam_batas_telat',
            'jam_batas_alpa',
            'jam_pulang_standar',
            'jam_pulang_minimal_flexibel',
            'flexible_work_hours_enabled',
            'toleransi_awal_shift_menit',
            'toleransi_telat_shift_menit',
        ];

        DB::table('pengaturans')->whereIn('key', $keys)->delete();
    }
};
