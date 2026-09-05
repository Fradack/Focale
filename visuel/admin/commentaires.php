<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Commentaires — Focale Admin</title>
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
    --ok: #4B6B4E;
    --warn: #8A6A1F;
    --danger: #A3402E;
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

  .shell { display: flex; min-height: 100vh; }

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

  .sidebar nav { display: flex; flex-direction: column; gap: 2px; }

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
    width: 17px; height: 17px; flex-shrink: 0;
    stroke: currentColor; fill: none; stroke-width: 1.6;
  }

  .sidebar nav a:hover { background: rgba(0,0,0,0.04); color: var(--ink); }

  .sidebar nav a.active {
    background: var(--panel);
    color: var(--ink);
    font-weight: 500;
    border: 1px solid var(--line);
  }

  .sidebar nav a .count {
    margin-left: auto;
    font-size: 11px;
    background: var(--clay);
    color: #fff;
    border-radius: 999px;
    padding: 1px 7px;
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
    width: 30px; height: 30px; border-radius: 50%;
    background: var(--clay); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 500; flex-shrink: 0;
  }

  .sidebar-footer .user-name { font-weight: 500; }
  .sidebar-footer .view-site { color: var(--ink-soft); font-size: 12px; }

  .main { flex: 1; min-width: 0; padding: 34px 44px 60px; }

  .topbar { margin-bottom: 8px; }

  .topbar h1 {
    font-family: 'Fraunces', serif;
    font-weight: 500;
    font-size: 27px;
    margin: 0 0 6px;
  }

  .topbar p {
    font-size: 13px;
    color: var(--ink-soft);
    margin: 0 0 26px;
    max-width: 60ch;
  }

  .toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
  }

  .toolbar input[type="search"] {
    flex: 1;
    min-width: 200px;
    padding: 10px 14px;
    border: 1px solid var(--line);
    border-radius: 8px;
    background: var(--panel);
    font-family: 'Work Sans', sans-serif;
    font-size: 14px;
  }

  .filter-chip {
    padding: 8px 14px;
    border: 1px solid var(--line);
    border-radius: 999px;
    background: var(--panel);
    font-size: 13px;
    color: var(--ink-soft);
    cursor: pointer;
  }

  .filter-chip.active {
    background: var(--ink);
    border-color: var(--ink);
    color: #fff;
  }

  .comment-list { display: flex; flex-direction: column; gap: 10px; }

  .comment-row {
    background: var(--panel);
    border: 1px solid var(--line);
    border-radius: 12px;
    padding: 18px 20px;
    display: flex;
    gap: 16px;
    align-items: flex-start;
  }

  .comment-row.pending { border-left: 3px solid var(--warn); }

  .comment-body { flex: 1; min-width: 0; }

  .comment-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
    flex-wrap: wrap;
  }

  .comment-meta .author { font-weight: 500; font-size: 14px; }
  .comment-meta .date { font-size: 12px; color: var(--ink-soft); }

  .album-tag {
    font-size: 11px;
    padding: 2px 9px;
    border-radius: 999px;
    border: 1px solid var(--line);
    color: var(--ink-soft);
  }

  .status-tag {
    font-size: 11px;
    padding: 2px 9px;
    border-radius: 999px;
  }

  .status-tag.pending { background: rgba(138,106,31,0.12); color: var(--warn); }
  .status-tag.approved { background: rgba(75,107,78,0.12); color: var(--ok); }

  .comment-text {
    font-size: 14px;
    line-height: 1.6;
    color: var(--ink);
    margin: 0;
  }

  .comment-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex-shrink: 0;
  }

  .action-btn {
    padding: 7px 14px;
    border-radius: 6px;
    border: 1px solid var(--line);
    background: #fff;
    font-size: 12px;
    font-family: 'Work Sans', sans-serif;
    cursor: pointer;
    white-space: nowrap;
  }

  .action-btn.approve { color: var(--ok); border-color: var(--ok); }
  .action-btn.approve:hover { background: rgba(75,107,78,0.08); }

  .action-btn.delete { color: var(--danger); border-color: var(--danger); }
  .action-btn.delete:hover { background: rgba(163,64,46,0.08); }

  .action-btn.disabled {
    color: var(--ink-soft);
    border-color: var(--line);
    cursor: default;
  }

  @media (max-width: 900px) {
    .sidebar { display: none; }
    .main { padding: 24px 20px 40px; }
    .comment-row { flex-direction: column; }
    .comment-actions { flex-direction: row; }
  }
