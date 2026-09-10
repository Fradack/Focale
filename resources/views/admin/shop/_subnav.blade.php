@props(['active'])
<div class="toolbar" style="margin-bottom:20px;">
  <a href="{{ route('admin.shop.products.index') }}" class="filter-chip {{ $active === 'products' ? 'active' : '' }}">Produits</a>
  <a href="{{ route('admin.shop.orders.index') }}" class="filter-chip {{ $active === 'orders' ? 'active' : '' }}">Commandes</a>
  <a href="{{ route('admin.shop.payment-methods.index') }}" class="filter-chip {{ $active === 'payment' ? 'active' : '' }}">Paiement</a>
  <a href="{{ route('admin.shop.shipping-options.index') }}" class="filter-chip {{ $active === 'shipping' ? 'active' : '' }}">Livraison</a>
</div>
