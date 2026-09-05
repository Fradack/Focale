Tu es un lead developer, product designer et architecte logiciel. Ta mission est de concevoir puis développer **Focale**, un CMS auto-hébergé destiné à un artiste, photographe, designer, illustrateur ou studio créatif souhaitant publier ses propres créations sur son propre site internet.

## 1. Contexte et vision

Focale est une alternative moderne, indépendante et durable à Koken.

Il ne s’agit pas d’un service de stockage de photos en ligne, d’un réseau social, d’une marketplace ou d’une plateforme SaaS accueillant les créations de milliers d’utilisateurs.

Focale est un **CMS personnel auto-hébergé** : une personne ou un studio l’installe sur son hébergement et son nom de domaine pour gérer son propre portfolio, ses projets, ses images, ses textes et l’identité visuelle de son site.

L’objectif est de permettre à un artiste non technique de présenter son travail avec élégance, sans dépendre d’une plateforme fermée et sans avoir à développer son site à la main.

Le produit doit être :

- Visuellement minimal, éditorial et centré sur les œuvres.
- Rapide, notamment pour les portfolios photographiques.
- Facile à administrer.
- Respectueux des données et des fichiers de l’artiste.
- Simple à auto-héberger et à sauvegarder.
- Durable, maintenable et extensible.
- Adapté à une personne, un artiste ou un petit studio.

## 2. Positionnement précis

Focale permet à un artiste de :

- Téléverser et organiser ses propres œuvres.
- Créer des portfolios, séries, albums, projets ou expositions.
- Ajouter titres, légendes, descriptions, crédits, dates et informations techniques.
- Créer des pages comme « À propos », « Contact », « Presse » ou « Expositions ».
- Choisir une présentation visuelle adaptée à son univers.
- Publier le tout sur son propre domaine.

Focale ne doit pas inclure dans son cœur de produit :

- Création de comptes publics.
- Inscription libre de nouveaux artistes.
- Fonctionnalités multi-tenant ou multi-sites.
- Stockage partagé entre des utilisateurs.
- Abonnements, paiement ou facturation.
- Flux social, likes, commentaires publics ou messagerie.
- Marketplace ou vente en ligne dans le MVP.
- Gestion de clients complexe.

L’instance installée correspond à un seul site d’artiste. Une gestion de rôles simples pour quelques collaborateurs pourra être ajoutée plus tard, mais l’application doit avant tout servir un propriétaire principal.

## 3. Utilisateurs cibles

- Photographes.
- Designers graphiques.
- Directeurs artistiques.
- Illustrateurs.
- Artistes plasticiens.
- Architectes.
- Studios créatifs.
- Toute personne présentant un travail visuel sous forme de portfolio.

L’interface doit être disponible en français au départ, tout en étant prête à être traduite plus tard.

## 4. Expérience recherchée

Le visiteur doit avoir la sensation d’entrer dans un portfolio soigné, silencieux et rapide, où les œuvres occupent la place principale.

L’administrateur doit avoir la sensation de ranger et d’accrocher ses œuvres dans un atelier numérique : une médiathèque claire, des projets faciles à composer et une publication sans friction.

Le produit ne doit jamais ressembler à un tableau de bord d’entreprise froid, à un site de stockage de fichiers ou à un réseau social.

## 5. Fonctionnalités de l’administration

Créer une interface privée sécurisée, accessible par exemple via `/admin`.

### 5.1 Tableau de bord

Prévoir un tableau de bord simple comprenant :

- Nombre total d’œuvres et de projets.
- Derniers projets modifiés.
- Dernières œuvres importées.
- Brouillons à finaliser.
- Raccourcis vers « Importer des œuvres » et « Créer un projet ».
- État du site : public ou en maintenance.
- Informations simples sur l’espace disque utilisé, si elles sont disponibles.

Ne pas surcharger cet écran avec des statistiques inutiles.

### 5.2 Médiathèque personnelle

La médiathèque sert uniquement à organiser les œuvres de l’artiste dans cette instance.

Elle doit permettre :

