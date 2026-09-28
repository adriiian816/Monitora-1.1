<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_login_ignores_and_cleans_expired_or_pre_boot_sessions(): void
    {
        $user = User::factory()->create();

        // Simulasikan sesi kadaluarsa atau sesi sebelum server dihidupkan
        DB::table('sessions')->insert([
            'id' => 'old_expired_session_123',
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0',
            'payload' => serialize(['some' => 'data']),
            'last_activity' => now()->subHours(5)->timestamp,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        // Pastikan sesi kadaluarsa telah dibersihkan
        $this->assertDatabaseMissing('sessions', [
            'id' => 'old_expired_session_123',
        ]);
    }

    public function test_active_concurrent_session_triggers_login_conflict(): void
    {
        $user = User::factory()->create();

        // Simulasikan sesi yang BENAR-BENAR masih aktif (baru 2 menit lalu)
        DB::table('sessions')->insert([
            'id' => 'active_session_456',
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0',
            'payload' => serialize(['some' => 'data']),
            'last_activity' => now()->subMinutes(2)->timestamp,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Harus diarahkan ke login.conflict karena sesi perangkat lain masih aktif
        $response->assertRedirect(route('login.conflict'));
    }

    public function test_sessions_clear_command_truncates_sessions_table(): void
    {
        $user = User::factory()->create();

        DB::table('sessions')->insert([
            'id' => 'some_session_789',
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0',
            'payload' => serialize(['some' => 'data']),
            'last_activity' => now()->timestamp,
        ]);

        $this->artisan('sessions:clear')->assertSuccessful();

        $this->assertDatabaseCount('sessions', 0);
    }
}
