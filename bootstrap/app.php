<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

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
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\EnsureInstalled::class,
            \App\Http\Middleware\AssignVisitorId::class,
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
