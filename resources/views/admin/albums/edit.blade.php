<x-admin-layout :active="'albums'" :title="$album->title">
  <div style="display:flex;justify-content:space-between;align-items:center;">
    <a href="{{ route('admin.albums.index') }}" class="back-link" style="display:inline-flex;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">← Albums</a>
    <form method="POST" action="{{ route('admin.albums.duplicate', $album) }}">
      @csrf
      <button type="submit" class="btn">Dupliquer</button>
    </form>
  </div>

  <form method="POST" action="{{ route('admin.albums.update', $album) }}">
    @csrf
    @method('put')

    <div class="editor-header" style="display:flex;justify-content:space-between;align-items:flex-start;gap:20px;margin-bottom:28px;flex-wrap:wrap;">
      <div style="flex:1;min-width:260px;">
        <input class="title-input" type="text" name="title" value="{{ old('title', $album->title) }}"
               style="font-family:'Fraunces',serif;font-weight:500;font-size:30px;border:none;background:none;color:var(--ink);width:100%;padding:4px 0;">
        <div style="display:flex;align-items:center;gap:8px;margin-top:6px;font-size:13px;color:var(--ink-soft);">
          /album/
          <input type="text" name="slug" value="{{ old('slug', $album->slug) }}"
                 style="border:none;background:none;color:var(--ink-soft);font-family:'Work Sans',sans-serif;font-size:13px;border-bottom:1px dashed var(--line);padding:2px 0;width:200px;">
        </div>
        <div style="display:flex;align-items:center;gap:10px;margin-top:14px;flex-wrap:wrap;">
          <select name="status" class="status-select" style="font-family:'Work Sans',sans-serif;font-size:13px;padding:7px 12px;border:1px solid var(--line);border-radius:999px;background:var(--panel);">
            <option value="draft" @selected($album->status === 'draft')>Brouillon</option>
            <option value="unlisted" @selected($album->status === 'unlisted')>Non répertorié</option>
            <option value="published" @selected($album->status === 'published')>Publié</option>
            <option value="archived" @selected($album->status === 'archived')>Archivé</option>
          </select>
          <select name="visibility" id="visibility-select" class="visibility-select" style="font-family:'Work Sans',sans-serif;font-size:13px;padding:7px 12px;border:1px solid var(--line);border-radius:999px;background:var(--panel);">
            <option value="public" @selected($album->visibility === 'public')>Publique</option>
            <option value="password" @selected($album->visibility === 'password')>Protégé par mot de passe</option>
            <option value="private" @selected($album->visibility === 'private')>Privée</option>
          </select>
          <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-soft);">
            <input type="checkbox" name="comments_enabled" value="1" @checked($album->comments_enabled)>
            Commentaires activés
          </label>
          <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-soft);">
            <input type="checkbox" name="is_featured" value="1" @checked($album->is_featured)>
            Mettre en avant sur la page d'accueil
          </label>
        </div>
        <div class="password-field {{ old('visibility', $album->visibility) === 'password' ? 'visible' : '' }}" id="password-field"
             style="margin-top:16px;padding:14px 16px;background:var(--panel);border:1px solid var(--line);border-radius:10px;max-width:420px;{{ old('visibility', $album->visibility) === 'password' ? '' : 'display:none;' }}">
          <label style="display:block;font-size:12px;color:var(--ink-soft);margin-bottom:6px;">Mot de passe de l'album</label>
          <div style="display:flex;gap:8px;">
            <input type="text" name="password" id="album-password" placeholder="{{ $album->password_hash ? 'Laisser vide pour ne pas changer' : '' }}"
                   style="flex:1;padding:9px 11px;border:1px solid var(--line);border-radius:6px;background:#fff;font-size:13px;">
            <button type="button" class="btn" id="generate-password-btn">Générer</button>
          </div>
          <p style="font-size:11px;color:var(--ink-soft);margin:8px 0 0;">Transmets ce mot de passe directement aux personnes concernées — la page n'est pas indexée par les moteurs de recherche.</p>
        </div>
      </div>
      <div style="display:flex;gap:10px;flex-shrink:0;">
        <a href="{{ route('public.album', $album) }}" class="btn" target="_blank">Aperçu</a>
        <button type="submit" class="btn primary">Enregistrer</button>
      </div>
    </div>

    @if (session('status') === 'album-updated')
      <p style="font-size:13px;color:var(--ok);margin:-18px 0 20px;">Enregistré.</p>
    @endif

    <div style="display:grid;grid-template-columns:300px 1fr;gap:28px;align-items:start;">
      <div>
        <div class="panel">
          <h2>Informations</h2>
          <div class="field">
            <label>Texte d'introduction</label>
            <textarea name="intro_text">{{ old('intro_text', $album->intro_text) }}</textarea>
          </div>
          <div class="field-row">
            <div class="field">
              <label>Date de création</label>
              <input type="date" name="created_on" value="{{ old('created_on', $album->created_on?->format('Y-m-d')) }}">
            </div>
            <div class="field">
              <label>Date de publication</label>
              <input type="date" name="published_at" value="{{ old('published_at', $album->published_at?->format('Y-m-d')) }}">
            </div>
          </div>
          <div class="field">
            <label>Crédits / collaborateurs</label>
            <input type="text" name="credits_text" value="{{ old('credits_text', $album->credits_text) }}">
          </div>
          <div class="field">
            <label>Lien externe (optionnel)</label>
            <input type="text" name="external_link" value="{{ old('external_link', $album->external_link) }}" placeholder="https://…">
          </div>
        </div>

        <div class="panel">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            <h2 style="margin:0;">SEO &amp; partage</h2>
            <label class="switch" style="position:relative;display:inline-block;cursor:pointer;">
              <input type="checkbox" name="seo_auto" id="seo-toggle" value="1" @checked($album->seo_auto) style="position:absolute;opacity:0;width:0;height:0;">
              <span class="switch-track" style="display:block;width:36px;height:20px;background:{{ $album->seo_auto ? 'var(--clay)' : 'var(--line)' }};border-radius:999px;position:relative;">
                <span class="switch-thumb" style="position:absolute;top:2px;left:{{ $album->seo_auto ? '18px' : '2px' }};width:16px;height:16px;border-radius:50%;background:var(--panel);"></span>
              </span>
            </label>
          </div>
          <p id="seo-hint" style="font-size:12px;color:var(--ink-soft);margin:0 0 14px;{{ $album->seo_auto ? '' : 'display:none;' }}">Champs désactivés — le titre et la description seront générés automatiquement à partir de l'album.</p>
          <div class="field" id="seo-title-field">
            <label>Titre SEO</label>
            <input type="text" name="seo_title" id="seo-title" value="{{ old('seo_title', $album->seo_title) }}" @disabled($album->seo_auto)>
          </div>
          <div class="field" id="seo-desc-field">
            <label>Description SEO</label>
            <textarea name="seo_description" id="seo-desc" @disabled($album->seo_auto)>{{ old('seo_description', $album->seo_description) }}</textarea>
          </div>
        </div>
      </div>

      <div>
        <div class="panel">
          <div class="media-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <h2 style="margin:0;font-family:'Fraunces',serif;font-weight:500;font-size:18px;">Œuvres de l'album</h2>
            <span style="font-size:13px;color:var(--ink-soft);">{{ $album->media->count() }} œuvre(s)</span>
          </div>

          <button type="button" class="add-media-btn" id="toggle-picker-btn"
                  style="display:flex;align-items:center;gap:8px;padding:10px 16px;border-radius:8px;border:1px dashed var(--clay);background:none;color:var(--clay);font-size:13px;cursor:pointer;margin-bottom:18px;">
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M12 5v14M5 12h14"></path></svg>
            Ajouter des œuvres depuis la médiathèque
          </button>

          <div id="media-picker" style="display:none;margin-bottom:20px;padding:16px;border:1px solid var(--line);border-radius:10px;background:var(--bg);">
            @php $available = \App\Models\Media::whereNull('trashed_at')->whereNotIn('id', $album->media->pluck('id'))->latest()->limit(60)->get(); @endphp
            @if ($available->isEmpty())
              <p style="margin:0;font-size:13px;color:var(--ink-soft);">Toutes les œuvres de la médiathèque sont déjà dans cet album.</p>
            @else
              <div class="media-grid" id="picker-grid">
                @foreach ($available as $item)
                  <label class="media-item" style="cursor:pointer;">
                    <input type="checkbox" value="{{ $item->id }}" class="picker-checkbox" style="position:absolute;top:8px;right:8px;width:18px;height:18px;">
                    @if ($thumb = $item->variant('thumbnail'))
                      <img src="{{ $thumb->url() }}" alt="">
                    @endif
                  </label>
                @endforeach
              </div>
              <button type="button" id="attach-selected-btn" class="btn primary" style="margin-top:14px;">Ajouter la sélection</button>
            @endif
          </div>

          <div class="media-grid" id="media-grid" data-reorder-url="{{ route('admin.albums.media.reorder', $album) }}">
            @foreach ($album->media as $item)
              <div class="media-item {{ $album->cover_media_id === $item->id ? 'is-cover' : '' }}" draggable="true" data-id="{{ $item->id }}">
                @if ($thumb = $item->variant('thumbnail'))
                  <img src="{{ $thumb->url() }}" alt="{{ $item->alt_text }}">
                @endif
                <span class="cover-badge">Couverture</span>
                <div class="media-item-overlay">
                  <button type="button" class="set-cover-btn" data-url="{{ route('admin.albums.cover', $album) }}" data-id="{{ $item->id }}">Couverture</button>
                  <button type="button" class="remove-btn" data-url="{{ route('admin.albums.media.detach', [$album, $item]) }}" aria-label="Retirer">✕</button>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </form>

