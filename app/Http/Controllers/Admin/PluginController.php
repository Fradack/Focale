<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use App\Services\PluginManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PluginController extends Controller
{
    public function index(Request $request, PluginManager $plugins): View
    {
        $query = trim((string) $request->query('q'));

        $installed = Plugin::orderBy('label')->get()->keyBy('slug');
        $catalog = $plugins->listAvailableFromGithub();

        if ($query !== '') {
            $needle = mb_strtolower($query);

            $installed = $installed->filter(
                fn (Plugin $plugin, string $slug) => $this->matches($needle, $slug, $plugin->label, $plugin->description)
            );

            $catalog = collect($catalog)
                ->filter(fn (array $manifest, string $slug) => $this->matches($needle, $slug, $manifest['label'] ?? null, $manifest['description'] ?? null))
                ->all();
        }

        return view('admin.plugins.index', [
            'installed' => $installed,
            'catalog' => $catalog,
            'query' => $query,
        ]);
    }

    public function show(string $slug, PluginManager $plugins): View
    {
        $catalog = $plugins->listAvailableFromGithub();

        return view('admin.plugins.show', [
            'slug' => $slug,
            'plugin' => Plugin::find($slug),
            'manifest' => $catalog[$slug] ?? null,
        ]);
    }

    public function install(string $slug, PluginManager $plugins): RedirectResponse
    {
        try {
            $plugins->install($slug);

            return redirect()->route('admin.plugins.show', $slug)->with('status', "plugin-installed:{$slug}");
        } catch (\Throwable $e) {
            return back()->withErrors(['plugin' => "Installation échouée : {$e->getMessage()}"]);
        }
    }

    public function enable(string $slug, PluginManager $plugins): RedirectResponse
    {
        try {
            $plugins->enable($slug);

            return back()->with('status', "plugin-enabled:{$slug}");
        } catch (\Throwable $e) {
            return back()->withErrors(['plugin' => "Activation échouée : {$e->getMessage()}"]);
        }
    }

    public function disable(string $slug, PluginManager $plugins): RedirectResponse
    {
        $plugins->disable($slug);

        return back()->with('status', "plugin-disabled:{$slug}");
    }

    private function matches(string $needle, string $slug, ?string $label, ?string $description): bool
    {
        foreach ([$slug, $label, $description] as $haystack) {
            if ($haystack && str_contains(mb_strtolower($haystack), $needle)) {
                return true;
            }
        }

        return false;
    }
}
