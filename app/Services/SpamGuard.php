<?php

namespace App\Services;

use Illuminate\Http\Request;

class SpamGuard
{
    /**
     * Un formulaire rempli et soumis en moins de 3 secondes, ou sans le champ
     * caché "form_started_at" (donc pas rendu par notre propre page), est
     * presque toujours un robot.
     */
    public function looksAutomated(Request $request, int $minSeconds = 3): bool
    {
        $startedAt = $request->input('form_started_at');

        if (! is_numeric($startedAt)) {
            return true;
        }

        return (time() - (int) $startedAt) < $minSeconds;
    }

    /**
     * Plusieurs liens dans un message est un signal de spam classique.
     */
    public function hasTooManyLinks(string $text, int $max = 2): bool
    {
        return preg_match_all('/https?:\/\//i', $text) > $max;
    }
}
