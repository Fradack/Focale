<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Marais de Gironde — Focale</title>
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

  main {
    display: grid;
    grid-template-columns: minmax(0, 1.6fr) minmax(280px, 0.9fr);
  }

  .figure {
    background: #0000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 6vh 5vw;
  }

  .figure img {
    width: 100%;
    height: auto;
    max-height: 82vh;
    object-fit: cover;
    display: block;
  }

  figcaption {
    font-size: 12px;
    color: var(--ink-soft);
    margin-top: 12px;
    text-align: left;
  }

  .figure-wrap {
    width: 100%;
  }

  aside {
    background: var(--panel);
    border-left: 1px solid var(--line);
    padding: 7vh 40px 40px;
  }

  .project-link {
    font-size: 12px;
    color: var(--ink-soft);
    letter-spacing: 0.03em;
    margin: 0 0 18px;
  }

  h1 {
    font-family: 'Fraunces', serif;
    font-weight: 500;
    font-size: 34px;
    line-height: 1.15;
    margin: 0 0 18px;
  }

  .description {
    font-size: 15px;
    line-height: 1.7;
    color: var(--ink-soft);
    margin: 0 0 28px;
    max-width: 40ch;
  }

  .tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 0 0 32px;
  }

  .tag {
    font-size: 12px;
    padding: 5px 12px;
    border: 1px solid var(--line);
    border-radius: 999px;
    color: var(--ink-soft);
  }

  .rule {
    height: 1px;
    background: var(--line);
    margin: 0 0 24px;
  }

  h2.section-title {
    font-family: 'Work Sans', sans-serif;
    font-weight: 500;
    font-size: 14px;
    margin: 0 0 16px;
  }

  dl.exif {
    margin: 0 0 32px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    row-gap: 0;
  }

  dl.exif > div {
    display: contents;
  }

  dl.exif dt,
  dl.exif dd {
    margin: 0;
    padding: 9px 0;
    border-bottom: 1px solid var(--line);
    font-size: 13px;
  }

  dl.exif dt {
    color: var(--ink-soft);
  }

  dl.exif dd {
    text-align: right;
  }

  .credit {
    font-size: 13px;
    line-height: 1.7;
    color: var(--ink-soft);
  }

  .credit strong {
    color: var(--ink);
    font-weight: 500;
  }

  footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 22px 6vw;
    border-top: 1px solid var(--line);
    font-size: 14px;
  }

  footer a {
    text-decoration: none;
    color: var(--ink-soft);
  }

  footer a:hover { color: var(--ink); }

  @media (max-width: 860px) {
    main {
      grid-template-columns: 1fr;
    }
    aside {
      border-left: none;
      border-top: 1px solid var(--line);
      padding: 40px 6vw;
    }
    .figure {
      padding: 5vh 6vw 0;
    }
    .description {
      max-width: none;
    }
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

<main>
  <div class="figure">
    <div class="figure-wrap">
      <img src="https://picsum.photos/id/1043/1400/1750" alt="Étendue de marais au petit matin, brume légère au-dessus de l'eau, silhouettes d'arbres à l'horizon">
      <figcaption>Marais de Gironde, série « Basses eaux » — 3 / 12</figcaption>
    </div>
  </div>

  <aside>
    <p class="project-link">série — basses eaux</p>
    <h1>Marais au lever du jour</h1>
    <p class="description">La brume se retire lentement des roseaux. Prise à l'heure où l'eau et le ciel se confondent encore, avant que la lumière ne durcisse.</p>

    <div class="tags">
      <span class="tag">paysage</span>
      <span class="tag">argentique</span>
      <span class="tag">noir et blanc</span>
      <span class="tag">gironde</span>
    </div>

    <div class="rule"></div>

    <h2 class="section-title">Détails techniques</h2>
    <dl class="exif">
      <div><dt>Appareil</dt><dd>Leica M6</dd></div>
      <div><dt>Objectif</dt><dd>Summicron 35mm</dd></div>
      <div><dt>Focale</dt><dd>35 mm</dd></div>
      <div><dt>Ouverture</dt><dd>f/5.6</dd></div>
      <div><dt>Vitesse</dt><dd>1/125s</dd></div>
      <div><dt>ISO</dt><dd>400</dd></div>
      <div><dt>Date de prise</dt><dd>12 mars 2026</dd></div>
      <div><dt>Lieu</dt><dd>Gironde, France</dd></div>
    </dl>

    <div class="rule"></div>

    <p class="credit"><strong>Crédit</strong> — Jonathan, 2026<br>Licence — tous droits réservés</p>
  </aside>
</main>

<footer>
  <a href="#">← Berges de la Dordogne</a>
  <a href="#">Brume sur les vignes →</a>
</footer>

</body>
</html>