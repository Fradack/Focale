<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ \App\Models\Setting::get('site_name', 'Focale') }}{{ $artistName ? ' — '.$artistName : '' }}</title>
@if ($bio)
<meta name="description" content="{{ \Illuminate\Support\Str::limit($bio, 160) }}">
@endif
<link rel="canonical" href="{{ route('home') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg: #E7E3DC; --ink: #1E1C19; --ink-soft: #5C574E; --clay: #7A4B33; --line: #C9C2B4; --img-fallback: #D8D3C7; }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: var(--bg); color: var(--ink); font-family: 'Work Sans', sans-serif; }
  a { color: inherit; text-decoration: none; }
  header { display: flex; align-items: center; justify-content: space-between; padding: 24px 6vw; }
  .wordmark { font-family: 'Fraunces', serif; font-weight: 500; font-size: 20px; }
  nav { display: flex; gap: 28px; font-size: 14px; color: var(--ink-soft); }
  nav a:hover { color: var(--ink); }

  .cover { position: relative; height: 78vh; min-height: 420px; background: var(--img-fallback); overflow: hidden; }
  .cover img { width: 100%; height: 100%; object-fit: cover; display: block; }
  .cover-empty { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
  .cover-empty h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: clamp(40px, 8vw, 80px); color: var(--ink-soft); opacity: 0.4; }

  .intro { max-width: 640px; margin: 0 auto; padding: 8vh 6vw 6vh; text-align: center; }
  .intro h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: clamp(26px, 4vw, 36px); margin: 0 0 18px; }
  .intro p { font-size: 16px; line-height: 1.8; color: var(--ink-soft); margin: 0 0 24px; }
  .intro .links { display: flex; justify-content: center; gap: 24px; font-size: 14px; }
  .intro .links a { text-decoration: underline; color: var(--ink-soft); }
  .intro .links a:hover { color: var(--ink); }

  .featured { padding: 4vh 6vw 10vh; }
  .featured h2 { font-family: 'Work Sans', sans-serif; font-weight: 500; font-size: 13px; letter-spacing: 0.05em; text-transform: uppercase; color: var(--ink-soft); text-align: center; margin: 0 0 32px; }
  .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 32px; }
  .card-media { aspect-ratio: 4/3; background: var(--img-fallback); overflow: hidden; }
  .card-media img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.4s ease; }
  .card:hover .card-media img { transform: scale(1.04); }
  .card-title { font-family: 'Fraunces', serif; font-weight: 500; font-size: 19px; margin: 16px 0 0; }

</style>
</head>
<body>

<x-public-nav />

<div class="cover">
  @if ($cover && $web = $cover->variant('web'))
    <img src="{{ $web->url() }}" alt="{{ $cover->alt_text }}">
  @else
    <div class="cover-empty"><h1>{{ \App\Models\Setting::get('site_name', 'Focale') }}</h1></div>
  @endif
</div>

<div class="intro">
  @if ($artistName)
    <h1>{{ $artistName }}</h1>
  @endif
  @if ($bio)
    <p>{{ $bio }}</p>
  @endif
  <div class="links">
    <a href="{{ route('public.albums') }}">Voir les albums</a>
    @if ($aboutPage)
      <a href="{{ route('page.show', $aboutPage) }}">À propos</a>
    @else
      <a href="{{ route('public.contact') }}">Contact</a>
    @endif
  </div>
</div>

@if ($featured->isNotEmpty())
  <section class="featured">
    <h2>Projets à la une</h2>
    <div class="grid">
      @foreach ($featured as $album)
        <a class="card" href="{{ route('public.album', $album) }}">
          <div class="card-media">
            @if ($cover = $album->cover?->variant('web'))
              <img src="{{ $cover->url() }}" alt="{{ $album->cover->alt_text }}">
            @endif
          </div>
          <p class="card-title">{{ $album->title }}</p>
        </a>
      @endforeach
    </div>
  </section>
@endif

<x-public-footer />

</body>
</html>
