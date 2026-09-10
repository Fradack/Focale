<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(): View
    {
        return view('admin.shop.payment-methods.index', [
            'methods' => PaymentMethod::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = (int) PaymentMethod::max('sort_order') + 1;
        $data['enabled'] = $request->boolean('enabled');

        PaymentMethod::create($data);

        return redirect()->route('admin.shop.payment-methods.index')->with('status', 'payment-method-created');
    }

    public function update(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $data = $this->validated($request);
        $data['enabled'] = $request->boolean('enabled');
        $data['sort_order'] = $request->integer('sort_order');

        $paymentMethod->update($data);

        return redirect()->route('admin.shop.payment-methods.index')->with('status', 'payment-method-updated');
    }

    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        $paymentMethod->delete();

        return redirect()->route('admin.shop.payment-methods.index')->with('status', 'payment-method-deleted');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:'.implode(',', array_keys(PaymentMethod::TYPES))],
            'paypal_link' => ['nullable', 'required_if:type,paypal', 'url', 'max:255'],
            'instructions' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
