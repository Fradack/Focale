<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Donner mon avis — Focale</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg: #E7E3DC; --panel: #EFEDE7; --ink: #1E1C19; --ink-soft: #5C574E; --clay: #7A4B33; --line: #C9C2B4; --danger: #A3402E; --ok: #4B6B4E; }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: var(--bg); color: var(--ink); font-family: 'Work Sans', sans-serif; }
  a { color: inherit; }
  .wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 6vh 6vw; }
  .card { background: var(--panel); border: 1px solid var(--line); border-radius: 14px; padding: 40px; width: 100%; max-width: 560px; }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: 26px; margin: 0 0 8px; }
  .intro { font-size: 14px; color: var(--ink-soft); margin: 0 0 28px; }
  label { display: block; font-size: 14px; margin-bottom: 10px; }
  .field { margin-bottom: 22px; }
  input[type="text"], textarea { width: 100%; padding: 10px 12px; border: 1px solid var(--line); border-radius: 8px; background: #fff; font-size: 14px; font-family: inherit; }
  textarea { resize: vertical; min-height: 90px; }
  .rating { display: flex; gap: 8px; }
  .rating label { display: flex; align-items: center; justify-content: center; width: 42px; height: 42px; border: 1px solid var(--line); border-radius: 8px; background: #fff; cursor: pointer; font-size: 15px; margin: 0; }
  .rating input { position: absolute; opacity: 0; width: 0; height: 0; }
  .rating label:has(input:checked) { background: var(--clay); color: #fff; border-color: var(--clay); }
  .honeypot { position: absolute; left: -9999px; }
  button { padding: 12px 24px; border-radius: 8px; border: 1px solid var(--clay); background: var(--clay); color: #fff; font-size: 14px; font-family: inherit; cursor: pointer; }
  button:hover { opacity: 0.92; }
  .thanks { font-size: 15px; color: var(--ok); }
</style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <h1>Votre avis compte</h1>

    @if (session('status') === 'avis-envoye')
      <p class="thanks">Merci, votre avis a bien été envoyé !</p>
    @else
      <p class="intro">Quelques questions rapides sur votre expérience sur le site — chaque réponse est facultative.</p>

      @if ($questions->isEmpty())
        <p style="font-size:14px;color:var(--ink-soft);">Aucune question n'est configurée pour l'instant.</p>
      @else
        <form method="POST" action="{{ route('public.avis.store') }}">
          @csrf
          <input type="text" name="website" class="honeypot" tabindex="-1" autocomplete="off">
          <input type="hidden" name="form_started_at" value="{{ time() }}">

          @foreach ($questions as $question)
            <div class="field">
              <label>{{ $question->question }}</label>
              @if ($question->type === 'rating')
                <div class="rating">
                  @for ($i = 1; $i <= 5; $i++)
                    <label>
                      <input type="radio" name="answers[{{ $question->id }}]" value="{{ $i }}">
                      <span>{{ $i }}</span>
                    </label>
                  @endfor
                </div>
              @else
                <textarea name="answers[{{ $question->id }}]"></textarea>
              @endif
            </div>
          @endforeach

          <button type="submit">Envoyer mon avis</button>
        </form>
      @endif
    @endif
  </div>
</div>
</body>
</html>
