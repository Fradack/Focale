# Focale — Synthèse produit & architecture technique (avant développement)

> Document de cadrage produit par l'IA "lead dev / architecte" avant le début du développement, conformément au périmètre défini.

---

## 1. Synthèse du produit et de ses limites

**Focale** est un CMS auto-hébergé mono-utilisateur (extensible à quelques collaborateurs plus tard) destiné à un artiste/photographe/designer qui veut publier son portfolio sur son propre nom de domaine.

**Ce que Focale EST :**
- Un outil d'édition et de publication de portfolio (projets, œuvres, pages de contenu).
- Une médiathèque personnelle avec métadonnées riches (EXIF/IPTC, crédits, licences).
- Un site public rapide, éditorial, orienté image.
- Un logiciel qu'on installe soi-même (Docker Compose ou hébergement PHP classique).

**Ce que Focale N'EST PAS (hors périmètre du cœur produit) :**
- Une plateforme SaaS multi-tenant, un réseau social, une marketplace.
- Un service de stockage cloud grand public.
- Un site avec inscription publique de visiteurs.

**Limite structurante à valider dès maintenant :** une instance = un site = un propriétaire principal. Le modèle de données prévoit les rôles (éditeur, contributeur, lecture seule) mais seul le rôle admin est implémenté au MVP.

---

## 2. Architecture technique retenue

La stack recommandée dans le brief est confirmée, avec quelques précisions :

| Couche | Choix | Justification |
|---|---|---|
| Backend | **Laravel 11** (PHP 8.3+) | Écosystème mature pour l'auto-hébergement, ORM Eloquent adapté aux relations projet↔médias, queues intégrées, migrations versionnées natives, grande base d'hébergeurs PHP mutualisés compatibles. |
| Base de données | **MySQL 8 / MariaDB 10.11** | Standard chez les hébergeurs PHP, JSON columns pour métadonnées EXIF flexibles. |
| Frontend admin | **Livewire + Alpine.js** (pas de SPA React/Vue séparée) | Évite une API JS lourde et un build front séparé ; reste "léger" comme demandé, tout en donnant une UX réactive pour le drag & drop de médiathèque. |
| Frontend public | **Blade + Tailwind CSS**, rendu serveur | Rapide, peu de JS, bon pour Core Web Vitals, aucune hydratation lourde nécessaire pour un portfolio. |
| Traitement d'images | **Laravel Queue + Intervention Image / libvips (via `spatie/image`)** | Génération asynchrone des variantes (thumbnail, web, retina) sans bloquer l'upload. |
| File d'attente | **Database driver** par défaut, **Redis** en option | La database queue driver fonctionne sans dépendance supplémentaire sur hébergement mutualisé ; Redis recommandé en VPS pour de meilleures perfs. |
| Stockage | **Laravel Filesystem (local disk)** par défaut, driver S3-compatible en option (MinIO/B2/S3) | Abstraction déjà native à Laravel, aucun développement custom nécessaire. |
| Cache | **Cache file/database** par défaut, Redis en option | Pages publiques cachées via cache HTTP applicatif (tags Laravel Cache), purge ciblée par projet/page. |
| Déploiement | **Docker Compose** (app + MySQL + Redis optionnel) pour VPS, doc alternative pour hébergement PHP mutualisé | Couvre les deux publics cibles (artistes techniques vs. non techniques avec hébergeur classique). |
| Tests | **Pest** (sur base PHPUnit) | Syntaxe lisible, standard actuel de l'écosystème Laravel. |

**Compromis à noter :**
- Livewire plutôt qu'une SPA React introduit une dépendance forte à Laravel côté admin (pas de découplage frontend/backend), ce qui est cohérent avec "pas de CMS headless au MVP" mais limite une éventuelle réécriture future du frontend admin sans toucher au backend.
- Le driver de queue "database" est plus simple à installer mais moins performant que Redis sous forte charge d'import (acceptable pour un usage mono-artiste).

---

## 3. Arborescence de projet (simplifiée)

```
focale/
├── app/
│   ├── Models/            (User, Media, MediaVariant, Project, ProjectMedia, Collection, Page, Block, Tag, Category, Setting, Redirect, ContactMessage, ActivityLog, Post)
│   ├── Http/
│   │   ├── Controllers/Admin/   (DashboardController, MediaController, ProjectController, CollectionController, PageController, SettingController, ...)
│   │   ├── Controllers/Public/  (HomeController, ProjectController, PageController, ContactController, ...)
│   │   └── Middleware/
│   ├── Livewire/           (composants admin: MediaUploader, MediaGrid, ProjectEditor, PageBlockEditor, ...)
│   ├── Jobs/               (GenerateMediaVariants, ExtractExifData, PurgePageCache)
│   ├── Services/           (MediaProcessingService, ExifService, SeoService, SitemapService, BackupService)
│   └── Policies/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/admin/        (Blade + Livewire)
│   ├── views/public/       (thème par défaut "Editorial")
│   └── css/js/
├── storage/app/media/       (originaux + variantes, hors webroot)
├── config/focale.php        (réglages spécifiques : formats acceptés, tailles de variantes, etc.)
├── docker-compose.yml
├── .env.example
└── docs/
    ├── installation-locale.md
    ├── installation-serveur.md
    └── sauvegarde-restauration.md
```

---

## 4. Schéma de base de données (entités principales)

