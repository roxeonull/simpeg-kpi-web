<?php

namespace Database\Seeders;

use App\Models\StatusShift;
use Illuminate\Database\Seeder;

class StatusShiftSeeder extends Seeder
{
    public function run(): void
    {
        StatusShift::updateOrCreate(
            ['kode' => 'CB'],
            [
                'nama' => 'Cuti Bersama',
                'warna' => '#fca5a5',
            ]
        );
    }
}
