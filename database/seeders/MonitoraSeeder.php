<?php

namespace Database\Seeders;

use App\Models\Opd;
use App\Models\Submission;
use App\Models\Tugas;
use App\Models\TugasOpd;
use App\Models\User;
use App\Models\WhatsappLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MonitoraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Super Admin & Admin Users
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@bappeda.malutprov.go.id'],
            [
                'name' => 'Super Admin BAPPEDA Malut',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@bappeda.malutprov.go.id'],
            [
                'name' => 'Admin Perencanaan BAPPEDA',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Create Master OPDs
        $opdData = [
            [
                'kode_opd' => 'DISPERINDAG',
                'nama_opd' => 'Dinas Perindustrian dan Perdagangan Provinsi Maluku Utara',
                'alamat' => 'Jl. Pemprov Malut, Gosale Puncak, Sofifi',
                'staff_name' => 'Ahmad Hi. Saleh, S.E.',
                'staff_email' => 'disperindag@malutprov.go.id',
                'staff_no_hp' => '081234567801',
            ],
            [
                'kode_opd' => 'DKP',
                'nama_opd' => 'Dinas Kelautan dan Perikanan Provinsi Maluku Utara',
                'alamat' => 'Jl. Kompleks Perkantoran Pemerintah Provinsi, Sofifi',
                'staff_name' => 'Ir. Hj. Nurhasanah Kasim, M.Si',
                'staff_email' => 'dkp@malutprov.go.id',
                'staff_no_hp' => '081234567802',
            ],
            [
                'kode_opd' => 'DISTAN',
                'nama_opd' => 'Dinas Pertanian dan Ketahanan Pangan Provinsi Maluku Utara',
                'alamat' => 'Jl. Trans Halmahera, Gosale, Sofifi',
                'staff_name' => 'Dr. M. Rizal Daud, S.P., M.Si',
                'staff_email' => 'pertanian@malutprov.go.id',
                'staff_no_hp' => '081234567803',
            ],
            [
                'kode_opd' => 'DISKOPUKM',
                'nama_opd' => 'Dinas Koperasi dan Usaha Kecil Menengah Provinsi Maluku Utara',
                'alamat' => 'Jl. KM 40 Sofifi, Maluku Utara',
                'staff_name' => 'Siti Rahmawati, S.E., M.M.',
                'staff_email' => 'diskopukm@malutprov.go.id',
                'staff_no_hp' => '081234567804',
            ],
            [
                'kode_opd' => 'DISPAR',
                'nama_opd' => 'Dinas Pariwisata Provinsi Maluku Utara',
                'alamat' => 'Jl. Raya Guraping, Kec. Oba Utara, Sofifi',
                'staff_name' => 'Fikri Alkatiri, S.Sos, M.Si',
                'staff_email' => 'pariwisata@malutprov.go.id',
                'staff_no_hp' => '081234567805',
            ],
            [
                'kode_opd' => 'BPKAD',
                'nama_opd' => 'Badan Pengelolaan Keuangan dan Aset Daerah Provinsi Maluku Utara',
                'alamat' => 'Jl. Lintas Halmahera, Kompleks Kantor Gubernur, Sofifi',
                'staff_name' => 'Hendra Pratama, S.STP, M.Tr.IP',
                'staff_email' => 'bpkad@malutprov.go.id',
                'staff_no_hp' => '081234567806',
            ],
        ];

        $createdOpds = [];
        foreach ($opdData as $data) {
            $opd = Opd::updateOrCreate(
                ['kode_opd' => $data['kode_opd']],
                [
                    'nama_opd' => $data['nama_opd'],
                    'alamat' => $data['alamat'],
                    'is_active' => true,
                ]
            );

            $createdOpds[$data['kode_opd']] = $opd;

            // Create user account for OPD Staff
            User::updateOrCreate(
                ['email' => $data['staff_email']],
                [
                    'name' => $data['staff_name'].' ('.$data['kode_opd'].')',
                    'password' => Hash::make('password'),
                    'role' => 'opd',
                    'opd_id' => $opd->id,
                    'no_hp' => $data['staff_no_hp'],
                    'email_verified_at' => now(),
                ]
            );
        }

        // Assign sample handled OPDs to the PIC (Admin Perencanaan BAPPEDA)
        $admin->handledOpds()->sync([
            $createdOpds['DISPERINDAG']->id,
            $createdOpds['DKP']->id,
            $createdOpds['DISTAN']->id,
        ]);

        // Prepare dummy sample storage directory for file upload demos
        Storage::disk('public')->makeDirectory('submissions');
        $samplePdfPath = 'submissions/sample_renja_disperindag.pdf';
        if (! Storage::disk('public')->exists($samplePdfPath)) {
            Storage::disk('public')->put($samplePdfPath, '%PDF-1.4 Mock Bappeda Malut Submission Document');
        }

        // 3. Create Sample Tasks
        // Task 1: Digital Format (Deadline 7 hari mendatang)
        $tugas1 = Tugas::updateOrCreate(
            ['judul' => 'Penyusunan Rencana Kerja (RENJA) OPD Bidang Perekonomian TA 2027'],
            [
                'admin_id' => $admin->id,
                'deskripsi' => 'Pengumpulan dokumen Rencana Kerja (RENJA) Perangkat Daerah Tahun Anggaran 2027 yang telah diselaraskan dengan prioritas RKPD Provinsi Maluku Utara. Dokumen diunggah dalam format PDF dan lembar matriks Excel.',
                'format_pengumpulan' => 'digital',
                'deadline' => Carbon::now()->addDays(7)->setTime(23, 59, 0),
                'tahun_anggaran' => '2027',
                'status' => 'aktif',
            ]
        );

        // Assign to all OPDs
        foreach ($createdOpds as $kode => $opd) {
            $to = TugasOpd::firstOrCreate(
                ['tugas_id' => $tugas1->id, 'opd_id' => $opd->id],
                ['status' => 'belum_dikumpulkan']
            );

            // Let DISPERINDAG submit early
            if ($kode === 'DISPERINDAG') {
                $to->update(['status' => 'sudah_dikumpulkan']);
                Submission::updateOrCreate(
                    ['tugas_opd_id' => $to->id],
                    [
                        'file_path' => $samplePdfPath,
                        'file_original_name' => 'RENJA_DISPERINDAG_2027_FINAL.pdf',
                        'file_size' => 2048500, // ~2 MB
                        'catatan_opd' => 'Terlampir matriks program dan pagu indikatif TA 2027 Disperindag Malut.',
                        'status_verifikasi' => 'menunggu_verifikasi',
                    ]
                );
            }

            // Let DKP be already verified & approved
            if ($kode === 'DKP') {
                $to->update(['status' => 'diterima']);
                Submission::updateOrCreate(
                    ['tugas_opd_id' => $to->id],
                    [
                        'file_path' => $samplePdfPath,
                        'file_original_name' => 'RENJA_DKP_MALUT_2027_V2.pdf',
                        'file_size' => 3145728,
                        'catatan_opd' => 'Dokumen Renja DKP sudah ditandatangani Kepala Dinas.',
                        'catatan_admin' => 'Dokumen lengkap, pagu indikatif dan indikator kinerja sudah sesuai.',
                        'status_verifikasi' => 'diterima',
                        'verified_at' => now()->subDay(),
                        'verified_by' => $admin->id,
                    ]
                );
            }
        }

        // Task 2: Hybrid Format (Deadline 3 hari mendatang)
        $tugas2 = Tugas::updateOrCreate(
            ['judul' => 'Laporan Evaluasi Kinerja Triwulan III TA 2026 (Softcopy & Berkas Fisik)'],
            [
                'admin_id' => $admin->id,
                'deskripsi' => 'Setiap OPD wajib menyampaikan softcopy laporan evaluasi triwulan III melalui sistem MONITORA, serta mengantarkan 2 (dua) eksemplar hardcopy dokumen bertanda tangan basah dan berstempel ke Ruang Bidang Perekonomian Bappeda Malut.',
                'format_pengumpulan' => 'hybrid',
                'deadline' => Carbon::now()->addDays(3)->setTime(16, 0, 0),
                'tahun_anggaran' => '2026',
                'status' => 'aktif',
            ]
        );

        foreach ($createdOpds as $kode => $opd) {
            $to = TugasOpd::firstOrCreate(
                ['tugas_id' => $tugas2->id, 'opd_id' => $opd->id],
                ['status' => 'belum_dikumpulkan']
            );

            // DISTAN submitted with hybrid confirmation
            if ($kode === 'DISTAN') {
                $to->update(['status' => 'sudah_dikumpulkan']);
                Submission::updateOrCreate(
                    ['tugas_opd_id' => $to->id],
                    [
                        'file_path' => $samplePdfPath,
                        'file_original_name' => 'Laporan_Evaluasi_TW3_DISTAN.pdf',
                        'file_size' => 1845100,
                        'konfirmasi_fisik' => true,
                        'nama_pengantar' => 'Muhammad Ali (Staf Perencanaan Distan)',
                        'nomor_surat' => '500/128/DISTAN-MU/IX/2026',
                        'tanggal_penyerahan_fisik' => Carbon::now()->subHours(4)->toDateString(),
                        'catatan_opd' => 'Berkas fisik 2 rangkap telah diserahkan di loket penerimaan Bappeda.',
                        'status_verifikasi' => 'menunggu_verifikasi',
                    ]
                );
            }
        }

        // Task 3: Fisik Format (Deadline kemarin - Terlambat untuk yang belum kumpul)
        $tugas3 = Tugas::updateOrCreate(
            ['judul' => 'Pengumpulan Asli Berita Acara Rekonsiliasi Realisasi Belanja Modal'],
            [
                'admin_id' => $admin->id,
                'deskripsi' => 'Pengumpulan fisik berkas asli Berita Acara Rekonsiliasi Belanja Modal semester I. Wajib diantar langsung oleh staf perencana/keuangan OPD ke Bidang Perekonomian Dan Perdagangan.',
                'format_pengumpulan' => 'fisik',
                'deadline' => Carbon::now()->subDays(2)->setTime(15, 0, 0),
                'tahun_anggaran' => '2026',
                'status' => 'aktif',
            ]
        );

        foreach ([$createdOpds['DISPAR'], $createdOpds['DISKOPUKM'], $createdOpds['BPKAD']] as $opd) {
            $status = ($opd->kode_opd === 'BPKAD') ? 'diterima' : 'terlambat';
            $to = TugasOpd::firstOrCreate(
                ['tugas_id' => $tugas3->id, 'opd_id' => $opd->id],
                ['status' => $status]
            );

            if ($opd->kode_opd === 'BPKAD') {
                Submission::updateOrCreate(
                    ['tugas_opd_id' => $to->id],
                    [
                        'konfirmasi_fisik' => true,
                        'nama_pengantar' => 'Samsudin (Pranata Keuangan BPKAD)',
                        'nomor_surat' => '900/412/BPKAD/VIII/2026',
                        'tanggal_penyerahan_fisik' => Carbon::now()->subDays(3)->toDateString(),
                        'catatan_opd' => 'Dokumen fisik lengkap bermaterai Rp 10.000.',
                        'catatan_admin' => 'Fisik telah diverifikasi di meja Bappeda.',
                        'status_verifikasi' => 'diterima',
                        'verified_at' => Carbon::now()->subDays(2),
                        'verified_by' => $admin->id,
                    ]
                );
            }
        }

        // 4. Sample WhatsApp Logs
        WhatsappLog::create([
            'tugas_id' => $tugas1->id,
            'opd_id' => $createdOpds['DISPERINDAG']->id,
            'tipe_pengingat' => 'pembuatan_tugas',
            'no_tujuan' => '6281234567801',
            'isi_pesan' => "📢 *MONITORA BAPPEDA PROVINSI MALUKU UTARA*\nTerdapat penugasan baru: Penyusunan Rencana Kerja (RENJA) OPD Bidang Perekonomian TA 2027.",
            'status' => 'simulasi',
            'response_payload' => ['provider' => 'simulation', 'info' => 'Notifikasi tugas baru berhasil dibuat'],
            'sent_at' => now()->subDays(3),
        ]);

        WhatsappLog::create([
            'tugas_id' => $tugas2->id,
            'opd_id' => $createdOpds['DISTAN']->id,
            'tipe_pengingat' => 'h-3',
            'no_tujuan' => '6281234567803',
            'isi_pesan' => "⏳ *MONITORA: PENGINGAT DEADLINE (H-3)*\nTugas: Laporan Evaluasi Kinerja Triwulan III TA 2026.",
            'status' => 'simulasi',
            'response_payload' => ['provider' => 'simulation', 'info' => 'Pengingat H-3 terkirim'],
            'sent_at' => now()->subHours(12),
        ]);

        WhatsappLog::create([
            'tugas_id' => $tugas3->id,
            'opd_id' => $createdOpds['DISPAR']->id,
            'tipe_pengingat' => 'keterlambatan',
            'no_tujuan' => '6281234567805',
            'isi_pesan' => "⚠️ *PERINGATAN KETERLAMBATAN TUGAS*\nStatus penugasan Anda telah ditandai TERLAMBAT karena telah melewati deadline.",
            'status' => 'simulasi',
            'response_payload' => ['provider' => 'simulation', 'info' => 'Peringatan keterlambatan dikirimkan'],
            'sent_at' => now()->subDay(),
        ]);
    }
}
