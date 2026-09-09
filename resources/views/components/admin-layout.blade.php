@props(['active' => null])
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title ?? 'Focale Admin' }} — Focale Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
@vite('resources/css/admin.css')
</head>
<body>

<div class="shell">
  <aside class="sidebar">
    <span class="wordmark">Focale</span>

    <nav>
      <a href="{{ route('admin.dashboard') }}" class="{{ $active === 'dashboard' ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg>
        Tableau de bord
      </a>
      <a href="{{ route('admin.media.index') }}" class="{{ $active === 'media' ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="9" r="1.5"></circle><path d="M21 15l-5-5-9 9"></path></svg>
        Médiathèque
      </a>
      <a href="{{ route('admin.albums.index') }}" class="{{ $active === 'albums' ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="14" rx="2"></rect><path d="M3 9h18"></path></svg>
        Albums
      </a>
      <a href="{{ route('admin.pages.index') }}" class="{{ $active === 'pages' ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M6 2h9l5 5v15H6z"></path><path d="M15 2v5h5"></path></svg>
        Pages
      </a>
      <a href="{{ route('admin.comments.index') }}" class="{{ $active === 'comments' ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path></svg>
        Commentaires
        @php($pendingCommentsCount = \App\Models\Comment::where('status', 'pending')->count())
        @if($pendingCommentsCount > 0)
          <span class="count">{{ $pendingCommentsCount }}</span>
        @endif
      </a>
      <a href="{{ route('admin.settings.edit') }}" class="{{ $active === 'settings' ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.7 1.7 0 00.34 1.87l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.7 1.7 0 00-1.87-.34 1.7 1.7 0 00-1 1.55V21a2 2 0 11-4 0v-.09a1.7 1.7 0 00-1-1.55 1.7 1.7 0 00-1.87.34l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.7 1.7 0 00.34-1.87 1.7 1.7 0 00-1.55-1H3a2 2 0 110-4h.09a1.7 1.7 0 001.55-1 1.7 1.7 0 00-.34-1.87l-.06-.06a2 2 0 112.83-2.83l.06.06a1.7 1.7 0 001.87.34H9a1.7 1.7 0 001-1.55V3a2 2 0 114 0v.09a1.7 1.7 0 001 1.55 1.7 1.7 0 001.87-.34l.06-.06a2 2 0 112.83 2.83l-.06.06a1.7 1.7 0 00-.34 1.87V9a1.7 1.7 0 001.55 1H21a2 2 0 110 4h-.09a1.7 1.7 0 00-1.55 1z"></path></svg>
        Réglages
      </a>
      <a href="{{ route('admin.updates.index') }}" class="{{ $active === 'updates' ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M21 12a9 9 0 11-3.5-7.14"></path><path d="M21 3v6h-6"></path></svg>
        Mises à jour
      </a>
    </nav>

    <div class="sidebar-footer">
      <span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
      <div>
        <a href="{{ route('admin.profile.edit') }}" class="user-name">{{ auth()->user()->name }}</a>
        <a href="{{ url('/') }}" class="view-site" target="_blank">Voir le site ↗</a>
        ·
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
          @csrf
          <button type="submit" class="view-site" style="background:none;border:none;padding:0;font:inherit;cursor:pointer;">Déconnexion</button>
        </form>
      </div>
    </div>
  </aside>

  <main class="main">
    <div class="processing-banner" id="processing-banner" hidden>
      <span class="processing-banner-label" id="processing-banner-label">Traitement des images…</span>
      <div class="processing-banner-track"><div class="processing-banner-fill" id="processing-banner-fill" style="width:0%;"></div></div>
      <span class="processing-banner-count" id="processing-banner-count"></span>
    </div>

    {{ $slot }}
  </main>
</div>

<div class="modal-overlay" id="confirm-modal-overlay" hidden>
  <div class="modal-box">
    <p id="confirm-modal-message"></p>
    <div class="modal-actions">
      <button type="button" class="btn" id="confirm-modal-cancel">Annuler</button>
      <button type="button" class="btn primary" id="confirm-modal-ok">Confirmer</button>
    </div>
  </div>
</div>

