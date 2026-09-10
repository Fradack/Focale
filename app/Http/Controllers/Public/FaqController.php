<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * Contenu statique — pas de section admin dédiée demandée pour l'instant,
     * juste une page qui explique le fonctionnement du site aux visiteurs.
     *
     * @return array<int, array{title: string, items: array<int, array{q: string, a: string}>}>
     */
    public static function sections(): array
    {
        return [
            [
                'title' => 'Parcourir le site',
                'items' => [
                    [
                        'q' => 'Comment sont organisées les photos ?',
                        'a' => "Les œuvres sont regroupées en albums (séries, reportages, projets). Chaque album a sa propre page, et chaque photo a aussi sa page dédiée avec ses informations techniques.",
                    ],
                    [
                        'q' => "Pourquoi un album me demande-t-il un mot de passe ?",
                        'a' => "Certains albums sont privés ou réservés (livraison client, travail en cours) : l'accès est protégé par un mot de passe communiqué directement par le photographe.",
                    ],
                    [
                        'q' => "Une photo récente ne s'affiche pas encore, pourquoi ?",
                        'a' => "Chaque photo est optimisée automatiquement après son ajout (plusieurs tailles générées pour un chargement rapide sur mobile comme sur grand écran). Ce traitement peut prendre de quelques secondes à quelques minutes selon la taille de la photo — elle apparaîtra dès qu'il est terminé, inutile de la renvoyer.",
                    ],
                ],
            ],
            [
                'title' => 'Likes, vues et vie privée',
                'items' => [
                    [
                        'q' => "Comment fonctionne le ❤ sur les photos ?",
                        'a' => "Un simple clic suffit, sans créer de compte. Le site retient votre like via un identifiant anonyme stocké dans votre navigateur (aucune adresse e-mail, aucune adresse IP) — chaque navigateur/appareil a son propre état, donc plusieurs personnes d'un même foyer peuvent chacune aimer une photo séparément.",
                    ],
                    [
                        'q' => "Le site me suit-il quand je navigue ?",
                        'a' => "Le site compte les visites et les likes de façon anonyme (un identifiant aléatoire par navigateur, jamais votre adresse IP, jamais d'identité). Ces informations servent uniquement au photographe pour comprendre quelles photos plaisent et à quelle fréquence le site est consulté.",
                    ],
                ],
            ],
            [
                'title' => 'Commentaires et contact',
                'items' => [
                    [
                        'q' => "Pourquoi mon commentaire n'apparaît-il pas tout de suite ?",
                        'a' => "Chaque commentaire est relu avant publication pour éviter le spam. Il apparaîtra sur la page de l'album dès qu'il aura été approuvé.",
                    ],
                    [
                        'q' => "Comment contacter le photographe ?",
                        'a' => "Le formulaire de la page Contact envoie directement un message — pas besoin de créer de compte.",
                    ],
                ],
            ],
        ];
    }

    public function index(): View
    {
        return view('public.faq', ['sections' => self::sections()]);
    }
}
