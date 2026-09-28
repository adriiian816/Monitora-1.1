<?php

namespace App\Listeners;

use App\Models\LoginLog;
use App\Models\User;
use App\Services\DeviceDetectorService;
use App\Services\GeoLocationService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class RecordAuthSessionActivity
{
    public function __construct(
        protected DeviceDetectorService $deviceDetector,
        protected GeoLocationService $geoLocation,
    ) {}

    /**
     * Handle user login event.
     */
    public function handleLogin(Login $event): void
    {
        $this->recordActivity($event->user, 'login');
    }

    /**
     * Handle user logout event.
     */
    public function handleLogout(Logout $event): void
    {
        $this->recordActivity($event->user, 'logout');
    }

    /**
     * Record session activity in login_logs table.
     */
    protected function recordActivity(mixed $user, string $action): void
    {
        if (! $user instanceof User) {
            return;
        }

        try {
            $request = request();
            $ip = $request ? $request->ip() : '127.0.0.1';
            $userAgent = $request ? (string) $request->userAgent() : '';

            $device = $this->deviceDetector->detect($userAgent);
            $geo = $this->geoLocation->resolve($ip);

            LoginLog::create([
                'user_id' => $user->id,
                'opd_id' => $user->opd_id,
                'action' => $action,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'device_type' => $device['device_type'],
                'platform' => $device['platform'],
                'browser' => $device['browser'],
                'city' => $geo['city'],
                'region' => $geo['region'],
                'country' => $geo['country'],
                'location' => $geo['location'],
                'latitude' => $geo['latitude'],
                'longitude' => $geo['longitude'],
                'logged_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to record {$action} session activity for user {$user->id}: ".$e->getMessage());
        }
    }
}
