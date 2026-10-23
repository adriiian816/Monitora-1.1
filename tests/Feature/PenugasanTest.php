<?php

namespace Tests\Feature;

use App\Models\Penugasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PenugasanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $anggota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@bappeda.malutprov.go.id',
            'role' => 'admin',
        ]);

        $this->anggota = User::factory()->create([
            'name' => 'Fahri Staf Ekop',
            'email' => 'fahri@bappeda.malutprov.go.id',
            'role' => 'admin',
            'bidang' => 'ekop',
            'no_hp' => '081234567890',
        ]);
    }

    public function test_admin_can_access_penugasan_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penugasan.index'));

        $response->assertStatus(200);
        $response->assertSee('Penugasan Bidang Ekop');
        $response->assertSee('Tugas / Kegiatan');
        $response->assertSee('Penerima (Anggota Bidang)');
        $response->assertSee('Status Penugasan');
        $response->assertSee('Status Laporan');
    }

    public function test_admin_can_view_create_page_and_see_anggota(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penugasan.create'));

        $response->assertStatus(200);
        $response->assertSee('Buat Penugasan Anggota Bidang');
        $response->assertSee($this->anggota->name);
        $response->assertSee($this->anggota->no_hp);
    }

    public function test_admin_can_create_penugasan_with_recipients(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.penugasan.store'), [
            'judul' => 'Rakor Inflasi dan Perdagangan Daerah',
            'deskripsi' => 'Menghadiri rakor teknis komoditas pokok di Ternate.',
            'tanggal_kegiatan' => now()->addDays(2)->format('Y-m-d'),
            'batas_laporan' => now()->addDays(5)->format('Y-m-d'),
            'catatan' => 'Membawa bahan presentasi data inflasi',
            'penerima_ids' => [$this->anggota->id],
        ]);

        $this->assertDatabaseHas('penugasan', [
            'judul' => 'Rakor Inflasi dan Perdagangan Daerah',
            'pemberi_id' => $this->admin->id,
        ]);

        $penugasan = Penugasan::where('judul', 'Rakor Inflasi dan Perdagangan Daerah')->first();
        $this->assertNotNull($penugasan);

        $this->assertDatabaseHas('penugasan_penerima', [
            'penugasan_id' => $penugasan->id,
            'user_id' => $this->anggota->id,
            'status_penugasan' => 'belum_dikerjakan',
            'status_laporan' => 'belum_dilaporkan',
        ]);

        $response->assertRedirect(route('admin.penugasan.show', $penugasan));
    }

    public function test_admin_can_update_member_task_and_report_status(): void
    {
        $penugasan = Penugasan::create([
            'pemberi_id' => $this->admin->id,
            'judul' => 'Inspeksi Pasar Higienis Bahari Berkesan',
            'tanggal_kegiatan' => now()->format('Y-m-d'),
        ]);

        $penugasan->penerima()->attach($this->anggota->id, [
            'status_penugasan' => 'belum_dikerjakan',
            'status_laporan' => 'belum_dilaporkan',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.penugasan.update-status', $penugasan), [
            'penerima_id' => $this->anggota->id,
            'status_penugasan' => 'selesai',
            'status_laporan' => 'sudah_dilaporkan',
        ]);

        $response->assertRedirect(route('admin.penugasan.show', $penugasan));

        $this->assertDatabaseHas('penugasan_penerima', [
            'penugasan_id' => $penugasan->id,
            'user_id' => $this->anggota->id,
            'status_penugasan' => 'selesai',
            'status_laporan' => 'sudah_dilaporkan',
        ]);
    }

    public function test_admin_can_delete_penugasan(): void
    {
        $penugasan = Penugasan::create([
            'pemberi_id' => $this->admin->id,
            'judul' => 'Penugasan Sementara yang Dibatalkan',
            'tanggal_kegiatan' => now()->format('Y-m-d'),
        ]);

        $penugasan->penerima()->attach($this->anggota->id);

        $response = $this->actingAs($this->admin)->delete(route('admin.penugasan.destroy', $penugasan));

        $response->assertRedirect(route('admin.penugasan.index'));
        $this->assertDatabaseMissing('penugasan', ['id' => $penugasan->id]);
        $this->assertDatabaseMissing('penugasan_penerima', ['penugasan_id' => $penugasan->id]);
    }

    public function test_opd_cannot_access_penugasan(): void
    {
        $opdUser = User::factory()->create(['role' => 'opd']);

        $response = $this->actingAs($opdUser)->get(route('admin.penugasan.index'));
        $response->assertStatus(403);
    }

    public function test_user_can_update_no_hp_in_profile(): void
    {
        $response = $this->actingAs($this->anggota)->patch(route('profile.update'), [
            'name' => 'Fahri Staf Ekop Updated',
            'email' => 'fahri@bappeda.malutprov.go.id',
            'no_hp' => '089988776655',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'id' => $this->anggota->id,
            'no_hp' => '089988776655',
        ]);
    }

    public function test_super_admin_can_bulk_delete_users(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($superAdmin)->post(route('admin.users.bulk-delete'), [
            'user_ids' => [$user1->id, $user2->id],
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user1->id]);
        $this->assertDatabaseMissing('users', ['id' => $user2->id]);
    }

    public function test_anggota_bidang_is_redirected_to_bidang_dashboard_from_dashboard(): void
    {
        $anggotaUser = User::factory()->anggotaBidang()->create();

        $response = $this->actingAs($anggotaUser)->get(route('dashboard'));

        $response->assertRedirect(route('bidang.dashboard'));
    }

    public function test_anggota_bidang_can_view_bidang_dashboard_with_only_own_penugasan(): void
    {
        $anggotaUser = User::factory()->anggotaBidang()->create();
        $otherUser = User::factory()->anggotaBidang()->create();

        $penugasanSelf = Penugasan::create([
            'pemberi_id' => $this->admin->id,
            'judul' => 'Tugas Khusus Anggota Sendiri',
            'tanggal_kegiatan' => now()->format('Y-m-d'),
        ]);
        $penugasanSelf->penerima()->attach($anggotaUser->id, [
            'status_penugasan' => 'sedang_dikerjakan',
            'status_laporan' => 'belum_dilaporkan',
        ]);

        $penugasanOther = Penugasan::create([
            'pemberi_id' => $this->admin->id,
            'judul' => 'Tugas Anggota Lain Saja',
            'tanggal_kegiatan' => now()->format('Y-m-d'),
        ]);
        $penugasanOther->penerima()->attach($otherUser->id, [
            'status_penugasan' => 'belum_dikerjakan',
            'status_laporan' => 'belum_dilaporkan',
        ]);

        $response = $this->actingAs($anggotaUser)->get(route('bidang.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Tugas Khusus Anggota Sendiri');
        $response->assertDontSee('Tugas Anggota Lain Saja');
        // Ensure no OPD task monitoring or verification queue is shown
        $response->assertDontSee('Monitoring Tugas OPD');
        $response->assertDontSee('Menunggu Verifikasi');
    }

    public function test_anggota_bidang_cannot_access_admin_restricted_routes(): void
    {
        $anggotaUser = User::factory()->anggotaBidang()->create();

        // Cannot view admin dashboard
        $this->actingAs($anggotaUser)->get(route('admin.dashboard'))->assertStatus(403);

        // Cannot access OPD task creation/management
        $this->actingAs($anggotaUser)->get(route('admin.tugas.index'))->assertStatus(403);
        $this->actingAs($anggotaUser)->get(route('admin.tugas.create'))->assertStatus(403);

        // Cannot access WhatsApp logs
        $this->actingAs($anggotaUser)->get(route('admin.whatsapp-logs.index'))->assertStatus(403);

        // Cannot create internal penugasan
        $this->actingAs($anggotaUser)->get(route('admin.penugasan.create'))->assertStatus(403);
        $this->actingAs($anggotaUser)->post(route('admin.penugasan.store'), [
            'judul' => 'Coba Buat Penugasan',
            'tanggal_kegiatan' => now()->format('Y-m-d'),
            'penerima_ids' => [$anggotaUser->id],
        ])->assertStatus(403);
    }

    public function test_anggota_bidang_can_only_update_own_status_not_others(): void
    {
        $anggotaUser = User::factory()->anggotaBidang()->create();
        $otherAnggota = User::factory()->anggotaBidang()->create();

        $penugasan = Penugasan::create([
            'pemberi_id' => $this->admin->id,
            'judul' => 'Tugas Bersama Anggota Bidang',
            'tanggal_kegiatan' => now()->format('Y-m-d'),
        ]);
        $penugasan->penerima()->attach($anggotaUser->id, [
            'status_penugasan' => 'belum_dikerjakan',
            'status_laporan' => 'belum_dilaporkan',
        ]);
        $penugasan->penerima()->attach($otherAnggota->id, [
            'status_penugasan' => 'belum_dikerjakan',
            'status_laporan' => 'belum_dilaporkan',
        ]);

        // Anggota can update their own status
        $responseSelf = $this->actingAs($anggotaUser)->post(route('admin.penugasan.update-status', $penugasan), [
            'penerima_id' => $anggotaUser->id,
            'status_penugasan' => 'selesai',
            'status_laporan' => 'sudah_dilaporkan',
        ]);
        $responseSelf->assertRedirect(route('admin.penugasan.show', $penugasan));
        $this->assertDatabaseHas('penugasan_penerima', [
            'penugasan_id' => $penugasan->id,
            'user_id' => $anggotaUser->id,
            'status_penugasan' => 'selesai',
            'status_laporan' => 'sudah_dilaporkan',
        ]);

        // Anggota is FORBIDDEN to update another member's status
        $responseOther = $this->actingAs($anggotaUser)->post(route('admin.penugasan.update-status', $penugasan), [
            'penerima_id' => $otherAnggota->id,
            'status_penugasan' => 'selesai',
            'status_laporan' => 'sudah_dilaporkan',
        ]);
        $responseOther->assertStatus(403);
    }

    public function test_anggota_bidang_can_confirm_penugasan(): void
    {
        $anggotaUser = User::factory()->anggotaBidang()->create();

        $penugasan = Penugasan::create([
            'pemberi_id' => $this->admin->id,
            'judul' => 'Rakor Teknis Evaluasi RKPD',
            'tanggal_kegiatan' => now()->addDays(2)->format('Y-m-d'),
        ]);

        $penugasan->penerima()->attach($anggotaUser->id, [
            'status_penugasan' => 'belum_dikerjakan',
            'status_konfirmasi' => 'menunggu_konfirmasi',
            'status_laporan' => 'belum_dilaporkan',
        ]);

        $response = $this->actingAs($anggotaUser)->post(route('admin.penugasan.konfirmasi', $penugasan), [
            'status_konfirmasi' => 'dikonfirmasi',
            'catatan_konfirmasi' => 'Siap hadir dan melaksanakan tugas.',
        ]);

        $response->assertRedirect(route('admin.penugasan.show', $penugasan));

        $this->assertDatabaseHas('penugasan_penerima', [
            'penugasan_id' => $penugasan->id,
            'user_id' => $anggotaUser->id,
            'status_konfirmasi' => 'dikonfirmasi',
            'status_penugasan' => 'sedang_dikerjakan',
            'catatan_konfirmasi' => 'Siap hadir dan melaksanakan tugas.',
        ]);
    }

    public function test_anggota_bidang_can_decline_penugasan(): void
    {
        $anggotaUser = User::factory()->anggotaBidang()->create();

        $penugasan = Penugasan::create([
            'pemberi_id' => $this->admin->id,
            'judul' => 'Kunjungan Lapangan Tidore',
            'tanggal_kegiatan' => now()->addDays(3)->format('Y-m-d'),
        ]);

        $penugasan->penerima()->attach($anggotaUser->id, [
            'status_penugasan' => 'belum_dikerjakan',
            'status_konfirmasi' => 'menunggu_konfirmasi',
            'status_laporan' => 'belum_dilaporkan',
        ]);

        $response = $this->actingAs($anggotaUser)->post(route('admin.penugasan.konfirmasi', $penugasan), [
            'status_konfirmasi' => 'ditolak',
            'catatan_konfirmasi' => 'Berhalangan karena sedang dinas luar kota.',
        ]);

        $response->assertRedirect(route('admin.penugasan.show', $penugasan));

        $this->assertDatabaseHas('penugasan_penerima', [
            'penugasan_id' => $penugasan->id,
            'user_id' => $anggotaUser->id,
            'status_konfirmasi' => 'ditolak',
            'catatan_konfirmasi' => 'Berhalangan karena sedang dinas luar kota.',
        ]);
    }

    public function test_anggota_bidang_can_submit_report_and_mark_done(): void
    {
        Storage::fake('public');

        $anggotaUser = User::factory()->anggotaBidang()->create();

        $penugasan = Penugasan::create([
            'pemberi_id' => $this->admin->id,
            'judul' => 'Rapat Koordinasi Pengendalian Inflasi',
            'tanggal_kegiatan' => now()->format('Y-m-d'),
            'batas_laporan' => now()->addDays(3)->format('Y-m-d'),
        ]);

        $penugasan->penerima()->attach($anggotaUser->id, [
            'status_penugasan' => 'sedang_dikerjakan',
            'status_konfirmasi' => 'dikonfirmasi',
            'status_laporan' => 'belum_dilaporkan',
        ]);

        $fakeFile = UploadedFile::fake()->create('laporan_kegiatan_inflasi.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($anggotaUser)->post(route('admin.penugasan.submit-laporan', $penugasan), [
            'isi_laporan' => 'Telah mengikuti rapat inflasi. Harga bahan pokok terpantau stabil di Kota Ternate.',
            'link_dokumen' => 'https://drive.google.com/folder/dokumentasi',
            'file_laporan' => $fakeFile,
            'tandai_selesai' => '1',
        ]);

        $response->assertRedirect(route('admin.penugasan.show', $penugasan));

        $this->assertDatabaseHas('penugasan_penerima', [
            'penugasan_id' => $penugasan->id,
            'user_id' => $anggotaUser->id,
            'status_laporan' => 'sudah_dilaporkan',
            'status_penugasan' => 'selesai',
            'file_laporan_name' => 'laporan_kegiatan_inflasi.pdf',
            'isi_laporan' => 'Telah mengikuti rapat inflasi. Harga bahan pokok terpantau stabil di Kota Ternate.',
            'link_dokumen' => 'https://drive.google.com/folder/dokumentasi',
        ]);
    }

    public function test_anggota_bidang_can_download_submitted_report(): void
    {
        Storage::fake('public');

        $anggotaUser = User::factory()->anggotaBidang()->create();

        $penugasan = Penugasan::create([
            'pemberi_id' => $this->admin->id,
            'judul' => 'Laporan Monev Triwulan',
            'tanggal_kegiatan' => now()->format('Y-m-d'),
        ]);

        $fakeFile = UploadedFile::fake()->create('monev.pdf', 500, 'application/pdf');
        $storedPath = $fakeFile->store('penugasan-laporan', 'public');

        $penugasan->penerima()->attach($anggotaUser->id, [
            'status_penugasan' => 'selesai',
            'status_laporan' => 'sudah_dilaporkan',
            'file_laporan' => $storedPath,
            'file_laporan_name' => 'monev.pdf',
            'file_laporan_size' => 512000,
        ]);

        // Anggota can download their report
        $response = $this->actingAs($anggotaUser)->get(route('admin.penugasan.download-laporan', [$penugasan, $anggotaUser]));
        $response->assertStatus(200);

        // Admin can also download
        $adminResponse = $this->actingAs($this->admin)->get(route('admin.penugasan.download-laporan', [$penugasan, $anggotaUser]));
        $adminResponse->assertStatus(200);
    }

    public function test_anggota_bidang_can_submit_report_without_file(): void
    {
        $anggotaUser = User::factory()->anggotaBidang()->create();

        $penugasan = Penugasan::create([
            'pemberi_id' => $this->admin->id,
            'judul' => 'Rakor Tanpa Berkas File',
            'tanggal_kegiatan' => now()->format('Y-m-d'),
        ]);

        $penugasan->penerima()->attach($anggotaUser->id, [
            'status_penugasan' => 'sedang_dikerjakan',
            'status_konfirmasi' => 'dikonfirmasi',
            'status_laporan' => 'belum_dilaporkan',
        ]);

        // Simulating browser sending empty file input
        $emptyFile = new UploadedFile('', '', null, UPLOAD_ERR_NO_FILE, true);

        $response = $this->actingAs($anggotaUser)->post(route('admin.penugasan.submit-laporan', $penugasan), [
            'isi_laporan' => 'Kegiatan rakor berjalan lancar dan selesai pada pukul 12.00 WIT.',
            'link_dokumen' => 'drive.google.com/test-folder', // Missing https:// scheme should be auto-prefixed
            'file_laporan' => $emptyFile,
            'tandai_selesai' => '1',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.penugasan.show', $penugasan));

        $this->assertDatabaseHas('penugasan_penerima', [
            'penugasan_id' => $penugasan->id,
            'user_id' => $anggotaUser->id,
            'status_laporan' => 'sudah_dilaporkan',
            'status_penugasan' => 'selesai',
            'file_laporan' => null,
            'link_dokumen' => 'https://drive.google.com/test-folder',
        ]);
    }

    public function test_anggota_bidang_can_submit_report_with_file_only(): void
    {
        Storage::fake('public');

        $anggotaUser = User::factory()->anggotaBidang()->create();

        $penugasan = Penugasan::create([
            'pemberi_id' => $this->admin->id,
            'judul' => 'Unggah File Saja Tanpa Uraian Teks',
            'tanggal_kegiatan' => now()->format('Y-m-d'),
        ]);

        $penugasan->penerima()->attach($anggotaUser->id, [
            'status_penugasan' => 'sedang_dikerjakan',
            'status_konfirmasi' => 'dikonfirmasi',
            'status_laporan' => 'belum_dilaporkan',
        ]);

        $fakeFile = UploadedFile::fake()->create('laporan_resmi.pdf', 2048, 'application/pdf');

        $response = $this->actingAs($anggotaUser)->post(route('admin.penugasan.submit-laporan', $penugasan), [
            'isi_laporan' => '',
            'file_laporan' => $fakeFile,
            'tandai_selesai' => '1',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.penugasan.show', $penugasan));

        $this->assertDatabaseHas('penugasan_penerima', [
            'penugasan_id' => $penugasan->id,
            'user_id' => $anggotaUser->id,
            'status_laporan' => 'sudah_dilaporkan',
            'file_laporan_name' => 'laporan_resmi.pdf',
        ]);
    }

    public function test_admin_can_submit_report_on_behalf_of_anggota(): void
    {
        $anggotaUser = User::factory()->anggotaBidang()->create();

        $penugasan = Penugasan::create([
            'pemberi_id' => $this->admin->id,
            'judul' => 'Tugas Yang Diinput Admin',
            'tanggal_kegiatan' => now()->format('Y-m-d'),
        ]);

        $penugasan->penerima()->attach($anggotaUser->id, [
            'status_penugasan' => 'sedang_dikerjakan',
            'status_konfirmasi' => 'dikonfirmasi',
            'status_laporan' => 'belum_dilaporkan',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.penugasan.submit-laporan', $penugasan), [
            'penerima_id' => $anggotaUser->id,
            'isi_laporan' => 'Laporan fisik diserahkan langsung ke ruangan admin.',
            'tandai_selesai' => '1',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.penugasan.show', $penugasan));

        $this->assertDatabaseHas('penugasan_penerima', [
            'penugasan_id' => $penugasan->id,
            'user_id' => $anggotaUser->id,
            'status_laporan' => 'sudah_dilaporkan',
            'status_penugasan' => 'selesai',
            'isi_laporan' => 'Laporan fisik diserahkan langsung ke ruangan admin.',
        ]);
    }
}
