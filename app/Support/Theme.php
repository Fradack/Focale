<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Thème visuel — choisi séparément pour l'administration et le site public
 * (deux réglages indépendants, voir Réglages → Apparence), avec des thèmes
 * saisonniers en plus du sombre classique.
 */
class Theme
{
    public const THEMES = ['light', 'dark', 'halloween', 'noel', 'midnight', 'sunset', 'safari', 'fullblack'];

    public const LABELS = [
        'light' => 'Clair',
        'dark' => 'Sombre',
        'halloween' => 'Halloween',
        'noel' => 'Noël',
        'midnight' => 'Bleu Minuit',
        'sunset' => 'Sunset',
        'safari' => 'Safari',
        'fullblack' => 'Fullblack',
    ];

    public static function adminTheme(): string
    {
        return self::normalize(Setting::get('theme_admin'));
    }

    public static function publicTheme(): string
    {
        return self::normalize(Setting::get('theme_public'));
    }

    private static function normalize(?string $value): string
    {
        return in_array($value, self::THEMES, true) ? $value : 'light';
    }

    public static function adminHtmlAttr(): string
    {
        return self::htmlAttr(self::adminTheme());
    }

    public static function publicHtmlAttr(): string
    {
        return self::htmlAttr(self::publicTheme());
    }

    private static function htmlAttr(string $theme): string
    {
        return $theme === 'light' ? '' : ' data-theme="'.$theme.'"';
    }
}
