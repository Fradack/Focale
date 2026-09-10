<x-admin-layout :active="'plugins'" :title="'Plugins'">
  <div class="topbar">
    <h1>Plugins</h1>
  </div>

  @error('plugin')
    <div class="alert alert-danger">{{ $message }}</div>
  @enderror

  <form class="toolbar" method="GET">
    <input type="search" name="q" value="{{ $query }}" placeholder="Rechercher un plugin (nom, description)…">
  </form>

  <h2 style="font-size:13px;text-transform:uppercase;letter-spacing:0.03em;color:var(--ink-soft);margin:0 0 12px;">Installés</h2>
  @if ($installed->isEmpty())
    <p style="font-size:13px;color:var(--ink-soft);">Aucun plugin installé ne correspond à « {{ $query }} ».</p>
  @else
    <div class="plugin-store-grid">
      @foreach ($installed as $slug => $plugin)
        <a href="{{ route('admin.plugins.show', $slug) }}" class="plugin-card">
          <div class="plugin-card-icon"><x-plugin-icon/></div>
          <div class="plugin-card-body">
            <div style="display:flex;align-items:center;gap:8px;">
              <h3>{{ $plugin->label }}</h3>
              <x-plugin-badge/>
            </div>
            @if ($plugin->description)
              <p>{{ Str::limit($plugin->description, 90) }}</p>
            @endif
            <span class="plugin-card-status {{ $plugin->enabled ? 'is-enabled' : '' }}">
              {{ $plugin->enabled ? 'Activé' : 'Désactivé' }}
            </span>
          </div>
        </a>
      @endforeach
    </div>
  @endif

  @php($notInstalled = collect($catalog)->reject(fn ($manifest, $slug) => $installed->has($slug)))
  <h2 style="font-size:13px;text-transform:uppercase;letter-spacing:0.03em;color:var(--ink-soft);margin:24px 0 12px;">Disponibles sur GitHub</h2>
  @if ($notInstalled->isEmpty())
    <p style="font-size:13px;color:var(--ink-soft);">
      @if ($query !== '')
        Aucun plugin correspondant à « {{ $query }} » trouvé sur GitHub.
      @else
        Aucun autre plugin disponible pour l'instant.
      @endif
    </p>
  @else
    <div class="plugin-store-grid">
      @foreach ($notInstalled as $slug => $manifest)
        <a href="{{ route('admin.plugins.show', $slug) }}" class="plugin-card">
          <div class="plugin-card-icon"><x-plugin-icon/></div>
          <div class="plugin-card-body">
            <div style="display:flex;align-items:center;gap:8px;">
              <h3>{{ $manifest['label'] ?? $slug }}</h3>
              <x-plugin-badge/>
            </div>
            @if (!empty($manifest['description']))
              <p>{{ Str::limit($manifest['description'], 90) }}</p>
            @endif
            <span class="plugin-card-status">Non installé</span>
          </div>
        </a>
      @endforeach
    </div>
  @endif
</x-admin-layout>
