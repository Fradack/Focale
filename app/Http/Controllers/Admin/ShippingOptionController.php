<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingOptionController extends Controller
{
    public function index(): View
    {
        return view('admin.shop.shipping-options.index', [
            'options' => ShippingOption::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = (int) ShippingOption::max('sort_order') + 1;
        $data['enabled'] = $request->boolean('enabled');

        ShippingOption::create($data);

        return redirect()->route('admin.shop.shipping-options.index')->with('status', 'shipping-option-created');
    }

    public function update(Request $request, ShippingOption $shippingOption): RedirectResponse
    {
        $data = $this->validated($request);
        $data['enabled'] = $request->boolean('enabled');
        $data['sort_order'] = $request->integer('sort_order');

        $shippingOption->update($data);

        return redirect()->route('admin.shop.shipping-options.index')->with('status', 'shipping-option-updated');
    }

    public function destroy(ShippingOption $shippingOption): RedirectResponse
    {
        $shippingOption->delete();

        return redirect()->route('admin.shop.shipping-options.index')->with('status', 'shipping-option-deleted');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        return [
            'label' => $data['label'],
            'price_cents' => (int) round($data['price'] * 100),
        ];
    }
}
