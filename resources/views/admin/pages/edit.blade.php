<x-admin-layout :active="'pages'" :title="$page->title">
  <a href="{{ route('admin.pages.index') }}" style="display:inline-flex;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">← Pages</a>

  <div class="topbar">
    <h1>{{ $page->title }}</h1>
  </div>

  <form method="POST" action="{{ route('admin.pages.update', $page) }}" style="max-width:640px;">
    @csrf
    @method('put')

    <div class="panel">
      <div class="field-row">
        <div class="field">
          <label>Titre</label>
          <input type="text" name="title" value="{{ old('title', $page->title) }}">
        </div>
        <div class="field">
          <label>Statut</label>
          <select name="status">
            <option value="draft" @selected($page->status === 'draft')>Brouillon</option>
            <option value="published" @selected($page->status === 'published')>Publiée</option>
          </select>
        </div>
      </div>
      <div class="field">
        <label>Adresse (slug)</label>
        <input type="text" name="slug" value="{{ old('slug', $page->slug) }}">
        @error('slug') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Contenu</label>
        <textarea name="content" rows="10">{{ old('content', $block?->content['text'] ?? '') }}</textarea>
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

    <div style="display:flex;align-items:center;gap:14px;">
      <button type="submit" class="btn primary">Enregistrer</button>
      @if (session('status') === 'page-updated')
        <span style="font-size:13px;color:var(--ok);">Enregistré.</span>
      @endif
    </div>
  </form>

  <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" style="max-width:640px;margin-top:20px;" onsubmit="return confirm('Supprimer définitivement cette page ?');">
    @csrf
    @method('delete')
    <button type="submit" class="btn" style="color:var(--danger);border-color:var(--danger);">Supprimer cette page</button>
  </form>
</x-admin-layout>
