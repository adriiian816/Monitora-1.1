<?php

namespace Tests\Feature;

use App\Models\Opd;
use App\Models\Submission;
use App\Models\Tugas;
use App\Models\TugasOpd;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppBotTest extends TestCase
{
    use RefreshDatabase;

    protected Opd $opd;

    protected User $staffUser;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->admin()->create([
            'email' => 'admin.test@bappeda.malutprov.go.id',
        ]);

        $this->opd = Opd::create([
            'kode_opd' => 'BAPPEDA',
            'nama_opd' => 'Bappeda Provinsi Maluku Utara',
            'nama_pic' => 'Faisal K.',
            'whatsapp_pic' => '08111222333',
            'is_active' => true,
        ]);

        $this->staffUser = User::factory()->opd($this->opd->id, '081234567890')->create([
            'name' => 'Staf Perencanaan',
            'email' => 'staf.perencanaan@malutprov.go.id',
        ]);
    }

    public function test_webhook_returns_422_when_phone_is_missing(): void
    {
        $response = $this->postJson(route('api.whatsapp.webhook'), [
            'message' => 'Halo',
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('status', 'error');
    }

    public function test_webhook_returns_unregistered_reply_for_unknown_phone(): void
    {
        $response = $this->postJson(route('api.whatsapp.webhook'), [
            'phone' => '089999999999',
            'message' => 'Halo bot',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('is_registered', false);
        $this->assertStringContainsString('belum terdaftar', (string) $response->json('reply'));
    }

    public function test_webhook_returns_menu_for_registered_staff(): void
    {
        // Test with international suffix @s.whatsapp.net
        $response = $this->postJson(route('api.whatsapp.webhook'), [
            'phone' => '6281234567890@s.whatsapp.net',
            'message' => 'MENU',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('is_registered', true);
        $response->assertJsonPath('user', 'Staf Perencanaan');
        $response->assertJsonPath('opd', 'Bappeda Provinsi Maluku Utara');
        $this->assertStringContainsString('ASISTEN BOT MONITORA', (string) $response->json('reply'));
        $this->assertStringContainsString('1️⃣ *1* atau *TUGAS*', (string) $response->json('reply'));
    }

    public function test_webhook_returns_task_list(): void
    {
        $tugas = Tugas::create([
            'admin_id' => $this->adminUser->id,
            'judul' => 'Laporan Evaluasi Renja Triwulan III',
            'deskripsi' => 'Pengumpulan laporan Renja.',
            'format_pengumpulan' => 'digital',
            'deadline' => now()->addDays(3),
            'tahun_anggaran' => 2026,
            'status' => 'aktif',
        ]);

        TugasOpd::create([
            'tugas_id' => $tugas->id,
            'opd_id' => $this->opd->id,
            'status' => 'belum_dikumpulkan',
        ]);

        $response = $this->postJson(route('api.whatsapp.webhook'), [
            'phone' => '081234567890',
            'message' => '1',
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('DAFTAR TUGAS AKTIF', (string) $response->json('reply'));
        $this->assertStringContainsString('Laporan Evaluasi Renja Triwulan III', (string) $response->json('reply'));
    }

    public function test_webhook_returns_verification_status(): void
    {
        $tugas = Tugas::create([
            'admin_id' => $this->adminUser->id,
            'judul' => 'Dokumen RKPD 2027',
            'deskripsi' => 'Pengumpulan RKPD.',
            'format_pengumpulan' => 'digital',
            'deadline' => now()->addDays(5),
            'tahun_anggaran' => 2026,
            'status' => 'aktif',
        ]);

        $tugasOpd = TugasOpd::create([
            'tugas_id' => $tugas->id,
            'opd_id' => $this->opd->id,
            'status' => 'diterima',
        ]);

        Submission::create([
            'tugas_opd_id' => $tugasOpd->id,
            'file_path' => 'submissions/rkpd_2027.pdf',
            'file_original_name' => 'rkpd_2027.pdf',
            'file_size' => 1024,
            'status_verifikasi' => 'diterima',
            'catatan_admin' => 'Dokumen lengkap dan valid.',
            'verified_at' => now(),
            'verified_by' => $this->adminUser->id,
        ]);

        $response = $this->postJson(route('api.whatsapp.webhook'), [
            'phone' => '081234567890',
            'message' => 'STATUS',
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('STATUS VERIFIKASI PENGUMPULAN TERAKHIR', (string) $response->json('reply'));
        $this->assertStringContainsString('Dokumen RKPD 2027', (string) $response->json('reply'));
        $this->assertStringContainsString('DITERIMA', (string) $response->json('reply'));
    }

    public function test_whatsapp_service_dispatches_to_local_bot(): void
    {
        Http::fake([
            'http://127.0.0.1:3000/send-message' => Http::response(['status' => 'sent'], 200),
        ]);

        config([
            'services.whatsapp.provider' => 'local_bot',
            'services.whatsapp.bot_url' => 'http://127.0.0.1:3000/send-message',
        ]);

        $service = new WhatsAppService;
        $log = $service->send('081234567890', 'Pesan uji coba bot.');

        $this->assertEquals('terkirim', $log->status);
        Http::assertSent(function ($request) {
            return $request->url() === 'http://127.0.0.1:3000/send-message'
                && $request['phone'] === '6281234567890'
                && $request['message'] === 'Pesan uji coba bot.';
        });
    }

    public function test_whatsapp_service_handles_queued_message_with_anti_spam_delay(): void
    {
        Http::fake([
            'http://127.0.0.1:3000/send-message' => Http::response([
                'status' => 'success',
                'code' => 'MESSAGE_QUEUED',
                'target' => '6281234567890',
                'queue_id' => 'MSG-12345',
                'queue_position' => 1,
                'min_delay_sec' => 30,
                'max_delay_sec' => 60,
                'message' => 'Nomor 6281234567890 terverifikasi aktif di WhatsApp. Pesan berhasil dimasukkan ke antrean dengan jeda acak 30-60 detik.',
            ], 200),
        ]);

        config([
            'services.whatsapp.provider' => 'local_bot',
            'services.whatsapp.bot_url' => 'http://127.0.0.1:3000/send-message',
        ]);

        $service = new WhatsAppService;
        $log = $service->send('081234567890', 'Pesan pengingat tugas.');

        $this->assertEquals('terkirim', $log->status);
        $this->assertEquals('MESSAGE_QUEUED', $log->response_payload['code']);
        $this->assertEquals(30, $log->response_payload['min_delay_sec']);
        $this->assertEquals(60, $log->response_payload['max_delay_sec']);
    }

    public function test_whatsapp_service_handles_unregistered_whatsapp_number(): void
    {
        Http::fake([
            'http://127.0.0.1:3000/send-message' => Http::response([
                'status' => 'error',
                'code' => 'NUMBER_NOT_REGISTERED',
                'target' => '628999999999',
                'message' => 'Nomor 628999999999 tidak terdaftar di WhatsApp. Pengiriman dibatalkan untuk menghindari pembatasan / banned WhatsApp.',
            ], 422),
        ]);

        config([
            'services.whatsapp.provider' => 'local_bot',
            'services.whatsapp.bot_url' => 'http://127.0.0.1:3000/send-message',
        ]);

        $service = new WhatsAppService;
        $log = $service->send('089999999999', 'Pesan ke nomor fiktif.');

        $this->assertEquals('gagal', $log->status);
        $this->assertEquals('NUMBER_NOT_REGISTERED', $log->response_payload['code']);
        $this->assertStringContainsString('tidak terdaftar di WhatsApp', $log->response_payload['message']);
    }

    public function test_whatsapp_service_checks_number_availability(): void
    {
        Http::fake([
            'http://127.0.0.1:3000/check-number' => Http::response([
                'status' => 'success',
                'target' => '6281234567890',
                'registered' => true,
                'message' => 'Nomor 6281234567890 terdaftar aktif di WhatsApp.',
            ], 200),
        ]);

        config([
            'services.whatsapp.provider' => 'local_bot',
            'services.whatsapp.bot_url' => 'http://127.0.0.1:3000/send-message',
        ]);

        $service = new WhatsAppService;
        $check = $service->checkNumberAvailability('081234567890');

        $this->assertTrue($check['success']);
        $this->assertTrue($check['registered']);
        $this->assertStringContainsString('terdaftar aktif di WhatsApp', $check['message']);
    }
}
