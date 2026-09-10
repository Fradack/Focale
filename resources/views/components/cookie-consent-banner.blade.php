@props(['consent' => null])
@if (\App\Support\Plugins::enabled('tracking'))
<style>
  .cookie-banner { position: fixed; left: 16px; right: 16px; bottom: 16px; z-index: 9998; max-width: 560px; margin: 0 auto; background: var(--panel, #EFEDE7); border: 1px solid var(--line, #C9C2B4); border-radius: 12px; padding: 18px 20px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); font-size: 13px; color: var(--ink, #1E1C19); }
  .cookie-banner p { margin: 0 0 14px; line-height: 1.5; color: var(--ink-soft, #5C574E); }
  .cookie-banner-actions { display: flex; flex-wrap: wrap; gap: 8px; }
  .cookie-banner-actions button { border-radius: 8px; border: 1px solid var(--line, #C9C2B4); background: var(--bg, #E7E3DC); color: var(--ink, #1E1C19); font-size: 12px; padding: 8px 14px; cursor: pointer; font-family: inherit; }
  .cookie-banner-actions button#cookie-accept { background: var(--clay, #7A4B33); border-color: var(--clay, #7A4B33); color: #fff; }
  .cookie-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px; }
  .cookie-modal-box { background: var(--panel, #EFEDE7); border-radius: 12px; padding: 22px; max-width: 440px; width: 100%; }
  .cookie-modal-box h3 { margin: 0 0 14px; font-size: 16px; }
  .cookie-modal-box label { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; line-height: 1.5; color: var(--ink-soft, #5C574E); margin-bottom: 18px; }
  .cookie-modal-actions { display: flex; justify-content: flex-end; gap: 8px; }
  .cookie-modal-actions button { border-radius: 8px; border: 1px solid var(--line, #C9C2B4); background: var(--bg, #E7E3DC); color: var(--ink, #1E1C19); font-size: 12px; padding: 8px 14px; cursor: pointer; font-family: inherit; }
  .cookie-modal-actions button#cookie-modal-save { background: var(--clay, #7A4B33); border-color: var(--clay, #7A4B33); color: #fff; }
</style>

<div id="cookie-banner" class="cookie-banner" {{ $consent ? 'hidden' : '' }}>
  <p>
    Focale utilise un traceur anonyme pour mesurer la fréquentation du site et améliorer votre expérience de
    navigation. Aucune donnée n'est utilisée à des fins publicitaires. Vous pouvez accepter, refuser, ou choisir
    vous-même — et changer d'avis à tout moment via le lien « Cookies » en bas de page.
  </p>
  <div class="cookie-banner-actions">
    <button type="button" id="cookie-accept">J'accepte les cookies</button>
    <button type="button" id="cookie-reject">Je refuse les cookies</button>
    <button type="button" id="cookie-settings">Paramétrer les cookies</button>
  </div>
</div>

<div id="cookie-modal" class="cookie-modal-overlay" hidden>
  <div class="cookie-modal-box">
    <h3>Paramètres des cookies</h3>
    <label>
      <input type="checkbox" id="cookie-analytics-toggle" style="margin-top:2px;" {{ $consent === 'accepted' ? 'checked' : '' }}>
      <span>Mesure d'audience et amélioration de l'expérience (adresse IP, localisation approximative, type d'appareil, temps passé par page)</span>
    </label>
    <div class="cookie-modal-actions">
      <button type="button" id="cookie-modal-cancel">Annuler</button>
      <button type="button" id="cookie-modal-save">Enregistrer</button>
    </div>
  </div>
</div>

<script>
(function () {
  const consentUrl = @json(route('public.consent.store'));
  const csrfToken = @json(csrf_token());
  const banner = document.getElementById('cookie-banner');
  const modal = document.getElementById('cookie-modal');
  const toggle = document.getElementById('cookie-analytics-toggle');
  const saveBtn = document.getElementById('cookie-modal-save');

  // L'état de consentement est déterminé côté serveur (voir $consent,
  // passé par public-footer.blade.php) et jamais relu depuis
  // document.cookie : le cookie focale_consent est chiffré par Laravel
  // (EncryptCookies), donc sa valeur brute côté client n'est jamais
  // "accepted"/"rejected" en clair — un bug qui rendait la case du panneau
  // "Paramétrer" toujours décochée à la réouverture, quel que soit le choix
  // réellement enregistré.
  let currentConsentValue = @json($consent);

  function send(value) {
    saveBtn && (saveBtn.disabled = true);
    fetch(consentUrl, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ consent: value }),
    })
      .then(() => { location.reload(); })
      .catch(() => {
        // Requête réseau perdue (pas juste un statut HTTP d'erreur, fetch()
        // ne rejette que sur un vrai échec réseau) : on prévient plutôt que
        // de laisser le bouton "Enregistrer" ne rien faire silencieusement.
        saveBtn && (saveBtn.disabled = false);
        alert("Impossible d'enregistrer votre choix — vérifiez votre connexion et réessayez.");
      });
  }

  document.getElementById('cookie-accept').addEventListener('click', () => send('accepted'));
  document.getElementById('cookie-reject').addEventListener('click', () => send('rejected'));
  document.getElementById('cookie-settings').addEventListener('click', () => {
    toggle.checked = currentConsentValue === 'accepted';
    banner.hidden = true;
    modal.hidden = false;
  });
  document.getElementById('cookie-modal-cancel').addEventListener('click', () => { modal.hidden = true; });
  saveBtn.addEventListener('click', () => {
    modal.hidden = true;
    send(toggle.checked ? 'accepted' : 'rejected');
  });

  const reopenLink = document.getElementById('cookie-reopen-link');
  if (reopenLink) {
    reopenLink.addEventListener('click', function (e) {
      e.preventDefault();
      toggle.checked = currentConsentValue === 'accepted';
      modal.hidden = false;
    });
  }
})();
</script>
@endif
