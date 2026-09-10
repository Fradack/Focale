<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>{{ $order->order_number }} — Focale</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg: #E7E3DC; --panel: #EFEDE7; --ink: #1E1C19; --ink-soft: #5C574E; --line: #C9C2B4; --ok: #3d6b4f; }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: var(--bg); color: var(--ink); font-family: 'Work Sans', sans-serif; }
  a { color: inherit; }
  header { display: flex; align-items: center; justify-content: space-between; padding: 24px 6vw; border-bottom: 1px solid var(--line); }
  .wordmark { font-family: 'Fraunces', serif; font-weight: 500; font-size: 20px; text-decoration: none; }
  main { max-width: 640px; margin: 8vh auto; padding: 0 6vw; }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: 28px; margin: 0 0 8px; }
  .back-link { display: inline-block; font-size: 13px; color: var(--ink-soft); margin-bottom: 20px; text-decoration: none; }
  .status-pill { display: inline-block; padding: 4px 12px; border-radius: 999px; background: var(--panel); border: 1px solid var(--line); font-size: 12px; margin-bottom: 24px; }
  .card { background: var(--panel); border: 1px solid var(--line); border-radius: 12px; padding: 20px 24px; margin-bottom: 20px; }
  .item-line { display: flex; justify-content: space-between; font-size: 14px; padding: 6px 0; }
  .item-line.total { border-top: 1px solid var(--line); margin-top: 8px; padding-top: 12px; font-weight: 500; font-size: 16px; }
  .confirm { color: var(--ok); font-size: 13px; margin-bottom: 20px; }
</style>
</head>
<body>
<header>
  <a class="wordmark" href="{{ route('home') }}">Focale</a>
</header>
<main>
  <a class="back-link" href="{{ route('customer.orders.index') }}">‹ Mes commandes</a>
  <h1>{{ $order->order_number }}</h1>
  <span class="status-pill">{{ $order->statusLabel() }}</span>

  @if (session('status') === 'order-placed')
    <p class="confirm">Commande confirmée — merci ! Les instructions de paiement sont ci-dessous.</p>
  @endif

  <div class="card">
    @foreach ($order->items as $item)
      <div class="item-line">
        <span>{{ $item->product_title }} — {{ $item->variant_label }} × {{ $item->quantity }}</span>
        <span>{{ $item->subtotalFormatted() }}</span>
      </div>
    @endforeach
    <div class="item-line"><span>Livraison ({{ $order->shipping_label }})</span><span>{{ number_format($order->shipping_price_cents / 100, 2, ',', ' ') }} €</span></div>
    <div class="item-line total"><span>Total</span><span>{{ $order->totalFormatted() }}</span></div>
  </div>

  <div class="card">
    <strong>Paiement : {{ $order->payment_method_label }}</strong>
    @if ($order->payment_instructions)
      <p style="white-space:pre-line;color:var(--ink-soft);margin:10px 0 0;">{{ $order->payment_instructions }}</p>
    @endif
  </div>
</main>
</body>
</html>
