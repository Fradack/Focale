<x-admin-layout :active="'likes'" :title="'J\'aime'">
  <div class="topbar">
    <h1>J'aime <x-plugin-badge/></h1>
  </div>

  @if (session('status') === 'plugin-enabled:likes')
    <div class="alert alert-success">Plugin activé.</div>
  @elseif (session('status') === 'plugin-disabled:likes')
    <div class="alert alert-success">Plugin désactivé.</div>
  @endif

  <div class="panel">
    <p style="font-size:14px;color:var(--ink-soft);margin:0 0 16px;">
      Cœur/like anonyme sur les photos et les albums, comptabilisé par visiteur (cookie), sans compte requis.
      Désactiver ce plugin masque le bouton cœur partout sur le site public.
    </p>

    <div style="display:flex;gap:24px;margin-bottom:20px;">
      <div>
        <span style="display:block;font-size:22px;font-weight:600;">{{ number_format($mediaLikes) }}</span>
        <span style="font-size:12px;color:var(--ink-soft);">J'aime sur des photos/vidéos</span>
      </div>
      <div>
        <span style="display:block;font-size:22px;font-weight:600;">{{ number_format($albumLikes) }}</span>
        <span style="font-size:12px;color:var(--ink-soft);">J'aime sur des albums</span>
      </div>
    </div>

    @if ($plugin->enabled)
      <form method="POST" action="{{ route('admin.plugins.disable', 'likes') }}">
        @csrf
        <button type="submit" class="btn">Désactiver</button>
      </form>
    @else
      <form method="POST" action="{{ route('admin.plugins.enable', 'likes') }}">
        @csrf
        <button type="submit" class="btn primary">Activer</button>
      </form>
    @endif
  </div>
</x-admin-layout>
