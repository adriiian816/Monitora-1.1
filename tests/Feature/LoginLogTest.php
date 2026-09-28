<?php

namespace Tests\Feature;

use App\Models\LoginLog;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $opdUser;

    protected Opd $opd;

    protected function setUp(): void
    {
        parent::setUp();

        $this->opd = Opd::create([
            'kode_opd' => 'DISPERINDAG',
            'nama_opd' => 'Dinas Perindustrian dan Perdagangan',
            'is_active' => true,
        ]);

        $this->superAdmin = User::factory()->superAdmin()->create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@bappeda.malutprov.go.id',
            'password' => bcrypt('password123'),
        ]);

        $this->admin = User::factory()->admin()->create([
            'name' => 'Admin Perencanaan Bappeda',
            'email' => 'admin@bappeda.malutprov.go.id',
            'password' => bcrypt('password123'),
        ]);

        $this->opdUser = User::factory()->opd($this->opd->id, '081234567890')->create([
            'name' => 'Staf Disperindag',
            'email' => 'staff.disperindag@malutprov.go.id',
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_guest_cannot_access_login_logs_page(): void
    {
        $response = $this->get(route('admin.login-logs.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_opd_user_cannot_access_login_logs_page(): void
    {
        $response = $this->actingAs($this->opdUser)->get(route('admin.login-logs.index'));

        $response->assertStatus(403);
    }

    public function test_admin_bappeda_cannot_access_login_logs_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.login-logs.index'));

        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_login_logs_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.login-logs.index'));

        $response->assertStatus(200);
        $response->assertSee('Log Sesi Pengguna OPD');
        $response->assertSee('Admin Only');
    }

    public function test_user_login_automatically_records_activity_log(): void
    {
        $response = $this->post(route('login'), [
            'email' => $this->opdUser->email,
            'password' => 'password123',
        ], [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('login_logs', [
            'user_id' => $this->opdUser->id,
            'opd_id' => $this->opd->id,
            'action' => 'login',
            'device_type' => 'desktop',
            'platform' => 'Windows 10/11',
        ]);
    }

    public function test_mobile_user_login_detects_mobile_device_and_platform(): void
    {
        $response = $this->post(route('login'), [
            'email' => $this->opdUser->email,
            'password' => 'password123',
        ], [
            'User-Agent' => 'Mozilla/5.0 (Linux; Android 14; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.6099.144 Mobile Safari/537.36',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('login_logs', [
            'user_id' => $this->opdUser->id,
            'device_type' => 'mobile',
            'platform' => 'Android 14',
        ]);
    }

    public function test_user_logout_automatically_records_logout_log(): void
    {
        $this->actingAs($this->opdUser);

        $response = $this->post(route('logout'), [], [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('login_logs', [
            'user_id' => $this->opdUser->id,
            'action' => 'logout',
        ]);
    }

    public function test_super_admin_can_filter_logs_by_opd_and_action(): void
    {
        $opd2 = Opd::create([
            'kode_opd' => 'DINKES',
            'nama_opd' => 'Dinas Kesehatan',
            'is_active' => true,
        ]);

        $opd2User = User::factory()->opd($opd2->id)->create([
            'name' => 'Staf Dinkes',
            'email' => 'staf.dinkes@malutprov.go.id',
        ]);

        LoginLog::factory()->create([
            'user_id' => $this->opdUser->id,
            'opd_id' => $this->opd->id,
            'action' => 'login',
        ]);

        LoginLog::factory()->create([
            'user_id' => $opd2User->id,
            'opd_id' => $opd2->id,
            'action' => 'logout',
        ]);

        // Filter by OPD 1
        $response = $this->actingAs($this->superAdmin)->get(route('admin.login-logs.index', [
            'opd_id' => $this->opd->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee($this->opdUser->name);
        $response->assertDontSee($opd2User->name);

        // Filter by action logout
        $response2 = $this->actingAs($this->superAdmin)->get(route('admin.login-logs.index', [
            'action' => 'logout',
        ]));

        $response2->assertStatus(200);
        $response2->assertSee($opd2User->name);
    }

    public function test_super_admin_can_delete_individual_log(): void
    {
        $log = LoginLog::factory()->create([
            'user_id' => $this->opdUser->id,
            'opd_id' => $this->opd->id,
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('admin.login-logs.destroy', $log));

        $response->assertRedirect();
        $this->assertDatabaseMissing('login_logs', ['id' => $log->id]);
    }

    public function test_super_admin_can_purge_old_logs(): void
    {
        $oldLog = LoginLog::factory()->create([
            'user_id' => $this->opdUser->id,
            'logged_at' => now()->subDays(40),
            'created_at' => now()->subDays(40),
        ]);

        $recentLog = LoginLog::factory()->create([
            'user_id' => $this->opdUser->id,
            'logged_at' => now()->subDays(5),
            'created_at' => now()->subDays(5),
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('admin.login-logs.purge'), [
            'days' => 30,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('login_logs', ['id' => $oldLog->id]);
        $this->assertDatabaseHas('login_logs', ['id' => $recentLog->id]);
    }
}
