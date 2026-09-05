<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use Illuminate\Support\Facades\Route;

// Focale a un seul administrateur (créé par un seeder), pas d'inscription
// publique ni de vérification d'e-mail — hors périmètre du CMS (brief section 8).
Route::prefix('administration')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        Route::get('mot-de-passe-oublie', [PasswordResetLinkController::class, 'create'])
            ->name('password.request');

        Route::post('mot-de-passe-oublie', [PasswordResetLinkController::class, 'store'])
            ->name('password.email');

        Route::get('reinitialiser-mot-de-passe/{token}', [NewPasswordController::class, 'create'])
            ->name('password.reset');

        Route::post('reinitialiser-mot-de-passe', [NewPasswordController::class, 'store'])
            ->name('password.store');

        Route::get('deux-facteurs', [TwoFactorChallengeController::class, 'show'])
            ->name('two-factor.challenge');

        Route::post('deux-facteurs', [TwoFactorChallengeController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('two-factor.challenge.store');
    });

    Route::middleware('auth')->group(function () {
        Route::get('confirmer-mot-de-passe', [ConfirmablePasswordController::class, 'show'])
            ->name('password.confirm');

        Route::post('confirmer-mot-de-passe', [ConfirmablePasswordController::class, 'store']);

        Route::put('mot-de-passe', [PasswordController::class, 'update'])->name('password.update');

        Route::post('deconnexion', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
    });
});
