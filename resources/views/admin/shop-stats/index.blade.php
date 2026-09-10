<x-admin-layout :active="'shop-stats'" :title="'Statistiques boutique'">
  <div class="topbar">
    <div>
      <h1>Statistiques boutique <x-plugin-badge/></h1>
      <p>Calculées sur les commandes payées, expédiées ou terminées — les commandes en attente ou annulées ne comptent pas.</p>
    </div>
  </div>

  <div class="stats">
    <div class="stat-card">
      <span class="value">{{ number_format($totalRevenueCents / 100, 2, ',', ' ') }} €</span>
      <span class="label">Chiffre d'affaires total</span>
    </div>
    <div class="stat-card">
      <span class="value">{{ number_format($ordersCount) }}</span>
      <span class="label">Commandes comptabilisées</span>
    </div>
    <div class="stat-card">
      <span class="value">{{ number_format($averageOrderCents / 100, 2, ',', ' ') }} €</span>
      <span class="label">Panier moyen</span>
    </div>
  </div>

  <div class="settings-grid" style="margin-top:24px;">
    <div class="panel">
      <h2>Commandes par statut</h2>
      <table>
        <thead><tr><th>Statut</th><th>Nombre</th></tr></thead>
        <tbody>
          @foreach (\App\Models\Order::STATUSES as $key => $label)
            <tr>
              <td>{{ $label }}</td>
              <td>{{ number_format($ordersByStatus[$key] ?? 0) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="panel">
      <h2>Meilleures ventes</h2>
      <table>
        <thead><tr><th>Produit</th><th>Qté vendue</th><th>Revenu</th></tr></thead>
        <tbody>
          @forelse ($topProducts as $product)
            <tr>
              <td>{{ $product->product_title }}</td>
              <td>{{ number_format($product->quantity_sold) }}</td>
              <td>{{ number_format($product->revenue_cents / 100, 2, ',', ' ') }} €</td>
            </tr>
          @empty
            <tr><td colspan="3" style="color:var(--ink-soft);">Aucune vente pour l'instant.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</x-admin-layout>
