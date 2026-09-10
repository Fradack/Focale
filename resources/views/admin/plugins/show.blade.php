<x-admin-layout :active="'plugins'" :title="$manifest['label'] ?? $plugin?->label ?? $slug">
  <a href="{{ route('admin.plugins.index') }}" style="display:inline-flex;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">← Plugins</a>

  <div class="topbar">
    <h1>{{ $manifest['label'] ?? $plugin?->label ?? $slug }} <x-plugin-badge/></h1>
  </div>

  @error('plugin')
    <div class="alert alert-danger">{{ $message }}</div>
  @enderror

  @if (session('status') === "plugin-installed:{$slug}")
    <div class="alert alert-success">Plugin installé. Vous pouvez maintenant l'activer.</div>
  @elseif (session('status') === "plugin-enabled:{$slug}")
    <div class="alert alert-success">Plugin activé.</div>
  @elseif (session('status') === "plugin-disabled:{$slug}")
    <div class="alert alert-success">Plugin désactivé.</div>
  @endif

  <div class="alert alert-info alert-block" id="install-progress" hidden>
    <div style="width:100%;">
      <strong>Installation en cours…</strong>
      <div style="margin-top:4px;">Ne quittez pas cette page et ne l'actualisez pas — l'opération peut prendre une minute.</div>
      <div class="progress-indeterminate-track" style="margin-top:10px;">
        <div class="progress-indeterminate-fill"></div>
      </div>
    </div>
  </div>

  <div class="updates-grid has-details">
    <div class="panel">
      <div class="plugin-detail-icon"><x-plugin-icon/></div>

      @if ($plugin)
        <p style="font-size:13px;color:var(--ink-soft);margin:0 0 4px;">
          Statut : <strong style="color:var(--ink);">{{ $plugin->enabled ? 'Activé' : 'Désactivé' }}</strong>
        </p>
      @else
        <p style="font-size:13px;color:var(--ink-soft);margin:0 0 4px;">Statut : <strong style="color:var(--ink);">Non installé</strong></p>
      @endif

      @if ($plugin?->version ?? $manifest['version'] ?? null)
        <p style="font-size:13px;color:var(--ink-soft);margin:0 0 16px;">Version {{ $plugin->version ?? $manifest['version'] }}</p>
      @endif

      @if ($plugin)
        @if ($plugin->enabled)
          <form method="POST" action="{{ route('admin.plugins.disable', $slug) }}">
            @csrf
            <button type="submit" class="btn">Désactiver</button>
          </form>
        @else
          <form method="POST" action="{{ route('admin.plugins.enable', $slug) }}">
            @csrf
            <button type="submit" class="btn primary">Activer</button>
          </form>
        @endif
      @elseif ($manifest)
        <form method="POST" action="{{ route('admin.plugins.install', $slug) }}" id="install-plugin-form"
              data-confirm="Télécharger et installer ce plugin ?">
          @csrf
          <button type="submit" class="btn primary" id="install-plugin-btn">Installer</button>
        </form>
      @else
        <p style="font-size:13px;color:var(--ink-soft);">Ce plugin n'est ni installé, ni trouvé sur GitHub.</p>
      @endif
    </div>

    <div class="panel">
      <h2 style="text-transform:none;letter-spacing:normal;font-size:18px;font-family:'Fraunces',serif;font-weight:500;color:var(--ink);">Description</h2>
      @php($description = $plugin->description ?? $manifest['description'] ?? null)
      @if ($description)
        <p style="font-size:14px;color:var(--ink);line-height:1.6;">{{ $description }}</p>
      @else
        <p style="font-size:13px;color:var(--ink-soft);">Aucune description disponible.</p>
      @endif
    </div>
  </div>

  <script>
    const installForm = document.getElementById('install-plugin-form');
    if (installForm) {
      installForm.addEventListener('submit', function () {
        if (installForm.dataset.confirmed !== '1') return;
        const btn = document.getElementById('install-plugin-btn');
        btn.disabled = true;
        btn.textContent = 'Installation…';
        document.getElementById('install-progress').hidden = false;
      });
    }
  </script>
</x-admin-layout>
