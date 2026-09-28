<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sessions:clear', function () {
    DB::table('sessions')->truncate();
    @file_put_contents(storage_path('framework/server_booted_at'), time());
    $this->info('Seluruh data sesi login perangkat akun di database berhasil dibersihkan.');
})->purpose('Bersihkan seluruh sesi login perangkat aktif di database');

// Pengecekan deadline dan pengingat WhatsApp 3x sehari (Pagi, Siang, dan Sore menjelang akhir jam kerja)
Schedule::command('monitora:check-reminders')->dailyAt('08:00');
Schedule::command('monitora:check-reminders')->dailyAt('13:00');
Schedule::command('monitora:check-reminders')->dailyAt('16:00');
Schedule::command('monitora:purge-logs')->dailyAt('00:00');
Schedule::command('model:prune')->dailyAt('00:30');
