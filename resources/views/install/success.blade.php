<x-install-layout :step="4" :title="'Focale est installé !'">
  <p class="notice" style="color:var(--danger);border-color:var(--danger);">
    Dernière chose importante : note ces codes de récupération quelque part de sûr.
    Ils ne seront plus jamais affichés et sont indispensables si tu perds l'accès à ton application d'authentification.
  </p>

  <div style="font-family:monospace;font-size:14px;line-height:2.2;text-align:center;margin-bottom:24px;">
    @foreach ($recoveryCodes as $code)
      {{ $code }}<br>
    @endforeach
  </div>

  <a href="{{ route('login') }}" class="btn" style="display:block;text-align:center;text-decoration:none;">Se connecter à l'administration</a>
</x-install-layout>
