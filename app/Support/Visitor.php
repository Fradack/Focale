<?php

namespace App\Support;

use App\Http\Middleware\AssignVisitorId;

class Visitor
{
    /**
     * Identifiant anonyme du visiteur courant — posé par AssignVisitorId sur
     * toute requête web. Le fallback sur le cookie brut ne sert qu'en test
     * (requête construite sans passer par le middleware).
     */
    public static function id(): string
    {
        return request()->attributes->get('visitor_id')
            ?? request()->cookie(AssignVisitorId::COOKIE)
            ?? 'anonymous';
    }
}
