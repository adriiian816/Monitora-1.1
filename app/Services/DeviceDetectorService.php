<?php

namespace App\Services;

class DeviceDetectorService
{
    /**
     * Parse user agent string into structured device, platform, and browser data.
     *
     * @return array{device_type: string, platform: string, browser: string}
     */
    public function detect(?string $userAgent): array
    {
        if (empty($userAgent)) {
            return [
                'device_type' => 'unknown',
                'platform' => 'Tidak Diketahui',
                'browser' => 'Tidak Diketahui',
            ];
        }

        return [
            'device_type' => $this->detectDeviceType($userAgent),
            'platform' => $this->detectPlatform($userAgent),
            'browser' => $this->detectBrowser($userAgent),
        ];
    }

    /**
     * Determine device category (desktop, mobile, tablet).
     */
    protected function detectDeviceType(string $ua): string
    {
        // Check for tablet first
        if (preg_match('/(ipad|tablet|(android(?!.*mobile))|(windows(?!.*phone)(.*touch))|kindle|playbook)/i', $ua)) {
            return 'tablet';
        }

        // Check for mobile phone
        if (preg_match('/(mobi|iphone|ipod|android.*mobile|blackberry|bb10|windows phone|opera mini|iemobile)/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Determine operating system / platform.
     */
    protected function detectPlatform(string $ua): string
    {
        $platforms = [
            '/windows nt 10.0/i' => 'Windows 10/11',
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows/i' => 'Windows',
            '/iphone/i' => 'iOS (iPhone)',
            '/ipad/i' => 'iOS (iPad)',
            '/ipod/i' => 'iOS (iPod)',
            '/macintosh|mac os x/i' => 'macOS',
            '/android ([0-9.]+)/i' => fn (array $m): string => 'Android '.$m[1],
            '/android/i' => 'Android',
            '/linux/i' => 'Linux',
            '/cros/i' => 'Chrome OS',
            '/ubuntu/i' => 'Ubuntu Linux',
            '/blackberry|bb10/i' => 'BlackBerry',
        ];

        foreach ($platforms as $pattern => $name) {
            if (preg_match($pattern, $ua, $matches)) {
                return is_callable($name) ? $name($matches) : $name;
            }
        }

        return 'Lainnya / OS Tidak Dikenal';
    }

    /**
     * Determine web browser.
     */
    protected function detectBrowser(string $ua): string
    {
        $browsers = [
            '/edg(e|a|ios)?\/([0-9.]+)/i' => 'Microsoft Edge',
            '/opr\/([0-9.]+)|opera\/([0-9.]+)/i' => 'Opera',
            '/samsungbrowser\/([0-9.]+)/i' => 'Samsung Internet',
            '/chrome\/([0-9.]+)/i' => 'Google Chrome',
            '/crios\/([0-9.]+)/i' => 'Google Chrome (iOS)',
            '/firefox\/([0-9.]+)|fxios\/([0-9.]+)/i' => 'Mozilla Firefox',
            '/version\/([0-9.]+).*safari/i' => 'Apple Safari',
            '/safari\/([0-9.]+)/i' => 'Apple Safari',
            '/msie ([0-9.]+)|trident.*rv:([0-9.]+)/i' => 'Internet Explorer',
        ];

        foreach ($browsers as $pattern => $name) {
            if (preg_match($pattern, $ua, $matches)) {
                $version = ! empty($matches[1]) ? explode('.', $matches[1])[0] : '';

                return $version ? "{$name} {$version}" : $name;
            }
        }

        return 'Browser Web';
    }
}
