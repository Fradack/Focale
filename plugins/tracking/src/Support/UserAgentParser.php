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
}
