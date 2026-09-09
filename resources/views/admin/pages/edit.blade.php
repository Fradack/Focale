<x-admin-layout :active="'pages'" :title="$page->title">
  <a href="{{ route('admin.pages.index') }}" style="display:inline-flex;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">← Pages</a>

  <div class="topbar">
    <h1>{{ $page->title }}</h1>
    <a href="{{ route('page.show', $page) }}" class="btn" target="_blank">Aperçu</a>
  </div>

  @if (session('status') === 'page-updated')
    <div class="alert alert-success">Enregistré.</div>
  @endif

  <form method="POST" action="{{ route('admin.pages.update', $page) }}">
    @csrf
    @method('put')

    <div style="display:grid;grid-template-columns:1fr 320px;gap:28px;align-items:start;">
      <div>
        <div class="panel">
          <div class="field-row">
            <div class="field">
              <label>Titre</label>
              <input type="text" name="title" value="{{ old('title', $page->title) }}">
            </div>
            <div class="field">
              <label>Adresse (slug)</label>
              <input type="text" name="slug" value="{{ old('slug', $page->slug) }}">
              @error('slug') <p class="error">{{ $message }}</p> @enderror
            </div>
          </div>

          <div class="field">
            <label>Contenu</label>
            <div class="bbcode-toolbar" id="bbcode-toolbar">
              <button type="button" data-wrap="[b]|[/b]" title="Gras"><strong>G</strong></button>
              <button type="button" data-wrap="[i]|[/i]" title="Italique"><em>I</em></button>
              <button type="button" data-wrap="[u]|[/u]" title="Souligné"><u>S</u></button>
              <button type="button" data-wrap="[s]|[/s]" title="Barré"><s>B</s></button>
              <span class="bbcode-sep"></span>
              <button type="button" id="bbcode-link-btn" title="Lien">Lien</button>
              <button type="button" data-wrap="[quote]|[/quote]" title="Citation">Citation</button>
              <button type="button" id="bbcode-list-btn" title="Liste à puces">Liste</button>
              <span class="bbcode-sep"></span>
              <button type="button" id="toggle-image-picker-btn" title="Insérer une image depuis la médiathèque">+ Image</button>
            </div>
            <textarea name="content" id="content-textarea" rows="24" style="width:100%;font-family:'Work Sans',monospace;">{{ old('content', $block?->content['text'] ?? '') }}</textarea>
            <p style="font-size:12px;color:var(--ink-soft);margin:8px 0 0;">
              BBCode pris en charge : [b][/b] [i][/i] [u][/u] [s][/s] [url=…][/url] [img][/img] [quote][/quote] [list][*]…[/list] [color=#hex][/color] [size=N][/size] [code][/code].
              Une ligne vide sépare les paragraphes.
            </p>
          </div>

          <div id="image-picker" style="display:none;margin-top:14px;padding:16px;border:1px solid var(--line);border-radius:10px;background:var(--bg);">
            @php $pickerMedia = \App\Models\Media::whereNull('trashed_at')->latest()->limit(60)->get(); @endphp
            @if ($pickerMedia->isEmpty())
              <p style="margin:0;font-size:13px;color:var(--ink-soft);">Aucune œuvre dans la médiathèque pour l'instant.</p>
            @else
              <div class="media-grid">
                @foreach ($pickerMedia as $item)
                  @php $insertUrl = $item->variant('web')?->url() ?? $item->variant('thumbnail')?->url(); @endphp
                  @if ($insertUrl)
                    <label class="media-item image-picker-item" style="cursor:pointer;" data-url="{{ $insertUrl }}">
                      @if ($thumb = $item->variant('thumbnail'))
                        <img src="{{ $thumb->url() }}" alt="">
                      @endif
                    </label>
                  @endif
                @endforeach
              </div>
            @endif
          </div>
        </div>

        <div class="panel">
          <h2>SEO</h2>
          <div class="field">
            <label>Titre SEO</label>
            <input type="text" name="seo_title" value="{{ old('seo_title', $page->seo_title) }}">
          </div>
          <div class="field">
            <label>Description SEO</label>
            <textarea name="seo_description">{{ old('seo_description', $page->seo_description) }}</textarea>
          </div>
        </div>
      </div>

      <div>
        <div class="panel">
          <div class="field">
            <label>Statut</label>
            <select name="status">
              <option value="draft" @selected($page->status === 'draft')>Brouillon</option>
              <option value="published" @selected($page->status === 'published')>Publiée</option>
            </select>
          </div>
          <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-soft);margin-top:12px;">
            <input type="checkbox" name="show_in_nav" value="1" @checked(old('show_in_nav', $page->show_in_nav))>
            Afficher dans le menu du site
          </label>
        </div>

        <div class="panel" style="display:flex;gap:10px;">
          <button type="submit" class="btn primary" style="flex:1;">Enregistrer</button>
        </div>
      </div>
    </div>
  </form>

  <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" style="max-width:640px;margin-top:20px;" onsubmit="return confirm('Supprimer définitivement cette page ?');">
    @csrf
    @method('delete')
    <button type="submit" class="btn" style="color:var(--danger);border-color:var(--danger);">Supprimer cette page</button>
  </form>

<script>
  const textarea = document.getElementById('content-textarea');

  function insertAtCursor(text) {
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    textarea.value = textarea.value.substring(0, start) + text + textarea.value.substring(end);
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = start + text.length;
  }

  function wrapSelection(before, after) {
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selected = textarea.value.substring(start, end) || 'texte';
    textarea.value = textarea.value.substring(0, start) + before + selected + after + textarea.value.substring(end);
    textarea.focus();
    textarea.selectionStart = start + before.length;
    textarea.selectionEnd = start + before.length + selected.length;
  }

  document.getElementById('bbcode-toolbar').addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-wrap]');
    if (!btn) return;
    const [before, after] = btn.dataset.wrap.split('|');
    wrapSelection(before, after);
  });

  document.getElementById('bbcode-link-btn').addEventListener('click', () => {
    const url = prompt('Adresse du lien (https://…) :');
    if (!url) return;
    wrapSelection(`[url=${url}]`, '[/url]');
  });

  document.getElementById('bbcode-list-btn').addEventListener('click', () => {
    insertAtCursor('\n[list]\n[*]Élément\n[*]Élément\n[/list]\n');
  });

  const imagePickerBtn = document.getElementById('toggle-image-picker-btn');
  const imagePicker = document.getElementById('image-picker');
  imagePickerBtn.addEventListener('click', () => {
    imagePicker.style.display = imagePicker.style.display === 'none' ? 'block' : 'none';
  });
  imagePicker.addEventListener('click', (e) => {
    const item = e.target.closest('.image-picker-item');
    if (!item) return;
    insertAtCursor(`[img]${item.dataset.url}[/img]`);
  });
</script>
</x-admin-layout>
