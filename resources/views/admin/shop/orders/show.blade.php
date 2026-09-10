<x-admin-layout :active="'shop-orders'" :title="$order->order_number">
  <a href="{{ route('admin.shop.orders.index') }}" style="display:inline-flex;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">← Commandes</a>

  <div class="topbar">
    <h1>{{ $order->order_number }}</h1>
    <span class="pill {{ $order->status }}">{{ $order->statusLabel() }}</span>
  </div>

  <div class="settings-grid">
    <div class="panel">
      <h2>Articles</h2>
      <table style="margin-bottom:0;">
        <thead>
          <tr><th>Produit</th><th>Format</th><th>Qté</th><th>Prix unitaire</th><th>Sous-total</th></tr>
        </thead>
        <tbody>
          @foreach ($order->items as $item)
            <tr>
              <td>{{ $item->product_title }}</td>
              <td>{{ $item->variant_label }}</td>
              <td>{{ $item->quantity }}</td>
              <td>{{ number_format($item->unit_price_cents / 100, 2, ',', ' ') }} €</td>
              <td>{{ $item->subtotalFormatted() }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div style="display:flex;flex-direction:column;gap:4px;margin-top:16px;padding-top:16px;border-top:1px solid var(--line);font-size:14px;">
        <div style="display:flex;justify-content:space-between;"><span>Articles</span><span>{{ number_format($order->items_total_cents / 100, 2, ',', ' ') }} €</span></div>
        <div style="display:flex;justify-content:space-between;color:var(--ink-soft);"><span>Livraison ({{ $order->shipping_label }})</span><span>{{ number_format($order->shipping_price_cents / 100, 2, ',', ' ') }} €</span></div>
        <div style="display:flex;justify-content:space-between;font-weight:500;font-size:16px;margin-top:6px;"><span>Total</span><span>{{ $order->totalFormatted() }}</span></div>
      </div>
    </div>

    <div class="panel">
      <h2>Client</h2>
      <p style="margin:0 0 4px;">{{ $order->customer_name }}</p>
      <p style="margin:0 0 16px;color:var(--ink-soft);">{{ $order->customer_email }}</p>

      <h2>Paiement</h2>
      <p style="margin:0 0 16px;">{{ $order->payment_method_label }}</p>

      @if ($order->customer_note)
        <h2>Message du client</h2>
        <p style="margin:0 0 16px;white-space:pre-line;">{{ $order->customer_note }}</p>
      @endif
    </div>

    <div class="panel" style="grid-column:1 / -1;">
      <h2>Gestion</h2>
      <form method="POST" action="{{ route('admin.shop.orders.update', $order) }}">
        @csrf
        @method('PUT')
        <div class="field-row">
          <div class="field">
            <label>Statut</label>
            <select name="status">
              @foreach (\App\Models\Order::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $order->status) === $value)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="field">
          <label>Note interne (jamais visible du client)</label>
          <textarea name="admin_note" rows="3">{{ old('admin_note', $order->admin_note) }}</textarea>
        </div>
        <div style="display:flex;align-items:center;gap:14px;">
          <button type="submit" class="btn primary">Enregistrer</button>
          @if (session('status') === 'order-updated')
            <span style="font-size:13px;color:var(--ok);">Enregistré.</span>
          @endif
        </div>
      </form>
    </div>
  </div>
</x-admin-layout>
