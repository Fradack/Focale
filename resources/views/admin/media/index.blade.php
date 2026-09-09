<x-admin-layout :active="'media'" :title="'Médiathèque'">
  <div class="topbar">
    <h1>Médiathèque</h1>
    <a href="{{ route('admin.media.import') }}" class="new-btn">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></svg>
      Importer des œuvres
    </a>
  </div>

  @if (session('status'))
    <p style="font-size:13px;color:var(--ok);margin:-14px 0 20px;">{{ session('status') }}</p>
  @endif

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
  @else
    <form method="POST" action="{{ route('admin.media.bulk') }}" id="bulk-form">
      @csrf

      <div class="bulk-bar" id="bulk-bar">
        <label style="display:flex;align-items:center;gap:8px;">
          <input type="checkbox" id="select-all">
          <span id="bulk-count">0 sélectionnée(s)</span>
        </label>
        <button type="submit" name="do" value="publish" class="btn">Publier</button>
        <button type="submit" name="do" value="unpublish" class="btn">Dépublier</button>
        <select name="album_id" style="padding:9px 11px;border:1px solid var(--line);border-radius:6px;font-size:13px;">
          <option value="">Ajouter à un album…</option>
          @foreach ($albums as $album)
            <option value="{{ $album->id }}">{{ $album->title }}</option>
          @endforeach
        </select>
        <button type="submit" name="do" value="add_to_album" class="btn">Ajouter</button>
        <button type="submit" name="do" value="trash" class="btn" style="color:var(--danger);border-color:var(--danger);margin-left:auto;" onsubmit="return confirm('Mettre ces œuvres à la corbeille ?');">Mettre à la corbeille</button>
      </div>

      @if ($view === 'list')
        <div class="table-panel">
          <table>
            <thead>
              <tr><th></th><th>Œuvre</th><th>Statut</th><th>Taille</th><th>Importée</th></tr>
            </thead>
            <tbody>
              @foreach ($media as $item)
                <tr>
                  <td style="width:30px;"><input type="checkbox" class="select-item" name="ids[]" value="{{ $item->id }}"></td>
                  <td>
                    <a href="{{ route('admin.media.edit', $item) }}" class="row-cell" style="text-decoration:none;color:inherit;">
                      @if ($thumb = $item->variant('thumbnail'))
                        <img src="{{ $thumb->url() }}" alt="{{ $item->alt_text }}">
                      @else
                        <div style="width:48px;height:48px;border-radius:6px;background:var(--img-fallback);flex-shrink:0;"></div>
                      @endif
                      <div>
                        <div class="name">{{ $item->title ?: 'Sans titre' }}</div>
                        <div class="slug">{{ $item->width }}×{{ $item->height }}</div>
                      </div>
                    </a>
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
        <div class="media-grid media-grid-dense">
          @foreach ($media as $item)
            <label class="media-item media-item-selectable">
              <input type="checkbox" class="select-item media-item-checkbox" name="ids[]" value="{{ $item->id }}">
              @if ($thumb = $item->variant('thumbnail'))
                <img src="{{ $thumb->url() }}" alt="{{ $item->alt_text }}">
              @else
                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:10px;color:var(--ink-soft);">Traitement…</div>
              @endif
              @if ($item->status === 'draft')
                <span class="cover-badge" style="display:block;background:var(--warn);">Brouillon</span>
              @endif
              <a href="{{ route('admin.media.edit', $item) }}" class="media-item-edit-link" aria-label="Modifier"></a>
            </label>
          @endforeach
        </div>
      @endif
    </form>
  @endif

  @if (! $media->isEmpty())
    <div style="margin-top:24px;">
      <x-pagination :paginator="$media" />
    </div>
  @endif

<script>
  const checkboxes = () => Array.from(document.querySelectorAll('.select-item'));
  const bulkBar = document.getElementById('bulk-bar');
  const bulkCount = document.getElementById('bulk-count');
  const selectAll = document.getElementById('select-all');

  function updateBulkBar() {
    const checked = checkboxes().filter(c => c.checked);
    bulkBar.classList.toggle('is-visible', checked.length > 0);
    bulkCount.textContent = `${checked.length} sélectionnée(s)`;
  }

  checkboxes().forEach(cb => cb.addEventListener('change', updateBulkBar));
  selectAll?.addEventListener('change', () => {
    checkboxes().forEach(cb => { cb.checked = selectAll.checked; });
    updateBulkBar();
  });

  document.getElementById('bulk-form')?.addEventListener('submit', (e) => {
    const submitter = e.submitter;
    if (submitter && submitter.value === 'trash' && !confirm('Mettre ces œuvres à la corbeille ?')) {
      e.preventDefault();
    }
  });

  updateBulkBar();
</script>
</x-admin-layout>
