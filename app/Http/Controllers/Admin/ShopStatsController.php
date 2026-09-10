<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\View\View;

class ShopStatsController extends Controller
{
    /**
     * Une commande ne compte comme chiffre d'affaires réel qu'une fois payée
     * — "pending" n'est qu'une intention de commande, "cancelled" n'a jamais
     * abouti.
     */
    private const COUNTED_STATUSES = ['paid', 'shipped', 'completed'];

    public function index(): View
    {
        $countedOrders = Order::whereIn('status', self::COUNTED_STATUSES);

        $totalRevenueCents = (clone $countedOrders)->sum('total_cents');
        $ordersCount = (clone $countedOrders)->count();

        $ordersByStatus = Order::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $topProducts = OrderItem::selectRaw('product_title, sum(quantity) as quantity_sold, sum(quantity * unit_price_cents) as revenue_cents')
            ->whereHas('order', fn ($q) => $q->whereIn('status', self::COUNTED_STATUSES))
            ->groupBy('product_title')
            ->orderByDesc('revenue_cents')
            ->limit(10)
            ->get();

        return view('admin.shop-stats.index', [
            'totalRevenueCents' => $totalRevenueCents,
            'ordersCount' => $ordersCount,
            'averageOrderCents' => $ordersCount > 0 ? intdiv($totalRevenueCents, $ordersCount) : 0,
            'ordersByStatus' => $ordersByStatus,
            'topProducts' => $topProducts,
        ]);
    }
}
