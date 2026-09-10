<x-admin-layout :active="'updates'" :title="'Mises à jour'">
  <div class="topbar">
    <h1>Mises à jour</h1>
  </div>

  @error('update')
    <div class="alert alert-danger">{{ $message }}</div>
  @enderror

  @if (session('status') === 'update-applied')
    <div class="alert alert-success">Mise à jour appliquée avec succès.</div>
    <script>
      // Le thème de l'admin colore aussi la petite célébration de fin de
      // mise à jour — confettis (ou citrouilles/sapins) et carillon.
      const celebrationTheme = @json(\App\Support\Theme::adminTheme());

      (function () {
        // position:absolute + hauteur totale du document (pas juste la
        // fenêtre visible) pour que les confettis retombent bien jusqu'en
        // bas de la page, même si elle défile.
        const pageHeight = Math.max(document.documentElement.scrollHeight, window.innerHeight);
        const canvas = document.createElement('canvas');
        canvas.style.cssText = `position:absolute;top:0;left:0;width:100%;height:${pageHeight}px;pointer-events:none;z-index:9999;`;
        canvas.width = window.innerWidth;
        canvas.height = pageHeight;
        document.body.appendChild(canvas);
        const ctx = canvas.getContext('2d');

        // Thème par défaut (clair/sombre) : confettis rectangulaires colorés.
        // Thèmes saisonniers : emoji à la place, plus parlant que des couleurs.
        const seasonalEmoji = { halloween: '🎃', noel: '🎄' }[celebrationTheme] || null;
        const colors = ['#7A4B33', '#4B6B4E', '#8A6A1F', '#A3402E', '#1E1C19'];

        const pieces = Array.from({ length: seasonalEmoji ? 90 : 220 }, () => ({
          x: Math.random() * canvas.width,
          y: -20 - Math.random() * canvas.height * 0.3,
          size: seasonalEmoji ? (18 + Math.random() * 16) : (6 + Math.random() * 6),
          color: colors[Math.floor(Math.random() * colors.length)],
          speedY: (seasonalEmoji ? 2 : 3) + Math.random() * 4,
          speedX: -1.5 + Math.random() * 3,
          rotation: Math.random() * 360,
          spin: -6 + Math.random() * 12,
        }));

        const maxFrames = 900;
        let frame = 0;
        function tick() {
          frame++;
          ctx.clearRect(0, 0, canvas.width, canvas.height);
          let anyOnScreen = false;
          pieces.forEach((p) => {
            p.x += p.speedX;
            p.y += p.speedY;
            p.rotation += p.spin;
            if (p.y < canvas.height + 30) anyOnScreen = true;
            ctx.save();
            ctx.translate(p.x, p.y);
            ctx.rotate((p.rotation * Math.PI) / 180);
            if (seasonalEmoji) {
              ctx.font = `${p.size}px serif`;
              ctx.textAlign = 'center';
              ctx.textBaseline = 'middle';
              ctx.fillText(seasonalEmoji, 0, 0);
            } else {
              ctx.fillStyle = p.color;
              ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.6);
            }
            ctx.restore();
          });
          if (anyOnScreen && frame < maxFrames) {
            requestAnimationFrame(tick);
          } else {
            canvas.remove();
          }
        }
        tick();
      })();

      (function () {
        // Petit carillon généré à la volée (Web Audio) plutôt qu'un fichier
        // audio à héberger — pas de son du tout si le navigateur bloque
        // l'audio sans interaction préalable, ce qui n'est pas bloquant ici.
        // Une note (fréquence Hz, durée en secondes) par thème.
        const tunes = {
          // Do (do6) - Mi - Sol - Do : arpège majeur, clochettes de Noël.
          noel: [
            [1046.50, 0.22], [1318.51, 0.22], [1567.98, 0.22], [2093.00, 0.4],
          ],
          // Descente chromatique grinçante, timbre carré pour l'aspect "grave".
          halloween: [
            [440.00, 0.18], [415.30, 0.18], [369.99, 0.18], [311.13, 0.5],
          ],
          // Carillon de succès par défaut : deux notes ascendantes.
          default: [
            [880.00, 0.35], [1318.51, 0.35],
          ],
        };
        const tune = tunes[celebrationTheme] || tunes.default;
        const oscType = celebrationTheme === 'halloween' ? 'square' : 'sine';

        try {
          const AudioCtx = window.AudioContext || window.webkitAudioContext;
          const ctx = new AudioCtx();
          let cursor = ctx.currentTime;
          tune.forEach(([freq, duration]) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = oscType;
            osc.frequency.value = freq;
            const start = cursor;
            gain.gain.setValueAtTime(0, start);
            gain.gain.linearRampToValueAtTime(0.24, start + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.0001, start + duration);
            osc.connect(gain).connect(ctx.destination);
            osc.start(start);
            osc.stop(start + duration);
            cursor += duration * 0.7;
          });
        } catch (e) {
          // Web Audio indisponible/bloqué — la mise à jour reste bien signalée visuellement.
        }
      })();
    </script>
  @endif

  <div class="alert alert-info alert-block" id="update-progress" hidden>
    <div style="width:100%;">
      <strong>Mise à jour en cours…</strong>
      <div style="margin-top:4px;">Ne quittez pas cette page et ne l'actualisez pas — l'opération peut prendre une minute.</div>
      <div class="progress-indeterminate-track" style="margin-top:10px;">
        <div class="progress-indeterminate-fill"></div>
      </div>
    </div>
  </div>

  <div class="updates-grid {{ $update['updateAvailable'] ? 'has-details' : '' }}">
    <div class="panel">
      <div class="field-row">
        <div class="field">
          <label>Version installée</label>
          <input type="text" value="{{ $update['current'] }}" disabled>
        </div>
        <div class="field">
          <label>Dernière version disponible</label>
          <input type="text" value="{{ $update['latest'] ?? '—' }}" disabled>
        </div>
      </div>

      @if ($update['updateAvailable'])
        <p style="font-size:14px;color:var(--clay);margin:0 0 16px;">Une mise à jour est disponible : {{ $update['current'] }} → {{ $update['latest'] }}</p>
      @else
        <p style="font-size:13px;color:var(--ink-soft);margin:0 0 16px;">Focale est à jour.</p>
      @endif

      <div style="display:flex;gap:10px;">
        <form method="POST" action="{{ route('admin.updates.check') }}">
          @csrf
          <button type="submit" class="btn">Vérifier maintenant</button>
        </form>

        @if ($update['updateAvailable'])
          <form method="POST" action="{{ route('admin.updates.apply') }}" id="apply-update-form">
            @csrf
            <button type="submit" class="btn primary" id="apply-update-btn">Mettre à jour</button>
          </form>
        @endif
      </div>
    </div>

    @if ($update['updateAvailable'])
      <div class="panel">
        <h2 style="text-transform:none;letter-spacing:normal;font-size:18px;font-family:'Fraunces',serif;font-weight:500;color:var(--ink);">
          {{ $update['name'] ?: 'Version '.$update['latest'] }}
        </h2>

        <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">
          @if ($update['publishedAt'])
            <span>Publiée le {{ \Illuminate\Support\Carbon::parse($update['publishedAt'])->translatedFormat('d F Y') }}</span>
          @endif
          @if ($update['assetSize'])
            <span>{{ number_format($update['assetSize'] / 1048576, 1) }} Mo à télécharger</span>
          @endif
          @if ($update['htmlUrl'])
            <a href="{{ $update['htmlUrl'] }}" target="_blank" rel="noopener" style="text-decoration:underline;">Voir sur GitHub ↗</a>
          @endif
        </div>

        <h3 style="font-size:13px;text-transform:uppercase;letter-spacing:0.03em;color:var(--ink-soft);margin:0 0 10px;">Notes de version</h3>
        <x-release-notes :notes="$update['notes']" />
      </div>
    @endif
  </div>

  @if ($lastInstalled)
    <div class="panel" style="margin-top:24px;">
      <h2 style="text-transform:none;letter-spacing:normal;font-size:16px;font-family:'Work Sans',sans-serif;font-weight:500;color:var(--ink-soft);margin:0 0 4px;">Dernière mise à jour installée</h2>
      <h3 style="text-transform:none;letter-spacing:normal;font-size:18px;font-family:'Fraunces',serif;font-weight:500;color:var(--ink);margin:0 0 10px;">
        {{ $lastInstalled['name'] ?: 'Version '.$lastInstalled['version'] }}
      </h3>

      <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">
        <span>Version {{ $lastInstalled['version'] }}</span>
        @if ($lastInstalled['appliedAt'])
          <span>Installée le {{ \Illuminate\Support\Carbon::parse($lastInstalled['appliedAt'])->isoFormat('D MMMM YYYY \à H:mm') }}</span>
        @elseif ($lastInstalled['publishedAt'])
          <span>Publiée le {{ \Illuminate\Support\Carbon::parse($lastInstalled['publishedAt'])->isoFormat('D MMMM YYYY') }}</span>
        @endif
        @if ($lastInstalled['htmlUrl'])
          <a href="{{ $lastInstalled['htmlUrl'] }}" target="_blank" rel="noopener" style="text-decoration:underline;">Voir sur GitHub ↗</a>
        @endif
      </div>

      <x-release-notes :notes="$lastInstalled['notes']" />
    </div>
  @endif

  @if ($update['updateAvailable'])
    <script>
      const applyForm = document.getElementById('apply-update-form');
      applyForm.addEventListener('submit', function (e) {
        if (applyForm.dataset.confirmed === '1') {
          const btn = document.getElementById('apply-update-btn');
          btn.disabled = true;
          btn.textContent = 'Mise à jour en cours…';
          document.getElementById('update-progress').hidden = false;
          return;
        }

        e.preventDefault();
        confirmModal('Une sauvegarde de la base et des fichiers sera créée avant la mise à jour. Continuer ?').then((ok) => {
          if (!ok) return;
          applyForm.dataset.confirmed = '1';
          applyForm.requestSubmit();
        });
      });
    </script>
  @endif
</x-admin-layout>
