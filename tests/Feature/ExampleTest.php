<?php

namespace Tests\Feature;

use App\Models\Opd;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example for guest.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_guest_can_access_portal_page(): void
    {
        $response = $this->get('/portal');

        $response->assertStatus(200);
    }

    public function test_authenticated_admin_visiting_root_is_redirected_to_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/');

        $response->assertRedirect(route('dashboard'));

        $dashboardResponse = $this->actingAs($admin)->get(route('dashboard'));
        $dashboardResponse->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_opd_visiting_root_is_redirected_to_dashboard(): void
    {
        $opd = Opd::create([
            'kode_opd' => 'DISPERINDAG',
            'nama_opd' => 'Dinas Perindustrian dan Perdagangan',
            'nama_pic' => 'Ahmad Hi. Saleh',
            'whatsapp_pic' => '081234567890',
            'email_pic' => 'disperindag@malutprov.go.id',
            'is_active' => true,
        ]);
        $opdUser = User::factory()->opd($opd->id)->create();

        $response = $this->actingAs($opdUser)->get('/');

        $response->assertRedirect(route('dashboard'));

        $dashboardResponse = $this->actingAs($opdUser)->get(route('dashboard'));
        $dashboardResponse->assertRedirect(route('opd.dashboard'));
    }
}
