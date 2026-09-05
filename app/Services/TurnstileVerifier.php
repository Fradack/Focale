<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Cloudflare Turnstile — CAPTCHA gratuit, respectueux de la vie privée,
 * strictement optionnel (brief section 9 : « CAPTCHA uniquement en option »).
 * N'agit que si l'artiste a renseigné ses clés dans Réglages.
 */
class TurnstileVerifier
{
    public function isEnabled(): bool
    {
        return (bool) Setting::get('turnstile_site_key') && (bool) Setting::get('turnstile_secret_key');
    }

    public function verify(Request $request): bool
    {
        if (! $this->isEnabled()) {
            return true;
        }

        $token = $request->input('cf-turnstile-response');
        if (! $token) {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(5)->post(
                'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                [
                    'secret' => Setting::get('turnstile_secret_key'),
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]
            );

            return (bool) $response->json('success');
        } catch (\Throwable) {
            // En cas de panne du service Cloudflare, on ne bloque jamais un
            // visiteur légitime : le CAPTCHA reste "best effort".
            return true;
        }
    }
}
