<?php

use App\Http\Controllers\Admin\AlbumController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UpdateController;
use App\Http\Controllers\Install\InstallController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\AlbumController as PublicAlbumController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ImageController as PublicImageController;
use App\Http\Controllers\Public\PageController as PublicPageController;
use App\Http\Controllers\Public\SeoController;
use Illuminate\Support\Facades\Route;

// Installateur — jamais accessible une fois storage/app/installed.lock présent.
Route::prefix('installation')->name('install.')->middleware('not_installed')->group(function () {
    Route::get('/', [InstallController::class, 'showAccount'])->name('account');
    Route::post('/', [InstallController::class, 'storeAccount'])->name('account.store');

    Route::get('securite', [InstallController::class, 'showSecurity'])->name('security');
    Route::post('securite', [InstallController::class, 'storeSecurity'])->name('security.store');

    Route::get('base-de-donnees', [InstallController::class, 'showDatabase'])->name('database');
    Route::post('base-de-donnees/tester', [InstallController::class, 'testDatabase'])->name('database.test');
    Route::post('base-de-donnees', [InstallController::class, 'storeDatabase'])->name('database.store');

    Route::get('finaliser', [InstallController::class, 'showFinalize'])->name('finalize');
    Route::post('finaliser', [InstallController::class, 'finalize'])->name('finalize.store');
});

// Site public
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('public.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('public.robots');

Route::middleware('maintenance')->group(function () {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/albums', [PublicAlbumController::class, 'index'])->name('public.albums');
    Route::get('/album/{album:slug}', [PublicAlbumController::class, 'show'])->name('public.album');
    Route::post('/album/{album:slug}/deverrouiller', [PublicAlbumController::class, 'unlock'])
        ->middleware('throttle:10,1')
        ->name('public.album.unlock');
    Route::post('/album/{album:slug}/commentaires', [PublicAlbumController::class, 'storeComment'])
        ->middleware('throttle:5,1')
        ->name('public.album.comment');
    Route::get('/galerie', [PublicImageController::class, 'index'])->name('public.gallery');
    Route::get('/image/{media:slug}', [PublicImageController::class, 'show'])->name('public.image');

    Route::get('/contact', [ContactController::class, 'show'])->name('public.contact');
    Route::post('/contact', [ContactController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('public.contact.store');
});

// Administration
Route::prefix('administration')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('mediatheque', [MediaController::class, 'index'])->name('media.index');
    Route::get('mediatheque/import', [MediaController::class, 'create'])->name('media.import');
    Route::post('mediatheque/import', [MediaController::class, 'store'])->name('media.store');
    Route::get('mediatheque/{media}', [MediaController::class, 'edit'])->name('media.edit');
    Route::put('mediatheque/{media}', [MediaController::class, 'update'])->name('media.update');
    Route::delete('mediatheque/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
    Route::post('mediatheque/{media}/restaurer', [MediaController::class, 'restore'])->name('media.restore');

    Route::get('albums', [AlbumController::class, 'index'])->name('albums.index');
    Route::post('albums', [AlbumController::class, 'store'])->name('albums.create');
    Route::get('albums/{album}', [AlbumController::class, 'edit'])->name('albums.edit');
    Route::put('albums/{album}', [AlbumController::class, 'update'])->name('albums.update');
    Route::post('albums/{album}/dupliquer', [AlbumController::class, 'duplicate'])->name('albums.duplicate');
    Route::delete('albums/{album}', [AlbumController::class, 'destroy'])->name('albums.destroy');
    Route::post('albums/{album}/medias', [AlbumController::class, 'attachMedia'])->name('albums.media.attach');
    Route::delete('albums/{album}/medias/{media}', [AlbumController::class, 'detachMedia'])->name('albums.media.detach');
    Route::put('albums/{album}/medias/ordre', [AlbumController::class, 'reorderMedia'])->name('albums.media.reorder');
    Route::put('albums/{album}/couverture', [AlbumController::class, 'setCover'])->name('albums.cover');

    Route::get('pages', [AdminPageController::class, 'index'])->name('pages.index');
    Route::post('pages', [AdminPageController::class, 'store'])->name('pages.store');
    Route::get('pages/{page}', [AdminPageController::class, 'edit'])->name('pages.edit');
    Route::put('pages/{page}', [AdminPageController::class, 'update'])->name('pages.update');
    Route::delete('pages/{page}', [AdminPageController::class, 'destroy'])->name('pages.destroy');

    Route::get('commentaires', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::patch('commentaires/{comment}/approuver', [AdminCommentController::class, 'approve'])->name('comments.approve');
    Route::delete('commentaires/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');

    Route::get('reglages', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('reglages', [SettingController::class, 'update'])->name('settings.update');

    Route::get('mises-a-jour', [UpdateController::class, 'index'])->name('updates.index');
    Route::post('mises-a-jour/verifier', [UpdateController::class, 'check'])->name('updates.check');
    Route::post('mises-a-jour/appliquer', [UpdateController::class, 'apply'])->name('updates.apply');

    Route::get('profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profil', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';

// Doit rester la toute dernière route : capture les pages de contenu génériques
// et ne doit donc jamais passer avant /administration/... ou les autres routes publiques.
Route::middleware('maintenance')->get('/{page:slug}', [PublicPageController::class, 'show'])->name('page.show');
