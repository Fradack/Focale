<x-admin-layout :active="'shop-products'" :title="'Produits'">
  <div class="topbar">
    <h1>Produits</h1>
    <form method="POST" action="{{ route('admin.shop.products.store') }}">
      @csrf
      <button type="submit" class="new-btn">
        <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></svg>
        Nouveau produit
      </button>
    </form>
  </div>

  <form class="toolbar" method="GET">
    <a href="{{ route('admin.shop.products.index') }}" class="filter-chip {{ ! request('status') ? 'active' : '' }}">Tous ({{ $counts['all'] }})</a>
    <a href="{{ route('admin.shop.products.index', ['status' => 'published']) }}" class="filter-chip {{ request('status') === 'published' ? 'active' : '' }}">Publiés ({{ $counts['published'] }})</a>
    <a href="{{ route('admin.shop.products.index', ['status' => 'draft']) }}" class="filter-chip {{ request('status') === 'draft' ? 'active' : '' }}">Brouillons ({{ $counts['draft'] }})</a>
  </form>

  @if ($products->isEmpty())
    <div class="panel">
      <p style="margin:0;color:var(--ink-soft);font-size:14px;">Aucun produit pour l'instant.</p>
    </div>
  @else
    <div class="table-panel">
      <table>
        <thead>
          <tr>
            <th>Produit</th>
            <th>Type</th>
            <th>Statut</th>
            <th>Formats</th>
            <th>Modifié</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($products as $product)
            <tr>
              <td>
                <div class="project-cell">
                  @if ($product->coverMedia?->variant('thumbnail'))
                    <img src="{{ $product->coverMedia->variant('thumbnail')->url() }}" alt="">
                  @else
                    <div style="width:48px;height:48px;border-radius:6px;background:var(--img-fallback);flex-shrink:0;"></div>
                  @endif
                  <div>
                    <div class="name">{{ $product->title }}</div>
                    <div class="slug">/boutique/{{ $product->slug }}</div>
                  </div>
                </div>
              </td>
              <td>{{ $types[$product->type] ?? $product->type }}</td>
              <td><span class="pill {{ $product->status }}">{{ $product->status === 'published' ? 'Publié' : 'Brouillon' }}</span></td>
              <td>{{ $product->variants_count }}</td>
              <td>{{ $product->updated_at->diffForHumans() }}</td>
              <td>
                <div class="row-actions">
                  <a href="{{ route('admin.shop.products.edit', $product) }}">Modifier</a>
                  <form method="POST" action="{{ route('admin.shop.products.destroy', $product) }}" data-confirm="Supprimer le produit « {{ $product->title }} » ? Cette action est irréversible.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color:var(--danger);">Supprimer</button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div style="margin-top:24px;"><x-pagination :paginator="$products" /></div>
  @endif
</x-admin-layout>
