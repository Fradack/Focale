<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Projets — Focale Admin</title>
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
    --warn: #8A6A1F;
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

  .topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 26px;
    gap: 20px;
    flex-wrap: wrap;
  }

  .topbar h1 {
    font-family: 'Fraunces', serif;
    font-weight: 500;
    font-size: 27px;
    margin: 0;
  }

  .new-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: var(--clay);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
  }

  .new-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; }

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

  .table-panel {
    background: var(--panel);
    border: 1px solid var(--line);
    border-radius: 12px;
    overflow: hidden;
  }

  table { width: 100%; border-collapse: collapse; }

  thead th {
    text-align: left;
    font-size: 12px;
    font-weight: 500;
    color: var(--ink-soft);
    padding: 14px 20px;
    border-bottom: 1px solid var(--line);
  }

  tbody tr { border-bottom: 1px solid var(--line); }
  tbody tr:last-child { border-bottom: none; }
  tbody tr:hover { background: rgba(0,0,0,0.02); }

  td { padding: 14px 20px; font-size: 14px; vertical-align: middle; }

  .project-cell { display: flex; align-items: center; gap: 12px; }

  .project-cell img {
    width: 48px; height: 48px;
    border-radius: 6px;
    object-fit: cover;
    background: var(--img-fallback);
    flex-shrink: 0;
  }

  .project-cell .name { font-weight: 500; }
  .project-cell .slug { font-size: 12px; color: var(--ink-soft); }

  .pill {
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 999px;
    border: 1px solid var(--line);
    color: var(--ink-soft);
    display: inline-block;
  }

  .pill.draft { border-color: var(--warn); color: var(--warn); }
  .pill.published { border-color: var(--ok); color: var(--ok); }
  .pill.archived { border-color: var(--ink-soft); color: var(--ink-soft); }
  .pill.unlisted { border-color: #7A6B4B; color: #7A6B4B; }

  .visibility {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--ink-soft);
  }

  .visibility svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 1.6; }

  .row-actions {
    display: flex;
    gap: 14px;
    font-size: 13px;
    color: var(--ink-soft);
  }

  .row-actions a:hover { color: var(--ink); text-decoration: underline; }

  @media (max-width: 900px) {
    .sidebar { display: none; }
    .main { padding: 24px 20px 40px; }
    thead { display: none; }
    table, tbody, tr, td { display: block; width: 100%; }
    tbody tr { padding: 14px 4px; }
    td { padding: 4px 16px; border: none; }
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
      <a href="#" class="active">
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
      <h1>Projets</h1>
      <button class="new-btn">
        <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></svg>
        Nouveau projet
      </button>
    </div>

    <div class="toolbar">
      <input type="search" placeholder="Rechercher un projet…">
      <button class="filter-chip active">Tous (12)</button>
      <button class="filter-chip">Publiés (8)</button>
      <button class="filter-chip">Brouillons (3)</button>
      <button class="filter-chip">Archivés (1)</button>
    </div>

    <div class="table-panel">
      <table>
        <thead>
          <tr>
            <th>Projet</th>
            <th>Statut</th>
            <th>Visibilité</th>
            <th>Œuvres</th>
            <th>Modifié</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <div class="project-cell">
                <img src="https://picsum.photos/id/1011/100/100" alt="">
                <div>
                  <div class="name">Claire &amp; Antoine</div>
                  <div class="slug">/projets/claire-et-antoine</div>
                </div>
              </div>
            </td>
            <td><span class="pill published">Publié</span></td>
            <td>
              <span class="visibility">
                <svg viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="10" rx="2"></rect><path d="M8 10V7a4 4 0 018 0v3"></path></svg>
                Mot de passe
              </span>
            </td>
            <td>18</td>
            <td>Il y a 2 heures</td>
            <td>
              <div class="row-actions">
                <a href="#">Modifier</a>
                <a href="#">Dupliquer</a>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="project-cell">
                <img src="https://picsum.photos/id/1015/100/100" alt="">
                <div>
                  <div class="name">Basses eaux</div>
                  <div class="slug">/projets/basses-eaux</div>
                </div>
              </div>
            </td>
            <td><span class="pill draft">Brouillon</span></td>
            <td>
              <span class="visibility">
                <svg viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                Publique
              </span>
            </td>
            <td>12</td>
            <td>Hier</td>
            <td>
              <div class="row-actions">
                <a href="#">Modifier</a>
                <a href="#">Dupliquer</a>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="project-cell">
                <img src="https://picsum.photos/id/1024/100/100" alt="">
                <div>
                  <div class="name">Portrait de studio, Léa</div>
                  <div class="slug">/projets/portrait-lea</div>
                </div>
              </div>
            </td>
            <td><span class="pill published">Publié</span></td>
            <td>
              <span class="visibility">
                <svg viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                Publique
              </span>
            </td>
            <td>24</td>
            <td>Il y a 3 jours</td>
            <td>
              <div class="row-actions">
                <a href="#">Modifier</a>
                <a href="#">Dupliquer</a>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="project-cell">
                <img src="https://picsum.photos/id/1039/100/100" alt="">
                <div>
                  <div class="name">Séance grossesse, Marine</div>
                  <div class="slug">/projets/grossesse-marine</div>
                </div>
              </div>
            </td>
            <td><span class="pill draft">Brouillon</span></td>
            <td>
              <span class="visibility">
                <svg viewBox="0 0 24 24"><path d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-10-8-10-8a18.5 18.5 0 015.06-5.94M9.9 4.24A10.6 10.6 0 0112 4c7 0 10 8 10 8a18.6 18.6 0 01-2.16 3.19M14.12 14.12a3 3 0 11-4.24-4.24"></path><path d="M1 1l22 22"></path></svg>
                Privée
              </span>
            </td>
            <td>9</td>
            <td>Il y a 5 jours</td>
            <td>
              <div class="row-actions">
                <a href="#">Modifier</a>
                <a href="#">Dupliquer</a>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="project-cell">
                <img src="https://picsum.photos/id/1043/100/100" alt="">
                <div>
                  <div class="name">Vignes en lumière</div>
                  <div class="slug">/projets/vignes-en-lumiere</div>
                </div>
              </div>
            </td>
            <td><span class="pill archived">Archivé</span></td>
            <td>
              <span class="visibility">
                <svg viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                Publique
              </span>
            </td>
            <td>31</td>
            <td>Il y a 2 mois</td>
            <td>
              <div class="row-actions">
                <a href="#">Modifier</a>
                <a href="#">Dupliquer</a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>
</div>

</body>
</html>