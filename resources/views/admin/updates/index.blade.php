<x-admin-layout :active="'updates'" :title="'Mises à jour'">
  <div class="topbar">
    <h1>Mises à jour</h1>
  </div>

  @error('update')
    <div class="alert alert-danger">{{ $message }}</div>
  @enderror

  @if (session('status') === 'update-applied')
    <div class="alert alert-success">Mise à jour appliquée avec succès.</div>
  @endif

  <div class="alert alert-info" id="update-progress" hidden>
    <div style="width:100%;">
      <strong>Mise à jour en cours…</strong>
      <div style="margin-top:4px;">Ne quittez pas cette page et ne l'actualisez pas — l'opération peut prendre une minute.</div>
      <div class="progress-indeterminate-track" style="margin-top:10px;">
        <div class="progress-indeterminate-fill"></div>
      </div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:{{ $update['updateAvailable'] ? '360px 1fr' : '480px' }};gap:20px;align-items:start;">
    <div class="panel">
      <div class="field-row">
        <div class="field">
          <label>Version installée</label>
          <input type="text" value="{{ $update['current'] }}" disabled>
        </div>
        <div class="field">
          <label>Dernière version disponible</label>
          <input type="text" value="{{ $update['latest'] ?? '—' }}" disabled>
        </div>
      </div>

      @if ($update['updateAvailable'])
        <p style="font-size:14px;color:var(--clay);margin:0 0 16px;">Une mise à jour est disponible : {{ $update['current'] }} → {{ $update['latest'] }}</p>
      @else
        <p style="font-size:13px;color:var(--ink-soft);margin:0 0 16px;">Focale est à jour.</p>
      @endif

      <div style="display:flex;gap:10px;">
        <form method="POST" action="{{ route('admin.updates.check') }}">
          @csrf
          <button type="submit" class="btn">Vérifier maintenant</button>
        </form>

        @if ($update['updateAvailable'])
          <form method="POST" action="{{ route('admin.updates.apply') }}" id="apply-update-form">
            @csrf
            <button type="submit" class="btn primary" id="apply-update-btn">Mettre à jour</button>
          </form>
        @endif
      </div>
    </div>

    @if ($update['updateAvailable'])
      <div class="panel">
        <h2 style="text-transform:none;letter-spacing:normal;font-size:18px;font-family:'Fraunces',serif;font-weight:500;color:var(--ink);">
          {{ $update['name'] ?: 'Version '.$update['latest'] }}
        </h2>

        <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">
          @if ($update['publishedAt'])
            <span>Publiée le {{ \Illuminate\Support\Carbon::parse($update['publishedAt'])->translatedFormat('d F Y') }}</span>
          @endif
          @if ($update['assetSize'])
            <span>{{ number_format($update['assetSize'] / 1048576, 1) }} Mo à télécharger</span>
          @endif
          @if ($update['htmlUrl'])
            <a href="{{ $update['htmlUrl'] }}" target="_blank" rel="noopener" style="text-decoration:underline;">Voir sur GitHub ↗</a>
          @endif
        </div>

        <h3 style="font-size:13px;text-transform:uppercase;letter-spacing:0.03em;color:var(--ink-soft);margin:0 0 10px;">Notes de version</h3>
        @if ($update['notes'])
          <div style="font-size:14px;line-height:1.7;color:var(--ink);white-space:pre-wrap;">{{ $update['notes'] }}</div>
        @else
          <p style="font-size:13px;color:var(--ink-soft);margin:0;">Aucune note de version fournie pour cette release.</p>
        @endif
      </div>
    @endif
  </div>

  @if ($update['updateAvailable'])
    <script>
      document.getElementById('apply-update-form').addEventListener('submit', function (e) {
        if (! confirm('Une sauvegarde de la base et des fichiers sera créée avant la mise à jour. Continuer ?')) {
          e.preventDefault();
          return;
        }

        const btn = document.getElementById('apply-update-btn');
        btn.disabled = true;
        btn.textContent = 'Mise à jour en cours…';
        document.getElementById('update-progress').hidden = false;
        window.onbeforeunload = () => 'Une mise à jour est en cours.';
      });
    </script>
  @endif
</x-admin-layout>
