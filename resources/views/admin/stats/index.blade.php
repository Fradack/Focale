<x-admin-layout :active="'stats'" :title="'Statistiques'">
  <div class="topbar">
    <div>
      <h1>Statistiques</h1>
      <p>Trafic anonyme (aucune adresse IP stockée — un visiteur = un cookie navigateur) sur les {{ $periodDays }} derniers jours, sauf mention contraire.</p>
    </div>
  </div>

  <div class="stats">
    <div class="stat-card">
      <span class="value">{{ number_format($periodUniqueVisitors) }}</span>
      <span class="label">Visiteurs uniques ({{ $periodDays }} j.)</span>
    </div>
    <div class="stat-card">
      <span class="value">{{ number_format($periodVisits) }}</span>
      <span class="label">Pages vues ({{ $periodDays }} j.)</span>
    </div>
    <div class="stat-card">
      <span class="value">{{ number_format($totalUniqueVisitors) }}</span>
      <span class="label">Visiteurs uniques (total)</span>
    </div>
    <div class="stat-card">
      <span class="value">{{ number_format($totalVisits) }}</span>
      <span class="label">Pages vues (total)</span>
    </div>
    <div class="stat-card">
      <span class="value">{{ number_format($totalViews) }}</span>
      <span class="label">Vues de photos (10 s+)</span>
    </div>
    <div class="stat-card">
      <span class="value">{{ number_format($totalLikes) }}</span>
      <span class="label">Likes ❤</span>
    </div>
  </div>

  <div class="panel">
    <div class="panel-header">
      <h2>Fréquentation quotidienne</h2>
      <span style="display:flex;align-items:center;gap:16px;font-size:12px;color:var(--ink-soft);">
        <span style="display:inline-flex;align-items:center;gap:6px;"><span style="width:10px;height:10px;border-radius:2px;background:var(--clay);display:inline-block;"></span>Pages vues</span>
        <span style="display:inline-flex;align-items:center;gap:6px;"><span style="width:10px;height:10px;border-radius:2px;background:var(--ok);display:inline-block;"></span>Visiteurs uniques</span>
      </span>
    </div>

    @php
      $maxVisits = max(1, $days->max('visits'));
    @endphp
    <div id="daily-chart" style="position:relative;display:flex;align-items:flex-end;gap:3px;height:160px;border-bottom:1px solid var(--line);padding-bottom:2px;">
      @foreach ($days as $day)
        <div class="daily-chart-col" style="flex:1;display:flex;align-items:flex-end;justify-content:center;gap:2px;height:100%;cursor:default;"
             data-tooltip="{{ \Illuminate\Support\Carbon::parse($day['date'])->isoFormat('D MMM') }} — {{ $day['visits'] }} page(s) vue(s), {{ $day['uniques'] }} visiteur(s) unique(s)">
          <div style="width:45%;height:{{ max(2, round($day['visits'] / $maxVisits * 100)) }}%;background:var(--clay);border-radius:2px 2px 0 0;pointer-events:none;"></div>
          <div style="width:45%;height:{{ max(2, round($day['uniques'] / $maxVisits * 100)) }}%;background:var(--ok);border-radius:2px 2px 0 0;pointer-events:none;"></div>
        </div>
      @endforeach
      <div id="daily-chart-tooltip" style="position:absolute;display:none;z-index:10;background:var(--ink);color:var(--bg);font-size:12px;line-height:1.4;padding:6px 10px;border-radius:6px;white-space:nowrap;pointer-events:none;box-shadow:0 4px 12px rgba(0,0,0,0.18);"></div>
    </div>

    <script>
      (function () {
        const chart = document.getElementById('daily-chart');
        const tooltip = document.getElementById('daily-chart-tooltip');
        if (!chart || !tooltip) return;

        chart.querySelectorAll('.daily-chart-col').forEach((col) => {
          col.addEventListener('mouseenter', () => {
            tooltip.textContent = col.dataset.tooltip;
            tooltip.style.display = 'block';

            const chartRect = chart.getBoundingClientRect();
            const colRect = col.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();

            // Au-dessus de la barre par défaut ; en dessous seulement s'il
            // n'y a pas la place au-dessus (haut de page/fenêtre) — jamais
            // hors écran dans un sens comme dans l'autre.
            const spaceAbove = colRect.top - tooltipRect.height - 8;
            const showBelow = spaceAbove < 0;

            tooltip.style.top = showBelow
              ? `${colRect.bottom - chartRect.top + 8}px`
              : `${colRect.top - chartRect.top - tooltipRect.height - 8}px`;

            let left = colRect.left - chartRect.left + colRect.width / 2 - tooltipRect.width / 2;
            left = Math.max(0, Math.min(left, chart.clientWidth - tooltipRect.width));
            tooltip.style.left = `${left}px`;
          });

          col.addEventListener('mouseleave', () => { tooltip.style.display = 'none'; });
        });
      })();
    </script>
    <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--ink-soft);margin-top:8px;">
      <span>{{ \Illuminate\Support\Carbon::parse($days->first()['date'])->isoFormat('D MMM') }}</span>
      <span>{{ \Illuminate\Support\Carbon::parse($days->last()['date'])->isoFormat('D MMM') }}</span>
    </div>

    <details style="margin-top:16px;">
      <summary style="font-size:12px;color:var(--ink-soft);cursor:pointer;">Voir le détail en tableau</summary>
      <div class="table-wrap" style="margin-top:10px;max-height:260px;overflow-y:auto;">
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
          <thead>
            <tr style="text-align:left;color:var(--ink-soft);">
              <th style="padding:6px 8px;border-bottom:1px solid var(--line);">Date</th>
              <th style="padding:6px 8px;border-bottom:1px solid var(--line);">Pages vues</th>
              <th style="padding:6px 8px;border-bottom:1px solid var(--line);">Visiteurs uniques</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($days->reverse() as $day)
              <tr>
                <td style="padding:6px 8px;border-bottom:1px solid var(--line);">{{ \Illuminate\Support\Carbon::parse($day['date'])->isoFormat('D MMMM YYYY') }}</td>
                <td style="padding:6px 8px;border-bottom:1px solid var(--line);">{{ $day['visits'] }}</td>
                <td style="padding:6px 8px;border-bottom:1px solid var(--line);">{{ $day['uniques'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </details>
  </div>

  <div class="settings-grid">
    <div class="panel">
      <h2>Pages les plus vues ({{ $periodDays }} j.)</h2>
      @if ($topPaths->isEmpty())
        <p style="font-size:13px;color:var(--ink-soft);margin:0;">Pas encore de données.</p>
      @else
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
          @foreach ($topPaths as $row)
            <tr>
              <td style="padding:7px 0;border-bottom:1px solid var(--line);">/{{ $row->path }}</td>
              <td style="padding:7px 0;border-bottom:1px solid var(--line);text-align:right;color:var(--ink-soft);">{{ number_format($row->visits) }}</td>
            </tr>
          @endforeach
        </table>
      @endif
    </div>

    <div class="panel">
      <h2>Référents ({{ $periodDays }} j.)</h2>
      @if ($topReferrers->isEmpty())
        <p style="font-size:13px;color:var(--ink-soft);margin:0;">Pas encore de données (accès directs uniquement).</p>
      @else
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
          @foreach ($topReferrers as $row)
            <tr>
              <td style="padding:7px 0;border-bottom:1px solid var(--line);word-break:break-all;">{{ \Illuminate\Support\Str::limit($row->referrer, 60) }}</td>
              <td style="padding:7px 0;border-bottom:1px solid var(--line);text-align:right;color:var(--ink-soft);white-space:nowrap;">{{ number_format($row->visits) }}</td>
            </tr>
          @endforeach
        </table>
      @endif
    </div>

    <div class="panel">
      <h2>Photos les plus aimées</h2>
      @if ($topLiked->isEmpty())
        <p style="font-size:13px;color:var(--ink-soft);margin:0;">Pas encore de likes.</p>
      @else
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
          @foreach ($topLiked as $item)
            <tr>
              <td style="padding:7px 0;border-bottom:1px solid var(--line);">
                <a href="{{ route('admin.media.edit', $item) }}">{{ $item->title ?: 'Sans titre' }}</a>
              </td>
              <td style="padding:7px 0;border-bottom:1px solid var(--line);text-align:right;color:var(--ink-soft);">❤ {{ $item->likes_count }}</td>
            </tr>
          @endforeach
        </table>
      @endif
    </div>

    <div class="panel">
      <h2>Photos les plus vues</h2>
      @if ($topViewed->isEmpty())
        <p style="font-size:13px;color:var(--ink-soft);margin:0;">Pas encore de vues.</p>
      @else
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
          @foreach ($topViewed as $item)
            <tr>
              <td style="padding:7px 0;border-bottom:1px solid var(--line);">
                <a href="{{ route('admin.media.edit', $item) }}">{{ $item->title ?: 'Sans titre' }}</a>
              </td>
              <td style="padding:7px 0;border-bottom:1px solid var(--line);text-align:right;color:var(--ink-soft);">{{ number_format($item->views_count) }}</td>
            </tr>
          @endforeach
        </table>
      @endif
    </div>
  </div>
</x-admin-layout>
