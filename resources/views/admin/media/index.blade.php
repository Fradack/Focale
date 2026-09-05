<x-admin-layout :active="'media'" :title="'Médiathèque'">
  <div class="topbar">
    <h1>Médiathèque</h1>
    <a href="{{ route('admin.media.import') }}" class="new-btn">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></svg>
      Importer des œuvres
    </a>
  </div>

  @php
    $baseQuery = request()->only('q');
  @endphp
  <form class="toolbar" method="GET">
    <input type="hidden" name="view" value="{{ $view }}">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher par titre ou tag…">
    <a href="{{ route('admin.media.index', $baseQuery + ['view' => $view]) }}" class="filter-chip {{ ! request('status') ? 'active' : '' }}">Tous ({{ $counts['all'] }})</a>
    <a href="{{ route('admin.media.index', $baseQuery + ['view' => $view, 'status' => 'published']) }}" class="filter-chip {{ request('status') === 'published' ? 'active' : '' }}">Publiées ({{ $counts['published'] }})</a>
    <a href="{{ route('admin.media.index', $baseQuery + ['view' => $view, 'status' => 'draft']) }}" class="filter-chip {{ request('status') === 'draft' ? 'active' : '' }}">Brouillons ({{ $counts['draft'] }})</a>

    <div class="view-toggle">
      <a href="{{ route('admin.media.index', $baseQuery + ['status' => request('status'), 'view' => 'grid']) }}" class="view-toggle-btn {{ $view === 'grid' ? 'active' : '' }}" aria-label="Vue grille">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect></svg>
      </a>
      <a href="{{ route('admin.media.index', $baseQuery + ['status' => request('status'), 'view' => 'list']) }}" class="view-toggle-btn {{ $view === 'list' ? 'active' : '' }}" aria-label="Vue liste">
        <svg viewBox="0 0 24 24"><path d="M8 6h13"></path><path d="M8 12h13"></path><path d="M8 18h13"></path><path d="M3 6h.01"></path><path d="M3 12h.01"></path><path d="M3 18h.01"></path></svg>
      </a>
    </div>
  </form>

  @if ($media->isEmpty())
    <div class="panel">
      <p style="margin:0;color:var(--ink-soft);font-size:14px;">
        Aucune œuvre pour l'instant. <a href="{{ route('admin.media.import') }}" style="text-decoration:underline;">Importez vos premières photos</a>.
      </p>
    </div>
  @elseif ($view === 'list')
    <div class="table-panel">
      <table>
        <thead>
          <tr><th>Œuvre</th><th>Statut</th><th>Taille</th><th>Importée</th></tr>
        </thead>
        <tbody>
          @foreach ($media as $item)
            <tr onclick="window.location='{{ route('admin.media.edit', $item) }}'" style="cursor:pointer;">
              <td>
                <div class="row-cell">
                  @if ($thumb = $item->variant('thumbnail'))
                    <img src="{{ $thumb->url() }}" alt="{{ $item->alt_text }}">
                  @else
                    <div style="width:48px;height:48px;border-radius:6px;background:var(--img-fallback);flex-shrink:0;"></div>
                  @endif
                  <div>
                    <div class="name">{{ $item->title ?: 'Sans titre' }}</div>
                    <div class="slug">{{ $item->width }}×{{ $item->height }}</div>
                  </div>
                </div>
              </td>
              <td><span class="pill {{ $item->status === 'published' ? 'published' : 'draft' }}">{{ $item->status === 'published' ? 'Publiée' : 'Brouillon' }}</span></td>
              <td>{{ number_format($item->filesize / 1048576, 1) }} Mo</td>
              <td>{{ $item->created_at->diffForHumans() }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div class="media-grid">
      @foreach ($media as $item)
        <a href="{{ route('admin.media.edit', $item) }}" class="media-item" style="cursor:pointer;">
          @if ($thumb = $item->variant('thumbnail'))
            <img src="{{ $thumb->url() }}" alt="{{ $item->alt_text }}">
          @else
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--ink-soft);">Traitement…</div>
          @endif
          @if ($item->status === 'draft')
            <span class="cover-badge" style="display:block;background:var(--warn);">Brouillon</span>
          @endif
        </a>
      @endforeach
    </div>
  @endif

  @if (! $media->isEmpty())
    <div style="margin-top:24px;">
      <x-pagination :paginator="$media" />
    </div>
  @endif
</x-admin-layout>
