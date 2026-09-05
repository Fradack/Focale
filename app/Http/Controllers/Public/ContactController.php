<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Services\SpamGuard;
use App\Services\TurnstileVerifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('public.contact', [
            'turnstileEnabled' => (new TurnstileVerifier)->isEnabled(),
        ]);
    }

    public function store(Request $request, SpamGuard $spamGuard, TurnstileVerifier $turnstile): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['prohibited'], // honeypot
        ]);
        unset($data['website']);

        // Signaux anti-spam silencieux : on ne dit jamais à un robot pourquoi
        // ça échoue, on affiche juste le même message de succès qu'un envoi
        // normal, mais rien n'est enregistré ni envoyé.
        if ($spamGuard->looksAutomated($request)
            || $spamGuard->hasTooManyLinks($data['message'])
            || ! $turnstile->verify($request)) {
            return back()->with('status', 'message-sent');
        }

        $contactMessage = ContactMessage::create($data + ['ip' => $request->ip()]);

        $recipient = Setting::get('contact_email');
        if ($recipient) {
            Mail::to($recipient)->send(new ContactMessageMail($contactMessage));
        }

        return back()->with('status', 'message-sent');
    }
}
