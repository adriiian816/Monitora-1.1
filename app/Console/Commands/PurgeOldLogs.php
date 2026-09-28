<?php

namespace App\Console\Commands;

use App\Models\LoginLog;
use App\Models\WhatsappLog;
use Illuminate\Console\Command;

class PurgeOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitora:purge-logs {--days=30 : Batas usia log dalam hari sebelum dihapus}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pembersihan otomatis data log sesi login/logout dan notifikasi WhatsApp yang lebih lama dari 30 hari';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = now()->subDays($days);

        $this->info("Memulai pembersihan log yang lebih lama dari {$days} hari (sebelum {$cutoffDate->toDateTimeString()})...");

        // 1. Purge Login & Logout logs
        $deletedLoginLogs = LoginLog::where('logged_at', '<', $cutoffDate)->delete();

        // 2. Purge WhatsApp Notification logs
        $deletedWhatsappLogs = WhatsappLog::where('sent_at', '<', $cutoffDate)
            ->orWhere('created_at', '<', $cutoffDate)
            ->delete();

        $this->line(" - Log sesi (Login/Logout) terhapus: <info>{$deletedLoginLogs}</info>");
        $this->line(" - Log notifikasi WhatsApp terhapus: <info>{$deletedWhatsappLogs}</info>");

        $totalDeleted = $deletedLoginLogs + $deletedWhatsappLogs;
        $this->info("Pembersihan log selesai. Total {$totalDeleted} rekaman log berhasil dibersihkan.");

        return Command::SUCCESS;
    }
}
