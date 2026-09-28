<?php

namespace Tests\Feature;

use App\Models\Opd;
use App\Models\Submission;
use App\Models\Tugas;
use App\Models\TugasOpd;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoraAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected Opd $opd;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@bappeda.malutprov.go.id',
            'role' => 'super_admin',
        ]);

        $this->admin = User::factory()->create([
            'email' => 'admin@bappeda.malutprov.go.id',
            'role' => 'admin',
        ]);

        $this->opd = Opd::create([
            'kode_opd' => 'DISPERINDAG',
            'nama_opd' => 'Dinas Perindustrian dan Perdagangan',
            'is_active' => true,
        ]);

        User::factory()->opd($this->opd->id, '081234567890')->create([
            'name' => 'Ahmad Hi. Saleh',
            'email' => 'disperindag@malutprov.go.id',
        ]);

        $this->admin->handledOpds()->attach($this->opd->id);
    }

    public function test_admin_can_access_dashboard_and_see_metrics(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard BAPPEDA Maluku Utara');
        $response->assertSee('Total OPD');
    }

    public function test_super_admin_can_create_new_opd(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.opd.store'), [
            'kode_opd' => 'DKP',
            'nama_opd' => 'Dinas Kelautan dan Perikanan',
            'alamat' => 'Jl. Kompleks Perkantoran Pemerintah Provinsi, Sofifi',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.opd.index'));
        $this->assertDatabaseHas('opds', [
            'kode_opd' => 'DKP',
            'nama_opd' => 'Dinas Kelautan dan Perikanan',
        ]);
    }

    public function test_super_admin_can_create_staff_user_with_phone_number(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.users.store'), [
            'name' => 'Staf Baru Disperindag',
            'email' => 'staf.baru@malutprov.go.id',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'opd',
            'opd_id' => $this->opd->id,
            'no_hp' => '081299998888',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'staf.baru@malutprov.go.id',
            'role' => 'opd',
            'opd_id' => $this->opd->id,
            'no_hp' => '081299998888',
        ]);
    }

    public function test_admin_cannot_create_new_opd(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.opd.store'), [
            'kode_opd' => 'DKP2',
            'nama_opd' => 'Dinas Terlarang',
            'is_active' => '1',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_create_task_and_distribute_to_opd(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.tugas.store'), [
            'judul' => 'Penyusunan Renja TA 2027',
            'deskripsi' => 'Kumpulkan dokumen Renja.',
            'format_pengumpulan' => 'digital',
            'deadline' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'tahun_anggaran' => '2027',
            'opd_ids' => [$this->opd->id],
            'send_wa' => '1',
        ]);

        $tugas = Tugas::where('judul', 'Penyusunan Renja TA 2027')->first();
        $this->assertNotNull($tugas);

        $response->assertRedirect(route('admin.tugas.show', $tugas));

        $this->assertDatabaseHas('tugas_opd', [
            'tugas_id' => $tugas->id,
            'opd_id' => $this->opd->id,
            'status' => 'belum_dikumpulkan',
        ]);

        // WhatsApp log created
        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $tugas->id,
            'opd_id' => $this->opd->id,
            'tipe_pengingat' => 'pembuatan_tugas',
        ]);
    }

    public function test_admin_can_create_task_and_notify_specific_staff(): void
    {
        $specificStaff = User::factory()->opd($this->opd->id, '081255556666')->create([
            'name' => 'Staf Khusus',
            'email' => 'staf.khusus@malutprov.go.id',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.tugas.store'), [
            'judul' => 'Penyusunan Renja TA 2027 Khusus',
            'deskripsi' => 'Kumpulkan dokumen Renja.',
            'format_pengumpulan' => 'digital',
            'deadline' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'tahun_anggaran' => '2027',
            'opd_ids' => [$this->opd->id],
            'staff_ids' => [$specificStaff->id],
            'send_wa' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('whatsapp_logs', [
            'no_tujuan' => '6281255556666',
            'tipe_pengingat' => 'pembuatan_tugas',
        ]);
    }

    public function test_admin_can_verify_submission(): void
    {
        $tugas = Tugas::create([
            'admin_id' => $this->admin->id,
            'judul' => 'Laporan TW3',
            'format_pengumpulan' => 'digital',
            'deadline' => now()->addDays(2),
            'tahun_anggaran' => '2026',
            'status' => 'aktif',
        ]);

        $tugasOpd = TugasOpd::create([
            'tugas_id' => $tugas->id,
            'opd_id' => $this->opd->id,
            'status' => 'sudah_dikumpulkan',
        ]);

        $submission = Submission::create([
            'tugas_opd_id' => $tugasOpd->id,
            'file_path' => 'submissions/sample.pdf',
            'file_original_name' => 'sample.pdf',
            'file_size' => 1024,
            'status_verifikasi' => 'menunggu_verifikasi',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.submissions.verify', $submission), [
            'status_verifikasi' => 'diterima',
            'catatan_admin' => 'Dokumen lengkap dan valid.',
            'send_wa_notification' => '1',
        ]);

        $response->assertRedirect(route('admin.tugas.show', $tugas));

        $this->assertDatabaseHas('submissions', [
            'id' => $submission->id,
            'status_verifikasi' => 'diterima',
            'catatan_admin' => 'Dokumen lengkap dan valid.',
        ]);

        $this->assertDatabaseHas('tugas_opd', [
            'id' => $tugasOpd->id,
            'status' => 'diterima',
        ]);

        // Verification WA log created
        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $tugas->id,
            'opd_id' => $this->opd->id,
            'tipe_pengingat' => 'verifikasi',
        ]);
    }

    public function test_admin_can_download_ics_calendar(): void
    {
        $tugas = Tugas::create([
            'admin_id' => $this->admin->id,
            'judul' => 'Pengumpulan Renja 2027',
            'format_pengumpulan' => 'digital',
            'deadline' => now()->addDays(7),
            'tahun_anggaran' => '2027',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.tugas.ics', $tugas));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
        $this->assertStringContainsString('BEGIN:VCALENDAR', $response->getContent());
        $this->assertStringContainsString('Pengumpulan Renja 2027', $response->getContent());
    }

    public function test_super_admin_can_delete_opd_permanently(): void
    {
        // Attach user to OPD
        $opdUser = User::factory()->create([
            'email' => 'staff.disperindag@malutprov.go.id',
            'role' => 'opd',
            'opd_id' => $this->opd->id,
        ]);

        // Attach task to OPD
        $tugas = Tugas::create([
            'admin_id' => $this->admin->id,
            'judul' => 'Tugas Uji Hapus',
            'format_pengumpulan' => 'digital',
            'deadline' => now()->addDays(3),
            'tahun_anggaran' => '2026',
            'status' => 'aktif',
        ]);

        $tugasOpd = TugasOpd::create([
            'tugas_id' => $tugas->id,
            'opd_id' => $this->opd->id,
            'status' => 'sudah_dikumpulkan',
        ]);

        $submission = Submission::create([
            'tugas_opd_id' => $tugasOpd->id,
            'file_original_name' => 'dokumen_uji.pdf',
            'status_verifikasi' => 'menunggu_verifikasi',
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('admin.opd.destroy', $this->opd));

        $response->assertRedirect(route('admin.opd.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('opds', [
            'id' => $this->opd->id,
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $opdUser->id,
        ]);

        $this->assertDatabaseMissing('tugas_opd', [
            'id' => $tugasOpd->id,
        ]);

        $this->assertDatabaseMissing('submissions', [
            'id' => $submission->id,
        ]);
    }

    public function test_ui_shows_staff_phone_and_task_staff_options(): void
    {
        // 1. Check user create view contains no_hp field
        $responseUserCreate = $this->actingAs($this->superAdmin)->get(route('admin.users.create'));
        $responseUserCreate->assertStatus(200);
        $responseUserCreate->assertSee('Nomor WhatsApp / HP Staf OPD');
        $responseUserCreate->assertSee('name="no_hp"', false);

        // 2. Check opd create view does NOT contain PIC or WhatsApp fields
        $responseOpdCreate = $this->actingAs($this->superAdmin)->get(route('admin.opd.create'));
        $responseOpdCreate->assertStatus(200);
        $responseOpdCreate->assertDontSee('Nama Penanggung Jawab (PIC)');
        $responseOpdCreate->assertDontSee('Nomor WhatsApp PIC');

        // 3. Check opd index view shows Staf OPD summary
        $responseOpdIndex = $this->actingAs($this->superAdmin)->get(route('admin.opd.index'));
        $responseOpdIndex->assertStatus(200);
        $responseOpdIndex->assertSee('Staf OPD & Nomor WhatsApp', false);

        // 4. Check tugas create view shows staff selection options
        $responseTugasCreate = $this->actingAs($this->admin)->get(route('admin.tugas.create'));
        $responseTugasCreate->assertStatus(200);
        $responseTugasCreate->assertSee('Target Instansi OPD & Staf Penerima Notifikasi', false);
        $responseTugasCreate->assertSee('Centang Semua Staf OPD Ini');
        $responseTugasCreate->assertSee('name="staff_ids[]"', false);
    }
}
