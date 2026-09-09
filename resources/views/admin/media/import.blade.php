<x-admin-layout :active="'media'" :title="'Importer des œuvres'">
  <div class="topbar">
    <div>
      <h1>Importer des œuvres</h1>
      <p>JPEG, PNG, WebP et GIF acceptés. Les fichiers en double sont détectés automatiquement à partir de leur empreinte.</p>
    </div>
  </div>

  <div class="alert alert-info alert-block">
    <div>
      <strong>Après l'envoi, les vignettes se génèrent en tâche de fond.</strong>
      <div style="margin-top:4px;">
        Ce traitement n'avance que pendant qu'une page de l'administration reste ouverte et active dans un onglet au premier plan
        (les navigateurs ralentissent fortement les onglets en arrière-plan). Restez sur cette page, ou une autre page d'administration,
        jusqu'à ce que la barre de progression en haut affiche 100% — sinon les œuvres restent visibles mais sans image tant que le traitement n'est pas terminé.
        Pour un traitement fiable même onglet fermé, une tâche planifiée peut être configurée côté hébergement (demande-le si besoin).
      </div>
    </div>
  </div>

  <div class="panel">
    <h2 style="margin:0 0 10px;">Import depuis un dossier serveur (gros transferts)</h2>
    <p style="font-size:13px;color:var(--ink-soft);margin:0 0 12px;">
      Pour des centaines de photos d'un coup, dépose-les par FTP/SFTP directement dans ce dossier sur le serveur au lieu de passer par l'envoi navigateur :
    </p>
    <code style="display:block;padding:10px 14px;background:var(--bg);border:1px solid var(--line);border-radius:6px;font-size:12px;margin-bottom:14px;word-break:break-all;">{{ $importFolderPath }}</code>

    @if (session('status') && str_contains(session('status'), 'importée'))
      <div class="alert alert-success" style="margin-bottom:14px;">{{ session('status') }}</div>
    @endif

    <div style="display:flex;align-items:center;gap:14px;">
      <span style="font-size:13px;color:var(--ink-soft);">{{ $importFolderCount }} fichier(s) en attente dans ce dossier</span>
      @if ($importFolderCount > 0)
        <form method="POST" action="{{ route('admin.media.import-folder') }}" id="import-folder-form">
          @csrf
          <button type="submit" class="btn primary" id="import-folder-btn">Importer depuis le dossier</button>
        </form>
      @endif
    </div>
  </div>

  <div class="dropzone" id="dropzone">
    <svg viewBox="0 0 24 24"><path d="M12 3v14"></path><path d="M5 10l7-7 7 7"></path><path d="M5 21h14"></path></svg>
    <h2>Glissez vos images ici</h2>
    <p>ou parcourez votre ordinateur pour en sélectionner plusieurs à la fois</p>
    <button class="browse-btn" type="button" id="browse-btn">Parcourir les fichiers</button>
    <input type="file" id="file-input" multiple accept="image/jpeg,image/png,image/webp,image/gif">
  </div>

  <div class="queue" id="queue" style="display:none;margin-top:32px;">
    <div class="queue-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
      <h2 style="font-family:'Work Sans',sans-serif;font-weight:500;font-size:14px;margin:0;">Import en cours</h2>
      <span class="summary" id="queue-summary" style="font-size:13px;color:var(--ink-soft);"></span>
    </div>
    <div class="queue-list" id="queue-list"></div>
    <div style="display:flex;justify-content:flex-end;margin-top:20px;">
      <a href="{{ route('admin.media.index') }}" class="btn primary" id="goto-library-btn" style="display:none;">Voir dans la médiathèque →</a>
    </div>
  </div>