<script>
  const csrfToken = @json(csrf_token());

  function postJson(url, method, body) {
    return fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
      body: body ? JSON.stringify(body) : undefined,
    });
  }

  // SEO auto toggle
  const seoToggle = document.getElementById('seo-toggle');
  const seoHint = document.getElementById('seo-hint');
  const seoTitle = document.getElementById('seo-title');
  const seoDesc = document.getElementById('seo-desc');
  seoToggle.addEventListener('change', () => {
    seoHint.style.display = seoToggle.checked ? 'block' : 'none';
    seoTitle.disabled = seoToggle.checked;
    seoDesc.disabled = seoToggle.checked;
  });

  // Visibility -> password field
  const visibilitySelect = document.getElementById('visibility-select');
  const passwordField = document.getElementById('password-field');
  visibilitySelect.addEventListener('change', () => {
    passwordField.style.display = visibilitySelect.value === 'password' ? 'block' : 'none';
  });
  document.getElementById('generate-password-btn').addEventListener('click', () => {
    const words = ['marais', 'lumiere', 'vigne', 'chateau', 'brume', 'rivage', 'atelier', 'cadre'];
    const word = words[Math.floor(Math.random() * words.length)];
    document.getElementById('album-password').value = word + Math.floor(1000 + Math.random() * 9000);
  });

  // Media picker
  const pickerBtn = document.getElementById('toggle-picker-btn');
  const picker = document.getElementById('media-picker');
  pickerBtn.addEventListener('click', () => {
    picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
  });
  const attachBtn = document.getElementById('attach-selected-btn');
  if (attachBtn) {
    attachBtn.addEventListener('click', () => {
      const ids = Array.from(document.querySelectorAll('.picker-checkbox:checked')).map(c => c.value);
      if (!ids.length) return;
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = @json(route('admin.albums.media.attach', $album));
      form.innerHTML = `@csrf <input type="hidden" name="media_ids" value="${ids.join(',')}">`;
      document.body.appendChild(form);
      form.submit();
    });
  }

  // Drag & drop reorder
  const grid = document.getElementById('media-grid');
  let dragged = null;

  grid.querySelectorAll('.media-item').forEach(attachItemHandlers);

  function attachItemHandlers(item) {
    item.addEventListener('dragstart', () => { dragged = item; item.classList.add('dragging'); });
    item.addEventListener('dragend', () => {
      item.classList.remove('dragging');
      grid.querySelectorAll('.media-item').forEach(i => i.classList.remove('drag-over'));
    });
    item.addEventListener('dragover', (e) => { e.preventDefault(); item.classList.add('drag-over'); });
    item.addEventListener('dragleave', () => item.classList.remove('drag-over'));
    item.addEventListener('drop', (e) => {
      e.preventDefault();
      item.classList.remove('drag-over');
      if (!dragged || dragged === item) return;
      const items = Array.from(grid.children);
      const fromIndex = items.indexOf(dragged);
      const toIndex = items.indexOf(item);
      if (fromIndex < toIndex) item.after(dragged); else item.before(dragged);
      saveOrder();
    });

    const setCoverBtn = item.querySelector('.set-cover-btn');
    setCoverBtn.addEventListener('click', () => {
      postJson(setCoverBtn.dataset.url, 'PUT', { media_id: setCoverBtn.dataset.id }).then(() => {
        grid.querySelectorAll('.media-item').forEach(i => i.classList.remove('is-cover'));
        item.classList.add('is-cover');
      });
    });

    const removeBtn = item.querySelector('.remove-btn');
    removeBtn.addEventListener('click', () => {
      if (!confirm('Retirer cette œuvre de l\'album ?')) return;
      postJson(removeBtn.dataset.url, 'DELETE').then(() => item.remove());
    });
  }

  function saveOrder() {
    const order = Array.from(grid.querySelectorAll('.media-item')).map(i => i.dataset.id);
    postJson(grid.dataset.reorderUrl, 'PUT', { order });
  }
</script>
</x-admin-layout>
