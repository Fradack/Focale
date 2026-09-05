<x-install-layout :step="2" :title="'Double authentification'">
  <p class="subtitle">Scanne ce QR code avec Google Authenticator, Authy ou une application équivalente, puis saisis le code affiché pour confirmer.</p>

  <div style="display:flex;justify-content:center;margin-bottom:20px;">
    {!! $qrCodeSvg !!}
  </div>

  <p class="notice">
    Impossible de scanner ? Saisis ce code manuellement dans ton application : <br>
    <strong style="font-family:monospace;letter-spacing:0.05em;">{{ $secret }}</strong>
  </p>

  <form method="POST" action="{{ route('install.security.store') }}">
    @csrf
    <div class="field">
      <label>Code à 6 chiffres</label>
      <input type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" autofocus required>
      @error('code') <p class="error">{{ $message }}</p> @enderror
    </div>
    <button type="submit" class="btn">Vérifier et continuer</button>
  </form>

  <details style="margin-top:24px;font-size:13px;color:var(--ink-soft);">
    <summary style="cursor:pointer;">Codes de récupération (à conserver précieusement)</summary>
    <p style="margin:10px 0 0;">Si tu perds l'accès à ton application d'authentification, chacun de ces codes permet une connexion unique :</p>
    <div style="font-family:monospace;font-size:13px;line-height:2;margin-top:8px;">
      @foreach ($recoveryCodes as $code)
        {{ $code }}<br>
      @endforeach
    </div>
  </details>
</x-install-layout>
