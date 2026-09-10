<?php

use App\Models\Block;
use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Remplace le texte "à compléter" (seed_legal_pages) par un vrai modèle
     * structuré selon les mentions légales/CGU/CGV usuelles en droit
     * français, avec des espaces réservés [ENTRE CROCHETS] pour les
     * informations que seul l'éditeur du site connaît (raison sociale,
     * adresse, hébergeur, etc.). Ce n'est PAS un avis juridique — voir
     * l'avertissement final de chaque document. N'écrase jamais un contenu
     * que l'admin aurait déjà modifié depuis le placeholder d'origine.
     */
    private const CONTENT = [
        'mentions-legales' => <<<'TEXT'
Éditeur du site

Le présent site est édité par : [NOM / RAISON SOCIALE], [statut juridique le cas échéant : entreprise individuelle, auto-entrepreneur, société XXX au capital de XXX €].
Adresse : [ADRESSE COMPLÈTE]
Numéro SIRET : [SIRET, si activité professionnelle déclarée]
E-mail : [ADRESSE E-MAIL DE CONTACT]
Téléphone : [NUMÉRO, facultatif]

Directeur de la publication

[NOM DU DIRECTEUR DE PUBLICATION] — [qualité, par exemple le photographe lui-même].

Hébergement

Le site est hébergé par : [NOM DE L'HÉBERGEUR]
Adresse : [ADRESSE DE L'HÉBERGEUR]
Site web : [SITE DE L'HÉBERGEUR]

Propriété intellectuelle

L'ensemble des photographies, textes, logos et éléments graphiques présents sur ce site sont la propriété exclusive de [NOM DE L'ÉDITEUR / DU PHOTOGRAPHE], sauf mention contraire. Toute reproduction, représentation, modification ou exploitation, totale ou partielle, sans autorisation écrite préalable, est interdite et constitue une contrefaçon sanctionnée par les articles L.335-2 et suivants du Code de la propriété intellectuelle.

Données personnelles

Le traitement des données personnelles collectées sur ce site (formulaire de contact, commentaires, identifiant anonyme de navigation pour les statistiques et les likes) est détaillé dans les Conditions générales d'utilisation. Conformément au Règlement général sur la protection des données (RGPD) et à la loi Informatique et Libertés, vous disposez d'un droit d'accès, de rectification et de suppression de vos données, à exercer auprès de [ADRESSE E-MAIL DE CONTACT].

Cookies

Ce site utilise un identifiant technique anonyme (cookie) nécessaire au fonctionnement de certaines fonctionnalités (likes, mesure d'audience). Aucune donnée personnelle identifiante n'est associée à cet identifiant.

Limitation de responsabilité

[NOM DE L'ÉDITEUR] s'efforce de fournir des informations aussi précises que possible sur ce site, mais ne pourra être tenu responsable des omissions, inexactitudes ou carences dans la mise à jour, qu'elles soient de son fait ou du fait de tiers.

Droit applicable

Les présentes mentions légales sont soumises au droit français. En cas de litige, et à défaut d'accord amiable, les tribunaux français seront seuls compétents.

—
Ceci est un modèle à compléter avec vos informations exactes. Faites-le relire par un professionnel si vous avez un doute, notamment sur votre statut juridique et vos obligations déclaratives (SIRET, TVA...).
TEXT,
        'cgu' => <<<'TEXT'
Article 1 — Objet

Les présentes Conditions générales d'utilisation (CGU) ont pour objet de définir les modalités d'accès et d'utilisation du site [NOM DU SITE] (ci-après « le Site »), édité par [NOM DE L'ÉDITEUR]. L'accès au Site implique l'acceptation pleine et entière des présentes CGU.

Article 2 — Accès au site

Le Site est accessible gratuitement à tout visiteur disposant d'un accès à internet. Certaines fonctionnalités (consultation d'albums protégés, création d'un compte) peuvent nécessiter un mot de passe ou une inscription.

Article 3 — Comptes utilisateurs

Un visiteur peut créer un compte personnel. Il s'engage à fournir des informations exactes et à conserver la confidentialité de son mot de passe. [NOM DE L'ÉDITEUR] se réserve le droit de suspendre ou supprimer tout compte en cas d'usage frauduleux ou contraire aux présentes CGU.

Article 4 — Propriété intellectuelle

L'ensemble des contenus du Site (photographies, textes, mise en page, éléments graphiques) est protégé par le droit d'auteur et reste la propriété exclusive de [NOM DE L'ÉDITEUR / DU PHOTOGRAPHE]. Toute utilisation non autorisée (reproduction, diffusion, modification) est strictement interdite.

Article 5 — Contenus publiés par les visiteurs

Les commentaires publiés par les visiteurs sont soumis à modération avant publication. [NOM DE L'ÉDITEUR] se réserve le droit de refuser ou supprimer tout commentaire contraire à la loi, aux bonnes mœurs, ou aux présentes CGU (propos injurieux, diffamatoires, publicitaires, etc.).

Article 6 — Données personnelles et cookies

Le Site collecte :
- les données transmises volontairement via les formulaires (contact, commentaires, création de compte) ;
- un identifiant anonyme (cookie technique) permettant de compter les likes, les vues de photos et les visites, sans jamais associer ces informations à une adresse IP ou à une identité.

Ces données sont traitées conformément au RGPD. Elles ne sont ni vendues ni transmises à des tiers à des fins commerciales. Vous pouvez exercer vos droits d'accès, de rectification et de suppression en écrivant à [ADRESSE E-MAIL DE CONTACT].

Article 7 — Responsabilité

[NOM DE L'ÉDITEUR] ne saurait être tenu responsable des dommages directs ou indirects résultant de l'utilisation du Site, notamment en cas d'indisponibilité temporaire, de bug ou de perte de données. L'utilisateur est seul responsable de l'usage qu'il fait du Site.

Article 8 — Modification des CGU

[NOM DE L'ÉDITEUR] se réserve le droit de modifier les présentes CGU à tout moment. Les utilisateurs sont invités à les consulter régulièrement.

Article 9 — Droit applicable et juridiction

Les présentes CGU sont régies par le droit français. Tout litige relatif à leur interprétation ou leur exécution relève de la compétence des tribunaux français.

—
Ceci est un modèle à compléter et à faire relire par un professionnel avant publication, en particulier si le Site propose ou proposera une fonctionnalité de commande (livre photo, tirages) — voir alors aussi les CGV.
TEXT,
        'cgv' => <<<'TEXT'
Les présentes Conditions générales de vente (CGV) s'appliquent à toute commande passée sur le site [NOM DU SITE], édité par [NOM DE L'ÉDITEUR]. Elles n'ont vocation à s'appliquer qu'à partir du moment où une fonctionnalité de commande (par exemple un livre photo) est effectivement proposée sur le Site.

Article 1 — Produits et services

[NOM DE L'ÉDITEUR] propose à la vente [DESCRIPTION DES PRODUITS : par exemple tirages photographiques, livres photo personnalisés]. Les caractéristiques essentielles de chaque produit sont présentées sur sa fiche.

Article 2 — Prix

Les prix sont indiqués en euros, [toutes taxes comprises / hors taxes — à préciser selon votre statut]. [NOM DE L'ÉDITEUR] se réserve le droit de modifier ses prix à tout moment, les produits étant facturés sur la base du tarif en vigueur au moment de la validation de la commande.

Article 3 — Commande

Toute commande passée sur le Site suppose l'acceptation pleine et entière des présentes CGV. Une confirmation de commande est envoyée par e-mail à l'adresse renseignée par le client.

Article 4 — Paiement

Le paiement s'effectue [MODALITÉS DE PAIEMENT à préciser] au moment de la commande. [NOM DE L'ÉDITEUR] ne conserve aucune donnée bancaire.

Article 5 — Livraison

Les produits sont [expédiés à l'adresse indiquée par le client / remis en main propre — à préciser], dans un délai indicatif de [DÉLAI] à compter de la validation de la commande. Les délais indiqués sont donnés à titre informatif.

Article 6 — Droit de rétractation

Conformément à l'article L221-18 du Code de la consommation, le client dispose en principe d'un délai de 14 jours pour exercer son droit de rétractation. ATTENTION — à vérifier avec un professionnel : les produits confectionnés selon les spécifications du client ou nettement personnalisés (par exemple un livre photo réalisé à partir de ses propres photos) sont généralement exclus de ce droit en application de l'article L221-28 3° du Code de la consommation, mais cette exclusion doit être confirmée pour votre cas précis avant d'être affichée comme définitive.

Article 7 — Garanties légales

Tout produit vendu bénéficie de la garantie légale de conformité (articles L217-3 et suivants du Code de la consommation) et de la garantie contre les vices cachés (articles 1641 et suivants du Code civil).

Article 8 — Réclamations et médiation

Pour toute réclamation, le client peut contacter [ADRESSE E-MAIL DE CONTACT]. OBLIGATOIRE avant mise en ligne si vous vendez à des particuliers : conformément à l'article L616-1 du Code de la consommation, vous devez proposer un médiateur de la consommation ([NOM DU MÉDIATEUR À DÉSIGNER ET À FAIRE FIGURER ICI]) — ce n'est pas une formalité optionnelle.

Article 9 — Droit applicable et juridiction

Les présentes CGV sont soumises au droit français. Tout litige relève de la compétence des tribunaux français.

—
Ceci est un modèle générique à adapter impérativement à votre activité réelle et à faire valider par un professionnel du droit avant toute mise en ligne — en particulier les articles 6 (rétractation) et 8 (médiateur), qui comportent des obligations légales strictes dès qu'une vente à un particulier est proposée.
TEXT,
    ];

    private const PLACEHOLDER_MARKER = 'À compléter avant publication';

    public function up(): void
    {
        foreach (self::CONTENT as $slug => $text) {
            $page = Page::where('slug', $slug)->first();
            if (! $page) {
                continue;
            }

            $block = $page->blocks()->first();
            if (! $block) {
                continue;
            }

            $currentText = $block->content['text'] ?? '';
            if (! str_contains($currentText, self::PLACEHOLDER_MARKER)) {
                // Déjà modifié par l'admin depuis le placeholder d'origine — on ne touche à rien.
                continue;
            }

            $block->update(['content' => ['text' => $text]]);
        }
    }

    public function down(): void
    {
        //
    }
};
