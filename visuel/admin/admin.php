<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tableau de bord — Focale Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #E7E3DC;
    --panel: #FBFAF7;
    --sidebar: #EFEDE7;
    --ink: #1E1C19;
    --ink-soft: #5C574E;
    --clay: #7A4B33;
    --line: #C9C2B4;
    --img-fallback: #D8D3C7;
    --ok: #4B6B4E;
  }

  * { box-sizing: border-box; }

  html, body {
    margin: 0;
    padding: 0;
    background: var(--bg);
    color: var(--ink);
    font-family: 'Work Sans', sans-serif;
  }

  a { color: inherit; text-decoration: none; }

  .shell {
    display: flex;
    min-height: 100vh;
  }

  /* Sidebar */
  .sidebar {
    width: 232px;
    flex-shrink: 0;
    background: var(--sidebar);
    border-right: 1px solid var(--line);
    display: flex;
    flex-direction: column;
    padding: 26px 18px;
  }

  .sidebar .wordmark {
    font-family: 'Fraunces', serif;
    font-weight: 500;
    font-size: 19px;
    padding: 0 10px;
    margin-bottom: 32px;
  }

  .sidebar nav {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .sidebar nav a {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 10px 12px;
    border-radius: 8px;
    font-size: 14px;
    color: var(--ink-soft);
  }

  .sidebar nav a svg {
    width: 17px;
    height: 17px;
    flex-shrink: 0;
    stroke: currentColor;
    fill: none;
    stroke-width: 1.6;
  }

  .sidebar nav a:hover {
    background: rgba(0,0,0,0.04);
    color: var(--ink);
  }

  .sidebar nav a.active {
    background: var(--panel);
    color: var(--ink);
    font-weight: 500;
    border: 1px solid var(--line);
  }

  .sidebar-footer {
    margin-top: auto;
    padding-top: 20px;
    border-top: 1px solid var(--line);
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
  }

  .avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: var(--clay);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 500;
    flex-shrink: 0;
  }

  .sidebar-footer .user-name { font-weight: 500; }
  .sidebar-footer .view-site { color: var(--ink-soft); font-size: 12px; }

  /* Main */
  .main {
    flex: 1;
    min-width: 0;
    padding: 34px 44px 60px;
  }

  .topbar {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 34px;
  }

  .topbar h1 {
    font-family: 'Fraunces', serif;
    font-weight: 500;
    font-size: 27px;
    margin: 0 0 6px;
  }

  .topbar .date {
    font-size: 13px;
    color: var(--ink-soft);
  }

  .status-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    background: var(--panel);
    border: 1px solid var(--line);
    border-radius: 999px;
    font-size: 13px;
  }

  .status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--ok);
  }

  /* Stat cards */
  .stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 36px;
  }

  .stat-card {
    background: var(--panel);
    border: 1px solid var(--line);
    border-radius: 12px;
    padding: 20px 22px;
  }

  .stat-card .value {
    font-family: 'Fraunces', serif;
    font-weight: 500;
    font-size: 30px;
    display: block;
    margin-bottom: 4px;
  }

  .stat-card .label {
    font-size: 13px;
    color: var(--ink-soft);
  }

  /* Two-column content */
  .content-grid {
    display: grid;
    grid-template-columns: 1.3fr 1fr;
    gap: 24px;
    margin-bottom: 36px;
  }

  .panel {
    background: var(--panel);
    border: 1px solid var(--line);
    border-radius: 12px;
    padding: 22px 24px;
  }

  .panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
  }

  .panel-header h2 {
    font-family: 'Work Sans', sans-serif;
    font-weight: 500;
    font-size: 15px;
    margin: 0;
  }

  .panel-header a {
    font-size: 13px;
    color: var(--ink-soft);
    text-decoration: underline;
  }

  .project-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 11px 0;
    border-bottom: 1px solid var(--line);
  }

  .project-row:last-child { border-bottom: none; }

  .project-thumb {
    width: 44px;
    height: 44px;
    border-radius: 6px;
    object-fit: cover;
    flex-shrink: 0;
    background: var(--img-fallback);
  }

  .project-info { flex: 1; min-width: 0; }
  .project-info .name { font-size: 14px; font-weight: 500; }
  .project-info .meta { font-size: 12px; color: var(--ink-soft); }

  .pill {
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 999px;
    border: 1px solid var(--line);
    color: var(--ink-soft);
    flex-shrink: 0;
  }

  .pill.draft { border-color: #C9A24A; color: #8A6A1F; }
  .pill.published { border-color: var(--ok); color: var(--ok); }

  .media-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
  }

  .media-grid img {
    width: 100%;
    aspect-ratio: 1 / 1;
    object-fit: cover;
    border-radius: 6px;
    background: var(--img-fallback);
  }

  /* Shortcuts */
  .shortcuts {
    display: flex;
    gap: 14px;
    margin-bottom: 36px;
  }

  .shortcut-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 20px;
    background: var(--panel);
    border: 1px solid var(--line);
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
  }

  .shortcut-btn:hover { border-color: var(--clay); }

  .shortcut-btn.primary {
    background: var(--clay);
    color: #fff;
    border-color: var(--clay);
  }

  .shortcut-btn svg {
    width: 16px;
    height: 16px;
    stroke: currentColor;
    fill: none;
    stroke-width: 1.8;
  }

  /* Disk usage */
  .disk-panel { max-width: 420px; }

  .disk-bar {
    height: 6px;
    background: var(--line);
    border-radius: 999px;
    overflow: hidden;
    margin: 12px 0 8px;
  }

  .disk-bar-fill {
    height: 100%;
    width: 34%;
    background: var(--clay);
  }

  .disk-label {
    font-size: 12px;
    color: var(--ink-soft);
    display: flex;
    justify-content: space-between;
  }

  @media (max-width: 960px) {
    .stats { grid-template-columns: 1fr; }
    .content-grid { grid-template-columns: 1fr; }
    .media-grid { grid-template-columns: repeat(4, 1fr); }
  }

  @media (max-width: 720px) {
    .sidebar { display: none; }
    .main { padding: 24px 20px 40px; }
    .topbar { flex-direction: column; gap: 14px; }
    .shortcuts { flex-direction: column; }
  }
