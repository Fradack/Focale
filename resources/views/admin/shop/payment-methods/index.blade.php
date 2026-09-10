<x-admin-layout :active="'shop-payment'" :title="'Paiement'">
  <div class="topbar">
    <h1>Moyens de paiement</h1>
  </div>

  <p style="font-size:13px;color:var(--ink-soft);max-width:640px;margin:-8px 0 20px;">
    Aucune passerelle de paiement en ligne n'est branchée : chaque moyen ci-dessous s'affiche au client au moment de la commande avec ses instructions, et la commande est marquée « Payée » manuellement depuis <a href="{{ route('admin.shop.orders.index') }}" style="text-decoration:underline;">Commandes</a> une fois le règlement reçu.
    Pour « PayPal.me », un code de commande est automatiquement ajouté aux instructions du client — inutile de le rédiger toi-même.
  </p>

  @if ($methods->isNotEmpty())
    <div class="table-panel" style="margin-bottom:24px;overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>Libellé</th>
            <th style="width:170px;">Type</th>
            <th>Lien PayPal.me</th>
            <th>Instructions</th>
            <th style="width:80px;">Activé</th>
            <th style="width:80px;">Ordre</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($methods as $method)
            <tr>
              <td><input form="pm-update-{{ $method->id }}" type="text" name="label" value="{{ $method->label }}" style="width:100%;"></td>
              <td>
                <select form="pm-update-{{ $method->id }}" name="type" style="width:100%;">
                  @foreach (\App\Models\PaymentMethod::TYPES as $value => $label)
                    <option value="{{ $value }}" @selected($method->type === $value)>{{ $label }}</option>
                  @endforeach
                </select>
              </td>
              <td><input form="pm-update-{{ $method->id }}" type="text" name="paypal_link" value="{{ $method->paypal_link }}" placeholder="https://paypal.me/votrenom" style="width:100%;"></td>
              <td><textarea form="pm-update-{{ $method->id }}" name="instructions" rows="2" style="width:100%;">{{ $method->instructions }}</textarea></td>
              <td><input form="pm-update-{{ $method->id }}" type="checkbox" name="enabled" value="1" @checked($method->enabled)></td>
              <td><input form="pm-update-{{ $method->id }}" type="number" name="sort_order" value="{{ $method->sort_order }}" style="width:100%;"></td>
              <td style="display:flex;gap:8px;">
                <button form="pm-update-{{ $method->id }}" type="submit" class="btn" style="padding:6px 12px;font-size:12px;">Enregistrer</button>
                <button form="pm-delete-{{ $method->id }}" type="submit" style="padding:6px 12px;font-size:12px;color:var(--danger);">Supprimer</button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @foreach ($methods as $method)
      <form id="pm-update-{{ $method->id }}" method="POST" action="{{ route('admin.shop.payment-methods.update', $method) }}">
        @csrf
        @method('PUT')
      </form>
      <form id="pm-delete-{{ $method->id }}" method="POST" action="{{ route('admin.shop.payment-methods.destroy', $method) }}" data-confirm="Supprimer le moyen de paiement « {{ $method->label }} » ?">
        @csrf
        @method('DELETE')
      </form>
    @endforeach

    @if (session('status'))
      <p style="font-size:13px;color:var(--ok);margin:-12px 0 20px;">Enregistré.</p>
    @endif
    @error('paypal_link') <p class="error" style="margin:-12px 0 20px;">{{ $message }}</p> @enderror
  @endif

  <div class="panel" style="max-width:600px;">
    <h2>Ajouter un moyen de paiement</h2>
    <form method="POST" action="{{ route('admin.shop.payment-methods.store') }}">
      @csrf
      <div class="field">
        <label>Libellé</label>
        <input type="text" name="label" value="{{ old('label') }}" placeholder="Ex. Virement bancaire, Chèque, Espèces au retrait, PayPal…">
        @error('label') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Type</label>
        <select name="type">
          @foreach (\App\Models\PaymentMethod::TYPES as $value => $label)
            <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label>Lien PayPal.me (uniquement pour le type « PayPal.me »)</label>
        <input type="text" name="paypal_link" value="{{ old('paypal_link') }}" placeholder="https://paypal.me/votrenom">
        @error('paypal_link') <p class="error">{{ $message }}</p> @enderror
        <p class="field-hint">Le code de commande à transmettre dans PayPal est ajouté automatiquement aux instructions — pas besoin de le mentionner ici.</p>
      </div>
      <div class="field">
        <label>Instructions complémentaires affichées au client (optionnel)</label>
        <textarea name="instructions" rows="3">{{ old('instructions') }}</textarea>
      </div>
      <label style="display:flex;align-items:center;gap:10px;font-size:14px;margin-bottom:16px;">
        <input type="checkbox" name="enabled" value="1" checked>
        Activé
      </label>
      <button type="submit" class="btn primary">Ajouter</button>
    </form>
  </div>
</x-admin-layout>
