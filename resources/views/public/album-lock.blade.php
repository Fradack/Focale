<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>Album protégé — Focale</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg: #E7E3DC; --ink: #1E1C19; --ink-soft: #5C574E; --clay: #7A4B33; --line: #C9C2B4; --line-on-scrim: rgba(231, 227, 220, 0.4); }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; height: 100%; background: var(--bg); color: var(--ink); font-family: 'Work Sans', sans-serif; }
  a { color: inherit; }
  header { position: relative; z-index: 2; display: flex; align-items: center; justify-content: space-between; padding: 24px 6vw; }
  .wordmark { font-family: 'Fraunces', serif; font-weight: 500; font-size: 20px; color: #fff; text-decoration: none; }
  .scene { position: relative; min-height: 100vh; display: flex; flex-direction: column; }
  .backdrop {
    position: absolute; inset: 0; background-color: #24211C;
    @if($cover = $album->cover?->variant('web'))
    background-image: linear-gradient(160deg, rgba(36,33,28,0.65), rgba(20,18,15,0.75)), url('{{ $cover->url() }}');
    @else
    background-image: linear-gradient(160deg, rgba(36,33,28,0.65), rgba(20,18,15,0.75));
    @endif
    background-size: cover; background-position: center; filter: blur(18px) saturate(0.9); transform: scale(1.08);
  }
  .gate { position: relative; z-index: 2; flex: 1; display: flex; align-items: center; justify-content: center; padding: 6vh 6vw; }
  .gate-content { width: 100%; max-width: 380px; text-align: center; color: #fff; }
  .lock { margin: 0 auto 28px; width: 34px; height: 34px; }
  .lock path, .lock rect { fill: none; stroke: rgba(255,255,255,0.85); stroke-width: 1.4; stroke-linecap: round; stroke-linejoin: round; }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: 30px; margin: 0 0 12px; }
  .intro { font-size: 14px; line-height: 1.7; color: rgba(255,255,255,0.75); margin: 0 0 32px; }
  form { display: flex; flex-direction: column; gap: 14px; }
  label { text-align: left; font-size: 13px; color: rgba(255,255,255,0.75); }
  input[type="password"] { width: 100%; padding: 12px 14px; background: rgba(255,255,255,0.08); border: 1px solid var(--line-on-scrim); border-radius: 6px; color: #fff; font-family: 'Work Sans', sans-serif; font-size: 15px; }
  .error { text-align: left; font-size: 13px; color: #F2B8A9; margin: 0; }
  button { margin-top: 6px; padding: 13px 20px; background: #fff; color: var(--ink); border: none; border-radius: 6px; font-family: 'Work Sans', sans-serif; font-size: 15px; font-weight: 500; cursor: pointer; }
  button:hover { background: #EFEDE7; }
  .note { margin-top: 28px; font-size: 12px; color: rgba(255,255,255,0.45); }
  footer { position: relative; z-index: 2; text-align: center; padding: 20px 6vw; font-size: 12px; color: rgba(255,255,255,0.4); }
</style>
</head>
<body>

<div class="scene">
  <div class="backdrop" aria-hidden="true"></div>

  <header>
    <a class="wordmark" href="{{ route('home') }}">Focale</a>
  </header>

  <div class="gate">
    <div class="gate-content">
      <svg class="lock" viewBox="0 0 34 34" role="img" aria-label="Cadenas">
        <rect x="7" y="15" width="20" height="15" rx="3"></rect>
        <path d="M11 15 V10 a6 6 0 0 1 12 0 v5"></path>
      </svg>

      <h1>Album protégé</h1>
      <p class="intro">« {{ $album->title }} » n'est pas répertorié publiquement. Saisis le mot de passe transmis par l'artiste pour y accéder.</p>

      <form method="POST" action="{{ route('public.album.unlock', $album) }}">
        @csrf
        <label for="pwd">Mot de passe</label>
        <input type="password" id="pwd" name="password" placeholder="••••••••" autocomplete="off" autofocus>
        @error('password') <p class="error">{{ $message }}</p> @enderror
        <button type="submit">Voir l'album</button>
      </form>

      <p class="note">L'accès à cette page n'est pas indexé par les moteurs de recherche.</p>
    </div>
  </div>

  <footer>Focale — atelier numérique</footer>
</div>

</body>
</html>
