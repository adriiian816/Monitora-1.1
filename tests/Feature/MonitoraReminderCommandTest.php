<?php

namespace Tests\Feature;

use App\Models\Opd;
use App\Models\Tugas;
use App\Models\TugasOpd;
use App\Models\User;
use App\Models\WhatsappLog;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoraReminderCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_detects_overdue_and_sends_warning(): void
    {
        $opd = Opd::create([
            'kode_opd' => 'DISTAN',
            'nama_opd' => 'Dinas Pertanian',
            'nama_pic' => 'Dr. Rizal',
            'whatsapp_pic' => '081234567803',
            'is_active' => true,
        ]);

        $tugas = Tugas::create([
            'judul' => 'Tugas Sudah Terlewat',
            'deadline' => now()->subHours(5), // Deadline in the past
            'format_pengumpulan' => 'digital',
            'tahun_anggaran' => '2026',
            'status' => 'aktif',
        ]);

        $to = TugasOpd::create([
            'tugas_id' => $tugas->id,
            'opd_id' => $opd->id,
            'status' => 'belum_dikumpulkan',
        ]);

        $this->artisan('monitora:check-reminders')
            ->expectsOutputToContain('ditandai TERLAMBAT')
            ->assertExitCode(0);

        $to->refresh();
        $this->assertEquals('terlambat', $to->status);

        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $tugas->id,
            'opd_id' => $opd->id,
            'tipe_pengingat' => 'keterlambatan',
        ]);
    }

    public function test_command_sends_approaching_deadline_reminder(): void
    {
        $opd = Opd::create([
            'kode_opd' => 'DISPAR',
            'nama_opd' => 'Dinas Pariwisata',
            'nama_pic' => 'Fikri',
            'whatsapp_pic' => '081234567805',
            'is_active' => true,
        ]);

        // Task with deadline exactly 7 days from now (start of day + 7 days)
        $tugas = Tugas::create([
            'judul' => 'Tugas Mendekati Deadline H-7',
            'deadline' => now()->copy()->startOfDay()->addDays(7)->setTime(23, 59, 0),
            'format_pengumpulan' => 'digital',
            'tahun_anggaran' => '2026',
            'status' => 'aktif',
        ]);

        TugasOpd::create([
            'tugas_id' => $tugas->id,
            'opd_id' => $opd->id,
            'status' => 'belum_dikumpulkan',
        ]);

        $this->artisan('monitora:check-reminders')
            ->assertExitCode(0);

        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $tugas->id,
            'opd_id' => $opd->id,
            'tipe_pengingat' => 'h-7',
        ]);
    }

    public function test_command_sends_reminders_to_staff_user_phone(): void
    {
        $opd = Opd::create([
            'kode_opd' => 'DISKOP',
            'nama_opd' => 'Dinas Koperasi',
            'is_active' => true,
        ]);

        User::factory()->opd($opd->id, '081277778888')->create();

        $tugas = Tugas::create([
            'judul' => 'Tugas Pengingat Staf',
            'deadline' => now()->copy()->startOfDay()->addDays(7)->setTime(23, 59, 0),
            'format_pengumpulan' => 'digital',
            'tahun_anggaran' => '2026',
            'status' => 'aktif',
        ]);

        TugasOpd::create([
            'tugas_id' => $tugas->id,
            'opd_id' => $opd->id,
            'status' => 'belum_dikumpulkan',
        ]);

        $this->artisan('monitora:check-reminders')
            ->assertExitCode(0);

        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $tugas->id,
            'opd_id' => $opd->id,
            'no_tujuan' => '6281277778888',
            'tipe_pengingat' => 'h-7',
        ]);
    }

    public function test_command_sends_h2_and_h14_reminders(): void
    {
        $opd = Opd::create([
            'kode_opd' => 'BAPPEDA_TEST',
            'nama_opd' => 'Bappeda Test',
            'nama_pic' => 'Admin Test',
            'whatsapp_pic' => '081234567899',
            'is_active' => true,
        ]);

        // Task with deadline 2 days from now
        $tugasH2 = Tugas::create([
            'judul' => 'Tugas Deadline H-2',
            'deadline' => now()->copy()->startOfDay()->addDays(2)->setTime(23, 59, 0),
            'format_pengumpulan' => 'digital',
            'tahun_anggaran' => '2026',
            'status' => 'aktif',
        ]);

        TugasOpd::create([
            'tugas_id' => $tugasH2->id,
            'opd_id' => $opd->id,
            'status' => 'belum_dikumpulkan',
        ]);

        // Task with deadline 14 days from now
        $tugasH14 = Tugas::create([
            'judul' => 'Tugas Deadline H-14',
            'deadline' => now()->copy()->startOfDay()->addDays(14)->setTime(23, 59, 0),
            'format_pengumpulan' => 'digital',
            'tahun_anggaran' => '2026',
            'status' => 'aktif',
        ]);

        TugasOpd::create([
            'tugas_id' => $tugasH14->id,
            'opd_id' => $opd->id,
            'status' => 'belum_dikumpulkan',
        ]);

        $this->artisan('monitora:check-reminders')->assertExitCode(0);

        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $tugasH2->id,
            'opd_id' => $opd->id,
            'tipe_pengingat' => 'h-2',
        ]);

        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $tugasH14->id,
            'opd_id' => $opd->id,
            'tipe_pengingat' => 'h-14',
        ]);
    }

    public function test_command_sends_h0_reminder_when_deadline_is_today(): void
    {
        $opd = Opd::create([
            'kode_opd' => 'DINKES',
            'nama_opd' => 'Dinas Kesehatan',
            'nama_pic' => 'Budi',
            'whatsapp_pic' => '081234567810',
            'is_active' => true,
        ]);

        // Task with deadline today in the evening
        $tugasH0 = Tugas::create([
            'judul' => 'Tugas Deadline Hari Ini',
            'deadline' => now()->copy()->setTime(23, 59, 0),
            'format_pengumpulan' => 'digital',
            'tahun_anggaran' => '2026',
            'status' => 'aktif',
        ]);

        TugasOpd::create([
            'tugas_id' => $tugasH0->id,
            'opd_id' => $opd->id,
            'status' => 'belum_dikumpulkan',
        ]);

        $this->artisan('monitora:check-reminders')->assertExitCode(0);

        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $tugasH0->id,
            'opd_id' => $opd->id,
            'tipe_pengingat' => 'h-0',
        ]);
    }

    public function test_command_escalates_overdue_warning_at_day_3_and_day_7(): void
    {
        $opd = Opd::create([
            'kode_opd' => 'PU_TEST',
            'nama_opd' => 'Dinas Pekerjaan Umum',
            'nama_pic' => 'Ir. Hasan',
            'whatsapp_pic' => '081234567820',
            'is_active' => true,
        ]);

        // Task overdue by 4 days
        $tugasOverdue4 = Tugas::create([
            'judul' => 'Tugas Terlambat 4 Hari',
            'deadline' => now()->subDays(4),
            'format_pengumpulan' => 'digital',
            'tahun_anggaran' => '2026',
            'status' => 'aktif',
        ]);

        TugasOpd::create([
            'tugas_id' => $tugasOverdue4->id,
            'opd_id' => $opd->id,
            'status' => 'belum_dikumpulkan',
        ]);

        // Task overdue by 8 days
        $tugasOverdue8 = Tugas::create([
            'judul' => 'Tugas Terlambat 8 Hari',
            'deadline' => now()->subDays(8),
            'format_pengumpulan' => 'digital',
            'tahun_anggaran' => '2026',
            'status' => 'aktif',
        ]);

        TugasOpd::create([
            'tugas_id' => $tugasOverdue8->id,
            'opd_id' => $opd->id,
            'status' => 'belum_dikumpulkan',
        ]);

        $this->artisan('monitora:check-reminders')->assertExitCode(0);

        // For tugasOverdue4: both initial keterlambatan and eskalasi_1 are triggered
        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $tugasOverdue4->id,
            'opd_id' => $opd->id,
            'tipe_pengingat' => 'keterlambatan',
        ]);

        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $tugasOverdue4->id,
            'opd_id' => $opd->id,
            'tipe_pengingat' => 'eskalasi_1',
        ]);

        // For tugasOverdue8: initial, eskalasi_1, and eskalasi_2 are triggered
        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $tugasOverdue8->id,
            'opd_id' => $opd->id,
            'tipe_pengingat' => 'eskalasi_2',
        ]);
    }

    public function test_command_sends_h0_sore_reminder_in_afternoon_if_h0_already_sent(): void
    {
        Carbon::setTestNow(now()->copy()->setTime(14, 0, 0));

        $opd = Opd::create([
            'kode_opd' => 'DINKES_SORE',
            'nama_opd' => 'Dinas Kesehatan Sore',
            'nama_pic' => 'Budi',
            'whatsapp_pic' => '081234567811',
            'is_active' => true,
        ]);

        $tugas = Tugas::create([
            'judul' => 'Tugas Deadline Sore Ini',
            'deadline' => now()->copy()->setTime(17, 0, 0),
            'format_pengumpulan' => 'digital',
            'tahun_anggaran' => '2026',
            'status' => 'aktif',
        ]);

        TugasOpd::create([
            'tugas_id' => $tugas->id,
            'opd_id' => $opd->id,
            'status' => 'belum_dikumpulkan',
        ]);

        // Simulate that morning h-0 was already sent today
        WhatsappLog::create([
            'tugas_id' => $tugas->id,
            'opd_id' => $opd->id,
            'tipe_pengingat' => 'h-0',
            'no_tujuan' => '6281234567811',
            'isi_pesan' => 'Pengingat pagi',
            'status' => 'terkirim',
            'sent_at' => now()->copy()->setTime(8, 0, 0),
        ]);

        $this->artisan('monitora:check-reminders')->assertExitCode(0);

        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $tugas->id,
            'opd_id' => $opd->id,
            'tipe_pengingat' => 'h-0-sore',
        ]);

        Carbon::setTestNow(); // reset
    }
}
