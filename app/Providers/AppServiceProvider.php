<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Politique de mot de passe appliquée partout où Password::defaults()
        // est utilisé (installateur, réinitialisation, changement de mot de
        // passe depuis le profil) : plus de 8 caractères, majuscule, minuscule,
        // chiffre et caractère spécial.
        Password::defaults(fn () => Password::min(9)
            ->mixedCase()
            ->numbers()
            ->symbols());
    }
}
