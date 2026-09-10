<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Mon compte — Focale</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg: #E7E3DC; --panel: #EFEDE7; --ink: #1E1C19; --ink-soft: #5C574E; --clay: #7A4B33; --line: #C9C2B4; }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: var(--bg); color: var(--ink); font-family: 'Work Sans', sans-serif; }
  a { color: inherit; }
  header { display: flex; align-items: center; justify-content: space-between; padding: 24px 6vw; border-bottom: 1px solid var(--line); }
  .wordmark { font-family: 'Fraunces', serif; font-weight: 500; font-size: 20px; text-decoration: none; }
  main { max-width: 1680px; margin: 8vh auto; padding: 0 6vw; }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: 28px; margin: 0 0 8px; }
  p { color: var(--ink-soft); line-height: 1.6; }
  .card { background: var(--panel); border: 1px solid var(--line); border-radius: 12px; padding: 24px; margin-top: 24px; }
  .card h2 { font-family: 'Fraunces', serif; font-weight: 500; font-size: 17px; margin: 0 0 14px; }
  button { padding: 10px 18px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel); color: var(--ink); font-size: 13px; font-family: inherit; cursor: pointer; }
  button:hover { border-color: var(--clay); }
  .empty { color: var(--ink-soft); font-size: 13px; margin: 0; }
  .comment-row { padding: 12px 0; border-top: 1px solid var(--line); }
  .comment-row:first-child { border-top: none; padding-top: 0; }
  .comment-row a { font-weight: 500; text-decoration: underline; }
  .comment-row p { margin: 4px 0 0; font-size: 14px; }
  .badge { display: inline-block; font-size: 11px; padding: 2px 8px; border-radius: 999px; margin-left: 8px; }
  .badge-approved { background: #DCE7DC; color: #2F4A2F; }
  .badge-pending { background: #EDE0C6; color: #6B4E1A; }
  .like-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 10px; }
  .like-grid a { display: block; }
  .like-grid img { width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 8px; border: 1px solid var(--line); }
  .album-list { list-style: none; margin: 0; padding: 0; }
  .album-list li { padding: 10px 0; border-top: 1px solid var(--line); }
  .album-list li:first-child { border-top: none; padding-top: 0; }
  .album-list a { text-decoration: underline; }
</style>
</head>
<body>
<header>
  <a class="wordmark" href="{{ route('home') }}">Focale</a>
</header>
<main>
  <h1>Bonjour {{ $user->name }}</h1>
  <p>Votre espace personnel Focale.</p>
  <div class="card">
    <p style="margin:0;"><strong style="color:var(--ink);">E-mail :</strong> {{ $user->email }}</p>
  </div>
  <div class="card">
    <p style="margin:0 0 12px;"><strong style="color:var(--ink);">Commandes</strong></p>
    <a href="{{ route('customer.orders.index') }}" style="text-decoration:underline;">Voir mes commandes →</a>
  </div>

  <div class="card">
    <h2>Mes commentaires</h2>
    @forelse ($comments as $comment)
      <div class="comment-row">
        @if ($comment->album)
          <a href="{{ route('public.album', $comment->album) }}">{{ $comment->album->title }}</a>
        @else
          <span>Album supprimé</span>
        @endif
        <span class="badge {{ $comment->status === 'approved' ? 'badge-approved' : 'badge-pending' }}">
          {{ $comment->status === 'approved' ? 'Publié' : 'En attente' }}
        </span>
        <p>{{ Str::limit($comment->body, 140) }}</p>
      </div>
    @empty
      <p class="empty">Vous n'avez pas encore commenté d'album.</p>
    @endforelse
  </div>

  <div class="card">
    <h2>Mes j'aime — Photos &amp; vidéos</h2>
    @if ($likedMedia->isNotEmpty())
      <div class="like-grid">
        @foreach ($likedMedia as $media)
          <a href="{{ route('public.image', $media) }}" title="{{ $media->title }}">
            <img src="{{ $media->variant('thumbnail')?->url() }}" alt="{{ $media->alt_text ?? $media->title }}">
          </a>
        @endforeach
      </div>
    @else
      <p class="empty">Vous n'avez encore aimé aucune photo ou vidéo.</p>
    @endif
  </div>

  <div class="card">
    <h2>Mes j'aime — Albums</h2>
    @if ($likedAlbums->isNotEmpty())
      <ul class="album-list">
        @foreach ($likedAlbums as $album)
          <li><a href="{{ route('public.album', $album) }}">{{ $album->title }}</a></li>
        @endforeach
      </ul>
    @else
      <p class="empty">Vous n'avez encore aimé aucun album.</p>
    @endif
  </div>

  <form method="POST" action="{{ route('customer.logout') }}" style="margin-top:24px;">
    @csrf
    <button type="submit">Se déconnecter</button>
  </form>
</main>
</body>
</html>
