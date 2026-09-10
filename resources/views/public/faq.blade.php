<!DOCTYPE html>
<html lang="fr"{!! \App\Support\Theme::publicHtmlAttr() !!}>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Foire aux questions — Focale</title>
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
  nav { display: flex; gap: 28px; font-size: 14px; color: var(--ink-soft); }
  nav a { text-decoration: none; }
  nav a:hover { color: var(--ink); }
  main { max-width: 720px; margin: 8vh auto; padding: 0 6vw 8vh; }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: clamp(30px, 4.5vw, 42px); margin: 0 0 8px; }
  .lede { font-size: 15px; color: var(--ink-soft); margin: 0 0 40px; }
  h2.section-title { font-family: 'Work Sans', sans-serif; font-weight: 500; font-size: 13px; text-transform: uppercase; letter-spacing: 0.04em; color: var(--ink-soft); margin: 36px 0 14px; }
  h2.section-title:first-of-type { margin-top: 0; }
  details.faq-tile { border: 1px solid var(--line); border-radius: 10px; background: var(--panel); margin-bottom: 10px; overflow: hidden; }
  details.faq-tile summary { list-style: none; cursor: pointer; padding: 16px 20px; font-size: 15px; font-weight: 500; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
  details.faq-tile summary::-webkit-details-marker { display: none; }
  details.faq-tile summary::after { content: '+'; font-size: 20px; color: var(--ink-soft); flex-shrink: 0; transition: transform 0.15s ease; }
  details.faq-tile[open] summary::after { transform: rotate(45deg); }
  details.faq-tile .faq-answer { padding: 0 20px 18px; font-size: 14px; line-height: 1.7; color: var(--ink-soft); }
</style>
@include('components.theme-vars-dark')
</head>
<body>

<x-public-nav />

<main>
  <h1>Foire aux questions</h1>
  <p class="lede">Le fonctionnement du site, en bref.</p>

  @foreach ($sections as $category => $items)
    <h2 class="section-title">{{ $category }}</h2>
    @foreach ($items as $item)
      <details class="faq-tile">
        <summary>{{ $item->question }}</summary>
        <div class="faq-answer">{{ $item->answer }}</div>
      </details>
    @endforeach
  @endforeach
</main>

</body>
</html>
