@if (\App\Support\Plugins::enabled('antipillage'))
<style>
  /* Dissuasif seulement : empêche la sélection/le glisser sur les images,
     mais un visiteur déterminé garde toujours des moyens de contournement
     (capture d'écran, outils développeur, etc.). */
  img { -webkit-user-drag: none; -webkit-touch-callout: none; }
</style>
<script>
(function () {
  document.addEventListener('contextmenu', function (e) {
    if (e.target.tagName === 'IMG') e.preventDefault();
  });
  document.addEventListener('dragstart', function (e) {
    if (e.target.tagName === 'IMG') e.preventDefault();
  });
})();
</script>
@endif
