<x-admin-layout :active="'media'" :title="$media->title ?: 'Œuvre'">
  <a href="{{ route('admin.media.index') }}" style="display:inline-flex;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">← Médiathèque</a>

  <div class="topbar">
    <h1>{{ $media->title ?: 'Œuvre sans titre' }}</h1>
  </div>

  <div style="display:grid;grid-template-columns:300px 1fr;gap:28px;align-items:start;">
    <div class="panel" style="padding:0;overflow:hidden;">
      @if ($web = $media->variant('web'))
        <img src="{{ $web->url() }}" alt="{{ $media->alt_text }}" style="width:100%;display:block;">
      @else
        <div style="aspect-ratio:1/1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;color:var(--ink-soft);font-size:13px;text-align:center;padding:0 20px;">
          <span>Traitement en cours…</span>
          <span style="font-size:12px;">{{ $media->variants->count() }} / 3 variantes générées</span>
        </div>
      @endif
    </div>

    <div>
      <form method="POST" action="{{ route('admin.media.update', $media) }}">
        @csrf
        @method('put')

        <div class="panel">
          <h2>Informations</h2>
          <div class="field-row">
            <div class="field">
              <label>Titre</label>
              <input type="text" name="title" value="{{ old('title', $media->title) }}">
            </div>
            <div class="field">
              <label>Statut</label>
              <select name="status">
                <option value="draft" @selected($media->status === 'draft')>Brouillon</option>
                <option value="published" @selected($media->status === 'published')>Publié</option>
              </select>
            </div>
          </div>
          <div class="field">
            <label>Texte alternatif</label>
            <input type="text" name="alt_text" value="{{ old('alt_text', $media->alt_text) }}">
          </div>
          <div class="field">
            <label>Légende</label>
            <input type="text" name="caption" value="{{ old('caption', $media->caption) }}">
          </div>
          <div class="field">
            <label>Description</label>
            <textarea name="description">{{ old('description', $media->description) }}</textarea>
          </div>
          <div class="field">
            <label>Tags (séparés par des virgules)</label>
            <input type="text" name="tags" value="{{ old('tags', $media->tags->pluck('name')->join(', ')) }}">
          </div>
        </div>

        <div class="panel">
          <h2>Crédits &amp; licence</h2>
          <div class="field-row">
            <div class="field">
              <label>Crédit</label>
              <input type="text" name="credit" value="{{ old('credit', $media->credit) }}">
            </div>
            <div class="field">
              <label>Auteur</label>
              <input type="text" name="author" value="{{ old('author', $media->author) }}">
            </div>
          </div>
          <div class="field-row">
            <div class="field">
              <label>Copyright</label>
              <input type="text" name="copyright" value="{{ old('copyright', $media->copyright) }}">
            </div>
            <div class="field">
              <label>Licence</label>
              <input type="text" name="license" value="{{ old('license', $media->license) }}">
            </div>
          </div>
          <div class="field">
            <label>Lieu</label>
            <input type="text" name="location" value="{{ old('location', $media->location) }}">
          </div>
        </div>

        <div class="panel">
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <h2 style="margin:0;">Données EXIF</h2>
            @if ($media->gps_lat)
              <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-soft);">
                <input type="checkbox" name="hide_gps" value="1" @checked($media->hide_gps)>
                Masquer les coordonnées GPS
              </label>
            @endif
          </div>

          @if (empty($media->exif))
            <p style="font-size:13px;color:var(--ink-soft);margin:14px 0 0;">Aucune donnée EXIF détectée dans ce fichier (courant pour une image déjà recompressée ou une capture d'écran).</p>
          @else
            <p style="font-size:12px;color:var(--ink-soft);margin:6px 0 14px;">Coche « Masquer » pour retirer un détail de l'affichage public, sans le supprimer.</p>
            <div style="display:grid;grid-template-columns:1fr auto auto;gap:0;align-items:center;">
              @foreach (\App\Models\Media::EXIF_FIELDS as $key => $label)
                @if (! empty($media->exif[$key]))
                  <div style="padding:9px 0;border-bottom:1px solid var(--line);font-size:13px;color:var(--ink-soft);">{{ $label }}</div>
                  <div style="padding:9px 16px;border-bottom:1px solid var(--line);font-size:13px;text-align:right;">{{ $media->exif[$key] }}</div>
                  <label style="padding:9px 0 9px 12px;border-bottom:1px solid var(--line);font-size:12px;color:var(--ink-soft);display:flex;align-items:center;gap:6px;white-space:nowrap;">
                    <input type="checkbox" name="exif_hidden_fields[]" value="{{ $key }}" @checked(in_array($key, $media->exif_hidden_fields ?? []))>
                    Masquer
                  </label>
                @endif
              @endforeach
            </div>
          @endif
        </div>

        <div style="display:flex;align-items:center;gap:14px;">
          <button type="submit" class="btn primary">Enregistrer</button>
          @if (session('status') === 'media-updated')
            <span style="font-size:13px;color:var(--ok);">Enregistré.</span>
          @endif
        </div>
      </form>

      <form method="POST" action="{{ route('admin.media.destroy', $media) }}" style="margin-top:14px;" onsubmit="return confirm('Mettre cette œuvre à la corbeille ?');">
        @csrf
        @method('delete')
        <button type="submit" class="btn" style="color:var(--danger);border-color:var(--danger);">Mettre à la corbeille</button>
      </form>
    </div>
  </div>
</x-admin-layout>
