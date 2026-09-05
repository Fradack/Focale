<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('2fa.user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $userId = $request->session()->get('2fa.user_id');
        if (! $userId) {
            return redirect()->route('login');
        }

        $data = $request->validate(['code' => ['required', 'string']]);

        /** @var User $user */
        $user = User::findOrFail($userId);

        $valid = $twoFactor->verify($user->two_factor_secret, $data['code'])
            || $twoFactor->verifyRecoveryCode($user, $data['code']);

        if (! $valid) {
            return back()->withErrors(['code' => 'Code incorrect.']);
        }

        Auth::login($user, (bool) $request->session()->pull('2fa.remember', false));
        $request->session()->forget('2fa.user_id');
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }
}
