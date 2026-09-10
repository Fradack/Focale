@php
  $exifFieldLabels = \App\Models\Media::EXIF_FIELDS;
@endphp
<!DOCTYPE html>
<html lang="fr"{!! \App\Support\Theme::publicHtmlAttr() !!}>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $album->seo_title ?: $album->title }} — Focale</title>
<meta name="description" content="{{ $album->seo_description ?: $album->intro_text }}">
<link rel="canonical" href="{{ route('public.album', $album) }}">
@if ($album->visibility !== 'public')
<meta name="robots" content="noindex">
@else
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $album->seo_title ?: $album->title }}">
<meta property="og:description" content="{{ $album->seo_description ?: $album->intro_text }}">
<meta property="og:url" content="{{ route('public.album', $album) }}">
@if ($cover = ($album->seoImage ?: $album->cover)?->variant('web'))
<meta property="og:image" content="{{ $cover->url() }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<script type="application/ld+json">
{!! json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'CreativeWork',
  'name' => $album->title,
  'description' => $album->intro_text,
  'url' => route('public.album', $album),
  'datePublished' => optional($album->published_at)->toAtomString(),
], JSON_UNESCAPED_SLASHES) !!}
</script>
@endif
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
  :root {
    --bg: #E7E3DC; --panel: #EFEDE7; --ink: #1E1C19; --ink-soft: #5C574E;
    --clay: #7A4B33; --line: #C9C2B4; --img-fallback: #D8D3C7;
  }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: var(--bg); color: var(--ink); font-family: 'Work Sans', sans-serif; }
  a { color: inherit; }
  header { display: flex; align-items: center; justify-content: space-between; padding: 24px 6vw; border-bottom: 1px solid var(--line); }
  .wordmark { font-family: 'Fraunces', serif; font-weight: 500; font-size: 20px; }
  nav { display: flex; gap: 28px; font-size: 14px; color: var(--ink-soft); }
  nav a { text-decoration: none; }
  nav a:hover { color: var(--ink); }
  .intro { max-width: 640px; margin: 7vh auto 5vh; padding: 0 6vw; text-align: center; }
  .intro .kicker { font-size: 13px; letter-spacing: 0.04em; color: var(--ink-soft); margin: 0 0 16px; }
  .intro h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: clamp(30px, 4.5vw, 42px); line-height: 1.15; margin: 0 0 16px; }
  .intro p { font-size: 16px; line-height: 1.7; color: var(--ink-soft); margin: 0 0 6px; }
  .intro .meta { font-size: 13px; color: var(--ink-soft); margin-top: 18px; }
  .slideshow-btn { margin-top: 22px; padding: 10px 22px; background: none; border: 1px solid var(--ink); border-radius: 999px; color: var(--ink); font-family: 'Work Sans', sans-serif; font-size: 14px; cursor: pointer; }
  .slideshow-btn:hover { background: var(--ink); color: var(--bg); }
  .slideshow-btn.active { background: var(--clay); border-color: var(--clay); color: #fff; }
  .like-btn { margin-top: 22px; margin-left: 10px; display: inline-flex; align-items: center; gap: 8px; border: 1px solid var(--line); background: var(--panel); border-radius: 999px; padding: 10px 20px; cursor: pointer; font-size: 14px; color: var(--ink); font-family: inherit; }
  .like-btn:hover { border-color: var(--clay); }
  .like-btn i { color: #DC2626; font-size: 15px; }
  .like-btn[aria-pressed="true"] { border-color: #DC2626; }
  .viewer { max-width: 1120px; margin: 0 auto; padding: 0 6vw; display: flex; align-items: center; gap: 48px; position: relative; }
  .viewer-media { flex: 1.6; min-width: 0; height: 66vh; display: flex; align-items: center; justify-content: center; background: var(--img-fallback); overflow: hidden; position: relative; }
  .viewer-media img { width: 100%; height: 100%; object-fit: contain; display: block; cursor: zoom-in; }
  .full-view { display: none; position: fixed; inset: 0; background: rgba(20, 18, 15, 0.95); z-index: 20; align-items: center; justify-content: center; padding: 4vh 4vw; }
  .full-view.open { display: flex; }
  .full-view img { width: 100%; height: 100%; object-fit: contain; }
  .full-view-close { position: absolute; top: 20px; right: 24px; background: none; border: none; color: rgba(255,255,255,0.8); font-family: 'Work Sans', sans-serif; font-size: 14px; cursor: pointer; padding: 10px; }
  .full-view-close:hover { color: #fff; }
  .full-view-caption { position: absolute; bottom: 32px; left: 0; right: 0; text-align: center; color: rgba(255,255,255,0.75); font-size: 13px; }
  .nav-arrow { position: absolute; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; border-radius: 50%; border: none; background: rgba(20,18,15,0.5); color: #fff; font-size: 20px; line-height: 1; cursor: pointer; z-index: 5; display: flex; align-items: center; justify-content: center; }
  .nav-arrow:hover { background: rgba(20,18,15,0.75); }
  .nav-arrow.prev { left: 12px; }
  .nav-arrow.next { right: 12px; }
  .viewer-media .nav-arrow { background: rgba(255,255,255,0.75); color: var(--ink); }
  .viewer-media .nav-arrow:hover { background: #fff; }
  .gallery-grid { display: none; max-width: 1200px; margin: 0 auto; padding: 0 6vw 8vh; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 10px; }
  .gallery-grid.open { display: grid; }
  .gallery-grid img { width: 100%; aspect-ratio: 1 / 1; object-fit: cover; border-radius: 8px; cursor: pointer; background: var(--img-fallback); }
  .gallery-grid img:hover { opacity: 0.85; }
  .viewer-info { width: 300px; flex-shrink: 0; padding: 8px 0 0 32px; border-left: 1px solid var(--line); }
  .viewer-info h2 { font-family: 'Fraunces', serif; font-weight: 500; font-size: 26px; margin: 0 0 12px; }
  .viewer-info .caption { font-size: 14px; line-height: 1.7; color: var(--ink-soft); margin: 0 0 22px; }
  .viewer-info .tags { display: flex; flex-wrap: wrap; gap: 8px; margin: 0 0 26px; }
  .viewer-info .tag { font-size: 12px; padding: 5px 12px; border: 1px solid var(--line); border-radius: 999px; color: var(--ink-soft); }
  .viewer-info .exif-title { font-family: 'Work Sans', sans-serif; font-weight: 500; font-size: 14px; margin: 0 0 14px; }
  dl.exif { margin: 0; }
  dl.exif > div { display: flex; justify-content: space-between; padding: 9px 0; border-bottom: 1px solid var(--line); font-size: 13px; }
  dl.exif dt { color: var(--ink-soft); }
  dl.exif dd { margin: 0; text-align: right; }
  .carousel-wrap { display: flex; align-items: center; justify-content: center; gap: 14px; padding: 5vh 6vw 8vh; }
  .carousel-arrow { flex-shrink: 0; width: 36px; height: 36px; border-radius: 50%; border: 1px solid var(--line); background: var(--panel); color: var(--ink); font-size: 16px; cursor: pointer; }
  .carousel-arrow:hover { background: #fff; }
  .carousel { display: flex; flex-direction: row-reverse; gap: 6px; overflow-x: auto; width: 800px; max-width: 76vw; padding: 10px; background: var(--panel); border: 1px solid var(--line); border-radius: 16px; scroll-behavior: smooth; scrollbar-width: none; }
  .carousel::-webkit-scrollbar { display: none; }
  .carousel img { height: 72px; width: auto; object-fit: cover; flex-shrink: 0; border-radius: 8px; opacity: 0.6; cursor: pointer; border: 1.5px solid transparent; }
  .carousel img:hover { opacity: 0.9; }
  .carousel img.active { opacity: 1; border-color: var(--clay); }
  .carousel-position { text-align: center; font-size: 12px; color: var(--ink-soft); margin: 10px 0 0; }
  footer { padding: 40px 6vw 32px; border-top: 1px solid var(--line); }
  .footer-top { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 24px; margin-bottom: 28px; }
  .footer-brand .wordmark { display: block; margin-bottom: 8px; }
  .footer-brand p { font-size: 13px; color: var(--ink-soft); margin: 0; }
  .footer-nav { display: flex; gap: 24px; font-size: 14px; }
  .footer-nav a { text-decoration: none; color: var(--ink-soft); }
  .footer-nav a:hover { color: var(--ink); }
  .footer-bottom { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 12px; padding-top: 20px; border-top: 1px solid var(--line); font-size: 12px; color: var(--ink-soft); }
  .comments { max-width: 960px; margin: 0 auto; padding: 0 6vw 8vh; }
  .comments h2 { font-family: 'Fraunces', serif; font-weight: 500; font-size: 22px; margin: 0 0 28px; }
  .comments-grid { display: flex; align-items: flex-start; gap: 56px; }
  .comments-form-col, .comments-list-col { flex: 1; min-width: 0; }
  @media (max-width: 720px) { .comments-grid { flex-direction: column; gap: 32px; } }
  .comment { padding: 16px 0; border-bottom: 1px solid var(--line); }
  .comment-meta { font-size: 13px; margin: 0 0 6px; }
  .comment-meta strong { font-weight: 500; }
  .comment-meta span { color: var(--ink-soft); font-weight: 400; }
  .comment-text { font-size: 14px; line-height: 1.6; color: var(--ink-soft); margin: 0; }
  .comment-form { margin-top: 0; display: flex; flex-direction: column; gap: 12px; }
  .comment-form input, .comment-form textarea { width: 100%; padding: 11px 14px; border: 1px solid var(--line); border-radius: 6px; background: var(--panel); color: var(--ink); font-family: 'Work Sans', sans-serif; font-size: 14px; }
  .comment-form textarea { resize: vertical; min-height: 90px; }
  .comment-form button { align-self: flex-start; padding: 11px 24px; background: var(--clay); border: none; border-radius: 999px; color: #fff; font-family: 'Work Sans', sans-serif; font-size: 14px; cursor: pointer; }
  .comment-form button:hover { opacity: 0.9; }
  .comment-notice { font-size: 13px; color: var(--ink-soft); margin: -4px 0 4px; }
  .comment-error { font-size: 13px; color: var(--danger, #A3402E); margin: 0; }
  @media (max-width: 860px) { .viewer { flex-direction: column; } .viewer-info { width: auto; padding: 24px 0 0; border-left: none; border-top: 1px solid var(--line); } .viewer-media { height: 48vh; } }
  @media (max-width: 480px) { .honeypot-field { display: none !important; } }
  .processing-notice { display: flex; align-items: center; gap: 16px; max-width: 640px; margin: 0 auto; padding: 12px 20px; background: var(--panel); border: 1px solid var(--line); border-radius: 10px; font-size: 13px; }
  .processing-notice-track { flex: 1; height: 6px; background: var(--line); border-radius: 999px; overflow: hidden; }
  .processing-notice-fill { height: 100%; background: var(--clay); transition: width 0.4s ease; }
  .processing-notice-count { flex-shrink: 0; font-weight: 500; white-space: nowrap; color: var(--ink-soft); }
</style>
@include('components.theme-vars-dark')
</head>
<body>

<x-public-nav />

<div class="intro">
  <p class="kicker">album</p>
  <h1>{{ $album->title }}</h1>
  @if ($album->intro_text)
    <p>{{ $album->intro_text }}</p>
  @endif
  <p class="meta">{{ $album->media->count() }} photographie{{ $album->media->count() > 1 ? 's' : '' }}
    @if ($album->published_at) — {{ $album->published_at->translatedFormat('d F Y') }} @endif
  </p>
  @if ($album->media->count() > 1)
    <button class="slideshow-btn" id="slideshow-btn">Lancer le diaporama</button>
  @endif
  @if (\App\Support\Plugins::enabled('likes'))
    <button type="button" id="album-like-btn" class="like-btn" aria-pressed="{{ $liked ? 'true' : 'false' }}">
      <i class="{{ $liked ? 'fa-solid' : 'fa-regular' }} fa-heart" aria-hidden="true"></i>
      <span id="album-like-count">{{ $album->likes()->count() }}</span>
    </button>
  @endif
  @if ($album->media->count() > 0)
    <button type="button" class="slideshow-btn" id="gallery-view-btn" style="margin-left:10px;">Vue galerie</button>
  @endif
  @if ($album->media->count() > 1)
    <select id="sort-select" class="slideshow-btn" style="margin-left:10px;padding:10px 16px;">
      <option value="default">Ordre de l'album</option>
      <option value="chrono">Du plus ancien au plus récent</option>
      <option value="antichrono">Du plus récent au plus ancien</option>
      <option value="random">Aléatoire</option>
    </select>
  @endif
</div>

@if ($processingStatus['pending'] > 0)
  <div class="processing-notice" id="processing-notice" style="margin-bottom:5vh;">
    <span class="processing-notice-label" id="processing-notice-label">Certaines photos de cet album sont encore en cours de traitement…</span>
    <div class="processing-notice-track"><div class="processing-notice-fill" id="processing-notice-fill" style="width:{{ $processingStatus['percent'] }}%;"></div></div>
    <span class="processing-notice-count" id="processing-notice-count">{{ $processingStatus['processed'] }} / {{ $processingStatus['total'] }}</span>
  </div>
@endif

@if ($album->media->isEmpty())
  <p style="text-align:center;color:var(--ink-soft);padding:0 6vw 8vh;">Cet album ne contient pas encore d'œuvres.</p>
@else
  <div class="viewer" id="single-view">
    <div class="viewer-media">
      @if ($album->media->count() > 1)
        <button type="button" class="nav-arrow prev" id="viewer-prev" aria-label="Photo précédente">‹</button>
        <button type="button" class="nav-arrow next" id="viewer-next" aria-label="Photo suivante">›</button>
      @endif
      <img id="v-img" src="" alt="">
    </div>
    <div class="viewer-info">
      <h2 id="v-title"></h2>
      <p class="caption" id="v-caption"></p>
      <div class="tags" id="v-tags"></div>
      <p class="exif-title">Détails techniques</p>
      <dl class="exif" id="v-exif"></dl>
    </div>
  </div>

  <div class="gallery-grid" id="gallery-grid"></div>

  <div class="full-view" id="full-view">
    <button class="full-view-close" id="full-view-close" aria-label="Fermer">Fermer ✕</button>
    @if ($album->media->count() > 1)
      <button type="button" class="nav-arrow prev" id="full-view-prev" aria-label="Photo précédente">‹</button>
      <button type="button" class="nav-arrow next" id="full-view-next" aria-label="Photo suivante">›</button>
    @endif
    <img id="full-view-img" src="" alt="">
    <p class="full-view-caption" id="full-view-caption"></p>
  </div>

  <div class="carousel-wrap" id="carousel-wrap">
    <button class="carousel-arrow" id="carousel-prev" aria-label="Photos précédentes">‹</button>
    <div class="carousel" id="carousel"></div>
    <button class="carousel-arrow" id="carousel-next" aria-label="Photos suivantes">›</button>
  </div>
  <p class="carousel-position" id="carousel-position"></p>

  <div id="photo-data" hidden>
    @foreach ($album->media as $item)
      <img
        data-id="{{ $item->id }}"
        data-href="{{ route('public.image', $item) }}"
        data-title="{{ $item->title }}"
        data-caption="{{ $item->caption }}"
        data-tags="{{ $item->tags->pluck('name')->join(', ') }}"
        @foreach ($exifFieldLabels as $key => $label)
          data-{{ str_replace('_', '-', $key) }}="{{ $item->visibleExif()[$key] ?? '' }}"
        @endforeach
        data-date="{{ $item->taken_at?->format('d/m/Y à H:i') }}"
        data-date-sort="{{ $item->taken_at?->timestamp ?? 0 }}"
        data-location="{{ $item->displayLocation() }}"
        data-location-map="{{ $item->mapUrl() }}"
        data-video="{{ $item->isVideo() ? '1' : '0' }}"
        data-thumb="{{ $item->isVideo() ? '' : ($item->variant('thumbnail')?->url() ?? $item->sourceUrl()) }}"
        loading="lazy"
        src="{{ $item->isVideo() ? 'data:image/svg+xml;utf8,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#5C574E" stroke-width="1.2"><rect x="2" y="5" width="15" height="14" rx="2"/><path d="M17 10l5-3v10l-5-3z"/></svg>') : $item->sourceUrl() }}"
        alt="{{ $item->alt_text }}">
    @endforeach
  </div>
@endif

<section class="comments">
  <h2>Commentaires ({{ $album->approvedComments->count() }})</h2>

  @if ($album->comments_enabled)
    <div class="comments-grid">
      <div class="comments-form-col">
        <form class="comment-form" method="POST" action="{{ route('public.album.comment', $album) }}">
          @csrf
          @if (session('status') === 'comment-submitted')
            <p class="comment-notice">Merci ! Votre commentaire sera visible après validation.</p>
          @endif
          @error('author_name') <p class="comment-error">{{ $message }}</p> @enderror
          @error('body') <p class="comment-error">{{ $message }}</p> @enderror
          @if (auth()->check() && auth()->user()->is_customer)
            <p style="font-size:13px;color:var(--ink-soft);margin:0;">Vous commentez en tant que <strong style="color:var(--ink);">{{ auth()->user()->name }}</strong>.</p>
          @else
            <input type="text" name="author_name" placeholder="Votre nom" autocomplete="name" value="{{ old('author_name') }}">
          @endif
          <textarea name="body" placeholder="Votre commentaire">{{ old('body') }}</textarea>
          <input type="hidden" name="form_started_at" value="{{ time() }}">
          <input type="text" name="website" class="honeypot-field" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;">
          <button type="submit">Publier</button>
        </form>
      </div>

      <div class="comments-list-col">
        @forelse ($album->approvedComments as $comment)
          <div class="comment">
            <p class="comment-meta"><strong>{{ $comment->author_name }}</strong>{{ $comment->user_id ? ' ✓' : '' }} <span>— {{ $comment->created_at->translatedFormat('d F Y') }}</span></p>
            <p class="comment-text">{{ $comment->body }}</p>
          </div>
        @empty
          <p style="color:var(--ink-soft);font-size:14px;">Soyez le premier à commenter cet album.</p>
        @endforelse
      </div>
    </div>
  @endif
</section>

<footer>
  <div class="footer-top">
    <div class="footer-brand">
      <span class="wordmark">Focale</span>
    </div>
    <nav class="footer-nav">
      <a href="{{ route('home') }}">Accueil</a>
      <a href="{{ route('login') }}">Administration</a>
    </nav>
  </div>
  <div class="footer-bottom">
    <span>@if ($previous)<a href="{{ route('public.album', $previous) }}">← {{ $previous->title }}</a>@endif</span>
    <span>@if ($next)<a href="{{ route('public.album', $next) }}">{{ $next->title }} →</a>@endif</span>
  </div>
</footer>

@if ($album->media->isNotEmpty())
<script>
  let images = Array.from(document.querySelectorAll('#photo-data img'));
  const originalOrder = images.slice();
  const vImg = document.getElementById('v-img');
  const vTitle = document.getElementById('v-title');
  const vCaption = document.getElementById('v-caption');
  const vTags = document.getElementById('v-tags');
  const vExif = document.getElementById('v-exif');
  const carousel = document.getElementById('carousel');
  const carouselPosition = document.getElementById('carousel-position');
  const fullView = document.getElementById('full-view');
  const fullViewImg = document.getElementById('full-view-img');
  const fullViewCaption = document.getElementById('full-view-caption');
  const fullViewClose = document.getElementById('full-view-close');
  const slideshowBtn = document.getElementById('slideshow-btn');
  const galleryViewBtn = document.getElementById('gallery-view-btn');

  const exifFields = [
    ['camera', 'Appareil'], ['lens', 'Objectif'], ['focal-length', 'Focale'],
    ['aperture', 'Ouverture'], ['shutter-speed', 'Vitesse'], ['iso', 'ISO'],
    ['date', 'Date de prise'], ['location', 'Lieu']
  ];

  let currentIndex = 0;

  let thumbs = [];

  function buildCarousel() {
    carousel.innerHTML = '';
    images.forEach((img, i) => {
      const thumb = document.createElement('img');
      // Vignette dédiée (petite, rapide) plutôt que la variante "web"
      // utilisée par la visionneuse principale — évite de charger deux fois
      // une image en pleine taille par photo, ce qui pouvait saturer les
      // connexions du navigateur sur un gros album et faire échouer
      // certaines vignettes.
      thumb.src = img.dataset.thumb || img.src;
      thumb.loading = 'lazy';
      thumb.alt = '';
      thumb.onerror = function () { if (this.src !== img.src) this.src = img.src; };
      thumb.addEventListener('click', () => { stopSlideshow(); select(i); });
      carousel.appendChild(thumb);
    });
    thumbs = Array.from(carousel.children);
  }

  buildCarousel();

  function select(index, scrollThumbIntoView = true) {
    currentIndex = index;
    const img = images[index];

    vImg.src = img.src;
    vImg.alt = img.alt;
    // Vidéo : pas d'aperçu ni de lecture inline dans ce viewer (juste la
    // tuile de remplacement posée dans #photo-data) — un clic mène vers la
    // fiche de l'œuvre, où <video controls> permet la vraie lecture.
    vImg.style.cursor = img.dataset.video === '1' ? 'pointer' : '';
    vTitle.textContent = (img.dataset.video === '1' ? '🎬 ' : '') + (img.dataset.title || '');
    vCaption.textContent = img.dataset.caption || '';

    vTags.innerHTML = '';
    (img.dataset.tags || '').split(',').map(t => t.trim()).filter(Boolean).forEach(tag => {
      const span = document.createElement('span');
      span.className = 'tag';
      span.textContent = tag;
      vTags.appendChild(span);
    });

    vExif.innerHTML = '';
    exifFields.forEach(([key, label]) => {
      if (!img.dataset[key]) return;
      const row = document.createElement('div');
      const dt = document.createElement('dt');
      dt.textContent = label;
      const dd = document.createElement('dd');
      if (key === 'location' && img.dataset.locationMap) {
        const a = document.createElement('a');
        a.href = img.dataset.locationMap;
        a.target = '_blank';
        a.rel = 'noopener';
        a.title = 'Voir sur la carte (OpenStreetMap)';
        a.style.textDecoration = 'underline';
        a.textContent = img.dataset[key];
        dd.appendChild(a);
      } else {
        dd.textContent = img.dataset[key];
      }
      row.appendChild(dt);
      row.appendChild(dd);
      vExif.appendChild(row);
    });

    thumbs.forEach((t, i) => t.classList.toggle('active', i === index));
    const activeThumb = thumbs[index];
    // scrollIntoView (même avec block:"nearest") peut quand même faire
    // défiler la page verticalement pour amener le strip de vignettes à
    // l'écran — indésirable en plein milieu d'un swipe ou d'un clic sur les
    // flèches, où l'utilisateur doit rester devant la photo. Désactivable
    // via le second paramètre, actif seulement pour un clic direct sur une
    // vignette du strip.
    if (scrollThumbIntoView && activeThumb) activeThumb.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });

    carouselPosition.textContent = `${index + 1} / ${images.length}`;

    if (fullView.classList.contains('open')) {
      fullViewImg.src = img.src;
      fullViewImg.alt = img.alt;
      fullViewCaption.textContent = `${img.dataset.title || ''} — ${index + 1} / ${images.length}`;
    }
  }

  select(0);

  vImg.addEventListener('click', (e) => {
    const current = images[currentIndex];
    if (current.dataset.video === '1') {
      window.location.href = current.dataset.href;
      return;
    }
    if (slideshowBtn) {
      e.preventDefault();
      fullView.classList.add('open');
      select(currentIndex);
    }
  });

  function closeFullView() {
    if (slideshowBtn && slideshowBtn.classList.contains('active')) stopSlideshow();
    else fullView.classList.remove('open');
  }

  fullViewClose.addEventListener('click', closeFullView);
  fullView.addEventListener('click', (e) => { if (e.target === fullView) closeFullView(); });

  // "Vue galerie" : affiche toutes les photos de l'album en tuiles, à la
  // place du viewer une-photo-à-la-fois — cliquer sur une tuile ouvre la
  // visionneuse plein écran sur cette photo précise.
  const singleView = document.getElementById('single-view');
  const carouselWrap = document.getElementById('carousel-wrap');
  const galleryGrid = document.getElementById('gallery-grid');

  function buildGalleryGrid() {
    if (!galleryGrid) return;
    galleryGrid.innerHTML = '';
    images.forEach((img, i) => {
      const tile = document.createElement('img');
      tile.src = img.dataset.thumb || img.src;
      tile.loading = 'lazy';
      tile.alt = img.dataset.title || '';
      tile.onerror = function () { if (this.src !== img.src) this.src = img.src; };
      tile.addEventListener('click', () => {
        fullView.classList.add('open');
        select(i, false);
      });
      galleryGrid.appendChild(tile);
    });
  }

  buildGalleryGrid();

  // Tri choisi par le visiteur — indépendant de l'ordre défini par
  // l'administrateur (sort_order), purement côté client puisque toutes les
  // photos sont déjà chargées dans la page.
  const sortSelect = document.getElementById('sort-select');
  if (sortSelect) {
    sortSelect.addEventListener('change', () => {
      const mode = sortSelect.value;

      if (mode === 'chrono') {
        images = originalOrder.slice().sort((a, b) => Number(a.dataset.dateSort) - Number(b.dataset.dateSort));
      } else if (mode === 'antichrono') {
        images = originalOrder.slice().sort((a, b) => Number(b.dataset.dateSort) - Number(a.dataset.dateSort));
      } else if (mode === 'random') {
        images = originalOrder.slice();
        for (let i = images.length - 1; i > 0; i--) {
          const j = Math.floor(Math.random() * (i + 1));
          [images[i], images[j]] = [images[j], images[i]];
        }
      } else {
        images = originalOrder.slice();
      }

      buildCarousel();
      buildGalleryGrid();
      select(0, false);
    });
  }

  if (galleryViewBtn) {
    galleryViewBtn.addEventListener('click', () => {
      const isGalleryOpen = galleryGrid.classList.toggle('open');
      singleView.style.display = isGalleryOpen ? 'none' : '';
      carouselWrap.style.display = isGalleryOpen ? 'none' : '';
      carouselPosition.style.display = isGalleryOpen ? 'none' : '';
      galleryViewBtn.textContent = isGalleryOpen ? 'Vue simple' : 'Vue galerie';
    });
  }

  // Glisser à gauche/droite pour changer d'image sur mobile, sans avoir à
  // descendre jusqu'au strip de vignettes — sur le viewer principal de la
  // page ET dans la visionneuse plein écran (vue galerie/diaporama). Ne
  // fait jamais défiler la page : l'utilisateur doit rester devant la
  // photo (voir select(index, false) plus haut).
  function enableSwipeNav(zone) {
    const MIN_SWIPE_PX = 60;
    let startX = null;
    let startY = null;

    zone.addEventListener('touchstart', (e) => {
      startX = e.touches[0].clientX;
      startY = e.touches[0].clientY;
    }, { passive: true });

    zone.addEventListener('touchend', (e) => {
      if (startX === null) return;
      const dx = e.changedTouches[0].clientX - startX;
      const dy = e.changedTouches[0].clientY - startY;
      startX = null;

      if (Math.abs(dx) < MIN_SWIPE_PX || Math.abs(dx) < Math.abs(dy)) return;

      if (dx < 0) select((currentIndex + 1) % images.length, false);
      else select((currentIndex - 1 + images.length) % images.length, false);
    }, { passive: true });
  }

  enableSwipeNav(fullView);
  const viewerMedia = document.querySelector('.viewer-media');
  if (viewerMedia) enableSwipeNav(viewerMedia);

  // Flèches cliquables — disponibles sur tous les appareils (souris comme
  // tactile), pas seulement le clavier ou le swipe.
  function goTo(delta) {
    select((currentIndex + delta + images.length) % images.length, false);
  }
  document.getElementById('viewer-prev')?.addEventListener('click', () => goTo(-1));
  document.getElementById('viewer-next')?.addEventListener('click', () => goTo(1));
  document.getElementById('full-view-prev')?.addEventListener('click', () => goTo(-1));
  document.getElementById('full-view-next')?.addEventListener('click', () => goTo(1));

  document.addEventListener('keydown', (e) => {
    const tag = document.activeElement && document.activeElement.tagName;
    if (tag === 'INPUT' || tag === 'TEXTAREA') return;
    if (e.key === 'Escape' && fullView.classList.contains('open')) { closeFullView(); return; }
    if (e.key === 'ArrowLeft') { stopSlideshow(); select((currentIndex - 1 + images.length) % images.length); }
    if (e.key === 'ArrowRight') { stopSlideshow(); select((currentIndex + 1) % images.length); }
  });

  const carouselPrev = document.getElementById('carousel-prev');
  const carouselNext = document.getElementById('carousel-next');
  const scrollStep = 5 * (72 + 6);
  carouselPrev.addEventListener('click', () => carousel.scrollBy({ left: -scrollStep, behavior: 'smooth' }));
  carouselNext.addEventListener('click', () => carousel.scrollBy({ left: scrollStep, behavior: 'smooth' }));

  if (slideshowBtn) {
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const slideshowInterval = prefersReducedMotion ? 6000 : 4000;
    let slideshowTimer = null;

    function tick() {
      slideshowTimer = setInterval(() => select((currentIndex + 1) % images.length), slideshowInterval);
    }
    function stopSlideshow() {
      clearInterval(slideshowTimer);
      slideshowTimer = null;
      slideshowBtn.textContent = 'Lancer le diaporama';
      slideshowBtn.classList.remove('active');
      fullView.classList.remove('open');
    }
    function startSlideshow() {
      slideshowBtn.textContent = 'Arrêter le diaporama';
      slideshowBtn.classList.add('active');
      fullView.classList.add('open');
      select(currentIndex);
      tick();
    }
    slideshowBtn.addEventListener('click', () => {
      slideshowBtn.classList.contains('active') ? stopSlideshow() : startSlideshow();
    });
    fullView.addEventListener('mouseenter', () => { if (slideshowTimer) { clearInterval(slideshowTimer); slideshowTimer = null; } });
    fullView.addEventListener('mouseleave', () => { if (slideshowBtn.classList.contains('active') && !slideshowTimer) tick(); });
  } else {
    function stopSlideshow() { fullView.classList.remove('open'); }
  }
</script>

<script>
  @if (\App\Support\Plugins::enabled('likes'))
  (function () {
    const btn = document.getElementById('album-like-btn');
    const icon = btn.querySelector('i');
    const countEl = document.getElementById('album-like-count');
    const likeUrl = @json(route('public.album.like', $album));
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
  @endif
</script>

@if ($processingStatus['pending'] > 0)
<script>
  (function () {
    const notice = document.getElementById('processing-notice');
    const label = document.getElementById('processing-notice-label');
    const fill = document.getElementById('processing-notice-fill');
    const count = document.getElementById('processing-notice-count');
    const statusUrl = @json(route('public.album.processing-status', $album));

    function formatEta(minutes) {
      if (minutes === null) return 'estimation en cours…';
      if (minutes < 1) return 'moins d\'une minute';
      if (minutes < 60) return `~${minutes} min`;
      const hours = Math.floor(minutes / 60);
      const rest = minutes % 60;
      return `~${hours} h${rest ? ' ' + rest + ' min' : ''}`;
    }

    function poll() {
      fetch(statusUrl, { headers: { Accept: 'application/json' } })
        .then(r => (r.ok ? r.json() : null))
        .then(data => {
          // Réponse absente ou de forme inattendue : on réessaie plus tard
          // plutôt que de recharger la page à tort.
          if (! data || typeof data.total !== 'number' || typeof data.pending !== 'number') {
            setTimeout(poll, 10000);
            return;
          }

          if (data.pending > 0) {
            fill.style.width = data.percent + '%';
            count.textContent = `${data.processed} / ${data.total}`;
            label.textContent = `Certaines photos de cet album sont encore en cours de traitement (${formatEta(data.eta_minutes)})…`;
            setTimeout(poll, 5000);
          } else {
            location.reload();
          }
        })
        .catch(() => { setTimeout(poll, 10000); });
    }

    setTimeout(poll, 5000);
  })();
</script>
@endif
@endif

</body>
</html>