- L’import d’images par glisser-déposer.
- L’import de plusieurs fichiers en une fois.
- L’affichage d’une progression claire pendant l’import.
- La conservation du fichier original.
- La création automatique de miniatures et de formats optimisés pour le web.
- La prise en charge initiale de JPEG, PNG, WebP et GIF.
- La prise en charge de SVG uniquement lorsque l’affichage est sécurisé.
- Une architecture permettant l’ajout ultérieur d’AVIF, TIFF et vidéo.
- La détection des doublons à partir d’une empreinte de fichier.
- L’affichage en grille et en liste.
- La prévisualisation d’un média en grand format.
- Une corbeille avec restauration possible avant suppression définitive.
- Les actions groupées : ajout de tags, déplacement, publication, dépublication ou suppression.
- La recherche par titre, tag, date ou type de fichier.
- Des filtres par orientation, type de fichier, date d’import et statut.

Chaque œuvre doit pouvoir contenir les informations suivantes :

- Titre.
- Slug ou identifiant interne.
- Texte alternatif.
- Légende.
- Description.
- Crédit.
- Copyright.
- Auteur.
- Licence.
- Date de création.
- Lieu, si pertinent.
- Tags et catégories.
- Statut de publication.
- Texte de remplacement ou note interne éventuelle.

Pour les photographies, lire les données EXIF/IPTC quand elles existent :

- Appareil.
- Objectif.
- Focale.
- Ouverture.
- Vitesse.
- ISO.
- Date de prise de vue.
- Coordonnées GPS.

Prévoir une option afin de masquer ou retirer les données GPS et métadonnées sensibles sur les versions publiques.

### 5.3 Projets, portfolios et séries

Les projets sont la principale manière de présenter le travail de l’artiste.

Un projet peut représenter :

- Une série photographique.
- Un reportage.
- Une collection.
- Une exposition.
- Une identité visuelle.
- Un projet éditorial.
- Un ensemble d’illustrations.
- Un travail personnel ou une commande.

Chaque projet doit contenir :

- Titre.
- Slug d’URL modifiable.
- Image de couverture.
- Texte d’introduction.
- Date de création.
- Date de publication.
- Tags et catégories.
- Crédits ou collaborateurs.
- Liens externes facultatifs.
- Statut : brouillon, non répertorié, publié ou archivé.
- Visibilité : publique, protégée par mot de passe ou privée.
- Titre SEO, description SEO et image de partage.
- Liste ordonnée de médias.
- Choix d’une mise en page.

Fonctionnalités attendues :

- Ajouter plusieurs œuvres d’un coup à un projet.
- Réorganiser les œuvres par glisser-déposer.
- Réutiliser la même œuvre dans plusieurs projets sans la dupliquer.
- Définir une œuvre de couverture.
- Prévisualiser le rendu du projet avant publication.
- Naviguer facilement entre projet précédent et suivant.
- Dupliquer un projet comme point de départ.
- Archiver un projet sans le supprimer.

Prévoir plusieurs modes de présentation, mais n’en développer qu’un ou deux excellents pour le MVP :

- Grille éditoriale.
- Mosaïque adaptative.
- Diaporama plein écran.
- Mise en page narrative avec texte et images.

### 5.4 Collections

Les collections permettent de regrouper plusieurs projets.

Exemples :

- Portraits.
- Reportages.
- Identités visuelles.
- Expositions.
- Archives.
- Commandes.
- Travaux personnels.

Une collection doit pouvoir avoir un titre, un texte, une image de couverture, un slug, un ordre de projets et des métadonnées SEO.

Cette fonctionnalité peut être livrée après le MVP si nécessaire, mais le modèle de données doit anticiper ce besoin.

### 5.5 Pages de contenu

Créer un système de pages simple pour les pages qui ne sont pas des portfolios :

- Accueil.
- À propos.
- Contact.
- Presse.
- Expositions.
- Clients ou publications.
- Mentions légales.
- Politique de confidentialité.
- Pages personnalisées.

L’édition des pages doit être basée sur des blocs simples et fiables :

- Titre.
- Texte riche limité et sécurisé.
- Image.
- Galerie.
- Citation.
- Séparateur.
- Lien.
- Bouton.
- Vidéo embarquée provenant de sources autorisées.
- Code HTML limité, filtré et réservé à l’administrateur.

Ne pas créer un constructeur de pages complexe. La priorité est une bonne hiérarchie éditoriale, une belle typographie et des composants robustes.

### 5.6 Journal facultatif

Le CMS pourra inclure un journal ou blog léger, mais cela ne doit pas prendre le pas sur les portfolios.

Fonctionnalités :

- Articles en brouillon ou publiés.
- Titre, date, image de couverture, contenu et tags.
- URL propre.
- Navigation entre articles.
- Désactivation complète possible depuis les réglages.

Cette fonctionnalité peut être reportée après la première version utilisable.

