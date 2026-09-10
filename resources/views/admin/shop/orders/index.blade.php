<x-admin-layout :active="'shop-orders'" :title="'Commandes'">
  <div class="topbar">
    <h1>Commandes</h1>
  </div>

  @include('admin.shop._subnav', ['active' => 'orders'])

  <form class="toolbar" method="GET">
    <a href="{{ route('admin.shop.orders.index') }}" class="filter-chip {{ ! request('status') ? 'active' : '' }}">Toutes ({{ $counts['all'] }})</a>
    @foreach (\App\Models\Order::STATUSES as $key => $label)
      <a href="{{ route('admin.shop.orders.index', ['status' => $key]) }}" class="filter-chip {{ request('status') === $key ? 'active' : '' }}">{{ $label }} ({{ $counts[$key] }})</a>
    @endforeach
  </form>

  @if ($orders->isEmpty())
    <div class="panel">
      <p style="margin:0;color:var(--ink-soft);font-size:14px;">Aucune commande pour l'instant.</p>
    </div>
  @else
    <div class="table-panel">
      <table>
        <thead>
          <tr>
            <th>Commande</th>
            <th>Client</th>
            <th>Statut</th>
            <th>Total</th>
            <th>Passée</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($orders as $order)
            <tr>
              <td>{{ $order->order_number }}</td>
              <td>{{ $order->customer_name }}<br><span class="slug">{{ $order->customer_email }}</span></td>
              <td><span class="pill {{ $order->status }}">{{ $order->statusLabel() }}</span></td>
              <td>{{ $order->totalFormatted() }}</td>
              <td>{{ $order->created_at->diffForHumans() }}</td>
              <td><a href="{{ route('admin.shop.orders.show', $order) }}">Voir</a></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div style="margin-top:24px;"><x-pagination :paginator="$orders" /></div>
  @endif
</x-admin-layout>
