<!DOCTYPE html>
<html lang="fr"{!! \App\Support\Theme::publicHtmlAttr() !!}>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $product->title }} — {{ \App\Models\Setting::get('site_name', 'Focale') }}</title>
<link rel="canonical" href="{{ route('public.shop.show', $product) }}">
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
  .product { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(280px, 0.9fr); gap: 6vw; align-items: start; }
  @media (max-width: 800px) { .product { grid-template-columns: 1fr; } }
  .product-media { aspect-ratio: 4/5; background: var(--img-fallback); overflow: hidden; }
  .product-media img { width: 100%; height: 100%; object-fit: cover; display: block; }
  .back-link { display: inline-block; font-size: 13px; color: var(--ink-soft); margin-bottom: 24px; }
  .back-link:hover { color: var(--ink); }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: clamp(26px, 3.5vw, 34px); margin: 0 0 8px; }
  .product-type { font-size: 13px; color: var(--ink-soft); margin-bottom: 20px; }
  .product-description { font-size: 15px; line-height: 1.7; color: var(--ink-soft); margin-bottom: 32px; }
  fieldset { border: none; padding: 0; margin: 0 0 24px; }
  legend { font-size: 13px; color: var(--ink-soft); margin-bottom: 10px; padding: 0; }
  .variant-option { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 14px; border: 1px solid var(--line); border-radius: 10px; margin-bottom: 8px; cursor: pointer; }
  .variant-option:has(input:checked) { border-color: var(--ink); }
  .variant-option input { accent-color: var(--ink); }
  .variant-option-label { display: flex; align-items: center; gap: 10px; font-size: 14px; }
  .variant-option-price { font-size: 14px; color: var(--ink-soft); }
  .qty-row { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
  .qty-row label { font-size: 13px; color: var(--ink-soft); }
  .qty-row input { width: 64px; padding: 8px 10px; border: 1px solid var(--line); border-radius: 8px; font-size: 14px; background: transparent; color: var(--ink); }
  .add-btn { display: inline-block; padding: 14px 28px; background: var(--ink); color: var(--bg); border: none; border-radius: 999px; font-size: 14px; cursor: pointer; }
  .add-btn:hover { opacity: 0.9; }
  .status { font-size: 13px; color: var(--ink-soft); margin-top: 14px; }
</style>
@include('components.theme-vars-dark')
</head>
<body>

<x-public-nav />

<main>
  <a class="back-link" href="{{ route('public.shop.index') }}">‹ Retour à la boutique</a>

  <div class="product">
    <div class="product-media">
      @if ($cover = $product->coverMedia?->variant('web'))
        <img src="{{ $cover->url() }}" alt="{{ $product->title }}">
      @endif
    </div>

    <div>
      <h1>{{ $product->title }}</h1>
      <p class="product-type">{{ ['album' => 'Album photo', 'cadre' => 'Tirage encadré', 'tableau' => 'Tableau'][$product->type] ?? $product->type }}</p>

      @if ($product->description)
        <p class="product-description">{{ $product->description }}</p>
      @endif

      @if ($product->variants->isNotEmpty())
        <form method="POST" action="{{ route('public.cart.add') }}">
          @csrf
          <fieldset>
            <legend>Format</legend>
            @foreach ($product->variants as $variant)
              <label class="variant-option">
                <span class="variant-option-label">
                  <input type="radio" name="variant_id" value="{{ $variant->id }}" @checked($loop->first) required>
                  {{ $variant->label }}
                </span>
                <span class="variant-option-price">{{ $variant->priceFormatted() }}</span>
              </label>
            @endforeach
          </fieldset>

          <div class="qty-row">
            <label for="quantity">Quantité</label>
            <input type="number" id="quantity" name="quantity" value="1" min="1" max="20">
          </div>

          <button type="submit" class="add-btn">Ajouter au panier</button>

          @if (session('status') === 'cart-added')
            <p class="status">Ajouté au panier.</p>
          @endif
        </form>
      @endif
    </div>
  </div>
</main>

<x-public-footer />

</body>
</html>