### 5.7 Navigation et réglages

Créer une section de réglages permettant de :

- Définir le nom du site.
- Ajouter un logo, un favicon et une image de partage par défaut.
- Renseigner le nom de l’artiste ou du studio.
- Ajouter une courte biographie.
- Ajouter une adresse e-mail de contact.
- Ajouter les liens vers les réseaux sociaux.
- Créer et ordonner le menu principal.
- Créer et ordonner le pied de page.
- Choisir la page d’accueil.
- Activer ou désactiver le journal.
- Modifier les couleurs, la typographie et la largeur de contenu du thème.
- Activer ou désactiver le mode sombre si le thème le permet.
- Configurer la langue, le fuseau horaire et les formats de dates.
- Activer un mode maintenance.
- Configurer un serveur SMTP pour le formulaire de contact.
- Ajouter, de manière facultative, un outil de mesure d’audience respectueux de la vie privée.
- Gérer les redirections 301.
- Gérer les paramètres SEO globaux.
- Générer un sitemap XML et un fichier robots.txt.

## 6. Site public

Le site public doit être rapide, responsive, élégant et entièrement consacré au travail de l’artiste.

Prévoir les pages suivantes :

- Accueil.
- Liste des projets.
- Page individuelle d’un projet.
- Liste et page des collections, si cette fonction est activée.
- Page À propos.
- Page Contact.
- Page Journal et article individuel, si cette fonction est activée.
- Page de recherche légère, si elle est pertinente.
- Page 404 soignée.
- Page de maintenance.

### 6.1 Accueil

L’accueil doit être facilement configurable et permettre au minimum :

- Une œuvre, image ou vidéo de couverture.
- Une courte présentation de l’artiste.
- Une sélection de projets mis en avant.
- Un lien vers les projets.
- Un lien vers la page À propos ou Contact.

Ne pas imposer une structure rigide : l’artiste doit pouvoir choisir une approche minimaliste ou plus éditoriale.

### 6.2 Présentation des œuvres

Pour les images du site public :

- Utiliser des formats responsives avec `srcset` et `sizes`.
- Charger les images de manière optimisée.
- Utiliser le lazy loading sans dégrader l’image principale.
- Précharger uniquement les éléments réellement importants.
- Générer des images adaptées aux écrans haute résolution.
- Proposer une lightbox élégante et accessible.
- Permettre la navigation au clavier.
- Afficher les légendes et crédits si l’artiste le souhaite.
- Rendre l’affichage plein écran possible lorsque pertinent.
- Respecter les préférences `prefers-reduced-motion`.
- Prévoir une protection légère contre le clic droit uniquement comme option esthétique ; ne jamais promettre qu’elle empêche le téléchargement.
- Permettre un filigrane sur les versions publiques uniquement, jamais sur les originaux.

## 7. Design et thèmes

Créer une base de thème extensible mais cohérente.

Le premier thème doit être :

- Minimal.
- Editorial.
- Intemporel.
- Raffiné.
- Très lisible.
- Centré sur les images.
- Responsive de façon exemplaire.

Principes visuels :

- Beaucoup d’espace blanc.
- Typographie soignée.
- Grandes images lorsque le contexte s’y prête.
- Grilles capables d’accueillir formats verticaux, horizontaux et carrés.
- Animations discrètes.
- Aucun effet décoratif nuisant à la vitesse ou à l’œuvre.
- Contrastes accessibles.
- Navigation claire.
- Excellent affichage sur mobile, tablette, ordinateur et écran large.

Le thème doit exposer quelques variables contrôlées :

- Couleur de fond.
- Couleur du texte.
- Couleur d’accent.
- Police des titres.
- Police du texte courant.
- Taille de typographie globale.
- Largeur maximale du contenu.
- Rayon des éléments visuels lorsque nécessaire.

Éviter une quantité excessive d’options. Les réglages doivent préserver une direction artistique de qualité.

## 8. Authentification et gestion des accès

Pour le MVP :

- Un administrateur principal.
- Connexion sécurisée par e-mail et mot de passe.
- Mots de passe correctement hachés.
- Réinitialisation de mot de passe par e-mail.
- Limitation des tentatives de connexion.
- Sessions sécurisées.
- Déconnexion de toutes les sessions si nécessaire.

Prévoir dans l’architecture, sans forcément l’implémenter au départ :

- Administrateur.
- Éditeur.
- Contributeur.
- Lecture seule.
- Authentification à deux facteurs.

