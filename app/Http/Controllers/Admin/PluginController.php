<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use App\Services\PluginManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PluginController extends Controller
{
    public function index(PluginManager $plugins): View
    {
        return view('admin.plugins.index', [
            'installed' => Plugin::orderBy('label')->get()->keyBy('slug'),
            'catalog' => $plugins->listAvailableFromGithub(),
        ]);
    }

    public function install(string $slug, PluginManager $plugins): RedirectResponse
    {
        try {
            $plugins->install($slug);

            return back()->with('status', "plugin-installed:{$slug}");
        } catch (\Throwable $e) {
            return back()->withErrors(['plugin' => "Installation échouée : {$e->getMessage()}"]);
        }
    }

    public function enable(string $slug, PluginManager $plugins): RedirectResponse
    {
        $plugins->enable($slug);

        return back()->with('status', "plugin-enabled:{$slug}");
    }

    public function disable(string $slug, PluginManager $plugins): RedirectResponse
    {
        $plugins->disable($slug);

        return back()->with('status', "plugin-disabled:{$slug}");
    }
}
