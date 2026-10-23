<?php

namespace Tests\Feature;

use App\Models\Opd;
use App\Models\Tugas;
use App\Models\TugasOpd;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MonitoraOpdTest extends TestCase
{
    use RefreshDatabase;

    protected User $opdUser;

    protected Opd $opd;

    protected Tugas $tugasDigital;

    protected Tugas $tugasHybrid;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->opd = Opd::create([
            'kode_opd' => 'DISPERINDAG',
            'nama_opd' => 'Dinas Perindustrian dan Perdagangan',
            'nama_pic' => 'Ahmad Hi. Saleh',
            'whatsapp_pic' => '081234567890',
            'email_pic' => 'disperindag@malutprov.go.id',
            'is_active' => true,
        ]);

        $this->opdUser = User::factory()->create([
            'name' => 'Ahmad (DISPERINDAG)',
            'email' => 'pic.disperindag@malutprov.go.id',
            'role' => 'opd',
            'opd_id' => $this->opd->id,
        ]);

        $this->tugasDigital = Tugas::create([
            'judul' => 'Tugas Format Digital',
            'deskripsi' => 'Upload file laporan',
            'format_pengumpulan' => 'digital',
            'deadline' => now()->addDays(5),
            'tahun_anggaran' => '2027',
            'status' => 'aktif',
        ]);

        TugasOpd::create([
            'tugas_id' => $this->tugasDigital->id,
            'opd_id' => $this->opd->id,
            'status' => 'belum_dikumpulkan',
        ]);

        $this->tugasHybrid = Tugas::create([
            'judul' => 'Tugas Format Hybrid',
            'deskripsi' => 'Upload dan serahkan berkas fisik',
            'format_pengumpulan' => 'hybrid',
            'deadline' => now()->addDays(3),
            'tahun_anggaran' => '2026',
            'status' => 'aktif',
        ]);

        TugasOpd::create([
            'tugas_id' => $this->tugasHybrid->id,
            'opd_id' => $this->opd->id,
            'status' => 'belum_dikumpulkan',
        ]);
    }

    public function test_opd_can_access_dashboard_and_tasks(): void
    {
        $response = $this->actingAs($this->opdUser)->get(route('opd.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dinas Perindustrian dan Perdagangan');
        $response->assertSee('Tugas Format Digital');
    }

    public function test_opd_can_view_task_detail(): void
    {
        $response = $this->actingAs($this->opdUser)->get(route('opd.tugas.show', $this->tugasDigital));

        $response->assertStatus(200);
        $response->assertSee('Tugas Format Digital');
        $response->assertSee('Upload file laporan');
    }

    public function test_opd_can_submit_digital_document(): void
    {
        $file = UploadedFile::fake()->create('laporan_renja.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->opdUser)->post(route('opd.submission.store', $this->tugasDigital), [
            'berkas_dokumen' => $file,
            'catatan_opd' => 'Pengumpulan berkas tahap 1',
        ]);

        $response->assertRedirect(route('opd.tugas.show', $this->tugasDigital));

        $this->assertDatabaseHas('tugas_opd', [
            'tugas_id' => $this->tugasDigital->id,
            'opd_id' => $this->opd->id,
            'status' => 'sudah_dikumpulkan',
        ]);

        $this->assertDatabaseHas('submissions', [
            'file_original_name' => 'laporan_renja.pdf',
            'catatan_opd' => 'Pengumpulan berkas tahap 1',
            'status_verifikasi' => 'menunggu_verifikasi',
        ]);
    }

    public function test_opd_can_submit_hybrid_document(): void
    {
        $file = UploadedFile::fake()->create('laporan_tw3.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->opdUser)->post(route('opd.submission.store', $this->tugasHybrid), [
            'berkas_dokumen' => $file,
            'konfirmasi_fisik' => '1',
            'nama_pengantar' => 'Fahri (Staf Disperindag)',
            'nomor_surat' => '050/88/DISPERINDAG/IX/2026',
            'tanggal_penyerahan_fisik' => now()->toDateString(),
            'catatan_opd' => 'Hardcopy berkas telah diantar',
        ]);

        $response->assertRedirect(route('opd.tugas.show', $this->tugasHybrid));

        $this->assertDatabaseHas('tugas_opd', [
            'tugas_id' => $this->tugasHybrid->id,
            'opd_id' => $this->opd->id,
            'status' => 'sudah_dikumpulkan',
        ]);

        $this->assertDatabaseHas('submissions', [
            'konfirmasi_fisik' => true,
            'nama_pengantar' => 'Fahri (Staf Disperindag)',
            'nomor_surat' => '050/88/DISPERINDAG/IX/2026',
        ]);
    }

    public function test_opd_cannot_access_admin_routes(): void
    {
        $response = $this->actingAs($this->opdUser)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_submission_sends_whatsapp_notification_to_pic_and_confirmation_to_opd(): void
    {
        $picBappeda = User::factory()->create([
            'name' => 'Faisal PIC Bappeda',
            'role' => 'admin',
            'no_hp' => '081299990001',
        ]);
        $this->opd->pics()->attach($picBappeda->id);

        $file = UploadedFile::fake()->create('laporan_akhir.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->opdUser)->post(route('opd.submission.store', $this->tugasDigital), [
            'berkas_dokumen' => $file,
            'catatan_opd' => 'Berkas laporan telah lengkap',
        ]);

        $response->assertRedirect(route('opd.tugas.show', $this->tugasDigital));

        // Assert WhatsApp notification was sent to PIC Bappeda
        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $this->tugasDigital->id,
            'opd_id' => $this->opd->id,
            'no_tujuan' => '6281299990001',
            'tipe_pengingat' => 'pengumpulan_tugas',
        ]);

        // Assert WhatsApp confirmation receipt was sent to OPD
        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $this->tugasDigital->id,
            'opd_id' => $this->opd->id,
            'no_tujuan' => '6281234567890',
            'tipe_pengingat' => 'konfirmasi_pengumpulan',
        ]);
    }

    public function test_super_admin_can_view_opd_index_with_metrics(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($superAdmin)->get(route('admin.opd.index'));

        $response->assertStatus(200);
        $response->assertSee('Master Data Instansi OPD');
        $response->assertSee('Total Instansi');
        $response->assertSee($this->opd->nama_opd);
    }

    public function test_super_admin_can_bulk_delete_opds(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $opd1 = Opd::create([
            'kode_opd' => 'OPD1',
            'nama_opd' => 'Dinas Testing Satu',
            'is_active' => true,
        ]);
        $opd2 = Opd::create([
            'kode_opd' => 'OPD2',
            'nama_opd' => 'Dinas Testing Dua',
            'is_active' => true,
        ]);

        $response = $this->actingAs($superAdmin)->post(route('admin.opd.bulk-delete'), [
            'opd_ids' => [$opd1->id, $opd2->id],
        ]);

        $response->assertRedirect(route('admin.opd.index'));
        $this->assertDatabaseMissing('opds', ['id' => $opd1->id]);
        $this->assertDatabaseMissing('opds', ['id' => $opd2->id]);
    }

    public function test_opd_can_download_task_attachment_provided_by_pic(): void
    {
        $file = UploadedFile::fake()->create('template_laporan.xlsx', 1024, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $path = $file->store('tugas', 'public');

        $this->tugasDigital->update([
            'file_path' => $path,
            'file_original_name' => 'template_laporan.xlsx',
            'file_size' => 1024 * 1024,
        ]);

        $response = $this->actingAs($this->opdUser)->get(route('opd.tugas.download', $this->tugasDigital));

        $response->assertStatus(200);
        $response->assertDownload('template_laporan.xlsx');
    }

    public function test_unauthorized_opd_cannot_download_task_attachment(): void
    {
        $otherOpd = Opd::create([
            'kode_opd' => 'DISPORA',
            'nama_opd' => 'Dinas Pemuda dan Olahraga',
            'is_active' => true,
        ]);

        $otherOpdUser = User::factory()->create([
            'name' => 'Staf Dispora',
            'email' => 'dispora@malutprov.go.id',
            'role' => 'opd',
            'opd_id' => $otherOpd->id,
        ]);

        $file = UploadedFile::fake()->create('rahasia.pdf', 500, 'application/pdf');
        $path = $file->store('tugas', 'public');

        $this->tugasDigital->update([
            'file_path' => $path,
            'file_original_name' => 'rahasia.pdf',
            'file_size' => 500 * 1024,
        ]);

        $response = $this->actingAs($otherOpdUser)->get(route('opd.tugas.download', $this->tugasDigital));

        $response->assertStatus(403);
    }
}