Aucun visiteur public ne doit avoir besoin de créer un compte.

## 9. Formulaire de contact

Créer un formulaire de contact simple avec :

- Nom.
- Adresse e-mail.
- Sujet.
- Message.
- Page de confirmation.
- Envoi vers une adresse e-mail configurable via SMTP.
- Honeypot anti-spam.
- Limitation de fréquence.
- CAPTCHA uniquement en option.
- Validation côté serveur.
- Conservation facultative des messages dans l’administration.

Prévoir une base respectueuse du RGPD, notamment une information claire lorsque les messages sont conservés.

## 10. SEO, partage et visibilité

Mettre en place une base SEO solide sans dépendre d’un plugin externe.

Prévoir :

- Balise `title` et meta description configurables.
- URLs propres, lisibles et stables.
- Slugs modifiables.
- Redirections 301 lors d’un changement d’URL.
- Balises canonical.
- Sitemap XML automatique.
- robots.txt configurable.
- Open Graph.
- Twitter Cards.
- Image de partage spécifique à chaque projet si nécessaire.
- Données structurées JSON-LD appropriées : `WebSite`, `Person` ou `Organization`, `CreativeWork`, `ImageObject`, `Article`.
- Bon affichage lors du partage sur les réseaux sociaux.
- Pages performantes et respectueuses des Core Web Vitals.

## 11. Architecture technique recommandée

Construire l’application comme un projet auto-hébergeable et maintenable sur le long terme.

Recommandation principale :

- Backend : PHP moderne avec Laravel.
- Base de données : MySQL ou MariaDB.
- Frontend : rendu côté serveur avec composants légers ; éviter une application JavaScript lourde.
- Interface d’administration : composants modernes et accessibles, avec une expérience fluide pour l’import et le classement des œuvres.
- Stockage local des médias par défaut.
- Abstraction de stockage permettant, plus tard et de manière facultative, l’usage de S3, Backblaze B2 ou MinIO.
- Traitement des images dans une file d’attente.
- File d’attente compatible avec une installation simple.
- Cache applicatif et cache HTTP.
- Génération d’images et de miniatures robuste.
- API interne propre, mais sans transformer le MVP en CMS headless.
- Docker Compose pour l’installation locale et sur VPS.
- Installation également documentée pour un hébergement PHP classique, lorsque cela est réaliste.
- Migrations de base de données versionnées.
- Fichier `.env.example` complet et commenté.
- Tests automatisés sur les fonctions essentielles.

Si tu recommandes une autre stack, explique clairement pourquoi elle améliore l’auto-hébergement, la maintenance ou la performance sans rendre le projet inutilement complexe.

## 12. Sécurité, confidentialité et fiabilité

Mettre en place les protections suivantes :

- Validation stricte de tous les fichiers importés.
- Vérification du vrai type MIME des fichiers.
- Limites configurables de taille et de format.
- Stockage des médias hors des répertoires exécutables lorsque possible.
- Protection CSRF.
- Protection contre les injections SQL.
- Protection contre les XSS.
- Échappement systématique du contenu public.
- Contrôle d’accès strict dans l’administration.
- En-têtes HTTP de sécurité appropriés.
- Journalisation d’erreurs sans fuite d’informations sensibles.
- Protection contre les tentatives de connexion répétées.
- Sauvegarde claire de la base de données et des médias.
- Export documenté des données et des contenus.
- Aucune télémétrie obligatoire.
- Aucun service tiers obligatoire pour que le CMS fonctionne.

## 13. Modèle de données

Définir un schéma de données clair comprenant au minimum :

- Utilisateurs.
- Réglages du site.
- Médias.
- Variantes de médias.
- Métadonnées techniques.
- Tags.
- Catégories.
- Projets.
- Relations entre projets et médias avec ordre personnalisable.
- Collections.
- Relations entre collections et projets avec ordre personnalisable.
- Pages.
- Blocs de contenu.
- Articles de journal, si activés.
- Menus et éléments de menu.
- Redirections.
- Messages de contact.
- Journal d’activité.
- Corbeille ou suppression différée.

Un même média peut appartenir à plusieurs projets sans être dupliqué.

## 14. Performance et accessibilité

Le site doit être capable d’accueillir un portfolio conséquent, y compris plusieurs milliers d’œuvres, sans devenir lent.

Prévoir :

