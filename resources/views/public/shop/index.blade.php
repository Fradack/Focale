<!DOCTYPE html>
<html lang="fr"{!! \App\Support\Theme::publicHtmlAttr() !!}>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Boutique — {{ \App\Models\Setting::get('site_name', 'Focale') }}</title>
<link rel="canonical" href="{{ route('public.shop.index') }}">
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
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: clamp(28px, 4.5vw, 40px); margin: 0 0 24px; }
  .type-filters { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 40px; }
  .type-filter { padding: 8px 16px; border: 1px solid var(--line); border-radius: 999px; font-size: 13px; color: var(--ink-soft); }
  .type-filter:hover { border-color: var(--ink); color: var(--ink); }
  .type-filter.active { background: var(--ink); border-color: var(--ink); color: #fff; }
  .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 32px; }
  .card-media { aspect-ratio: 4/5; background: var(--img-fallback); overflow: hidden; }
  .card-media img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.4s ease; }
  .card:hover .card-media img { transform: scale(1.04); }
  .card-title { font-family: 'Fraunces', serif; font-weight: 500; font-size: 19px; margin: 16px 0 4px; }
  .card-meta { font-size: 13px; color: var(--ink-soft); }
  .empty { color: var(--ink-soft); font-size: 15px; }
</style>
@include('components.theme-vars-dark')
</head>
<body>

<x-public-nav />

<main>
  <h1>Boutique</h1>

  <div class="type-filters">
    <a class="type-filter {{ $activeType ? '' : 'active' }}" href="{{ route('public.shop.index') }}">Tout</a>
    @foreach ($types as $key => $label)
      <a class="type-filter {{ $activeType === $key ? 'active' : '' }}" href="{{ route('public.shop.index', ['type' => $key]) }}">{{ $label }}</a>
    @endforeach
  </div>

  @if ($products->isEmpty())
    <p class="empty">Aucun produit disponible pour l'instant.</p>
  @else
    <div class="grid">
      @foreach ($products as $product)
        <a class="card" href="{{ route('public.shop.show', $product) }}">
          <div class="card-media">
            @if ($cover = $product->coverMedia?->variant('web'))
              <img src="{{ $cover->url() }}" alt="{{ $product->title }}">
            @endif
          </div>
          <p class="card-title">{{ $product->title }}</p>
          <p class="card-meta">
            {{ $types[$product->type] ?? $product->type }}
            @if ($cheapest = $product->cheapestVariant())
              — à partir de {{ $cheapest->priceFormatted() }}
            @endif
          </p>
        </a>
      @endforeach
    </div>
  @endif
</main>

<x-public-footer />

</body>
</html>
