<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Focale</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #E7E3DC;
    --ink: #1E1C19;
    --ink-soft: #5C574E;
    --clay: #7A4B33;
    --line: #C9C2B4;
  }

  * { box-sizing: border-box; }

  html, body {
    margin: 0;
    padding: 0;
    background: var(--bg);
    color: var(--ink);
    height: 100%;
  }

  body {
    font-family: 'Work Sans', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 6vh 6vw;
  }

  .cover {
    width: 100%;
    max-width: 640px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .rule {
    width: 100%;
    height: 1px;
    background: var(--line);
  }

  .kicker {
    margin: 28px 0 0;
    font-size: 13px;
    letter-spacing: 0.06em;
    color: var(--ink-soft);
  }

  .aperture {
    width: min(46vw, 260px);
    height: min(46vw, 260px);
    margin: 64px 0 48px;
  }

  .blade {
    fill: var(--clay);
    transform-origin: 130px 130px;
    transform: scale(0.4);
    opacity: 0;
    animation: open 900ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
    animation-delay: 120ms;
  }

  .blade:nth-child(2) { animation-delay: 170ms; }
  .blade:nth-child(3) { animation-delay: 220ms; }
  .blade:nth-child(4) { animation-delay: 270ms; }
  .blade:nth-child(5) { animation-delay: 320ms; }
  .blade:nth-child(6) { animation-delay: 370ms; }
  .blade:nth-child(7) { animation-delay: 420ms; }
  .blade:nth-child(8) { animation-delay: 470ms; }

  @keyframes open {
    to { transform: scale(1); opacity: 1; }
  }

  @media (prefers-reduced-motion: reduce) {
    .blade {
      animation: none;
      transform: scale(1);
      opacity: 1;
    }
  }

  h1 {
    margin: 0;
    font-family: 'Fraunces', serif;
    font-optical-sizing: auto;
    font-weight: 500;
    font-size: clamp(48px, 9vw, 84px);
    line-height: 1;
  }

  .tagline {
    margin: 20px 0 0;
    max-width: 34ch;
    font-size: 16px;
    line-height: 1.6;
    color: var(--ink-soft);
  }

  .meta {
    margin-top: 64px;
    font-size: 12px;
    letter-spacing: 0.04em;
    color: var(--ink-soft);
  }

  @media (max-width: 480px) {
    .aperture { margin: 48px 0 36px; }
    .meta { margin-top: 40px; }
  }
</style>
</head>
<body>

<div class="cover">
  <div class="rule"></div>
  <p class="kicker">cms auto-hébergé pour artistes</p>

  <svg class="aperture" viewBox="0 0 260 260" role="img" aria-label="Diaphragme d'appareil photo, symbole du projet Focale">
    <g transform="translate(130,130)">
      <polygon class="blade" points="0,-110 19,-70 -19,-70" transform="rotate(0)"></polygon>
      <polygon class="blade" points="0,-110 19,-70 -19,-70" transform="rotate(45)"></polygon>
      <polygon class="blade" points="0,-110 19,-70 -19,-70" transform="rotate(90)"></polygon>
      <polygon class="blade" points="0,-110 19,-70 -19,-70" transform="rotate(135)"></polygon>
      <polygon class="blade" points="0,-110 19,-70 -19,-70" transform="rotate(180)"></polygon>
      <polygon class="blade" points="0,-110 19,-70 -19,-70" transform="rotate(225)"></polygon>
      <polygon class="blade" points="0,-110 19,-70 -19,-70" transform="rotate(270)"></polygon>
      <polygon class="blade" points="0,-110 19,-70 -19,-70" transform="rotate(315)"></polygon>
    </g>
  </svg>

  <h1>Focale</h1>
  <p class="tagline">Un atelier numérique pour publier son travail : photographies, séries et projets, sur son propre nom de domaine.</p>

  <p class="meta">2026</p>
  <div class="rule" style="margin-top: 28px;"></div>
</div>

</body>
</html>