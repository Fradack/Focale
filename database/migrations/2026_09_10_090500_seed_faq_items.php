<?php

use App\Models\FaqItem;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Contenu de départ (déjà rédigé pour le premier lancement de la FAQ) —
     * insère seulement si la table est vide, pour ne jamais écraser du
     * contenu que l'admin aurait déjà modifié via /administration/faq.
     */
    private const ITEMS = [
        ['category' => 'Parcourir le site', 'question' => 'Comment sont organisées les photos ?', 'answer' => "Les œuvres sont regroupées en albums (séries, reportages, projets). Chaque album a sa propre page, et chaque photo a aussi sa page dédiée avec ses informations techniques."],
        ['category' => 'Parcourir le site', 'question' => "Pourquoi un album me demande-t-il un mot de passe ?", 'answer' => "Certains albums sont privés ou réservés (livraison client, travail en cours) : l'accès est protégé par un mot de passe communiqué directement par le photographe."],
        ['category' => 'Parcourir le site', 'question' => "Une photo récente ne s'affiche pas encore, pourquoi ?", 'answer' => "Chaque photo est optimisée automatiquement après son ajout (plusieurs tailles générées pour un chargement rapide sur mobile comme sur grand écran). Ce traitement peut prendre de quelques secondes à quelques minutes selon la taille de la photo — elle apparaîtra dès qu'il est terminé, inutile de la renvoyer."],
        ['category' => 'Likes, vues et vie privée', 'question' => "Comment fonctionne le ❤ sur les photos ?", 'answer' => "Un simple clic suffit, sans créer de compte. Le site retient votre like via un identifiant anonyme stocké dans votre navigateur (aucune adresse e-mail, aucune adresse IP) — chaque navigateur/appareil a son propre état, donc plusieurs personnes d'un même foyer peuvent chacune aimer une photo séparément."],
        ['category' => 'Likes, vues et vie privée', 'question' => "Le site me suit-il quand je navigue ?", 'answer' => "Le site compte les visites et les likes de façon anonyme (un identifiant aléatoire par navigateur, jamais votre adresse IP, jamais d'identité). Ces informations servent uniquement au photographe pour comprendre quelles photos plaisent et à quelle fréquence le site est consulté."],
        ['category' => 'Commentaires et contact', 'question' => "Pourquoi mon commentaire n'apparaît-il pas tout de suite ?", 'answer' => "Chaque commentaire est relu avant publication pour éviter le spam. Il apparaîtra sur la page de l'album dès qu'il aura été approuvé."],
        ['category' => 'Commentaires et contact', 'question' => "Comment contacter le photographe ?", 'answer' => "Le formulaire de la page Contact envoie directement un message — pas besoin de créer de compte."],
    ];

    public function up(): void
    {
        if (FaqItem::count() > 0) {
            return;
        }

        foreach (self::ITEMS as $i => $item) {
            FaqItem::create($item + ['sort_order' => $i]);
        }
    }

    public function down(): void
    {
        //
    }
};
