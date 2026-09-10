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
        const colors = ['#7A4B33', '#4B6B4E', '#8A6A1F', '#A3402E', '#1E1C19'];
        const pieces = Array.from({ length: 220 }, () => ({
          x: Math.random() * canvas.width,
          y: -20 - Math.random() * canvas.height * 0.3,
          size: 6 + Math.random() * 6,
          color: colors[Math.floor(Math.random() * colors.length)],
          speedY: 3 + Math.random() * 4,
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
            ctx.fillStyle = p.color;
            ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.6);
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
        @if ($update['notes'])
          @php
            // Les notes viennent du corps de la release GitHub, en texte brut
            // (jamais de HTML, pour éviter d'injecter du contenu non fiable) —
            // on structure juste ce qui ressemble à des puces ou des
            // paragraphes séparés par une ligne vide.
            //
            // Convention d'écriture des releases : une ligne peut commencer
            // par « Ajout : », « Correction : », « Suppression : » ou
            // « Information : » (insensible à la casse, espace avant les
            // deux-points optionnel) pour afficher un badge de catégorie.
            $noteBadges = [
                'suppression' => ['label' => 'Suppression', 'class' => 'badge-danger'],
                'correction' => ['label' => 'Correction', 'class' => 'badge-warning'],
                'ajout' => ['label' => 'Ajout', 'class' => 'badge-success'],
                'information' => ['label' => 'Information', 'class' => 'badge-info'],
            ];
            $parseNoteLine = function (string $line) use ($noteBadges): array {
                if (preg_match('/^(' . implode('|', array_keys($noteBadges)) . ')\s*:\s*(.+)$/iu', $line, $m)) {
                    return ['badge' => $noteBadges[mb_strtolower($m[1])], 'text' => $m[2]];
                }
                return ['badge' => null, 'text' => $line];
            };

            // Un BOM UTF-8 en tête de texte (fréquent quand les notes sont
            // générées via un éditeur/outil qui l'ajoute par défaut) rend le
            // tout premier caractère invisible mais bien présent : la regex
            // ci-dessus, ancrée en début de ligne, ne matchait alors jamais
            // la toute première ligne — aucun badge sur elle malgré un
            // préfixe correct. On le retire avant tout traitement.
            $notesText = ltrim($update['notes'], "\xEF\xBB\xBF");

            $blocks = [];
            $currentList = [];
            foreach (preg_split('/\r\n|\r|\n/', trim($notesText)) as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                if (str_starts_with($line, '- ') || str_starts_with($line, '* ')) {
                    $currentList[] = $parseNoteLine(ltrim(substr($line, 2)));
                    continue;
                }
                if ($currentList) {
                    $blocks[] = ['list', $currentList];
                    $currentList = [];
                }
                $blocks[] = ['p', $parseNoteLine($line)];
            }
            if ($currentList) {
                $blocks[] = ['list', $currentList];
            }
          @endphp
          <div style="font-size:14px;line-height:1.7;color:var(--ink);">
            @foreach ($blocks as [$kind, $content])
              @if ($kind === 'list')
                <ul style="margin:0 0 12px;padding-left:20px;">
                  @foreach ($content as $item)
                    <li style="margin-bottom:4px;">
                      @if ($item['badge'])
                        <span class="badge {{ $item['badge']['class'] }}">{{ $item['badge']['label'] }}</span>
                      @endif
                      {{ $item['text'] }}
                    </li>
                  @endforeach
                </ul>
              @else
                <p style="margin:0 0 12px;">
                  @if ($content['badge'])
                    <span class="badge {{ $content['badge']['class'] }}">{{ $content['badge']['label'] }}</span>
                  @endif
                  {{ $content['text'] }}
                </p>
              @endif
            @endforeach
          </div>
        @else
          <p style="font-size:13px;color:var(--ink-soft);margin:0;">Aucune note de version fournie pour cette release.</p>
        @endif
      </div>
    @endif
  </div>

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
