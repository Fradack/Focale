<x-admin-layout :active="'tracking'" :title="'Tracking'">
  <style>
    /*
      Rampe séquentielle à teinte unique (bleu) pour la mini-carte des pays,
      clair -> foncé, validée avec le script de la skill dataviz contre les
      fonds réels de ce panneau (--panel clair #FBFAF7 et sombre #242019) :
      palier 1 = le moins de visites, palier 5 = le plus. Les deux jeux de
      valeurs inversent l'ancrage en mode sombre (le palier bas se fond vers
      le fond, le palier haut se détache), comme recommandé par la skill.
    */
    #tracking-map-panel {
      --tm-b1: #86b6ef;
      --tm-b2: #5598e7;
      --tm-b3: #2a78d6;
      --tm-b4: #1c5cab;
      --tm-b5: #104281;
      --tm-nodata: var(--img-fallback);
    }
    :root[data-theme="dark"] #tracking-map-panel {
      --tm-b1: #1c5cab;
      --tm-b2: #2a78d6;
      --tm-b3: #5598e7;
      --tm-b4: #86b6ef;
      --tm-b5: #b7d3f6;
    }

    #tracking-world-map { width: 100%; height: auto; display: block; }
    #tracking-world-map > g > [id] {
      fill: var(--tm-nodata);
      stroke: var(--panel);
      stroke-width: 0.6;
      transition: fill .15s, stroke-width .15s;
    }
    #tracking-world-map > g > [id]:hover { stroke: var(--ink); stroke-width: 1.4; }

    @foreach ($countryBuckets as $code => $bucket)
      #tracking-world-map #{{ $code }} { fill: var(--tm-b{{ $bucket }}); }
    @endforeach

    .tm-swatch { display: inline-block; width: 10px; height: 10px; border-radius: 2px; vertical-align: middle; }
    .tm-legend { display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--ink-soft); flex-wrap: wrap; margin-top: 10px; }
  </style>

  <div class="topbar">
    <div>
      <h1>Tracking <x-plugin-badge/></h1>
      <p>Visites enregistrées uniquement pour les visiteurs ayant accepté le suivi via le bandeau de cookies.</p>
    </div>
  </div>

  <div class="stats">
    <div class="stat-card">
      <span class="value">{{ number_format($total) }}</span>
      <span class="label">Visites enregistrées</span>
    </div>
    @foreach (['desktop' => 'Ordinateur', 'mobile' => 'Mobile', 'tablet' => 'Tablette'] as $key => $label)
      <div class="stat-card">
        <span class="value">{{ number_format($byDevice[$key] ?? 0) }}</span>
        <span class="label">{{ $label }}</span>
      </div>
    @endforeach
  </div>

  <div class="panel" style="margin-top:24px;">
    <h2>Système d'exploitation</h2>
    @if ($byOs->isEmpty())
      <p style="font-size:13px;color:var(--ink-soft);margin:0;">Pas encore de données.</p>
    @else
      <div class="stats">
        @foreach ($byOs as $os => $count)
          <div class="stat-card">
            <span class="value">{{ number_format($count) }}</span>
            <span class="label">{{ $os ?: 'Inconnu' }}</span>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  <div class="settings-grid" style="margin-top:24px;">
    <div class="panel" id="tracking-map-panel">
      <div class="panel-header">
        <h2>Localisation des visiteurs</h2>
        <span style="font-size:12px;color:var(--ink-soft);">Approximative, déduite de l'adresse IP — jamais de position précise.</span>
      </div>

      @if ($byCountry->isEmpty())
        <p style="font-size:13px;color:var(--ink-soft);margin:0;">Pas encore de données.</p>
      @else
        <div style="max-width:520px;margin:0 auto;">
          @include('plugins.tracking.admin.world-map')
        </div>
        <div class="tm-legend">
          <span>Intensité :</span>
          <span class="tm-swatch" style="background:var(--tm-nodata);"></span> <span>Aucune donnée</span>
          @for ($b = 1; $b <= 5; $b++)
            <span class="tm-swatch" style="background:var(--tm-b{{ $b }});"></span>
          @endfor
          <span>Peu &rarr; beaucoup</span>
        </div>

        <div id="tracking-map-tooltip" style="position:fixed;display:none;z-index:20;background:var(--ink);color:var(--bg);font-size:12px;line-height:1.4;padding:6px 10px;border-radius:6px;white-space:nowrap;pointer-events:none;box-shadow:0 4px 12px rgba(0,0,0,0.18);"></div>

        <script>
          (function () {
            const svg = document.getElementById('tracking-world-map');
            const tooltip = document.getElementById('tracking-map-tooltip');
            if (!svg || !tooltip) return;

            const data = @json($byCountry->mapWithKeys(fn ($row) => [
                strtolower($row->country_code) => ['name' => $row->country_name, 'total' => $row->total],
            ]));

            svg.querySelectorAll(':scope > g > [id]').forEach((el) => {
              const code = el.id.toUpperCase();
              const entry = data[el.id];

              el.addEventListener('mousemove', (event) => {
                tooltip.textContent = entry
                  ? `${entry.name ?? code} — ${entry.total} visite(s)`
                  : `${code} — aucune visite`;
                tooltip.style.display = 'block';

                const pad = 14;
                let left = event.clientX + pad;
                let top = event.clientY + pad;
                tooltip.style.left = `${left}px`;
                tooltip.style.top = `${top}px`;
              });

              el.addEventListener('mouseleave', () => { tooltip.style.display = 'none'; });
            });
          })();
        </script>
      @endif
    </div>

    <div class="panel">
      <h2>Top pays</h2>
      @if ($byCountry->isEmpty())
        <p style="font-size:13px;color:var(--ink-soft);margin:0;">Pas encore de données.</p>
      @else
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
          @foreach ($byCountry as $row)
            @php $bucket = $countryBuckets[strtolower($row->country_code)] ?? null; @endphp
            <tr>
              <td style="padding:7px 0;border-bottom:1px solid var(--line);width:16px;">
                @if ($bucket)
                  <span class="tm-swatch" style="background:var(--tm-b{{ $bucket }});"></span>
                @endif
              </td>
              <td style="padding:7px 0;border-bottom:1px solid var(--line);">{{ $row->country_name ?? $row->country_code }}</td>
              <td style="padding:7px 0;border-bottom:1px solid var(--line);text-align:right;color:var(--ink-soft);">{{ number_format($row->total) }}</td>
            </tr>
          @endforeach
        </table>
      @endif
    </div>
  </div>

  <div class="panel" style="margin-top:24px;">
    <h2>Visites récentes</h2>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>Page</th>
            <th>Commune</th>
            <th>Pays</th>
            <th>Appareil</th>
            <th>OS</th>
            <th>Temps passé</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($recent as $visit)
            <tr>
              <td>{{ $visit->path }}</td>
              <td>{{ $visit->commune ?? '—' }}</td>
              <td>{{ $visit->country ?? '—' }}{{ $visit->country_code ? ' ('.$visit->country_code.')' : '' }}</td>
              <td>{{ $visit->device_type ?? '—' }}</td>
              <td>{{ $visit->os ?? '—' }}</td>
              <td>{{ $visit->duration_seconds !== null ? $visit->duration_seconds.' s' : '—' }}</td>
              <td>{{ $visit->created_at?->diffForHumans() }}</td>
            </tr>
          @empty
            <tr><td colspan="7" style="color:var(--ink-soft);">Aucune visite enregistrée pour l'instant.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</x-admin-layout>
