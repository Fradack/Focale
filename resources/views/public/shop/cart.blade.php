<!DOCTYPE html>
<html lang="fr"{!! \App\Support\Theme::publicHtmlAttr() !!}>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panier — {{ \App\Models\Setting::get('site_name', 'Focale') }}</title>
<link rel="canonical" href="{{ route('public.cart.show') }}">
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
  main { padding: 6vh 6vw 8vh; max-width: 820px; }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: clamp(28px, 4.5vw, 40px); margin: 0 0 40px; }
  .empty { color: var(--ink-soft); font-size: 15px; }
  .cart-item { display: grid; grid-template-columns: 88px 1fr auto auto; gap: 20px; align-items: center; padding: 20px 0; border-bottom: 1px solid var(--line); }
  @media (max-width: 600px) { .cart-item { grid-template-columns: 64px 1fr; grid-template-areas: "media info" "media qty" "media price"; } }
  .cart-item-media { width: 88px; height: 88px; background: var(--img-fallback); overflow: hidden; border-radius: 8px; }
  .cart-item-media img { width: 100%; height: 100%; object-fit: cover; display: block; }
  .cart-item-title { font-family: 'Fraunces', serif; font-size: 16px; margin: 0 0 4px; }
  .cart-item-variant { font-size: 13px; color: var(--ink-soft); }
  .qty-form { display: flex; align-items: center; gap: 8px; }
  .qty-form input { width: 56px; padding: 6px 8px; border: 1px solid var(--line); border-radius: 8px; font-size: 14px; background: transparent; color: var(--ink); }
  .qty-form button { border: 1px solid var(--line); background: transparent; border-radius: 8px; padding: 6px 10px; font-size: 13px; color: var(--ink-soft); cursor: pointer; }
  .qty-form button:hover { border-color: var(--ink); color: var(--ink); }
  .remove-form button { border: none; background: transparent; font-size: 13px; color: var(--ink-soft); text-decoration: underline; cursor: pointer; padding: 0; }
  .remove-form button:hover { color: var(--ink); }
  .cart-item-price { font-size: 14px; white-space: nowrap; }
  .cart-summary { display: flex; justify-content: space-between; align-items: center; padding-top: 28px; font-family: 'Fraunces', serif; font-size: 20px; }
  .cart-note { margin-top: 20px; padding: 16px 18px; border: 1px solid var(--line); border-radius: 10px; font-size: 13px; color: var(--ink-soft); }
  .add-btn { padding: 14px 28px; background: var(--ink); color: var(--bg); border: none; border-radius: 999px; font-size: 14px; cursor: pointer; text-decoration: none; }
  .add-btn:hover { opacity: 0.9; }
</style>
@include('components.theme-vars-dark')
</head>
<body>

<x-public-nav />

<main>
  <h1>Panier</h1>

  @if ($items->isEmpty())
    <p class="empty">Votre panier est vide. <a href="{{ route('public.shop.index') }}" style="text-decoration: underline;">Voir la boutique</a>.</p>
  @else
    @foreach ($items as $item)
      @php $variant = $item['variant']; $product = $variant->product; @endphp
      <div class="cart-item">
        <div class="cart-item-media">
          @if ($cover = $product->coverMedia?->variant('thumbnail'))
            <img src="{{ $cover->url() }}" alt="{{ $product->title }}">
          @endif
        </div>
        <div>
          <p class="cart-item-title">{{ $product->title }}</p>
          <p class="cart-item-variant">{{ $variant->label }}</p>
          <form class="remove-form" method="POST" action="{{ route('public.cart.remove', $variant) }}">
            @csrf @method('DELETE')
            <button type="submit">Retirer</button>
          </form>
        </div>
        <form class="qty-form" method="POST" action="{{ route('public.cart.update', $variant) }}">
          @csrf @method('PATCH')
          <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="0" max="20">
          <button type="submit">Mettre à jour</button>
        </form>
        <p class="cart-item-price">{{ number_format($item['subtotal_cents'] / 100, 2, ',', ' ') }} €</p>
      </div>
    @endforeach

    <div class="cart-summary">
      <span>Total</span>
      <span>{{ $total }}</span>
    </div>

    <a href="{{ route('public.cart.checkout') }}" class="add-btn" style="display:inline-block;margin-top:24px;">Passer commande</a>
  @endif
</main>

<x-public-footer />

</body>
</html>
