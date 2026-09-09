<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact — Focale</title>
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
  nav { display: flex; gap: 28px; font-size: 14px; color: var(--ink-soft); }
  nav a { text-decoration: none; }
  nav a:hover { color: var(--ink); }
  main { max-width: 560px; margin: 8vh auto; padding: 0 6vw; }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: clamp(28px, 4.5vw, 38px); margin: 0 0 24px; }
  .notice { font-size: 14px; color: var(--ink-soft); background: var(--panel); border: 1px solid var(--line); border-radius: 8px; padding: 14px 16px; margin-bottom: 20px; }
  .error { font-size: 13px; color: #A3402E; margin: 4px 0 0; }
  form { display: flex; flex-direction: column; gap: 14px; }
  input, textarea { width: 100%; padding: 12px 14px; border: 1px solid var(--line); border-radius: 6px; background: var(--panel); color: var(--ink); font-family: 'Work Sans', sans-serif; font-size: 14px; }
  textarea { resize: vertical; min-height: 140px; }
  button { align-self: flex-start; padding: 12px 26px; background: var(--clay); border: none; border-radius: 999px; color: #fff; font-size: 14px; cursor: pointer; }
  button:hover { opacity: 0.9; }
</style>
</head>
<body>

<x-public-nav />

<main>
  <h1>Contact</h1>

  @if (session('status') === 'message-sent')
    <p class="notice">Merci, votre message a bien été envoyé.</p>
  @else
    <form method="POST" action="{{ route('public.contact.store') }}">
      @csrf
      <div>
        <input type="text" name="name" placeholder="Votre nom" value="{{ old('name') }}">
        @error('name') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div>
        <input type="email" name="email" placeholder="Votre e-mail" value="{{ old('email') }}">
        @error('email') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div>
        <input type="text" name="subject" placeholder="Sujet (optionnel)" value="{{ old('subject') }}">
      </div>
      <div>
        <textarea name="message" placeholder="Votre message">{{ old('message') }}</textarea>
        @error('message') <p class="error">{{ $message }}</p> @enderror
      </div>
      <input type="hidden" name="form_started_at" value="{{ time() }}">
      <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;">
      @if ($turnstileEnabled)
        <div class="cf-turnstile" data-sitekey="{{ \App\Models\Setting::get('turnstile_site_key') }}"></div>
      @endif
      <button type="submit">Envoyer</button>
    </form>
    @if ($turnstileEnabled)
      <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
  @endif
</main>

<x-public-footer />

</body>
</html>
