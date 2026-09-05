<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Importer des œuvres — Focale Admin</title>
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
    background: var(--bg);
    color: var(--ink);
    font-family: 'Work Sans', sans-serif;
  }

  a { color: inherit; text-decoration: none; }

  .shell { display: flex; min-height: 100vh; }

  .sidebar {
    width: 232px; flex-shrink: 0;
    background: var(--sidebar);
    border-right: 1px solid var(--line);
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
    padding: 10px 12px; border-radius: 8px; font-size: 14px; color: var(--ink-soft);
  }

  .sidebar nav a svg { width: 17px; height: 17px; flex-shrink: 0; stroke: currentColor; fill: none; stroke-width: 1.6; }
  .sidebar nav a:hover { background: rgba(0,0,0,0.04); color: var(--ink); }
  .sidebar nav a.active { background: var(--panel); color: var(--ink); font-weight: 500; border: 1px solid var(--line); }

  .sidebar-footer {
    margin-top: auto; padding-top: 20px; border-top: 1px solid var(--line);
    display: flex; align-items: center; gap: 10px; font-size: 13px;
  }

  .avatar {
    width: 30px; height: 30px; border-radius: 50%; background: var(--clay); color: #fff;
    display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 500; flex-shrink: 0;
  }

  .sidebar-footer .user-name { font-weight: 500; }
  .sidebar-footer .view-site { color: var(--ink-soft); font-size: 12px; }

  .main { flex: 1; min-width: 0; padding: 34px 44px 60px; }

  .topbar h1 {
    font-family: 'Fraunces', serif; font-weight: 500; font-size: 27px; margin: 0 0 6px;
  }
  .topbar p { font-size: 13px; color: var(--ink-soft); margin: 0 0 30px; }

  .dropzone {
    border: 1.5px dashed var(--line);
    border-radius: 14px;
    background: var(--panel);
    padding: 60px 30px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s;
  }

  .dropzone.drag-active {
    border-color: var(--clay);
    background: rgba(122,75,51,0.05);
  }

  .dropzone svg {
    width: 34px; height: 34px;
    stroke: var(--ink-soft);
    fill: none;
    stroke-width: 1.4;
    margin-bottom: 16px;
  }

  .dropzone h2 {
    font-family: 'Fraunces', serif;
    font-weight: 500;
    font-size: 19px;
    margin: 0 0 8px;
  }

  .dropzone p {
    font-size: 13px;
    color: var(--ink-soft);
    margin: 0 0 20px;
  }

  .browse-btn {
    padding: 10px 20px;
    border-radius: 8px;
    border: 1px solid var(--clay);
    background: none;
    color: var(--clay);
    font-family: 'Work Sans', sans-serif;
    font-size: 13px;
    cursor: pointer;
  }

  .browse-btn:hover { background: rgba(122,75,51,0.06); }

  .formats-note {
    font-size: 12px;
    color: var(--ink-soft);
    margin-top: 18px;
  }

  .queue {
    margin-top: 32px;
  }

  .queue-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
  }

  .queue-header h2 {
    font-family: 'Work Sans', sans-serif;
    font-weight: 500;
    font-size: 14px;
    margin: 0;
  }

  .queue-header .summary {
    font-size: 13px;
    color: var(--ink-soft);
  }

  .queue-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .queue-item {
    display: flex;
    align-items: center;
    gap: 14px;
    background: var(--panel);
    border: 1px solid var(--line);
    border-radius: 10px;
    padding: 10px 14px;
  }

  .queue-item img {
    width: 46px;
    height: 46px;
    object-fit: cover;
    border-radius: 6px;
    background: var(--img-fallback);
    flex-shrink: 0;
  }

  .queue-item-body {
    flex: 1;
    min-width: 0;
  }

  .queue-item-name {
    font-size: 13px;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .queue-item-meta {
    font-size: 12px;
    color: var(--ink-soft);
    margin-top: 2px;
  }

  .progress-track {
    height: 4px;
    background: var(--line);
    border-radius: 999px;
    overflow: hidden;
    margin-top: 8px;
  }

  .progress-fill {
    height: 100%;
    width: 0%;
    background: var(--clay);
    transition: width 0.2s linear;
  }

  .queue-item.done .progress-fill { background: var(--ok); }

  .status-label {
    font-size: 12px;
    color: var(--ink-soft);
    flex-shrink: 0;
    width: 90px;
    text-align: right;
  }

  .queue-item.done .status-label { color: var(--ok); }
  .queue-item.duplicate .status-label { color: #8A6A1F; }

  .queue-footer {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
  }

  .goto-library-btn {
    padding: 11px 20px;
    border-radius: 8px;
    background: var(--clay);
    color: #fff;
    border: none;
    font-size: 13px;
    font-family: 'Work Sans', sans-serif;
    cursor: pointer;
    display: none;
  }

  .goto-library-btn.visible { display: inline-block; }

  #file-input { display: none; }

  @media (max-width: 720px) {
    .sidebar { display: none; }
    .main { padding: 24px 20px 40px; }
    .dropzone { padding: 40px 18px; }
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
      <a href="#" class="active">
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
      <h1>Importer des œuvres</h1>
      <p>JPEG, PNG, WebP et GIF acceptés. Les fichiers en double sont détectés automatiquement à partir de leur empreinte.</p>
    </div>

    <div class="dropzone" id="dropzone">
      <svg viewBox="0 0 24 24"><path d="M12 3v14"></path><path d="M5 10l7-7 7 7"></path><path d="M5 21h14"></path></svg>
      <h2>Glissez vos images ici</h2>
      <p>ou parcourez votre ordinateur pour en sélectionner plusieurs à la fois</p>
      <button class="browse-btn" type="button" id="browse-btn">Parcourir les fichiers</button>
      <input type="file" id="file-input" multiple accept="image/jpeg,image/png,image/webp,image/gif">
    </div>

    <div class="queue" id="queue" style="display:none;">
      <div class="queue-header">
        <h2>Import en cours</h2>
        <span class="summary" id="queue-summary"></span>
      </div>
      <div class="queue-list" id="queue-list"></div>
      <div class="queue-footer">
        <button class="goto-library-btn" id="goto-library-btn">Voir dans la médiathèque →</button>
      </div>
    </div>
  </main>
</div>

<script>
  const dropzone = document.getElementById('dropzone');
  const fileInput = document.getElementById('file-input');
  const browseBtn = document.getElementById('browse-btn');
  const queue = document.getElementById('queue');
  const queueList = document.getElementById('queue-list');
  const queueSummary = document.getElementById('queue-summary');
  const gotoLibraryBtn = document.getElementById('goto-library-btn');

  const seenNames = new Set();
  let total = 0;
  let done = 0;

  browseBtn.addEventListener('click', () => fileInput.click());
  dropzone.addEventListener('click', (e) => { if (e.target === dropzone) fileInput.click(); });

  ['dragenter', 'dragover'].forEach(evt => {
    dropzone.addEventListener(evt, (e) => {
      e.preventDefault();
      dropzone.classList.add('drag-active');
    });
  });
  ['dragleave', 'drop'].forEach(evt => {
    dropzone.addEventListener(evt, (e) => {
      e.preventDefault();
      dropzone.classList.remove('drag-active');
    });
  });

  dropzone.addEventListener('drop', (e) => {
    handleFiles(e.dataTransfer.files);
  });

  fileInput.addEventListener('change', () => {
    handleFiles(fileInput.files);
    fileInput.value = '';
  });

  function formatSize(bytes) {
    if (bytes < 1024) return bytes + ' o';
    if (bytes < 1024 * 1024) return Math.round(bytes / 1024) + ' Ko';
    return (bytes / (1024 * 1024)).toFixed(1) + ' Mo';
  }

  function handleFiles(fileList) {
    const files = Array.from(fileList).filter(f => /^image\/(jpeg|png|webp|gif)$/.test(f.type));
    if (!files.length) return;

    queue.style.display = 'block';
    total += files.length;
    updateSummary();

    files.forEach(addQueueItem);
  }

  function updateSummary() {
    queueSummary.textContent = `${done} / ${total} importées`;
    if (done === total && total > 0) {
      gotoLibraryBtn.classList.add('visible');
    }
  }

  function addQueueItem(file) {
    const isDuplicate = seenNames.has(file.name);
    seenNames.add(file.name);

    const item = document.createElement('div');
    item.className = 'queue-item';

    const img = document.createElement('img');
    const reader = new FileReader();
    reader.onload = () => { img.src = reader.result; };
    if (typeof reader.readAsDataURL === 'function') {
      try { reader.readAsDataURL(file); } catch (err) { /* ignore preview errors */ }
    }

    item.innerHTML = `
      <div class="queue-item-body">
        <div class="queue-item-name"></div>
        <div class="queue-item-meta"></div>
        <div class="progress-track"><div class="progress-fill"></div></div>
      </div>
      <span class="status-label">Import…</span>
    `;
    item.prepend(img);
    item.querySelector('.queue-item-name').textContent = file.name;
    item.querySelector('.queue-item-meta').textContent = formatSize(file.size);

    queueList.prepend(item);

    if (isDuplicate) {
      item.classList.add('duplicate');
      item.querySelector('.progress-fill').style.width = '100%';
      item.querySelector('.status-label').textContent = 'Doublon détecté';
      done++;
      updateSummary();
      return;
    }

    const fill = item.querySelector('.progress-fill');
    const statusLabel = item.querySelector('.status-label');
    let progress = 0;
    const timer = setInterval(() => {
      progress += 8 + Math.random() * 12;
      if (progress >= 100) {
        progress = 100;
        clearInterval(timer);
        item.classList.add('done');
        statusLabel.textContent = 'Importée';
        done++;
        updateSummary();
      }
      fill.style.width = progress + '%';
    }, 180);
  }
</script>

</body>
</html>