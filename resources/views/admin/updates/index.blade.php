<x-admin-layout :active="'updates'" :title="'Mises à jour'">
  <div class="topbar">
    <h1>Mises à jour</h1>
  </div>

  @error('update')
    <div class="panel" style="border-color:var(--danger);">
      <p style="margin:0;color:var(--danger);font-size:14px;">{{ $message }}</p>
    </div>
  @enderror

  @if (session('status') === 'update-applied')
    <div class="panel" style="border-color:var(--ok);">
      <p style="margin:0;color:var(--ok);font-size:14px;">Mise à jour appliquée avec succès.</p>
    </div>
  @endif

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
          <form method="POST" action="{{ route('admin.updates.apply') }}" onsubmit="return confirm('Une sauvegarde de la base et des fichiers sera créée avant la mise à jour. Continuer ?');">
            @csrf
            <button type="submit" class="btn primary">Mettre à jour</button>
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
</x-admin-layout>
