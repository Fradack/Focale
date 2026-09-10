<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Mon compte — Focale</title>
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
  main { max-width: 560px; margin: 8vh auto; padding: 0 6vw; }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: 28px; margin: 0 0 8px; }
  p { color: var(--ink-soft); line-height: 1.6; }
  .card { background: var(--panel); border: 1px solid var(--line); border-radius: 12px; padding: 24px; margin-top: 24px; }
  button { padding: 10px 18px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel); color: var(--ink); font-size: 13px; font-family: inherit; cursor: pointer; }
  button:hover { border-color: var(--clay); }
</style>
</head>
<body>
<header>
  <a class="wordmark" href="{{ route('home') }}">Focale</a>
</header>
<main>
  <h1>Bonjour {{ $user->name }}</h1>
  <p>Votre espace personnel Focale.</p>
  <div class="card">
    <p style="margin:0;"><strong style="color:var(--ink);">E-mail :</strong> {{ $user->email }}</p>
  </div>
  <div class="card">
    <p style="margin:0 0 12px;"><strong style="color:var(--ink);">Commandes</strong></p>
    <a href="{{ route('customer.orders.index') }}" style="text-decoration:underline;">Voir mes commandes →</a>
  </div>
  <form method="POST" action="{{ route('customer.logout') }}" style="margin-top:24px;">
    @csrf
    <button type="submit">Se déconnecter</button>
  </form>
</main>
</body>
</html>
