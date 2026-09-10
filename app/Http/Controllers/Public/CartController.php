<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\ProductVariant;
use App\Models\ShippingOption;
use App\Services\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CartController extends Controller
{
    public function show(Cart $cart): View
    {
        return view('public.shop.cart', [
            'items' => $cart->items(),
            'total' => $cart->totalFormatted(),
        ]);
    }

    public function store(Request $request, Cart $cart): RedirectResponse
    {
        $data = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $cart->add((int) $data['variant_id'], (int) ($data['quantity'] ?? 1));

        return back()->with('status', 'cart-added');
    }

    public function update(Request $request, ProductVariant $variant, Cart $cart): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:20'],
        ]);

        $cart->update($variant->id, (int) $data['quantity']);

        return back()->with('status', 'cart-updated');
    }

    public function destroy(ProductVariant $variant, Cart $cart): RedirectResponse
    {
        $cart->remove($variant->id);

        return back()->with('status', 'cart-updated');
    }

    public function showCheckout(Request $request, Cart $cart): View|RedirectResponse
    {
        if ($redirect = $this->requireCustomer($request)) {
            return $redirect;
        }

        $items = $cart->items();

        if ($items->isEmpty()) {
            return redirect()->route('public.cart.show');
        }

        return view('public.shop.checkout', [
            'items' => $items,
            'total' => $cart->totalFormatted(),
            'itemsTotalCents' => $cart->totalCents(),
            'shippingOptions' => ShippingOption::enabled()->get(),
            'paymentMethods' => PaymentMethod::enabled()->get(),
            'orderReference' => $this->checkoutReference(),
        ]);
    }

    /**
     * Code affiché dès la page de commande (avant même sa validation) pour
     * qu'un client payant via PayPal.me puisse le recopier dans son paiement
     * sans avoir à revenir en arrière. Conservé en session et réutilisé tel
     * quel comme order_number si la commande est bien passée juste après —
     * la confirmation affiche donc exactement le code déjà transmis.
     */
    private function checkoutReference(): string
    {
        $reference = session('checkout_reference');

        if (! $reference || Order::where('order_number', $reference)->exists()) {
            $reference = Order::generateOrderNumber();
            while (Order::where('order_number', $reference)->exists()) {
                $reference = Order::generateOrderNumber();
            }
            session(['checkout_reference' => $reference]);
        }

        return $reference;
    }

    public function placeOrder(Request $request, Cart $cart): RedirectResponse
    {
        if ($redirect = $this->requireCustomer($request)) {
            return $redirect;
        }

        $items = $cart->items();

        if ($items->isEmpty()) {
            return redirect()->route('public.cart.show');
        }

        $data = $request->validate([
            'shipping_option_id' => ['required', 'exists:shipping_options,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $shipping = ShippingOption::enabled()->findOrFail($data['shipping_option_id']);
        $payment = PaymentMethod::enabled()->findOrFail($data['payment_method_id']);
        $user = $request->user();
        $itemsTotalCents = $cart->totalCents();
        $totalCents = $itemsTotalCents + $shipping->price_cents;
        $orderNumber = $this->checkoutReference();
        $totalFormatted = number_format($totalCents / 100, 2, ',', ' ').' €';

        $order = DB::transaction(function () use ($items, $shipping, $payment, $user, $itemsTotalCents, $totalCents, $data, $orderNumber, $totalFormatted) {
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'shipping_option_id' => $shipping->id,
                'shipping_label' => $shipping->label,
                'shipping_price_cents' => $shipping->price_cents,
                'payment_method_id' => $payment->id,
                'payment_method_label' => $payment->label,
                'payment_instructions' => $payment->buildInstructions($orderNumber, $totalFormatted),
                'status' => 'pending',
                'items_total_cents' => $itemsTotalCents,
                'total_cents' => $totalCents,
                'customer_note' => $data['customer_note'] ?? null,
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['variant']->product->id,
                    'product_variant_id' => $item['variant']->id,
                    'product_title' => $item['variant']->product->title,
                    'variant_label' => $item['variant']->label,
                    'unit_price_cents' => $item['variant']->price_cents,
                    'quantity' => $item['quantity'],
                ]);
            }

            return $order;
        });

        $cart->clear();
        session()->forget('checkout_reference');

        return redirect()->route('customer.orders.show', $order)->with('status', 'order-placed');
    }

    private function requireCustomer(Request $request): ?RedirectResponse
    {
        if ($request->user()) {
            return null;
        }

        session(['url.intended' => $request->fullUrl()]);

        return redirect()->route('customer.login')->with('status', 'login-required-for-checkout');
    }
}
