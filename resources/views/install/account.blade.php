<x-install-layout :step="1" :title="'Créer le compte administrateur'">
  <p class="subtitle">Ce compte sera l'unique administrateur de ce site Focale.</p>

  <form method="POST" action="{{ route('install.account.store') }}">
    @csrf
    <div class="field-row">
      <div class="field">
        <label>Prénom</label>
        <input type="text" name="first_name" value="{{ old('first_name') }}" required autofocus>
        @error('first_name') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Nom</label>
        <input type="text" name="last_name" value="{{ old('last_name') }}" required>
        @error('last_name') <p class="error">{{ $message }}</p> @enderror
      </div>
    </div>
    <div class="field">
      <label>Adresse e-mail</label>
      <input type="email" name="email" value="{{ old('email') }}" required>
      @error('email') <p class="error">{{ $message }}</p> @enderror
    </div>
    <x-password-field name="password" label="Mot de passe" autocomplete="new-password"
      hint="9 caractères minimum, avec majuscule, minuscule, chiffre et caractère spécial." />

    <x-password-field name="password_confirmation" label="Confirmer le mot de passe" autocomplete="new-password" />

    <button type="submit" class="btn">Continuer</button>
  </form>
</x-install-layout>
