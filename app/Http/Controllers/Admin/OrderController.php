<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::query();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        $counts = ['all' => Order::count()];
        foreach (array_keys(Order::STATUSES) as $status) {
            $counts[$status] = Order::where('status', $status)->count();
        }

        return view('admin.shop.orders.index', [
            'orders' => $orders,
            'counts' => $counts,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load('items');

        return view('admin.shop.orders.show', ['order' => $order]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(Order::STATUSES))],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($data['status'] === 'paid' && $order->status !== 'paid' && ! $order->paid_at) {
            $data['paid_at'] = now();
        }

        if ($data['status'] === 'shipped' && $order->status !== 'shipped' && ! $order->shipped_at) {
            $data['shipped_at'] = now();
        }

        $order->update($data);

        return redirect()->route('admin.shop.orders.show', $order)->with('status', 'order-updated');
    }
}
