<?php

namespace App\Support;

/**
 * Notes de release GitHub (texte brut, jamais de HTML) → blocs structurés
 * (puces/paragraphes) avec badge de catégorie optionnel. Convention
 * d'écriture : une ligne peut commencer par « Ajout : », « Correction : »,
 * « Suppression : » ou « Information : » (insensible à la casse, espace
 * avant les deux-points optionnel) pour afficher un badge coloré.
 */
class ReleaseNotes
{
    private const BADGES = [
        'suppression' => ['label' => 'Suppression', 'class' => 'badge-danger'],
        'correction' => ['label' => 'Correction', 'class' => 'badge-warning'],
        'ajout' => ['label' => 'Ajout', 'class' => 'badge-success'],
        'information' => ['label' => 'Information', 'class' => 'badge-info'],
    ];

    /**
     * @return array<int, array{0: string, 1: mixed}> paires [kind, content] — kind vaut 'list' (content = liste de ['badge'=>?array,'text'=>string]) ou 'p' (content = ['badge'=>?array,'text'=>string])
     */
    public static function parse(?string $notes): array
    {
        if (! $notes) {
            return [];
        }

        // Un BOM UTF-8 en tête de texte (fréquent selon l'outil utilisé pour
        // écrire les notes) rend le tout premier caractère invisible mais
        // bien présent : la regex ci-dessous, ancrée en début de ligne, ne
        // matcherait alors jamais la toute première ligne.
        $text = ltrim($notes, "\xEF\xBB\xBF");

        $blocks = [];
        $currentList = [];

        foreach (preg_split('/\r\n|\r|\n/', trim($text)) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if (str_starts_with($line, '- ') || str_starts_with($line, '* ')) {
                $currentList[] = self::parseLine(ltrim(substr($line, 2)));

                continue;
            }

            if ($currentList) {
                $blocks[] = ['list', $currentList];
                $currentList = [];
            }

            $blocks[] = ['p', self::parseLine($line)];
        }

        if ($currentList) {
            $blocks[] = ['list', $currentList];
        }

        return $blocks;
    }

    /**
     * @return array{badge: ?array{label: string, class: string}, text: string}
     */
    private static function parseLine(string $line): array
    {
        $pattern = '/^('.implode('|', array_keys(self::BADGES)).')\s*:\s*(.+)$/iu';

        if (preg_match($pattern, $line, $m)) {
            return ['badge' => self::BADGES[mb_strtolower($m[1])], 'text' => $m[2]];
        }

        return ['badge' => null, 'text' => $line];
    }
}
