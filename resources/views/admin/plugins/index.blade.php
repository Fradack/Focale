<x-admin-layout :active="'plugins'" :title="'Plugins'">
  <div class="topbar">
    <h1>Plugins</h1>
  </div>

  @error('plugin')
    <div class="alert alert-danger">{{ $message }}</div>
  @enderror

  @if (str_starts_with(session('status') ?? '', 'plugin-installed:'))
    <div class="alert alert-success">Plugin installé. Vous pouvez maintenant l'activer.</div>
  @elseif (str_starts_with(session('status') ?? '', 'plugin-enabled:'))
    <div class="alert alert-success">Plugin activé.</div>
  @elseif (str_starts_with(session('status') ?? '', 'plugin-disabled:'))
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

  <h2 style="font-size:13px;text-transform:uppercase;letter-spacing:0.03em;color:var(--ink-soft);margin:0 0 12px;">Installés</h2>
  <div class="updates-grid">
    @foreach ($installed as $slug => $plugin)
      <div class="panel">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
          <h3 style="margin:0;font-size:16px;font-family:'Fraunces',serif;font-weight:500;">{{ $plugin->label }}</h3>
          <x-plugin-badge/>
        </div>
        @if ($plugin->description)
          <p style="font-size:13px;color:var(--ink-soft);margin:0 0 12px;">{{ $plugin->description }}</p>
        @endif
        @if ($plugin->version)
          <p style="font-size:12px;color:var(--ink-soft);margin:0 0 12px;">Version {{ $plugin->version }}</p>
        @endif

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
      </div>
    @endforeach
  </div>

  @php($notInstalled = collect($catalog)->reject(fn ($manifest, $slug) => $installed->has($slug)))
  @if ($notInstalled->isNotEmpty())
    <h2 style="font-size:13px;text-transform:uppercase;letter-spacing:0.03em;color:var(--ink-soft);margin:24px 0 12px;">Disponibles</h2>
    <div class="updates-grid">
      @foreach ($notInstalled as $slug => $manifest)
        <div class="panel">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
            <h3 style="margin:0;font-size:16px;font-family:'Fraunces',serif;font-weight:500;">{{ $manifest['label'] ?? $slug }}</h3>
            <x-plugin-badge/>
          </div>
          @if (!empty($manifest['description']))
            <p style="font-size:13px;color:var(--ink-soft);margin:0 0 12px;">{{ $manifest['description'] }}</p>
          @endif
          <form method="POST" action="{{ route('admin.plugins.install', $slug) }}" class="install-plugin-form"
                data-confirm="Télécharger et installer ce plugin ?">
            @csrf
            <button type="submit" class="btn primary">Installer</button>
          </form>
        </div>
      @endforeach
    </div>
  @endif

  <script>
    document.querySelectorAll('.install-plugin-form').forEach((form) => {
      form.addEventListener('submit', function () {
        if (form.dataset.confirmed !== '1') return;
        const btn = form.querySelector('button');
        btn.disabled = true;
        btn.textContent = 'Installation…';
        document.getElementById('install-progress').hidden = false;
      });
    });
  </script>
</x-admin-layout>
