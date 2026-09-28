<?php

namespace App\Console\Commands;

use App\Models\Tugas;
use App\Models\WhatsappLog;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitora:check-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa deadline tugas aktif, update status keterlambatan, dan kirim pengingat WhatsApp otomatis';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsAppService): int
    {
        $this->info('Memulai pengecekan deadline tugas MONITORA...');

        $now = Carbon::now();
        $tugasList = Tugas::with(['tugasOpd.opd', 'tugasOpd.submission'])
            ->where('status', 'aktif')
            ->get();

        $remindersSent = 0;
        $overdueMarked = 0;

        foreach ($tugasList as $tugas) {
            $deadline = $tugas->deadline;

            foreach ($tugas->tugasOpd as $tugasOpd) {
                $opd = $tugasOpd->opd;
                if (! $opd || ! $opd->is_active) {
                    continue;
                }

                // Skip if already successfully completed
                if ($tugasOpd->status === 'diterima') {
                    continue;
                }

                // If already submitted and awaiting verification, do not send urgency reminder
                if ($tugasOpd->status === 'sudah_dikumpulkan') {
                    continue;
                }

                // 1. Check if overdue
                if ($now->greaterThan($deadline)) {
                    if ($tugasOpd->status !== 'terlambat') {
                        $tugasOpd->update(['status' => 'terlambat']);
                        $overdueMarked++;
                        $this->warn("Tugas '{$tugas->judul}' untuk OPD '{$opd->nama_opd}' ditandai TERLAMBAT.");
                    }

                    // Calculate days overdue from deadline day
                    $daysOverdue = (int) $deadline->copy()->startOfDay()->diffInDays($now->copy()->startOfDay(), false);

                    // Level 1: Initial overdue warning
                    $alreadySentOverdue = WhatsappLog::where('tugas_id', $tugas->id)
                        ->where('opd_id', $opd->id)
                        ->where('tipe_pengingat', 'keterlambatan')
                        ->exists();

                    if (! $alreadySentOverdue) {
                        $whatsAppService->sendOverdueWarning($tugas, $opd, 'keterlambatan');
                        $remindersSent++;
                        $this->info("Notifikasi keterlambatan dikirim ke {$opd->nama_opd}.");
                    }

                    // Level 2: Escalation after 3+ days overdue
                    if ($daysOverdue >= 3) {
                        $alreadySentEskalasi1 = WhatsappLog::where('tugas_id', $tugas->id)
                            ->where('opd_id', $opd->id)
                            ->where('tipe_pengingat', 'eskalasi_1')
                            ->exists();

                        if (! $alreadySentEskalasi1) {
                            $whatsAppService->sendOverdueWarning($tugas, $opd, 'eskalasi_1');
                            $remindersSent++;
                            $this->warn("Notifikasi eskalasi (3+ hari) dikirim ke {$opd->nama_opd}.");
                        }
                    }

                    // Level 3: Critical escalation after 7+ days overdue
                    if ($daysOverdue >= 7) {
                        $alreadySentEskalasi2 = WhatsappLog::where('tugas_id', $tugas->id)
                            ->where('opd_id', $opd->id)
                            ->where('tipe_pengingat', 'eskalasi_2')
                            ->exists();

                        if (! $alreadySentEskalasi2) {
                            $whatsAppService->sendOverdueWarning($tugas, $opd, 'eskalasi_2');
                            $remindersSent++;
                            $this->error("Notifikasi kritis (7+ hari) dikirim ke {$opd->nama_opd}.");
                        }
                    }

                    continue;
                }

                // 2. Check approaching deadlines (H-14, H-7, H-3, H-2, H-1, H-0)
                // Calculate days remaining (whole days difference from start of day)
                $daysRemaining = (int) $now->copy()->startOfDay()->diffInDays($deadline->copy()->startOfDay(), false);

                if ($daysRemaining < 0) {
                    continue;
                }

                $targetInterval = null;
                if ($daysRemaining === 14) {
                    $targetInterval = 'h-14';
                } elseif ($daysRemaining === 7) {
                    $targetInterval = 'h-7';
                } elseif ($daysRemaining === 3) {
                    $targetInterval = 'h-3';
                } elseif ($daysRemaining === 2) {
                    $targetInterval = 'h-2';
                } elseif ($daysRemaining === 1) {
                    $targetInterval = 'h-1';
                } elseif ($daysRemaining === 0 && $now->lessThanOrEqualTo($deadline)) {
                    // Check if morning reminder was already sent today
                    $hasSentMorningH0 = WhatsappLog::where('tugas_id', $tugas->id)
                        ->where('opd_id', $opd->id)
                        ->where('tipe_pengingat', 'h-0')
                        ->whereDate('sent_at', $now->toDateString())
                        ->exists();

                    // If it is afternoon (>= 13:00) and morning reminder has already been sent, send urgent afternoon reminder
                    if ($now->hour >= 13 && $hasSentMorningH0) {
                        $targetInterval = 'h-0-sore';
                    } else {
                        $targetInterval = 'h-0';
                    }
                }

                if ($targetInterval) {
                    $alreadySent = WhatsappLog::where('tugas_id', $tugas->id)
                        ->where('opd_id', $opd->id)
                        ->where('tipe_pengingat', $targetInterval)
                        ->whereDate('sent_at', $now->toDateString())
                        ->exists();

                    if (! $alreadySent) {
                        $whatsAppService->sendDeadlineReminder($tugas, $opd, $targetInterval);
                        $remindersSent++;
                        $this->info("Pengingat {$targetInterval} dikirim ke {$opd->nama_opd} untuk tugas '{$tugas->judul}'.");
                    }
                }
            }
        }

        $this->info("Selesai! {$overdueMarked} status terlambat diperbarui, {$remindersSent} pengingat terkirim.");

        return Command::SUCCESS;
    }
}
