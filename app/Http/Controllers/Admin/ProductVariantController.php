<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $product->variants()->create([
            'label' => $data['label'],
            'price_cents' => (int) round($data['price'] * 100),
            'sort_order' => (int) $product->variants()->max('sort_order') + 1,
        ]);

        return redirect()->route('admin.shop.products.edit', $product)->with('status', 'variant-added');
    }

    public function update(Request $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $variant->update([
            'label' => $data['label'],
            'price_cents' => (int) round($data['price'] * 100),
            'sort_order' => $data['sort_order'] ?? $variant->sort_order,
        ]);

        return redirect()->route('admin.shop.products.edit', $product)->with('status', 'variant-updated');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        $variant->delete();

        return redirect()->route('admin.shop.products.edit', $product)->with('status', 'variant-deleted');
    }
}
