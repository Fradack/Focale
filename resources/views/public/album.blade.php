@php
  $exifFieldLabels = \App\Models\Media::EXIF_FIELDS;
@endphp
<!DOCTYPE html>
<html lang="fr">
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
  .viewer { max-width: 1120px; margin: 0 auto; padding: 0 6vw; display: flex; align-items: center; gap: 48px; }
  .viewer-media { flex: 1.6; min-width: 0; height: 66vh; display: flex; align-items: center; justify-content: center; background: var(--img-fallback); overflow: hidden; }
  .viewer-media img { width: 100%; height: 100%; object-fit: contain; display: block; cursor: zoom-in; }
  .full-view { display: none; position: fixed; inset: 0; background: rgba(20, 18, 15, 0.95); z-index: 20; align-items: center; justify-content: center; padding: 4vh 4vw; }
  .full-view.open { display: flex; }
  .full-view img { width: 100%; height: 100%; object-fit: contain; }
  .full-view-close { position: absolute; top: 20px; right: 24px; background: none; border: none; color: rgba(255,255,255,0.8); font-family: 'Work Sans', sans-serif; font-size: 14px; cursor: pointer; padding: 10px; }
  .full-view-close:hover { color: #fff; }
  .full-view-caption { position: absolute; bottom: 32px; left: 0; right: 0; text-align: center; color: rgba(255,255,255,0.75); font-size: 13px; }
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
  .comment-error { font-size: 13px; color: #A3402E; margin: 0; }
  @media (max-width: 860px) { .viewer { flex-direction: column; } .viewer-info { width: auto; padding: 24px 0 0; border-left: none; border-top: 1px solid var(--line); } .viewer-media { height: 48vh; } }
  @media (max-width: 480px) { .honeypot-field { display: none !important; } }
  .processing-notice { display: flex; align-items: center; gap: 16px; max-width: 640px; margin: 0 auto; padding: 12px 20px; background: var(--panel); border: 1px solid var(--line); border-radius: 10px; font-size: 13px; }
  .processing-notice-track { flex: 1; height: 6px; background: var(--line); border-radius: 999px; overflow: hidden; }
  .processing-notice-fill { height: 100%; background: var(--clay); transition: width 0.4s ease; }
  .processing-notice-count { flex-shrink: 0; font-weight: 500; white-space: nowrap; color: var(--ink-soft); }
</style>
</head>
<body>

<header>
  <a class="wordmark" href="{{ route('home') }}">Focale</a>
  <nav>
    <a href="{{ route('public.albums') }}">Albums</a>
    <a href="{{ route('public.gallery') }}">Galerie</a>
  </nav>
</header>

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
  <div class="viewer">
    <div class="viewer-media">
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

  <div class="full-view" id="full-view">
    <button class="full-view-close" id="full-view-close" aria-label="Fermer">Fermer ✕</button>
    <img id="full-view-img" src="" alt="">
    <p class="full-view-caption" id="full-view-caption"></p>
  </div>

  <div class="carousel-wrap">
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
        data-date="{{ $item->taken_at?->format('d/m/Y') }}"
        data-location="{{ $item->hide_gps ? '' : $item->location }}"
        src="{{ $item->variant('web')?->url() }}"
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
          <input type="text" name="author_name" placeholder="Votre nom" autocomplete="name" value="{{ old('author_name') }}">
          <textarea name="body" placeholder="Votre commentaire">{{ old('body') }}</textarea>
          <input type="hidden" name="form_started_at" value="{{ time() }}">
          <input type="text" name="website" class="honeypot-field" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;">
          <button type="submit">Publier</button>
        </form>
      </div>

      <div class="comments-list-col">
        @forelse ($album->approvedComments as $comment)
          <div class="comment">
            <p class="comment-meta"><strong>{{ $comment->author_name }}</strong> <span>— {{ $comment->created_at->translatedFormat('d F Y') }}</span></p>
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
  const images = Array.from(document.querySelectorAll('#photo-data img'));
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

  const exifFields = [
    ['camera', 'Appareil'], ['lens', 'Objectif'], ['focal-length', 'Focale'],
    ['aperture', 'Ouverture'], ['shutter-speed', 'Vitesse'], ['iso', 'ISO'],
    ['date', 'Date de prise'], ['location', 'Lieu']
  ];

  let currentIndex = 0;

  images.forEach((img, i) => {
    const thumb = document.createElement('img');
    thumb.src = img.src;
    thumb.alt = '';
    thumb.addEventListener('click', () => { stopSlideshow(); select(i); });
    carousel.appendChild(thumb);
  });
  const thumbs = Array.from(carousel.children);

  function select(index) {
    currentIndex = index;
    const img = images[index];

    vImg.src = img.src;
    vImg.alt = img.alt;
    vTitle.textContent = img.dataset.title || '';
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
      row.innerHTML = `<dt>${label}</dt><dd>${img.dataset[key]}</dd>`;
      vExif.appendChild(row);
    });

    thumbs.forEach((t, i) => t.classList.toggle('active', i === index));
    const activeThumb = thumbs[index];
    if (activeThumb) activeThumb.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });

    carouselPosition.textContent = `${index + 1} / ${images.length}`;

    if (fullView.classList.contains('open')) {
      fullViewImg.src = img.src;
      fullViewImg.alt = img.alt;
      fullViewCaption.textContent = `${img.dataset.title || ''} — ${index + 1} / ${images.length}`;
    }
  }

  select(0);

  vImg.addEventListener('click', (e) => {
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
