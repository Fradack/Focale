@php
  $exifFieldLabels = \App\Models\Media::EXIF_FIELDS;
  $visibleExif = $media->visibleExif();
@endphp
<!DOCTYPE html>
<html lang="fr"{!! \App\Support\Theme::publicHtmlAttr() !!}>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $media->title ?: 'Photographie' }} — Focale</title>
@if ($media->description)
<meta name="description" content="{{ \Illuminate\Support\Str::limit($media->description, 160) }}">
@endif
<link rel="canonical" href="{{ route('public.image', $media) }}">
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $media->title ?: 'Photographie' }}">
<meta property="og:url" content="{{ route('public.image', $media) }}">
@if ($web = $media->variant('web'))
<meta property="og:image" content="{{ $web->url() }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<script type="application/ld+json">
{!! json_encode(array_filter([
  '@context' => 'https://schema.org',
  '@type' => 'ImageObject',
  'name' => $media->title,
  'contentUrl' => $media->variant('web')?->url(),
  'creator' => $media->author,
  'license' => $media->license,
]), JSON_UNESCAPED_SLASHES) !!}
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
  :root { --bg: #E7E3DC; --panel: #EFEDE7; --ink: #1E1C19; --ink-soft: #5C574E; --clay: #7A4B33; --line: #C9C2B4; }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: var(--bg); color: var(--ink); font-family: 'Work Sans', sans-serif; }
  a { color: inherit; }
  header { display: flex; align-items: center; justify-content: space-between; padding: 24px 6vw; border-bottom: 1px solid var(--line); }
  .wordmark { font-family: 'Fraunces', serif; font-weight: 500; font-size: 20px; text-decoration: none; }
  main { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(280px, 0.9fr); }
  .figure { background: #0000; display: flex; align-items: center; justify-content: center; padding: 6vh 5vw; }
  .figure img { width: 100%; height: auto; max-height: 82vh; object-fit: contain; display: block; }
  figcaption { font-size: 12px; color: var(--ink-soft); margin-top: 12px; text-align: left; }
  .figure-wrap { width: 100%; }
  aside { background: var(--panel); border-left: 1px solid var(--line); padding: 7vh 40px 40px; }
  .project-link { font-size: 12px; color: var(--ink-soft); letter-spacing: 0.03em; margin: 0 0 18px; text-decoration: none; display: inline-block; }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: 34px; line-height: 1.15; margin: 0 0 18px; }
  .description { font-size: 15px; line-height: 1.7; color: var(--ink-soft); margin: 0 0 28px; max-width: 40ch; }
  .tags { display: flex; flex-wrap: wrap; gap: 8px; margin: 0 0 32px; }
  .tag { font-size: 12px; padding: 5px 12px; border: 1px solid var(--line); border-radius: 999px; color: var(--ink-soft); }
  .rule { height: 1px; background: var(--line); margin: 0 0 24px; }
  h2.section-title { font-family: 'Work Sans', sans-serif; font-weight: 500; font-size: 14px; margin: 0 0 16px; }
  dl.exif { margin: 0 0 32px; display: grid; grid-template-columns: 1fr 1fr; row-gap: 0; }
  dl.exif > div { display: contents; }
  dl.exif dt, dl.exif dd { margin: 0; padding: 9px 0; border-bottom: 1px solid var(--line); font-size: 13px; }
  dl.exif dt { color: var(--ink-soft); }
  dl.exif dd { text-align: right; }
  .credit { font-size: 13px; line-height: 1.7; color: var(--ink-soft); }
  .credit strong { color: var(--ink); font-weight: 500; }
  footer { display: flex; align-items: center; justify-content: space-between; padding: 22px 6vw; border-top: 1px solid var(--line); font-size: 14px; }
  footer a { text-decoration: none; color: var(--ink-soft); }
  footer a:hover { color: var(--ink); }
  .like-btn { display: inline-flex; align-items: center; gap: 8px; border: 1px solid var(--line); background: var(--bg); border-radius: 999px; padding: 8px 16px; margin: 0 0 22px; cursor: pointer; font-size: 14px; color: var(--ink); font-family: inherit; }
  .like-btn:hover { border-color: var(--clay); }
  .like-btn i { color: #DC2626; font-size: 15px; }
  .like-btn[aria-pressed="true"] { border-color: #DC2626; }
  @media (max-width: 860px) {
    main { grid-template-columns: 1fr; }
    aside { border-left: none; border-top: 1px solid var(--line); padding: 40px 6vw; }
    .figure { padding: 5vh 6vw 0; }
    .description { max-width: none; }
  }
</style>
@include('components.theme-vars-dark')
</head>
<body>

<header>
  <a class="wordmark" href="{{ route('home') }}">Focale</a>
  <a href="{{ route('login') }}" style="font-size:13px;color:var(--ink-soft);">Administration</a>
</header>

<main>
  <div class="figure">
    <div class="figure-wrap">
      @if ($media->isVideo())
        <video src="{{ $media->sourceUrl() }}" controls playsinline style="width:100%;max-height:82vh;display:block;"></video>
      @else
        <img src="{{ $media->sourceUrl() }}" alt="{{ $media->alt_text }}">
      @endif
      @if ($media->caption)
        <figcaption>{{ $media->caption }}</figcaption>
      @endif
    </div>
  </div>

  <aside>
    @if ($album)
      <a class="project-link" href="{{ route('public.album', $album) }}">← {{ $album->title }}</a>
    @endif
    <h1>{{ $media->title ?: 'Photographie' }}</h1>

    <button type="button" id="like-btn" class="like-btn" aria-pressed="{{ $liked ? 'true' : 'false' }}">
      <i class="{{ $liked ? 'fa-solid' : 'fa-regular' }} fa-heart" aria-hidden="true"></i>
      <span id="like-count">{{ $media->likes()->count() }}</span>
    </button>

    @if ($media->description)
      <p class="description">{{ $media->description }}</p>
    @endif

    @if ($media->tags->isNotEmpty())
      <div class="tags">
        @foreach ($media->tags as $tag)
          <span class="tag">{{ $tag->name }}</span>
        @endforeach
      </div>
    @endif

    @if (! empty($visibleExif) || ($media->taken_at) || $media->displayLocation())
      <div class="rule"></div>
      <h2 class="section-title">Détails techniques</h2>
      <dl class="exif">
        @foreach ($exifFieldLabels as $key => $label)
          @if (! empty($visibleExif[$key]))
            <div><dt>{{ $label }}</dt><dd>{{ $visibleExif[$key] }}</dd></div>
          @endif
        @endforeach
        @if ($media->taken_at)
          <div><dt>Date de prise</dt><dd>{{ $media->taken_at->format('d/m/Y à H:i') }}</dd></div>
        @endif
        @if ($media->displayLocation())
          <div><dt>Lieu</dt><dd>
            {{ $media->displayLocation() }}
            @if ($media->mapUrl())
              — <a href="{{ $media->mapUrl() }}" target="_blank" rel="noopener">Voir sur la carte ↗</a>
            @endif
          </dd></div>
        @endif
      </dl>
    @endif

    @if ($media->credit || $media->license)
      <div class="rule"></div>
      <p class="credit">
        @if ($media->credit)<strong>Crédit</strong> — {{ $media->credit }}<br>@endif
        @if ($media->license)Licence — {{ $media->license }}@endif
      </p>
    @endif
  </aside>
</main>

<footer>
  <span>@if ($previous)<a href="{{ route('public.image', $previous) }}">← {{ $previous->title }}</a>@endif</span>
  <span>@if ($next)<a href="{{ route('public.image', $next) }}">{{ $next->title }} →</a>@endif</span>
</footer>

<script>
  (function () {
    const btn = document.getElementById('like-btn');
    const icon = btn.querySelector('i');
    const countEl = document.getElementById('like-count');
    const likeUrl = @json(route('public.image.like', $media));
    const csrfToken = @json(csrf_token());
    let busy = false;

    btn.addEventListener('click', () => {
      if (busy) return;
      busy = true;

      fetch(likeUrl, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
      })
        .then((r) => r.json())
        .then((data) => {
          btn.setAttribute('aria-pressed', data.liked ? 'true' : 'false');
          icon.classList.toggle('fa-solid', data.liked);
          icon.classList.toggle('fa-regular', !data.liked);
          countEl.textContent = data.count;
        })
        .finally(() => { busy = false; });
    });
  })();

  // Une vue n'est comptabilisée qu'après 10s passées sur la page — pas au
  // simple chargement — pour refléter une vraie consultation de la photo.
  (function () {
    const viewUrl = @json(route('public.image.view', $media));
    const csrfToken = @json(csrf_token());

    setTimeout(() => {
      fetch(viewUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' } });
    }, 10000);
  })();
</script>

</body>
</html>
