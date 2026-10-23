<?php

namespace Tests\Feature;

use App\Models\Opd;
use App\Models\Tugas;
use App\Models\TugasOpd;
use App\Models\User;
use App\Models\WhatsappLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $picBappeda;

    protected Opd $opd;

    protected User $staffOpd;

    protected Tugas $tugas;

    protected TugasOpd $tugasOpd;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // 1. Setup PIC Bappeda
        $this->picBappeda = User::factory()->admin('081299998888')->create([
            'name' => 'Fahri PIC Bappeda',
            'email' => 'fahri.pic@bappeda.malutprov.go.id',
        ]);

        // 2. Setup OPD and assign PIC
        $this->opd = Opd::create([
            'kode_opd' => 'DISPERINDAG',
            'nama_opd' => 'Dinas Perindustrian dan Perdagangan',
            'nama_pic' => 'Ahmad Kasubag',
            'whatsapp_pic' => '081234567890',
            'is_active' => true,
        ]);
        $this->opd->pics()->attach($this->picBappeda->id);

        // 3. Setup OPD Staff
        $this->staffOpd = User::factory()->opd($this->opd->id, '081234567890')->create([
            'name' => 'M. Rizal Staf OPD',
            'email' => 'rizal@disperindag.malutprov.go.id',
        ]);

        // 4. Setup Task assigned to OPD
        $this->tugas = Tugas::create([
            'admin_id' => $this->picBappeda->id,
            'judul' => 'Laporan Renja Tahunan TA 2027',
            'deskripsi' => 'Unggah dokumen Renja format PDF.',
            'format_pengumpulan' => 'digital',
            'deadline' => now()->addDays(3),
            'tahun_anggaran' => 2027,
            'status' => 'aktif',
        ]);

        $this->tugasOpd = TugasOpd::create([
            'tugas_id' => $this->tugas->id,
            'opd_id' => $this->opd->id,
            'status' => 'belum_dikumpulkan',
        ]);
    }

    public function test_pic_bappeda_receives_whatsapp_notification_when_opd_submits_task(): void
    {
        $file = UploadedFile::fake()->create('laporan_renja_2027.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->staffOpd)->post(route('opd.submission.store', $this->tugas), [
            'berkas_dokumen' => $file,
            'catatan_opd' => 'Dokumen renja final telah diverifikasi pimpinan.',
        ]);

        $response->assertRedirect(route('opd.tugas.show', $this->tugas));
        $response->assertSessionHas('success');

        // Check TugasOpd status updated
        $this->assertDatabaseHas('tugas_opd', [
            'id' => $this->tugasOpd->id,
            'status' => 'sudah_dikumpulkan',
        ]);

        // Check submission record created
        $this->assertDatabaseHas('submissions', [
            'tugas_opd_id' => $this->tugasOpd->id,
            'file_original_name' => 'laporan_renja_2027.pdf',
            'catatan_opd' => 'Dokumen renja final telah diverifikasi pimpinan.',
            'status_verifikasi' => 'menunggu_verifikasi',
        ]);

        // Check WhatsApp notification to PIC Bappeda was created in WhatsappLog
        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $this->tugas->id,
            'opd_id' => $this->opd->id,
            'tipe_pengingat' => 'pengumpulan_tugas',
            'no_tujuan' => '6281299998888',
        ]);

        $picLog = WhatsappLog::where('tipe_pengingat', 'pengumpulan_tugas')
            ->where('no_tujuan', '6281299998888')
            ->latest('id')
            ->first();

        $this->assertNotNull($picLog);
        $this->assertStringContainsString('PENGUMPULAN TUGAS DARI OPD', $picLog->isi_pesan);
        $this->assertStringContainsString('Dinas Perindustrian dan Perdagangan', $picLog->isi_pesan);
        $this->assertStringContainsString('Laporan Renja Tahunan TA 2027', $picLog->isi_pesan);
        $this->assertStringContainsString('M. Rizal Staf OPD', $picLog->isi_pesan);
        $this->assertStringContainsString('laporan_renja_2027.pdf', $picLog->isi_pesan);
        $this->assertStringContainsString('/admin/submissions/', $picLog->isi_pesan);

        // Also check WhatsApp confirmation receipt was sent to OPD
        $this->assertDatabaseHas('whatsapp_logs', [
            'tugas_id' => $this->tugas->id,
            'opd_id' => $this->opd->id,
            'tipe_pengingat' => 'konfirmasi_pengumpulan',
            'no_tujuan' => '6281234567890',
        ]);
    }
}
