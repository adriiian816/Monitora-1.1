<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocationService
{
    /**
     * Resolve geographical location details for a given IP address.
     *
     * @return array{city: ?string, region: ?string, country: ?string, location: ?string, latitude: ?float, longitude: ?float}
     */
    public function resolve(?string $ip): array
    {
        if (empty($ip)) {
            return [
                'city' => null,
                'region' => null,
                'country' => null,
                'location' => 'Tidak Diketahui',
                'latitude' => null,
                'longitude' => null,
            ];
        }

        // Check if IP is local/private/loopback
        if ($this->isLocalOrPrivateIp($ip)) {
            return [
                'city' => 'Jaringan Lokal',
                'region' => 'Maluku Utara',
                'country' => 'Indonesia',
                'location' => "Jaringan Lokal / Intranet ({$ip})",
                'latitude' => 0.7893,
                'longitude' => 127.3871,
            ];
        }

        // Cache public IP geolocation lookup for 24 hours
        $cacheKey = "geoip_{$ip}";

        return Cache::remember($cacheKey, now()->addDay(), function () use ($ip): array {
            try {
                $response = Http::timeout(1.5)
                    ->get("http://ip-api.com/json/{$ip}?fields=status,message,country,regionName,city,lat,lon");

                if ($response->successful()) {
                    $data = $response->json();

                    if (($data['status'] ?? '') === 'success') {
                        $city = $data['city'] ?? 'Tidak Diketahui';
                        $region = $data['regionName'] ?? '';
                        $country = $data['country'] ?? 'Indonesia';

                        $parts = array_filter([$city, $region, $country]);
                        $location = implode(', ', $parts);

                        return [
                            'city' => $city,
                            'region' => $region,
                            'country' => $country,
                            'location' => $location,
                            'latitude' => isset($data['lat']) ? (float) $data['lat'] : null,
                            'longitude' => isset($data['lon']) ? (float) $data['lon'] : null,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::debug("GeoLocationService failed for IP {$ip}: ".$e->getMessage());
            }

            // Fallback for public IP if API is unreachable
            return [
                'city' => null,
                'region' => null,
                'country' => 'Indonesia',
                'location' => "IP Publik ({$ip})",
                'latitude' => null,
                'longitude' => null,
            ];
        });
    }

    /**
     * Check if given IP address is private, loopback, or reserved.
     */
    protected function isLocalOrPrivateIp(string $ip): bool
    {
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'], true)) {
            return true;
        }

        return ! filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
