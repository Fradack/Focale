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
     * Synonymes fréquents ne correspondant à aucun des 4 mots-clés
     * canoniques ci-dessus, mais dont l'intention est claire — rattachés au
     * badge le plus proche plutôt que de n'afficher aucune couleur.
     */
    private const ALIASES = [
        'ajustement' => 'information',
        'amélioration' => 'ajout',
        'amelioration' => 'ajout',
        'optimisation' => 'ajout',
        'renommage' => 'information',
        'retrait' => 'suppression',
        'suppression de' => 'suppression',
        'correctif' => 'correction',
        'fix' => 'correction',
    ];

    /**
     * Mots-clés cherchés n'importe où dans le texte (pas seulement en
     * préfixe) quand rien ci-dessus n'a matché — dernier recours avant le
     * badge neutre par défaut, pour qu'une ligne n'affiche jamais aucune
     * couleur du tout.
     */
    private const KEYWORD_HINTS = [
        'suppression' => ['supprim', 'retiré', 'retire', 'retrait'],
        'correction' => ['corrig', 'bug', 'erreur', 'plantage', 'cassé', 'casse'],
        'ajout' => ['ajout', 'nouveau', 'nouvelle', 'amélior', 'amelior', 'optimis'],
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
     * @return array{badge: array{label: string, class: string}, text: string}
     */
    private static function parseLine(string $line): array
    {
        $canonicalPattern = '/^('.implode('|', array_keys(self::BADGES)).')\s*:\s*(.+)$/iu';
        if (preg_match($canonicalPattern, $line, $m)) {
            return ['badge' => self::BADGES[mb_strtolower($m[1])], 'text' => $m[2]];
        }

        $aliasPattern = '/^('.implode('|', array_map(fn ($a) => preg_quote($a, '/'), array_keys(self::ALIASES))).')\s*:\s*(.+)$/iu';
        if (preg_match($aliasPattern, $line, $m)) {
            return ['badge' => self::BADGES[self::ALIASES[mb_strtolower($m[1])]], 'text' => $m[2]];
        }

        // Toujours une couleur, même sans préfixe reconnu : on devine la
        // catégorie la plus probable à partir de mots-clés présents dans le
        // texte, plutôt que de laisser la ligne sans badge du tout.
        $haystack = mb_strtolower($line);
        foreach (self::KEYWORD_HINTS as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($haystack, $keyword)) {
                    return ['badge' => self::BADGES[$category], 'text' => $line];
                }
            }
        }

        return ['badge' => self::BADGES['information'], 'text' => $line];
    }
}
