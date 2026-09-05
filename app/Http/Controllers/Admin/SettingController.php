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
        'footer_copyright', 'import_concurrency',
    ];

    public function edit(): View
    {
        $values = collect(self::KEYS)->mapWithKeys(fn ($key) => [$key => Setting::get($key)]);
        $values['import_concurrency'] ??= 3;

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
        ]);

        $data['maintenance_mode'] = $request->boolean('maintenance_mode') ? '1' : '0';
        $data['import_concurrency'] = $data['import_concurrency'] ?? 3;

        foreach (self::KEYS as $key) {
            Setting::set($key, $data[$key] ?? null);
        }

        return back()->with('status', 'settings-updated');
    }
}
