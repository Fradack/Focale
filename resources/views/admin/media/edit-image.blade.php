<x-admin-layout :active="'media'" :title="'Éditer — '.($media->title ?: 'Œuvre')">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">

  <a href="{{ route('admin.media.edit', $media) }}" style="display:inline-flex;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">← {{ $media->title ?: 'Œuvre' }}</a>

  <div class="topbar">
    <h1>Éditer l'image <x-plugin-badge/></h1>
  </div>

  @error('photoedit')
    <div class="alert alert-danger">{{ $message }}</div>
  @enderror

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  @if ($media->original_backup_path)
    <div class="alert alert-info alert-block" style="margin-bottom:20px;">
      <div style="flex:1;">
        <strong>Une version originale est sauvegardée.</strong>
        <div style="margin-top:4px;">Tu peux revenir à cette version à tout moment tant qu'elle n'a pas été écrasée par une nouvelle restauration.</div>
      </div>
      <form method="POST" action="{{ route('admin.media.edit-image.revert', $media) }}" data-confirm="Revenir à la version originale ? L'édition actuelle sera perdue." style="flex-shrink:0;">
        @csrf
        <button type="submit" class="btn" style="white-space:nowrap;">Revenir à l'originale</button>
      </form>
    </div>
  @endif

  <div style="display:grid;grid-template-columns:1fr 260px;gap:24px;align-items:start;">
    <div class="panel" style="padding:16px;">
      <div style="max-height:70vh;overflow:hidden;">
        <img id="photoedit-canvas" src="{{ route('admin.media.original', $media) }}" alt="{{ $media->alt_text }}" style="display:block;max-width:100%;">
      </div>

      <form method="POST" action="{{ route('admin.media.edit-image.apply', $media) }}" id="photoedit-form" style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        @csrf
        <input type="hidden" name="crop_x" id="photoedit-crop-x">
        <input type="hidden" name="crop_y" id="photoedit-crop-y">
        <input type="hidden" name="crop_width" id="photoedit-crop-width">
        <input type="hidden" name="crop_height" id="photoedit-crop-height">
        <input type="hidden" name="rotate" id="photoedit-rotate" value="0">

        <button type="button" class="btn" id="photoedit-rotate-left">↺ Pivoter à gauche</button>
        <button type="button" class="btn" id="photoedit-rotate-right">↻ Pivoter à droite</button>
        <button type="button" class="btn" id="photoedit-reset">Réinitialiser le cadrage</button>
        <button type="submit" class="btn primary" style="margin-left:auto;">Enregistrer</button>
      </form>
    </div>

    <div>
      <div class="panel">
        <h2>Vignette actuelle</h2>
        @if ($thumb = $media->variant('thumbnail'))
          <img src="{{ $thumb->url() }}" alt="{{ $media->alt_text }}" style="width:100%;border-radius:6px;display:block;">
        @else
          <p style="font-size:13px;color:var(--ink-soft);margin:0;">Pas encore de vignette.</p>
        @endif
        <p style="font-size:12px;color:var(--ink-soft);margin:10px 0 0;">{{ $media->width }}×{{ $media->height }}</p>
      </div>

      <div class="panel">
        <h2>Aide</h2>
        <p style="font-size:13px;color:var(--ink-soft);margin:0;">
          Fais glisser les bords du cadre pour recadrer, utilise les boutons de rotation, puis "Enregistrer".
          L'original est sauvegardé automatiquement avant la première modification — "Revenir à l'originale"
          l'restaure à tout moment.
        </p>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
  <script>
    (function () {
      const image = document.getElementById('photoedit-canvas');
      const form = document.getElementById('photoedit-form');
      const rotateInput = document.getElementById('photoedit-rotate');
      let rotation = 0;

      const cropper = new Cropper(image, {
        viewMode: 1,
        autoCropArea: 1,
        background: false,
      });

      document.getElementById('photoedit-rotate-left').addEventListener('click', () => {
        cropper.rotate(-90);
        rotation = (rotation - 90) % 360;
      });
      document.getElementById('photoedit-rotate-right').addEventListener('click', () => {
        cropper.rotate(90);
        rotation = (rotation + 90) % 360;
      });
      document.getElementById('photoedit-reset').addEventListener('click', () => {
        cropper.reset();
        rotation = 0;
      });

      form.addEventListener('submit', (e) => {
        const data = cropper.getData(true);
        document.getElementById('photoedit-crop-x').value = data.x;
        document.getElementById('photoedit-crop-y').value = data.y;
        document.getElementById('photoedit-crop-width').value = data.width;
        document.getElementById('photoedit-crop-height').value = data.height;
        rotateInput.value = ((rotation % 360) + 360) % 360;
      });
    })();
  </script>
</x-admin-layout>
