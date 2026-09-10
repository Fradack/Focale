<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\View\View;

/**
 * Raccourci direct vers les 3 pages légales (créées par la migration
 * seed_legal_pages) : ce sont des Page comme les autres — modifiées via
 * PageController::edit()/update() — juste rassemblées ici pour qu'on les
 * retrouve sans avoir à les chercher dans la liste générale des pages.
 */
class LegalDocumentController extends Controller
{
    private const SLUGS = ['mentions-legales', 'cgu', 'cgv'];

    public function index(): View
    {
        $pages = Page::whereIn('slug', self::SLUGS)->get()->keyBy('slug');

        return view('admin.legal.index', [
            'documents' => [
                'mentions-legales' => ['label' => 'Mentions légales', 'page' => $pages->get('mentions-legales')],
                'cgu' => ['label' => "Conditions générales d'utilisation (CGU)", 'page' => $pages->get('cgu')],
                'cgv' => ['label' => 'Conditions générales de vente (CGV)', 'page' => $pages->get('cgv')],
            ],
        ]);
    }
}
