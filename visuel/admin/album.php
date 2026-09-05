<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
<title>Claire &amp; Antoine — Éditer le projet — Focale Admin</title>
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
    margin: 0; padding: 0;
    background: #E7E3DC;
    color: #1E1C19;
    font-family: 'Work Sans', sans-serif;
  }

  a { color: inherit; text-decoration: none; }

  .shell { display: flex; min-height: 100vh; }

  .sidebar {
    width: 232px; flex-shrink: 0;
    background: #EFEDE7;
    border-right: 1px solid #C9C2B4;
    display: flex; flex-direction: column;
    padding: 26px 18px;
  }

  .sidebar .wordmark {
    font-family: 'Fraunces', serif; font-weight: 500; font-size: 19px;
    padding: 0 10px; margin-bottom: 32px;
  }

  .sidebar nav { display: flex; flex-direction: column; gap: 2px; }

  .sidebar nav a {
    display: flex; align-items: center; gap: 11px;
    padding: 10px 12px; border-radius: 8px; font-size: 14px; color: #5C574E;
  }

  .sidebar nav a svg { width: 17px; height: 17px; flex-shrink: 0; stroke: currentColor; fill: none; stroke-width: 1.6; }
  .sidebar nav a:hover { background: rgba(0,0,0,0.04); color: #1E1C19; }
  .sidebar nav a.active { background: #FBFAF7; color: #1E1C19; font-weight: 500; border: 1px solid #C9C2B4; }

  .sidebar-footer {
    margin-top: auto; padding-top: 20px; border-top: 1px solid #C9C2B4;
    display: flex; align-items: center; gap: 10px; font-size: 13px;
  }

  .avatar {
    width: 30px; height: 30px; border-radius: 50%; background: #7A4B33; color: #fff;
    display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 500; flex-shrink: 0;
  }

  .sidebar-footer .user-name { font-weight: 500; }
  .sidebar-footer .view-site { color: #5C574E; font-size: 12px; }

  .main { flex: 1; min-width: 0; padding: 28px 44px 60px; }

  .back-link {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 13px; color: #5C574E; margin-bottom: 18px;
  }
  .back-link:hover { color: #1E1C19; }

  .editor-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    gap: 20px; margin-bottom: 28px; flex-wrap: wrap;
  }

  .title-block { flex: 1; min-width: 260px; }

  .title-input {
    font-family: 'Fraunces', serif; font-weight: 500; font-size: 30px;
    border: none; background: none; color: #1E1C19; -webkit-text-fill-color: #1E1C19;
    width: 100%; padding: 4px 0; border-bottom: 1px solid transparent; text-decoration: none;
  }
  .title-input:focus { outline: none; border-bottom: 1px solid #C9C2B4; }

  .slug-row { display: flex; align-items: center; gap: 8px; margin-top: 6px; font-size: 13px; color: #5C574E; }
  .slug-row input {
    border: none; background: none; color: #5C574E; -webkit-text-fill-color: currentColor;
    font-family: 'Work Sans', sans-serif; font-size: 13px;
    border-bottom: 1px dashed #C9C2B4; padding: 2px 0; width: 200px; text-decoration: none;
  }
  .slug-row input:focus { outline: none; color: #1E1C19; }

  .header-meta {
    display: flex; align-items: center; gap: 10px; margin-top: 14px; flex-wrap: wrap;
  }

  select.status-select, select.visibility-select {
    font-family: 'Work Sans', sans-serif;
    font-size: 13px;
    padding: 7px 12px;
    border: 1px solid #C9C2B4;
    border-radius: 999px;
    background: #FBFAF7;
    color: #1E1C19;
  }

  .password-field {
    margin-top: 16px;
    padding: 14px 16px;
    background: #FBFAF7;
    border: 1px solid #C9C2B4;
    border-radius: 10px;
    max-width: 420px;
    display: none;
  }

  .password-field.visible { display: block; }

  .password-field label {
    display: block;
    font-size: 12px;
    color: #5C574E;
    margin-bottom: 6px;
  }

  .password-row { display: flex; gap: 8px; }

  .password-row input {
    flex: 1;
    padding: 9px 11px;
    border: 1px solid #C9C2B4;
    border-radius: 6px;
    background: #fff;
    color: #1E1C19;
    -webkit-text-fill-color: #1E1C19;
    font-family: 'Work Sans', sans-serif;
    font-size: 13px;
    text-decoration: none;
  }

  .password-hint {
    font-size: 11px;
    color: #5C574E;
    margin: 8px 0 0;
  }

  .header-actions { display: flex; gap: 10px; flex-shrink: 0; }

  .btn {
    padding: 10px 18px; border-radius: 8px; font-size: 13px; font-family: 'Work Sans', sans-serif;
    cursor: pointer; border: 1px solid #C9C2B4; background: #FBFAF7; color: #1E1C19;
  }
  .btn:hover { border-color: #7A4B33; }
  .btn.primary { background: #7A4B33; border-color: #7A4B33; color: #fff; }
  .btn.primary:hover { opacity: 0.92; }

  .layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 28px;
    align-items: start;
  }

  .panel {
    background: #FBFAF7;
    border: 1px solid #C9C2B4;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
  }

  .panel h2 {
    font-family: 'Work Sans', sans-serif; font-weight: 500; font-size: 13px;
    margin: 0 0 14px; text-transform: uppercase; letter-spacing: 0.03em; color: #5C574E;
  }

  .seo-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
  }

  .seo-panel-header h2 { text-transform: uppercase; letter-spacing: 0.03em; color: #5C574E; }

  .switch { position: relative; display: inline-block; cursor: pointer; }
  .switch input { position: absolute; opacity: 0; width: 0; height: 0; }

  .switch-track {
    display: block;
    width: 36px;
    height: 20px;
    background: #C9C2B4;
    border-radius: 999px;
    position: relative;
    transition: background 0.15s;
  }

  .switch-thumb {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #FBFAF7;
    transition: transform 0.15s;
  }

  .switch input:checked + .switch-track { background: #7A4B33; }
  .switch input:checked + .switch-track .switch-thumb { transform: translateX(16px); }

  .seo-hint {
    font-size: 12px;
    color: #5C574E;
    margin: 0 0 14px;
    display: none;
  }

  .seo-hint.visible { display: block; }

  #seo-title-field.disabled,
  #seo-desc-field.disabled {
    opacity: 0.45;
  }

  #seo-title-field.disabled input,
  #seo-desc-field.disabled textarea {
    background: #F1EFEA;
    cursor: not-allowed;
  }

  .field { margin-bottom: 14px; }
  .field:last-child { margin-bottom: 0; }
  .field label { display: block; font-size: 12px; color: #5C574E; margin-bottom: 5px; }

  .field input, .field textarea {
    width: 100%; padding: 9px 11px; border: 1px solid #C9C2B4; border-radius: 6px;
    background: #fff; color: #1E1C19; -webkit-text-fill-color: #1E1C19;
    font-family: 'Work Sans', sans-serif; font-size: 13px;
    text-decoration: none; -webkit-appearance: none; appearance: none;
    min-width: 0; box-sizing: border-box;
  }
  .field textarea { resize: vertical; min-height: 70px; }

  .field-row { display: flex; gap: 10px; }
  .field-row .field { flex: 1; }

  /* Media grid */
  .media-header {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;
  }
  .media-header h2 { margin: 0; font-family: 'Fraunces', serif; font-weight: 500; font-size: 18px; }
  .media-count { font-size: 13px; color: #5C574E; }

  .add-media-btn {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 16px; border-radius: 8px; border: 1px dashed #7A4B33;
    background: none; color: #7A4B33; font-size: 13px; cursor: pointer; margin-bottom: 18px;
  }
  .add-media-btn svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }

  .media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
  }

  .media-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    background: #D8D3C7;
    aspect-ratio: 1 / 1;
    cursor: grab;
    border: 2px solid transparent;
  }

  .media-item.dragging { opacity: 0.4; }
  .media-item.drag-over { border-color: #7A4B33; }

  .media-item img {
    width: 100%; height: 100%; object-fit: cover; display: block; pointer-events: none;
  }

  .media-item .cover-badge {
    position: absolute; top: 8px; left: 8px;
    background: #7A4B33; color: #fff;
    font-size: 10px; padding: 3px 8px; border-radius: 999px;
    display: none;
  }
  .media-item.is-cover .cover-badge { display: block; }

  .media-item-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.55), transparent 50%);
    opacity: 0;
    transition: opacity 0.15s;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    padding: 8px;
  }
  .media-item:hover .media-item-overlay { opacity: 1; }

  .media-item-overlay button {
    background: rgba(255,255,255,0.92);
    border: none;
    border-radius: 999px;
    font-size: 10px;
    padding: 5px 9px;
    cursor: pointer;
    font-family: 'Work Sans', sans-serif;
  }

  .media-item-overlay .remove-btn {
    background: rgba(163,64,46,0.9);
    color: #fff;
    width: 22px; height: 22px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    padding: 0; font-size: 13px;
  }

  @media (max-width: 960px) {
    .layout { grid-template-columns: 1fr; }
  }

  @media (max-width: 720px) {
    .sidebar { display: none; }
    .main { padding: 20px 20px 40px; }
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
      <a href="focale-admin-import.html">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="9" r="1.5"></circle><path d="M21 15l-5-5-9 9"></path></svg>
        Médiathèque
      </a>
      <a href="focale-admin-projects.html" class="active">
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
    <a class="back-link" href="focale-admin-projects.html">← Projets</a>

    <div class="editor-header">
      <div class="title-block">
        <input class="title-input" type="text" value="Claire &amp; Antoine">
        <div class="slug-row">
          focale.fr/projets/
          <input type="text" value="claire-et-antoine">
        </div>
        <div class="header-meta">
          <select class="status-select" id="status-select">
            <option>Publié</option>
            <option>Brouillon</option>
            <option>Non répertorié</option>
            <option>Archivé</option>
          </select>
          <select class="visibility-select" id="visibility-select">
            <option>Protégé par mot de passe</option>
            <option>Publique</option>
            <option>Privée</option>
          </select>
        </div>
        <div class="password-field" id="password-field">
          <label for="album-password">Mot de passe de l'album</label>
          <div class="password-row">
            <input type="text" id="album-password" value="basseseaux2026">
            <button type="button" class="btn" id="generate-password-btn">Générer</button>
          </div>
          <p class="password-hint">Transmets ce mot de passe directement aux personnes concernées — la page n'est pas indexée par les moteurs de recherche.</p>
        </div>
      </div>
      <div class="header-actions">
        <button class="btn">Aperçu</button>
        <button class="btn">Dupliquer</button>
        <button class="btn primary">Publier</button>
      </div>
    </div>

    <div class="layout">
      <div class="side-column">
        <div class="panel">
          <h2>Informations</h2>
          <div class="field">
            <label>Texte d'introduction</label>
            <textarea>Une journée d'automne dans le Bordelais, de la préparation aux premières danses.</textarea>
          </div>
          <div class="field">
            <label>Date de création</label>
            <input type="date" value="2026-09-14">
          </div>
          <div class="field">
            <label>Date de publication</label>
            <input type="date" value="2026-09-16">
          </div>
          <div class="field">
            <label>Tags</label>
            <input type="text" value="mariage, extérieur, château">
          </div>
          <div class="field">
            <label>Crédits / collaborateurs</label>
            <input type="text" value="Fleuriste : Atelier des Lianes">
          </div>
          <div class="field">
            <label>Lien externe (optionnel)</label>
            <input type="text" placeholder="https://…">
          </div>
        </div>

        <div class="panel">
          <div class="seo-panel-header">
            <h2 style="margin:0;">SEO &amp; partage</h2>
            <label class="switch">
              <input type="checkbox" id="seo-toggle" checked>
              <span class="switch-track"><span class="switch-thumb"></span></span>
            </label>
          </div>
          <p class="seo-hint" id="seo-hint">Champs désactivés — le titre et la description seront générés automatiquement à partir du projet.</p>
          <div class="field" id="seo-title-field">
            <label>Titre SEO</label>
            <input type="text" id="seo-title" value="Mariage de Claire & Antoine — Focale">
          </div>
          <div class="field" id="seo-desc-field">
            <label>Description SEO</label>
            <textarea id="seo-desc">Reportage de mariage au Château Rauzan, en Gironde — septembre 2026.</textarea>
          </div>
          <div class="field">
            <label>Image de partage</label>
            <input type="text" value="Œuvre de couverture (par défaut)" disabled>
          </div>
        </div>
      </div>

      <div class="main-column">
        <div class="panel">
          <div class="media-header">
            <h2>Œuvres du projet</h2>
            <span class="media-count" id="media-count">12 œuvres</span>
          </div>

          <button class="add-media-btn">
            <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></svg>
            Ajouter des œuvres depuis la médiathèque
          </button>

          <div class="media-grid" id="media-grid"></div>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
  const photoIds = [1011, 1015, 1016, 1018, 1019, 1024, 1025, 1035, 1036, 1039, 1041, 1043];
  const grid = document.getElementById('media-grid');
  const countLabel = document.getElementById('media-count');
  let coverIndex = 0;

  function render() {
    grid.innerHTML = '';
    photoIds.forEach((id, i) => {
      const item = document.createElement('div');
      item.className = 'media-item' + (i === coverIndex ? ' is-cover' : '');
      item.draggable = true;
      item.dataset.index = i;
      item.innerHTML = `
        <img src="https://picsum.photos/id/${id}/300/300" alt="">
        <span class="cover-badge">Couverture</span>
        <div class="media-item-overlay">
          <button class="set-cover-btn" type="button">Couverture</button>
          <button class="remove-btn" type="button" aria-label="Retirer">✕</button>
        </div>`;
      grid.appendChild(item);
    });
    countLabel.textContent = `${photoIds.length} œuvre${photoIds.length > 1 ? 's' : ''}`;
    attachHandlers();
  }

  function attachHandlers() {
    const items = Array.from(grid.children);

    items.forEach((item) => {
      item.addEventListener('dragstart', () => item.classList.add('dragging'));
      item.addEventListener('dragend', () => {
        item.classList.remove('dragging');
        items.forEach(i => i.classList.remove('drag-over'));
      });

      item.addEventListener('dragover', (e) => {
        e.preventDefault();
        item.classList.add('drag-over');
      });
      item.addEventListener('dragleave', () => item.classList.remove('drag-over'));

      item.addEventListener('drop', (e) => {
        e.preventDefault();
        item.classList.remove('drag-over');
        const fromIndex = Number(grid.querySelector('.dragging').dataset.index);
        const toIndex = Number(item.dataset.index);
        if (fromIndex === toIndex) return;

        const wasCover = photoIds[coverIndex];
        const [moved] = photoIds.splice(fromIndex, 1);
        photoIds.splice(toIndex, 0, moved);
        coverIndex = photoIds.indexOf(wasCover);
        render();
      });

      item.querySelector('.set-cover-btn').addEventListener('click', () => {
        coverIndex = Number(item.dataset.index);
        render();
      });

      item.querySelector('.remove-btn').addEventListener('click', () => {
        const idx = Number(item.dataset.index);
        const wasCover = photoIds[coverIndex];
        photoIds.splice(idx, 1);
        coverIndex = Math.max(0, photoIds.indexOf(wasCover));
        render();
      });
    });
  }

  render();

  // SEO fields can be turned off to fall back to auto-generated values.
  const seoToggle = document.getElementById('seo-toggle');
  const seoHint = document.getElementById('seo-hint');
  const seoTitleField = document.getElementById('seo-title-field');
  const seoDescField = document.getElementById('seo-desc-field');
  const seoTitleInput = document.getElementById('seo-title');
  const seoDescInput = document.getElementById('seo-desc');

  function applySeoState() {
    const enabled = seoToggle.checked;
    seoTitleField.classList.toggle('disabled', !enabled);
    seoDescField.classList.toggle('disabled', !enabled);
    seoHint.classList.toggle('visible', !enabled);
    seoTitleInput.disabled = !enabled;
    seoDescInput.disabled = !enabled;
  }

  seoToggle.addEventListener('change', applySeoState);
  applySeoState();

  // Show the password field only when visibility is "protected".
  const visibilitySelect = document.getElementById('visibility-select');
  const passwordField = document.getElementById('password-field');
  const generateBtn = document.getElementById('generate-password-btn');
  const passwordInput = document.getElementById('album-password');

  function applyVisibilityState() {
    passwordField.classList.toggle('visible', visibilitySelect.value === 'Protégé par mot de passe');
  }

  visibilitySelect.addEventListener('change', applyVisibilityState);
  applyVisibilityState();

  generateBtn.addEventListener('click', () => {
    const words = ['marais', 'lumiere', 'vigne', 'chateau', 'brume', 'rivage', 'atelier', 'cadre'];
    const word = words[Math.floor(Math.random() * words.length)];
    const num = Math.floor(1000 + Math.random() * 9000);
    passwordInput.value = word + num;
  });
</script>

</body>
</html>