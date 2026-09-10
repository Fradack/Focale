<?php

namespace App\Plugins\Tracking\Support;

class UserAgentParser
{
    public static function deviceType(?string $userAgent): string
    {
        $ua = strtolower($userAgent ?? '');

        if (preg_match('/ipad|tablet/', $ua)) {
            return 'tablet';
        }

        if (preg_match('/mobile|android|iphone/', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    public static function os(?string $userAgent): string
    {
        $ua = strtolower($userAgent ?? '');

        if (preg_match('/iphone|ipad|ipod/', $ua)) {
            return 'iOS';
        }

        if (preg_match('/android/', $ua)) {
            return 'Android';
        }

        if (preg_match('/windows/', $ua)) {
            return 'Windows';
        }

        if (preg_match('/mac os x|macintosh/', $ua)) {
            return 'macOS';
        }

        if (preg_match('/linux/', $ua)) {
            return 'Linux';
        }

        return 'Autre';
    }
}
