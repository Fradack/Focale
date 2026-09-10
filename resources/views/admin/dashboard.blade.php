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

  <div class="stats">
    <div class="stat-card">
      <span class="value">{{ number_format($publishedCount) }}</span>
      <span class="label">Œuvres publiées</span>
    </div>
    <div class="stat-card">
      <span class="value">{{ number_format($visitors7d) }}</span>
      <span class="label">Visiteurs uniques (7 j.)</span>
    </div>
    <div class="stat-card">
      <span class="value">{{ number_format($likesCount) }}</span>
      <span class="label">Likes ❤ au total</span>
    </div>
  </div>
  <p style="margin:-20px 0 24px;"><a href="{{ route('admin.stats.index') }}" style="font-size:13px;text-decoration:underline;color:var(--ink-soft);">Voir toutes les statistiques →</a></p>

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