</style>
</head>
<body>

<div class="shell">
  <aside class="sidebar">
    <span class="wordmark">Focale</span>

    <nav>
      <a href="#" class="active">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg>
        Tableau de bord
      </a>
      <a href="#">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="9" r="1.5"></circle><path d="M21 15l-5-5-9 9"></path></svg>
        Médiathèque
      </a>
      <a href="focale-admin-projects.html">
        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="14" rx="2"></rect><path d="M3 9h18"></path></svg>
        Projets
      </a>
      <a href="#">
        <svg viewBox="0 0 24 24"><path d="M6 2h9l5 5v15H6z"></path><path d="M15 2v5h5"></path></svg>
        Pages
      </a>
      <a href="focale-admin-comments.html">
        <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path></svg>
        Commentaires
      </a>
      <a href="#">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.7 1.7 0 00.34 1.87l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.7 1.7 0 00-1.87-.34 1.7 1.7 0 00-1 1.55V21a2 2 0 11-4 0v-.09a1.7 1.7 0 00-1-1.55 1.7 1.7 0 00-1.87.34l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.7 1.7 0 00.34-1.87 1.7 1.7 0 00-1.55-1H3a2 2 0 110-4h.09a1.7 1.7 0 001.55-1 1.7 1.7 0 00-.34-1.87l-.06-.06a2 2 0 112.83-2.83l.06.06a1.7 1.7 0 001.87.34H9a1.7 1.7 0 001-1.55V3a2 2 0 114 0v.09a1.7 1.7 0 001 1.55 1.7 1.7 0 001.87-.34l.06-.06a2 2 0 112.83 2.83l-.06.06a1.7 1.7 0 00-.34 1.87V9a1.7 1.7 0 001.55 1H21a2 2 0 110 4h-.09a1.7 1.7 0 00-1.55 1z"></path></svg>
        Réglages
      </a>
    </nav>

    <div class="sidebar-footer">
      <span class="avatar">J</span>
      <div>
        <div class="user-name">Jonathan</div>
        <a href="#" class="view-site">Voir le site ↗</a>
      </div>
    </div>
  </aside>

  <main class="main">
    <div class="topbar">
      <div>
        <h1>Bonjour, Jonathan</h1>
        <p class="date">Samedi 5 septembre 2026</p>
      </div>
      <div class="status-badge">
        <span class="status-dot"></span>
        Site public
      </div>
    </div>

    <div class="stats">
      <div class="stat-card">
        <span class="value">248</span>
        <span class="label">Œuvres importées</span>
      </div>
      <div class="stat-card">
        <span class="value">12</span>
        <span class="label">Projets</span>
      </div>
      <div class="stat-card">
        <span class="value">3</span>
        <span class="label">Brouillons à finaliser</span>
      </div>
    </div>

    <div class="content-grid">
      <div class="panel">
        <div class="panel-header">
          <h2>Derniers projets modifiés</h2>
          <a href="#">Voir tout</a>
        </div>

        <div class="project-row">
          <img class="project-thumb" src="https://picsum.photos/id/1011/100/100" alt="">
          <div class="project-info">
            <div class="name">Claire &amp; Antoine</div>
            <div class="meta">Modifié il y a 2 heures</div>
          </div>
          <span class="pill published">Publié</span>
        </div>
        <div class="project-row">
          <img class="project-thumb" src="https://picsum.photos/id/1015/100/100" alt="">
          <div class="project-info">
            <div class="name">Basses eaux</div>
            <div class="meta">Modifié hier</div>
          </div>
          <span class="pill draft">Brouillon</span>
        </div>
        <div class="project-row">
          <img class="project-thumb" src="https://picsum.photos/id/1024/100/100" alt="">
          <div class="project-info">
            <div class="name">Portrait de studio, Léa</div>
            <div class="meta">Modifié il y a 3 jours</div>
          </div>
          <span class="pill published">Publié</span>
        </div>
        <div class="project-row">
          <img class="project-thumb" src="https://picsum.photos/id/1039/100/100" alt="">
          <div class="project-info">
            <div class="name">Séance grossesse, Marine</div>
            <div class="meta">Modifié il y a 5 jours</div>
          </div>
          <span class="pill draft">Brouillon</span>
        </div>
      </div>

      <div class="panel">
        <div class="panel-header">
          <h2>Dernières œuvres importées</h2>
          <a href="#">Voir tout</a>
        </div>
        <div class="media-grid">
          <img src="https://picsum.photos/id/1011/200/200" alt="">
          <img src="https://picsum.photos/id/1015/200/200" alt="">
          <img src="https://picsum.photos/id/1016/200/200" alt="">
          <img src="https://picsum.photos/id/1018/200/200" alt="">
          <img src="https://picsum.photos/id/1019/200/200" alt="">
          <img src="https://picsum.photos/id/1024/200/200" alt="">
          <img src="https://picsum.photos/id/1025/200/200" alt="">
          <img src="https://picsum.photos/id/1035/200/200" alt="">
        </div>
      </div>
    </div>

    <div class="shortcuts">
      <button class="shortcut-btn primary">
        <svg viewBox="0 0 24 24"><path d="M12 3v14"></path><path d="M5 10l7-7 7 7"></path><path d="M5 21h14"></path></svg>
        Importer des œuvres
      </button>
      <button class="shortcut-btn">
        <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></svg>
        Créer un projet
      </button>
    </div>

    <div class="panel disk-panel">
      <div class="panel-header">
        <h2>Espace disque</h2>
      </div>
      <div class="disk-bar"><div class="disk-bar-fill"></div></div>
      <div class="disk-label">
        <span>17,2 Go utilisés</span>
        <span>50 Go</span>
      </div>
    </div>
  </main>
</div>

</body>
</html>