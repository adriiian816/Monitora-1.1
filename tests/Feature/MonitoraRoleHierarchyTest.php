<?php

namespace Tests\Feature;

use App\Models\Opd;
use App\Models\Submission;
use App\Models\Tugas;
use App\Models\TugasOpd;
use App\Models\User;
use App\Models\WhatsappLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoraRoleHierarchyTest extends TestCase
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
        ]);

        $this->admin = User::factory()->admin()->create([
            'name' => 'Admin Perencanaan Bappeda',
            'email' => 'admin@bappeda.malutprov.go.id',
        ]);

        $this->admin->handledOpds()->attach($this->opd->id);

        $this->opdUser = User::factory()->opd($this->opd->id, '081234567890')->create([
            'name' => 'Staf Disperindag',
            'email' => 'staff.disperindag@malutprov.go.id',
        ]);
    }

    public function test_super_admin_has_full_access_to_dashboard_opd_and_users(): void
    {
        // 1. Dashboard
        $response = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Monitoring');
        $response->assertSee('Kelola Pengguna');

        // 2. OPD Master Management
        $response = $this->actingAs($this->superAdmin)->get(route('admin.opd.index'));
        $response->assertStatus(200);
        $response->assertSee('Master Data Instansi OPD');

        // 3. User Management
        $response = $this->actingAs($this->superAdmin)->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna');
    }

    public function test_super_admin_can_create_admin_and_opd_staff_accounts(): void
    {
        // Create Admin Bappeda account with handled OPDs
        $responseAdmin = $this->actingAs($this->superAdmin)->post(route('admin.users.store'), [
            'name' => 'Admin Baru',
            'email' => 'admin.baru@bappeda.malutprov.go.id',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',
            'handled_opd_ids' => [$this->opd->id],
        ]);

        $responseAdmin->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'admin.baru@bappeda.malutprov.go.id',
            'role' => 'admin',
            'opd_id' => null,
        ]);

        $newAdminUser = User::where('email', 'admin.baru@bappeda.malutprov.go.id')->first();
        $this->assertDatabaseHas('pic_opd', [
            'user_id' => $newAdminUser->id,
            'opd_id' => $this->opd->id,
        ]);

        // Create Staf OPD account with no_hp
        $responseOpd = $this->actingAs($this->superAdmin)->post(route('admin.users.store'), [
            'name' => 'Staf OPD Baru',
            'email' => 'staf.opd.baru@malutprov.go.id',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'opd',
            'opd_id' => $this->opd->id,
            'no_hp' => '081288887777',
        ]);

        $responseOpd->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'staf.opd.baru@malutprov.go.id',
            'role' => 'opd',
            'opd_id' => $this->opd->id,
            'no_hp' => '081288887777',
        ]);
    }

    public function test_super_admin_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->superAdmin)->delete(route('admin.users.destroy', $this->superAdmin));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', [
            'id' => $this->superAdmin->id,
        ]);
    }

    public function test_admin_bappeda_can_create_task_and_verify_submission(): void
    {
        // 1. Create Task
        $response = $this->actingAs($this->admin)->post(route('admin.tugas.store'), [
            'judul' => 'Laporan Renja 2027',
            'deskripsi' => 'Pengumpulan laporan Renja',
            'format_pengumpulan' => 'digital',
            'deadline' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'tahun_anggaran' => '2027',
            'opd_ids' => [$this->opd->id],
        ]);

        $tugas = Tugas::where('judul', 'Laporan Renja 2027')->first();
        $this->assertNotNull($tugas);
        $response->assertRedirect(route('admin.tugas.show', $tugas));

        // 2. Verify Submission
        $tugasOpd = TugasOpd::firstOrCreate([
            'tugas_id' => $tugas->id,
            'opd_id' => $this->opd->id,
        ], ['status' => 'sudah_dikumpulkan']);

        $submission = Submission::create([
            'tugas_opd_id' => $tugasOpd->id,
            'file_path' => 'submissions/sample.pdf',
            'file_original_name' => 'sample.pdf',
            'file_size' => 1024,
            'status_verifikasi' => 'menunggu_verifikasi',
        ]);

        $verifyResponse = $this->actingAs($this->admin)->post(route('admin.submissions.verify', $submission), [
            'status_verifikasi' => 'diterima',
            'catatan_admin' => 'Dokumen sesuai standar.',
        ]);

        $verifyResponse->assertRedirect(route('admin.tugas.show', $tugas));
        $this->assertDatabaseHas('submissions', [
            'id' => $submission->id,
            'status_verifikasi' => 'diterima',
        ]);
    }

    public function test_admin_bappeda_is_forbidden_from_managing_opd(): void
    {
        // View OPD list
        $responseIndex = $this->actingAs($this->admin)->get(route('admin.opd.index'));
        $responseIndex->assertStatus(403);

        // Access OPD create page
        $responseCreate = $this->actingAs($this->admin)->get(route('admin.opd.create'));
        $responseCreate->assertStatus(403);

        // Post new OPD
        $responseStore = $this->actingAs($this->admin)->post(route('admin.opd.store'), [
            'kode_opd' => 'DISPAR',
            'nama_opd' => 'Dinas Pariwisata',
            'nama_pic' => 'Fikri',
            'whatsapp_pic' => '081234567891',
        ]);
        $responseStore->assertStatus(403);

        // Delete OPD
        $responseDelete = $this->actingAs($this->admin)->delete(route('admin.opd.destroy', $this->opd));
        $responseDelete->assertStatus(403);
    }

    public function test_admin_bappeda_is_forbidden_from_managing_users(): void
    {
        // View Users list
        $responseIndex = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $responseIndex->assertStatus(403);

        // Access User create page
        $responseCreate = $this->actingAs($this->admin)->get(route('admin.users.create'));
        $responseCreate->assertStatus(403);

        // Post new User
        $responseStore = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Akun Ilegal',
            'email' => 'ilegal@bappeda.malutprov.go.id',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',
        ]);
        $responseStore->assertStatus(403);

        // Delete User
        $responseDelete = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->opdUser));
        $responseDelete->assertStatus(403);
    }

    public function test_opd_staff_is_forbidden_from_all_admin_routes(): void
    {
        $this->actingAs($this->opdUser)->get(route('admin.dashboard'))->assertStatus(403);
        $this->actingAs($this->opdUser)->get(route('admin.tugas.index'))->assertStatus(403);
        $this->actingAs($this->opdUser)->get(route('admin.opd.index'))->assertStatus(403);
        $this->actingAs($this->opdUser)->get(route('admin.users.index'))->assertStatus(403);
    }

    public function test_opd_staff_can_view_dashboard_and_update_own_profile(): void
    {
        // View OPD dashboard
        $response = $this->actingAs($this->opdUser)->get(route('opd.dashboard'));
        $response->assertStatus(200);

        // Update own profile
        $updateResponse = $this->actingAs($this->opdUser)->patch(route('profile.update'), [
            'name' => 'Nama Staf Baru',
            'email' => 'staff.disperindag@malutprov.go.id',
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'id' => $this->opdUser->id,
            'name' => 'Nama Staf Baru',
        ]);
    }

    public function test_quick_login_buttons_are_removed_and_super_admin_can_login(): void
    {
        // 1. Verify 1-click demo buttons are no longer present on login page
        $loginPage = $this->get(route('login'));
        $loginPage->assertStatus(200);
        $loginPage->assertDontSee('Pilihan Akun Uji Coba Cepat');
        $loginPage->assertDontSee('superadmin@bappeda.malutprov.go.id');

        // 2. Perform normal login post
        $loginPost = $this->post(route('login'), [
            'email' => 'superadmin@bappeda.malutprov.go.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($this->superAdmin);
        $loginPost->assertRedirect(route('dashboard', absolute: false));

        // 3. Central dashboard redirects to admin.dashboard
        $dashResponse = $this->actingAs($this->superAdmin)->get(route('dashboard'));
        $dashResponse->assertRedirect(route('admin.dashboard'));
    }

    public function test_super_admin_can_update_pic_handled_opds(): void
    {
        $otherOpd = Opd::create([
            'kode_opd' => 'DKP',
            'nama_opd' => 'Dinas Kelautan dan Perikanan',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->put(route('admin.users.update', $this->admin), [
            'name' => 'Admin Perencanaan Updated',
            'email' => $this->admin->email,
            'role' => 'admin',
            'handled_opd_ids' => [$otherOpd->id],
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertFalse($this->admin->fresh()->handlesOpd($this->opd->id));
        $this->assertTrue($this->admin->fresh()->handlesOpd($otherOpd->id));
    }

    public function test_pic_only_sees_and_creates_tasks_for_handled_opds(): void
    {
        $otherOpd = Opd::create([
            'kode_opd' => 'DISTAN',
            'nama_opd' => 'Dinas Pertanian',
            'is_active' => true,
        ]);

        // 1. PIC create task view only shows handled OPD
        $createResponse = $this->actingAs($this->admin)->get(route('admin.tugas.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee($this->opd->nama_opd);
        $createResponse->assertDontSee($otherOpd->nama_opd);

        // 2. PIC cannot submit a task targeting an unhandled OPD
        $invalidStore = $this->actingAs($this->admin)->post(route('admin.tugas.store'), [
            'judul' => 'Tugas Ilegal',
            'deskripsi' => 'Deskripsi',
            'format_pengumpulan' => 'digital',
            'deadline' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'tahun_anggaran' => '2027',
            'opd_ids' => [$otherOpd->id],
        ]);

        $invalidStore->assertSessionHasErrors('opd_ids.0');

        // 3. PIC cannot view a task that does not target any of their handled OPDs
        $unhandledTask = Tugas::create([
            'admin_id' => $this->superAdmin->id,
            'judul' => 'Tugas Khusus Distan',
            'deskripsi' => 'Deskripsi',
            'format_pengumpulan' => 'digital',
            'deadline' => now()->addDays(3),
            'tahun_anggaran' => '2027',
            'status' => 'aktif',
        ]);
        TugasOpd::create([
            'tugas_id' => $unhandledTask->id,
            'opd_id' => $otherOpd->id,
            'status' => 'belum_dikumpulkan',
        ]);

        $showResponse = $this->actingAs($this->admin)->get(route('admin.tugas.show', $unhandledTask));
        $showResponse->assertStatus(403);
    }

    public function test_pic_cannot_view_verify_or_download_unhandled_opd_submissions(): void
    {
        $otherOpd = Opd::create([
            'kode_opd' => 'BPKAD',
            'nama_opd' => 'Badan Pengelolaan Keuangan',
            'is_active' => true,
        ]);

        $task = Tugas::create([
            'admin_id' => $this->superAdmin->id,
            'judul' => 'Tugas BPKAD',
            'deskripsi' => 'Deskripsi',
            'format_pengumpulan' => 'digital',
            'deadline' => now()->addDays(3),
            'tahun_anggaran' => '2027',
            'status' => 'aktif',
        ]);

        $tugasOpd = TugasOpd::create([
            'tugas_id' => $task->id,
            'opd_id' => $otherOpd->id,
            'status' => 'sudah_dikumpulkan',
        ]);

        $submission = Submission::create([
            'tugas_opd_id' => $tugasOpd->id,
            'file_path' => 'submissions/sample.pdf',
            'file_original_name' => 'bpkad.pdf',
            'file_size' => 1024,
            'status_verifikasi' => 'menunggu_verifikasi',
        ]);

        // PIC forbidden to view submission
        $this->actingAs($this->admin)
            ->get(route('admin.submissions.show', $submission))
            ->assertStatus(403);

        // PIC forbidden to verify submission
        $this->actingAs($this->admin)
            ->post(route('admin.submissions.verify', $submission), [
                'status_verifikasi' => 'diterima',
            ])
            ->assertStatus(403);

        // PIC forbidden to download file
        $this->actingAs($this->admin)
            ->get(route('admin.submissions.download', $submission))
            ->assertStatus(403);
    }

    public function test_pic_dashboard_and_logs_scoped_to_handled_opds(): void
    {
        $otherOpd = Opd::create([
            'kode_opd' => 'DISKOPUKM',
            'nama_opd' => 'Dinas Koperasi dan UKM',
            'is_active' => true,
        ]);

        // Dashboard for PIC shows 1 handled OPD, not all 2 OPDs
        $dashResponse = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertViewHas('totalOpd', 1);

        // WhatsApp log for other OPD
        $logOther = WhatsappLog::create([
            'opd_id' => $otherOpd->id,
            'tipe_pengingat' => 'pembuatan_tugas',
            'no_tujuan' => '081299990000',
            'isi_pesan' => 'Pesan untuk dinas lain',
            'status' => 'terkirim',
            'sent_at' => now(),
        ]);

        // WhatsApp log index for PIC does not show log for other OPD
        $logIndexResponse = $this->actingAs($this->admin)->get(route('admin.whatsapp-logs.index'));
        $logIndexResponse->assertStatus(200);
        $logIndexResponse->assertDontSee('Pesan untuk dinas lain');

        // Resend log for other OPD is forbidden
        $this->actingAs($this->admin)
            ->post(route('admin.whatsapp-logs.resend', $logOther))
            ->assertStatus(403);
    }
}
