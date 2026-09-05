<x-admin-layout :active="'albums'" :title="'Albums'">
  <div class="topbar">
    <h1>Albums</h1>
    <form method="POST" action="{{ route('admin.albums.create') }}">
      @csrf
      <button type="submit" class="new-btn">
        <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></svg>
        Nouvel album
      </button>
    </form>
  </div>

  <form class="toolbar" method="GET">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher un album…">
    <a href="{{ route('admin.albums.index') }}" class="filter-chip {{ ! request('status') ? 'active' : '' }}">Tous ({{ $counts['all'] }})</a>
    <a href="{{ route('admin.albums.index', ['status' => 'published']) }}" class="filter-chip {{ request('status') === 'published' ? 'active' : '' }}">Publiés ({{ $counts['published'] }})</a>
    <a href="{{ route('admin.albums.index', ['status' => 'draft']) }}" class="filter-chip {{ request('status') === 'draft' ? 'active' : '' }}">Brouillons ({{ $counts['draft'] }})</a>
    <a href="{{ route('admin.albums.index', ['status' => 'archived']) }}" class="filter-chip {{ request('status') === 'archived' ? 'active' : '' }}">Archivés ({{ $counts['archived'] }})</a>
  </form>

  @if ($albums->isEmpty())
    <div class="panel">
      <p style="margin:0;color:var(--ink-soft);font-size:14px;">Aucun album pour l'instant.</p>
    </div>
  @else
    <div class="table-panel">
      <table>
        <thead>
          <tr>
            <th>Album</th>
            <th>Statut</th>
            <th>Visibilité</th>
            <th>Œuvres</th>
            <th>Modifié</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($albums as $album)
            <tr>
              <td>
                <div class="project-cell">
                  @if ($album->cover?->variant('thumbnail'))
                    <img src="{{ $album->cover->variant('thumbnail')->url() }}" alt="">
                  @else
                    <div style="width:48px;height:48px;border-radius:6px;background:var(--img-fallback);flex-shrink:0;"></div>
                  @endif
                  <div>
                    <div class="name">{{ $album->title }}</div>
                    <div class="slug">/album/{{ $album->slug }}</div>
                  </div>
                </div>
              </td>
              <td><span class="pill {{ $album->status }}">{{ ['draft' => 'Brouillon', 'unlisted' => 'Non répertorié', 'published' => 'Publié', 'archived' => 'Archivé'][$album->status] }}</span></td>
              <td>
                <span class="visibility">{{ ['public' => 'Publique', 'password' => 'Mot de passe', 'private' => 'Privée'][$album->visibility] }}</span>
              </td>
              <td>{{ $album->media_count }}</td>
              <td>{{ $album->updated_at->diffForHumans() }}</td>
              <td>
                <div class="row-actions">
                  <a href="{{ route('admin.albums.edit', $album) }}">Modifier</a>
                  <form method="POST" action="{{ route('admin.albums.duplicate', $album) }}">
                    @csrf
                    <button type="submit">Dupliquer</button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div style="margin-top:24px;"><x-pagination :paginator="$albums" /></div>
  @endif
</x-admin-layout>
