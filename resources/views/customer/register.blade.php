<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Créer un compte — Focale</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg: #E7E3DC; --panel: #EFEDE7; --ink: #1E1C19; --ink-soft: #5C574E; --clay: #7A4B33; --line: #C9C2B4; --danger: #A3402E; }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: var(--bg); color: var(--ink); font-family: 'Work Sans', sans-serif; }
  a { color: inherit; }
  .wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 6vh 6vw; }
  .card { background: var(--panel); border: 1px solid var(--line); border-radius: 14px; padding: 40px; width: 100%; max-width: 420px; }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: 26px; margin: 0 0 24px; }
  label { display: block; font-size: 13px; color: var(--ink-soft); margin-bottom: 6px; }
  .field { margin-bottom: 16px; }
  input { width: 100%; padding: 10px 12px; border: 1px solid var(--line); border-radius: 8px; background: #fff; font-size: 14px; font-family: inherit; }
  .error { color: var(--danger); font-size: 12px; margin-top: 4px; }
  button { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--clay); background: var(--clay); color: #fff; font-size: 14px; font-family: inherit; cursor: pointer; margin-top: 8px; }
  button:hover { opacity: 0.92; }
  .switch { margin-top: 20px; font-size: 13px; color: var(--ink-soft); text-align: center; }
  .switch a { text-decoration: underline; }
</style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <h1>Créer un compte</h1>
    <form method="POST" action="{{ route('customer.register.store') }}">
      @csrf
      <div class="field">
        <label for="name">Nom</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
        @error('name')<div class="error">{{ $message }}</div>@enderror
      </div>
      <div class="field">
        <label for="email">E-mail</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        @error('email')<div class="error">{{ $message }}</div>@enderror
      </div>
      <div class="field">
        <label for="password">Mot de passe</label>
        <input id="password" type="password" name="password" required>
        @error('password')<div class="error">{{ $message }}</div>@enderror
      </div>
      <div class="field">
        <label for="password_confirmation">Confirmer le mot de passe</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>
      </div>
      <button type="submit">Créer mon compte</button>
    </form>
    <p class="switch">Déjà un compte ? <a href="{{ route('customer.login') }}">Se connecter</a></p>
  </div>
</div>
</body>
</html>
