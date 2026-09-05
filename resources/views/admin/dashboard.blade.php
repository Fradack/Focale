<x-admin-layout :active="'dashboard'" :title="'Tableau de bord'">
  <div class="topbar">
    <div>
      <h1>Bonjour, {{ auth()->user()->name }}</h1>
      <p class="date">{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</p>
    </div>
    <div class="status-badge">
      <span class="status-dot"></span>
      Site public
    </div>
  </div>

  <p style="color:var(--ink-soft);font-size:14px;">
    Le tableau de bord affichera les statistiques réelles (œuvres, albums, brouillons, espace disque)
    une fois la médiathèque et les albums en place.
  </p>

  <div class="shortcuts" style="display:flex;gap:14px;margin-top:24px;">
    <a href="{{ route('admin.media.import') }}" class="shortcut-btn primary">
      <svg viewBox="0 0 24 24"><path d="M12 3v14"></path><path d="M5 10l7-7 7 7"></path><path d="M5 21h14"></path></svg>
      Importer des œuvres
    </a>
    <a href="{{ route('admin.albums.create') }}" class="shortcut-btn">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></svg>
      Créer un album
    </a>
  </div>
</x-admin-layout>
