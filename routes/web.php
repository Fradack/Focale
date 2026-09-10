<?php

use App\Http\Controllers\Admin\AlbumController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\HelpController;
use App\Http\Controllers\Admin\LegalDocumentController;
use App\Http\Controllers\Admin\LikeSettingController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PaymentMethodController as AdminPaymentMethodController;
use App\Http\Controllers\Admin\PluginController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductVariantController as AdminProductVariantController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShippingOptionController as AdminShippingOptionController;
use App\Http\Controllers\Admin\ShopStatsController;
use App\Http\Controllers\Admin\StatsController;
use App\Http\Controllers\Admin\UpdateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Customer\AccountController as CustomerAccountController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Install\InstallController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\AlbumController as PublicAlbumController;
use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\Public\ConsentController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\FaqController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ImageController as PublicImageController;
use App\Http\Controllers\Public\PageController as PublicPageController;
use App\Http\Controllers\Public\SeoController;
use App\Http\Controllers\Public\ShopController;
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

Route::middleware(['maintenance', 'visit-log'])->group(function () {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/albums', [PublicAlbumController::class, 'index'])->name('public.albums');
    Route::get('/album/{album:slug}', [PublicAlbumController::class, 'show'])->name('public.album');
    Route::get('/album/{album:slug}/statut-traitement', [PublicAlbumController::class, 'processingStatus'])->name('public.album.processing-status');
    Route::post('/album/{album:slug}/deverrouiller', [PublicAlbumController::class, 'unlock'])
        ->middleware('throttle:10,1')
        ->name('public.album.unlock');
    Route::post('/album/{album:slug}/commentaires', [PublicAlbumController::class, 'storeComment'])
        ->middleware('throttle:5,1')
        ->name('public.album.comment');
    Route::post('/album/{album:slug}/aimer', [PublicAlbumController::class, 'toggleLike'])
        ->middleware(['throttle:30,1', 'likes_enabled'])
        ->name('public.album.like');
    Route::get('/galerie', [PublicImageController::class, 'index'])->name('public.gallery');
    Route::get('/image/{media:slug}', [PublicImageController::class, 'show'])->name('public.image');
    Route::post('/image/{media:slug}/aimer', [PublicImageController::class, 'toggleLike'])
        ->middleware(['throttle:30,1', 'likes_enabled'])
        ->name('public.image.like');
    Route::post('/image/{media:slug}/vue', [PublicImageController::class, 'recordView'])
        ->middleware('throttle:30,1')
        ->name('public.image.view');

    Route::middleware('shop_enabled')->group(function () {
        Route::get('/boutique', [ShopController::class, 'index'])->name('public.shop.index');
        Route::get('/boutique/{product:slug}', [ShopController::class, 'show'])->name('public.shop.show');

        Route::get('/panier', [CartController::class, 'show'])->name('public.cart.show');
        Route::post('/panier', [CartController::class, 'store'])->name('public.cart.add');
        Route::patch('/panier/{variant}', [CartController::class, 'update'])->name('public.cart.update');
        Route::delete('/panier/{variant}', [CartController::class, 'destroy'])->name('public.cart.remove');
        Route::get('/panier/commander', [CartController::class, 'showCheckout'])->name('public.cart.checkout');
        Route::post('/panier/commander', [CartController::class, 'placeOrder'])->name('public.cart.checkout.store');
    });

    Route::get('/faq', [FaqController::class, 'index'])->name('public.faq');

    Route::get('/contact', [ContactController::class, 'show'])->name('public.contact');
    Route::post('/contact', [ContactController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('public.contact.store');

    Route::post('/consentement-cookies', [ConsentController::class, 'store'])->name('public.consent.store');
});

// Administration — 'staff' bloque les comptes client (voir EnsureIsStaff) :
// 'auth' seul ne vérifie que la connexion, jamais le rôle.
Route::prefix('administration')->name('admin.')->middleware(['auth', 'staff'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('mediatheque', [MediaController::class, 'index'])->name('media.index');
    Route::get('mediatheque/import', [MediaController::class, 'create'])->name('media.import');
    Route::post('mediatheque/import', [MediaController::class, 'store'])->name('media.store');
    Route::post('mediatheque/import/dossier', [MediaController::class, 'importFromFolder'])->name('media.import-folder');
    Route::post('mediatheque/action', [MediaController::class, 'bulkAction'])->name('media.bulk');
    Route::post('mediatheque/vider-bloquees', [MediaController::class, 'clearStuck'])->name('media.clear-stuck');
    Route::get('mediatheque/statut-traitement', [MediaController::class, 'processingStatus'])->name('media.processing-status');
    Route::get('mediatheque/{media}/statut-traitement', [MediaController::class, 'itemProcessingStatus'])->name('media.item-processing-status');
    Route::get('mediatheque/{media}', [MediaController::class, 'edit'])->name('media.edit');
    Route::put('mediatheque/{media}', [MediaController::class, 'update'])->name('media.update');
    Route::delete('mediatheque/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
    Route::post('mediatheque/{media}/restaurer', [MediaController::class, 'restore'])->name('media.restore');
    Route::post('mediatheque/{media}/reessayer', [MediaController::class, 'retry'])->name('media.retry');

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

    Route::get('documents-legaux', [LegalDocumentController::class, 'index'])->name('legal.index');

    Route::get('pages', [AdminPageController::class, 'index'])->name('pages.index');
    Route::post('pages', [AdminPageController::class, 'store'])->name('pages.store');
    Route::get('pages/{page}', [AdminPageController::class, 'edit'])->name('pages.edit');
    Route::put('pages/{page}', [AdminPageController::class, 'update'])->name('pages.update');
    Route::delete('pages/{page}', [AdminPageController::class, 'destroy'])->name('pages.destroy');

    Route::get('aide', [HelpController::class, 'index'])->name('help.index');

    Route::get('faq', [AdminFaqController::class, 'index'])->name('faq.index');
    Route::get('faq/nouveau', [AdminFaqController::class, 'create'])->name('faq.create');
    Route::post('faq', [AdminFaqController::class, 'store'])->name('faq.store');
    Route::get('faq/{faqItem}', [AdminFaqController::class, 'edit'])->name('faq.edit');
    Route::put('faq/{faqItem}', [AdminFaqController::class, 'update'])->name('faq.update');
    Route::delete('faq/{faqItem}', [AdminFaqController::class, 'destroy'])->name('faq.destroy');

    Route::get('utilisateurs', [UserController::class, 'index'])->name('users.index');
    Route::get('utilisateurs/nouveau', [UserController::class, 'create'])->name('users.create');
    Route::post('utilisateurs', [UserController::class, 'store'])->name('users.store');
    Route::get('utilisateurs/{user}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('utilisateurs/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('utilisateurs/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('commentaires', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::patch('commentaires/{comment}/approuver', [AdminCommentController::class, 'approve'])->name('comments.approve');
    Route::delete('commentaires/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');

    Route::get('statistiques', [StatsController::class, 'index'])->name('stats.index');
    Route::get('statistiques-boutique', [ShopStatsController::class, 'index'])->name('shop-stats.index');

    Route::prefix('boutique')->name('shop.')->group(function () {
        Route::get('produits', [AdminProductController::class, 'index'])->name('products.index');
        Route::post('produits', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('produits/{product}', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('produits/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('produits/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

        Route::post('produits/{product}/formats', [AdminProductVariantController::class, 'store'])->name('products.variants.store');
        Route::put('produits/{product}/formats/{variant}', [AdminProductVariantController::class, 'update'])->name('products.variants.update');
        Route::delete('produits/{product}/formats/{variant}', [AdminProductVariantController::class, 'destroy'])->name('products.variants.destroy');

        Route::get('paiement', [AdminPaymentMethodController::class, 'index'])->name('payment-methods.index');
        Route::post('paiement', [AdminPaymentMethodController::class, 'store'])->name('payment-methods.store');
        Route::put('paiement/{paymentMethod}', [AdminPaymentMethodController::class, 'update'])->name('payment-methods.update');
        Route::delete('paiement/{paymentMethod}', [AdminPaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');

        Route::get('livraison', [AdminShippingOptionController::class, 'index'])->name('shipping-options.index');
        Route::post('livraison', [AdminShippingOptionController::class, 'store'])->name('shipping-options.store');
        Route::put('livraison/{shippingOption}', [AdminShippingOptionController::class, 'update'])->name('shipping-options.update');
        Route::delete('livraison/{shippingOption}', [AdminShippingOptionController::class, 'destroy'])->name('shipping-options.destroy');

        Route::get('commandes', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('commandes/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::put('commandes/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    });

    Route::get('reglages', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('reglages', [SettingController::class, 'update'])->name('settings.update');

    Route::get('mises-a-jour', [UpdateController::class, 'index'])->name('updates.index');
    Route::post('mises-a-jour/verifier', [UpdateController::class, 'check'])->name('updates.check');
    Route::post('mises-a-jour/appliquer', [UpdateController::class, 'apply'])->name('updates.apply');

    Route::get('plugins', [PluginController::class, 'index'])->name('plugins.index');
    Route::post('plugins/{slug}/installer', [PluginController::class, 'install'])->name('plugins.install');
    Route::post('plugins/{slug}/activer', [PluginController::class, 'enable'])->name('plugins.enable');
    Route::post('plugins/{slug}/desactiver', [PluginController::class, 'disable'])->name('plugins.disable');

    Route::get('likes', [LikeSettingController::class, 'index'])->name('likes.index');

    Route::get('profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profil', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';

// Comptes client — inscription volontairement non annoncée (aucun lien dans
// la navigation), prépare le futur système de commande de livre photo.
// 'guest'/'auth' partagent le même garde standard que le reste du site.
Route::prefix('compte')->name('customer.')->group(function () {
    Route::middleware('guest.customer')->group(function () {
        Route::get('inscription', [CustomerAccountController::class, 'showRegister'])->name('register');
        Route::post('inscription', [CustomerAccountController::class, 'register'])
            ->middleware('throttle:5,1')
            ->name('register.store');
        Route::get('connexion', [CustomerAccountController::class, 'showLogin'])->name('login');
        Route::post('connexion', [CustomerAccountController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', [CustomerAccountController::class, 'dashboard'])->name('dashboard');
        Route::post('deconnexion', [CustomerAccountController::class, 'logout'])->name('logout');

        Route::get('commandes', [CustomerOrderController::class, 'index'])->name('orders.index');
        Route::get('commandes/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
    });
});

// Routes ajoutées par les plugins installés (voir App\Services\PluginManager).
// Chaque fichier commence par un garde sur son propre statut d'activation.
// Placé avant le catch-all générique ci-dessous pour ne jamais s'y faire
// masquer.
foreach (glob(base_path('routes/plugins/*.php')) as $pluginRouteFile) {
    require $pluginRouteFile;
}

// Doit rester la toute dernière route : capture les pages de contenu génériques
// et ne doit donc jamais passer avant /administration/... ou les autres routes publiques.
Route::middleware(['maintenance', 'visit-log'])->get('/{page:slug}', [PublicPageController::class, 'show'])->name('page.show');
