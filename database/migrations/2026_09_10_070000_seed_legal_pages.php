<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Crée les 3 pages légales si elles n'existent pas déjà (slug), en
     * brouillon avec un texte de départ à compléter — jamais publiées
     * automatiquement : ce ne sont pas de vrais textes juridiques, juste une
     * structure de départ. L'administrateur doit les relire (et idéalement
     * les faire relire) avant de les publier.
     */
    private const PAGES = [
        [
            'slug' => 'mentions-legales',
            'title' => 'Mentions légales',
            'placeholder' => "À compléter avant publication (voir un professionnel du droit si besoin) :\n\n- Identité de l'éditeur du site (nom, statut, adresse, contact)\n- Nom et coordonnées de l'hébergeur\n- Directeur de la publication\n- Propriété intellectuelle des photographies",
        ],
        [
            'slug' => 'cgu',
            'title' => 'Conditions générales d\'utilisation',
            'placeholder' => "À compléter avant publication :\n\n- Objet du site et accès\n- Compte utilisateur (le cas échéant)\n- Propriété intellectuelle des contenus\n- Données personnelles collectées (voir la fonctionnalité de mentions RGPD à jour dans Focale : cookies de suivi anonyme pour les likes et les statistiques de visite)\n- Responsabilité et droit applicable",
        ],
        [
            'slug' => 'cgv',
            'title' => 'Conditions générales de vente',
            'placeholder' => "À compléter avant publication (utile dès qu'une commande — ex. livre photo — est proposée) :\n\n- Produits/services proposés et tarifs\n- Modalités de commande et de paiement\n- Livraison et délais\n- Droit de rétractation\n- Garanties et service après-vente",
        ],
    ];

    public function up(): void
    {
        foreach (self::PAGES as $definition) {
            $page = Page::firstOrCreate(
                ['slug' => $definition['slug']],
                [
                    'title' => $definition['title'],
                    'template' => 'default',
                    'status' => 'draft',
                    'show_in_nav' => false,
                ]
            );

            if ($page->wasRecentlyCreated) {
                $page->blocks()->create([
                    'type' => 'text',
                    'content' => ['text' => $definition['placeholder']],
                    'sort_order' => 0,
                ]);
            }
        }
    }

    public function down(): void
    {
        Page::whereIn('slug', array_column(self::PAGES, 'slug'))->delete();
    }
};
