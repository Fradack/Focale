<x-admin-layout :active="'shop-products'" :title="$product->title">
  <a href="{{ route('admin.shop.products.index') }}" style="display:inline-flex;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">← Produits</a>

  <div class="topbar">
    <h1>{{ $product->title }}</h1>
    @if ($product->status === 'published')
      <a href="{{ route('public.shop.show', $product) }}" target="_blank" class="btn">Voir sur le site ↗</a>
    @endif
  </div>

  @include('admin.shop._subnav', ['active' => 'products'])

  <form method="POST" action="{{ route('admin.shop.products.update', $product) }}" style="max-width:720px;">
    @csrf
    @method('PUT')

    <div class="panel">
      <div class="field">
        <label>Titre</label>
        <input type="text" name="title" value="{{ old('title', $product->title) }}">
        @error('title') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $product->slug) }}">
        @error('slug') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field-row">
        <div class="field">
          <label>Type</label>
          <select name="type">
            @foreach ($types as $value => $label)
              <option value="{{ $value }}" @selected(old('type', $product->type) === $value)>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label>Statut</label>
          <select name="status">
            <option value="draft" @selected(old('status', $product->status) === 'draft')>Brouillon</option>
            <option value="published" @selected(old('status', $product->status) === 'published')>Publié</option>
          </select>
        </div>
        <div class="field" style="max-width:140px;">
          <label>Ordre</label>
          <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}">
        </div>
      </div>
      <div class="field">
        <label>Description</label>
        <textarea name="description" rows="4">{{ old('description', $product->description) }}</textarea>
      </div>
    </div>

    <div class="panel">
      <h2>Image de couverture</h2>
      @if ($coverCandidates->isEmpty())
        <p style="font-size:13px;color:var(--ink-soft);">Importe des œuvres dans la médiathèque pour pouvoir en choisir une.</p>
      @else
        <div class="media-grid" style="max-height:260px;overflow-y:auto;">
          <label class="media-item {{ (string) old('cover_media_id', $product->cover_media_id) === '' ? 'is-cover' : '' }}" style="cursor:pointer;">
            <input type="radio" name="cover_media_id" value="" style="position:absolute;top:8px;right:8px;" @checked((string) old('cover_media_id', $product->cover_media_id) === '')>
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--ink-soft);text-align:center;padding:4px;">Aucune</div>
          </label>
          @foreach ($coverCandidates as $item)
            <label class="media-item {{ (string) old('cover_media_id', $product->cover_media_id) === (string) $item->id ? 'is-cover' : '' }}" style="cursor:pointer;">
              <input type="radio" name="cover_media_id" value="{{ $item->id }}" style="position:absolute;top:8px;right:8px;" @checked((string) old('cover_media_id', $product->cover_media_id) === (string) $item->id)>
              @if ($thumb = $item->variant('thumbnail'))
                <img src="{{ $thumb->url() }}" alt="">
              @endif
            </label>
          @endforeach
        </div>
      @endif
    </div>

    <div class="panel" style="display:flex;gap:10px;">
      <button type="submit" class="btn primary" style="flex:1;">Enregistrer</button>
      @if (session('status') === 'product-updated')
        <span style="font-size:13px;color:var(--ok);align-self:center;">Enregistré.</span>
      @endif
    </div>
  </form>

  <div class="panel" style="max-width:720px;">
    <h2>Formats &amp; prix</h2>

    @if ($product->variants->isEmpty())
      <p style="font-size:13px;color:var(--ink-soft);margin-bottom:16px;">Aucun format pour l'instant — le produit ne sera pas achetable tant qu'il n'en a pas au moins un.</p>
    @else
      {{-- Chaque ligne a deux actions (mise à jour / suppression) : les <form>
           réels vivent hors du tableau et les champs s'y rattachent via
           l'attribut HTML `form="…"`, seule façon valide d'avoir plusieurs
           <form> par <tr> sans imbrication interdite. --}}
      <table style="margin-bottom:16px;">
        <thead>
          <tr><th>Libellé</th><th style="width:140px;">Prix (€)</th><th style="width:90px;">Ordre</th><th></th></tr>
        </thead>
        <tbody>
          @foreach ($product->variants as $variant)
            <tr>
              <td><input form="variant-update-{{ $variant->id }}" type="text" name="label" value="{{ $variant->label }}" style="width:100%;"></td>
              <td><input form="variant-update-{{ $variant->id }}" type="number" name="price" step="0.01" min="0" value="{{ number_format($variant->price_cents / 100, 2, '.', '') }}" style="width:100%;"></td>
              <td><input form="variant-update-{{ $variant->id }}" type="number" name="sort_order" value="{{ $variant->sort_order }}" style="width:100%;"></td>
              <td style="display:flex;gap:8px;">
                <button form="variant-update-{{ $variant->id }}" type="submit" class="btn" style="padding:6px 12px;font-size:12px;">Mettre à jour</button>
                <button form="variant-delete-{{ $variant->id }}" type="submit" style="padding:6px 12px;font-size:12px;color:var(--danger);">Supprimer</button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      @foreach ($product->variants as $variant)
        <form id="variant-update-{{ $variant->id }}" method="POST" action="{{ route('admin.shop.products.variants.update', [$product, $variant]) }}">
          @csrf
          @method('PUT')
        </form>
        <form id="variant-delete-{{ $variant->id }}" method="POST" action="{{ route('admin.shop.products.variants.destroy', [$product, $variant]) }}" data-confirm="Supprimer le format « {{ $variant->label }} » ?">
          @csrf
          @method('DELETE')
        </form>
      @endforeach
    @endif

    <form method="POST" action="{{ route('admin.shop.products.variants.store', $product) }}" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
      @csrf
      <div class="field" style="flex:1;min-width:200px;margin:0;">
        <label>Nouveau format (ex. « 30×40 cm »)</label>
        <input type="text" name="label" value="{{ old('label') }}">
      </div>
      <div class="field" style="width:140px;margin:0;">
        <label>Prix (€)</label>
        <input type="number" name="price" step="0.01" min="0" value="{{ old('price') }}">
      </div>
      <button type="submit" class="btn">Ajouter</button>
    </form>
  </div>
</x-admin-layout>
