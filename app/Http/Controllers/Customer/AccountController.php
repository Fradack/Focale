<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Comptes client — inscription publique volontairement non annoncée (aucun
 * lien dans la navigation) : préparation du futur système de commande de
 * livre photo, pas encore branché. Distinct des comptes d'équipe
 * (admin/editor/contributor/viewer) via `users.is_customer` — voir
 * EnsureIsStaff pour le garde-fou qui empêche ces comptes d'atteindre
 * /administration. Pas de vérification d'e-mail ni de 2FA ici, cohérent
 * avec le choix déjà fait pour le reste du site (voir la doc d'architecture).
 */
class AccountController extends Controller
{
    public function showRegister(): View
    {
        return view('customer.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'viewer',
            'is_customer' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard');
    }

    public function showLogin(): View
    {
        return view('customer.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Identifiants incorrects.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('customer.dashboard', absolute: false));
    }

    public function dashboard(Request $request): View
    {
        return view('customer.dashboard', ['user' => $request->user()]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
