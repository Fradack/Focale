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

      <div class="panel" style="grid-column:1 / -1;">
        <h2>Restriction géographique</h2>
        <p style="font-size:12px;color:var(--ink-soft);margin:0 0 12px;">
          Bloque l'accès au site public selon le pays du visiteur (détecté via son adresse IP, de façon approximative). Sans effet sur l'administration ni sur les visiteurs déjà connectés.
        </p>
        <div>
          <label style="display:flex;align-items:center;gap:8px;font-weight:400;">
            <input type="radio" name="country_restriction_mode" value="disabled" style="width:auto;" @checked(old('country_restriction_mode', $values['country_restriction_mode']) === 'disabled')>
            Désactivé
          </label>
          <label style="display:flex;align-items:center;gap:8px;font-weight:400;margin-top:6px;">
            <input type="radio" name="country_restriction_mode" value="blocklist" style="width:auto;" @checked(old('country_restriction_mode', $values['country_restriction_mode']) === 'blocklist')>
            Bloquer certains pays
          </label>
          <label style="display:flex;align-items:center;gap:8px;font-weight:400;margin-top:6px;">
            <input type="radio" name="country_restriction_mode" value="allowlist" style="width:auto;" @checked(old('country_restriction_mode', $values['country_restriction_mode']) === 'allowlist')>
            Autoriser uniquement certains pays
          </label>
        </div>
        <div style="margin-top:12px;">
          <label style="display:block;font-size:12px;color:var(--ink-soft);margin-bottom:5px;">Pays concernés</label>
          <div style="max-height:220px;overflow-y:auto;border:1px solid var(--line);border-radius:6px;padding:10px;display:grid;grid-template-columns:repeat(auto-fill, minmax(180px, 1fr));gap:4px 12px;">
            @php $selected = old('country_restriction_countries', $values['country_restriction_countries']); @endphp
            @foreach ($countries as $code => $label)
              <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:400;">
                <input type="checkbox" name="country_restriction_countries[]" value="{{ $code }}" style="width:auto;" @checked(in_array($code, $selected, true))>
                {{ $label }}
              </label>
            @endforeach
          </div>
        </div>

        <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--line);">
          <button type="button" id="test-geo-btn" class="btn" style="font-size:13px;">Tester la détection</button>
          <span id="test-geo-result" style="margin-left:10px;font-size:13px;color:var(--ink-soft);"></span>
          <p style="font-size:11px;color:var(--ink-soft);margin:6px 0 0;">
            Résout votre propre adresse IP pour vérifier que la détection de pays fonctionne réellement sur ce serveur. Si elle échoue systématiquement, la restriction géographique ne bloque plus personne (elle laisse toujours passer en cas d'échec).
          </p>
        </div>

        <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--line);">
          <label style="display:flex;align-items:center;gap:8px;font-weight:400;">
            <input type="checkbox" name="bot_restriction_enabled" value="1" style="width:auto;" @checked($values['bot_restriction_enabled'] === '1')>
            Bloquer les robots (bots/crawlers), y compris les moteurs de recherche
          </label>
          <p style="font-size:11px;color:var(--ink-soft);margin:6px 0 0;">
            Attention : bloque aussi Googlebot/Bingbot et les autres moteurs de recherche légitimes — le site disparaîtra progressivement des résultats de recherche tant que ce réglage est actif.
          </p>
        </div>
      </div>

      <script>
        (function () {
          const btn = document.getElementById('test-geo-btn');
          const result = document.getElementById('test-geo-result');
          if (!btn) return;
          btn.addEventListener('click', function () {
            result.textContent = 'Test en cours…';
            fetch(@json(route('admin.settings.test-geo')), { headers: { 'Accept': 'application/json' } })
              .then((r) => r.json())
              .then((data) => {
                result.textContent = data.ok
                  ? ('OK — votre IP (' + data.ip + ') est détectée comme : ' + data.country)
                  : ('Échec — votre IP (' + data.ip + ') n\'a pas pu être résolue en pays (la restriction géographique laisse alors toujours passer).');
                result.style.color = data.ok ? 'var(--ok)' : 'var(--danger)';
              })
              .catch(() => {
                result.textContent = 'Erreur réseau lors du test.';
                result.style.color = 'var(--danger)';
              });
          });
        })();
      </script>

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
