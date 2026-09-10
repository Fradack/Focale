<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Setting;
use App\Support\Countries;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    private const KEYS = [
        'site_name', 'artist_name', 'bio', 'contact_email',
        'social_instagram', 'social_twitter', 'maintenance_mode',
        'home_cover_media_id', 'turnstile_site_key', 'turnstile_secret_key',
        'footer_copyright', 'theme_admin', 'theme_public',
        'country_restriction_mode', 'country_restriction_countries',
    ];

    private const COUNTRY_RESTRICTION_MODES = ['disabled', 'blocklist', 'allowlist'];

    public function edit(): View
    {
        $values = collect(self::KEYS)->mapWithKeys(fn ($key) => [$key => Setting::get($key)]);
        $values['theme_admin'] = \App\Support\Theme::adminTheme();
        $values['theme_public'] = \App\Support\Theme::publicTheme();
        $values['country_restriction_mode'] = $values['country_restriction_mode'] ?: 'disabled';

        $selectedCountries = json_decode($values['country_restriction_countries'] ?? '[]', true);
        $values['country_restriction_countries'] = is_array($selectedCountries) ? $selectedCountries : [];

        return view('admin.settings.edit', [
            'values' => $values,
            'coverCandidates' => Media::whereNull('trashed_at')->latest()->limit(60)->get(),
            'countries' => Countries::list(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['nullable', 'string', 'max:255'],
            'artist_name' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_twitter' => ['nullable', 'url', 'max:255'],
            'maintenance_mode' => ['nullable', 'boolean'],
            'home_cover_media_id' => ['nullable', 'exists:media,id'],
            'turnstile_site_key' => ['nullable', 'string', 'max:255'],
            'turnstile_secret_key' => ['nullable', 'string', 'max:255'],
            'footer_copyright' => ['nullable', 'string', 'max:255'],
            'theme_admin' => ['nullable', 'in:'.implode(',', \App\Support\Theme::THEMES)],
            'theme_public' => ['nullable', 'in:'.implode(',', \App\Support\Theme::THEMES)],
            'country_restriction_mode' => ['nullable', 'in:'.implode(',', self::COUNTRY_RESTRICTION_MODES)],
            'country_restriction_countries' => ['nullable', 'array'],
            'country_restriction_countries.*' => ['string', function ($attribute, $value, $fail) {
                if (! Countries::isValidCode($value)) {
                    $fail('Code pays invalide.');
                }
            }],
        ]);

        $data['maintenance_mode'] = $request->boolean('maintenance_mode') ? '1' : '0';
        $data['theme_admin'] = $data['theme_admin'] ?? 'light';
        $data['theme_public'] = $data['theme_public'] ?? 'light';
        $data['country_restriction_mode'] = $data['country_restriction_mode'] ?? 'disabled';
        $selectedCountries = array_values(array_unique(array_map('strtoupper', $data['country_restriction_countries'] ?? [])));
        $data['country_restriction_countries'] = json_encode($selectedCountries);

        foreach (self::KEYS as $key) {
            Setting::set($key, $data[$key] ?? null);
        }

        return back()->with('status', 'settings-updated');
    }
}
