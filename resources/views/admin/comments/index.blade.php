<x-admin-layout :active="'comments'" :title="'Commentaires'">
  <div class="topbar">
    <h1>Commentaires</h1>
    <p>Fonctionnalité hors du périmètre initial de Focale (cœur produit sans commentaires publics) — ajoutée ici en extension optionnelle, désactivable par album depuis ses réglages.</p>
  </div>

  <form class="toolbar" method="GET">
    <a href="{{ route('admin.comments.index') }}" class="filter-chip {{ ! request('status') ? 'active' : '' }}">Tous ({{ $counts['all'] }})</a>
    <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" class="filter-chip {{ request('status') === 'pending' ? 'active' : '' }}">En attente ({{ $counts['pending'] }})</a>
    <a href="{{ route('admin.comments.index', ['status' => 'approved']) }}" class="filter-chip {{ request('status') === 'approved' ? 'active' : '' }}">Approuvés ({{ $counts['approved'] }})</a>
  </form>

  @if ($comments->isEmpty())
    <div class="panel"><p style="margin:0;color:var(--ink-soft);font-size:14px;">Aucun commentaire pour l'instant.</p></div>
  @else
    <div class="comment-list">
      @foreach ($comments as $comment)
        <div class="comment-row {{ $comment->status === 'pending' ? 'pending' : '' }}">
          <div class="comment-body">
            <div class="comment-meta">
              <span class="author">{{ $comment->author_name }}</span>
              <span class="date">— {{ $comment->created_at->diffForHumans() }}</span>
              <span class="album-tag">{{ $comment->album->title }}</span>
              <span class="status-tag {{ $comment->status }}">{{ $comment->status === 'pending' ? 'En attente' : 'Approuvé' }}</span>
            </div>
            <p class="comment-text">{{ $comment->body }}</p>
          </div>
          <div class="comment-actions">
            @if ($comment->status === 'pending')
              <form method="POST" action="{{ route('admin.comments.approve', $comment) }}">
                @csrf
                @method('patch')
                <button type="submit" class="action-btn approve">Approuver</button>
              </form>
            @else
              <button class="action-btn disabled" disabled>Approuvé</button>
            @endif
            <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" data-confirm="Supprimer ce commentaire ?">
              @csrf
              @method('delete')
              <button type="submit" class="action-btn delete">Supprimer</button>
            </form>
          </div>
        </div>
      @endforeach
    </div>

    <div style="margin-top:24px;"><x-pagination :paginator="$comments" /></div>
  @endif
</x-admin-layout>
