<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    private const TYPES = [
        'album' => 'Albums photo',
        'cadre' => 'Tirages encadrés',
        'tableau' => 'Tableaux',
    ];

    public function index(Request $request): View
    {
        $type = $request->query('type');

        $products = Product::published()
            ->when($type && array_key_exists($type, self::TYPES), fn ($q) => $q->ofType($type))
            ->with('coverMedia.variants', 'variants')
            ->orderBy('sort_order')
            ->get();

        return view('public.shop.index', [
            'products' => $products,
            'types' => self::TYPES,
            'activeType' => $type,
        ]);
    }

    public function show(Product $product): View
    {
        if ($product->status !== 'published') {
            abort(404);
        }

        $product->load('coverMedia.variants', 'variants');

        return view('public.shop.show', ['product' => $product]);
    }
}
