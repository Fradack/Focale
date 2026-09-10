<x-admin-layout :active="'tracking'" :title="'Tracking'">
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
    <h2>Visites récentes</h2>
    <table>
      <thead>
        <tr>
          <th>Page</th>
          <th>Commune</th>
          <th>Pays</th>
          <th>Appareil</th>
          <th>Temps passé</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($recent as $visit)
          <tr>
            <td>{{ $visit->path }}</td>
            <td>{{ $visit->commune ?? '—' }}</td>
            <td>{{ $visit->country ?? '—' }}</td>
            <td>{{ $visit->device_type ?? '—' }}</td>
            <td>{{ $visit->duration_seconds !== null ? $visit->duration_seconds.' s' : '—' }}</td>
            <td>{{ $visit->created_at?->diffForHumans() }}</td>
          </tr>
        @empty
          <tr><td colspan="6" style="color:var(--ink-soft);">Aucune visite enregistrée pour l'instant.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-layout>
