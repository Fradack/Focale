<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

// Autoloader pur PHP pour les classes des plugins installés dynamiquement
// (voir App\Services\PluginManager) : en production, optimize-autoloader
// fige un classmap Composer au déploiement — un fichier PHP copié après
// coup dans app/Plugins/ n'y figure jamais, et `composer dump-autoload`
// n'est pas disponible sur cet hébergement mutualisé (même contrainte que
// le backup de UpdateService, qui évite déjà proc_open). Ce callback
// résout App\Plugins\Xxx\Yyy vers app/Plugins/Xxx/Yyy.php indépendamment
// de Composer — enregistré avant Application::configure() pour être actif
// dès le tout premier autoload du boot.
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\Plugins\\';

    if (! str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $path = dirname(__DIR__).'/app/Plugins/'.str_replace('\\', '/', $relative).'.php';

    if (is_file($path)) {
        require $path;
    }
});

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'maintenance' => \App\Http\Middleware\CheckMaintenanceMode::class,
            'not_installed' => \App\Http\Middleware\EnsureNotInstalled::class,
            'visit-log' => \App\Http\Middleware\LogSiteVisit::class,
            'staff' => \App\Http\Middleware\EnsureIsStaff::class,
            'guest.customer' => \App\Http\Middleware\RedirectCustomerIfAuthenticated::class,
            'shop_enabled' => \App\Http\Middleware\EnsureShopEnabled::class,
            'likes_enabled' => \App\Http\Middleware\EnsureLikesEnabled::class,
            'country-restriction' => \App\Http\Middleware\EnsureCountryAllowed::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\EnsureInstalled::class,
            \App\Http\Middleware\AssignVisitorId::class,
        ]);

        // navigator.sendBeacon() (voir plugin Tracking) ne peut pas poser
        // d'en-tête personnalisé, donc pas de jeton CSRF possible sur cette
        // route précise — elle ne fait qu'ajouter une ligne de mesure
        // d'audience anonyme, jamais d'action sensible.
        $middleware->validateCsrfTokens(except: [
            'tracking/vue',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

// Sur certains hébergements mutualisés, la racine web ne peut pas être
// pointée vers le sous-dossier public/ (ex. dépôt FTP dans un sous-répertoire
// d'un domaine existant). Ce fichier optionnel, absent par défaut et jamais
// versionné, permet d'indiquer où vit réellement le dossier public/ déployé.
if (is_file($publicPathOverride = __DIR__.'/public_path.php')) {
    $app->usePublicPath(require $publicPathOverride);
}

return $app;
