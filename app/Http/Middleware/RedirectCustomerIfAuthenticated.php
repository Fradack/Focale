<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Remplace le middleware 'guest' standard sur /compte/connexion et
 * /compte/inscription : celui-ci redirige tout utilisateur authentifié, sans
 * distinguer un admin/staff connecté côté /administration d'un client déjà
 * connecté. Un admin naviguant sur le site public se voit alors renvoyé vers
 * l'accueil au lieu de voir le formulaire — la page paraît "cassée" alors
 * qu'elle fonctionne, simplement pas pour ce visiteur-là. Seul un compte
 * client déjà connecté doit être redirigé (vers son espace).
 */
class RedirectCustomerIfAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->is_customer) {
            return redirect()->route('customer.dashboard');
        }

        return $next($request);
    }
}
