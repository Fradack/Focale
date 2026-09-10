<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqItem;
use Illuminate\View\View;

/**
 * FAQ Administrateur : répond aux questions de l'admin sur le fonctionnement
 * de Focale (distincte de la FAQ Visiteur, publique) — jamais accessible
 * hors de l'administration. Le contenu se gère depuis FaqController (même
 * table `faq_items`, colonne `audience`).
 */
class HelpController extends Controller
{
    public function index(): View
    {
        return view('admin.help.index', ['items' => FaqItem::admin()->ordered()->get()->groupBy('category')]);
    }
}
