<?php

namespace Tests\Feature;

use App\Models\Penugasan;
use App\Models\User;
use App\Models\WhatsappLog;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppAntiBannedAndRateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $anggota;

    protected Penugasan $penugasan;

    protected WhatsAppService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create([
            'name' => 'Admin Ekop Test',
            'no_hp' => '081234567890',
        ]);

        $this->anggota = User::factory()->create([
            'role' => 'admin',
            'bidang' => 'ekonomi',
            'name' => 'Fathur Staf Ekop',
            'no_hp' => '081298765432',
        ]);

        $this->penugasan = Penugasan::create([
            'pemberi_id' => $this->admin->id,
            'judul' => 'Rapat Koordinasi Evaluasi Inflasi Daerah',
            'deskripsi' => 'Menghadiri rapat TPID bertempat di Kantor Gubernur Maluku Utara',
            'tanggal_kegiatan' => Carbon::tomorrow()->toDateString(),
            'batas_laporan' => Carbon::tomorrow()->addDay()->toDateString(),
            'catatan' => 'Siapkan data agregat inflasi triwulan berjalan',
        ]);

        $this->service = app(WhatsAppService::class);
    }

    public function test_penugasan_notification_templates_are_varied_and_not_identical(): void
    {
        $messages = [];
        $hashes = [];

        // Generate 10 messages for the same penugasan & anggota
        for ($i = 0; $i < 10; $i++) {
            $msg = $this->service->generateVariedPenugasanMessage($this->penugasan, $this->anggota);
            $messages[] = $msg;
            $hashes[] = md5($msg);
        }

        // All 10 messages must have distinct hashes (not twin/identical)
        $uniqueHashes = array_unique($hashes);
        $this->assertCount(10, $uniqueHashes, 'Setiap pesan penugasan yang digenerate harus unik dan tidak boleh kembar.');

        // Verify each message contains essential elements and unique ref tag
        foreach ($messages as $msg) {
            $this->assertStringContainsString('Rapat Koordinasi Evaluasi Inflasi Daerah', $msg);
            $this->assertStringContainsString('Fathur Staf Ekop', $msg);
            $this->assertMatchesRegularExpression('/_Ref: #[A-F0-9]{6} • \d{2}:\d{2}:\d{2} WIT_/', $msg);
        }
    }

    public function test_rate_limit_allows_up_to_30_messages_per_hour_per_number(): void
    {
        $targetPhone = '081298765432';
        $cleanedPhone = $this->service->cleanPhoneNumber($targetPhone);

        // Pre-create 29 sent logs within the last hour
        for ($i = 1; $i <= 29; $i++) {
            WhatsappLog::create([
                'tugas_id' => null,
                'opd_id' => null,
                'no_tujuan' => $cleanedPhone,
                'isi_pesan' => "Pesan uji ke-{$i}",
                'tipe_pengingat' => 'penugasan_ekop',
                'status' => 'simulasi',
                'sent_at' => now()->subMinutes(rand(1, 50)),
            ]);
        }

        $this->assertEquals(29, $this->service->getHourlyCount($targetPhone));
        $this->assertFalse($this->service->isRateLimited($targetPhone));

        // Send the 30th message
        $log30 = $this->service->send($targetPhone, 'Pesan ke-30 (Mencapai batas)', null, null, 'penugasan_ekop');
        $this->assertNotNull($log30);
        $this->assertContains($log30->status, ['terkirim', 'simulasi']);
        $this->assertEquals(30, $this->service->getHourlyCount($targetPhone));

        // Now rate limited
        $this->assertTrue($this->service->isRateLimited($targetPhone));

        // The 31st message should be blocked with status 'dibatasi'
        $log31 = $this->service->send($targetPhone, 'Pesan ke-31 (Melebihi batas)', null, null, 'penugasan_ekop');
        $this->assertNotNull($log31);
        $this->assertEquals('dibatasi', $log31->status);
        $this->assertTrue($log31->response_payload['rate_limited']);
        $this->assertEquals(30, $log31->response_payload['hourly_count']);
        $this->assertStringContainsString('maksimal 30 pesan per jam', strtolower($log31->response_payload['message']));
    }

    public function test_rate_limit_does_not_block_different_phone_numbers(): void
    {
        $phoneA = '081298765432';
        $phoneB = '085211223344';
        $cleanedPhoneA = $this->service->cleanPhoneNumber($phoneA);

        // Fill phoneA with 30 logs in the last hour
        for ($i = 1; $i <= 30; $i++) {
            WhatsappLog::create([
                'tugas_id' => null,
                'opd_id' => null,
                'no_tujuan' => $cleanedPhoneA,
                'isi_pesan' => "Pesan ke-{$i}",
                'tipe_pengingat' => 'penugasan_ekop',
                'status' => 'terkirim',
                'sent_at' => now()->subMinutes(10),
            ]);
        }

        $this->assertTrue($this->service->isRateLimited($phoneA));
        $this->assertFalse($this->service->isRateLimited($phoneB));

        // Sending to phoneB must still succeed
        $logB = $this->service->send($phoneB, 'Halo untuk nomor B', null, null, 'penugasan_ekop');
        $this->assertNotEquals('dibatasi', $logB->status);
    }

    public function test_logs_older_than_one_hour_do_not_count_towards_hourly_rate_limit(): void
    {
        $targetPhone = '081298765432';
        $cleanedPhone = $this->service->cleanPhoneNumber($targetPhone);

        // Create 30 logs older than 1 hour (e.g. 70 minutes ago)
        for ($i = 1; $i <= 30; $i++) {
            WhatsappLog::create([
                'tugas_id' => null,
                'opd_id' => null,
                'no_tujuan' => $cleanedPhone,
                'isi_pesan' => "Pesan lampau ke-{$i}",
                'tipe_pengingat' => 'penugasan_ekop',
                'status' => 'terkirim',
                'sent_at' => now()->subMinutes(70),
            ]);
        }

        // Count in the last hour must be 0
        $this->assertEquals(0, $this->service->getHourlyCount($targetPhone));
        $this->assertFalse($this->service->isRateLimited($targetPhone));

        $log = $this->service->send($targetPhone, 'Pesan baru dalam jam baru', null, null, 'penugasan_ekop');
        $this->assertNotEquals('dibatasi', $log->status);
    }

    public function test_failed_or_already_rate_limited_logs_do_not_consume_hourly_quota(): void
    {
        $targetPhone = '081298765432';
        $cleanedPhone = $this->service->cleanPhoneNumber($targetPhone);

        // Create 30 failed or dibatasi logs
        for ($i = 1; $i <= 30; $i++) {
            WhatsappLog::create([
                'tugas_id' => null,
                'opd_id' => null,
                'no_tujuan' => $cleanedPhone,
                'isi_pesan' => "Gagal ke-{$i}",
                'tipe_pengingat' => 'penugasan_ekop',
                'status' => ($i % 2 === 0) ? 'gagal' : 'dibatasi',
                'sent_at' => now()->subMinutes(15),
            ]);
        }

        // None of these should count as consumed quota
        $this->assertEquals(0, $this->service->getHourlyCount($targetPhone));
        $this->assertFalse($this->service->isRateLimited($targetPhone));
    }
}
