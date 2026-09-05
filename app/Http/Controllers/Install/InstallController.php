<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use App\Services\EnvFileWriter;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class InstallController extends Controller
{
    public function showAccount(): View
    {
        return view('install.account');
    }

    public function storeAccount(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $request->session()->put('install.account', [
            'name' => trim($data['first_name'].' '.$data['last_name']),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Nouveau secret à chaque passage par l'étape 1, pour ne pas garder
        // un secret orphelin si l'utilisateur change d'adresse e-mail.
        $request->session()->forget(['install.totp_secret', 'install.recovery_codes']);

        return redirect()->route('install.security');
    }

    public function showSecurity(Request $request, TwoFactorService $twoFactor): View|RedirectResponse
    {
        $account = $request->session()->get('install.account');
        if (! $account) {
            return redirect()->route('install.account');
        }

        if (! $request->session()->has('install.totp_secret')) {
            $request->session()->put('install.totp_secret', $twoFactor->generateSecret());
            $request->session()->put('install.recovery_codes', $twoFactor->generateRecoveryCodes());
        }

        $secret = $request->session()->get('install.totp_secret');

        return view('install.security', [
            'email' => $account['email'],
            'secret' => $secret,
            'qrCodeSvg' => $twoFactor->qrCodeSvg($account['email'], $secret),
            'recoveryCodes' => $request->session()->get('install.recovery_codes'),
        ]);
    }

    public function storeSecurity(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        if (! $request->session()->has('install.account') || ! $request->session()->has('install.totp_secret')) {
            return redirect()->route('install.account');
        }

        $data = $request->validate(['code' => ['required', 'string']]);

        if (! $twoFactor->verify($request->session()->get('install.totp_secret'), $data['code'])) {
            return back()->withErrors(['code' => 'Le code saisi est incorrect. Réessaie avec le code affiché dans ton application.']);
        }

        $request->session()->put('install.security_confirmed', true);

        return redirect()->route('install.database');
    }

    public function showDatabase(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('install.security_confirmed')) {
            return redirect()->route('install.account');
        }

        return view('install.database');
    }

    public function testDatabase(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->validateDatabase($request);

        try {
            Config::set('database.connections.mysql', array_merge(
                config('database.connections.mysql'),
                [
                    'host' => $data['db_host'],
                    'port' => $data['db_port'],
                    'database' => $data['db_database'],
                    'username' => $data['db_username'],
                    'password' => $data['db_password'],
                    // Sans ça, un hôte injoignable peut faire attendre la
                    // connexion PDO plusieurs dizaines de secondes.
                    'options' => [\PDO::ATTR_TIMEOUT => 5],
                ]
            ));
            DB::purge('mysql');
            DB::connection('mysql')->getPdo();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => "Connexion impossible : vérifie l'hôte, le port et les identifiants."]);
        }
    }

    public function storeDatabase(Request $request, EnvFileWriter $envWriter): RedirectResponse
    {
        if (! $request->session()->get('install.security_confirmed')) {
            return redirect()->route('install.account');
        }

        $data = $this->validateDatabase($request);

        try {
            Config::set('database.connections.mysql', array_merge(
                config('database.connections.mysql'),
                [
                    'host' => $data['db_host'],
                    'port' => $data['db_port'],
                    'database' => $data['db_database'],
                    'username' => $data['db_username'],
                    'password' => $data['db_password'],
                    // Sans ça, un hôte injoignable peut faire attendre la
                    // connexion PDO plusieurs dizaines de secondes.
                    'options' => [\PDO::ATTR_TIMEOUT => 5],
                ]
            ));
            DB::purge('mysql');
            DB::connection('mysql')->getPdo();
        } catch (\Throwable $e) {
            return back()->withErrors(['db_host' => "Connexion à la base impossible avec ces identifiants."])->withInput();
        }

        $envWriter->update([
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $data['db_host'],
            'DB_PORT' => (string) $data['db_port'],
            'DB_DATABASE' => $data['db_database'],
            'DB_USERNAME' => $data['db_username'],
            'DB_PASSWORD' => $data['db_password'],
        ]);

        return redirect()->route('install.finalize');
    }

    public function showFinalize(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('install.security_confirmed')) {
            return redirect()->route('install.account');
        }

        return view('install.finalize');
    }

    public function finalize(Request $request): View|RedirectResponse
    {
        $account = $request->session()->get('install.account');
        $secret = $request->session()->get('install.totp_secret');
        $recoveryCodes = $request->session()->get('install.recovery_codes');

        if (! $account || ! $secret || ! $request->session()->get('install.security_confirmed')) {
            return redirect()->route('install.account');
        }

        Artisan::call('migrate', ['--force' => true]);

        $user = User::create([
            'name' => $account['name'],
            'email' => $account['email'],
            'password' => $account['password'],
            'role' => 'admin',
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => $recoveryCodes,
            'two_factor_confirmed_at' => now(),
        ]);

        Setting::set('site_name', 'Focale');

        $about = Page::firstOrCreate(
            ['slug' => 'a-propos'],
            ['title' => 'À propos', 'template' => 'default', 'status' => 'draft']
        );
        if ($about->blocks()->doesntExist()) {
            $about->blocks()->create(['type' => 'text', 'content' => ['text' => ''], 'sort_order' => 0]);
        }

        file_put_contents(storage_path('app/installed.lock'), now()->toAtomString());

        $request->session()->forget(['install.account', 'install.totp_secret', 'install.recovery_codes', 'install.security_confirmed']);

        return view('install.success', ['recoveryCodes' => $recoveryCodes]);
    }

    /**
     * @return array{db_host: string, db_port: int, db_database: string, db_username: string, db_password: string}
     */
    private function validateDatabase(Request $request): array
    {
        $data = $request->validate([
            'db_host' => ['required', 'string', 'max:255'],
            'db_port' => ['required', 'integer'],
            'db_database' => ['required', 'string', 'max:255'],
            'db_username' => ['required', 'string', 'max:255'],
            'db_password' => ['nullable', 'string', 'max:255'],
        ]);

        $data['db_password'] ??= '';

        return $data;
    }
}
