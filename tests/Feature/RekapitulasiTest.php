<?php

namespace Tests\Feature;

use App\Models\Opd;
use App\Models\Penugasan;
use App\Models\PenugasanPenerima;
use App\Models\Submission;
use App\Models\Tugas;
use App\Models\TugasOpd;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekapitulasiTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $anggotaBidang;

    protected User $opdUser;

    protected Opd $opd;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'name' => 'Super Administrator Bappeda',
            'email' => 'superadmin@malutprov.go.id',
            'role' => 'super_admin',
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin PIC Bappeda',
            'email' => 'adminpic@malutprov.go.id',
            'role' => 'admin',
        ]);

        $this->anggotaBidang = User::factory()->create([
            'name' => 'Fahmi Ahmad, S.E.',
            'email' => 'fahmi@bappeda.malutprov.go.id',
            'role' => 'anggota_bidang',
            'bidang' => 'ekop',
            'no_hp' => '081234567890',
        ]);

        $this->opd = Opd::create([
            'kode_opd' => 'BAPPEDA-EKOP',
            'nama_opd' => 'Bappeda Bidang Perekonomian & Perdagangan',
            'nama_pic' => 'Siti Rahma',
            'whatsapp_pic' => '082198765432',
            'is_active' => true,
        ]);

        $this->opdUser = User::factory()->opd($this->opd->id)->create([
            'name' => 'Staf OPD',
            'email' => 'opd@malutprov.go.id',
            'role' => 'opd',
        ]);
    }

    public function test_super_admin_can_access_rekapitulasi_opd_page(): void
    {
        // Create task within the last 30 days
        $tugas = Tugas::create([
            'admin_id' => $this->superAdmin->id,
            'judul' => 'Laporan Realisasi Investasi Daerah Triwulan III',
            'deskripsi' => 'Pengumpulan berkas fisik dan digital realisasi investasi',
            'format_pengumpulan' => 'hybrid',
            'deadline' => Carbon::now()->addDays(2),
            'tahun_anggaran' => '2026',
            'status' => 'aktif',
        ]);

        $tugasOpd = TugasOpd::create([
            'tugas_id' => $tugas->id,
            'opd_id' => $this->opd->id,
            'status' => 'diterima',
        ]);

        Submission::create([
            'tugas_opd_id' => $tugasOpd->id,
            'file_path' => 'submissions/sample.pdf',
            'file_original_name' => 'Laporan_Investasi_2026.pdf',
            'file_size' => 102400,
            'nomor_surat' => '500/01/INV/2026',
            'status_verifikasi' => 'diterima',
            'verified_at' => Carbon::now(),
            'verified_by' => $this->superAdmin->id,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.rekapitulasi.opd'));

        $response->assertStatus(200);
        $response->assertSee('REKAPITULASI EVALUASI KINERJA KEPATUHAN PERANGKAT DAERAH (OPD)');
        $response->assertSee('1 Bulan Terakhir');
        $response->assertSee('Bappeda Bidang Perekonomian & Perdagangan');
        $response->assertSee('Laporan Realisasi Investasi Daerah Triwulan III');
        $response->assertSee('Cetak Rekapitulasi');
        $response->assertSee('window.print()', false);
    }

    public function test_super_admin_can_filter_rekapitulasi_opd_by_period(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.rekapitulasi.opd', ['period' => 'this_month']));

        $response->assertStatus(200);
        $response->assertSee('Bulan Ini');
    }

    public function test_super_admin_can_access_rekapitulasi_bidang_page(): void
    {
        // Create penugasan internal bidang in the last 30 days
        $penugasan = Penugasan::create([
            'pemberi_id' => $this->superAdmin->id,
            'judul' => 'Rapat Koordinasi Pengendalian Inflasi Daerah di Tidore',
            'deskripsi' => 'Menghadiri rakor TPID tingkat provinsi di Tidore Kepulauan',
            'tanggal_kegiatan' => Carbon::now()->subDays(5),
            'batas_laporan' => Carbon::now()->subDays(2),
            'catatan' => 'Membawa bahan paparan komoditas cabai & bawang',
        ]);

        PenugasanPenerima::create([
            'penugasan_id' => $penugasan->id,
            'user_id' => $this->anggotaBidang->id,
            'status_penugasan' => 'selesai',
            'status_konfirmasi' => 'dikonfirmasi',
            'dikonfirmasi_pada' => Carbon::now()->subDays(6),
            'status_laporan' => 'sudah_dilaporkan',
            'file_laporan' => 'reports/sample.pdf',
            'file_laporan_name' => 'Notulensi_Rakor_Inflasi.pdf',
            'file_laporan_size' => 204800,
            'isi_laporan' => 'Rakor berjalan lancar dengan kesepakatan pasokan beras stabil.',
            'dilaporkan_pada' => Carbon::now()->subDays(3),
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.rekapitulasi.bidang'));

        $response->assertStatus(200);
        $response->assertSee('REKAPITULASI EVALUASI KINERJA & PRODUKTIVITAS ANGGOTA BIDANG');
        $response->assertSee('Fahmi Ahmad, S.E.');
        $response->assertSee('Rapat Koordinasi Pengendalian Inflasi Daerah di Tidore');
        $response->assertSee('Notulensi_Rakor_Inflasi.pdf');
        $response->assertSee('Cetak Rekapitulasi');
        $response->assertSee('window.print()', false);
    }

    public function test_non_super_admin_cannot_access_rekapitulasi_routes(): void
    {
        // OPD user should receive 403 Forbidden
        $responseOpd = $this->actingAs($this->opdUser)
            ->get(route('admin.rekapitulasi.opd'));
        $responseOpd->assertStatus(403);

        $responseBidang = $this->actingAs($this->opdUser)
            ->get(route('admin.rekapitulasi.bidang'));
        $responseBidang->assertStatus(403);

        // Guest should be redirected to login
        $this->post(route('logout'));
        $responseGuest = $this->get(route('admin.rekapitulasi.opd'));
        $responseGuest->assertRedirect(route('login'));
    }

    public function test_sidebar_contains_target_blank_links_for_super_admin(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('admin.rekapitulasi.opd'));
        $response->assertSee(route('admin.rekapitulasi.bidang'));
        $response->assertSee('target="_blank"', false);
        $response->assertSee('Rekap Kinerja OPD');
        $response->assertSee('Rekap Kinerja Bidang');
    }

    public function test_sidebar_does_not_contain_rekapitulasi_links_for_opd_user(): void
    {
        $response = $this->actingAs($this->opdUser)
            ->get(route('opd.dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee(route('admin.rekapitulasi.opd'));
        $response->assertDontSee(route('admin.rekapitulasi.bidang'));
    }
}