- Pagination et chargement progressif dans la médiathèque d’administration.
- Traitement asynchrone des variantes d’image.
- Images redimensionnées selon les besoins réels de l’affichage.
- Compression visuellement propre.
- Requêtes de base de données indexées et optimisées.
- Mise en cache des pages publiques.
- Purge ciblée du cache après publication ou modification.
- Peu de JavaScript et des dépendances limitées.
- CDN facultatif, jamais obligatoire.

Respecter au minimum les principes WCAG 2.2 AA :

- Navigation au clavier.
- Focus visible.
- Contrastes suffisants.
- Textes alternatifs.
- Hiérarchie de titres correcte.
- Labels explicites dans les formulaires.
- Messages d’erreur compréhensibles.
- Compatibilité lecteur d’écran.
- Respect de `prefers-reduced-motion`.

## 15. MVP : périmètre à livrer en premier

Commencer par un MVP réellement utilisable pour un artiste qui veut mettre son portfolio en ligne.

Le MVP doit inclure :

1. Installation locale documentée.
2. Authentification administrateur.
3. Tableau de bord minimal.
4. Import de photos dans une médiathèque personnelle.
5. Génération de miniatures et d’images responsives.
6. Édition des informations essentielles d’une œuvre.
7. Création, modification, brouillon et publication d’un projet.
8. Ajout et réorganisation d’œuvres dans un projet.
9. Choix d’une image de couverture.
10. Thème public minimal, rapide et responsive.
11. Page d’accueil.
12. Liste des projets.
13. Page projet avec grille d’images et lightbox.
14. Page À propos configurable.
15. Formulaire de contact sécurisé.
16. Réglages essentiels du site.
17. SEO de base, sitemap et métadonnées de partage.
18. Documentation de sauvegarde et de restauration.

Reporter après le MVP :

- Collections avancées.
- Éditeur par blocs complet.
- Journal/blog.
- Plusieurs rôles utilisateurs.
- Pages protégées par mot de passe.
- Thèmes supplémentaires.
- Import depuis Koken ou une arborescence existante.
- Stockage objet externe.
- Vidéo.
- Multilingue.
- API publique.
- Plugins.
- Authentification à deux facteurs.
- Vente de tirages ou espace client.

## 16. Livrables attendus

Avant le développement, fournir :

1. Une synthèse du produit et de ses limites.
2. Une proposition d’architecture technique argumentée.
3. Une arborescence de projet claire.
4. Un schéma de base de données.
5. Des wireframes textuels des écrans importants.
6. Un découpage précis du MVP en étapes de développement.
7. Les choix de sécurité, de performance et de déploiement.

Pendant le développement, fournir :

1. Le code source du MVP.
2. Les migrations de base de données.
3. Les modèles, contrôleurs, services et composants nécessaires.
4. Le thème public.
5. Les tests essentiels.
6. Un fichier `.env.example`.
7. Une configuration Docker Compose.
8. La documentation d’installation locale.
9. La documentation d’installation sur serveur.
10. La documentation de sauvegarde, restauration et mise à jour.
11. Une liste claire des fonctions livrées et reportées.

## 17. Méthode de travail

Avant de coder :

- Résume les décisions techniques.
- Signale les compromis importants.
- Valide que le périmètre reste celui d’un CMS personnel d’artiste.
- Propose le plan du MVP.
- Pose uniquement les questions qui bloquent réellement la suite.

Pendant le développement :

- Construis une base fonctionnelle avant les raffinements.
- Donne la priorité à la publication et à la présentation des œuvres.
- N’ajoute pas de fonctionnalités de cloud, réseau social ou SaaS.
- Évite les dépendances inutiles.
- Garde l’interface simple pour un artiste non technique.
- Écris du code clair, testé, sécurisé et documenté.
- Vérifie le rendu sur mobile.
- Ne sacrifie jamais la rapidité pour des effets visuels inutiles.
- Préserve la possibilité de faire évoluer le produit sans surarchitecturer le MVP.

Le résultat final doit être un outil calme, beau, fiable et autonome : un véritable atelier numérique personnel permettant à un artiste de maîtriser entièrement la publication de son travail.

j'aimerais que la page d'installation soit une page ou l'utilisateur renseigne ces informations personnelles Nom prenom, adresse email. mot de passe , otp. -> etape 2 connexion a la db du client. etape 3 installation du cms depuis mon serveur (les fichiers seront sur mon serveur cela permettra de mettre a jour son site et le mien aussi donc implementer un systeme de mise a jour et de detection de maj)