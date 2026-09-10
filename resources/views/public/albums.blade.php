<!DOCTYPE html>
<html lang="fr"{!! \App\Support\Theme::publicHtmlAttr() !!}>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Albums — {{ \App\Models\Setting::get('site_name', 'Focale') }}</title>
<link rel="canonical" href="{{ route('public.albums') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg: #E7E3DC; --ink: #1E1C19; --ink-soft: #5C574E; --line: #C9C2B4; --img-fallback: #D8D3C7; }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: var(--bg); color: var(--ink); font-family: 'Work Sans', sans-serif; }
  a { color: inherit; text-decoration: none; }
  header { display: flex; align-items: center; justify-content: space-between; padding: 24px 6vw; border-bottom: 1px solid var(--line); }
  .wordmark { font-family: 'Fraunces', serif; font-weight: 500; font-size: 20px; }
  nav { display: flex; gap: 28px; font-size: 14px; color: var(--ink-soft); }
  nav a:hover { color: var(--ink); }
  main { padding: 6vh 6vw 8vh; }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: clamp(28px, 4.5vw, 40px); margin: 0 0 40px; }
  .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 32px; }
  .card-media { aspect-ratio: 4/3; background: var(--img-fallback); overflow: hidden; }
  .card-media img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.4s ease; }
  .card:hover .card-media img { transform: scale(1.04); }
  .card-title { font-family: 'Fraunces', serif; font-weight: 500; font-size: 19px; margin: 16px 0 4px; }
  .card-meta { font-size: 13px; color: var(--ink-soft); }
  .empty { color: var(--ink-soft); font-size: 15px; }
  .pagination-nav { margin-top: 48px; display: flex; align-items: center; justify-content: center; gap: 6px; flex-wrap: wrap; }
  .pagination-pages { display: flex; gap: 6px; }
  .pagination-link { padding: 8px 14px; border: 1px solid var(--line); border-radius: 999px; font-size: 13px; color: var(--ink-soft); }
  .pagination-link:hover { border-color: var(--ink); color: var(--ink); }
  .pagination-link.active { background: var(--active-bg, var(--ink)); border-color: var(--active-bg, var(--ink)); color: var(--active-text, #fff); }
  .pagination-link.disabled { opacity: 0.4; }
</style>
@include('components.theme-vars-dark')
</head>
<body>

<x-public-nav />

<main>
  <h1>Albums</h1>

  @if ($albums->isEmpty())
    <p class="empty">Aucun album publié pour l'instant.</p>
  @else
    <div class="grid">
      @foreach ($albums as $album)
        <a class="card" href="{{ route('public.album', $album) }}">
          <div class="card-media">
            @if ($cover = $album->cover?->variant('web'))
              <img src="{{ $cover->url() }}" alt="{{ $album->cover->alt_text }}">
            @endif
          </div>
          <p class="card-title">{{ $album->title }}</p>
          <p class="card-meta">
            {{ $album->media()->count() }} photographie{{ $album->media()->count() > 1 ? 's' : '' }}
            @if ($album->published_at) — {{ $album->published_at->translatedFormat('d F Y') }} @endif
          </p>
        </a>
      @endforeach
    </div>

    <x-pagination :paginator="$albums" />
  @endif
</main>

<x-public-footer />

</body>
</html>