</style>
</head>
<body>

<div class="shell">
  <aside class="sidebar">
    <span class="wordmark">Focale</span>
    <nav>
      <a href="focale-admin-dashboard.html">
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
      <a href="#" class="active">
        <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path></svg>
        Commentaires
        <span class="count">2</span>
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
      <h1>Commentaires</h1>
      <p>Fonctionnalité hors du périmètre initial de Focale (cœur produit sans commentaires publics) — ajoutée ici en extension optionnelle, désactivable par album depuis ses réglages.</p>
    </div>

    <div class="toolbar">
      <input type="search" placeholder="Rechercher un commentaire ou un auteur…">
      <button class="filter-chip active">Tous (5)</button>
      <button class="filter-chip">En attente (2)</button>
      <button class="filter-chip">Approuvés (3)</button>
    </div>

    <div class="comment-list">
      <div class="comment-row pending">
        <div class="comment-body">
          <div class="comment-meta">
            <span class="author">Camille</span>
            <span class="date">— il y a 20 minutes</span>
            <span class="album-tag">Claire &amp; Antoine</span>
            <span class="status-tag pending">En attente</span>
          </div>
          <p class="comment-text">Superbe reportage, on dirait un film ! Merci pour votre talent.</p>
        </div>
        <div class="comment-actions">
          <button class="action-btn approve">Approuver</button>
          <button class="action-btn delete">Supprimer</button>
        </div>
      </div>

      <div class="comment-row pending">
        <div class="comment-body">
          <div class="comment-meta">
            <span class="author">Visiteur anonyme</span>
            <span class="date">— il y a 2 heures</span>
            <span class="album-tag">Claire &amp; Antoine</span>
            <span class="status-tag pending">En attente</span>
          </div>
          <p class="comment-text">Regardez mon profil pour des offres exceptionnelles sur vos prochains tirages !</p>
        </div>
        <div class="comment-actions">
          <button class="action-btn approve">Approuver</button>
          <button class="action-btn delete">Supprimer</button>
        </div>
      </div>

      <div class="comment-row">
        <div class="comment-body">
          <div class="comment-meta">
            <span class="author">Léa</span>
            <span class="date">— 15 septembre 2026</span>
            <span class="album-tag">Claire &amp; Antoine</span>
            <span class="status-tag approved">Approuvé</span>
          </div>
          <p class="comment-text">Quelle belle journée, merci pour ces photos magnifiques ! On revit chaque instant.</p>
        </div>
        <div class="comment-actions">
          <button class="action-btn disabled">Approuvé</button>
          <button class="action-btn delete">Supprimer</button>
        </div>
      </div>

      <div class="comment-row">
        <div class="comment-body">
          <div class="comment-meta">
            <span class="author">Marc</span>
            <span class="date">— 15 septembre 2026</span>
            <span class="album-tag">Claire &amp; Antoine</span>
            <span class="status-tag approved">Approuvé</span>
          </div>
          <p class="comment-text">La lumière du soir est incroyable, bravo pour le travail.</p>
        </div>
        <div class="comment-actions">
          <button class="action-btn disabled">Approuvé</button>
          <button class="action-btn delete">Supprimer</button>
        </div>
      </div>

      <div class="comment-row">
        <div class="comment-body">
          <div class="comment-meta">
            <span class="author">Sophie</span>
            <span class="date">— 16 septembre 2026</span>
            <span class="album-tag">Claire &amp; Antoine</span>
            <span class="status-tag approved">Approuvé</span>
          </div>
          <p class="comment-text">On garde toutes ces images précieusement. Merci !</p>
        </div>
        <div class="comment-actions">
          <button class="action-btn disabled">Approuvé</button>
          <button class="action-btn delete">Supprimer</button>
        </div>
      </div>
    </div>
  </main>
</div>

</body>
</html>