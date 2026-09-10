<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Thème sombre : un seul réglage (`theme_dark_mode`) choisit sur quelle(s)
 * surface(s) il s'applique — l'admin décide, voir Réglages → Apparence.
 */
class Theme
{
    public const SCOPES = ['off', 'public', 'admin', 'both'];

    public static function mode(): string
    {
        $value = Setting::get('theme_dark_mode', 'off');

        return in_array($value, self::SCOPES, true) ? $value : 'off';
    }

    private static function isDarkFor(string $surface): bool
    {
        $mode = self::mode();

        return $mode === 'both' || $mode === $surface;
    }

    public static function publicHtmlAttr(): string
    {
        return self::isDarkFor('public') ? ' data-theme="dark"' : '';
    }

    public static function adminHtmlAttr(): string
    {
        return self::isDarkFor('admin') ? ' data-theme="dark"' : '';
    }
}
