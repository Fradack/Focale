<!DOCTYPE html>
<html lang="fr"{!! \App\Support\Theme::publicHtmlAttr() !!}>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Commander — {{ \App\Models\Setting::get('site_name', 'Focale') }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg: #E7E3DC; --ink: #1E1C19; --ink-soft: #5C574E; --line: #C9C2B4; }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: var(--bg); color: var(--ink); font-family: 'Work Sans', sans-serif; }
  a { color: inherit; text-decoration: none; }
  header { display: flex; align-items: center; justify-content: space-between; padding: 24px 6vw; border-bottom: 1px solid var(--line); }
  .wordmark { font-family: 'Fraunces', serif; font-weight: 500; font-size: 20px; }
  main { padding: 6vh 6vw 8vh; max-width: 640px; }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: clamp(26px, 3.5vw, 34px); margin: 0 0 32px; }
  .back-link { display: inline-block; font-size: 13px; color: var(--ink-soft); margin-bottom: 24px; }
  .summary { border: 1px solid var(--line); border-radius: 12px; padding: 18px 20px; margin-bottom: 28px; }
  .summary-line { display: flex; justify-content: space-between; font-size: 14px; padding: 6px 0; }
  .summary-line.total { border-top: 1px solid var(--line); margin-top: 6px; padding-top: 12px; font-weight: 500; font-size: 16px; }
  fieldset { border: none; padding: 0; margin: 0 0 28px; }
  legend { font-family: 'Fraunces', serif; font-size: 17px; margin-bottom: 12px; padding: 0; }
  .option { display: block; padding: 14px 16px; border: 1px solid var(--line); border-radius: 10px; margin-bottom: 8px; cursor: pointer; font-size: 14px; }
  .option:has(input:checked) { border-color: var(--ink); }
  .option input { accent-color: var(--ink); margin-right: 10px; }
  .option-price { float: right; color: var(--ink-soft); }
  .option-instructions { display: block; margin: 6px 0 0 24px; font-size: 12px; color: var(--ink-soft); }
  .paypal-note { margin: 10px 0 0 24px; padding: 10px 12px; background: rgba(0,0,0,0.04); border-radius: 8px; font-size: 12px; color: var(--ink-soft); }
  .paypal-note strong { color: var(--ink); }
  .paypal-note code { background: rgba(0,0,0,0.08); padding: 1px 6px; border-radius: 4px; color: var(--ink); font-size: 12px; }
  textarea { width: 100%; padding: 10px 12px; border: 1px solid var(--line); border-radius: 8px; font-family: inherit; font-size: 14px; background: transparent; color: var(--ink); }
  .place-btn { display: block; width: 100%; padding: 16px; background: var(--ink); color: var(--bg); border: none; border-radius: 999px; font-size: 15px; cursor: pointer; }
  .place-btn:hover { opacity: 0.9; }
  .error { color: #a33; font-size: 12px; margin: 4px 0 0; }
</style>
</head>
<body>

<header>
  <a class="wordmark" href="{{ route('home') }}">{{ \App\Models\Setting::get('site_name', 'Focale') }}</a>
</header>

<main>
  <a class="back-link" href="{{ route('public.cart.show') }}">‹ Retour au panier</a>
  <h1>Finaliser la commande</h1>

  <div class="summary">
    @foreach ($items as $item)
      <div class="summary-line">
        <span>{{ $item['variant']->product->title }} — {{ $item['variant']->label }} × {{ $item['quantity'] }}</span>
        <span>{{ number_format($item['subtotal_cents'] / 100, 2, ',', ' ') }} €</span>
      </div>
    @endforeach
    <div class="summary-line total">
      <span>Articles</span>
      <span>{{ $total }}</span>
    </div>
  </div>

  <form method="POST" action="{{ route('public.cart.checkout.store') }}">
    @csrf

    <fieldset>
      <legend>Livraison</legend>
      @forelse ($shippingOptions as $option)
        <label class="option">
          <input type="radio" name="shipping_option_id" value="{{ $option->id }}" @checked($loop->first) required>
          {{ $option->label }}
          <span class="option-price">{{ $option->priceFormatted() }}</span>
        </label>
      @empty
        <p style="font-size:13px;color:var(--ink-soft);">Aucune option de livraison disponible pour l'instant.</p>
      @endforelse
      @error('shipping_option_id') <p class="error">{{ $message }}</p> @enderror
    </fieldset>

    <fieldset>
      <legend>Paiement</legend>
      @forelse ($paymentMethods as $method)
        <label class="option">
          <input type="radio" name="payment_method_id" value="{{ $method->id }}" @checked($loop->first) required>
          {{ $method->label }}
          @if ($method->isPaypal())
            <span class="paypal-note">
              Vous serez redirigé vers <strong>{{ $method->paypal_link }}</strong> pour régler.
              Indiquez impérativement le code <code>{{ $orderReference }}</code> dans le message du paiement PayPal, afin que nous puissions identifier votre règlement.
              @if ($method->instructions)
                <br>{{ $method->instructions }}
              @endif
            </span>
          @elseif ($method->instructions)
            <span class="option-instructions">{{ $method->instructions }}</span>
          @endif
        </label>
      @empty
        <p style="font-size:13px;color:var(--ink-soft);">Aucun moyen de paiement disponible pour l'instant.</p>
      @endforelse
      @error('payment_method_id') <p class="error">{{ $message }}</p> @enderror
    </fieldset>

    <fieldset>
      <legend>Message (optionnel)</legend>
      <textarea name="customer_note" rows="3" placeholder="Précisions sur votre commande…">{{ old('customer_note') }}</textarea>
    </fieldset>

    @if ($shippingOptions->isNotEmpty() && $paymentMethods->isNotEmpty())
      <button type="submit" class="place-btn">Confirmer la commande</button>
    @endif
  </form>
</main>

</body>
</html>
