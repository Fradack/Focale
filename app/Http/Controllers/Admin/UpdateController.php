<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UpdateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UpdateController extends Controller
{
    public function index(UpdateService $updates): View
    {
        return view('admin.updates.index', [
            'update' => $updates->checkForUpdate(),
            'lastInstalled' => $updates->lastInstalledUpdate(),
        ]);
    }

    public function check(UpdateService $updates): RedirectResponse
    {
        $updates->checkForUpdate(fresh: true);

        return back();
    }

    public function apply(UpdateService $updates): RedirectResponse
    {
        try {
            $updates->applyUpdate();

            return back()->with('status', 'update-applied');
        } catch (\Throwable $e) {
            return back()->withErrors(['update' => "La mise à jour a échoué : {$e->getMessage()}"]);
        }
    }
}
