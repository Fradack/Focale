<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $page->seo_title ?: $page->title }} — Focale</title>
@if ($page->seo_description)
<meta name="description" content="{{ $page->seo_description }}">
@endif
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg: #E7E3DC; --ink: #1E1C19; --ink-soft: #5C574E; --line: #C9C2B4; }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: var(--bg); color: var(--ink); font-family: 'Work Sans', sans-serif; }
  a { color: inherit; }
  header { display: flex; align-items: center; justify-content: space-between; padding: 24px 6vw; border-bottom: 1px solid var(--line); }
  .wordmark { font-family: 'Fraunces', serif; font-weight: 500; font-size: 20px; text-decoration: none; }
  nav { display: flex; gap: 28px; font-size: 14px; color: var(--ink-soft); }
  nav a { text-decoration: none; }
  nav a:hover { color: var(--ink); }
  main { max-width: 680px; margin: 8vh auto; padding: 0 6vw; }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: clamp(28px, 4.5vw, 40px); margin: 0 0 28px; }
  .content { font-size: 16px; line-height: 1.8; color: var(--ink-soft); }
  .content p { margin: 0 0 1.2em; }
  .content img { max-width: 100%; height: auto; border-radius: 8px; margin: 0.4em 0 1.2em; display: block; }
  .content blockquote { margin: 0 0 1.2em; padding: 4px 20px; border-left: 3px solid var(--line); color: var(--ink); font-style: italic; }
  .content ul { margin: 0 0 1.2em; padding-left: 22px; }
  .content pre { background: #fff; border: 1px solid var(--line); border-radius: 8px; padding: 14px; overflow-x: auto; margin: 0 0 1.2em; }
  .content a { color: var(--ink); text-decoration: underline; }
</style>
</head>
<body>

<x-public-nav />

<main>
  <h1>{{ $page->title }}</h1>
  <div class="content">
    @foreach ($page->blocks as $block)
      @if ($block->type === 'text')
        {!! \App\Services\BbcodeParser::toHtml($block->content['text'] ?? '') !!}
      @endif
    @endforeach
  </div>
</main>

<x-public-footer />

</body>
</html>
