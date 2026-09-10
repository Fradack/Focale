<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Mes commandes — Focale</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg: #E7E3DC; --panel: #EFEDE7; --ink: #1E1C19; --ink-soft: #5C574E; --line: #C9C2B4; }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: var(--bg); color: var(--ink); font-family: 'Work Sans', sans-serif; }
  a { color: inherit; }
  header { display: flex; align-items: center; justify-content: space-between; padding: 24px 6vw; border-bottom: 1px solid var(--line); }
  .wordmark { font-family: 'Fraunces', serif; font-weight: 500; font-size: 20px; text-decoration: none; }
  main { max-width: 640px; margin: 8vh auto; padding: 0 6vw; }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: 28px; margin: 0 0 24px; }
  .back-link { display: inline-block; font-size: 13px; color: var(--ink-soft); margin-bottom: 20px; text-decoration: none; }
  .order-row { display: flex; justify-content: space-between; align-items: center; background: var(--panel); border: 1px solid var(--line); border-radius: 12px; padding: 16px 20px; margin-bottom: 12px; text-decoration: none; }
  .order-row .num { font-weight: 500; }
  .order-row .meta { font-size: 13px; color: var(--ink-soft); }
  .empty { color: var(--ink-soft); }
</style>
</head>
<body>
<header>
  <a class="wordmark" href="{{ route('home') }}">Focale</a>
</header>
<main>
  <a class="back-link" href="{{ route('customer.dashboard') }}">‹ Mon compte</a>
  <h1>Mes commandes</h1>

  @if ($orders->isEmpty())
    <p class="empty">Vous n'avez pas encore passé de commande.</p>
  @else
    @foreach ($orders as $order)
      <a class="order-row" href="{{ route('customer.orders.show', $order) }}">
        <span>
          <span class="num">{{ $order->order_number }}</span><br>
          <span class="meta">{{ $order->created_at->translatedFormat('d F Y') }}</span>
        </span>
        <span>
          <span class="meta">{{ $order->statusLabel() }}</span><br>
          {{ $order->totalFormatted() }}
        </span>
      </a>
    @endforeach

    <x-pagination :paginator="$orders" />
  @endif
</main>
</body>
</html>
