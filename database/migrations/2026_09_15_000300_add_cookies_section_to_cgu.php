<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

/**
 * Ajoute une section décrivant le plugin Tracking (facultatif, désactivé par
 * défaut) à la page CGU existante — même logique prudente que le seed
 * initial : simple ajout de bloc, ne republie jamais la page toute seule.
 */
return new class extends Migration
{
    private const TEXT = <<<'TEXT'
Cookies et traceurs

Lorsque le plugin Tracking est activé, Focale dépose un traceur de session anonyme et enregistre, pour chaque page visitée : votre adresse IP, une localisation approximative déduite de cette IP (commune, pays — jamais votre position GPS), le type d'appareil utilisé (mobile, tablette, ordinateur), et le temps passé sur la page. Ces informations servent uniquement à comprendre la fréquentation du site et à en améliorer le confort de navigation ; elles ne servent à aucun ciblage publicitaire.

Un bandeau vous permet, dès votre première visite, d'accepter ou de refuser ce suivi. Vous pouvez changer d'avis à tout moment en cliquant sur le lien « Cookies » présent en bas de chaque page, qui rouvre le panneau de réglages.
TEXT;

    public function up(): void
    {
        $cgu = Page::where('slug', 'cgu')->first();

        if (! $cgu) {
            return;
        }

        if ($cgu->blocks()->where('content->text', self::TEXT)->exists()) {
            return;
        }

        $nextOrder = ((int) $cgu->blocks()->max('sort_order')) + 1;

        $cgu->blocks()->create([
            'type' => 'text',
            'content' => ['text' => self::TEXT],
            'sort_order' => $nextOrder,
        ]);
    }

    public function down(): void
    {
        $cgu = Page::where('slug', 'cgu')->first();
        $cgu?->blocks()->where('content->text', self::TEXT)->delete();
    }
};
