<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Le mariage de Claire &amp; Antoine — Focale</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #E7E3DC;
    --panel: #EFEDE7;
    --ink: #1E1C19;
    --ink-soft: #5C574E;
    --clay: #7A4B33;
    --line: #C9C2B4;
    --img-fallback: #D8D3C7;
  }

  * { box-sizing: border-box; }

  html, body {
    margin: 0;
    padding: 0;
    background: var(--bg);
    color: var(--ink);
    font-family: 'Work Sans', sans-serif;
  }

  a { color: inherit; }

  header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24px 6vw;
    border-bottom: 1px solid var(--line);
  }

  .wordmark {
    font-family: 'Fraunces', serif;
    font-weight: 500;
    font-size: 20px;
  }

  nav {
    display: flex;
    gap: 28px;
    font-size: 14px;
    color: var(--ink-soft);
  }

  nav a { text-decoration: none; }
  nav a:hover { color: var(--ink); }

  .intro {
    max-width: 640px;
    margin: 7vh auto 5vh;
    padding: 0 6vw;
    text-align: center;
  }

  .intro .kicker {
    font-size: 13px;
    letter-spacing: 0.04em;
    color: var(--ink-soft);
    margin: 0 0 16px;
  }

  .intro h1 {
    font-family: 'Fraunces', serif;
    font-weight: 500;
    font-size: clamp(30px, 4.5vw, 42px);
    line-height: 1.15;
    margin: 0 0 16px;
  }

  .intro p {
    font-size: 16px;
    line-height: 1.7;
    color: var(--ink-soft);
    margin: 0 0 6px;
  }

  .intro .meta {
    font-size: 13px;
    color: var(--ink-soft);
    margin-top: 18px;
  }

  .slideshow-btn {
    margin-top: 22px;
    padding: 10px 22px;
    background: none;
    border: 1px solid var(--ink);
    border-radius: 999px;
    color: var(--ink);
    font-family: 'Work Sans', sans-serif;
    font-size: 14px;
    cursor: pointer;
  }

  .slideshow-btn:hover {
    background: var(--ink);
    color: var(--bg);
  }

  .slideshow-btn.active {
    background: var(--clay);
    border-color: var(--clay);
    color: #fff;
  }

  /* Viewer: large photo + EXIF panel, always visible */
  .viewer {
    max-width: 1120px;
    margin: 0 auto;
    padding: 0 6vw;
    display: flex;
    align-items: center;
    gap: 48px;
  }

  .viewer-media {
    flex: 1.6;
    min-width: 0;
    height: 66vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--img-fallback);
    overflow: hidden;
  }

  .viewer-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    cursor: zoom-in;
  }

  .full-view {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(20, 18, 15, 0.95);
    z-index: 20;
    align-items: center;
    justify-content: center;
    padding: 4vh 4vw;
  }

  .full-view.open { display: flex; }

  .full-view img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .full-view-close {
    position: absolute;
    top: 20px;
    right: 24px;
    background: none;
    border: none;
    color: rgba(255,255,255,0.8);
    font-family: 'Work Sans', sans-serif;
    font-size: 14px;
    cursor: pointer;
    padding: 10px;
  }

  .full-view-close:hover { color: #fff; }

  .full-view-caption {
    position: absolute;
    bottom: 32px;
    left: 0;
    right: 0;
    text-align: center;
    color: rgba(255,255,255,0.75);
    font-size: 13px;
  }

  .viewer-info {
    width: 300px;
    flex-shrink: 0;
    padding: 8px 0 0 32px;
    border-left: 1px solid var(--line);
  }

  .viewer-info h2 {
    font-family: 'Fraunces', serif;
    font-weight: 500;
    font-size: 26px;
    margin: 0 0 12px;
  }

  .viewer-info .caption {
    font-size: 14px;
    line-height: 1.7;
    color: var(--ink-soft);
    margin: 0 0 22px;
  }

  .viewer-info .tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 0 0 26px;
  }

  .viewer-info .tag {
    font-size: 12px;
    padding: 5px 12px;
    border: 1px solid var(--line);
    border-radius: 999px;
    color: var(--ink-soft);
  }

  .viewer-info .exif-title {
    font-family: 'Work Sans', sans-serif;
    font-weight: 500;
    font-size: 14px;
    margin: 0 0 14px;
  }

  dl.exif { margin: 0; }

  dl.exif > div {
    display: flex;
    justify-content: space-between;
    padding: 9px 0;
    border-bottom: 1px solid var(--line);
    font-size: 13px;
  }

  dl.exif dt { color: var(--ink-soft); }
  dl.exif dd { margin: 0; text-align: right; }

  /* Carousel selector */
  .carousel-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    padding: 5vh 6vw 8vh;
  }

  .carousel-arrow {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid var(--line);
    background: var(--panel);
    color: var(--ink);
    font-size: 16px;
    cursor: pointer;
  }

  .carousel-arrow:hover { background: #fff; }

  .carousel {
    display: flex;
    flex-direction: row-reverse;
    gap: 6px;
    overflow-x: auto;
    width: 800px;
    max-width: 76vw;
    padding: 10px;
    background: var(--panel);
    border: 1px solid var(--line);
    border-radius: 16px;
    scroll-behavior: smooth;
    scrollbar-width: none;
  }

  .carousel::-webkit-scrollbar { display: none; }

  .carousel img {
    height: 72px;
    width: auto;
    object-fit: cover;
    flex-shrink: 0;
    border-radius: 8px;
    opacity: 0.6;
    cursor: pointer;
    border: 1.5px solid transparent;
  }

  .carousel img:hover { opacity: 0.9; }

  .carousel img.active {
    opacity: 1;
    border-color: var(--clay);
  }

  .carousel-position {
    text-align: center;
    font-size: 12px;
    color: var(--ink-soft);
    margin: 10px 0 0;
  }

  footer {
    padding: 40px 6vw 32px;
    border-top: 1px solid var(--line);
  }

  .footer-top {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 28px;
  }

  .footer-brand .wordmark { display: block; margin-bottom: 8px; }

  .footer-brand p {
    font-size: 13px;
    color: var(--ink-soft);
    margin: 0;
  }

  .footer-nav {
    display: flex;
    gap: 24px;
    font-size: 14px;
  }

  .footer-nav a { text-decoration: none; color: var(--ink-soft); }
  .footer-nav a:hover { color: var(--ink); }

  .footer-bottom {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid var(--line);
    font-size: 12px;
    color: var(--ink-soft);
  }

  .footer-bottom a { color: var(--ink-soft); text-decoration: underline; }

  /* Comments (per album, not per photo) */
  .comments {
    max-width: 960px;
    margin: 0 auto;
    padding: 0 6vw 8vh;
  }

  .comments h2 {
    font-family: 'Fraunces', serif;
    font-weight: 500;
    font-size: 22px;
    margin: 0 0 28px;
  }

  .comments-grid {
    display: flex;
    align-items: flex-start;
    gap: 56px;
  }

  .comments-form-col,
  .comments-list-col {
    flex: 1;
    min-width: 0;
  }

  @media (max-width: 720px) {
    .comments-grid { flex-direction: column; gap: 32px; }
  }

  .comment {
    padding: 16px 0;
    border-bottom: 1px solid var(--line);
  }

  .comment-meta {
    font-size: 13px;
    margin: 0 0 6px;
  }

  .comment-meta strong { font-weight: 500; }
  .comment-meta span { color: var(--ink-soft); font-weight: 400; }

  .comment-text {
    font-size: 14px;
    line-height: 1.6;
    color: var(--ink-soft);
    margin: 0;
  }

  .comment-form {
    margin-top: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .comment-form input,
  .comment-form textarea {
    width: 100%;
    padding: 11px 14px;
    border: 1px solid var(--line);
    border-radius: 6px;
    background: var(--panel);
    color: var(--ink);
    font-family: 'Work Sans', sans-serif;
    font-size: 14px;
  }

  .comment-form textarea {
    resize: vertical;
    min-height: 90px;
  }

  .comment-form button {
    align-self: flex-start;
    padding: 11px 24px;
    background: var(--clay);
    border: none;
    border-radius: 999px;
    color: #fff;
    font-family: 'Work Sans', sans-serif;
    font-size: 14px;
    cursor: pointer;
  }

  .comment-form button:hover { opacity: 0.9; }

  .comment-error {
    display: none;
    font-size: 13px;
    color: #A3402E;
  }

  .comment-error.visible { display: block; }

  @media (max-width: 860px) {
    .viewer { flex-direction: column; }
    .viewer-info {
      width: auto;
      padding: 24px 0 0;
      border-left: none;
      border-top: 1px solid var(--line);
    }
    .viewer-media { height: 48vh; }
  }
</style>
</head>
<body>

<header>
  <span class="wordmark">Focale</span>
  <nav>
    <a href="#">Projets</a>
    <a href="#">À propos</a>
    <a href="#">Contact</a>
  </nav>
</header>

<div class="intro">
  <p class="kicker">album — mariage</p>
  <h1>Claire &amp; Antoine</h1>
  <p>Une journée d'automne dans le Bordelais, de la préparation aux premières danses.</p>
  <p class="meta">12 photographies — 14 septembre 2026</p>
  <button class="slideshow-btn" id="slideshow-btn">Lancer le diaporama</button>
</div>

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
  <img data-title="Préparatifs" data-caption="Dans la suite de l'hôtel, avant l'arrivée du photographe officiel." data-tags="préparatifs, matin" data-camera="Sony A7 IV" data-lens="35mm f/1.4" data-focale="35 mm" data-aperture="f/2" data-shutter="1/200s" data-iso="800" data-date="14 sept. 2026, 10:32" data-location="Château Rauzan, Gironde" src="https://picsum.photos/id/1011/1400/1000" alt="Préparatifs de la mariée">
  <img data-title="La robe" data-caption="Détail de la robe suspendue près de la fenêtre." data-tags="détail, robe" data-camera="Sony A7 IV" data-lens="90mm f/2.8 macro" data-focale="90 mm" data-aperture="f/4" data-shutter="1/160s" data-iso="400" data-date="14 sept. 2026, 10:58" data-location="Château Rauzan, Gironde" src="https://picsum.photos/id/1015/1400/1000" alt="Détail de la robe de mariée">
  <img data-title="Échange des vœux" data-caption="L'instant de l'échange des vœux, sous le chêne du parc." data-tags="cérémonie, extérieur" data-camera="Canon R6 II" data-lens="85mm f/1.2" data-focale="85 mm" data-aperture="f/1.8" data-shutter="1/500s" data-iso="200" data-date="14 sept. 2026, 15:14" data-location="Château Rauzan, Gironde" src="https://picsum.photos/id/1016/1400/1000" alt="Les mariés lors de la cérémonie">
  <img data-title="Les alliances" data-caption="Posées quelques minutes avant la cérémonie." data-tags="détail, alliances" data-camera="Sony A7 IV" data-lens="90mm f/2.8 macro" data-focale="90 mm" data-aperture="f/5.6" data-shutter="1/125s" data-iso="200" data-date="14 sept. 2026, 14:40" data-location="Château Rauzan, Gironde" src="https://picsum.photos/id/1018/1400/1000" alt="Alliances posées sur un coussin">
  <img data-title="Le bouquet" data-caption="Composition de saison, réalisée par un fleuriste local." data-tags="détail, fleurs" data-camera="Sony A7 IV" data-lens="35mm f/1.4" data-focale="35 mm" data-aperture="f/2.2" data-shutter="1/250s" data-iso="200" data-date="14 sept. 2026, 11:20" data-location="Château Rauzan, Gironde" src="https://picsum.photos/id/1019/1400/1000" alt="Bouquet de la mariée">
  <img data-title="Sortie de cérémonie" data-caption="Les invités lancent des pétales à la sortie." data-tags="cérémonie, invités" data-camera="Canon R6 II" data-lens="35mm f/1.8" data-focale="35 mm" data-aperture="f/3.5" data-shutter="1/400s" data-iso="200" data-date="14 sept. 2026, 15:47" data-location="Château Rauzan, Gironde" src="https://picsum.photos/id/1024/1400/1000" alt="Sortie de cérémonie">
  <img data-title="Vin d'honneur" data-caption="Les premiers toasts, dans les jardins du château." data-tags="réception, extérieur" data-camera="Canon R6 II" data-lens="50mm f/1.2" data-focale="50 mm" data-aperture="f/2" data-shutter="1/320s" data-iso="200" data-date="14 sept. 2026, 17:10" data-location="Château Rauzan, Gironde" src="https://picsum.photos/id/1025/1400/1000" alt="Vin d'honneur dans les jardins">
  <img data-title="Les invités" data-caption="Un moment de discussion avant le dîner." data-tags="réception, invités" data-camera="Canon R6 II" data-lens="50mm f/1.2" data-focale="50 mm" data-aperture="f/2.2" data-shutter="1/250s" data-iso="400" data-date="14 sept. 2026, 17:35" data-location="Château Rauzan, Gironde" src="https://picsum.photos/id/1035/1400/1000" alt="Les invités réunis">
  <img data-title="La table d'honneur" data-caption="Mise en place avant l'arrivée des invités." data-tags="détail, décoration" data-camera="Sony A7 IV" data-lens="35mm f/1.4" data-focale="35 mm" data-aperture="f/2.8" data-shutter="1/60s" data-iso="640" data-date="14 sept. 2026, 18:50" data-location="Château Rauzan, Gironde" src="https://picsum.photos/id/1036/1400/1000" alt="Décoration de la table d'honneur">
  <img data-title="Première danse" data-caption="Ouverture du bal, en fin de soirée." data-tags="soirée, danse" data-camera="Canon R6 II" data-lens="85mm f/1.2" data-focale="85 mm" data-aperture="f/1.6" data-shutter="1/200s" data-iso="3200" data-date="14 sept. 2026, 21:40" data-location="Château Rauzan, Gironde" src="https://picsum.photos/id/1039/1400/1000" alt="Première danse des mariés">
  <img data-title="La pièce montée" data-caption="Découpe du gâteau avant le début du bal." data-tags="détail, gâteau" data-camera="Sony A7 IV" data-lens="90mm f/2.8 macro" data-focale="90 mm" data-aperture="f/4.5" data-shutter="1/100s" data-iso="1000" data-date="14 sept. 2026, 21:05" data-location="Château Rauzan, Gironde" src="https://picsum.photos/id/1041/1400/1000" alt="Pièce montée du mariage">
  <img data-title="Fin de soirée" data-caption="Les derniers danseurs, autour de minuit." data-tags="soirée, ambiance" data-camera="Canon R6 II" data-lens="35mm f/1.8" data-focale="35 mm" data-aperture="f/2" data-shutter="1/60s" data-iso="4000" data-date="15 sept. 2026, 00:20" data-location="Château Rauzan, Gironde" src="https://picsum.photos/id/1043/1400/1000" alt="Derniers instants de la soirée">
</div>

<section class="comments">
  <h2 id="comments-title">Commentaires (3)</h2>

  <div class="comments-grid">
    <div class="comments-form-col">
      <form class="comment-form" id="comment-form" novalidate>
        <input type="text" id="comment-name" placeholder="Votre nom" autocomplete="name">
        <textarea id="comment-text" placeholder="Votre commentaire"></textarea>
        <p class="comment-error" id="comment-error" role="alert">Merci de renseigner un nom et un commentaire.</p>
        <button type="submit">Publier</button>
      </form>
    </div>

    <div class="comments-list-col" id="comment-list">
      <div class="comment">
        <p class="comment-meta"><strong>Léa</strong> <span>— 15 septembre 2026</span></p>
        <p class="comment-text">Quelle belle journée, merci pour ces photos magnifiques ! On revit chaque instant.</p>
      </div>
      <div class="comment">
        <p class="comment-meta"><strong>Marc</strong> <span>— 15 septembre 2026</span></p>
        <p class="comment-text">La lumière du soir est incroyable, bravo pour le travail.</p>
      </div>
      <div class="comment">
        <p class="comment-meta"><strong>Sophie</strong> <span>— 16 septembre 2026</span></p>
        <p class="comment-text">On garde toutes ces images précieusement. Merci !</p>
      </div>
    </div>
  </div>
</section>

<footer>
  <div class="footer-top">
    <div class="footer-brand">
      <span class="wordmark">Focale</span>
      <p>Photographe de mariage &amp; portrait — Gironde</p>
    </div>
    <nav class="footer-nav">
      <a href="#">Projets</a>
      <a href="#">À propos</a>
      <a href="#">Contact</a>
    </nav>
  </div>
  <div class="footer-bottom">
    <span>© 2026. Tous droits réservés.</span>
    <a href="#">contact@focale.fr</a>
  </div>
</footer>

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
    ['camera', 'Appareil'],
    ['lens', 'Objectif'],
    ['focale', 'Focale'],
    ['aperture', 'Ouverture'],
    ['shutter', 'Vitesse'],
    ['iso', 'ISO'],
    ['date', 'Date de prise'],
    ['location', 'Lieu']
  ];

  let currentIndex = 0;

  // Carousel is a selector, not a trigger for a modal.
  // row-reverse makes it unroll right to left: the first photo sits
  // on the right and the strip scrolls toward it as you move forward.
  images.forEach((img, i) => {
    const thumb = document.createElement('img');
    thumb.src = img.src;
    thumb.alt = '';
    thumb.addEventListener('click', () => { stopSlideshow(); select(i); });
    carousel.appendChild(thumb);
  });
  const thumbs = Array.from(carousel.children);

  // select() is the single source of truth: it updates the main
  // viewer, the carousel, the position indicator, and — if the
  // fullscreen view is open — the fullscreen photo and caption too.
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
    if (activeThumb && typeof activeThumb.scrollIntoView === 'function') {
      activeThumb.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });
    }

    carouselPosition.textContent = `${index + 1} / ${images.length}`;

    if (fullView.classList.contains('open')) {
      fullViewImg.src = img.src;
      fullViewImg.alt = img.alt;
      fullViewCaption.textContent = `${img.dataset.title || ''} — ${index + 1} / ${images.length}`;
    }
  }

  select(0);

  // Click the main photo to view it full screen.
  vImg.addEventListener('click', () => {
    fullView.classList.add('open');
    select(currentIndex);
  });

  function closeFullView() {
    if (slideshowBtn.classList.contains('active')) {
      stopSlideshow();
    } else {
      fullView.classList.remove('open');
    }
  }

  fullViewClose.addEventListener('click', closeFullView);
  fullView.addEventListener('click', (e) => { if (e.target === fullView) closeFullView(); });

  // Keyboard navigation for the main viewer (not just the carousel),
  // and Escape to leave the fullscreen view. Ignored while typing.
  document.addEventListener('keydown', (e) => {
    const tag = document.activeElement && document.activeElement.tagName;
    if (tag === 'INPUT' || tag === 'TEXTAREA') return;

    if (e.key === 'Escape' && fullView.classList.contains('open')) {
      closeFullView();
      return;
    }
    if (e.key === 'ArrowLeft') {
      stopSlideshow();
      select((currentIndex - 1 + images.length) % images.length);
    }
    if (e.key === 'ArrowRight') {
      stopSlideshow();
      select((currentIndex + 1) % images.length);
    }
  });

  // Carousel arrows: scroll by roughly 5 thumbnails at a time.
  const carouselPrev = document.getElementById('carousel-prev');
  const carouselNext = document.getElementById('carousel-next');
  const scrollStep = 5 * (72 + 6);

  carouselPrev.addEventListener('click', () => carousel.scrollBy({ left: -scrollStep, behavior: 'smooth' }));
  carouselNext.addEventListener('click', () => carousel.scrollBy({ left: scrollStep, behavior: 'smooth' }));

  // Slideshow: fullscreen, auto-advancing, pauses on hover.
  const prefersReducedMotion = (typeof window.matchMedia === 'function')
    ? window.matchMedia('(prefers-reduced-motion: reduce)').matches
    : false;
  const slideshowInterval = prefersReducedMotion ? 6000 : 4000;
  let slideshowTimer = null;

  function tick() {
    slideshowTimer = setInterval(() => {
      select((currentIndex + 1) % images.length);
    }, slideshowInterval);
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
    if (slideshowBtn.classList.contains('active')) {
      stopSlideshow();
    } else {
      startSlideshow();
    }
  });

  // Pause auto-advance while the visitor's mouse is over the photo;
  // resume where it left off on mouse leave.
  fullView.addEventListener('mouseenter', () => {
    if (slideshowTimer) {
      clearInterval(slideshowTimer);
      slideshowTimer = null;
    }
  });

  fullView.addEventListener('mouseleave', () => {
    if (slideshowBtn.classList.contains('active') && !slideshowTimer) {
      tick();
    }
  });

  // Album comments (client-side demo only — no real submission)
  const commentForm = document.getElementById('comment-form');
  const commentList = document.getElementById('comment-list');
  const commentsTitle = document.getElementById('comments-title');
  const commentError = document.getElementById('comment-error');
  let commentCount = commentList.children.length;

  commentForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const name = document.getElementById('comment-name').value.trim();
    const text = document.getElementById('comment-text').value.trim();

    if (!name || !text) {
      commentError.classList.add('visible');
      return;
    }
    commentError.classList.remove('visible');

    const entry = document.createElement('div');
    entry.className = 'comment';
    const today = new Date().toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
    entry.innerHTML = `<p class="comment-meta"><strong></strong> <span>— ${today}</span></p><p class="comment-text"></p>`;
    entry.querySelector('strong').textContent = name;
    entry.querySelector('.comment-text').textContent = text;
    commentList.appendChild(entry);

    commentCount++;
    commentsTitle.textContent = `Commentaires (${commentCount})`;
    commentForm.reset();
  });
</script>

</body>
</html>