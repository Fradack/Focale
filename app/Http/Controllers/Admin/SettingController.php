<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    private const KEYS = [
        'site_name', 'artist_name', 'bio', 'contact_email',
        'social_instagram', 'social_twitter', 'maintenance_mode',
        'home_cover_media_id', 'turnstile_site_key', 'turnstile_secret_key',
        'footer_copyright', 'import_concurrency', 'import_one_by_one', 'theme_dark_mode',
    ];

    public function edit(): View
    {
        $values = collect(self::KEYS)->mapWithKeys(fn ($key) => [$key => Setting::get($key)]);
        $values['import_concurrency'] ??= 3;
        // Coché et recommandé par défaut : seul un réglage explicite à '0' le désactive.
        $values['import_one_by_one'] = $values['import_one_by_one'] !== '0';
        $values['theme_dark_mode'] = \App\Support\Theme::mode();

        return view('admin.settings.edit', [
            'values' => $values,
            'coverCandidates' => Media::whereNull('trashed_at')->latest()->limit(60)->get(),
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
            'import_concurrency' => ['nullable', 'integer', 'min:1', 'max:10'],
            'import_one_by_one' => ['nullable', 'boolean'],
            'theme_dark_mode' => ['nullable', 'in:'.implode(',', \App\Support\Theme::SCOPES)],
        ]);

        $data['maintenance_mode'] = $request->boolean('maintenance_mode') ? '1' : '0';
        $data['import_concurrency'] = $data['import_concurrency'] ?? 3;
        $data['import_one_by_one'] = $request->boolean('import_one_by_one') ? '1' : '0';
        $data['theme_dark_mode'] = $data['theme_dark_mode'] ?? 'off';

        foreach (self::KEYS as $key) {
            Setting::set($key, $data[$key] ?? null);
        }

        return back()->with('status', 'settings-updated');
    }
}
