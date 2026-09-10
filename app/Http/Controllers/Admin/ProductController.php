<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    private const TYPES = [
        'album' => 'Album photo',
        'cadre' => 'Tirage encadré',
        'tableau' => 'Tableau',
    ];

    public function index(Request $request): View
    {
        $query = Product::query();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $products = $query->withCount('variants')->orderBy('sort_order')->paginate(20)->withQueryString();

        return view('admin.shop.products.index', [
            'products' => $products,
            'types' => self::TYPES,
            'counts' => [
                'all' => Product::count(),
                'published' => Product::where('status', 'published')->count(),
                'draft' => Product::where('status', 'draft')->count(),
            ],
        ]);
    }

    public function store(): RedirectResponse
    {
        $title = 'Nouveau produit';
        $slug = $title;
        $i = 1;
        while (Product::where('slug', Str::slug($slug))->exists()) {
            $slug = $title.' '.(++$i);
        }

        $product = Product::create([
            'title' => $title,
            'slug' => Str::slug($slug),
            'type' => 'album',
            'status' => 'draft',
            'sort_order' => (int) Product::max('sort_order') + 1,
        ]);

        return redirect()->route('admin.shop.products.edit', $product);
    }

    public function edit(Product $product): View
    {
        $product->load('variants');

        return view('admin.shop.products.edit', [
            'product' => $product,
            'types' => self::TYPES,
            'coverCandidates' => Media::whereNull('trashed_at')->latest()->limit(60)->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:products,slug,'.$product->id],
            'type' => ['required', 'in:'.implode(',', array_keys(self::TYPES))],
            'description' => ['nullable', 'string'],
            'cover_media_id' => ['nullable', 'exists:media,id'],
            'status' => ['required', 'in:draft,published'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $product->update($data);

        return redirect()->route('admin.shop.products.edit', $product)->with('status', 'product-updated');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.shop.products.index')->with('status', 'product-deleted');
    }
}
