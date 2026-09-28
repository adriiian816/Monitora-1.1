<?php

namespace App\Providers;

use App\Listeners\RecordAuthSessionActivity;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('id');
        \Illuminate\Support\Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.utf8', 'indonesian', 'id_ID', 'id');

        Event::listen(Login::class, [RecordAuthSessionActivity::class, 'handleLogin']);
        Event::listen(Logout::class, [RecordAuthSessionActivity::class, 'handleLogout']);

        // Bersihkan seluruh sesi perangkat tertaut di database setiap kali server dihidupkan
        if ($this->app->runningInConsole()) {
            Event::listen(CommandStarting::class, function (CommandStarting $event) {
                if (in_array($event->command, ['serve', 'dev'], true)) {
                    try {
                        if (Schema::hasTable('sessions')) {
                            DB::table('sessions')->truncate();
                            @file_put_contents(storage_path('framework/server_booted_at'), time());
                            if ($event->output) {
                                $event->output->writeln('<info>[MONITORA]</info> Server dihidupkan: Seluruh data sesi perangkat terhubung ke akun berhasil dibersihkan.');
                            }
                        }
                    } catch (\Throwable $e) {
                        // Abaikan jika database belum siap
                    }
                }
            });
        }
    }
}
