<?php

namespace Tests\Feature;

use App\Models\LoginLog;
use App\Models\User;
use App\Models\WhatsappLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurgeOldLogsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_purges_login_and_whatsapp_logs_older_than_30_days(): void
    {
        $user = User::factory()->create();

        // 1. Old logs (> 30 days)
        $oldLoginLog = LoginLog::factory()->create([
            'user_id' => $user->id,
            'logged_at' => now()->subDays(35),
            'created_at' => now()->subDays(35),
        ]);

        $oldWaLog = WhatsappLog::create([
            'tipe_pengingat' => 'manual',
            'no_tujuan' => '628123456789',
            'isi_pesan' => 'Pesan lama',
            'status' => 'terkirim',
            'sent_at' => now()->subDays(35),
        ]);
        $oldWaLog->created_at = now()->subDays(35);
        $oldWaLog->save();

        // 2. Recent logs (< 30 days)
        $recentLoginLog = LoginLog::factory()->create([
            'user_id' => $user->id,
            'logged_at' => now()->subDays(10),
            'created_at' => now()->subDays(10),
        ]);

        $recentWaLog = WhatsappLog::create([
            'tipe_pengingat' => 'manual',
            'no_tujuan' => '628123456789',
            'isi_pesan' => 'Pesan baru',
            'status' => 'terkirim',
            'sent_at' => now()->subDays(10),
            'created_at' => now()->subDays(10),
        ]);

        // Run command
        $this->artisan('monitora:purge-logs')
            ->expectsOutputToContain('Pembersihan log selesai')
            ->assertExitCode(0);

        // Verify old logs deleted
        $this->assertDatabaseMissing('login_logs', ['id' => $oldLoginLog->id]);
        $this->assertDatabaseMissing('whatsapp_logs', ['id' => $oldWaLog->id]);

        // Verify recent logs retained
        $this->assertDatabaseHas('login_logs', ['id' => $recentLoginLog->id]);
        $this->assertDatabaseHas('whatsapp_logs', ['id' => $recentWaLog->id]);
    }

    public function test_command_supports_custom_days_option(): void
    {
        $user = User::factory()->create();

        $log10Days = LoginLog::factory()->create([
            'user_id' => $user->id,
            'logged_at' => now()->subDays(10),
            'created_at' => now()->subDays(10),
        ]);

        $log3Days = LoginLog::factory()->create([
            'user_id' => $user->id,
            'logged_at' => now()->subDays(3),
            'created_at' => now()->subDays(3),
        ]);

        // Purge logs older than 7 days
        $this->artisan('monitora:purge-logs --days=7')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('login_logs', ['id' => $log10Days->id]);
        $this->assertDatabaseHas('login_logs', ['id' => $log3Days->id]);
    }

    public function test_models_support_laravel_model_prune(): void
    {
        $user = User::factory()->create();

        $oldLogin = LoginLog::factory()->create([
            'user_id' => $user->id,
            'logged_at' => now()->subDays(31),
            'created_at' => now()->subDays(31),
        ]);

        $this->artisan('model:prune', ['--model' => [LoginLog::class]])
            ->assertExitCode(0);

        $this->assertDatabaseMissing('login_logs', ['id' => $oldLogin->id]);
    }
}