<script>
  // Modale de confirmation réutilisable, à la place de window.confirm() —
  // Chrome bloque de plus en plus agressivement les popups natifs répétés.
  // confirmModal(message) renvoie une Promise<boolean>.
  function confirmModal(message) {
    return new Promise((resolve) => {
      const overlay = document.getElementById('confirm-modal-overlay');
      document.getElementById('confirm-modal-message').textContent = message;
      overlay.hidden = false;

      const okBtn = document.getElementById('confirm-modal-ok');
      const cancelBtn = document.getElementById('confirm-modal-cancel');

      function cleanup(result) {
        overlay.hidden = true;
        okBtn.removeEventListener('click', onOk);
        cancelBtn.removeEventListener('click', onCancel);
        overlay.removeEventListener('click', onOverlay);
        resolve(result);
      }
      function onOk() { cleanup(true); }
      function onCancel() { cleanup(false); }
      function onOverlay(e) { if (e.target === overlay) cleanup(false); }

      okBtn.addEventListener('click', onOk);
      cancelBtn.addEventListener('click', onCancel);
      overlay.addEventListener('click', onOverlay);
    });
  }

  // Convertit automatiquement tout <form data-confirm="…"> : le submit est
  // intercepté, la modale s'affiche, et le formulaire n'est réellement
  // soumis qu'après confirmation — plus besoin d'un onsubmit="confirm(...)"
  // par formulaire.
  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', function (e) {
      if (form.dataset.confirmed === '1') return;
      e.preventDefault();
      confirmModal(form.dataset.confirm).then((ok) => {
        if (ok) {
          form.dataset.confirmed = '1';
          form.requestSubmit ? form.requestSubmit(e.submitter || undefined) : form.submit();
        }
      });
    });
  });

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.password-toggle');
    if (!btn) return;
    const input = document.getElementById(btn.dataset.for);
    if (!input) return;
    const visible = input.type === 'text';
    input.type = visible ? 'password' : 'text';
    btn.classList.toggle('is-visible', !visible);
    btn.setAttribute('aria-label', visible ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
  });

  (function () {
    const banner = document.getElementById('processing-banner');
    const label = document.getElementById('processing-banner-label');
    const fill = document.getElementById('processing-banner-fill');
    const count = document.getElementById('processing-banner-count');
    const statusUrl = @json(route('admin.media.processing-status'));
    let timer = null;

    function formatEta(minutes) {
      if (minutes === null) return 'estimation en cours…';
      if (minutes < 1) return 'moins d\'une minute restante';
      if (minutes < 60) return `~${minutes} min restantes`;
      const hours = Math.floor(minutes / 60);
      const rest = minutes % 60;
      return `~${hours} h${rest ? ' ' + rest + ' min' : ''} restantes`;
    }

    function poll() {
      fetch(statusUrl, { headers: { Accept: 'application/json' } })
        .then(r => (r.ok ? r.json() : null))
        .then(data => {
          // Réponse absente ou de forme inattendue (session expirée, erreur
          // serveur…) : on ignore ce sondage sans toucher à l'état affiché,
          // plutôt que de risquer d'afficher un faux "terminé".
          if (! data || typeof data.total !== 'number' || typeof data.pending !== 'number') {
            timer = setTimeout(poll, 8000);
            return;
          }

          if (data.pending > 0) {
            banner.hidden = false;
            banner.classList.remove('is-done');
            label.textContent = `Traitement des images (${formatEta(data.eta_minutes)})`;
            fill.style.width = data.percent + '%';
            count.textContent = `${data.processed} / ${data.total}`;
            timer = setTimeout(poll, 4000);
          } else if (!banner.hidden) {
            banner.classList.add('is-done');
            label.textContent = 'Traitement terminé';
            fill.style.width = '100%';
            count.textContent = `${data.total} / ${data.total}`;
            setTimeout(() => { banner.hidden = true; }, 4000);
          }
        })
        .catch(() => { timer = setTimeout(poll, 8000); });
    }

    poll();
    window.addEventListener('beforeunload', () => { if (timer) clearTimeout(timer); });
  })();
</script>

</body>
</html>
