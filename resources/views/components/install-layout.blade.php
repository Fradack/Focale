@props(['step' => 1, 'title' => ''])
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>Installation — Focale</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #E7E3DC; --panel: #FBFAF7; --ink: #1E1C19; --ink-soft: #5C574E;
    --clay: #7A4B33; --line: #C9C2B4; --ok: #4B6B4E; --danger: #A3402E;
  }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: var(--bg); color: var(--ink); font-family: 'Work Sans', sans-serif; }
  body { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 6vh 6vw; }
  a { color: inherit; }
  .card { width: 100%; max-width: 480px; background: var(--panel); border: 1px solid var(--line); border-radius: 16px; padding: 40px; }
  .wordmark { font-family: 'Fraunces', serif; font-weight: 500; font-size: 20px; display: block; text-align: center; margin-bottom: 8px; }
  .steps { display: flex; justify-content: center; gap: 6px; margin-bottom: 28px; }
  .steps span { width: 26px; height: 3px; border-radius: 999px; background: var(--line); }
  .steps span.done, .steps span.active { background: var(--clay); }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: 24px; margin: 0 0 8px; text-align: center; }
  .subtitle { font-size: 13px; color: var(--ink-soft); text-align: center; margin: 0 0 28px; }
  .field { margin-bottom: 14px; }
  .field label { display: block; font-size: 12px; color: var(--ink-soft); margin-bottom: 5px; }
  .field input, .field select { width: 100%; padding: 11px 13px; border: 1px solid var(--line); border-radius: 6px; background: #fff; color: var(--ink); font-family: 'Work Sans', sans-serif; font-size: 14px; }
  .field-row { display: flex; gap: 10px; }
  .field-row .field { flex: 1; }
  .error { font-size: 12px; color: var(--danger); margin: 4px 0 0; }
  .field-hint { font-size: 11px; color: var(--ink-soft); margin: 4px 0 0; }

  .password-wrap { position: relative; }
  .password-wrap input { padding-right: 42px; }
  .password-toggle {
    position: absolute; top: 50%; right: 4px; transform: translateY(-50%);
    width: 32px; height: 32px; border: none; background: none; border-radius: 999px;
    display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--ink-soft);
  }
  .password-toggle:hover { background: rgba(0,0,0,0.05); color: var(--ink); }
  .password-toggle svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 1.6; }
  .password-toggle .icon-off { display: none; }
  .password-toggle.is-visible .icon-on { display: none; }
  .password-toggle.is-visible .icon-off { display: block; }
  .btn { width: 100%; padding: 13px 20px; background: var(--clay); border: none; border-radius: 8px; color: #fff; font-family: 'Work Sans', sans-serif; font-size: 15px; font-weight: 500; cursor: pointer; margin-top: 8px; }
  .btn:hover { opacity: 0.92; }
  .btn.secondary { background: none; border: 1px solid var(--line); color: var(--ink); }
  .notice { font-size: 13px; color: var(--ink-soft); background: #fff; border: 1px solid var(--line); border-radius: 8px; padding: 12px 14px; margin: 0 0 20px; }
</style>
</head>
<body>
  <div class="card">
    <span class="wordmark">Focale</span>
    <div class="steps">
      @for ($i = 1; $i <= 4; $i++)
        <span class="{{ $i < $step ? 'done' : ($i === $step ? 'active' : '') }}"></span>
      @endfor
    </div>
    <h1>{{ $title }}</h1>
    {{ $slot }}
  </div>

<script>
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.password-toggle');
    if (!btn) return;
    const input = document.getElementById(btn.dataset.for);
    if (!input) return;
    const visible = input.type === 'text';
    input.type = visible ? 'password' : 'text';
    btn.classList.toggle('is-visible', !visible);
    btn.setAttribute('aria-label', visible ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
  });
</script>
</body>
</html>
