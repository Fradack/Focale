<x-admin-layout :active="'settings'" :title="'Réglages'">
  <div class="topbar">
    <h1>Réglages</h1>
  </div>

  <form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('put')

    <div class="settings-grid">
      <div class="panel">
        <h2>Identité</h2>
        <div class="field">
          <label>Nom du site</label>
          <input type="text" name="site_name" value="{{ old('site_name', $values['site_name']) }}" placeholder="Focale">
        </div>
        <div class="field">
          <label>Nom de l'artiste ou du studio</label>
          <input type="text" name="artist_name" value="{{ old('artist_name', $values['artist_name']) }}">
        </div>
        <div class="field">
          <label>Courte biographie</label>
          <textarea name="bio">{{ old('bio', $values['bio']) }}</textarea>
        </div>
      </div>

      <div class="panel">
        <h2>Contact &amp; réseaux</h2>
        <div class="field">
          <label>Adresse e-mail de contact</label>
          <input type="email" name="contact_email" value="{{ old('contact_email', $values['contact_email']) }}">
        </div>
        <div class="field">
          <label>Instagram</label>
          <input type="text" name="social_instagram" value="{{ old('social_instagram', $values['social_instagram']) }}" placeholder="https://instagram.com/…">
        </div>
        <div class="field">
          <label>Twitter / X</label>
          <input type="text" name="social_twitter" value="{{ old('social_twitter', $values['social_twitter']) }}" placeholder="https://x.com/…">
        </div>
      </div>

      <div class="panel" style="grid-column:1 / -1;">
        <h2>Page d'accueil</h2>
        <p style="font-size:12px;color:var(--ink-soft);margin:0 0 12px;">Œuvre de couverture affichée en haut de l'accueil. La biographie ci-dessus et les albums marqués « à la une » (depuis leur fiche) complètent la page.</p>
        @if ($coverCandidates->isEmpty())
          <p style="font-size:13px;color:var(--ink-soft);">Importe des œuvres dans la médiathèque pour pouvoir en choisir une.</p>
        @else
          <div class="media-grid" style="max-height:260px;overflow-y:auto;">
            <label class="media-item {{ (string) old('home_cover_media_id', $values['home_cover_media_id']) === '' ? 'is-cover' : '' }}" style="cursor:pointer;">
              <input type="radio" name="home_cover_media_id" value="" style="position:absolute;top:8px;right:8px;" @checked((string) old('home_cover_media_id', $values['home_cover_media_id']) === '')>
              <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--ink-soft);text-align:center;padding:4px;">Aucune</div>
            </label>
            @foreach ($coverCandidates as $item)
              <label class="media-item {{ (string) old('home_cover_media_id', $values['home_cover_media_id']) === (string) $item->id ? 'is-cover' : '' }}" style="cursor:pointer;">
                <input type="radio" name="home_cover_media_id" value="{{ $item->id }}" style="position:absolute;top:8px;right:8px;" @checked((string) old('home_cover_media_id', $values['home_cover_media_id']) === (string) $item->id)>
                @if ($thumb = $item->variant('thumbnail'))
                  <img src="{{ $thumb->url() }}" alt="">
                @endif
              </label>
            @endforeach
          </div>
        @endif
      </div>

      <div class="panel">
        <h2>Pied de page</h2>
        <div class="field">
          <label>Mention de copyright</label>
          <input type="text" name="footer_copyright" value="{{ old('footer_copyright', $values['footer_copyright']) }}" placeholder="© {{ now()->year }} {{ $values['artist_name'] ?: ($values['site_name'] ?: 'Focale') }}. Tous droits réservés.">
        </div>
        <p style="font-size:12px;color:var(--ink-soft);margin:0;">
          Les mentions légales et CGU sont gérées comme des pages classiques — crée-les depuis <a href="{{ route('admin.pages.index') }}" style="text-decoration:underline;">Pages</a> (par exemple avec les adresses <code>mentions-legales</code> et <code>cgu</code>) : une fois publiées, elles apparaissent automatiquement dans le pied de page du site.
        </p>
      </div>

      <div class="panel">
        <h2>Anti-spam</h2>
        <p style="font-size:12px;color:var(--ink-soft);margin:0 0 12px;">
          Les formulaires de contact et de commentaires sont déjà protégés (piège à robots, délai de remplissage, limite de fréquence).
          Ajouter un CAPTCHA Cloudflare Turnstile (gratuit) est optionnel — laisse les champs vides pour ne pas l'activer.
        </p>
        <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank" rel="noopener" class="btn" style="display:inline-flex;margin-bottom:16px;">
          Configurer Turnstile avec Spin ↗
        </a>
        <div class="field">
          <label>Clé de site Turnstile</label>
          <input type="text" name="turnstile_site_key" value="{{ old('turnstile_site_key', $values['turnstile_site_key']) }}">
        </div>
        <div class="field">
          <label>Clé secrète Turnstile</label>
          <input type="password" name="turnstile_secret_key" value="{{ old('turnstile_secret_key', $values['turnstile_secret_key']) }}">
        </div>
      </div>

      <div class="panel">
        <h2>Site</h2>
        <label style="display:flex;align-items:center;gap:10px;font-size:14px;">
          <input type="checkbox" name="maintenance_mode" value="1" @checked($values['maintenance_mode'] === '1')>
          Activer le mode maintenance (site public masqué)
        </label>
      </div>

      <div class="panel">
        <h2>Boutique</h2>
        <label style="display:flex;align-items:center;gap:10px;font-size:14px;">
          <input type="checkbox" name="shop_enabled" value="1" @checked($values['shop_enabled'] === '1')>
          Activer la boutique (affiche le lien « Boutique » dans la navigation et rend /boutique et /panier accessibles)
        </label>
        <p style="font-size:12px;color:var(--ink-soft);margin:12px 0 0;">
          Gestion des articles, moyens de paiement, options de livraison et commandes depuis
          <a href="{{ route('admin.shop.products.index') }}" style="text-decoration:underline;">Boutique → Produits</a>
          dans le menu.
        </p>
      </div>

      <div class="panel">
        <h2>Apparence</h2>
        <div class="field-row">
          <div class="field">
            <label>Thème — Administration</label>
            <select name="theme_admin">
              @foreach (\App\Support\Theme::LABELS as $value => $label)
                <option value="{{ $value }}" @selected(old('theme_admin', $values['theme_admin']) === $value)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label>Thème — Site public</label>
            <select name="theme_public">
              @foreach (\App\Support\Theme::LABELS as $value => $label)
                <option value="{{ $value }}" @selected(old('theme_public', $values['theme_public']) === $value)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <p style="font-size:12px;color:var(--ink-soft);margin:6px 0 0;">Chaque côté a son propre thème, choisi indépendamment. Contrastes vérifiés pour rester lisible dans tous les cas.</p>
      </div>
    </div>

    <div style="display:flex;align-items:center;gap:14px;margin-top:20px;">
      <button type="submit" class="btn primary">Enregistrer</button>
      @if (session('status') === 'settings-updated')
        <span style="font-size:13px;color:var(--ok);">Enregistré.</span>
      @endif
    </div>
  </form>
</x-admin-layout>
