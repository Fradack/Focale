<?php

namespace App\Services;

/**
 * Convertit un texte BBCode saisi par l'artiste en HTML sûr pour l'affichage
 * public. Le texte entier est d'abord échappé (htmlspecialchars) avant toute
 * substitution, donc rien de ce que l'auteur tape ne peut injecter de balise
 * HTML arbitraire — seules les balises BBCode reconnues ci-dessous sont
 * transformées en équivalents HTML précis.
 */
class BbcodeParser
{
    public static function toHtml(?string $text): string
    {
        if (! $text || trim($text) === '') {
            return '';
        }

        $html = e($text);

        $html = preg_replace_callback('/\[url=(.+?)\](.+?)\[\/url\]/is', function ($m) {
            $url = self::safeUrl($m[1]);

            return $url ? '<a href="'.$url.'" target="_blank" rel="noopener nofollow">'.$m[2].'</a>' : $m[2];
        }, $html);

        $html = preg_replace_callback('/\[url\](.+?)\[\/url\]/is', function ($m) {
            $url = self::safeUrl($m[1]);

            return $url ? '<a href="'.$url.'" target="_blank" rel="noopener nofollow">'.$m[1].'</a>' : $m[1];
        }, $html);

        $html = preg_replace_callback('/\[img\](.+?)\[\/img\]/is', function ($m) {
            $url = self::safeUrl($m[1]);

            return $url ? '<img src="'.$url.'" loading="lazy" alt="">' : '';
        }, $html);

        $html = preg_replace_callback('/\[color=(#?[a-zA-Z0-9]{3,20})\](.+?)\[\/color\]/is', fn ($m) => '<span style="color:'.$m[1].';">'.$m[2].'</span>', $html);

        $html = preg_replace_callback('/\[size=(\d{1,3})\](.+?)\[\/size\]/is', function ($m) {
            $size = max(10, min(48, (int) $m[1]));

            return '<span style="font-size:'.$size.'px;">'.$m[2].'</span>';
        }, $html);

        $html = preg_replace_callback('/\[list\](.+?)\[\/list\]/is', function ($m) {
            $items = array_filter(array_map('trim', preg_split('/\[\*\]/', $m[1])));

            return '<ul>'.implode('', array_map(fn ($i) => '<li>'.$i.'</li>', $items)).'</ul>';
        }, $html);

        $simple = [
            '/\[b\](.+?)\[\/b\]/is' => '<strong>$1</strong>',
            '/\[i\](.+?)\[\/i\]/is' => '<em>$1</em>',
            '/\[u\](.+?)\[\/u\]/is' => '<u>$1</u>',
            '/\[s\](.+?)\[\/s\]/is' => '<s>$1</s>',
            '/\[quote\](.+?)\[\/quote\]/is' => '<blockquote>$1</blockquote>',
            '/\[code\](.+?)\[\/code\]/is' => '<pre><code>$1</code></pre>',
        ];
        foreach ($simple as $pattern => $replacement) {
            $html = preg_replace($pattern, $replacement, $html);
        }

        $blocks = preg_split('/\n\s*\n/', trim($html)) ?: [];

        return implode('', array_map(fn ($block) => '<p>'.nl2br($block).'</p>', array_filter($blocks, fn ($b) => trim($b) !== '')));
    }

    /**
     * N'autorise que les schémas d'URL inoffensifs (jamais "javascript:"),
     * puisque le résultat est injecté directement dans un attribut href/src.
     */
    private static function safeUrl(string $url): ?string
    {
        $url = trim($url);

        return preg_match('#^(https?://|mailto:|/)#i', $url) ? $url : null;
    }
}