<script>
  const dropzone = document.getElementById('dropzone');
  const fileInput = document.getElementById('file-input');
  const browseBtn = document.getElementById('browse-btn');
  const queue = document.getElementById('queue');
  const queueList = document.getElementById('queue-list');
  const queueSummary = document.getElementById('queue-summary');
  const gotoLibraryBtn = document.getElementById('goto-library-btn');
  const uploadUrl = @json(route('admin.media.store'));
  const csrfToken = @json(csrf_token());
  const concurrency = @json(max(1, (int) \App\Models\Setting::get('import_concurrency', 3)));

  let total = 0;
  let done = 0;
  let active = 0;
  const pending = [];

  browseBtn.addEventListener('click', () => fileInput.click());
  dropzone.addEventListener('click', (e) => { if (e.target === dropzone) fileInput.click(); });

  ['dragenter', 'dragover'].forEach(evt => {
    dropzone.addEventListener(evt, (e) => { e.preventDefault(); dropzone.classList.add('drag-active'); });
  });
  ['dragleave', 'drop'].forEach(evt => {
    dropzone.addEventListener(evt, (e) => { e.preventDefault(); dropzone.classList.remove('drag-active'); });
  });
  dropzone.addEventListener('drop', (e) => handleFiles(e.dataTransfer.files));
  fileInput.addEventListener('change', () => { handleFiles(fileInput.files); fileInput.value = ''; });

  function describeError(xhr) {
    if (xhr.status === 413) return 'Fichier trop volumineux (20 Mo max)';
    if (xhr.status === 419) return 'Session expirée — recharge la page';

    try {
      const data = JSON.parse(xhr.responseText);
      if (data.errors) {
        return Object.values(data.errors).flat().join(' ');
      }
      if (data.message) return data.message;
    } catch (e) {
      // Réponse non-JSON (page d'erreur serveur) : on garde le message générique ci-dessous.
    }

    return `Erreur (code ${xhr.status})`;
  }

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
    pending.push(...files);
    fillSlots();
  }

  // N'envoie jamais plus de `concurrency` fichiers en même temps : au-delà,
  // le reste attend son tour dans `pending` (réglable depuis Réglages →
  // Médiathèque, utile sur un hébergement modeste).
  function fillSlots() {
    while (active < concurrency && pending.length > 0) {
      const file = pending.shift();
      active++;
      uploadFile(file, () => {
        active--;
        fillSlots();
      });
    }
  }

  function updateSummary() {
    queueSummary.textContent = `${done} / ${total} importées`;
    if (done === total && total > 0) gotoLibraryBtn.style.display = 'inline-block';
  }

  function uploadFile(file, onSettled) {
    const item = document.createElement('div');
    item.className = 'queue-item';
    item.innerHTML = `
      <img alt="">
      <div class="queue-item-body">
        <div class="queue-item-name"></div>
        <div class="queue-item-meta"></div>
        <div class="progress-track"><div class="progress-fill"></div></div>
      </div>
      <span class="status-label">Import…</span>
    `;
    item.querySelector('.queue-item-name').textContent = file.name;
    item.querySelector('.queue-item-meta').textContent = formatSize(file.size);
    queueList.prepend(item);

    const reader = new FileReader();
    reader.onload = () => { item.querySelector('img').src = reader.result; };
    reader.readAsDataURL(file);

    const fill = item.querySelector('.progress-fill');
    const statusLabel = item.querySelector('.status-label');

    const formData = new FormData();
    formData.append('file', file);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', uploadUrl);
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Accept', 'application/json');

    xhr.upload.addEventListener('progress', (e) => {
      if (e.lengthComputable) fill.style.width = Math.round((e.loaded / e.total) * 100) + '%';
    });

    xhr.addEventListener('load', () => {
      fill.style.width = '100%';
      done++;
      updateSummary();

      if (xhr.status >= 200 && xhr.status < 300) {
        const data = JSON.parse(xhr.responseText);
        if (data.duplicate) {
          item.classList.add('duplicate');
          statusLabel.textContent = 'Doublon détecté';
        } else {
          item.classList.add('done');
          statusLabel.textContent = 'Importée';
        }
      } else {
        item.classList.add('failed');
        statusLabel.textContent = describeError(xhr);
      }
      onSettled();
    });

    xhr.addEventListener('error', () => {
      done++;
      updateSummary();
      item.classList.add('failed');
      statusLabel.textContent = 'Erreur réseau';
      onSettled();
    });

    xhr.send(formData);
  }

  // Un gros dépôt FTP se traite par lots (voir MediaIngestService) : tant
  // qu'il en reste, on relance automatiquement l'import après chaque passage
  // plutôt que d'obliger à recliquer manuellement.
  @if ($importFolderCount > 0 && session('status') && str_contains(session('status'), 'importée'))
    (function () {
      const btn = document.getElementById('import-folder-btn');
      if (!btn) return;
      btn.disabled = true;
      btn.textContent = 'Import automatique en cours…';
      setTimeout(() => document.getElementById('import-folder-form').requestSubmit(), 1500);
    })();
  @endif
</script>
</x-admin-layout>
