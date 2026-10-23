<?php

namespace App\Services;

use App\Models\Opd;
use App\Models\Penugasan;
use App\Models\PenugasanPenerima;
use App\Models\Submission;
use App\Models\Tugas;
use App\Models\User;
use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $provider;

    protected ?string $token;

    /**
     * Maximum WhatsApp messages allowed per hour per phone number to prevent number banning.
     */
    public const MAX_PER_HOUR_PER_NUMBER = 30;

    protected ?string $apiUrl;

    protected ?string $botUrl;

    public function __construct()
    {
        $this->provider = config('services.whatsapp.provider', env('WA_PROVIDER', 'simulation'));
        $this->token = config('services.whatsapp.token', env('WA_API_TOKEN'));
        $this->apiUrl = config('services.whatsapp.url', env('WA_API_URL', 'https://api.fonnte.com/send'));
        $this->botUrl = config('services.whatsapp.bot_url', env('WA_BOT_URL', 'http://127.0.0.1:3000/send-message'));
    }

    /**
     * Clean and normalize Indonesian phone number (convert 08xx to 628xx).
     */
    public function cleanPhoneNumber(string $phone): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        return $cleanPhone;
    }

    /**
     * Count messages sent to this phone number within the last hour.
     */
    public function getHourlyCount(string $targetPhone): int
    {
        $cleanPhone = $this->cleanPhoneNumber($targetPhone);
        if (empty($cleanPhone)) {
            return 0;
        }

        return WhatsappLog::where('no_tujuan', $cleanPhone)
            ->where('sent_at', '>=', now()->subHour())
            ->whereIn('status', ['terkirim', 'simulasi'])
            ->count();
    }

    /**
     * Check if a phone number has exceeded the hourly rate limit.
     */
    public function isRateLimited(string $targetPhone): bool
    {
        return $this->getHourlyCount($targetPhone) >= self::MAX_PER_HOUR_PER_NUMBER;
    }

    /**
     * Send generic WhatsApp message and log into database.
     */
    public function send(
        string $targetPhone,
        string $message,
        ?int $tugasId = null,
        ?int $opdId = null,
        string $tipe = 'manual'
    ): WhatsappLog {
        // Clean phone number (convert 08xx to 628xx if needed)
        $cleanPhone = $this->cleanPhoneNumber($targetPhone);

        // Rate limit check: Maksimal 30 pesan per jam per nomor
        $hourlyCount = $this->getHourlyCount($cleanPhone);
        if ($hourlyCount >= self::MAX_PER_HOUR_PER_NUMBER) {
            Log::warning("WhatsApp send rate limit exceeded for {$cleanPhone}. Sent {$hourlyCount}/".self::MAX_PER_HOUR_PER_NUMBER.' messages in the last hour.');

            return WhatsappLog::create([
                'tugas_id' => $tugasId,
                'opd_id' => $opdId,
                'tipe_pengingat' => $tipe,
                'no_tujuan' => $cleanPhone ?: $targetPhone,
                'isi_pesan' => $message,
                'status' => 'dibatasi',
                'response_payload' => [
                    'rate_limited' => true,
                    'message' => 'Batas pengiriman notifikasi tercapai (maksimal 30 pesan per jam untuk nomor ini).',
                    'error' => 'Batas pengiriman notifikasi tercapai (maksimal 30 pesan per jam untuk nomor ini).',
                    'hourly_count' => $hourlyCount,
                    'limit' => self::MAX_PER_HOUR_PER_NUMBER,
                    'rejected_at' => now()->toIso8601String(),
                ],
                'sent_at' => now(),
            ]);
        }

        $status = 'simulasi';
        $responsePayload = null;

        // If real provider configured and token present
        if ($this->provider === 'fonnte' && ! empty($this->token)) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $this->token,
                ])->post($this->apiUrl, [
                    'target' => $cleanPhone,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

                $responsePayload = $response->json();
                $status = $response->successful() ? 'terkirim' : 'gagal';
            } catch (\Throwable $e) {
                Log::error('WhatsApp send error: '.$e->getMessage());
                $status = 'gagal';
                $responsePayload = ['error' => $e->getMessage()];
            }
        } elseif (in_array($this->provider, ['local_bot', 'bot'], true)) {
            try {
                $response = Http::timeout(15)->post($this->botUrl, [
                    'phone' => $cleanPhone,
                    'message' => $message,
                ]);

                $responsePayload = $response->json() ?? ['raw' => $response->body()];
                $status = $response->successful() ? 'terkirim' : 'gagal';
            } catch (\Throwable $e) {
                Log::warning('WhatsApp local bot send error: '.$e->getMessage());
                $status = 'gagal';
                $responsePayload = ['error' => $e->getMessage(), 'provider' => 'local_bot'];
            }
        } else {
            // Local simulation
            $responsePayload = [
                'provider' => 'simulation',
                'notice' => 'Pesan dicatat dalam mode simulasi lokal.',
                'simulated_at' => now()->toIso8601String(),
            ];
            $status = 'simulasi';
        }

        return WhatsappLog::create([
            'tugas_id' => $tugasId,
            'opd_id' => $opdId,
            'tipe_pengingat' => $tipe,
            'no_tujuan' => $cleanPhone ?: $targetPhone,
            'isi_pesan' => $message,
            'status' => $status,
            'response_payload' => $responsePayload,
            'sent_at' => now(),
        ]);
    }

    /**
     * Notification when a new task is created sent specifically to an OPD staff member.
     */
    public function sendTaskCreatedToStaff(Tugas $tugas, User $staff, ?Opd $opd = null): ?WhatsappLog
    {
        if (empty($staff->no_hp)) {
            return null;
        }

        $opd = $opd ?: $staff->opd;
        $opdName = $opd?->nama_opd ?? 'Instansi OPD';
        $deadlineFormatted = $tugas->deadline->translatedFormat('l, d F Y - H:i').' WIT';
        $formatLabel = $tugas->format_label;
        $appUrl = url('/opd/tugas/'.$tugas->id);

        $message = "📢 *MONITORA BAPPEDA PROVINSI MALUKU UTARA*\n"
            ."Yth. {$staff->name} (Staf {$opdName}),\n\n"
            ."Terdapat penugasan pengumpulan berkas/laporan baru:\n"
            ."📌 *Judul Tugas:* {$tugas->judul}\n"
            ."📅 *Batas Deadline:* {$deadlineFormatted}\n"
            ."📂 *Format Pengumpulan:* {$formatLabel}\n"
            ."🗓️ *Tahun Anggaran:* {$tugas->tahun_anggaran}\n\n"
            ."Silakan segera akses portal MONITORA untuk melihat detail instruksi serta mengunduh jadwal ke kalender:\n"
            ."🔗 {$appUrl}\n\n"
            .'_Pesan otomatis ini dikirim oleh Sistem MONITORA Bappeda Maluku Utara._';

        return $this->send($staff->no_hp, $message, $tugas->id, $opd?->id, 'pembuatan_tugas');
    }

    /**
     * Notification when a new task is created and assigned to OPD.
     * Broadcasts to all staff of the OPD, or falls back to OPD whatsapp_pic if no staff has phone.
     */
    public function sendTaskCreated(Tugas $tugas, Opd $opd): ?WhatsappLog
    {
        $staffList = $opd->users()->where('role', 'opd')->whereNotNull('no_hp')->get();
        $lastLog = null;

        foreach ($staffList as $staff) {
            $log = $this->sendTaskCreatedToStaff($tugas, $staff, $opd);
            if ($log) {
                $lastLog = $log;
            }
        }

        if ($staffList->isEmpty() && ! empty($opd->whatsapp_pic)) {
            $deadlineFormatted = $tugas->deadline->translatedFormat('l, d F Y - H:i').' WIT';
            $formatLabel = $tugas->format_label;
            $appUrl = url('/opd/tugas/'.$tugas->id);

            $message = "📢 *MONITORA BAPPEDA PROVINSI MALUKU UTARA*\n"
                ."Yth. PIC {$opd->nama_opd},\n\n"
                ."Terdapat penugasan pengumpulan berkas/laporan baru:\n"
                ."📌 *Judul Tugas:* {$tugas->judul}\n"
                ."📅 *Batas Deadline:* {$deadlineFormatted}\n"
                ."📂 *Format Pengumpulan:* {$formatLabel}\n"
                ."🗓️ *Tahun Anggaran:* {$tugas->tahun_anggaran}\n\n"
                ."Silakan segera akses portal MONITORA untuk melihat detail instruksi serta mengunduh jadwal ke kalender:\n"
                ."🔗 {$appUrl}\n\n"
                .'_Pesan otomatis ini dikirim oleh Sistem MONITORA Bappeda Maluku Utara._';

            return $this->send($opd->whatsapp_pic, $message, $tugas->id, $opd->id, 'pembuatan_tugas');
        }

        return $lastLog;
    }

    /**
     * Periodic deadline reminder sent specifically to an OPD staff member.
     */
    public function sendDeadlineReminderToStaff(Tugas $tugas, User $staff, string $interval, ?Opd $opd = null): ?WhatsappLog
    {
        if (empty($staff->no_hp)) {
            return null;
        }

        $opd = $opd ?: $staff->opd;
        $opdName = $opd?->nama_opd ?? 'Instansi OPD';
        $deadlineFormatted = $tugas->deadline->translatedFormat('l, d F Y - H:i').' WIT';
        $appUrl = url('/opd/tugas/'.$tugas->id);

        $badgeTitle = match ($interval) {
            'h-14' => 'PENGINGAT DEADLINE (H-14)',
            'h-7' => 'PENGINGAT DEADLINE (H-7)',
            'h-3' => 'PENGINGAT DEADLINE (H-3)',
            'h-2' => 'PENGINGAT DEADLINE (H-2)',
            'h-1' => 'PENGINGAT MENDESAK (H-1)',
            'h-0' => 'HARI INI BATAS TERAKHIR (H-0)',
            'h-0-sore' => 'PERINGATAN TERAKHIR HARI INI (H-0)',
            default => 'PENGINGAT DEADLINE',
        };

        $urgencyNote = match ($interval) {
            'h-0-sore' => '⚠️ *PERHATIAN KHUSUS:* Batas akhir pengumpulan berakhir dalam beberapa jam ke depan hari ini! Mohon segera kirimkan sebelum batas waktu.',
            'h-0' => '⚠️ Hari ini adalah batas waktu terakhir pengumpulan berkas.',
            'h-1' => '⚠️ Waktu pengumpulan tersisa 1 hari lagi.',
            'h-2' => 'ℹ️ Waktu pengumpulan tersisa 2 hari lagi.',
            default => null,
        };

        $message = "⏳ *MONITORA: {$badgeTitle}*\n"
            ."Yth. {$staff->name} (Staf {$opdName}),\n\n"
            ."Mengingatkan kembali batas pengumpulan dokumen berikut:\n"
            ."📌 *Judul Tugas:* {$tugas->judul}\n"
            ."⏰ *Batas Akhir:* {$deadlineFormatted}\n"
            ."📂 *Format:* {$tugas->format_label}\n\n"
            .($urgencyNote ? "{$urgencyNote}\n\n" : '')
            ."Mohon segera menyelesaikan dan mengunggah/menyerahkan berkas sebelum waktu deadline berakhir.\n"
            ."🔗 Buka MONITORA: {$appUrl}\n\n"
            .'_BAPPEDA Bidang Perekonomian dan Perdagangan Maluku Utara_';

        return $this->send($staff->no_hp, $message, $tugas->id, $opd?->id, $interval);
    }

    /**
     * Periodic deadline reminder broadcast to all staff with phone.
     */
    public function sendDeadlineReminder(Tugas $tugas, Opd $opd, string $interval): ?WhatsappLog
    {
        $staffList = $opd->users()->where('role', 'opd')->whereNotNull('no_hp')->get();
        $lastLog = null;

        foreach ($staffList as $staff) {
            $log = $this->sendDeadlineReminderToStaff($tugas, $staff, $interval, $opd);
            if ($log) {
                $lastLog = $log;
            }
        }

        if ($staffList->isEmpty() && ! empty($opd->whatsapp_pic)) {
            $deadlineFormatted = $tugas->deadline->translatedFormat('l, d F Y - H:i').' WIT';
            $appUrl = url('/opd/tugas/'.$tugas->id);

            $badgeTitle = match ($interval) {
                'h-14' => 'PENGINGAT DEADLINE (H-14)',
                'h-7' => 'PENGINGAT DEADLINE (H-7)',
                'h-3' => 'PENGINGAT DEADLINE (H-3)',
                'h-2' => 'PENGINGAT DEADLINE (H-2)',
                'h-1' => 'PENGINGAT MENDESAK (H-1)',
                'h-0' => 'HARI INI BATAS TERAKHIR (H-0)',
                'h-0-sore' => 'PERINGATAN TERAKHIR HARI INI (H-0)',
                default => 'PENGINGAT DEADLINE',
            };

            $urgencyNote = match ($interval) {
                'h-0-sore' => '⚠️ *PERHATIAN KHUSUS:* Batas akhir pengumpulan berakhir dalam beberapa jam ke depan hari ini! Mohon segera kirimkan sebelum batas waktu.',
                'h-0' => '⚠️ Hari ini adalah batas waktu terakhir pengumpulan berkas.',
                'h-1' => '⚠️ Waktu pengumpulan tersisa 1 hari lagi.',
                'h-2' => 'ℹ️ Waktu pengumpulan tersisa 2 hari lagi.',
                default => null,
            };

            $message = "⏳ *MONITORA: {$badgeTitle}*\n"
                ."Yth. PIC {$opd->nama_opd},\n\n"
                ."Mengingatkan kembali batas pengumpulan dokumen berikut:\n"
                ."📌 *Judul Tugas:* {$tugas->judul}\n"
                ."⏰ *Batas Akhir:* {$deadlineFormatted}\n"
                ."📂 *Format:* {$tugas->format_label}\n\n"
                .($urgencyNote ? "{$urgencyNote}\n\n" : '')
                ."Mohon segera menyelesaikan dan mengunggah/menyerahkan berkas sebelum waktu deadline berakhir.\n"
                ."🔗 Buka MONITORA: {$appUrl}\n\n"
                .'_BAPPEDA Bidang Perekonomian dan Perdagangan Maluku Utara_';

            return $this->send($opd->whatsapp_pic, $message, $tugas->id, $opd->id, $interval);
        }

        return $lastLog;
    }

    /**
     * Overdue warning notification sent specifically to an OPD staff member.
     */
    public function sendOverdueWarningToStaff(Tugas $tugas, User $staff, ?Opd $opd = null, string $level = 'keterlambatan'): ?WhatsappLog
    {
        if (empty($staff->no_hp)) {
            return null;
        }

        $opd = $opd ?: $staff->opd;
        $opdName = $opd?->nama_opd ?? 'Instansi OPD';
        $deadlineFormatted = $tugas->deadline->translatedFormat('l, d F Y - H:i').' WIT';
        $appUrl = url('/opd/tugas/'.$tugas->id);

        $header = match ($level) {
            'eskalasi_1' => '🔴 *PERINGATAN ESKALASI KETERLAMBATAN (3+ HARI)*',
            'eskalasi_2' => '🚨 *PERINGATAN KRITIS KETERLAMBATAN (7+ HARI)*',
            default => '⚠️ *PERINGATAN KETERLAMBATAN TUGAS*',
        };

        $detail = match ($level) {
            'eskalasi_1' => 'Tugas ini telah melewati batas waktu lebih dari 3 hari dan belum ada berkas yang dikumpulkan.',
            'eskalasi_2' => 'Tugas ini telah melewati batas waktu lebih dari 7 hari (1 minggu). Laporan keterlambatan ini masuk ke rekap pengawasan pimpinan Bappeda.',
            default => 'Status tugas Anda telah ditandai *TERLAMBAT* karena melewati batas waktu.',
        };

        $message = "{$header}\n"
            ."Yth. {$staff->name} (Staf {$opdName}),\n\n"
            ."{$detail}\n"
            ."📌 *Judul Tugas:* {$tugas->judul}\n"
            ."⏰ *Deadline Sebelumnya:* {$deadlineFormatted}\n\n"
            ."Harap segera menghubungi tim BAPPEDA Bidang Perekonomian dan menyelesaikan pengumpulan dokumen susulan.\n"
            ."🔗 {$appUrl}\n\n"
            .'_Sistem Monitoring MONITORA Bappeda Malut_';

        return $this->send($staff->no_hp, $message, $tugas->id, $opd?->id, $level);
    }

    /**
     * Overdue warning notification broadcast to all staff with phone.
     */
    public function sendOverdueWarning(Tugas $tugas, Opd $opd, string $level = 'keterlambatan'): ?WhatsappLog
    {
        $staffList = $opd->users()->where('role', 'opd')->whereNotNull('no_hp')->get();
        $lastLog = null;

        foreach ($staffList as $staff) {
            $log = $this->sendOverdueWarningToStaff($tugas, $staff, $opd, $level);
            if ($log) {
                $lastLog = $log;
            }
        }

        if ($staffList->isEmpty() && ! empty($opd->whatsapp_pic)) {
            $deadlineFormatted = $tugas->deadline->translatedFormat('l, d F Y - H:i').' WIT';
            $appUrl = url('/opd/tugas/'.$tugas->id);

            $header = match ($level) {
                'eskalasi_1' => '🔴 *PERINGATAN ESKALASI KETERLAMBATAN (3+ HARI)*',
                'eskalasi_2' => '🚨 *PERINGATAN KRITIS KETERLAMBATAN (7+ HARI)*',
                default => '⚠️ *PERINGATAN KETERLAMBATAN TUGAS*',
            };

            $detail = match ($level) {
                'eskalasi_1' => 'Tugas ini telah melewati batas waktu lebih dari 3 hari dan belum ada berkas yang dikumpulkan.',
                'eskalasi_2' => 'Tugas ini telah melewati batas waktu lebih dari 7 hari (1 minggu). Laporan keterlambatan ini masuk ke rekap pengawasan pimpinan Bappeda.',
                default => 'Status tugas Anda telah ditandai *TERLAMBAT* karena melewati batas waktu.',
            };

            $message = "{$header}\n"
                ."Yth. PIC {$opd->nama_opd},\n\n"
                ."{$detail}\n"
                ."📌 *Judul Tugas:* {$tugas->judul}\n"
                ."⏰ *Deadline Sebelumnya:* {$deadlineFormatted}\n\n"
                ."Harap segera menghubungi tim BAPPEDA Bidang Perekonomian dan menyelesaikan pengumpulan dokumen susulan.\n"
                ."🔗 {$appUrl}\n\n"
                .'_Sistem Monitoring MONITORA Bappeda Malut_';

            return $this->send($opd->whatsapp_pic, $message, $tugas->id, $opd->id, $level);
        }

        return $lastLog;
    }

    /**
     * Manual broadcast sent specifically to an OPD staff member.
     */
    public function sendManualBroadcastToStaff(Tugas $tugas, User $staff, ?string $customNote = null, ?Opd $opd = null): ?WhatsappLog
    {
        if (empty($staff->no_hp)) {
            return null;
        }

        $opd = $opd ?: $staff->opd;
        $opdName = $opd?->nama_opd ?? 'Instansi OPD';
        $deadlineFormatted = $tugas->deadline->translatedFormat('l, d F Y - H:i').' WIT';
        $appUrl = url('/opd/tugas/'.$tugas->id);

        $message = "📢 *PENGINGAT KHUSUS BAPPEDA MALUKU UTARA*\n"
            ."Yth. {$staff->name} (Staf {$opdName}),\n\n"
            ."Pesan ini dikirimkan langsung oleh Tim Admin Bappeda terkait penugasan:\n"
            ."📌 *{$tugas->judul}*\n"
            ."⏰ *Batas Akhir:* {$deadlineFormatted}\n\n"
            .($customNote ? "Catatan Tambahan:\n\"{$customNote}\"\n\n" : '')
            ."Mohon atensi dan kerjasamanya untuk segera melakukan pengumpulan berkas.\n"
            ."🔗 Tautan Pengumpulan: {$appUrl}";

        return $this->send($staff->no_hp, $message, $tugas->id, $opd?->id, 'manual_broadcast');
    }

    /**
     * Manual broadcast from Admin broadcast to all staff with phone.
     */
    public function sendManualBroadcast(Tugas $tugas, Opd $opd, ?string $customNote = null): ?WhatsappLog
    {
        $staffList = $opd->users()->where('role', 'opd')->whereNotNull('no_hp')->get();
        $lastLog = null;

        foreach ($staffList as $staff) {
            $log = $this->sendManualBroadcastToStaff($tugas, $staff, $customNote, $opd);
            if ($log) {
                $lastLog = $log;
            }
        }

        if ($staffList->isEmpty() && ! empty($opd->whatsapp_pic)) {
            $deadlineFormatted = $tugas->deadline->translatedFormat('l, d F Y - H:i').' WIT';
            $appUrl = url('/opd/tugas/'.$tugas->id);

            $message = "📢 *PENGINGAT KHUSUS BAPPEDA MALUKU UTARA*\n"
                ."Yth. PIC {$opd->nama_opd},\n\n"
                ."Pesan ini dikirimkan langsung oleh Tim Admin Bappeda terkait penugasan:\n"
                ."📌 *{$tugas->judul}*\n"
                ."⏰ *Batas Akhir:* {$deadlineFormatted}\n\n"
                .($customNote ? "Catatan Tambahan:\n\"{$customNote}\"\n\n" : '')
                ."Mohon atensi dan kerjasamanya untuk segera melakukan pengumpulan berkas.\n"
                ."🔗 Tautan Pengumpulan: {$appUrl}";

            return $this->send($opd->whatsapp_pic, $message, $tugas->id, $opd->id, 'manual_broadcast');
        }

        return $lastLog;
    }

    /**
     * Notification of verification result sent specifically to a staff member.
     */
    public function sendVerificationResultToStaff(Submission $submission, User $staff): ?WhatsappLog
    {
        if (empty($staff->no_hp)) {
            return null;
        }

        $tugasOpd = $submission->tugasOpd;
        if (! $tugasOpd) {
            return null;
        }

        $tugas = $tugasOpd->tugas;
        $opd = $tugasOpd->opd;
        $opdName = $opd?->nama_opd ?? 'Instansi OPD';

        $statusText = match ($submission->status_verifikasi) {
            'diterima' => '✅ DITERIMA & DIVERIFIKASI',
            'perlu_revisi' => '⚠️ PERLU REVISI',
            default => 'MENUNGGU VERIFIKASI',
        };

        $catatan = $submission->catatan_admin ?: '-';
        $appUrl = url('/opd/tugas/'.$tugas->id);

        $message = "📑 *HASIL VERIFIKASI SUBMISSION MONITORA*\n"
            ."Yth. {$staff->name} (Staf {$opdName}),\n\n"
            ."Pengumpulan tugas *{$tugas->judul}* telah diperiksa oleh Admin Bappeda:\n"
            ."Status: *{$statusText}*\n"
            ."Catatan Admin: {$catatan}\n\n"
            ."Detail dapat dilihat pada tautan berikut:\n"
            ."🔗 {$appUrl}";

        return $this->send($staff->no_hp, $message, $tugas->id, $opd?->id, 'verifikasi');
    }

    /**
     * Notification of verification result broadcast to staff.
     */
    public function sendVerificationResult(Submission $submission): ?WhatsappLog
    {
        $tugasOpd = $submission->tugasOpd;
        if (! $tugasOpd || ! $tugasOpd->opd) {
            return null;
        }

        $opd = $tugasOpd->opd;
        $staffList = $opd->users()->where('role', 'opd')->whereNotNull('no_hp')->get();
        $lastLog = null;

        foreach ($staffList as $staff) {
            $log = $this->sendVerificationResultToStaff($submission, $staff);
            if ($log) {
                $lastLog = $log;
            }
        }

        if ($staffList->isEmpty() && ! empty($opd->whatsapp_pic)) {
            $tugas = $tugasOpd->tugas;
            $statusText = match ($submission->status_verifikasi) {
                'diterima' => '✅ DITERIMA & DIVERIFIKASI',
                'perlu_revisi' => '⚠️ PERLU REVISI',
                default => 'MENUNGGU VERIFIKASI',
            };

            $catatan = $submission->catatan_admin ?: '-';
            $appUrl = url('/opd/tugas/'.$tugas->id);

            $message = "📑 *HASIL VERIFIKASI SUBMISSION MONITORA*\n"
                ."Yth. PIC {$opd->nama_opd},\n\n"
                ."Pengumpulan tugas *{$tugas->judul}* telah diperiksa oleh Admin Bappeda:\n"
                ."Status: *{$statusText}*\n"
                ."Catatan Admin: {$catatan}\n\n"
                ."Detail dapat dilihat pada tautan berikut:\n"
                ."🔗 {$appUrl}";

            return $this->send($opd->whatsapp_pic, $message, $tugas->id, $opd->id, 'verifikasi');
        }

        return $lastLog;
    }

    /**
     * Notification sent to PIC (Admin Bappeda) when an OPD submits a task.
     */
    public function sendSubmissionNoticeToPic(Submission $submission, ?User $submittedBy = null): ?WhatsappLog
    {
        $tugasOpd = $submission->tugasOpd;
        if (! $tugasOpd) {
            return null;
        }

        $tugas = $tugasOpd->tugas;
        $opd = $tugasOpd->opd;
        if (! $tugas || ! $opd) {
            return null;
        }

        $opdName = $opd->nama_opd ?? 'Instansi OPD';
        $formatLabel = $tugas->format_label;
        $adminUrl = url('/admin/submissions/'.$submission->id);
        $submittedAt = now()->translatedFormat('l, d F Y - H:i').' WIT';
        $submitterName = $submittedBy?->name ?? 'Staf OPD';

        // PICs assigned to handle this OPD
        $picUsers = $opd->pics()->whereNotNull('no_hp')->where('no_hp', '!=', '')->get();
        $lastLog = null;

        foreach ($picUsers as $pic) {
            $message = "📥 *MONITORA: PENGUMPULAN TUGAS DARI OPD*\n"
                ."Yth. {$pic->name} (PIC Bappeda),\n\n"
                ."Instansi *{$opdName}* telah mengumpulkan dokumen/berkas penugasan:\n"
                ."📌 *Judul Tugas:* {$tugas->judul}\n"
                ."📂 *Format:* {$formatLabel}\n"
                .($submission->file_original_name ? "📄 *Berkas Dokumen:* {$submission->file_original_name} ({$submission->formatted_file_size})\n" : '')
                .($submission->konfirmasi_fisik ? "📦 *Penyerahan Fisik:* Telah diserahkan oleh {$submission->nama_pengantar} (No. Surat: {$submission->nomor_surat})\n" : '')
                .($submission->catatan_opd ? "💬 *Catatan OPD:* \"{$submission->catatan_opd}\"\n" : '')
                ."👤 *Pengunggah:* {$submitterName}\n"
                ."⏰ *Waktu:* {$submittedAt}\n\n"
                ."Silakan segera periksa dan verifikasi berkas melalui tautan berikut:\n"
                ."🔗 {$adminUrl}\n\n"
                .'_Sistem Monitoring MONITORA Bappeda Malut_';

            $log = $this->send($pic->no_hp, $message, $tugas->id, $opd->id, 'pengumpulan_tugas');
            if ($log) {
                $lastLog = $log;
            }
        }

        // If no assigned PIC has a phone number, fallback to the task creator (admin)
        if ($picUsers->isEmpty() && $tugas->admin && ! empty($tugas->admin->no_hp)) {
            $admin = $tugas->admin;
            $message = "📥 *MONITORA: PENGUMPULAN TUGAS DARI OPD*\n"
                ."Yth. {$admin->name} (Admin Bappeda),\n\n"
                ."Instansi *{$opdName}* telah mengumpulkan dokumen/berkas penugasan:\n"
                ."📌 *Judul Tugas:* {$tugas->judul}\n"
                ."📂 *Format:* {$formatLabel}\n"
                .($submission->file_original_name ? "📄 *Berkas Dokumen:* {$submission->file_original_name} ({$submission->formatted_file_size})\n" : '')
                .($submission->konfirmasi_fisik ? "📦 *Penyerahan Fisik:* Telah diserahkan oleh {$submission->nama_pengantar} (No. Surat: {$submission->nomor_surat})\n" : '')
                .($submission->catatan_opd ? "💬 *Catatan OPD:* \"{$submission->catatan_opd}\"\n" : '')
                ."👤 *Pengunggah:* {$submitterName}\n"
                ."⏰ *Waktu:* {$submittedAt}\n\n"
                ."Silakan segera periksa dan verifikasi berkas melalui tautan berikut:\n"
                ."🔗 {$adminUrl}\n\n"
                .'_Sistem Monitoring MONITORA Bappeda Malut_';

            return $this->send($admin->no_hp, $message, $tugas->id, $opd->id, 'pengumpulan_tugas');
        }

        // If still empty, fallback to Super Admin
        if ($picUsers->isEmpty() && (! $tugas->admin || empty($tugas->admin->no_hp))) {
            $superAdmin = User::where('role', 'super_admin')->whereNotNull('no_hp')->where('no_hp', '!=', '')->first();
            if ($superAdmin) {
                $message = "📥 *MONITORA: PENGUMPULAN TUGAS DARI OPD*\n"
                    ."Yth. {$superAdmin->name} (Administrator Bappeda),\n\n"
                    ."Instansi *{$opdName}* telah mengumpulkan dokumen/berkas penugasan:\n"
                    ."📌 *Judul Tugas:* {$tugas->judul}\n"
                    ."📂 *Format:* {$formatLabel}\n"
                    .($submission->file_original_name ? "📄 *Berkas Dokumen:* {$submission->file_original_name} ({$submission->formatted_file_size})\n" : '')
                    .($submission->konfirmasi_fisik ? "📦 *Penyerahan Fisik:* Telah diserahkan oleh {$submission->nama_pengantar} (No. Surat: {$submission->nomor_surat})\n" : '')
                    .($submission->catatan_opd ? "💬 *Catatan OPD:* \"{$submission->catatan_opd}\"\n" : '')
                    ."👤 *Pengunggah:* {$submitterName}\n"
                    ."⏰ *Waktu:* {$submittedAt}\n\n"
                    ."Silakan segera periksa dan verifikasi berkas melalui tautan berikut:\n"
                    ."🔗 {$adminUrl}\n\n"
                    .'_Sistem Monitoring MONITORA Bappeda Malut_';

                return $this->send($superAdmin->no_hp, $message, $tugas->id, $opd->id, 'pengumpulan_tugas');
            }
        }

        return $lastLog;
    }

    /**
     * Confirmation notification sent to OPD staff/PIC when document is successfully submitted.
     */
    public function sendSubmissionConfirmationToOpd(Submission $submission, ?User $submittedBy = null): ?WhatsappLog
    {
        $tugasOpd = $submission->tugasOpd;
        if (! $tugasOpd) {
            return null;
        }

        $tugas = $tugasOpd->tugas;
        $opd = $tugasOpd->opd;
        if (! $tugas || ! $opd) {
            return null;
        }

        $opdName = $opd->nama_opd ?? 'Instansi OPD';
        $formatLabel = $tugas->format_label;
        $opdUrl = url('/opd/tugas/'.$tugas->id);
        $submittedAt = now()->translatedFormat('l, d F Y - H:i').' WIT';

        $phone = $submittedBy?->no_hp ?: $opd->whatsapp_pic;
        $recipientName = $submittedBy?->name ?: ($opd->nama_pic ?: 'PIC '.$opdName);

        if (empty($phone)) {
            return null;
        }

        $message = "✅ *MONITORA: BUKTI PENGUMPULAN BERKAS*\n"
            ."Yth. {$recipientName} ({$opdName}),\n\n"
            ."Dokumen penugasan Anda telah berhasil dikumpulkan ke sistem MONITORA:\n"
            ."📌 *Judul Tugas:* {$tugas->judul}\n"
            ."📂 *Format:* {$formatLabel}\n"
            .($submission->file_original_name ? "📄 *Berkas:* {$submission->file_original_name} ({$submission->formatted_file_size})\n" : '')
            .($submission->konfirmasi_fisik ? "📦 *Penyerahan Fisik:* Dokumen fisik diserahkan oleh {$submission->nama_pengantar} (No. Surat: {$submission->nomor_surat})\n" : '')
            ."⏰ *Waktu Pengumpulan:* {$submittedAt}\n"
            ."📊 *Status Saat Ini:* Menunggu Verifikasi Admin Bappeda\n\n"
            ."Terima kasih atas kedisiplinan dan kerjasamanya.\n"
            ."🔗 Pantau Status: {$opdUrl}\n\n"
            .'_BAPPEDA Bidang Perekonomian dan Perdagangan Maluku Utara_';

        return $this->send($phone, $message, $tugas->id, $opd->id, 'konfirmasi_pengumpulan');
    }

    /**
     * Send both PIC notification and OPD confirmation when a submission is received.
     *
     * @return array{pic: ?WhatsappLog, opd: ?WhatsappLog}
     */
    public function sendSubmissionReceived(Submission $submission, ?User $submittedBy = null): array
    {
        return [
            'pic' => $this->sendSubmissionNoticeToPic($submission, $submittedBy),
            'opd' => $this->sendSubmissionConfirmationToOpd($submission, $submittedBy),
        ];
    }

    /**
     * Check if a phone number is registered on WhatsApp via bot service.
     *
     * @return array{success: bool, registered: bool, message: string}
     */
    public function checkNumberAvailability(string $phone): array
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        if (! in_array($this->provider, ['local_bot', 'bot'], true)) {
            return [
                'success' => true,
                'registered' => true,
                'message' => 'Mode simulasi: nomor dianggap tersedia.',
            ];
        }

        try {
            $checkUrl = str_replace('/send-message', '/check-number', $this->botUrl);
            $response = Http::timeout(15)->post($checkUrl, [
                'phone' => $cleanPhone,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'registered' => (bool) ($data['registered'] ?? false),
                    'message' => $data['message'] ?? 'Status nomor berhasil dicek.',
                ];
            }

            return [
                'success' => false,
                'registered' => false,
                'message' => $response->json('message') ?? 'Gagal memeriksa status nomor.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'registered' => false,
                'message' => 'Tidak dapat terhubung ke bot WhatsApp: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Generate dynamic, randomized WhatsApp message for penugasan anggota bidang
     * to avoid duplicate/identical template patterns that risk WhatsApp banning.
     */
    public function generateVariedPenugasanMessage(Penugasan $penugasan, User $anggota): string
    {
        $headers = [
            '📋 *PENUGASAN BARU - BIDANG PEREKONOMIAN & PERDAGANGAN*',
            '📢 *INSTRUKSI PENUGASAN BIDANG EKOP*',
            '📌 *INFORMASI DELEGASI TUGAS - BAPPEDA MALUT*',
            '✨ *AGENDA PENUGASAN INTERNAL BIDANG EKOP*',
            '📂 *NOTIFIKASI TUGAS BARU - BIDANG EKOP BAPPEDA*',
        ];

        $greetings = [
            "Yth. {$anggota->name},",
            "Yth. Rekan {$anggota->name},",
            "Salam hormat {$anggota->name},",
            "Kepada Yth. {$anggota->name},",
            "Halo {$anggota->name},",
        ];

        $pemberiName = $penugasan->pemberi?->name ?? 'Admin Bidang Ekop';

        $intros = [
            "Anda mendapatkan penugasan baru dari *{$pemberiName}*:",
            "Pimpinan/Admin *{$pemberiName}* telah mendelegasikan tugas kegiatan berikut kepada Anda:",
            "Terdapat agenda penugasan baru yang ditugaskan oleh *{$pemberiName}*:",
            "Berikut rincian instruksi tugas baru dari *{$pemberiName}*:",
            "Mohon atensinya, Anda telah didelegasikan oleh *{$pemberiName}* untuk kegiatan berikut:",
        ];

        $tanggalKegiatan = $penugasan->tanggal_kegiatan->translatedFormat('l, d F Y');
        $batasLaporan = $penugasan->batas_laporan
            ? $penugasan->batas_laporan->translatedFormat('l, d F Y')
            : 'Tidak ditentukan';

        // Varied bullet structures & icons
        $fieldStyles = [
            [
                'judul' => "📌 *Judul Tugas:* {$penugasan->judul}",
                'tgl' => "📅 *Tanggal Kegiatan:* {$tanggalKegiatan}",
                'batas' => "📊 *Batas Laporan:* {$batasLaporan}",
            ],
            [
                'judul' => "🎯 *Nama Kegiatan:* {$penugasan->judul}",
                'tgl' => "🗓️ *Waktu Pelaksanaan:* {$tanggalKegiatan}",
                'batas' => "⏳ *Tenggat Laporan:* {$batasLaporan}",
            ],
            [
                'judul' => "🏷️ *Uraian Agenda:* {$penugasan->judul}",
                'tgl' => "📆 *Hari/Tanggal:* {$tanggalKegiatan}",
                'batas' => "⏰ *Batas Akhir Laporan:* {$batasLaporan}",
            ],
            [
                'judul' => "📝 *Topik Penugasan:* {$penugasan->judul}",
                'tgl' => "📍 *Jadwal Kegiatan:* {$tanggalKegiatan}",
                'batas' => "⏱️ *Batas Pengumpulan:* {$batasLaporan}",
            ],
        ];

        $ctas = [
            'Silakan segera tinjau dan konfirmasi penugasan serta laporkan progres melalui portal MONITORA:',
            'Harap mengakses sistem MONITORA untuk konfirmasi kesiapan dan mengumpulkan laporan hasil:',
            'Mohon konfirmasikan kesiapan tugas dan serahkan laporan hasil di MONITORA:',
            'Akses tautan MONITORA berikut untuk konfirmasi penugasan dan pengumpulan laporan:',
        ];

        $signoffs = [
            '_Pesan otomatis ini dikirim oleh Sistem MONITORA Bappeda Maluku Utara._',
            '_Sistem Monitoring MONITORA - Bappeda Provinsi Maluku Utara._',
            '_Layanan Notifikasi Penugasan MONITORA Bidang Ekop Malut._',
            '_Notifikasi resmi otomatis dari MONITORA Bappeda Maluku Utara._',
        ];

        $header = $headers[array_rand($headers)];
        $greeting = $greetings[array_rand($greetings)];
        $intro = $intros[array_rand($intros)];
        $fields = $fieldStyles[array_rand($fieldStyles)];
        $cta = $ctas[array_rand($ctas)];
        $signoff = $signoffs[array_rand($signoffs)];

        $appUrl = url('/admin/penugasan/'.$penugasan->id);

        $body = "{$header}\n"
            ."{$greeting}\n\n"
            ."{$intro}\n"
            ."{$fields['judul']}\n"
            ."{$fields['tgl']}\n"
            ."{$fields['batas']}\n";

        if ($penugasan->deskripsi) {
            $descLabels = ['📝 *Keterangan:* ', 'ℹ️ *Deskripsi Tugas:* ', '📋 *Uraian:* '];
            $descLabel = $descLabels[array_rand($descLabels)];
            $body .= "{$descLabel}{$penugasan->deskripsi}\n";
        }

        if ($penugasan->catatan) {
            $catatanLabels = ['💬 *Catatan Khusus:* ', '💡 *Catatan Pimpinan:* ', '📌 *Instruksi Tambahan:* '];
            $catLabel = $catatanLabels[array_rand($catatanLabels)];
            $body .= "{$catLabel}{$penugasan->catatan}\n";
        }

        // Anti-duplicate micro-tag so message hash is never identical
        $uniqueCode = strtoupper(substr(md5($penugasan->id.'_'.$anggota->id.'_'.uniqid('', true)), 0, 6));
        $timestamp = now()->format('H:i:s');

        $body .= "\n{$cta}\n"
            ."🔗 {$appUrl}\n\n"
            ."{$signoff}\n"
            ."_Ref: #{$uniqueCode} • {$timestamp} WIT_";

        return $body;
    }

    /**
     * Send WhatsApp notification to a Bidang Ekop anggota when a new penugasan is assigned.
     */
    public function sendPenugasanToAnggota(Penugasan $penugasan, User $anggota): ?WhatsappLog
    {
        if (empty($anggota->no_hp)) {
            return null;
        }

        $message = $this->generateVariedPenugasanMessage($penugasan, $anggota);

        return $this->send($anggota->no_hp, $message, null, null, 'penugasan_ekop');
    }

    /**
     * Send WhatsApp notification to Admin when an anggota confirms/responds to penugasan.
     */
    public function sendKonfirmasiPenugasanToAdmin(
        Penugasan $penugasan,
        User $anggota,
        string $statusKonfirmasi,
        ?string $catatan = null
    ): ?WhatsappLog {
        $pemberi = $penugasan->pemberi;
        if (! $pemberi || empty($pemberi->no_hp)) {
            return null;
        }

        $appUrl = url('/admin/penugasan/'.$penugasan->id);
        $statusText = match ($statusKonfirmasi) {
            'dikonfirmasi' => '✅ BERSEDIA & SIAP MELAKSANAKAN',
            'ditolak' => '❌ BERHALANGAN / MENOLAK TUGAS',
            default => 'MENUNGGU KONFIRMASI',
        };
        $waktu = now()->translatedFormat('l, d F Y - H:i').' WIT';

        $message = "📢 *MONITORA: RESPON PENUGASAN INTERNAL*\n"
            ."Yth. {$pemberi->name},\n\n"
            ."Anggota Bidang *{$anggota->name}* telah merespons penugasan kegiatan:\n"
            ."📌 *Kegiatan:* {$penugasan->judul}\n"
            ."📊 *Status:* {$statusText}\n"
            .($catatan ? "💬 *Catatan Anggota:* \"{$catatan}\"\n" : '')
            ."⏰ *Waktu:* {$waktu}\n\n"
            ."Lihat detail lengkap di portal MONITORA:\n"
            ."🔗 {$appUrl}\n\n"
            .'_Sistem Monitoring MONITORA Bappeda Malut_';

        return $this->send($pemberi->no_hp, $message, null, null, 'konfirmasi_penugasan');
    }

    /**
     * Send WhatsApp notification to Admin when an anggota submits a report.
     */
    public function sendLaporanPenugasanToAdmin(
        Penugasan $penugasan,
        User $anggota,
        PenugasanPenerima $pivot
    ): ?WhatsappLog {
        $pemberi = $penugasan->pemberi;
        if (! $pemberi || empty($pemberi->no_hp)) {
            return null;
        }

        $appUrl = url('/admin/penugasan/'.$penugasan->id);
        $waktu = now()->translatedFormat('l, d F Y - H:i').' WIT';

        $message = "📥 *MONITORA: PENGUMPULAN LAPORAN BIDANG*\n"
            ."Yth. {$pemberi->name},\n\n"
            ."Anggota Bidang *{$anggota->name}* telah mengumpulkan laporan kegiatan:\n"
            ."📌 *Kegiatan:* {$penugasan->judul}\n"
            .($pivot->file_laporan_name ? "📄 *Berkas Laporan:* {$pivot->file_laporan_name} ({$pivot->formatted_file_size})\n" : '')
            .($pivot->isi_laporan ? "📝 *Ringkasan Hasil:* \"{$pivot->isi_laporan}\"\n" : '')
            .($pivot->link_dokumen ? "🔗 *Link Dokumen:* {$pivot->link_dokumen}\n" : '')
            ."⏰ *Waktu:* {$waktu}\n\n"
            ."Silakan periksa laporan melalui tautan berikut:\n"
            ."🔗 {$appUrl}\n\n"
            .'_Sistem Monitoring MONITORA Bappeda Malut_';

        return $this->send($pemberi->no_hp, $message, null, null, 'laporan_penugasan');
    }

    /**
     * Send WhatsApp confirmation to Anggota after submitting report.
     */
    public function sendLaporanConfirmationToAnggota(
        Penugasan $penugasan,
        User $anggota,
        PenugasanPenerima $pivot
    ): ?WhatsappLog {
        if (empty($anggota->no_hp)) {
            return null;
        }

        $appUrl = url('/admin/penugasan/'.$penugasan->id);
        $waktu = now()->translatedFormat('l, d F Y - H:i').' WIT';

        $message = "✅ *MONITORA: BUKTI PENGUMPULAN LAPORAN*\n"
            ."Yth. {$anggota->name},\n\n"
            ."Laporan hasil kegiatan Anda telah berhasil tersimpan di sistem:\n"
            ."📌 *Kegiatan:* {$penugasan->judul}\n"
            .($pivot->file_laporan_name ? "📄 *Berkas:* {$pivot->file_laporan_name} ({$pivot->formatted_file_size})\n" : '')
            ."⏰ *Waktu:* {$waktu}\n"
            ."📊 *Status:* Laporan Berhasil Dikumpulkan\n\n"
            ."Terima kasih atas dedikasi dan kinerjanya.\n"
            ."🔗 Tautan: {$appUrl}\n\n"
            .'_BAPPEDA Bidang Perekonomian dan Perdagangan Maluku Utara_';

        return $this->send($anggota->no_hp, $message, null, null, 'konfirmasi_laporan');
    }
}
