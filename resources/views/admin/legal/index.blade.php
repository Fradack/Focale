<x-admin-layout :active="'legal'" :title="'Documents légaux'">
  <div class="topbar">
    <h1>Documents légaux</h1>
    <p>Mentions légales, CGU et CGV — publiées, elles apparaissent automatiquement dans le pied de page du site.</p>
  </div>

  <div class="panel">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr style="text-align:left;color:var(--ink-soft);font-size:13px;">
          <th style="padding:10px 0;border-bottom:1px solid var(--line);">Document</th>
          <th style="padding:10px 0;border-bottom:1px solid var(--line);">Statut</th>
          <th style="padding:10px 0;border-bottom:1px solid var(--line);"></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($documents as $slug => $doc)
          <tr>
            <td style="padding:12px 0;border-bottom:1px solid var(--line);">{{ $doc['label'] }}</td>
            <td style="padding:12px 0;border-bottom:1px solid var(--line);">
              @if ($doc['page'])
                <span class="pill {{ $doc['page']->status === 'published' ? 'published' : 'draft' }}">{{ $doc['page']->status === 'published' ? 'Publiée' : 'Brouillon' }}</span>
              @else
                <span style="font-size:13px;color:var(--danger);">Introuvable</span>
              @endif
            </td>
            <td style="padding:12px 0;border-bottom:1px solid var(--line);text-align:right;">
              @if ($doc['page'])
                <a href="{{ route('admin.pages.edit', $doc['page']) }}" class="btn">Modifier</a>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-layout>