- **users** — id, name, email, password, role (enum, préparé pour multi-rôles), timestamps.
- **settings** — clé/valeur (site_name, artist_name, bio, logo, colors, fonts, maintenance_mode, etc.).
- **media** — id, uuid, title, slug, alt_text, caption, description, credit, copyright, author, license, taken_at, location, gps_lat, gps_lng, exif_json, hide_gps (bool), status, checksum (dédoublonnage), trashed_at.
- **media_variants** — id, media_id, type (thumbnail/web/retina/original), path, width, height, filesize.
- **tags** / **categories** — id, name, slug.
- **media_tag**, **media_category** — tables pivots.
- **projects** — id, title, slug, cover_media_id, intro_text, created_at, published_at, status (draft/unlisted/published/archived), visibility (public/password/private), password_hash, seo_title, seo_description, seo_image_id, layout_type, sort_order.
- **project_media** — project_id, media_id, sort_order, is_cover (pivot ordonné, réutilisation sans duplication).
- **collections** — id, title, slug, intro_text, cover_media_id, seo_*.
- **collection_project** — collection_id, project_id, sort_order.
- **pages** — id, title, slug, template, seo_*, status.
- **blocks** — id, page_id, type (text/image/gallery/quote/divider/link/button/video/html), content_json, sort_order.
- **posts** (journal, optionnel) — id, title, slug, cover_media_id, content, status, published_at.
- **menus** / **menu_items** — id, label, url/page_id, sort_order, location (header/footer).
- **redirects** — id, from_path, to_path, status_code.
- **contact_messages** — id, name, email, subject, message, read_at, ip.
- **activity_log** — id, user_id, action, subject_type, subject_id, timestamps.

**Relation clé** : `project_media` et `collection_project` sont les tables pivots ordonnées qui permettent la réutilisation d'un même média/projet sans duplication — exigence explicite du brief.

---

## 5. Wireframes textuels des écrans principaux

**Admin — Tableau de bord**
```
[Header: logo Focale | statut site (public/maintenance) | utilisateur]
[Bloc stats: nb œuvres | nb projets | brouillons]
[Colonne gauche: derniers projets modifiés]      [Colonne droite: dernières œuvres importées]
[Raccourcis: + Importer des œuvres]  [+ Créer un projet]
```

**Admin — Médiathèque**
```
[Barre d'outils: Recherche | Filtres (orientation/type/date/statut) | Vue grille/liste | Import]
[Grille de vignettes avec sélection multiple]
[Panneau latéral (au clic): titre, alt, légende, EXIF, tags, statut, bouton "masquer GPS"]
[Barre d'actions groupées: tag / déplacer / publier / dépublier / supprimer]
```

**Admin — Éditeur de projet**
```
[En-tête: titre, slug éditable, statut, visibilité]
[Colonne gauche: intro, dates, tags, crédits, SEO]
[Zone centrale: grille des œuvres du projet, réorganisables par glisser-déposer, badge "couverture"]
[Bouton: + Ajouter des œuvres depuis la médiathèque]
[Bouton: Prévisualiser | Publier | Dupliquer | Archiver]
```

**Public — Page projet**
```
[Titre du projet + méta (date, crédits)]
[Grille éditoriale des images, responsive srcset]
[Clic image -> lightbox clavier-accessible avec légende/crédit]
[Navigation: ← Projet précédent | Projet suivant →]
```

---

## 6. Découpage du MVP en étapes de développement

1. **Socle** — install Laravel, Docker Compose, .env.example, CI de base, auth admin (email/mdp, hachage, reset, rate limiting).
2. **Modèle de données** — migrations pour users, settings, media, media_variants, tags, categories.
3. **Médiathèque** — upload drag & drop multi-fichiers, jobs de génération de variantes, extraction EXIF/IPTC, dédoublonnage par checksum, vue grille/liste, corbeille.
4. **Projets** — CRUD projet, pivot project_media ordonnable (drag & drop), choix de couverture, statuts, preview.
5. **Thème public v1** — accueil, liste projets, page projet (grille + lightbox), page À propos configurable.
6. **Pages & réglages** — système de pages à blocs simples, réglages du site (identité, menu, couleurs/typo de base).
7. **Contact & SEO** — formulaire sécurisé (honeypot, rate limit), sitemap.xml, robots.txt, meta tags, JSON-LD de base.
8. **Tableau de bord** — vue de synthèse une fois les modules précédents en place.
9. **Documentation** — installation locale/serveur, sauvegarde/restauration.

Chaque étape est livrable et testable indépendamment ; l'ordre priorise la chaîne "importer une œuvre → la publier dans un projet → la voir sur le site public" le plus tôt possible.

---

## 7. Choix de sécurité, performance et déploiement

- **Sécurité** : validation MIME réelle (`finfo`, pas l'extension), stockage des originaux hors webroot, CSRF/XSS via les protections natives Laravel + Blade, rate limiting sur login et formulaire de contact, en-têtes de sécurité via middleware dédié, purge GPS/EXIF optionnelle avant publication.
- **Performance** : variantes d'images générées en file d'attente (jamais en synchrone au moment de l'upload), cache HTTP par tag sur les pages publiques avec purge ciblée à la publication, pagination sur la médiathèque admin, peu de JS côté public (pas de framework SPA).
- **Déploiement** : `docker-compose.yml` (app PHP-FPM + Nginx + MySQL + Redis optionnel) pour VPS ; documentation alternative pour hébergement mutualisé (PHP classique + MySQL, sans Docker, avec cron pour les jobs si pas de worker persistant possible).

---

## 8. Questions bloquantes avant de coder

1. **Hébergement cible prioritaire** : VPS avec Docker, ou compatibilité hébergement mutualisé PHP classique dès le MVP (impacte le choix du driver de queue par défaut) ?
2. **Rendu par défaut du frontend public** : Blade/Tailwind serveur (recommandé) confirmé, ou souhait explicite d'un frontend découplé (Vue/React) malgré la recommandation de rester léger ?

Le reste du périmètre (fonctionnalités reportées après MVP, modèle de données, architecture) suit strictement le brief fourni — aucune fonctionnalité cloud/réseau social/SaaS n'a été ajoutée.