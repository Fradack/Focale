<!DOCTYPE html>
<html lang="fr"{!! \App\Support\Theme::publicHtmlAttr() !!}>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>Site indisponible — Focale</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg: #E7E3DC; --ink: #1E1C19; --ink-soft: #5C574E; --line: #C9C2B4; }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: var(--bg); color: var(--ink); height: 100%; }
  body { font-family: 'Work Sans', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 6vh 6vw; text-align: center; }
  h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: clamp(28px, 5vw, 42px); margin: 0 0 16px; }
  p { color: var(--ink-soft); font-size: 15px; max-width: 42ch; margin: 0 auto; }
</style>
@include('components.theme-vars-dark')
</head>
<body>
  <div>
    <h1>Site indisponible</h1>
    <p>Ce site n'est pas disponible dans votre pays.</p>
  </div>
</body>
</html>
