<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class CutiFeatureTest extends TestCase
{
    /**
     * Memastikan Admin dapat mengakses semua sub-halaman Cuti & Izin.
     */
    public function test_admin_can_access_cuti_pages(): void
    {
        $admin = User::where('email', 'admin@kpi.go.id')->first();
        $this->assertNotNull($admin, 'User admin demo tidak ditemukan di database.');

        $response = $this->actingAs($admin)->get('/cuti');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/cuti/kalender');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/cuti/analitik');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/cuti/rekomendasi');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/cuti/workflows');
        $response->assertStatus(200);
    }

    /**
     * Memastikan Atasan dapat mengakses semua sub-halaman Cuti & Izin kecuali Workflows.
     */
    public function test_atasan_can_access_cuti_pages(): void
    {
        $atasan = User::where('email', 'atasan@kpi.go.id')->first();
        $this->assertNotNull($atasan, 'User atasan demo tidak ditemukan di database.');

        $response = $this->actingAs($atasan)->get('/cuti');
        $response->assertStatus(200);

        $response = $this->actingAs($atasan)->get('/cuti/kalender');
        $response->assertStatus(200);

        $response = $this->actingAs($atasan)->get('/cuti/analitik');
        $response->assertStatus(200);

        $response = $this->actingAs($atasan)->get('/cuti/rekomendasi');
        $response->assertStatus(200);

        $response = $this->actingAs($atasan)->get('/cuti/workflows');
        $response->assertStatus(403);
    }
}
