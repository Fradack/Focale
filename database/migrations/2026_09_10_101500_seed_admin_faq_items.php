<?php

use App\Models\FaqItem;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const ITEMS = [
        ['category' => 'Import et traitement des photos', 'question' => "Pourquoi une photo reste \"en cours de traitement\" un moment après son import ?", 'answer' => "Chaque photo est redimensionnée en 3 tailles (vignette, web, rétina) et convertie en WebP. Sur cet hébergement il n'y a pas de processus permanent dédié à ce traitement : il n'avance que pendant qu'une page de l'administration reste ouverte et active. Une photo très volumineuse peut prendre plus de temps, ou dans de rares cas bloquer la file — voir la question suivante."],
        ['category' => 'Import et traitement des photos', 'question' => "Des œuvres restent bloquées en traitement depuis longtemps, que faire ?", 'answer' => "Dans Médiathèque, si des œuvres sont en traitement depuis plus de 30 minutes, un bandeau d'avertissement apparaît avec un bouton \"Vider les œuvres bloquées\" qui les met à la corbeille (réversible) et débloque la file pour les suivantes. Une œuvre qui vient d'être importée n'est jamais concernée par ce bouton."],
        ['category' => 'Import et traitement des photos', 'question' => "À quoi sert le réglage \"Import une photo par une photo\" ?", 'answer' => "Activé (recommandé, par défaut), chaque photo doit être entièrement envoyée ET traitée à 100% avant que la suivante ne démarre — ça évite de surcharger un hébergement modeste. Il désactive aussi l'import de masse depuis un dossier serveur tant qu'il est actif. Réglable dans Réglages → Médiathèque."],
        ['category' => 'Import et traitement des photos', 'question' => "Pourquoi une vidéo n'a pas de vraie miniature ?", 'answer' => "Générer une image d'aperçu à partir d'une vidéo nécessite un outil (ffmpeg) qui n'est pas installé sur cet hébergement. Une tuile générique s'affiche à la place dans la médiathèque et les albums — la vidéo elle-même se lit normalement sur sa page."],
        ['category' => 'Statistiques et visiteurs', 'question' => "Que veut dire \"visiteur unique\" dans les statistiques ?", 'answer' => "Chaque visiteur reçoit un identifiant anonyme stocké dans son navigateur (aucune adresse IP, aucune identité). \"Visiteur unique\" compte le nombre d'identifiants différents sur la période, peu importe le nombre de pages qu'il a vues."],
        ['category' => 'Statistiques et visiteurs', 'question' => "D'où viennent les compteurs de likes et de vues ?", 'answer' => "Un like est enregistré une fois par (photo, identifiant anonyme) — cliquer à nouveau retire le like. Une vue n'est comptée qu'après 10 secondes de consultation réelle de la page d'une photo, une fois par (photo, visiteur, jour) pour éviter qu'un simple rafraîchissement ne gonfle le chiffre."],
        ['category' => 'Comptes et utilisateurs', 'question' => "Quelle différence entre les rôles Admin / Éditeur / Contributeur / Lecteur ?", 'answer' => "Ces rôles existent dans la gestion des utilisateurs (Utilisateurs) pour l'équipe. Ils sont distincts des comptes clients (inscription publique cachée, jamais liée dans la navigation) qui n'ont jamais accès à l'administration, quel que soit leur rôle technique."],
        ['category' => 'Comptes et utilisateurs', 'question' => "Où gérer les mentions légales, CGU et CGV ?", 'answer' => "Dans Documents légaux, qui pointe directement vers ces 3 pages. Ce sont des pages classiques (comme \"À propos\") : une fois leur contenu complété et leur statut passé à \"Publiée\", elles apparaissent automatiquement dans le pied de page du site public."],
        ['category' => 'Apparence', 'question' => "Comment activer le thème sombre ?", 'answer' => "Dans Réglages → Apparence, le menu \"Thème sombre\" permet de l'appliquer au site public, à l'administration, aux deux, ou de le désactiver. C'est un réglage global choisi par vous, pas un interrupteur laissé aux visiteurs."],
    ];

    public function up(): void
    {
        if (FaqItem::where('audience', 'admin')->count() > 0) {
            return;
        }

        foreach (self::ITEMS as $i => $item) {
            FaqItem::create($item + ['audience' => 'admin', 'sort_order' => $i]);
        }
    }

    public function down(): void
    {
        //
    }
};
