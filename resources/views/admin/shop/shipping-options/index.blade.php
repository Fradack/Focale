<x-admin-layout :active="'shop-shipping'" :title="'Livraison'">
  <div class="topbar">
    <h1>Options de livraison</h1>
  </div>

  @include('admin.shop._subnav', ['active' => 'shipping'])

  <p style="font-size:13px;color:var(--ink-soft);max-width:640px;margin:-8px 0 20px;">
    Tarifs fixes uniquement — pas de calcul automatique par poids ou par zone. Le client choisit l'une de ces options au moment de la commande.
  </p>

  @if ($options->isNotEmpty())
    <div class="table-panel" style="margin-bottom:24px;">
      <table>
        <thead>
          <tr><th>Libellé</th><th style="width:140px;">Prix (€)</th><th style="width:90px;">Activé</th><th style="width:90px;">Ordre</th><th></th></tr>
        </thead>
        <tbody>
          @foreach ($options as $option)
            <tr>
              <td><input form="ship-update-{{ $option->id }}" type="text" name="label" value="{{ $option->label }}" style="width:100%;"></td>
              <td><input form="ship-update-{{ $option->id }}" type="number" name="price" step="0.01" min="0" value="{{ number_format($option->price_cents / 100, 2, '.', '') }}" style="width:100%;"></td>
              <td><input form="ship-update-{{ $option->id }}" type="checkbox" name="enabled" value="1" @checked($option->enabled)></td>
              <td><input form="ship-update-{{ $option->id }}" type="number" name="sort_order" value="{{ $option->sort_order }}" style="width:100%;"></td>
              <td style="display:flex;gap:8px;">
                <button form="ship-update-{{ $option->id }}" type="submit" class="btn" style="padding:6px 12px;font-size:12px;">Enregistrer</button>
                <button form="ship-delete-{{ $option->id }}" type="submit" style="padding:6px 12px;font-size:12px;color:var(--danger);">Supprimer</button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @foreach ($options as $option)
      <form id="ship-update-{{ $option->id }}" method="POST" action="{{ route('admin.shop.shipping-options.update', $option) }}">
        @csrf
        @method('PUT')
      </form>
      <form id="ship-delete-{{ $option->id }}" method="POST" action="{{ route('admin.shop.shipping-options.destroy', $option) }}" data-confirm="Supprimer l'option « {{ $option->label }} » ?">
        @csrf
        @method('DELETE')
      </form>
    @endforeach

    @if (session('status'))
      <p style="font-size:13px;color:var(--ok);margin:-12px 0 20px;">Enregistré.</p>
    @endif
  @endif

  <div class="panel" style="max-width:600px;">
    <h2>Ajouter une option de livraison</h2>
    <form method="POST" action="{{ route('admin.shop.shipping-options.store') }}">
      @csrf
      <div class="field">
        <label>Libellé</label>
        <input type="text" name="label" value="{{ old('label') }}" placeholder="Ex. Retrait à l'atelier, Colissimo…">
        @error('label') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field" style="max-width:160px;">
        <label>Prix (€)</label>
        <input type="number" name="price" step="0.01" min="0" value="{{ old('price', 0) }}">
      </div>
      <label style="display:flex;align-items:center;gap:10px;font-size:14px;margin-bottom:16px;">
        <input type="checkbox" name="enabled" value="1" checked>
        Activé
      </label>
      <button type="submit" class="btn primary">Ajouter</button>
    </form>
  </div>
</x-admin-layout>
